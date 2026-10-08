<?php

namespace Awcodes\Curator\Support;

use Closure;
use DOMDocument;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Symfony\Component\Mime\MimeTypes;

class MimeType
{
    public const OCTET_STREAM = 'application/octet-stream';

    public const SVG = 'image/svg+xml';

    public const TEXT_PLAIN = 'text/plain';

    /**
     * The extension each common type is stored under. Types not listed here use
     * the first extension Symfony's MIME database knows for them.
     */
    private const CANONICAL_EXTENSIONS = [
        'application/epub+zip' => 'epub',
        'application/gzip' => 'gz',
        'application/java-archive' => 'jar',
        'application/json' => 'json',
        'application/ld+json' => 'jsonld',
        'application/msword' => 'doc',
        'application/octet-stream' => 'bin',
        'application/ogg' => 'ogx',
        'application/pdf' => 'pdf',
        'application/rtf' => 'rtf',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.ms-powerpoint' => 'ppt',
        'application/vnd.oasis.opendocument.presentation' => 'odp',
        'application/vnd.oasis.opendocument.spreadsheet' => 'ods',
        'application/vnd.oasis.opendocument.text' => 'odt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.rar' => 'rar',
        'application/x-7z-compressed' => '7z',
        'application/x-tar' => 'tar',
        'application/xhtml+xml' => 'xhtml',
        'application/xml' => 'xml',
        'application/zip' => 'zip',
        'audio/aac' => 'aac',
        'audio/midi' => 'midi',
        'audio/mpeg' => 'mp3',
        'audio/ogg' => 'oga',
        'audio/opus' => 'opus',
        'audio/wav' => 'wav',
        'audio/webm' => 'weba',
        'audio/x-wav' => 'wav',
        'font/otf' => 'otf',
        'font/ttf' => 'ttf',
        'font/woff' => 'woff',
        'font/woff2' => 'woff2',
        'image/avif' => 'avif',
        'image/bmp' => 'bmp',
        'image/gif' => 'gif',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/svg+xml' => 'svg',
        'image/tiff' => 'tiff',
        'image/vnd.microsoft.icon' => 'ico',
        'image/webp' => 'webp',
        'text/calendar' => 'ics',
        'text/css' => 'css',
        'text/csv' => 'csv',
        'text/html' => 'html',
        'text/javascript' => 'js',
        'text/plain' => 'txt',
        'video/mp2t' => 'ts',
        'video/mp4' => 'mp4',
        'video/mpeg' => 'mpeg',
        'video/ogg' => 'ogv',
        'video/quicktime' => 'mov',
        'video/webm' => 'webm',
        'video/x-msvideo' => 'avi',
    ];

    /**
     * Aliases a server may run as code or serve in a way the detected type
     * does not imply (server-side includes, PHP handlers, compressed SVG).
     */
    private const EXECUTABLE_EXTENSIONS = [
        'asp', 'aspx', 'cgi', 'htaccess', 'jsp', 'phar', 'php', 'php3', 'php4', 'php5', 'php7', 'php8',
        'phps', 'pht', 'phtml', 'pl', 'py', 'shtm', 'shtml', 'stm', 'svgz',
    ];

