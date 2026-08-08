<?php

declare(strict_types=1);

namespace Awcodes\Curator\Commands;

use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SanitizeSvgsCommand extends Command
{
    public $signature = 'curator:sanitize-svgs
        {--dry-run : Report what would change without writing any files}';

    public $description = 'Re-sanitize SVG media already stored on disk to strip any embedded scripts.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $model = App::make(Media::class);

        // ext is stored lowercased on upload, but guard against legacy/manual
        // records by comparing case-insensitively.
        $query = $model::query()->whereRaw('LOWER(ext) = ?', ['svg']);

        $total = $query->count();

        if ($total === 0) {
            $this->info('No SVG media found.');

            return self::SUCCESS;
        }

        $this->info(($dryRun ? '[dry run] ' : '') . "Scanning {$total} SVG file(s)...");

        $cleaned = 0;
        $unchanged = 0;
        $skipped = 0;

        $query->chunkById(100, function ($records) use (&$cleaned, &$unchanged, &$skipped, $dryRun): void {
            foreach ($records as $media) {
                $disk = Storage::disk($media->disk);

                try {
                    if (! $disk->exists($media->path)) {
                        $this->warn("  skipped (missing file): [{$media->id}] {$media->path}");
                        $skipped++;

                        continue;
                    }

                    $original = $disk->get($media->path);
                    $clean = Curator::sanitizeSvg($original);

                    // sanitizeSvg fails closed with an empty string. Never let a
                    // batch job blank out an existing asset — warn and leave it.
                    if ($clean === '' && $original !== '') {
                        $this->warn("  skipped (could not sanitize): [{$media->id}] {$media->path}");
                        $skipped++;

                        continue;
                    }

                    if ($clean === $original) {
                        $unchanged++;

                        continue;
                    }

                    if (! $dryRun) {
                        $disk->put($media->path, $clean, $media->visibility ?? 'public');
                        $media->forceFill(['size' => $disk->size($media->path)])->saveQuietly();
                    }

                    $this->line(($dryRun ? '  would sanitize: ' : '  sanitized: ') . "[{$media->id}] {$media->path}");
                    $cleaned++;
                } catch (Throwable $e) {
                    $this->error("  error: [{$media->id}] {$media->path} — {$e->getMessage()}");
                    $skipped++;
                }
            }
        });

        $this->newLine();
        $this->info(sprintf(
            '%sDone. %d %s, %d unchanged, %d skipped.',
            $dryRun ? '[dry run] ' : '',
            $cleaned,
            $dryRun ? 'would be sanitized' : 'sanitized',
            $unchanged,
            $skipped,
        ));

        return self::SUCCESS;
    }
}
