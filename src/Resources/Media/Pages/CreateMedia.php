<?php

declare(strict_types=1);

namespace Awcodes\Curator\Resources\Media\Pages;

use Awcodes\Curator\Resources\Media\MediaResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['title'])) {
            $data['title'] = pathinfo((string) $data['originalFilename'], PATHINFO_FILENAME);
        }

        unset($data['originalFilename']);

        return $data;
    }
}
