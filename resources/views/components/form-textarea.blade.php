@props([
    'name',
    'label' => '',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'id' => null,
    'class' => 'form-control', // Kelas default dari Sneat/Bootstrap
    'rows' => 3, // Atribut rows default untuk textarea
    'helpText' => '',
    'horizontal' => false, // Untuk layout horizontal
    'labelColClass' => 'col-md-2', // Kelas kolom default untuk label pada layout horizontal
    'inputColClass' => 'col-md-10', // Kelas kolom default untuk input pada layout horizontal
    'noMargin' => false, // Untuk menghilangkan margin bottom jika diperlukan
])

@php
    $id = $id ?? $name;
    $mainDivClass = $noMargin ? '' : 'mb-3';
    if ($horizontal) {
        $mainDivClass .= ' row';
    }
@endphp

<div class="{{ $mainDivClass }}">
    @if($label && !$horizontal)
        <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @endif

    @if($horizontal)
        <label for="{{ $id }}" class="{{ $labelColClass }} col-form-label">{{ $label }}</label>
        <div class="{{ $inputColClass }}">
    @endif

    <textarea name="{{ $name }}"
              id="{{ $id }}"
              class="{{ $class }} @error($name) is-invalid @enderror"
              rows="{{ $rows }}"
              @if($placeholder) placeholder="{{ $placeholder }}" @endif
              @if($required) required @endif
              @if($disabled) disabled @endif
              @if($readonly) readonly @endif
              {{ $attributes }}
    >{{ old($name, $value) }}</textarea>

    @error($name)
        <div class="invalid-feedback d-block">
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