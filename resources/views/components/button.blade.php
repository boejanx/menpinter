@props([
    'type' => 'button', // submit, reset, button
    'text' => '', // Teks di dalam tombol, bisa juga menggunakan slot
    'variant' => 'primary', // primary, secondary, success, danger, warning, info, light, dark, link
    'size' => '', // lg, sm
    'outline' => false,
    'icon' => '', // Nama kelas ikon (misal: 'bx bx-user')
    'iconPosition' => 'before', // before, after
    'disabled' => false,
    'loading' => false, // Jika true, tampilkan spinner
    'loadingText' => 'Memuat...', // Teks saat loading
    'id' => null,
    'class' => '', // Kelas CSS tambahan
    'href' => null, // Jika ini adalah link, bukan button
    'target' => null, // Untuk link, misal _blank
])

@php
    $baseClass = 'btn';
    $variantClass = $outline ? "btn-outline-{$variant}" : "btn-{$variant}";
    $sizeClass = $size ? "btn-{$size}" : '';

    $allClasses = collect([$baseClass, $variantClass, $sizeClass, $class])->filter()->implode(' ');

    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if($tag === 'button') type="{{ $type }}" @else href="{{ $href }}" @if($target) target="{{ $target }}" @endif @endif
    id="{{ $id }}"
    class="{{ $allClasses }} @if($loading) disabled @endif"
    @if($disabled || $loading) disabled @endif
    {{ $attributes }}
>
    @if($loading)
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        <span class="visually-hidden">{{ $loadingText }}</span>
        @if($loadingText && $text)
            <span class="ms-1">{{ $loadingText }}</span>
        @elseif($loadingText)
            {{ $loadingText }}
        @endif
    @else
        @if($icon && $iconPosition === 'before')
            <i class="{{ $icon }} @if($text || $slot->isNotEmpty()) me-1 @endif"></i>
        @endif

        {{ $text ?: $slot }}

        @if($icon && $iconPosition === 'after')
            <i class="{{ $icon }} @if($text || $slot->isNotEmpty()) ms-1 @endif"></i>
        @endif
    @endif
</{{ $tag }}>