<?php

namespace FilamentCurator\Commands;

use FilamentCurator\Facades\CuratorThumbnails;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegenerateThumbnails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'curator:regenerate-thumbnails 
        {--chunk=100 : number of items to chunk for processing}
    ';

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
    public function handle(): int
    {
        $mediaCount = DB::table('media')->count();

        if ($mediaCount > 0) {
            $this->info('Regenerating...');

            $progress = $this->output->createProgressBar($mediaCount);

            $this->info($this->option('chunk'));

            DB::table('media')->orderBy('id')->chunk($this->option('chunk'), function ($media) use ($progress) {
                foreach ($media as $item) {
                    if (CuratorThumbnails::hasSizes($item->ext)) {
                        CuratorThumbnails::destroy($item);
                        CuratorThumbnails::generate($item);
                    }

                    $progress->advance();
                }
            });

            $progress->finish();
        } else {
            $this->comment('Nothing to regenerate.');
        }

        return self::SUCCESS;
    }
}
