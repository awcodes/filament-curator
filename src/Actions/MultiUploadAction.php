<?php

namespace Awcodes\Curator\Actions;

use Awcodes\Curator\Components\Forms\Uploader;
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

        $this
            ->button()
            ->color('gray')
            ->label(trans('curator::forms.multi_upload.action_label'))
            ->modalHeading(trans('curator::forms.multi_upload.modal_heading'))
            ->form([
                Uploader::make('files')
                    ->acceptedFileTypes(config('curator.accepted_file_types'))
                    ->directory(config('curator.directory'))
                    ->disk(config('curator.disk'))
                    ->label(trans('curator::forms.multi_upload.modal_file_label'))
                    ->minSize(config('curator.min_size'))
                    ->maxSize(config('curator.max_size'))
                    ->multiple()
                    ->pathGenerator(config('curator.path_generator'))
                    ->preserveFilenames(config('curator.should_preserve_filenames'))
                    ->required()
                    ->visibility(config('curator.visibility'))
                    ->storeFileNamesIn('originalFilename'),
            ])
            ->action(function ($data) {
                foreach ($data['files'] as $item) {
                    // Fix malformed utf-8 characters
                    if (! empty($item['exif'])) {
                        array_walk_recursive($item['exif'], function (&$entry) {
                            if (! mb_detect_encoding($entry, 'utf-8', true)) {
                                $entry = mb_convert_encoding($entry, 'utf-8');
                            }
                        });
                    }

                    $item['title'] = pathinfo($data['originalFilename'][$item['path']] ?? null, PATHINFO_FILENAME);

                    tap(
                        App::make(Media::class)->create($item),
                        fn (Media $media) => $media->getPrettyName(),
                    )->toArray();
                }
            });
    }
}
