<?php

declare(strict_types=1);

namespace Awcodes\Curator\Enums;

use Closure;
use DOMDocument;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Symfony\Component\Mime\MimeTypes;

enum MimeType: string
{
    case ApplicationEpubZip = 'application/epub+zip';
    case ApplicationGzip = 'application/gzip';
    case ApplicationJavaArchive = 'application/java-archive';
    case ApplicationJson = 'application/json';
    case ApplicationLdJson = 'application/ld+json';
    case ApplicationMsword = 'application/msword';
    case ApplicationOctetStream = 'application/octet-stream';
    case ApplicationOgg = 'application/ogg';
    case ApplicationPdf = 'application/pdf';
    case ApplicationRtf = 'application/rtf';
    case ApplicationVndAmazonEbook = 'application/vnd.amazon.ebook';
    case ApplicationVndAppleInstallerXml = 'application/vnd.apple.installer+xml';
    case ApplicationVndMozillaXulXml = 'application/vnd.mozilla.xul+xml';
    case ApplicationVndMsExcel = 'application/vnd.ms-excel';
    case ApplicationVndMsFontobject = 'application/vnd.ms-fontobject';
    case ApplicationVndMsPowerpoint = 'application/vnd.ms-powerpoint';
    case ApplicationVndOasisOpendocumentPresentation = 'application/vnd.oasis.opendocument.presentation';
    case ApplicationVndOasisOpendocumentSpreadsheet = 'application/vnd.oasis.opendocument.spreadsheet';
    case ApplicationVndOasisOpendocumentText = 'application/vnd.oasis.opendocument.text';
    case ApplicationVndOpenxmlformatsOfficedocumentPresentationmlPresentation = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
    case ApplicationVndOpenxmlformatsOfficedocumentSpreadsheetmlSheet = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    case ApplicationVndOpenxmlformatsOfficedocumentWordprocessingmlDocument = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    case ApplicationVndRar = 'application/vnd.rar';
    case ApplicationVndVisio = 'application/vnd.visio';
    case ApplicationX7zCompressed = 'application/x-7z-compressed';
    case ApplicationXAbiword = 'application/x-abiword';
    case ApplicationXBzip = 'application/x-bzip';
    case ApplicationXBzip2 = 'application/x-bzip2';
    case ApplicationXCdf = 'application/x-cdf';
    case ApplicationXCsh = 'application/x-csh';
    case ApplicationXhtmlXml = 'application/xhtml+xml';
    case ApplicationXHttpdPhp = 'application/x-httpd-php';
    case ApplicationXml = 'application/xml';
    case ApplicationXSh = 'application/x-sh';
    case ApplicationXShockwaveFlash = 'application/x-shockwave-flash';
    case ApplicationXTar = 'application/x-tar';
    case ApplicationZip = 'application/zip';
    case Audio3gpp = 'audio/3gpp';
    case Audio3gpp2 = 'audio/3gpp2';
    case AudioAAC = 'audio/aac';
    case AudioMidi = 'audio/midi';
    case AudioMpeg = 'audio/mpeg';
    case AudioOgg = 'audio/ogg';
    case AudioOpus = 'audio/opus';
    case AudioWav = 'audio/wav';
    case AudioWebm = 'audio/webm';
    case AudioXMidi = 'audio/x-midi';
    case AudioXWav = 'audio/x-wav';
    case FontOtf = 'font/otf';
    case FontTtf = 'font/ttf';
    case FontWoff = 'font/woff';
    case FontWoff2 = 'font/woff2';
    case ImageAvif = 'image/avif';
    case ImageBmp = 'image/bmp';
    case ImageGif = 'image/gif';
    case ImageJpeg = 'image/jpeg';
    case ImagePng = 'image/png';
    case ImageSvgXml = 'image/svg+xml';
    case ImageTiff = 'image/tiff';
    case ImageVndMicrosoftIcon = 'image/vnd.microsoft.icon';
    case ImageWebp = 'image/webp';
    case TextCalendar = 'text/calendar';
    case TextCss = 'text/css';
    case TextCsv = 'text/csv';
    case TextHtml = 'text/html';
    case TextJavascript = 'text/javascript';
    case TextPlain = 'text/plain';
    case Video3gpp = 'video/3gpp';
    case Video3gpp2 = 'video/3gpp2';
    case VideoMp2t = 'video/mp2t';
    case VideoMp4 = 'video/mp4';
    case VideoMpeg = 'video/mpeg';
    case VideoOgg = 'video/ogg';
    case VideoQuicktime = 'video/quicktime';
    case VideoWebm = 'video/webm';
    case VideoXMsvideo = 'video/x-msvideo';

