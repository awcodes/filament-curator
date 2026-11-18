@props(['title'])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · Curator Workbench</title>
    <style>
        :root { color-scheme: light; --ink: #1f2933; --muted: #616e7c; --line: #e4e7eb; --panel: #f5f7fa; }
        * { box-sizing: border-box; }
        body { margin: 0; font: 16px/1.6 system-ui, sans-serif; color: var(--ink); background: #fff; }
        header, main { max-width: 1024px; margin: 0 auto; padding: 0 16px; }
        header { display: flex; gap: 16px; align-items: baseline; padding-block: 16px; border-bottom: 1px solid var(--line); }
        header a { color: inherit; }
        main { padding-bottom: 48px; }
        h1 { margin: 32px 0 8px; line-height: 1.2; }
        h2 { margin: 48px 0 8px; font-size: 1.25rem; border-bottom: 1px solid var(--line); padding-bottom: 4px; }
        h3 { margin: 24px 0 4px; font-size: 1rem; }
        .note, figcaption, .muted { color: var(--muted); font-size: 0.875rem; }
        img { max-width: 100%; height: auto; display: block; background: var(--panel); }
        figure { margin: 0; }
        .grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); }
        .shape { display: grid; gap: 16px; grid-template-columns: 1fr 1fr; align-items: start; }
        .shape > * { min-width: 0; }
        pre { margin: 0; padding: 12px; overflow-x: auto; background: var(--panel); border-radius: 4px; font-size: 0.75rem; white-space: pre-wrap; word-break: break-all; }
        .rendered { padding: 12px; border: 1px dashed var(--line); border-radius: 4px; min-height: 48px; }
        .posts { list-style: none; padding: 0; }
        .posts li { display: flex; gap: 16px; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--line); }
        @media (max-width: 640px) { .shape { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <strong>Curator Workbench</strong>
        <a href="{{ route('workbench.posts.index') }}">Posts</a>
        <a href="/admin">Admin</a>
    </header>
    <main>
        {{ $slot }}
    </main>
</body>
</html>
