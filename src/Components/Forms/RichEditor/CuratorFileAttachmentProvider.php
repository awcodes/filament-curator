<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Forms\RichEditor;

use Awcodes\Curator\Components\Forms\Uploader;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Closure;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\Contracts\FileAttachmentProvider;
use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Filament\Schemas\Schema;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Rich editor images kept in the Curator library, by media key.
 *
 * An image stores its media key in `data-id`, and Filament asks this provider for its URL whenever the content is
 * edited or rendered, so the image follows its media item: a replaced file or a moved disk shows up everywhere it is
 * used, and a deleted item drops out rather than leaving a dead link. Images uploaded, dropped or pasted into the
 * editor are added to the library the way the panel's uploader adds them.
 *
 * The key comes from client-submitted content, so only public media resolves: a key naming private media gets no
 * URL. Uploads are stored publicly for the same reason.
 *
 * cleanUpFileAttachments() does nothing. Filament calls it to delete the attachments a saved document no longer
 * uses, which suits files owned by one record and not a library shared between them.
 */
class CuratorFileAttachmentProvider implements FileAttachmentProvider
{
    use EvaluatesClosures;

    protected array | Closure | null $acceptedFileTypes = null;

    protected string | Closure | null $directory = null;

    protected string | Closure | null $diskName = null;

    protected int | Closure | null $maxSize = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public function attribute(RichContentAttribute $attribute): static
    {
        return $this;
    }

    public function acceptedFileTypes(array | Closure | null $types): static
    {
        $this->acceptedFileTypes = $types;

        return $this;
    }

    public function directory(string | Closure | null $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    public function disk(string | Closure | null $disk): static
    {
        $this->diskName = $disk;

        return $this;
    }

    /**
     * In kilobytes.
     */
    public function maxSize(int | Closure | null $size): static
    {
        $this->maxSize = $size;

        return $this;
    }

    public function getAcceptedFileTypes(): array
    {
        return $this->evaluate($this->acceptedFileTypes) ?? Curator::getAcceptedFileTypes();
    }

    public function getDirectory(): ?string
    {
        return $this->evaluate($this->directory) ?? Curator::getDirectory();
    }

    public function getDiskName(): string
    {
        return $this->evaluate($this->diskName) ?? Curator::getDiskName();
    }

    public function getMaxSize(): int
    {
        return $this->evaluate($this->maxSize) ?? Curator::getMaxSize();
    }

    /**
     * The public media item a key names, or null.
     */
    public function findMedia(mixed $key): ?Media
    {
        if (! is_int($key) && (! is_string($key) || $key === '')) {
            return null;
        }

        $media = App::make(Media::class)::query()->whereKey($key)->first();

        return $media instanceof Media && $media->isPublic() ? $media : null;
    }

    public function getFileAttachmentUrl(mixed $file): ?string
    {
        return $this->findMedia($file)?->url;
    }

    /**
     * @throws ValidationException when the upload is too large, of a type the library doesn't accept, or an SVG that
     *                             cannot be sanitized
     */
    public function saveUploadedFileAttachment(TemporaryUploadedFile $file): mixed
    {
        $uploader = $this->makeUploader();

        if ($file->getSize() > $this->getMaxSize() * 1024) {
            throw ValidationException::withMessages([
                $uploader->getStatePath() => __('validation.max.file', ['attribute' => $uploader->getValidationAttribute(), 'max' => $this->getMaxSize()]),
            ]);
        }

        if (! $uploader->isAcceptedFile($file, $this->getAcceptedFileTypes())) {
            throw ValidationException::withMessages([
                $uploader->getStatePath() => __('validation.mimetypes', ['attribute' => $uploader->getValidationAttribute(), 'values' => implode(', ', $this->getAcceptedFileTypes())]),
            ]);
        }

        $attributes = $uploader->storeUploadedFile($file);

        if ($attributes === null) {
            return null;
        }

        $attributes['exif'] = empty($attributes['exif']) ? null : Curator::sanitizeExif($attributes['exif']);
        $attributes['title'] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        return App::make(Media::class)::query()->create($attributes)->getKey();
    }

    public function getDefaultFileAttachmentVisibility(): ?string
    {
        return 'public';
    }

    public function isExistingRecordRequiredToSaveNewFileAttachments(): bool
    {
        return false;
    }

    public function cleanUpFileAttachments(array $exceptIds): void {}

    /**
     * The panel's uploader, so editor uploads get the same type detection, SVG sanitizing, naming and paths. It sits
     * in a schema of its own because its validation messages are keyed by its state path.
     */
    protected function makeUploader(): Uploader
    {
        return Uploader::make('file')
            ->disk($this->getDiskName())
            ->directory($this->getDirectory())
            ->visibility('public')
            ->acceptedFileTypes($this->getAcceptedFileTypes())
            ->preserveFilenames(Curator::shouldPreserveFilenames())
            ->container(Schema::make());
    }
}