    /**
     * Aliases a server may run as code or serve in a way the detected type
     * does not imply (server-side includes, PHP handlers, compressed SVG).
     */
    private const EXECUTABLE_EXTENSIONS = [
        'asp', 'aspx', 'cgi', 'htaccess', 'jsp', 'phar', 'php', 'php3', 'php4', 'php5', 'php7', 'php8',
        'phps', 'pht', 'phtml', 'pl', 'py', 'shtm', 'shtml', 'stm', 'svgz',
    ];

    /**
     * Document formats stored as zip archives. libmagic reports them as
     * application/zip unless the archive happens to start with the entry it
     * recognises them by.
     */
    private const ZIP_BASED_EXTENSIONS = [
        'docm', 'docx', 'dotm', 'dotx', 'epub', 'odg', 'odp', 'ods', 'odt',
        'potx', 'ppsx', 'pptm', 'pptx', 'vsdx', 'xlsm', 'xlsx', 'xltm', 'xltx',
    ];

    /**
     * Legacy Office formats stored as OLE compound files, with the type each
     * extension stands for.
     */
    private const OLE_BASED_EXTENSIONS = [
        'doc' => 'application/msword',
        'dot' => 'application/msword',
        'msg' => 'application/vnd.ms-outlook',
        'pot' => 'application/vnd.ms-powerpoint',
        'pps' => 'application/vnd.ms-powerpoint',
        'ppt' => 'application/vnd.ms-powerpoint',
        'vsd' => 'application/vnd.visio',
        'xls' => 'application/vnd.ms-excel',
        'xlt' => 'application/vnd.ms-excel',
    ];

    /**
     * What libmagic reports for an OLE compound file it cannot attribute to
     * an application. The name depends on the libmagic version.
     */
    private const OLE_CONTAINER_TYPES = [
        'application/cdfv2',
        'application/octet-stream',
        'application/vnd.ms-office',
        'application/x-ole-storage',
    ];

    /**
     * Plain-data text formats libmagic may report only as text/plain, with
     * the type each extension stands for. Limited to types in the default
     * accepted list, so refining never turns an accepted upload into a
     * rejected one, and to formats a browser never runs as script.
     */
    private const PLAIN_TEXT_EXTENSIONS = [
        'csv' => 'text/csv',
        'ics' => 'text/calendar',
    ];

    /**
     * Types outside the restricted list that still render as a scriptable
     * document or run as code, accepted only when listed exactly.
     */
    private const SCRIPTABLE_TYPES = [
        'application/ecmascript',
        'application/javascript',
        'application/x-javascript',
        'application/x-perl',
        'application/x-php',
        'application/x-shellscript',
        'text/ecmascript',
        'text/x-perl',
        'text/x-php',
        'text/x-python',
        'text/x-shellscript',
        'text/xml',
        'text/xsl',
    ];

    private const DETECTION_SAMPLE_BYTES = 64 * 1024;

    private const SVG_NAMESPACE = 'http://www.w3.org/2000/svg';

