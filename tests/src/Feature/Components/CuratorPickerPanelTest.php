<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Tests\Fixtures\Livewire\PickerForm;
use Filament\Actions\Testing\TestAction;
use Illuminate\Validation\Rule;
use Livewire\Livewire;

beforeEach(function () {
    PickerForm::$configurePicker = null;
    PickerForm::$fieldName = 'media';
    PickerForm::$wrapPicker = null;
});

it('opens the panel', function () {
    Livewire::test(PickerForm::class)
        ->mountAction(TestAction::make('launchPanel')->schemaComponent('media', schema: 'form'))
        ->assertActionMounted(TestAction::make('launchPanel')->schemaComponent('media', schema: 'form'))
        ->assertOk();
});

it('opens the panel when the picker has closure and rule object rules', function () {
    PickerForm::$configurePicker = fn (CuratorPicker $picker): CuratorPicker => $picker
        ->multiple()
        ->maxItems(3)
        ->rules([
            fn (): Closure => fn (string $attribute, mixed $value, Closure $fail) => null,
            Rule::dimensions()->maxWidth(2000),
            Rule::exists('media', 'id'),
        ]);

    Livewire::test(PickerForm::class)
        ->mountAction(TestAction::make('launchPanel')->schemaComponent('media', schema: 'form'))
        ->assertOk();
});

it('passes only string rules to the panel', function () {
    PickerForm::$configurePicker = fn (CuratorPicker $picker): CuratorPicker => $picker
        ->rules([
            'image',
            fn (): Closure => fn (string $attribute, mixed $value, Closure $fail) => null,
            Rule::dimensions()->maxWidth(2000),
        ]);

    $form = Livewire::test(PickerForm::class)->instance();
    $picker = $form->form->getFlatFields()['media'];

    $rules = (fn (): array => $this->getPanelValidationRules())->call($picker);

    expect($rules)
        ->each->toBeString()
        ->and($rules)->toContain('image', 'dimensions:max_width=2000');
});
