<?php

namespace Awcodes\Curator\Actions;

use Awcodes\Curator\Models\Media;
use Filament\Forms\Components\Actions\Action;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Livewire\Component;

class MediaAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'filament_tiptap_media';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->arguments([
                'src' => '',
            ])
            ->action(function (TiptapEditor $component, Component $livewire, array $arguments) {

                $selected = $arguments['src'] !== ''
                    ? [App::get(Media::class)->firstWhere('name', Str::of($arguments['src'])->afterLast('/')->beforeLast('.'))]
                    : [];

                $livewire->dispatch('open-modal', id: 'curator-panel', settings: [
                    'acceptedFileTypes' => $component->getAcceptedFileTypes(),
                    'defaultSort' => 'desc',
                    'directory' => $component->getDirectory(),
                    'diskName' => $component->getDisk(),
                    'imageCropAspectRatio' => $component->getImageCropAspectRatio(),
                    'imageResizeTargetWidth' => $component->getImageResizeTargetWidth(),
                    'imageResizeTargetHeight' => $component->getImageResizeTargetHeight(),
                    'imageResizeMode' => $component->getImageResizeMode(),
                    'isLimitedToDirectory' => false,
                    'isTenantAware' => Config::get('curator.is_tenant_aware'),
                    'tenantOwnershipRelationshipName' => Config::get('curator.tenant_ownership_relationship_name'),
                    'isMultiple' => false,
                    'maxItems' => 1,
                    'maxSize' => $component->getMaxSize(),
                    'minSize' => $component->getMinSize(),
                    'modalId' => $component->getLivewire()->getId() . '-form-component-action',
                    'pathGenerator' => Config::get('curator.path_generator'),
                    'rules' => [],
                    'selected' => $selected,
                    'shouldPreserveFilenames' => $component->shouldPreserveFilenames(),
                    'statePath' => $component->getStatePath(),
                    'types' => $component->getAcceptedFileTypes(),
                    'visibility' => $component->getVisibility(),
                ]);
            });
    }
}
