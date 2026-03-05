<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Forms\RichEditor;

use Awcodes\Curator\Facades\Curator;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;
use Tiptap\Core\Extension;

class AttachCuratorMediaPlugin implements RichContentPlugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * @return array<Extension>
     */
    public function getTipTapPhpExtensions(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    public function getTipTapJsExtensions(): array
    {
        return [
            FilamentAsset::getScriptSrc('rich-editor-integration', 'awcodes/curator'),
        ];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        return [
            RichEditorTool::make('attachCuratorMedia')
                ->label(__('curator::views.attach_curator_media.modal.heading'))
                ->icon('heroicon-o-photo')
                ->action('attachCuratorMedia'),
        ];
    }

    /**
     * @return array<Action>
     */
    public function getEditorActions(): array
    {
        return [
            Action::make('attachCuratorMedia')
                ->modalWidth(Width::Screen)
                ->modalHeading(__('curator::views.attach_curator_media.modal.heading'))
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
                ->modalContent(fn (RichEditor $component, array $arguments): View => view('curator::components.modals.curator-panel', [
                    'key' => $component->getKey(),
                    'settings' => [
                        'acceptedFileTypes' => $component->getFileAttachmentsAcceptedFileTypes() ?? Curator::getAcceptedFileTypes(),
                        'defaultSort' => 'desc',
                        'directory' => $component->getFileAttachmentsDirectory() ?? Curator::getDirectory(),
                        'diskName' => $component->getFileAttachmentsDiskName() ?? Curator::getDiskName(),
                        'imageCropAspectRatio' => Curator::getImageCropAspectRatio(),
                        'imageResizeTargetWidth' => Curator::getImageResizeTargetWidth(),
                        'imageResizeTargetHeight' => Curator::getImageResizeTargetHeight(),
                        'imageResizeMode' => Curator::getImageResizeMode(),
                        'isLimitedToDirectory' => false,
                        'isTenantAware' => Curator::isTenantAware(),
                        'tenantOwnershipRelationshipName' => Curator::getTenantName(),
                        'isMultiple' => false,
                        'maxItems' => 1,
                        'maxSize' => $component->getFileAttachmentsMaxSize() ?? Curator::getMaxSize(),
                        'minSize' => Curator::getMinSize(),
                        'pathGenerator' => Config::get('curator.path_generator'),
                        'rules' => [],
                        'selected' => [],
                        'shouldPreserveFilenames' => Curator::shouldPreserveFilenames(),
                        'statePath' => $component->getStatePath(),
                        'context' => 'richEditor',
                        'visibility' => $component->getFileAttachmentsVisibility() ?? Curator::getVisibility(),
                    ],
                ]))
                ->action(fn (): null => null),
        ];
    }
}
