<?php

namespace Awcodes\Curator\Commands;

use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Support\MimeType;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Throwable;

use function Awcodes\Curator\is_media_svg;
use function Awcodes\Curator\sanitize_svg;

class RepairExtensionsCommand extends Command
{
    public $signature = 'curator:repair-extensions
        {--dry-run : Report what would change without moving any files}';

    public $description = 'Rename stored media whose file extension does not match its contents and could be served as a document or script.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $prefix = $dryRun ? '[dry run] ' : '';

        $model = app(Media::class);
        $total = $model::query()->count();

        if ($total === 0) {
            $this->info('No media found.');

            return self::SUCCESS;
        }

        $this->info($prefix . "Checking {$total} media file(s)...");

        $counts = ['renamed' => 0, 'mismatched' => 0, 'review' => 0, 'skipped' => 0];

        $model::query()->chunkById(100, function ($records) use (&$counts, $dryRun): void {
            foreach ($records as $media) {
                try {
                    $result = $this->inspect($media, $dryRun);
                } catch (Throwable $e) {
                    $this->error("  error: [{$media->id}] {$media->path} — {$e->getMessage()}");
                    $result = 'skipped';
                }

                if (isset($counts[$result])) {
                    $counts[$result]++;
                }
            }
        });

        $this->newLine();
        $this->info(sprintf(
            '%sDone. %d %s, %d to review, %d harmless mismatch(es) left as they are, %d skipped.',
            $prefix,
            $counts['renamed'],
            $dryRun ? 'would be renamed' : 'renamed',
            $counts['review'],
            $counts['mismatched'],
            $counts['skipped'],
        ));

        return self::SUCCESS;
    }

    /**
     * @return 'ok'|'renamed'|'mismatched'|'review'|'skipped'
     */
    protected function inspect(Media $media, bool $dryRun): string
    {
        $disk = Storage::disk($media->disk);
        $path = (string) $media->path;

        if ($path === '' || ! $disk->exists($path)) {
            $this->warn("  skipped (missing file): [{$media->id}] {$path}");

            return 'skipped';
        }

        // Extensions were stored as the client sent them, so `.JPG` is common
        // and harmless. Case alone never makes an extension unsafe.
        $storedExtension = mb_strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $columnExtension = mb_strtolower((string) $media->ext);
        $detectedType = MimeType::refineDetectedType(
            MimeType::detect($disk->readStream($path)) ?? MimeType::OCTET_STREAM,
            $storedExtension,
            fn (): string => (string) $disk->get($path),
        );

        if (MimeType::isRestricted($detectedType)) {
            return $this->neutralise($media, $disk, $detectedType, $storedExtension, $dryRun);
        }

        $expectedExtension = MimeType::resolveExtension($detectedType, $storedExtension);

        if ($storedExtension === $expectedExtension && $columnExtension === $expectedExtension) {
            if (pathinfo($path, PATHINFO_EXTENSION) !== $expectedExtension || $media->ext !== $expectedExtension) {
                $this->line("  mismatch (case only), left as is: [{$media->id}] {$path}");

                return 'mismatched';
            }

            return 'ok';
        }

        if (! MimeType::isUnsafeExtension($storedExtension) && ! MimeType::isUnsafeExtension($columnExtension)) {
            $this->line("  mismatch, left as is: [{$media->id}] {$path} is {$detectedType}");

            return 'mismatched';
        }

        return $this->rename($media, $disk, $expectedExtension, $detectedType, $dryRun);
    }

    /**
     * Content that renders as a document or runs as code, such as HTML stored
     * under an image's name. It is renamed to `.txt`, so that it is served as
     * plain text while the bytes are kept for review. Applications that accept
     * the type on purpose keep it and are warned.
     */
    protected function neutralise(Media $media, Filesystem $disk, string $detectedType, string $storedExtension, bool $dryRun): string
    {
        if ($storedExtension === 'txt') {
            return 'ok';
        }

        if (MimeType::isAccepted($detectedType, (array) config('curator.accepted_file_types', []))) {
            $this->warn("  review (holds {$detectedType}, which this app accepts): [{$media->id}] {$media->path}");

            return 'review';
        }

        return $this->rename($media, $disk, 'txt', MimeType::TEXT_PLAIN, $dryRun);
    }

    /**
     * @return 'renamed'|'skipped'
     */
    protected function rename(Media $media, Filesystem $disk, string $extension, string $type, bool $dryRun): string
    {
        $oldPath = (string) $media->path;

        // Renamed in place, beside the file it replaces. The stored name is not
        // used because it was never validated as a path segment.
        $directory = dirname(ltrim($oldPath, '/'));
        $filename = pathinfo($oldPath, PATHINFO_FILENAME);
        $filename = $filename === '' ? 'media-' . $media->getKey() : $filename;
        $newPath = ($directory === '.' ? '' : $directory . '/') . $filename . '.' . $extension;

        if ($disk->exists($newPath)) {
            $this->warn("  skipped (target exists): [{$media->id}] {$oldPath} -> {$newPath}");

            return 'skipped';
        }

        $content = null;

        // Giving markup an .svg extension makes it render inline, so it has to be
        // sanitized on the way.
        if (is_media_svg($type)) {
            $original = (string) $disk->get($oldPath);
            $content = sanitize_svg($original);

            if ($content === '' && $original !== '') {
                $this->warn("  skipped (could not sanitize): [{$media->id}] {$oldPath}");

                return 'skipped';
            }
        }

        if (! $dryRun) {
            if ($content !== null) {
                $disk->put($newPath, $content, $media->visibility ?? 'public');
                $disk->delete($oldPath);
            } elseif (! $disk->move($oldPath, $newPath)) {
                $this->error("  error: [{$media->id}] could not move {$oldPath} to {$newPath}");

                return 'skipped';
            }

            $media->forceFill([
                'path' => $newPath,
                'ext' => $extension,
                'type' => $type,
                'size' => $disk->size($newPath),
            ])->saveQuietly();

            try {
                app(config('curator.glide.server'))->getFactory()->deleteCache($oldPath);
            } catch (Throwable) {
                // A stale cached rendition is harmless once the source is renamed.
            }
        }

        $this->line(($dryRun ? '  would rename: ' : '  renamed: ') . "[{$media->id}] {$oldPath} -> {$newPath} ({$type})");

        return 'renamed';
    }
}
