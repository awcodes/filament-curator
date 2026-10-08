<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use League\Glide\Urls\UrlBuilderFactory;

function writePng(string $path, int $width, int $height): void
{
    File::ensureDirectoryExists(dirname($path));

    $image = imagecreatetruecolor($width, $height);
    imagepng($image, $path);
    imagedestroy($image);
}

beforeEach(function () {
    $this->glideName = 'glide-' . Str::random(8) . '.png';
    $this->glideFiles = [
        storage_path('app/public/curator/' . $this->glideName),
        storage_path('app/public/' . $this->glideName),
    ];
});

afterEach(function () {
    File::delete($this->glideFiles);
    File::deleteDirectory(storage_path('app/.cache/curator/' . $this->glideName));
    File::deleteDirectory(storage_path('app/.cache/' . $this->glideName));
});

test('media in a directory named after the glide route is served from its own path', function () {
    writePng($this->glideFiles[0], 20, 10);
    writePng($this->glideFiles[1], 40, 30);

    $url = UrlBuilderFactory::create('/curator/', config('app.key'))
        ->getUrl('curator/' . $this->glideName, ['fm' => 'png']);

    $response = $this->get($url);

    $response->assertOk();

    [$width, $height] = getimagesizefromstring($response->streamedContent());

    expect([$width, $height])->toBe([20, 10]);
});
