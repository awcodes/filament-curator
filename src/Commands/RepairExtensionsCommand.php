<?php

declare(strict_types=1);

namespace Awcodes\Curator\Commands;

use Awcodes\Curator\Enums\MimeType;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Symfony\Component\Mime\MimeTypes;
use Throwable;

class RepairExtensionsCommand extends Command
{
    public $signature = 'curator:repair-extensions
        {--dry-run : Report what would change without moving any files}';

    public $description = 'Rename stored media whose file extension does not match its contents and could be served as a document or script.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $prefix = $dryRun ? '[dry run] ' : '';

        $model = App::make(Media::class);
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

        // Extensions were stored as sent before 4.1.1, so `.JPG` is common and
        // harmless. Case alone never makes an extension unsafe.
        $storedExtension = mb_strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $columnExtension = mb_strtolower((string) $media->ext);
        $detectedType = MimeType::refineDetectedType(
            $this->detectType($disk, $path),
            $storedExtension,
            fn (): string => (string) $disk->get($path),
        );

        if ($this->isUnsafeContent($detectedType)) {
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

        if (! $this->isUnsafeExtension($storedExtension) && ! $this->isUnsafeExtension($columnExtension)) {
            $this->line("  mismatch, left as is: [{$media->id}] {$path} is {$detectedType}");

            return 'mismatched';
        }

        return $this->rename($media, $disk, $expectedExtension, $detectedType, $dryRun);
    }

    /**
     * Content that renders as a document or runs as code, such as HTML stored
     * while 5.0.0 to 5.1.4 accepted it by default. It is renamed to `.txt`, so
     * that it is served as plain text while the bytes are kept for review.
     * Applications that accept the type on purpose keep it and are warned.
     */
    protected function neutralise(Media $media, Filesystem $disk, string $detectedType, string $storedExtension, bool $dryRun): string
    {
        if ($storedExtension === MimeType::TextPlain->getExt()) {
            return 'ok';
        }

        if (in_array($detectedType, Curator::getAcceptedFileTypes(), true)) {
            $this->warn("  review (holds {$detectedType}, which this app accepts): [{$media->id}] {$media->path}");

            return 'review';
        }

        return $this->rename($media, $disk, MimeType::TextPlain->getExt(), MimeType::TextPlain->value, $dryRun);
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
        if (Curator::isSvgMimeType($type)) {
            $original = (string) $disk->get($oldPath);
            $content = Curator::sanitizeSvg($original);

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

            Glide::getServer()->deleteCache($oldPath);
        }

        $this->line(($dryRun ? '  would rename: ' : '  renamed: ') . "[{$media->id}] {$oldPath} -> {$newPath} ({$type})");

        return 'renamed';
    }

    /**
     * Detect the type from the stored bytes, the same way uploads are detected.
     */
    protected function detectType(Filesystem $disk, string $path): string
    {
        $stream = $disk->readStream($path);
        $sample = is_resource($stream) ? stream_get_contents($stream, 64 * 1024) : false;

        if (is_resource($stream)) {
            fclose($stream);
        }

        if (! is_string($sample) || $sample === '') {
            return MimeType::ApplicationOctetStream->value;
        }

        return (new FinfoMimeTypeDetector)->detectMimeTypeFromBuffer($sample)
            ?: MimeType::ApplicationOctetStream->value;
    }

    /**
     * Whether the content itself would run script or code if served under its
     * own type. libmagic reports XML as text/xml as well as application/xml.
     * SVG is left to curator:sanitize-svgs, and octet-stream is only libmagic
     * giving up.
     */
    protected function isUnsafeContent(string $type): bool
    {
        return $type === 'text/xml'
            || ($type !== MimeType::ApplicationOctetStream->value && in_array($type, MimeType::restricted(), true));
    }

    /**
     * Whether a web server could serve a file with this (lowercased) extension
     * as a document, script or server-side code.
     */
    protected function isUnsafeExtension(string $extension): bool
    {
        if (preg_match('/^[a-z0-9]+$/', $extension) !== 1) {
            return true;
        }

        if (MimeType::isExecutableExtension($extension) || Curator::isRestricted($extension) || Curator::isSvg($extension)) {
            return true;
        }

        foreach (MimeTypes::getDefault()->getMimeTypes($extension) as $type) {
            if (Curator::isUnsafeInlineMimeType($type)) {
                return true;
            }
        }

        return false;
    }
}
