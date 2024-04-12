<?php

namespace Awcodes\Curator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateGlideTokenCommand extends Command
{
    public $signature = 'curator:token';

    public $description = 'Generate a new token for Glide.';

    public function handle(): int
    {
        $token = Str::password(symbols: false);
        $path = app()->environmentFilePath();
        $key = 'CURATOR_GLIDE_TOKEN';

        $file = file_get_contents($path);

        if (! str_contains($file, $key)) {
            $file = $file . "\r\n" . $key . '=' . $token;

            file_put_contents($path, $file);
        } else {
            file_put_contents($path, preg_replace("/^{$key}.*$/m", "{$key}={$token}", $file));
        }

        $this->info('Curator Glide token successfully regenerated.');

        return self::SUCCESS;
    }
}
