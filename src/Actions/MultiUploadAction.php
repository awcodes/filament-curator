<?php

namespace Awcodes\Curator\Actions;

use Awcodes\Curator\Components\Forms\Uploader;
use Awcodes\Curator\Config\CuratorManager;
use Awcodes\Curator\Models\Media;
use Filament\Actions\Action;
use Illuminate\Support\Facades\App;

class MultiUploadAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'curator_multi_upload';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $config = app(CuratorManager::class);

        $this
            ->button()
            ->color('gray')
            ->label(trans('curator::forms.multi_upload.action_label'))
            ->modalHeading(trans('curator::forms.multi_upload.modal_heading'))
            ->form([
                Uploader::make('files')
                    ->acceptedFileTypes($config->getAcceptedFileTypes())
                    ->directory($config->getDirectory())
                    ->disk($config->getDiskName())
                    ->label(trans('curator::forms.multi_upload.modal_file_label'))
                    ->minSize($config->getMinSize())
                    ->maxSize($config->getMaxSize())
                    ->multiple()
//                    ->pathGenerator(config('curator.path_generator'))
                    ->preserveFilenames($config->shouldPreserveFilenames())
                    ->required()
                    ->visibility($config->getVisibility())
                    ->storeFileNamesIn('originalFilename'),
            ])
            ->action(function ($data) {
                foreach ($data['files'] as $item) {
                    // Fix malformed utf-8 characters
                    if (!empty($item['exif'])) {
                        array_walk_recursive($item['exif'], function (&$entry) {
                            if (!mb_detect_encoding($entry, 'utf-8', true)) {
                                $entry = utf8_encode($entry);
                            }
                        });
                    }

                    $item['title'] = pathinfo($data['originalFilename'][$item['path']] ?? null, PATHINFO_FILENAME);

                    tap(
                        App::make(Media::class)->create($item),
                        fn(Media $media) => $media->getPrettyName(),
                    )->toArray();
                }
            });
    }
}
