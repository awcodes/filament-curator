<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();

        $this->form->fill([
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
    }
}
