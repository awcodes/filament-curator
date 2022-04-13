<?php

namespace FilamentCurator\Forms\Components;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\TemporaryUploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\BaseFileUpload;

class MediaUpload extends FileUpload
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file, $state, $set) {

            $filename = $component->shouldPreserveFilenames() ? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) : Str::uuid();

            $storeMethod = $component->getVisibility() === 'public' ? 'storePubliclyAs' : 'storeAs';

            if (Str::contains($file->getMimeType(), 'image')) {
                $image = Image::make($file->getRealPath());
                $width = $image->getWidth();
                $height = $image->getHeight();
            }

            if (Storage::disk($component->getDiskName())->exists(ltrim($component->getDirectory() . '/' . $filename  .  '.' . $file->getClientOriginalExtension(), '/'))) {
                $filename = $filename . '-' . time();
            }

            return [
                'public_id' => ltrim($component->getDirectory() . '/' . $filename, '/'),
                'ext' => $file->getClientOriginalExtension(),
                'type' => $file->getMimeType(),
                'width' => isset($width) ? $width : null,
                'height' => isset($height) ? $height : null,
                'disk' => $component->getDiskName(),
                'directory' => $component->getDirectory(),
                'size' => $file->getSize(),
                'filename' => $file->{$storeMethod}($component->getDirectory(), $filename  .  '.' . $file->getClientOriginalExtension(), $component->getDiskName()),
            ];
        });
    }

    public function saveUploadedFiles(): void
    {
        if (blank($this->getState())) {
            $this->state([]);

            return;
        }

        if (!is_array($this->getState())) {
            $this->state([$this->getState()]);
        }

        $state = array_map(function (TemporaryUploadedFile | array $file) {
            if (!$file instanceof TemporaryUploadedFile) {
                return $file;
            }

            $callback = $this->saveUploadedFileUsing;

            if (!$callback) {
                $file->delete();

                return $file;
            }

            $storedFile = $this->evaluate($callback, [
                'file' => $file,
            ]);

            $file->delete();

            return $storedFile;
        }, $this->getState());

        if ($this->canReorder && ($callback = $this->reorderUploadedFilesUsing)) {
            $state = $this->evaluate($callback, [
                'state' => $state,
            ]);
        }

        $this->state($state);
    }
}
