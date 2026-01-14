@props([
    'name',
    'label' => '',
    'value' => '1', // Nilai default checkbox jika dicentang
    'checked' => false,
    'disabled' => false,
    'id' => null,
    'class' => 'form-check-input', // Kelas default dari Sneat/Bootstrap
    'labelClass' => 'form-check-label',
    'helpText' => '',
    'inline' => false, // Untuk checkbox inline
    'switch' => false, // Untuk tampilan switch (Bootstrap 5)
    'horizontal' => false,
    'labelColClass' => 'col-md-2',
    'inputColClass' => 'col-md-10',
    'noMargin' => false, // Untuk menghilangkan margin bottom jika diperlukan
    'isGroup' => false, // Menandakan jika ini bagian dari grup checkbox horizontal
])

@php
    $id = $id ?? $name . '_' . Str::random(4);
    $mainDivClass = $noMargin && !$isGroup ? '' : 'mb-3';
    $containerClass = 'form-check';
    if ($inline) $containerClass .= ' form-check-inline';
    if ($switch) $containerClass .= ' form-switch';

    if ($horizontal && !$isGroup) {
        $mainDivClass .= ' row';
    }

    // Handle old input for checkbox
    // If the form was submitted and this checkbox was not checked, old($name) will be null.
    // If it was checked, old($name) will be its value.
    // If the form was not submitted, $checked prop is used.
    $isChecked = old($name) !== null ? old($name) == $value : $checked;
@endphp

<div class="{{ $mainDivClass }}">
    @if($horizontal && !$isGroup)
        <div class="{{ $labelColClass }}"></div> {{-- Kolom kosong untuk alignment label di baris input --}}
        <div class="{{ $inputColClass }}">
            <div class="{{ $containerClass }}">
                <input type="checkbox"
                       name="{{ $name }}"
                       id="{{ $id }}"
                       value="{{ $value }}"
                       class="{{ $class }} @error($name) is-invalid @enderror"
                       @if($isChecked) checked @endif
                       @if($disabled) disabled @endif
                       {{ $attributes }}
                >
                @if($label)
                    <label class="{{ $labelClass }}" for="{{ $id }}">{{ $label }}</label>
                @endif
                @error($name)
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
                @if($helpText)
                    <div id="{{ $id }}Help" class="form-text">{{ $helpText }}</div>
                @endif
            </div>
        </div>
    @else
        <div class="{{ $containerClass }}">
            <input type="checkbox"
                   name="{{ $name }}"
                   id="{{ $id }}"
                   value="{{ $value }}"
                   class="{{ $class }} @error($name) is-invalid @enderror"
                   @if($isChecked) checked @endif
                   @if($disabled) disabled @endif
                   {{ $attributes }}
            >
            @if($label)
                <label class="{{ $labelClass }}" for="{{ $id }}">{{ $label }}</label>
            @endif
            @error($name)
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
            @if($helpText && !$inline && !$switch) {{-- Bantuan teks lebih baik di luar div .form-check untuk non-inline/switch --}}
                 </div>
                 <div id="{{ $id }}Help" class="form-text">{{ $helpText }}</div>
            @elseif($helpText)
                <div id="{{ $id }}Help" class="form-text">{{ $helpText }}</div>
            @endif
        @if(!$helpText || ($helpText && ($inline || $switch)))
            </div>
        @endif
    @endif
</div>