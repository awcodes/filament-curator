<x-workbench::layouts.front :title="$post->title">
    <h1>{{ $post->title }}</h1>

    <section data-focus="featured-image">
        <h2>Featured image</h2>
        <p class="note">&lt;x-curator-glider&gt; with a width srcset, sizes and webp.</p>
        <x-curator-glider
            :media="$post->featuredImage"
            :srcset="['1024w', '640w']"
            sizes="(max-width: 1024px) 100vw, 1024px"
            format="webp"
        />

        <h3>Pixel density</h3>
        <p class="note">A fixed 400px width with a 1x / 2x srcset.</p>
        <x-curator-glider
            :media="$post->featuredImage"
            width="400"
            :srcset="['1x', '2x']"
            sizes="400px"
            format="webp"
        />
    </section>

    <section data-focus="gallery">
        <h2>Gallery</h2>
        <p class="note">The post's ordered gallery relationship, cropped to 4:3.</p>
        @if ($post->galleryMedia->isEmpty())
            <p class="muted">This post has no gallery.</p>
        @else
            <div class="grid">
                @foreach ($post->galleryMedia as $media)
                    <figure>
                        <x-curator-glider :media="$media" width="320" height="240" fit="crop" format="webp" loading="lazy" />
                        <figcaption>{{ $media->caption }}</figcaption>
                    </figure>
                @endforeach
            </div>
        @endif
    </section>

    <section data-focus="curation">
        <h2>Curation</h2>
        <p class="note">&lt;x-curator-curation&gt; for the seeded "Post thumbnail" curation of Forest hills.</p>
        <x-curator-curation :media="$curated" curation="post_thumbnail" />
    </section>

    @if ($post->featuredImage)
        <section data-focus="size-urls">
            <h2>Size URLs</h2>
            <p class="note">The Media model's appended URLs.</p>
            <div class="grid">
                @foreach (['thumbnail_url', 'medium_url', 'large_url', 'url'] as $attribute)
                    <figure>
                        <img src="{{ $post->featuredImage->{$attribute} }}" alt="{{ $post->featuredImage->alt }}" loading="lazy">
                        <figcaption><code>{{ $attribute }}</code></figcaption>
                    </figure>
                @endforeach
            </div>
        </section>
    @endif

    <section data-focus="content">
        <h2>Content</h2>
        <p class="note">The post's saved content, rendered with RichContentRenderer and AttachCuratorMediaPlugin.</p>
        <div class="rendered">{!! $content !!}</div>
    </section>

    @if ($richContentShapes !== [])
        <section data-focus="rich-content-shapes">
            <h2>Saved rich content shapes</h2>
            <p class="note">Built from the featured image. Each shows what is stored, then what the renderer outputs.</p>
            @foreach ($richContentShapes as $shape)
                <h3>{{ $shape['label'] }}</h3>
                <p class="note">{{ $shape['description'] }}</p>
                <div class="shape">
                    <pre>{{ $shape['stored'] }}</pre>
                    <div class="rendered">{!! $shape['html'] !!}</div>
                </div>
            @endforeach
        </section>
    @endif
</x-workbench::layouts.front>
