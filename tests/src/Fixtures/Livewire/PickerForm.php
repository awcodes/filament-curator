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
 * A form holding one picker, named `media` unless the test names it, configured by the test, optionally bound to a
 * record so it can save to the record and its relationships.
 */
class PickerForm extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    /** @var (Closure(CuratorPicker): CuratorPicker)|null */
    public static ?Closure $configurePicker = null;

    public static string $fieldName = 'media';

    /** @var (Closure(CuratorPicker): array<int, \Filament\Schemas\Components\Component>)|null */
    public static ?Closure $wrapPicker = null;

    public ?Model $record = null;

    public ?array $data = [];

    public mixed $saved = null;

    /**
     * The record isn't typed: Laravel 12's container resolves a class-typed mount parameter that isn't passed rather
     * than using its default, and a Model can't be built.
     */
    public function mount(mixed $record = null, mixed $initial = null): void
    {
        $this->record = $record instanceof Model ? $record : null;

        $this->form->fill($record instanceof Model ? $record->attributesToArray() : [static::$fieldName => $initial]);
    }

    public function form(Schema $schema): Schema
    {
        $picker = CuratorPicker::make(static::$fieldName);

        if (static::$configurePicker instanceof Closure) {
            $picker = (static::$configurePicker)($picker);
        }

        return $schema
            ->components(static::$wrapPicker instanceof Closure ? (static::$wrapPicker)($picker) : [$picker])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $this->saved = $state[static::$fieldName] ?? null;

        if ($this->record instanceof Model) {
            $this->record->update($state);
            $this->form->saveRelationships();
        }
    }

    public function getPickerKey(): string
    {
        return $this->form->getFlatFields()[static::$fieldName]->getKey();
    }

    public function render(): string
    {
        return '<div>{{ $this->form }}</div>';
    }
}
