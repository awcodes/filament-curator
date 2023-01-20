<?php

namespace Awcodes\Curator\Components\Forms;

use Awcodes\Curator\Actions\DownloadAction;
use Awcodes\Curator\Actions\PickerAction;
use Awcodes\Curator\Models\Media;
use Closure;
use Exception;
use Filament\Forms\Components\Field;
use Filament\Support\Actions\Concerns\CanBeOutlined;
use Filament\Support\Actions\Concerns\HasColor;
use Filament\Support\Actions\Concerns\HasSize;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CuratorPicker extends Field
{
    use HasColor;
    use HasSize;
    use CanBeOutlined;

    protected string $view = 'curator::components.forms.picker';

    protected string|Htmlable|Closure|null $buttonLabel = null;

    protected bool|Closure|null $isConstrained = false;

    protected string|Closure|null $curatorDiskName = 'public';

    protected string|Closure|null $curatorDirectory = 'media';

    protected string|null $curatorPathGenerator = null;

    protected string|Closure|null $curatorVisibility = 'public';

    protected array|Closure $curatorAcceptedFileTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'application/pdf'];

    protected bool|Closure $curatorShouldPreserveFilenames = false;

    protected int|Closure|null $curatorMaxSize = 1024;

    protected int|Closure|null $curatorMinSize = null;

    protected string|Closure|null $curatorImageCropAspectRatio = null;

    protected string|Closure|null $curatorImageResizeTargetHeight = null;

    protected string|Closure|null $curatorImageResizeTargetWidth = null;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->buttonLabel = __('curator::views.picker.button');
        $this->size = 'md';
        $this->color = 'primary';
        $this->isOutlined = true;

        $this->curatorDirectory = app('curator')->getDirectory();
        $this->curatorPathGenerator = app('curator')->getPathGenerator();

        $this->curatorShouldPreserveFilenames = app('curator')->shouldPreserveFilenames();
        $this->maxWidth = app('curator')->getMaxWidth();
        $this->curatorMinSize = app('curator')->getMinSize();
        $this->curatorMaxSize = app('curator')->getMaxSize();
        $this->curatorAcceptedFileTypes = app('curator')->getAcceptedFileTypes();
        $this->curatorDiskName = app('curator')->getDiskName();
        $this->curatorVisibility = app('curator')->getVisibility();
        $this->curatorImageCropAspectRatio = app('curator')->getImageCropAspectRatio();
        $this->curatorImageResizeTargetHeight = app('curator')->getImageResizeTargetHeight();
        $this->curatorImageResizeTargetWidth = app('curator')->getImageResizeTargetWidth();

        $this->registerActions([
            PickerAction::make(),
            DownloadAction::make(),
        ]);
    }

    public function buttonLabel(string|Htmlable|Closure|null $label): static
    {
        $this->buttonLabel = $label;

        return $this;
    }

    public function constrained(bool|Closure|null $condition = true): static
    {
        $this->isConstrained = $condition;

        return $this;
    }

    public function directory(Closure|string $directory): static
    {
        $this->curatorDirectory = $directory;

        return $this;
    }

    public function pathGenerator(string|null $generator): static
    {
        $this->curatorPathGenerator = $generator;

        return $this;
    }

    public function acceptedFileTypes(array|Arrayable|Closure $types): static
    {
        $this->curatorAcceptedFileTypes = $types;

        return $this;
    }

    public function disk(string|Closure|null $name): static
    {
        $this->curatorDiskName = $name;

        return $this;
    }

    public function maxSize(int|Closure|null $size): static
    {
        $this->curatorMaxSize = $size;

        return $this;
    }

    public function minSize(int|Closure|null $size): static
    {
        $this->curatorMinSize = $size;

        return $this;
    }

    public function imageCropAspectRatio(string|Closure|null $ratio): static
    {
        $this->curatorImageCropAspectRatio = $ratio;

        return $this;
    }

    public function imageResizeTargetHeight(string|Closure|null $height): static
    {
        $this->curatorImageResizeTargetHeight = $height;

        return $this;
    }

    public function imageResizeTargetWidth(string|Closure|null $width): static
    {
        $this->curatorImageResizeTargetWidth = $width;

        return $this;
    }

    public function preserveFilenames(bool|Closure $condition = true): static
    {
        $this->curatorShouldPreserveFilenames = $condition;

        return $this;
    }

    public function getCurrentItem(): Model|Collection|null
    {
        return Media::where('id', $this->getState())->first();
    }

    public function getButtonLabel(): string|Htmlable|null
    {
        return $this->evaluate($this->buttonLabel);
    }

    public function isConstrained(): bool
    {
        return $this->evaluate($this->isConstrained);
    }

    public function getDirectory(): ?string
    {
        return $this->evaluate($this->curatorDirectory);
    }

    public function getPathGenerator(): ?string
    {
        return $this->curatorPathGenerator;
    }

    public function getDiskName(): string
    {
        return $this->evaluate($this->curatorDiskName) ?? config('forms.default_filesystem_disk');
    }

    public function getMaxSize(): ?int
    {
        return $this->evaluate($this->curatorMaxSize);
    }

    public function getMinSize(): ?int
    {
        return $this->evaluate($this->curatorMinSize);
    }

    public function getVisibility(): string
    {
        return $this->evaluate($this->curatorVisibility);
    }

    public function shouldPreserveFilenames(): bool
    {
        return $this->evaluate($this->curatorShouldPreserveFilenames);
    }

    public function getImageCropAspectRatio(): ?string
    {
        return $this->evaluate($this->curatorImageCropAspectRatio);
    }

    public function getImageResizeTargetHeight(): ?string
    {
        return $this->evaluate($this->curatorImageResizeTargetHeight);
    }

    public function getImageResizeTargetWidth(): ?string
    {
        return $this->evaluate($this->curatorImageResizeTargetWidth);
    }

    public function getAcceptedFileTypes(): ?array
    {
        $types = $this->evaluate($this->curatorAcceptedFileTypes);

        if ($types instanceof Arrayable) {
            $types = $types->toArray();
        }

        return $types;
    }
}
