<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Forms\RichEditor;

use Awcodes\Curator\Facades\Curator;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\Contracts\FileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\HasFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;
use Tiptap\Core\Extension;

class AttachCuratorMediaPlugin implements HasFileAttachmentProvider, RichContentPlugin
{
    use EvaluatesClosures;

    protected bool | Closure $shouldStoreMediaIds = false;

    protected ?FileAttachmentProvider $fileAttachmentProvider = null;

    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * Insert media with its key in the image's `data-id`, as Filament stores its own attachments, so the image's URL
     * is looked up from the library whenever the content is edited or rendered.
     *
     * Filament reads `data-id` through the editor's file attachment provider and, without one, as a path on its disk,
     * which loses the image's src. The plugin supplies CuratorFileAttachmentProvider, or the one given, wherever
     * Filament takes a provider from its plugins: a model's rich content attribute and RichContentRenderer. An editor
     * outside both, such as one in a nested form, needs the provider set on the field itself.
     */
    public function storeMediaIds(bool | Closure $condition = true, ?FileAttachmentProvider $provider = null): static
    {
        $this->shouldStoreMediaIds = $condition;
        $this->fileAttachmentProvider = $provider;

        return $this;
    }

    public function shouldStoreMediaIds(): bool
    {
        return (bool) $this->evaluate($this->shouldStoreMediaIds);
    }

    public function getFileAttachmentProvider(): ?FileAttachmentProvider
    {
        if (! $this->shouldStoreMediaIds()) {
            return null;
        }

        return $this->fileAttachmentProvider ??= CuratorFileAttachmentProvider::make();
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
                        'diskName' => $this->getExplicitAttachmentSetting($component, 'fileAttachmentsDiskName', 'getDefaultFileAttachmentsDiskName') ?? Curator::getDiskName(),
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
                        'storeMediaIds' => $this->shouldStoreMediaIds(),
                        'visibility' => $this->getExplicitAttachmentSetting($component, 'fileAttachmentsVisibility', 'getDefaultFileAttachmentsVisibility') ?? Curator::getVisibility(),
                    ],
                ]))
                ->action(fn (): null => null),
        ];
    }

    /**
     * An attachment setting the app set on the editor, or on the model's rich content attribute, or null. Filament's
     * own getters fall back to its default disk and visibility instead, which would override Curator's.
     */
    protected function getExplicitAttachmentSetting(RichEditor $component, string $property, string $defaultMethod): ?string
    {
        $value = (fn (): mixed => $this->evaluate($this->{$property}) ?? $this->{$defaultMethod}())->call($component);

        return is_string($value) && filled($value) ? $value : null;
    }
}
