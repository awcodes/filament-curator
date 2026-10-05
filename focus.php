<?php

declare(strict_types=1);

use Awcodes\Focus\Card;
use Awcodes\Focus\Enums\Size;
use Awcodes\Focus\Screenshot;
use Awcodes\Focus\ScreenshotSuite;

/*
 * Documentation screenshots for Curator, generated with awcodes/focus from the Workbench (run `composer build`
 * first). The Workbench seeds eight GD-drawn fixture images from workbench/fixtures/media onto the local public disk at
 * fixed dates, and five posts that use them as featured images and galleries. Thumbnails are rendered locally by Glide.
 */

// Both modals are full-screen, so they are captured as the viewport rather than framed.
$panel = '.curator-panel:visible';
$launchPicker = '[data-focus="featured-image-picker"] button:has-text("Add media")';

// The awcodes card templates frame each screenshot at 1400x816. The media library grid needs that full width to keep
// four columns.
$card = [1400, 816];

// composer.json's full description wraps onto the screenshots in the card templates, so the cards use its first clause.
$description = 'A media picker and manager for Filament.';

return ScreenshotSuite::make()
    ->screenshots([
        // The media resource in its default grid layout.
        Screenshot::make('media-library')
            ->visit('/admin/media')
            ->viewport(),

        // The media resource's edit page: preview, details and meta.
        Screenshot::make('media-edit')
            ->visit('/admin/media/8/edit')
            ->fullPage(),

        // A single picker with an image selected.
        Screenshot::make('picker')
            ->visit('/admin/posts/1/edit')
            ->focus('[data-focus="featured-image-picker"]')
            ->padding(16),

        // A multiple picker bound to a morph-many relationship.
        Screenshot::make('picker-multiple')
            ->visit('/admin/posts/1/edit')
            ->focus('[data-focus="gallery-picker"]')
            ->padding(16),

        // The picker's library modal, with one image selected.
        Screenshot::make('picker-modal')
            ->viewportSize(1280, 720)
            ->visit('/admin/posts/create')
            ->click($launchPicker)
            ->waitFor($panel)
            ->click('.curator-panel button:has(img[alt="Violet mountain ridges against an orange dusk sky"])')
            ->viewport(),

        // The curation modal with the Workbench's "Post thumbnail" preset chosen.
        Screenshot::make('curation')
            ->visit('/admin/media/8/edit')
            ->click('button[role="tab"]:has-text("Curations")')
            ->click('button:has-text("Add to curations")')
            ->click('button:has-text("Create Curation")')
            ->waitFor('.cropper-container')
            ->select('select[name="preset"]:visible', 'post_thumbnail')
            ->viewport(),

        // CuratorColumn: a single featured image, and a stacked, ringed gallery limited to three.
        Screenshot::make('table-column')
            ->visit('/admin/posts')
            ->focus('[data-focus="posts-table"]'),

        // The share-image source, shaped to the card templates' screenshot slots. The two-up templates show it
        // dark in slot 1 and light in slot 2, so it is captured in both themes.
        Screenshot::make('card-library')
            ->viewportSize(...$card)
            ->visit('/admin/media')
            ->viewport(),
    ])
    ->cardTemplates('https://github.com/awcodes/focus-templates/tree/v2.1.0/dist')
    ->cards([
        // Open Graph and the GitHub social preview share one 2400x1260 template; GitHub crops 30px top and bottom.
        Card::make('social')
            ->template('two-up-wide')
            ->title('Curator')
            ->description($description)
            ->screenshots(['card-library', 'card-library'])
            ->sizes([Size::OpenGraph, Size::GitHubSocial]),

        // The Filament plugin directory's 2560x1440 thumbnail.
        Card::make('thumbnail')
            ->template('two-up')
            ->title('Curator')
            ->description($description)
            ->screenshots(['card-library', 'card-library'])
            ->sizes([Size::Filament]),

        // Unbranded 16:9 image for aw.codes, which adds its own heading: the same screenshots, no text or logo.
        Card::make('plain')
            ->template('two-up-plain')
            ->screenshots(['card-library', 'card-library'])
            ->sizes([[2560, 1440]])
            ->scale(1),
    ]);
