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

        $repaired = 0;
        $mismatched = 0;
        $skipped = 0;

        $model::query()->chunkById(100, function ($records) use (&$repaired, &$mismatched, &$skipped, $dryRun): void {
            foreach ($records as $media) {
                try {
                    $result = $this->inspect($media, $dryRun);
                } catch (Throwable $e) {
                    $this->error("  error: [{$media->id}] {$media->path} — {$e->getMessage()}");
                    $result = 'skipped';
                }

                match ($result) {
                    'repaired' => $repaired++,
                    'mismatched' => $mismatched++,
                    'skipped' => $skipped++,
                    default => null,
                };
            }
        });

        $this->newLine();
        $this->info(sprintf(
            '%sDone. %d %s, %d harmless mismatch(es) left as they are, %d skipped.',
            $prefix,
            $repaired,
            $dryRun ? 'would be renamed' : 'renamed',
            $mismatched,
            $skipped,
        ));

        return self::SUCCESS;
    }

    /**
     * @return 'ok'|'repaired'|'mismatched'|'skipped'
     */
    protected function inspect(Media $media, bool $dryRun): string
    {
        $disk = Storage::disk($media->disk);

        if (blank($media->path) || ! $disk->exists($media->path)) {
            $this->warn("  skipped (missing file): [{$media->id}] {$media->path}");

            return 'skipped';
        }

        $storedExtension = pathinfo($media->path, PATHINFO_EXTENSION);
        $detectedType = $this->detectType($disk, $media->path);
        $expectedExtension = MimeType::resolveExtension($detectedType, $storedExtension);

        if ($storedExtension === $expectedExtension && $media->ext === $expectedExtension) {
            return 'ok';
        }

        if (! $this->isUnsafe($storedExtension) && ! $this->isUnsafe((string) $media->ext)) {
            $this->line("  mismatch, left as is: [{$media->id}] {$media->path} is {$detectedType}");

            return 'mismatched';
        }

        $directory = dirname(ltrim($media->path, '/'));
        $name = filled($media->name) ? $media->name : pathinfo($media->path, PATHINFO_FILENAME);
        $newPath = ($directory === '.' ? '' : $directory . '/') . $name . '.' . $expectedExtension;

        if ($newPath !== $media->path && $disk->exists($newPath)) {
            $this->warn("  skipped (target exists): [{$media->id}] {$media->path} -> {$newPath}");

            return 'skipped';
        }

        $content = null;

        // Giving markup an .svg extension makes it render inline, so it has to be
        // sanitized on the way.
        if (Curator::isSvgMimeType($detectedType)) {
            $original = $disk->get($media->path);
            $content = Curator::sanitizeSvg((string) $original);

            if ($content === '' && $original !== '') {
                $this->warn("  skipped (could not sanitize): [{$media->id}] {$media->path}");

                return 'skipped';
            }
        }

        if (! $dryRun) {
            $oldPath = $media->path;

            if ($content !== null) {
                $disk->put($newPath, $content, $media->visibility ?? 'public');

                if ($newPath !== $oldPath) {
                    $disk->delete($oldPath);
                }
            } elseif ($newPath !== $oldPath && ! $disk->move($oldPath, $newPath)) {
                $this->error("  error: [{$media->id}] could not move {$oldPath} to {$newPath}");

                return 'skipped';
            }

            $media->forceFill([
                'path' => $newPath,
                'ext' => $expectedExtension,
                'type' => $detectedType,
                'size' => $disk->size($newPath),
            ])->saveQuietly();

            Glide::getServer()->deleteCache($oldPath);
        }

        $this->line(($dryRun ? '  would rename: ' : '  renamed: ') . "[{$media->id}] {$media->path} -> {$newPath} ({$detectedType})");

        return 'repaired';
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
     * Whether a web server could serve a file with this extension as a
     * document, script or server-side code.
     */
    protected function isUnsafe(string $extension): bool
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
