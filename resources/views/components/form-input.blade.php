@props([
    'type' => 'text',
    'name',
    'label' => '',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'id' => null,
    'class' => 'form-control',
    'helpText' => '',
    'horizontal' => false, // Properti baru untuk layout horizontal
    'labelColClass' => 'col-sm-2', // Default kelas kolom untuk label
    'inputColClass' => 'col-sm-10', // Default kelas kolom untuk input
    'noMargin' => false // Properti untuk menghilangkan margin bottom
])

@php
    $id = $id ?? $name;
    $mainDivClass = $noMargin ? '' : 'mb-3';
    if ($horizontal) {
        $mainDivClass .= ' row';
    }
@endphp

<div class="{{ $mainDivClass }}">
    @if($label)
        <label for="{{ $id }}" class="form-label @if($horizontal){{ $labelColClass }} col-form-label @endif">{{ $label }}</label>
    @endif

    @if($horizontal)
    <div class="{{ $inputColClass }}">
    @endif

    <input type="{{ $type }}"
           name="{{ $name }}"
           id="{{ $id }}"
           value="{{ old($name, $value) }}"
           class="{{ $class }} @error($name) is-invalid @enderror"
           @if($placeholder) placeholder="{{ $placeholder }}" @endif
           @if($required) required @endif
           @if($disabled) disabled @endif
           @if($readonly) readonly @endif
           {{ $attributes }}
    >
    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
    @if($helpText)
        <div id="{{ $id }}Help" class="form-text">{{ $helpText }}</div>
    @endif

    @if($horizontal)
    </div>
    @endif
</div>