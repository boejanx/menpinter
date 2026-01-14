@props([
    'title' => null,
    'header' => null,
    'footer' => null,
    'imgSrc' => null,
    'imgAlt' => 'Card image',
    'imgPosition' => 'top', // top, bottom, none
    'variant' => null,
    'outline' => false,
    'class' => '',
    'headerClass' => '',
    'bodyClass' => '',
    'footerClass' => '',
    'titleClass' => 'card-title',
    'noBody' => false,
    'fallbackImg' => '/assets/img/default.jpg', // default fallback image
])

@php
    $cardClasses = ['card'];
    if ($variant) {
        $cardClasses[] = $outline ? "border-{$variant}" : "bg-{$variant} text-white";
    }
    if (!empty($class)) {
        $cardClasses[] = $class;
    }

    $shouldShowImage = $imgSrc && $imgPosition !== 'none';
@endphp

<div {{ $attributes->merge(['class' => implode(' ', $cardClasses)]) }}>
    {{-- Gambar di atas --}}
    @if($shouldShowImage && $imgPosition === 'top')
        <img
            class="card-img-top lazy"
            data-src="{{ $imgSrc }}"
            alt="{{ $imgAlt }}"
            onerror="this.onerror=null;this.src='{{ $fallbackImg }}';"
        >
    @endif

    {{-- Header judul --}}
    @if($header || $title)
        <div class="card-header {{ $headerClass }}">
            @if($header)
                {{ $header }}
            @else
                <h5 class="{{ $titleClass }}">{{ $title }}</h5>
            @endif
        </div>
    @endif

    {{-- Body --}}
    @if(!$noBody)
        <div class="card-body {{ $bodyClass }}">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif

    {{-- Gambar di bawah --}}
    @if($shouldShowImage && $imgPosition === 'bottom')
        <img
            class="card-img-bottom lazy"
            data-src="{{ $imgSrc }}"
            alt="{{ $imgAlt }}"
            onerror="this.onerror=null;this.src='{{ $fallbackImg }}';"
        >
    @endif

    {{-- Footer --}}
    @if($footer)
        <div class="card-footer {{ $footerClass }}">
            {{ $footer }}
        </div>
    @endif
</div>
