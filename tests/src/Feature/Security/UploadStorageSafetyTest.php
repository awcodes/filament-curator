<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Forms\Uploader;
use Awcodes\Curator\Components\Modals\CuratorPanel;
use Awcodes\Curator\Config\CuratorManager;
use Awcodes\Curator\CuratorUtils;
use Awcodes\Curator\Enums\MimeType;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\Pages\CreateMedia;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Livewire\Component;
use Livewire\Livewire;

/**
 * A disk that records what is written to it, and can be told to report every
 * write as failed the way a disk configured with `throw => false` does.
 */
class UploadStorageSafetyDisk extends FilesystemAdapter
{
    /** @var array<int, array{0: string, 1: ?string}> */
    public array $writes = [];

    public bool $failWrites = false;

    public int $streamsRead = 0;

    public function readStream($path)
    {
        $this->streamsRead++;

        return parent::readStream($path);
    }

    public function put($path, $contents, $options = [])
    {
        if ($this->failWrites) {
            return false;
        }

        $this->writes[] = [$path, is_resource($contents) ? null : (string) $contents];

        return parent::put($path, $contents, $options);
    }
}

class UploadStorageSafetyForm extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Uploader::make('file')
                ->disk('public')
                ->acceptedFileTypes(['image/png'])
                ->validationMessages(['mimetypes' => 'Only :values, not this :attribute.']),
        ]);
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): string
    {
        return '<div>{{ $this->form }}</div>';
    }
}

function uploadStorageSafetyDisk(string $name = 'public'): UploadStorageSafetyDisk
{
    $root = sys_get_temp_dir() . '/curator-upload-safety-' . bin2hex(random_bytes(6));
    $adapter = new LocalFilesystemAdapter($root);
    $disk = new UploadStorageSafetyDisk(new Flysystem($adapter), $adapter, ['root' => $root]);

    Storage::set($name, $disk);

    return $disk;
}

function uploadStorageSafetyUpload(string $name, string $content): UploadedFile
{
    return UploadedFile::fake()->createWithContent($name, $content)->mimeType((string) (new finfo(FILEINFO_MIME_TYPE))->buffer($content));
}

function uploadStorageSafetySourceFile(string $name, string $content): string
{
    $directory = sys_get_temp_dir() . '/curator-upload-safety-' . bin2hex(random_bytes(6));
    mkdir($directory);
    file_put_contents($directory . '/' . $name, $content);

    return $directory . '/' . $name;
}

const UPLOAD_SAFETY_XHTML_AS_SVG = '<?xml version="1.0"?><html xmlns="http://www.w3.org/1999/xhtml"><body><svg/><script>alert(1)</script></body></html>';

const UPLOAD_SAFETY_SVG = '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script><rect width="1" height="1"/></svg>';

beforeEach(function () {
    config(['curator.default_disk' => 'public']);
});

test('an xhtml document detected as svg is rejected and leaves no file', function (bool $preserveFilenames) {
    expect((new finfo(FILEINFO_MIME_TYPE))->buffer(UPLOAD_SAFETY_XHTML_AS_SVG))->toBe('image/svg+xml');

    $public = uploadStorageSafetyDisk('public');
    $local = uploadStorageSafetyDisk('local');
    app(CuratorManager::class)->preserveFilenames($preserveFilenames);

    Livewire::test(CreateMedia::class)
        ->set('data.file', uploadStorageSafetyUpload('page.svg', UPLOAD_SAFETY_XHTML_AS_SVG))
        ->call('create')
        ->assertHasFormErrors(['file']);

    expect(Media::query()->count())->toBe(0)
        ->and($public->writes)->toBe([])
        ->and($public->allFiles())->toBe([])
        ->and($local->allFiles())->toBe([]);
})->with([
    'generated filename' => false,
    'preserved filename' => true,
]);

test('an xhtml document detected as svg is rejected by the picker panel', function () {
    $public = uploadStorageSafetyDisk('public');

    Livewire::test(CuratorPanel::class, ['settings' => [
        'acceptedFileTypes' => ['image/svg+xml'],
        'diskName' => 'public',
        'directory' => 'uploads',
        'visibility' => 'public',
        'isMultiple' => true,
        'rules' => [],
        'statePath' => 'data.media',
    ]])
        ->set('panelData.files_to_add', [uploadStorageSafetyUpload('page.svg', UPLOAD_SAFETY_XHTML_AS_SVG)])
        ->callAction('addFiles')
        ->assertHasFormErrors(['files_to_add']);

    expect(Media::query()->count())->toBe(0)
        ->and($public->allFiles())->toBe([]);
});

