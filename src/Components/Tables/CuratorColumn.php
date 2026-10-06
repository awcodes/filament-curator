<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Tables;

use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Glide\GlideBuilder;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Providers\GlideUrlProvider;
use Closure;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

class CuratorColumn extends ImageColumn
{
    protected int | Closure | null $resolution = null;

    protected string $view = 'curator::components.tables.curator-column';

    public function getMedia(): Media | Collection | array | null
    {
        $record = $this->getRecord();

        if (! is_a($record, Media::class)) {
            $state = $this->getState();

            if (is_a($state, Collection::class)) {
                return $state->take($this->limit);
            }

            if (is_a($state, Media::class)) {
                return Arr::wrap($state);
            }

            $state = Arr::wrap($state);

            return get_media_items(array_slice($state, 0, $this->limit));
        }

        return Arr::wrap($record);
    }

    /**
     * With a resolution set, the image is requested from Glide at the column's display size multiplied by it, so it
     * stays sharp on high-density screens. Without one, or with a custom URL provider, the thumbnail is used.
     */
    public function getMediaUrl(Media $item): string
    {
        $resolution = $this->getResolution();

        if (! $resolution || ! is_media_resizable((string) $item->ext) || ! Curator::getUrlProvider() instanceof GlideUrlProvider) {
            return $item->thumbnail_url;
        }

        $height = (int) $this->getImageHeight() * $resolution;
        $width = (int) ($this->getImageWidth() ?? ($this->isRounded() ? $this->getImageHeight() : null)) * $resolution;

        if (! $width && ! $height) {
            return $item->thumbnail_url;
        }

        $glide = GlideBuilder::make()->format('webp')->fit('crop');

        if ($width) {
            $glide->width($width);
        }

        if ($height) {
            $glide->height($height);
        }

        return $glide->toUrl($item->path);
    }

    public function getResolution(): ?int
    {
        return $this->evaluate($this->resolution);
    }

    public function resolution(int | Closure | null $resolution): static
    {
        $this->resolution = $resolution;

        return $this;
    }

    public function applyEagerLoading(EloquentBuilder | Relation $query): EloquentBuilder | Relation
    {
        $model = $query->getModel();

        if (! $this->hasRelationship($query->getModel())) {
            return $query;
        }

        if ($model instanceof Media || is_subclass_of(Media::class, $model)) {
            return $query;
        }

        $relationshipName = $this->getRelationshipName();

        if (array_key_exists($relationshipName, $query->getEagerLoads())) {
            return $query;
        }

        return $query->with([$relationshipName]);
    }
}
