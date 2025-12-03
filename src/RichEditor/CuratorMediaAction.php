<?php

declare(strict_types=1);

namespace Awcodes\Curator\RichEditor;

use Awcodes\Curator\Models\Media;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class CuratorMediaAction extends Action
{
    /**
     * Get the toolbar button for the RichEditor.
     */
    public static function tool(): RichEditorTool
    {
        return RichEditorTool::make('curatorMedia')
            ->label(trans('curator::views.picker.button'))
            ->icon(Heroicon::Photo)
            ->action(arguments: '{ src: $getEditor().getAttributes(\'image\')?.src, id: $getEditor().getAttributes(\'image\')?.id, alt: $getEditor().getAttributes(\'image\')?.alt }')
            ->activeKey('image');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->arguments([
                'src' => '',
                'id' => null,
                'alt' => null,
            ])
            ->modalHeading(trans('curator::views.panel.heading'))
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->modalCloseButton(false)
            ->modalWidth(Width::Screen)
            ->modalContent(function (RichEditor $component, array $arguments): View {
                return view('curator::components.modals.curator-panel', [
                    'key' => $component->getKey(),
                    'settings' => [
                        'acceptedFileTypes' => $component->getFileAttachmentsAcceptedFileTypes() ?? ['image/*'],
                        'defaultSort' => 'desc',
                        'directory' => config('curator.default_directory') ?? '',
                        'diskName' => config('curator.default_disk', 'public'),
                        'imageCropAspectRatio' => null,
                        'imageResizeMode' => null,
                        'imageResizeTargetWidth' => null,
                        'imageResizeTargetHeight' => null,
                        'isLimitedToDirectory' => config('curator.features.directory_restriction', false),
                        'isTenantAware' => config('curator.features.tenancy.enabled', false),
                        'tenantOwnershipRelationshipName' => config('curator.features.tenancy.relationship_name'),
                        'isMultiple' => false,
                        'maxItems' => 1,
                        'maxSize' => $component->getFileAttachmentsMaxSize() ?? 5000,
                        'maxWidth' => null,
                        'minSize' => 0,
                        'pathGenerator' => config('curator.path_generator'),
                        'rules' => [],
                        'selected' => self::getSelectedMedia($arguments),
                        'shouldPreserveFilenames' => false,
                        'statePath' => $component->getStatePath(),
                        'visibility' => config('curator.default_visibility', 'public'),
                    ],
                ]);
            })
            ->action(fn (): null => null);
    }

    protected static function getSelectedMedia(array $arguments): array
    {
        // If editing an existing image, try to find it in Curator by ID first
        if (! empty($arguments['id']) && is_numeric($arguments['id'])) {
            $media = Media::find($arguments['id']);
            if ($media) {
                return [$media->toArray()];
            }
        }

        // Try to find by src URL
        if (! empty($arguments['src'])) {
            $filename = Str::of($arguments['src'])->afterLast('/')->beforeLast('.');
            $media = Media::where('name', $filename)->first();
            if ($media) {
                return [$media->toArray()];
            }
        }

        return [];
    }

    public static function getDefaultName(): ?string
    {
        return 'curatorMedia';
    }
}
