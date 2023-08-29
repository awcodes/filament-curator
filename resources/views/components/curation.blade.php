@if ($curatedMedia)
<img
    src="{{ $curatedMedia['url'] }}"
    alt="{{ $media['alt'] }}"
    width="{{ $curatedMedia['width'] }}"
    height="{{ $curatedMedia['height'] }}"
    {{ $attributes }}
/>
@endif

