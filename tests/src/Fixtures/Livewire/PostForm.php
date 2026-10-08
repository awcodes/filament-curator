<?php

namespace Awcodes\Curator\Tests\Fixtures\Livewire;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Tests\Fixtures\Models\Post;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class PostForm extends Component implements HasForms
{
    use InteractsWithForms;

    public Post $post;

    public ?array $data = [];

    public function mount(Post $post): void
    {
        $this->post = $post;

        $this->form->fill($post->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title'),
                CuratorPicker::make('image_id')
                    ->acceptedFileTypes(['image/*']),
                Repeater::make('gallery')
                    ->schema([
                        CuratorPicker::make('image')->acceptedFileTypes(['image/*']),
                        TextInput::make('caption'),
                    ]),
                Builder::make('content')
                    ->blocks([
                        Builder\Block::make('hero')->schema([
                            CuratorPicker::make('image')->acceptedFileTypes(['image/*']),
                        ]),
                        Builder\Block::make('text')->schema([
                            TextInput::make('body'),
                        ]),
                    ]),
                Group::make([
                    CuratorPicker::make('cover')->acceptedFileTypes(['image/*']),
                ])->statePath('meta'),
                CuratorPicker::make('settings.logo')->acceptedFileTypes(['image/*']),
            ])
            ->model($this->post)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->post->update($this->form->getState());
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>{{ $this->form }}</div>
            BLADE;
    }
}
