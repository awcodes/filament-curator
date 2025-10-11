<?php

declare(strict_types=1);

namespace Awcodes\Curator\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Config;

class RichEditorMediaAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->arguments(['src' => ''])
            ->modalHeading('Select Media from Library')
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->modalWidth(Width::Screen)
            ->modalContent(function (RichEditor $component, array $arguments): View {
                return view('curator::components.modals.curator-panel', [
                    'key' => $component->getKey(),
                    'settings' => [
                        'acceptedFileTypes' => $component->getFileAttachmentsAcceptedFileTypes() ?? Config::get('curator.accepted_file_types'),
                        'defaultSort' => 'desc',
                        'directory' => Config::get('curator.directory'),
                        'diskName' => Config::get('curator.disk'),
                        'imageCropAspectRatio' => Config::get('curator.image_crop_aspect_ratio'),
                        'imageResizeTargetWidth' => Config::get('curator.image_resize_target_width'),
                        'imageResizeTargetHeight' => Config::get('curator.image_resize_target_height'),
                        'imageResizeMode' => Config::get('curator.image_resize_mode'),
                        'isLimitedToDirectory' => false,
                        'isTenantAware' => Config::get('curator.is_tenant_aware'),
                        'tenantOwnershipRelationshipName' => Config::get('curator.tenant_ownership_relationship_name'),
                        'isMultiple' => false,
                        'maxItems' => 1,
                        'maxSize' => $component->getFileAttachmentsMaxSize() ?? Config::get('curator.max_size'),
                        'maxWidth' => Config::get('curator.max_width'),
                        'minSize' => Config::get('curator.min_size'),
                        'pathGenerator' => Config::get('curator.path_generator'),
                        'rules' => [],
                        'selected' => [],
                        'shouldPreserveFilenames' => Config::get('curator.should_preserve_filenames'),
                        'statePath' => $component->getStatePath(),
                        'visibility' => Config::get('curator.visibility'),
                    ],
                ]);
            })
            ->action(fn (): null => null);
    }

    public static function getDefaultName(): ?string
    {
        return 'filament_tiptap_media';
    }
}