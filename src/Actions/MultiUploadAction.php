<?php

declare(strict_types=1);

namespace Awcodes\Curator\Actions;

use Awcodes\Curator\Components\Forms\Uploader;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Exception;
use Filament\Actions\Action;
use Illuminate\Support\Facades\App;

class MultiUploadAction extends Action
{
    /** @throws Exception */
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->button()
            ->color('gray')
            ->label(trans('curator::forms.multi_upload.action_label'))
            ->modalHeading(trans('curator::forms.multi_upload.modal_heading'))
            ->schema([
                Uploader::make('files')
                    ->acceptedFileTypes(Curator::getAcceptedFileTypes())
                    ->directory(Curator::getDirectory())
                    ->disk(Curator::getDiskName())
                    ->label(trans('curator::forms.multi_upload.modal_file_label'))
                    ->minSize(Curator::getMinSize())
                    ->maxSize(Curator::getMaxSize())
                    ->multiple()
                    ->panelLayout('grid')
//                    ->pathGenerator(config('curator.path_generator'))
                    ->preserveFilenames(Curator::shouldPreserveFilenames())
                    ->required()
                    ->visibility(Curator::getVisibility())
                    ->storeFileNamesIn('originalFilename')
                    ->imageCropAspectRatio(Curator::getImageCropAspectRatio())
                    ->imageResizeMode(Curator::getImageResizeMode())
                    ->imageResizeTargetWidth(Curator::getImageResizeTargetWidth())
                    ->imageResizeTargetHeight(Curator::getImageResizeTargetHeight()),
            ])
            ->action(function (array $data): void {
                foreach ($data['files'] as $item) {
                    $item['exif'] = empty($item['exif']) ? null : Curator::sanitizeExif($item['exif']);
                    $item['title'] = pathinfo((string) ($data['originalFilename'][$item['path']] ?? null), PATHINFO_FILENAME);

                    tap(
                        App::make(Media::class)->create($item),
                        fn (Media $media): string => $media->getPrettyName(),
                    );
                }
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'curator_multi_upload';
    }
}
