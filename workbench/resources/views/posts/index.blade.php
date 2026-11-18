<x-workbench::layouts.front title="Posts">
    <h1>Posts</h1>
    <p class="note">Each post renders Curator's front-end output outside Filament.</p>

    <ul class="posts" data-focus="post-list">
        @foreach ($posts as $post)
            <li>
                <x-curator-glider :media="$post->featuredImage" width="96" height="60" fit="crop" format="webp" />
                <a href="{{ route('workbench.posts.show', $post) }}">{{ $post->title }}</a>
            </li>
        @endforeach
    </ul>
</x-workbench::layouts.front>
