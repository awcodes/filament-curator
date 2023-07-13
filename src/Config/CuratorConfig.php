<?php

namespace Awcodes\Curator\Config;

use Awcodes\Curator\Models\Media;
use Closure;
use Filament\Facades\Filament;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Database\Eloquent\Collection;

class CuratorConfig
{
    use EvaluatesClosures;
    use Concerns\HasFileUploadSettings;
    use Concerns\HasGlideSettings;
    use Concerns\HasPathGenerator;
    use Concerns\HasCurationPresets;

    protected bool $isLimitedToDirectory = false;

    protected string|null $mediaModel = null;

    public function getMedia(array|Media|int $ids): Collection|array
    {
        if ($ids instanceof Media) {
            return [$ids];
        }

        $ids = array_values($ids);

        if (isset($ids[0]['id'])) {
            return $ids;
        }

        if (filled($ids)) {
            return $this->getMediaModel()::whereIn('id', $ids)
                ->get()
                ->sortBy(function ($model) use ($ids) {
                    return array_search($model->id, $ids);
                });
        }

        return [];
    }

    public function getMediaModel(): string
    {
        return $this->mediaModel ?? Media::class;
    }

    public function getMediaModelResource(): string
    {
        return Filament::getModelResource($this->getMediaModel());
    }

    public function isLimitedToDirectory(): bool
    {
        return $this->evaluate($this->isLimitedToDirectory);
    }

    public function isResizable(string $ext): bool
    {
        return in_array($ext, ['jpeg', 'jpg', 'png', 'webp', 'bmp']);
    }

    public function limitToDirectory(bool|Closure|null $condition = true): static
    {
        $this->isLimitedToDirectory = $condition;

        return $this;
    }

    public function mediaModel(string $model): static
    {
        $this->mediaModel = $model;

        return $this;
    }
}