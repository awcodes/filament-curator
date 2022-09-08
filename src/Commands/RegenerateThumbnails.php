<?php

namespace FilamentCurator\Commands;

use FilamentCurator\Thumbnails;
use Illuminate\Console\Command;
use FilamentCurator\Models\Media;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use FilamentCurator\Facades\CuratorThumbnails;

class RegenerateThumbnails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'curator:regenerate-thumbnails {--path : regenerate from path instead of url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate thumbnails for the site.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $media = Media::all();

        if ($media->isNotEmpty()) {
            $this->info('Regenerating...');

            $progress = $this->output->createProgressBar(count($media));

            $progress->start();

            $this->newLine();

            foreach ($media as $item) {
                if (CuratorThumbnails::hasSizes($item->ext)) {
                    CuratorThumbnails::destroy($item);
                    CuratorThumbnails::generate($item, $this->option('path'));
                }

                $progress->advance();
            }

            $progress->finish();
        } else {
            $this->comment('Nothing to regenerate.');
        }

        return self::SUCCESS;
    }
}
