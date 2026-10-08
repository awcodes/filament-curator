<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Livewire;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Closure;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

/**
 * A form holding one picker named `media`, configured by the test, optionally bound to a record so relationship
 * pickers can save.
 */
class PickerForm extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    /** @var (Closure(CuratorPicker): CuratorPicker)|null */
    public static ?Closure $configurePicker = null;

    public ?Model $record = null;

    public ?array $data = [];

    public mixed $saved = null;

    public function mount(?Model $record = null, mixed $initial = null): void
    {
        $this->record = $record;

        $this->form->fill($record instanceof Model ? $record->attributesToArray() : ['media' => $initial]);
    }

    public function form(Schema $schema): Schema
    {
        $picker = CuratorPicker::make('media');

        if (static::$configurePicker instanceof Closure) {
            $picker = (static::$configurePicker)($picker);
        }

        return $schema
            ->components([$picker])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->saved = $this->form->getState()['media'] ?? null;

        if ($this->record instanceof Model) {
            $this->record->update(collect($this->form->getState())->except('media')->all());
            $this->form->saveRelationships();
        }
    }

    public function getPickerKey(): string
    {
        return $this->form->getFlatFields()['media']->getKey();
    }

    public function render(): string
    {
        return '<div>{{ $this->form }}</div>';
    }
}
