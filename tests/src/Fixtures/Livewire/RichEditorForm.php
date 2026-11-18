<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

/**
 * A form holding one RichEditor for a record's `content`, as an edit page fills it from the record and saves it back.
 */
class RichEditorForm extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    /**
     * The record isn't typed: Laravel 12's container resolves a class-typed mount parameter that isn't passed rather
     * than using its default, and a Model can't be built.
     */
    public mixed $record = null;

    public ?array $data = [];

    public mixed $saved = null;

    public function mount(mixed $record = null): void
    {
        $this->record = $record instanceof Model ? $record : null;

        $this->form->fill($this->record?->attributesToArray() ?? []);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([RichEditor::make('content')])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->saved = $this->form->getState()['content'] ?? null;
    }

    public function render(): string
    {
        return '<div>{{ $this->form }}</div>';
    }
}