test('an svg is sanitized before it is written', function () {
    $public = uploadStorageSafetyDisk('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', uploadStorageSafetyUpload('logo.svg', UPLOAD_SAFETY_SVG))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($public->writes)->toHaveCount(1)
        ->and($public->writes[0][0])->toBe($media->path)
        ->and($public->writes[0][1])->toBeString()->not->toContain('<script')->toContain('rect')
        ->and($public->get($media->path))->toBe($public->writes[0][1])
        ->and($media->ext)->toBe('svg')
        ->and($media->size)->toBe(mb_strlen($public->writes[0][1], '8bit'));
});

test('sanitizeSvg fails closed when the sanitizer throws', function () {
    expect(Curator::sanitizeSvg(UPLOAD_SAFETY_XHTML_AS_SVG))->toBe('');
});

test('an imported svg is sanitized before it is written', function () {
    $public = uploadStorageSafetyDisk('public');

    $data = CuratorUtils::importMedia(uploadStorageSafetySourceFile('logo.svg', UPLOAD_SAFETY_SVG), disk: 'public');

    expect($public->writes)->toHaveCount(1)
        ->and($public->writes[0][1])->not->toContain('<script')
        ->and($public->get($data['path']))->toContain('rect')->not->toContain('<script');
});

test('an imported xhtml document detected as svg is refused and leaves no file', function () {
    $public = uploadStorageSafetyDisk('public');

    expect(fn () => CuratorUtils::importMedia(uploadStorageSafetySourceFile('page.svg', UPLOAD_SAFETY_XHTML_AS_SVG), disk: 'public'))
        ->toThrow(Exception::class);

    expect($public->allFiles())->toBe([]);
});

test('repair-extensions keeps the original when writing the sanitized copy fails', function () {
    $public = uploadStorageSafetyDisk('public');
    $public->put('media/logo.html', UPLOAD_SAFETY_SVG);
    $public->failWrites = true;

    $media = makeMedia(['directory' => 'media', 'name' => 'logo', 'path' => 'media/logo.html', 'ext' => 'html', 'type' => 'text/html']);

    $this->artisan('curator:repair-extensions')->assertSuccessful();

    expect($public->exists('media/logo.html'))->toBeTrue()
        ->and($public->get('media/logo.html'))->toBe(UPLOAD_SAFETY_SVG)
        ->and($media->refresh()->path)->toBe('media/logo.html');
});

test('wildcards do not accept scriptable document types', function (string $name, string $content, array $acceptedTypes) {
    uploadStorageSafetyDisk('public');
    Curator::acceptedFileTypes($acceptedTypes);

    Livewire::test(CreateMedia::class)
        ->set('data.file', uploadStorageSafetyUpload($name, $content))
        ->call('create')
        ->assertHasFormErrors(['file']);

    expect(Media::query()->count())->toBe(0);
})->with([
    'xhtml under text/*' => ['page.xhtml', '<?xml version="1.0"?><html xmlns="http://www.w3.org/1999/xhtml"><body><script>alert(1)</script></body></html>', ['text/*']],
    'html under text/*' => ['page.html', '<html><body><script>alert(1)</script></body></html>', ['text/*']],
    'javascript under application/*' => ['app.js', "#!/usr/bin/env node\nconsole.log(1)\n", ['application/*']],
]);

test('wildcards still accept ordinary types, and scriptable types listed exactly', function (string $name, string $content, array $acceptedTypes, string $type) {
    uploadStorageSafetyDisk('public');
    Curator::acceptedFileTypes($acceptedTypes);

    Livewire::test(CreateMedia::class)
        ->set('data.file', uploadStorageSafetyUpload($name, $content))
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Media::query()->sole()->type)->toBe($type);
})->with([
    'csv under text/*' => ['data.csv', "a,b\n1,2\n", ['text/*'], 'text/csv'],
    'pdf under application/*' => ['report.pdf', "%PDF-1.4\n", ['application/*'], 'application/pdf'],
    'svg under image/*' => ['logo.svg', UPLOAD_SAFETY_SVG, ['image/*'], 'image/svg+xml'],
    'xml listed exactly' => ['data.xml', '<?xml version="1.0"?><note><to>a</to></note>', ['text/xml'], 'text/xml'],
]);

