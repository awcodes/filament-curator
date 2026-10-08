---
title: Accepted file types
description: What Curator accepts by default, why certain types are excluded, and how to opt back in safely.
---

# Accepted file types

## The default list

When you do not set `acceptedFileTypes()` yourself, Curator falls back to `MimeType::defaults()` — the full `MimeType` list minus types that are effectively executable content:

`text/html`, `application/xhtml+xml`, `text/javascript`, `application/xml`, `application/vnd.mozilla.xul+xml`, `application/x-httpd-php`, `application/x-sh`, `application/x-csh`, `application/x-shockwave-flash` and `application/octet-stream`.

## Why those are excluded

Uploaded files are served from your application's own origin. An HTML or XML document containing a `<script>` tag executes with the session of whoever opens it, and with the default `public` disk the storage directory sits inside the document root, where a server may execute scripts directly.

`application/octet-stream` is excluded separately: it is what `finfo` reports for anything it cannot classify, so allowing it turns the allow list into a wildcard.

## Opting back in

You can allow these types, globally or per field, if your application genuinely needs to host them. A field's own setting takes precedence over the global one:

```php
use Awcodes\Curator\Enums\MimeType;
use Awcodes\Curator\Facades\Curator;

// globally
Curator::acceptedFileTypes([...MimeType::defaults(), 'text/html']);
```

```php
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Enums\MimeType;

// or per field
CuratorPicker::make('attachment')
    ->acceptedFileTypes([...MimeType::defaults(), 'text/html']);
```

> [!WARNING]
> Curator only sanitizes SVG uploads. Any other type you opt into is stored and served verbatim. Media served through Curator's route is sent with `X-Content-Type-Options: nosniff`, and restricted types are forced to `Content-Disposition: attachment`, but files on the `public` disk are also reachable directly through the `storage` symlink, where those headers do not apply. If you allow executable types, serve them from a private disk.

## How the stored extension is chosen

Curator decides whether to accept a file from its detected type, the type read from the file's contents. Curator detects the type from the file's contents itself, so neither the name nor the type the browser declared plays a part, whichever Livewire version or temporary upload disk you use. The stored extension follows that same type, not the name the browser sent. Web servers pick a file's content type from its extension, so the two have to agree.

- The original extension is kept, lowercased, when it is a known extension for the detected type. `photo.jpeg` stays `.jpeg` and `PHOTO.JPG` becomes `.jpg`.
- Text content is often detected only as `text/plain`, so it may keep a plain-data extension such as `.csv`, `.md`, `.json`, `.yaml` or `.css`. Other text files are stored as `.txt`, including ones with uncommon extensions such as `.srt`.
- An `.svg` file that starts with whitespace or a comment is detected as plain text, XML or, when it contains a script, HTML. It is still stored as `.svg`, and sanitized, if its content parses as XML whose root element is `<svg>` in the SVG namespace. Anything else, such as an HTML page named `.svg`, keeps its detected type.
- Office and OpenDocument files (`.docx`, `.xlsx`, `.pptx`, `.odt`, `.epub` and similar) are zip archives and are sometimes detected as `application/zip`. They keep their extension when the content is a zip archive.
- Otherwise the extension is replaced with the detected type's usual one. A JPEG uploaded as `photo.png` is stored as `.jpg`, and a file named `notes.html` whose contents are plain text is stored as `.txt`.
- When the detected type has no known extension, the file is stored as `.bin`, and Curator's route serves it as a download. This includes every `application/octet-stream` upload, if you have allowed that type.
- Extensions a server may run as code (`.php`, `.phtml`, `.phar`, `.shtml`, `.cgi` and similar) are never kept.

The same rule applies to `CuratorUtils::importMedia()`, which detects the type from the imported file's contents.

`preserveFilenames()` affects only the base name. The extension still follows the detected type.

Only fresh uploads become media. Any other value in an upload field's state is rejected by validation.

## Repairing media stored by earlier versions

Earlier versions kept the browser's extension. As a result, a database can hold media whose file extension doesn't match its contents, including some that a web server would serve as HTML. Versions 5.0.0 to 5.1.4 also accepted HTML files by default. After upgrading, check for these with a dry run:

```bash
php artisan curator:repair-extensions --dry-run
```

Then apply the changes:

```bash
php artisan curator:repair-extensions
```

For every media row, the command detects the stored file's type from its contents, then:

- **Renames files with an unsafe extension.** When the extension could be served as a document, script or server-side code (such as `.html`, `.svg`, `.xml`, `.js` or `.php`) but the content is something else, or the extension contains characters other than letters and digits, the file gets the extension for its detected type. Content detected as SVG is sanitized as it is renamed.
- **Neutralises HTML, XML and script content.** When the content itself is HTML, XHTML, XML or JavaScript, the file is renamed to `.txt`, so it is served as plain text. The bytes are kept for you to review or delete. If your global `acceptedFileTypes()` deliberately includes that type, the file is left in place and listed as `review` instead.
- **Reports harmless mismatches** and leaves them as they are, such as a JPEG stored as `.png`, or an extension that differs only in case, such as `.JPG`. Renaming them would only break existing links.

When a file is renamed, the row's `path`, `ext` and `type` are updated and the file stays in its directory.

- The file's contents are kept. Only SVG content is changed, by sanitizing. Each rename is printed as `old path -> new path`, so keep the output if you may need to undo a change.
- A row is skipped, with a warning, when its file is missing, when the new name is already taken, or when SVG content can't be sanitized.
- The media's URL changes with its path. Links to a renamed file that were copied elsewhere, for example into rich editor content, will no longer resolve.
- The command doesn't sanitize SVG files that already have an `.svg` extension. Run `php artisan curator:sanitize-svgs` for those.
