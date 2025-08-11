<?php

declare(strict_types=1);

namespace Awcodes\Curator\Enums;

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

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
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
}
