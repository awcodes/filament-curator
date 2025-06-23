<?php

declare(strict_types=1);

namespace Awcodes\Curator\Resources\MediaResource;

use Awcodes\Curator\Resources\MediaResource;
use Filament\Resources\Pages\CreateRecord;

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