    private const PLAIN_TEXT_TYPES = [
        'application/ics',
        'application/json',
        'application/ld+json',
        'application/x-yaml',
        'application/yaml',
        'text/calendar',
        'text/css',
        'text/csv',
        'text/markdown',
        'text/plain',
        'text/tab-separated-values',
        'text/vtt',
        'text/x-markdown',
        'text/yaml',
    ];

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Types that are never accepted by default because uploading them is
     * equivalent to publishing executable content:
     *
     * - html, xhtml, js, xml and xul render as documents in the application's
     *   own origin, so embedded script runs with the user's session.
     * - php, sh and csh execute server side if the storage directory sits
     *   inside the document root, which the default `public` disk does.
     * - flash is legacy plugin content with the same inline-execution problem.
     * - octet-stream is what finfo reports for anything it cannot classify, so
     *   allowing it turns the allow list into a wildcard.
     *
     * Applications that genuinely need these can still opt back in per field
     * with `->acceptedFileTypes()`, or globally via `Curator::acceptedFileTypes()`.
     */
    public static function restricted(): array
    {
        return [
            self::ApplicationOctetStream->value,
            self::ApplicationVndMozillaXulXml->value,
            self::ApplicationXCsh->value,
            self::ApplicationXHttpdPhp->value,
            self::ApplicationXhtmlXml->value,
            self::ApplicationXml->value,
            self::ApplicationXSh->value,
            self::ApplicationXShockwaveFlash->value,
            self::TextHtml->value,
            self::TextJavascript->value,
        ];
    }

    /**
     * The accepted file types used when a developer has not set their own.
     */
    public static function defaults(): array
    {
        return array_values(array_diff(self::toArray(), self::restricted()));
    }

    /**
     * File extensions belonging to the restricted types, used to decide whether
     * stored media may be served inline.
     */
    public static function restrictedExtensions(): array
    {
        $restricted = self::restricted();

        return array_values(array_unique(array_map(
            fn (self $type): string => $type->getExt(),
            array_filter(self::cases(), fn (self $type): bool => in_array($type->value, $restricted, true)),
        )));
    }

