<?php

namespace Awcodes\Curator\Components\Forms;

use Awcodes\Curator\Actions\DownloadAction;
use Awcodes\Curator\Actions\PickerAction;
use Awcodes\Curator\Facades\Curator;
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

    protected string|Closure|null $curatorDiskName = null;

    protected string|Closure|null $curatorDirectory = null;

    protected string|null $curatorPathGenerator = null;

    protected string|Closure|null $curatorVisibility = null;

    protected array|Closure|null $curatorAcceptedFileTypes = null;

    protected bool|Closure|null $curatorShouldPreserveFilenames = null;

    protected int|Closure|null $curatorMaxSize = null;

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

        $this->registerActions([
            PickerAction::make(),
            DownloadAction::make(),
        ]);
    }

    public function buttonLabel(string|Htmlable|Closure $label): static
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

    public function disk(string|Closure $name): static
    {
        $this->curatorDiskName = $name;

        return $this;
    }

    public function maxSize(int|Closure $size): static
    {
        $this->curatorMaxSize = $size;

        return $this;
    }

    public function minSize(int|Closure $size): static
    {
        $this->curatorMinSize = $size;

        return $this;
    }

    public function imageCropAspectRatio(string|Closure $ratio): static
    {
        $this->curatorImageCropAspectRatio = $ratio;

        return $this;
    }

    public function imageResizeTargetHeight(string|Closure $height): static
    {
        $this->curatorImageResizeTargetHeight = $height;

        return $this;
    }

    public function imageResizeTargetWidth(string|Closure $width): static
    {
        $this->curatorImageResizeTargetWidth = $width;

        return $this;
    }

    public function preserveFilenames(bool|Closure|null $condition = true): static
    {
        $this->curatorShouldPreserveFilenames = $condition;

        return $this;
    }

    public function getCurrentItem(): Model|Collection|null
    {
        if (! filled($this->getState())) {
            return null;
        }

        return Curator::getMediaModel()::where('id', $this->getState())->first();
    }

    public function getButtonLabel(): string
    {
        return $this->evaluate($this->buttonLabel);
    }

    public function isConstrained(): bool
    {
        return $this->evaluate($this->isConstrained);
    }

    public function getDirectory(): string
    {
        return $this->evaluate($this->curatorDirectory) ?? app('curator')->getDirectory();
    }

    public function getPathGenerator(): ?string
    {
        return $this->curatorPathGenerator ?? app('curator')->getPathGenerator();
    }

    public function getDiskName(): string
    {
        return $this->evaluate($this->curatorDiskName) ?? app('curator')->getDiskName();
    }

    public function getMaxSize(): ?int
    {
        return $this->evaluate($this->curatorMaxSize) ?? app('curator')->getMaxSize();
    }

    public function getMinSize(): ?int
    {
        return $this->evaluate($this->curatorMinSize) ?? app('curator')->getMinSize();
    }

    public function getVisibility(): string
    {
        return $this->evaluate($this->curatorVisibility) ?? app('curator')->getVisibility();
    }

    public function shouldPreserveFilenames(): bool
    {
        return $this->evaluate($this->curatorShouldPreserveFilenames) ?? app('curator')->shouldPreserveFilenames();
    }

    public function getImageCropAspectRatio(): ?string
    {
        return $this->evaluate($this->curatorImageCropAspectRatio) ?? app('curator')->getImageCropAspectRatio();
    }

    public function getImageResizeTargetHeight(): ?string
    {
        return $this->evaluate($this->curatorImageResizeTargetHeight) ?? app('curator')->getImageResizeTargetHeight();
    }

    public function getImageResizeTargetWidth(): ?string
    {
        return $this->evaluate($this->curatorImageResizeTargetWidth) ?? app('curator')->getImageResizeTargetWidth();
    }

    public function getAcceptedFileTypes(): ?array
    {
        $types = $this->evaluate($this->curatorAcceptedFileTypes) ?? app('curator')->getAcceptedFileTypes();

        if ($types instanceof Arrayable) {
            $types = $types->toArray();
        }

        return $types;
    }
}
