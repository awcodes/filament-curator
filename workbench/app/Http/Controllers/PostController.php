<?php

declare(strict_types=1);

namespace Workbench\App\Http\Controllers;

use Awcodes\Curator\Components\Forms\RichEditor\AttachCuratorMediaPlugin;
use Awcodes\Curator\Models\Media;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Contracts\View\View;
use Workbench\App\Models\Post;

/**
 * A public front end for the Workbench posts, rendering Curator's output outside Filament the way an app would.
 */
class PostController
{
    public function index(): View
    {
        return view('workbench::posts.index', [
            'posts' => Post::query()->with('featuredImage')->oldest('id')->get(),
        ]);
    }

    public function show(Post $post): View
    {
        $post->load(['featuredImage', 'galleryMedia']);

        return view('workbench::posts.show', [
            'post' => $post,
            'content' => RichContentRenderer::make($post->getAttribute('content'))
                ->plugins([AttachCuratorMediaPlugin::make()])
                ->toHtml(),
            'curated' => Media::query()->where('name', 'forest-hills')->first(),
            'richContentShapes' => $post->featuredImage instanceof Media
                ? $this->richContentShapes($post->featuredImage)
                : [],
        ]);
    }

    /**
     * The three shapes saved RichEditor content can take, each rendered as the editor's plugin would render it.
     *
     * @return array<int, array{label: string, description: string, stored: string, html: string}>
     */
    protected function richContentShapes(Media $media): array
    {
        $key = $media->getKey();
        $alt = e($media->alt);
        $title = e($media->title);
        $url = e($media->url);

        $legacy = "<p><img src=\"/storage/moved-since-insert.jpg\" alt=\"{$alt}\" title=\"{$title}\" data-id=\"{$key}\"></p>";
        $plain = "<p><img src=\"{$url}\" alt=\"{$alt}\" title=\"{$title}\"></p>";

        $json = [
            'type' => 'doc',
            'content' => [[
                'type' => 'paragraph',
                'content' => [[
                    'type' => 'image',
                    'attrs' => ['src' => '/storage/moved-since-insert.jpg', 'alt' => $media->alt, 'title' => $media->title, 'id' => $key],
                ]],
            ]],
        ];

        return [
            [
                'label' => 'A. Legacy (≤ 5.4), default plugin',
                'description' => 'data-id holds the media key and src is the URL at insert time. Without the provider Filament reads data-id as a path and the src is lost.',
                'stored' => $legacy,
                'html' => $this->render($legacy, storeMediaIds: false),
            ],
            [
                'label' => 'A. Legacy (≤ 5.4), plugin storing media ids',
                'description' => 'The same HTML, resolved through CuratorFileAttachmentProvider: the src follows the media item.',
                'stored' => $legacy,
                'html' => $this->render($legacy, storeMediaIds: true),
            ],
            [
                'label' => 'B. Plain image (5.5+ default)',
                'description' => 'A static src with no media key. It breaks if the file is swapped, moved or deleted.',
                'stored' => $plain,
                'html' => $this->render($plain, storeMediaIds: false),
            ],
            [
                'label' => 'C. Media ids (5.6+ storeMediaIds), JSON storage',
                'description' => 'TipTap JSON with the media key in attrs.id, resolved through the provider on every render.',
                'stored' => (string) json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'html' => $this->render($json, storeMediaIds: true),
            ],
        ];
    }

    /**
     * @param  string | array<string, mixed>  $content
     */
    protected function render(string | array $content, bool $storeMediaIds): string
    {
        return RichContentRenderer::make($content)
            ->plugins([AttachCuratorMediaPlugin::make()->storeMediaIds($storeMediaIds)])
            ->toHtml();
    }
}