    /**
     * Resolve the type an extension declares, used to pin the content type of
     * media that is streamed straight from disk instead of being sniffed from
     * its contents. Returns null for extensions outside the enum.
     */
    public static function tryFromExtension(?string $extension): ?self
    {
        if (blank($extension)) {
            return null;
        }

        $extension = mb_strtolower($extension);

        foreach (self::cases() as $case) {
            if ($case->getExt() === $extension) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Detect a type from the first 64 KiB of a stream's bytes, and close it.
     *
     * Livewire 3.8.6 and 4.4.2 detect an upload's type this way. Earlier
     * releases return the temporary file's storage metadata instead, which on
     * an S3 temporary disk is the Content-Type the browser declared, so
     * uploads are detected here rather than through getMimeType().
     *
     * @param  resource|null  $stream
     */
    public static function detectFromStream(mixed $stream): string
    {
        return self::detectFromContents(self::readSample($stream));
    }

    /**
     * Detect a type from the first 64 KiB of some bytes, as detectFromStream()
     * does.
     */
    public static function detectFromContents(string $contents): string
    {
        if ($contents === '') {
            return self::ApplicationOctetStream->value;
        }

        return (new FinfoMimeTypeDetector)->detectMimeTypeFromBuffer(substr($contents, 0, self::DETECTION_SAMPLE_BYTES))
            ?: self::ApplicationOctetStream->value;
    }

    /**
     * The first 64 KiB of a stream, the sample a type is detected from. The
     * stream is closed.
     *
     * @param  resource|null  $stream
     */
    public static function readSample(mixed $stream): string
    {
        if (! is_resource($stream)) {
            return '';
        }

        try {
            $sample = stream_get_contents($stream, self::DETECTION_SAMPLE_BYTES);
        } finally {
            fclose($stream);
        }

        return is_string($sample) ? $sample : '';
    }

    /**
     * Whether a type is in a list of accepted types, which may hold wildcards
     * such as `image/*`, matched the way Laravel's `mimetypes` rule matches.
     *
     * Types that run script when a browser renders them, or code on a server,
     * are accepted only when listed exactly. A wildcard such as `text/*` or
     * `application/*` reads as "documents" or "data" and would otherwise let
     * HTML, XHTML and XML through. SVG is the exception: `image/*` does match
     * it, because every SVG is sanitized before it is stored, and an SVG that
     * cannot be sanitized is rejected.
     *
     * @param  array<int, string>  $acceptedTypes
     */
    public static function isAccepted(string $type, array $acceptedTypes): bool
    {
        $type = self::normalizeType($type);

        if (in_array($type, $acceptedTypes, true)) {
            return true;
        }

        return ! self::isScriptable($type)
            && in_array(explode('/', $type)[0] . '/*', $acceptedTypes, true);
    }

    /**
     * The types isScriptable() names explicitly, for matching stored types in
     * a query. Any other `+xml` type apart from SVG is scriptable as well.
     *
     * @return array<int, string>
     */
    public static function scriptableTypes(): array
    {
        return array_values(array_unique([...self::restricted(), ...self::SCRIPTABLE_TYPES]));
    }

    /**
     * Whether a browser may run script in content of this type, or a server
     * may run it as code. Includes every restricted type and any other XML
     * document type, apart from SVG, which Curator sanitizes.
     */
    public static function isScriptable(?string $type): bool
    {
        $type = self::normalizeType($type);

        if ($type === self::ImageSvgXml->value) {
            return false;
        }

        return in_array($type, self::restricted(), true)
            || in_array($type, self::SCRIPTABLE_TYPES, true)
            || str_ends_with($type, '+xml');
    }

    /**
     * Correct the detected type where libmagic is known to under-report a
     * format the client's extension claims, after checking the content really
     * is that format:
     *
     * - SVG that starts with whitespace or a comment is reported as text/plain
     *   or XML, or as text/html when it contains a script element, so it would
     *   otherwise lose its extension, and with it the sanitizing every SVG goes
     *   through. It is refined only when it parses as XML whose root element
     *   is an SVG namespace `<svg>`, so an HTML document never qualifies.
     * - Office and OpenDocument files are zip archives, and are reported as
     *   application/zip unless the archive's first entry identifies them.
     * - CSV and iCalendar files are plain text, and whether libmagic names
     *   the format or reports text/plain depends on its version and on the
     *   content. A text/plain file with one of those extensions takes that
     *   format's type, so a field that accepts only `text/csv` accepts CSV
     *   files on every platform.
     * - Legacy Office files (.doc, .xls, .ppt and their templates) are OLE
     *   compound files. libmagic names the application only when the part of
     *   the file that identifies it falls within the sample, and otherwise
     *   reports a generic OLE container.
     *
     * @param  Closure(): string  $contents  the whole file, read only when an SVG correction applies
     * @param  (Closure(): string)|null  $sample  the start of the file, enough for a signature check; defaults to $contents
     */
    public static function refineDetectedType(?string $type, ?string $clientExtension, Closure $contents, ?Closure $sample = null): string
    {
        $type = self::normalizeType($type);
        $extension = mb_strtolower(trim((string) $clientExtension));
        $sample ??= $contents;

        if (
            $extension === self::ImageSvgXml->getExt()
            && in_array($type, [self::TextPlain->value, self::TextHtml->value, 'text/xml', self::ApplicationXml->value], true)
            && self::isSvgDocument($contents())
        ) {
            return self::ImageSvgXml->value;
        }

        if ($type === self::TextPlain->value && array_key_exists($extension, self::PLAIN_TEXT_EXTENSIONS)) {
            return self::PLAIN_TEXT_EXTENSIONS[$extension];
        }

        if (
            in_array($extension, self::ZIP_BASED_EXTENSIONS, true)
            && in_array($type, [self::ApplicationZip->value, self::ApplicationOctetStream->value], true)
            && str_starts_with($sample(), "PK\x03\x04")
        ) {
            return MimeTypes::getDefault()->getMimeTypes($extension)[0] ?? $type;
        }

        if (
            array_key_exists($extension, self::OLE_BASED_EXTENSIONS)
            && in_array($type, self::OLE_CONTAINER_TYPES, true)
            && str_starts_with($sample(), "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1")
        ) {
            return self::OLE_BASED_EXTENSIONS[$extension];
        }

        return $type;
    }

    /**
     * Decide the extension a file is stored under from its detected type.
     *
     * The client's filename is only a hint: web servers pick the content type
     * of a stored file from its extension, so trusting it would let bytes that
     * sniff as an accepted image be served as HTML. The client's extension is
     * kept only when it is a known alias of the detected type (`.jpeg` for
     * image/jpeg, say). Content that libmagic can only call text/plain may keep
     * a plain-data text extension such as `.csv` or `.md`. Anything else is
     * replaced with the type's canonical extension, or `bin` when the type has
     * none, which Curator always serves as a download.
     */
    public static function resolveExtension(?string $type, ?string $clientExtension = null): string
    {
        $type = self::normalizeType($type);
        $clientExtension = mb_strtolower(trim((string) $clientExtension));

        if (! preg_match('/^[a-z0-9]{1,16}$/', $clientExtension) || in_array($clientExtension, self::EXECUTABLE_EXTENSIONS, true)) {
            $clientExtension = null;
        }

        $aliases = self::extensionsFor($type);

        if ($clientExtension !== null && in_array($clientExtension, $aliases, true)) {
            return $clientExtension;
        }

        if ($clientExtension !== null && $type === self::TextPlain->value && self::isPlainTextExtension($clientExtension)) {
            return $clientExtension;
        }

        return $aliases[0] ?? self::ApplicationOctetStream->getExt();
    }

    /**
     * Every extension a type may be stored under, canonical first. Extensions
     * a web server may execute or treat specially are never included.
     *
     * @return array<int, string>
     */
    public static function extensionsFor(?string $type): array
    {
        $type = self::normalizeType($type);

        if ($type === '') {
            return [];
        }

        $extensions = [
            self::tryFrom($type)?->getExt(),
            ...MimeTypes::getDefault()->getExtensions($type),
        ];

        return array_values(array_unique(array_filter(
            $extensions,
            fn (?string $extension): bool => is_string($extension)
                && preg_match('/^[a-z0-9]{1,16}$/', $extension) === 1
                && ! in_array($extension, self::EXECUTABLE_EXTENSIONS, true),
        )));
    }

    /**
     * Whether a web server may run a file with this extension as code, or
     * treat it in a way its content type does not imply.
     */
    public static function isExecutableExtension(?string $extension): bool
    {
        return in_array(mb_strtolower((string) $extension), self::EXECUTABLE_EXTENSIONS, true);
    }

    public function getExt(): string
    {
        return match ($this) {
            self::ApplicationEpubZip => 'epub',
            self::ApplicationGzip => 'gz',
            self::ApplicationJavaArchive => 'jar',
            self::ApplicationJson => 'json',
            self::ApplicationLdJson => 'jsonld',
            self::ApplicationMsword => 'doc',
            self::ApplicationOctetStream => 'bin',
            self::ApplicationOgg => 'ogx',
            self::ApplicationPdf => 'pdf',
            self::ApplicationRtf => 'rtf',
            self::ApplicationVndAmazonEbook => 'azw',
            self::ApplicationVndAppleInstallerXml => 'mpkg',
            self::ApplicationVndMozillaXulXml => 'xul',
            self::ApplicationVndMsExcel => 'xls',
            self::ApplicationVndMsFontobject => 'eot',
            self::ApplicationVndMsPowerpoint => 'ppt',
            self::ApplicationVndOasisOpendocumentPresentation => 'odp',
            self::ApplicationVndOasisOpendocumentSpreadsheet => 'ods',
            self::ApplicationVndOasisOpendocumentText => 'odt',
            self::ApplicationVndOpenxmlformatsOfficedocumentPresentationmlPresentation => 'pptx',
            self::ApplicationVndOpenxmlformatsOfficedocumentSpreadsheetmlSheet => 'xlsx',
            self::ApplicationVndOpenxmlformatsOfficedocumentWordprocessingmlDocument => 'docx',
            self::ApplicationVndRar => 'rar',
            self::ApplicationVndVisio => 'vsd',
            self::ApplicationX7zCompressed => '7z',
            self::ApplicationXAbiword => 'abw',
            self::ApplicationXBzip => 'bz',
            self::ApplicationXBzip2 => 'bz2',
            self::ApplicationXCdf => 'cda',
            self::ApplicationXCsh => 'csh',
            self::ApplicationXhtmlXml => 'xhtml',
            self::ApplicationXHttpdPhp => 'php',
            self::ApplicationXml => 'xml',
            self::ApplicationXSh => 'sh',
            self::ApplicationXShockwaveFlash => 'svf',
            self::ApplicationXTar => 'tar',
            self::ApplicationZip => 'zip',
            self::Audio3gpp, self::Video3gpp => '3gp',
            self::Audio3gpp2, self::Video3gpp2 => '3g2',
            self::AudioAAC => 'aac',
            self::AudioMidi, self::AudioXMidi => 'midi',
            self::AudioMpeg => 'mp3',
            self::AudioOgg => 'oga',
            self::AudioOpus => 'opus',
            self::AudioWav, self::AudioXWav => 'wav',
            self::AudioWebm => 'weba',
            self::FontOtf => 'otf',
            self::FontTtf => 'ttf',
            self::FontWoff => 'woff',
            self::FontWoff2 => 'woff2',
            self::ImageAvif => 'avif',
            self::ImageBmp => 'bmp',
            self::ImageGif => 'gif',
            self::ImageJpeg => 'jpg',
            self::ImagePng => 'png',
            self::ImageSvgXml => 'svg',
            self::ImageTiff => 'tiff',
            self::ImageVndMicrosoftIcon => 'ico',
            self::ImageWebp => 'webp',
            self::TextCalendar => 'ics',
            self::TextCss => 'css',
            self::TextCsv => 'csv',
            self::TextHtml => 'html',
            self::TextJavascript => 'js',
            self::TextPlain => 'txt',
            self::VideoMp2t => 'ts',
            self::VideoMp4 => 'mp4',
            self::VideoMpeg => 'mpeg',
            self::VideoOgg => 'ogv',
            self::VideoQuicktime => 'mov',
            self::VideoWebm => 'webm',
            self::VideoXMsvideo => 'avi',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ApplicationEpubZip => 'Electronic publication (EPUB)',
            self::ApplicationGzip => 'GZip Compressed Archive',
            self::ApplicationJavaArchive => 'Java Archive (JAR)',
            self::ApplicationJson => 'JSON format',
            self::ApplicationLdJson => 'JSON-LD format',
            self::ApplicationMsword => 'Microsoft Word',
            self::ApplicationOctetStream => 'Any kind of binary data',
            self::ApplicationOgg => 'OGG',
            self::ApplicationPdf => 'Adobe Portable Document Format (PDF)',
            self::ApplicationRtf => 'Rich Text Format (RTF)',
            self::ApplicationVndAmazonEbook => 'Amazon Kindle eBook format',
            self::ApplicationVndAppleInstallerXml => 'Apple Installer Package',
            self::ApplicationVndMozillaXulXml => 'XUL',
            self::ApplicationVndMsExcel => 'Microsoft Excel',
            self::ApplicationVndMsFontobject => 'MS Embedded OpenType fonts',
            self::ApplicationVndMsPowerpoint => 'Microsoft PowerPoint',
            self::ApplicationVndOasisOpendocumentPresentation => 'OpenDocument presentation document',
            self::ApplicationVndOasisOpendocumentSpreadsheet => 'OpenDocument spreadsheet document',
            self::ApplicationVndOasisOpendocumentText => 'OpenDocument text document',
            self::ApplicationVndOpenxmlformatsOfficedocumentPresentationmlPresentation => 'Microsoft PowerPoint (OpenXML)',
            self::ApplicationVndOpenxmlformatsOfficedocumentSpreadsheetmlSheet => 'Microsoft Excel (OpenXML)',
            self::ApplicationVndOpenxmlformatsOfficedocumentWordprocessingmlDocument => 'Microsoft Word (OpenXML)',
            self::ApplicationVndRar => 'RAR archive',
            self::ApplicationVndVisio => 'Microsoft Visio',
            self::ApplicationX7zCompressed => '7-zip archive',
            self::ApplicationXAbiword => 'AbiWord document',
            self::ApplicationXBzip => 'BZip archive',
            self::ApplicationXBzip2 => 'BZip2 archive',
            self::ApplicationXCdf => 'CD audio',
            self::ApplicationXCsh => 'C-Shell script',
            self::ApplicationXhtmlXml => 'XHTML',
            self::ApplicationXHttpdPhp => 'PHP',
            self::ApplicationXml => 'XML',
            self::ApplicationXSh => 'Bourne shell script',
            self::ApplicationXShockwaveFlash => 'Adobe Flash document',
            self::ApplicationXTar => 'Tape Archive (TAR)',
            self::ApplicationZip => 'ZIP archive',
            self::Audio3gpp => '3GPP audio container',
            self::Audio3gpp2 => '3GPP2 audio container',
            self::AudioAAC => 'AAC audio',
            self::AudioMidi, self::AudioXMidi => 'Musical Instrument Digital Interface (MIDI)',
            self::AudioMpeg => 'MP3 audio',
            self::AudioOgg => 'OGG audio',
            self::AudioOpus => 'Opus audio',
            self::AudioWav, self::AudioXWav => 'Waveform Audio Format',
            self::AudioWebm => 'WEBM audio',
            self::FontOtf => 'OpenType font',
            self::FontTtf => 'TrueType Font',
            self::FontWoff => 'Web Open Font Format (WOFF)',
            self::FontWoff2 => 'Web Open Font Format 2 (WOFF2)',
            self::ImageAvif => 'AVIF image',
            self::ImageBmp => 'Windows OS/2 Bitmap Graphics',
            self::ImageGif => 'Graphics Interchange Format (GIF)',
            self::ImageJpeg => 'JPEG images',
            self::ImagePng => 'Portable Network Graphics',
            self::ImageSvgXml => 'Scalable Vector Graphics (SVG)',
            self::ImageTiff => 'Tagged Image File Format (TIFF)',
            self::ImageVndMicrosoftIcon => 'Icon format',
            self::ImageWebp => 'WEBP image',
            self::TextCalendar => 'iCalendar format',
            self::TextCss => 'Cascading Style Sheets (CSS)',
            self::TextCsv => 'Comma-separated values (CSV)',
            self::TextHtml => 'HyperText Markup Language (HTML)',
            self::TextJavascript => 'JavaScript',
            self::TextPlain => 'Text (generally ASCII or ISO 8859-n)',
            self::Video3gpp => '3GPP video container',
            self::Video3gpp2 => '3GPP2 video container',
            self::VideoMp2t => 'MPEG transport stream',
            self::VideoMp4 => 'MP4 video',
            self::VideoMpeg => 'MPEG Video',
            self::VideoOgg => 'OGG video',
            self::VideoQuicktime => 'QuickTime video',
            self::VideoWebm => 'WEBM video',
            self::VideoXMsvideo => 'AVI: Audio Video Interleave',
        };
    }

    private static function isSvgDocument(string $contents): bool
    {
        if (! str_contains($contents, '<svg')) {
            return false;
        }

        $previous = libxml_use_internal_errors(true);

        try {
            $document = new DOMDocument;

            return $document->loadXML($contents, LIBXML_NONET)
                && $document->documentElement?->localName === 'svg'
                && $document->documentElement->namespaceURI === self::SVG_NAMESPACE;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    private static function normalizeType(?string $type): string
    {
        return mb_strtolower(trim(explode(';', (string) $type)[0]));
    }

    /**
     * Text content gives libmagic little to go on, so a CSV or Markdown file is
     * often detected as text/plain. Its extension can be kept as long as the
     * type a server would serve it as is plain data that never renders as a
     * document.
     */
    private static function isPlainTextExtension(string $extension): bool
    {
        $declared = MimeTypes::getDefault()->getMimeTypes($extension)[0] ?? null;

        return in_array($declared, self::PLAIN_TEXT_TYPES, true);
    }
}