    /**
     * Content that renders as a document or runs as code when served under its
     * own type.
     */
    private const RESTRICTED_TYPES = [
        'application/vnd.mozilla.xul+xml',
        'application/x-csh',
        'application/x-httpd-php',
        'application/x-sh',
        'application/x-shockwave-flash',
        'application/xhtml+xml',
        'application/xml',
        'application/ecmascript',
        'application/javascript',
        'application/x-javascript',
        'text/ecmascript',
        'text/html',
        'text/javascript',
        'text/xml',
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
     * Legacy Office formats are OLE compound files. libmagic only names the
     * application when the entry it recognises them by falls within the bytes
     * it is given, so a large file's first 64KB often reads as a bare container.
     */
    private const OLE_BASED_EXTENSIONS = ['doc', 'dot', 'msg', 'ppt', 'pps', 'xls', 'xlt'];

    private const OLE_CONTAINER_TYPES = ['application/cdfv2', 'application/x-ole-storage', 'application/octet-stream'];

    private const OLE_SIGNATURE = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1";

    /**
     * Plain-data formats libmagic may report only as text/plain, depending on
     * its version and platform, mapped to the type their extension declares.
     */
    private const PLAIN_DATA_EXTENSION_TYPES = [
        'csv' => 'text/csv',
        'ics' => 'text/calendar',
    ];

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

    /**
     * libmagic's answers for content it can't identify.
     */
    private const INCONCLUSIVE_TYPES = [
        'application/x-empty',
        'inode/x-empty',
    ];

    /**
     * Detect a type from the content itself, reading at most the first 64KB
     * of the stream. Returns null when the content gives nothing to go on.
     *
     * @param  resource|string|null  $contents
     */
    public static function detect(mixed $contents): ?string
    {
        if (is_resource($contents)) {
            $sample = stream_get_contents($contents, 64 * 1024);
            fclose($contents);
            $contents = $sample;
        }

        if (! is_string($contents) || $contents === '') {
            return null;
        }

        $type = self::normalizeType((new FinfoMimeTypeDetector)->detectMimeTypeFromBuffer($contents));

        return ($type === '' || in_array($type, self::INCONCLUSIVE_TYPES, true)) ? null : $type;
    }

    /**
     * Correct the detected type where libmagic is known to under-report a
     * format the client's extension claims, after checking the content really
     * is that format:
     *
     * - SVG that starts with whitespace or a comment is reported as text/plain
     *   (or as XML or HTML, depending on the libmagic version), so it would
     *   otherwise lose its extension, and with it the sanitizing every SVG
     *   goes through.
     * - Office and OpenDocument files are zip archives, and are reported as
     *   application/zip unless the archive's first entry identifies them.
     * - CSV and calendar text is reported as text/plain by some libmagic
     *   versions, so it takes the type its extension declares. Other text,
     *   such as Markdown, stays text/plain, which fields already accept it as.
     * - Legacy Office files are OLE containers, reported as such when the entry
     *   that identifies them lies beyond the bytes that were sampled.
     *
     * @param  Closure(): string  $contents  the whole file, read only to check an SVG document
     * @param  Closure(int): string  $head  the file's first bytes, read only to check a signature
     */
    public static function refineDetectedType(?string $type, ?string $clientExtension, Closure $contents, Closure $head): string
    {
        $type = self::normalizeType($type);
        $extension = mb_strtolower(trim((string) $clientExtension));

        if ($type === self::TEXT_PLAIN && isset(self::PLAIN_DATA_EXTENSION_TYPES[$extension])) {
            return self::PLAIN_DATA_EXTENSION_TYPES[$extension];
        }

        if (
            $extension === 'svg'
            && in_array($type, [self::TEXT_PLAIN, 'text/xml', 'application/xml', 'text/html'], true)
            && self::isSvgDocument($contents())
        ) {
            return self::SVG;
        }

        if (
            in_array($extension, self::ZIP_BASED_EXTENSIONS, true)
            && in_array($type, ['application/zip', self::OCTET_STREAM], true)
            && str_starts_with($head(4), "PK\x03\x04")
        ) {
            return MimeTypes::getDefault()->getMimeTypes($extension)[0] ?? $type;
        }

        if (
            in_array($extension, self::OLE_BASED_EXTENSIONS, true)
            && in_array($type, self::OLE_CONTAINER_TYPES, true)
            && str_starts_with($head(strlen(self::OLE_SIGNATURE)), self::OLE_SIGNATURE)
        ) {
            return MimeTypes::getDefault()->getMimeTypes($extension)[0] ?? $type;
        }

        return $type;
    }

    /**
     * Read the first bytes of a stream, closing it.
     *
     * @param  resource|null  $stream
     */
    public static function readHead(mixed $stream, int $length): string
    {
        if (! is_resource($stream)) {
            return '';
        }

        $head = stream_get_contents($stream, $length);
        fclose($stream);

        return is_string($head) ? $head : '';
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
     * replaced with the type's usual extension, or `bin` when the type has
     * none, which Curator always serves as a download.
     */
    public static function resolveExtension(?string $type, ?string $clientExtension = null): string
    {
        $type = self::normalizeType($type);
        $clientExtension = mb_strtolower(trim((string) $clientExtension));

        if (! preg_match('/^[a-z0-9]{1,16}$/', $clientExtension) || self::isExecutableExtension($clientExtension)) {
            $clientExtension = null;
        }

        $aliases = self::extensionsFor($type);

        if ($clientExtension !== null && in_array($clientExtension, $aliases, true)) {
            return $clientExtension;
        }

        if ($clientExtension !== null && $type === self::TEXT_PLAIN && self::isPlainTextExtension($clientExtension)) {
            return $clientExtension;
        }

        return $aliases[0] ?? self::CANONICAL_EXTENSIONS[self::OCTET_STREAM];
    }

    /**
     * Every extension a type may be stored under, usual one first. Extensions
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
            self::CANONICAL_EXTENSIONS[$type] ?? null,
            ...MimeTypes::getDefault()->getExtensions($type),
        ];

        return array_values(array_unique(array_filter(
            $extensions,
            fn (?string $extension): bool => is_string($extension)
                && preg_match('/^[a-z0-9]{1,16}$/', $extension) === 1
                && ! self::isExecutableExtension($extension),
        )));
    }

    public static function isExecutableExtension(?string $extension): bool
    {
        return in_array(mb_strtolower((string) $extension), self::EXECUTABLE_EXTENSIONS, true);
    }

    /**
     * Whether the content would run script or code if served under its own
     * type. SVG is sanitized instead, and octet-stream is only libmagic giving
     * up.
     */
    public static function isRestricted(?string $type): bool
    {
        return in_array(self::normalizeType($type), self::RESTRICTED_TYPES, true);
    }

    /**
     * Whether a web server could serve a file with this extension as a
     * document, script or server-side code.
     */
    public static function isUnsafeExtension(string $extension): bool
    {
        $extension = mb_strtolower($extension);

        if (preg_match('/^[a-z0-9]+$/', $extension) !== 1 || self::isExecutableExtension($extension)) {
            return true;
        }

        foreach (MimeTypes::getDefault()->getMimeTypes($extension) as $type) {
            if (self::isRestricted($type) || $type === self::SVG) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether a type matches one of a list of accepted types, which may use
     * wildcards such as `image/*`. Types that render as a document or run as
     * script only match when they are listed exactly, never through a
     * wildcard such as `text/*` or `application/*`.
     *
     * @param  array<int, string>  $acceptedTypes
     */
    public static function isAccepted(?string $type, array $acceptedTypes): bool
    {
        $type = self::normalizeType($type);

        foreach ($acceptedTypes as $accepted) {
            $accepted = self::normalizeType($accepted);

            if ($accepted === $type) {
                return true;
            }

            if (str_ends_with($accepted, '/*') && ! self::isRestricted($type) && str_starts_with($type, substr($accepted, 0, -1))) {
                return true;
            }
        }

        return false;
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
                && $document->documentElement?->localName === 'svg';
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
