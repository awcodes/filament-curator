<?php

namespace Awcodes\Curator\Database\Factories;

use Awcodes\Curator\CuratorUtils;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
    protected $model = Media::class;

    protected ?string $directory = null;

    protected ?string $disk = null;

    protected ?string $type = null;

    protected ?string $fixturesPath = null;

    /**
     * @throws Exception
     */
    public function definition(): array
    {
        return match ($this->getType()) {
            'svg' => $this->handleSvg(),
            'document' => $this->handleDocument(),
            'video' => $this->handleVideo(),
            default => $this->handleImage(),
        };
    }

    public function private(): MediaFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'visibility' => 'private',
            ];
        });
    }

    public function randomTimestamps(): MediaFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'created_at' => Carbon::now()->addDays(rand(-800, 0))->addMinutes(rand(
                    0,
                    60 * 23
                ))->addSeconds(rand(0, 60)),
                'updated_at' => Carbon::now()->addDays(rand(-799, 0))->addMinutes(rand(
                    0,
                    60 * 23
                ))->addSeconds(rand(0, 60)),
            ];
        });
    }

    public function fixturesPath(string $path): static
    {
        $this->fixturesPath = $path;

        return $this;
    }

    public function directory(string $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    public function disk(string $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getFixturesPath(): ?string
    {
        return $this->fixturesPath ?? database_path('seeders/fixtures/');
    }

    public function getDirectory(): ?string
    {
        return $this->directory ?? Curator::getDirectory();
    }

    public function getDisk(): ?string
    {
        return $this->disk ?? Curator::getDiskName();
    }

    public function getType(): string
    {
        return $this->type ?? 'image';
    }

    /**
     * @throws Exception
     */
    public function handleImage(): array
    {
        $filename = collect([
            'alberto-restifo-Ni4NgA64TFQ-unsplash',
            'blake-verdoorn-cssvEZacHvQ-unsplash',
            'daniel-roe-lpjb_UMOyx8-unsplash',
            'dave-hoefler-lsoogGC_5dg-unsplash',
            'david-marcu-78A265wPiO4-unsplash',
            'dawid-zawila--G3rw6Y02D0-unsplash',
            'eberhard-grossgasteiger-pBgnT4KH8d4-unsplash',
            'eberhard-grossgasteiger-y2azHvupCVo-unsplash',
            'fabian-quintero-UWQP2mh5YJI-unsplash',
            'federico-respini-sYffw0LNr7s-unsplash',
            'felix-mittermeier-L4-16dmZ-1c-unsplash',
            'guillaume-briard-QegnXyECDfw-unsplash',
            'henry-be-IicyiaPYGGI-unsplash',
            'igor-kasalovic-tNDvFkxkBHo-unsplash',
            'joel-vodell-TApAkERW5pQ-unsplash',
            'kees-streefkerk-Adl90-aXYwA-unsplash',
            'luca-bravo-zAjdgNXsMeg-unsplash',
            'lukasz-szmigiel-jFCViYFYcus-unsplash',
            'niko-photos-tGTVxeOr_Rs-unsplash',
            'robert-lukeman-_RBcxo9AU-U-unsplash',
            'robert-lukeman-zNN6ubHmruI-unsplash',
            'tim-swaan-eOpewngf68w-unsplash',
        ])->random() . '.jpg';

        return CuratorUtils::importMedia(
            path: $this->getFixturesPath() . $filename,
            disk: $this->getDisk(),
            directory: $this->getDirectory(),
            alt: $this->faker->words(rand(3, 8), true),
            caption: $this->faker->words(rand(3, 8), true),
            description: $this->faker->words(rand(3, 8), true),
        );
    }

    /**
     * @throws Exception
     */
    public function handleSvg(): array
    {
        $filename = collect([
            'awcodes-logo',
            'search',
            'screen',
            'brand',
            'apps',
            'link',
        ])->random() . '.svg';

        return CuratorUtils::importMedia(
            path: $this->getFixturesPath() . $filename,
            disk: $this->getDisk(),
            directory: $this->getDirectory(),
            alt: $this->faker->words(rand(3, 8), true),
            caption: $this->faker->words(rand(3, 8), true),
            description: $this->faker->words(rand(3, 8), true),
        );
    }

    /**
     * @throws Exception
     */
    public function handleDocument(): array
    {
        $filename = collect([
            'curator-sucks-excel.xlsx',
            'curator-rocks-excel.xlsx',
            'curator-rocks-word.docx',
            'curator-sucks-word.docx',
        ])->random();

        return CuratorUtils::importMedia(
            path: $this->getFixturesPath() . $filename,
            disk: $this->getDisk(),
            directory: $this->getDirectory(),
        );
    }

    /**
     * @throws Exception
     */
    public function handleVideo(): array
    {
        $filename = collect([
            'panel-video.mp4',
            'generic-video.mov',
            'generic-video-2.mov',
        ])->random();

        return CuratorUtils::importMedia(
            path: $this->getFixturesPath() . $filename,
            disk: $this->getDisk(),
            directory: $this->getDirectory(),
        );
    }
}