test('isAccepted only matches scriptable types exactly', function (string $type, array $accepted, bool $expected) {
    expect(MimeType::isAccepted($type, $accepted))->toBe($expected);
})->with([
    ['text/html', ['text/*'], false],
    ['text/xml', ['text/*'], false],
    ['application/xml', ['application/*'], false],
    ['application/xhtml+xml', ['application/*'], false],
    ['application/rss+xml', ['application/*'], false],
    ['application/javascript', ['application/*'], false],
    ['application/octet-stream', ['application/*'], false],
    ['text/html', ['text/html'], true],
    ['image/svg+xml', ['image/*'], true],
    ['text/csv', ['text/*'], true],
]);

test('a legacy office file larger than the detection sample keeps its type', function () {
    $content = (string) gzdecode((string) file_get_contents(__DIR__ . '/../../Fixtures/Files/legacy-word.doc.gz'));

    expect(strlen($content))->toBeGreaterThan(64 * 1024)
        ->and(MimeType::detectFromContents($content))->not->toBe('application/msword');

    uploadStorageSafetyDisk('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', uploadStorageSafetyUpload('report.doc', $content))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($media->ext)->toBe('doc')
        ->and($media->type)->toBe('application/msword');
});

test('ole refinement needs the ole signature and a legacy office extension', function (string $type, string $extension, string $sample, string $expected) {
    expect(MimeType::refineDetectedType($type, $extension, fn (): string => throw new LogicException('read the whole file'), fn (): string => $sample))
        ->toBe($expected);
})->with([
    'xls' => ['application/x-ole-storage', 'xls', "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1rest", 'application/vnd.ms-excel'],
    'ppt, older libmagic' => ['application/CDFV2', 'ppt', "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1rest", 'application/vnd.ms-powerpoint'],
    'not an office extension' => ['application/x-ole-storage', 'png', "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1rest", 'application/x-ole-storage'],
    'no signature' => ['application/x-ole-storage', 'doc', 'not ole', 'application/x-ole-storage'],
    'zip, from the sample' => ['application/zip', 'docx', "PK\x03\x04rest", 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
]);

test('a custom mimetypes message is used for the detected type check', function () {
    uploadStorageSafetyDisk('public');

    Livewire::test(UploadStorageSafetyForm::class)
        ->set('data.file', UploadedFile::fake()->createWithContent('page.png', '<html><body>page</body></html>')->mimeType('image/png'))
        ->call('save')
        ->assertHasErrors(['data.file' => 'Only image/png, not this file.']);
});

test('an upload is read once to detect its type', function () {
    uploadStorageSafetyDisk('public');
    $temporary = uploadStorageSafetyDisk('tmp-for-tests');

    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent('report.pdf', "%PDF-1.4\n"))
        ->call('create')
        ->assertHasNoFormErrors();

    // One read detects the type for validation and saving, one copies the file.
    expect(Media::query()->sole()->type)->toBe('application/pdf')
        ->and($temporary->streamsRead)->toBe(2);
});

test('a csv detected as plain text is accepted by a field that accepts only csv', function () {
    $content = "name\nalice\n";

    expect((new finfo(FILEINFO_MIME_TYPE))->buffer($content))->toBe('text/plain');

    uploadStorageSafetyDisk('public');
    Curator::acceptedFileTypes(['text/csv']);

    Livewire::test(CreateMedia::class)
        ->set('data.file', uploadStorageSafetyUpload('names.csv', $content))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($media->type)->toBe('text/csv')
        ->and($media->ext)->toBe('csv');
});

test('plain text takes the type of a plain-data extension only', function (string $type, string $extension, string $expected) {
    expect(MimeType::refineDetectedType($type, $extension, fn (): string => throw new LogicException('read the file')))
        ->toBe($expected);
})->with([
    'csv' => ['text/plain', 'csv', 'text/csv'],
    'uppercase csv' => ['text/plain', 'CSV', 'text/csv'],
    'ics' => ['text/plain', 'ics', 'text/calendar'],
    'markdown stays plain text' => ['text/plain', 'md', 'text/plain'],
    'html stays plain text' => ['text/plain', 'html', 'text/plain'],
    'js stays plain text' => ['text/plain', 'js', 'text/plain'],
    'only plain text is refined' => ['text/html', 'csv', 'text/html'],
]);
