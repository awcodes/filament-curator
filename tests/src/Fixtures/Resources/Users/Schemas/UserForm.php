<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Resources\Users\Schemas;

use Exception;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    /** @throws Exception */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->unique(ignoreRecord: true)
                    ->required(),
            ]);
    }
}
