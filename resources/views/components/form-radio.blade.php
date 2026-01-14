@props([
    'name',
    'label' => '',
    'value' => '',
    'checkedValue' => null, // Nilai yang seharusnya terpilih
    'disabled' => false,
    'id' => null,
    'class' => 'form-check-input', // Kelas default dari Sneat/Bootstrap
    'labelClass' => 'form-check-label',
    'helpText' => '', // Tidak umum untuk radio individual, tapi bisa saja
    'inline' => false,
    'horizontal' => false, // Menandakan jika ini bagian dari grup radio horizontal
    'labelColClass' => 'col-md-2', // Hanya relevan jika ada label grup untuk radio horizontal
    'inputColClass' => 'col-md-10', // Hanya relevan jika ada label grup untuk radio horizontal
    'noMargin' => false,
    'isGroup' => false, // Menandakan jika ini bagian dari grup radio
])

@php
    $id = $id ?? $name . '_' . Str::slug($value, '_') . '_' . Str::random(4);
    $mainDivClass = $noMargin && !$isGroup ? '' : 'mb-0'; // Margin diatur oleh group jika ada
    $containerClass = 'form-check';
    if ($inline) $containerClass .= ' form-check-inline';

    // Handle old input for radio
    // If the form was submitted, old($name) will be the value of the selected radio.
    // If not submitted, $checkedValue prop is used.
    $isChecked = old($name) !== null ? old($name) == $value : $checkedValue == $value;
@endphp

<div class="{{ $containerClass }} @if($horizontal && $isGroup) {{ $mainDivClass }} @elseif(!$isGroup) {{ $noMargin ? '' : 'mb-3' }} @endif">
    <input type="radio"
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
    {{-- Error dan help text biasanya ditampilkan di level group untuk radio --}}
    @if(!$isGroup)
        @error($name)
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
        @enderror
        @if($helpText)
            <div id="{{ $name }}Help" class="form-text">{{ $helpText }}</div>
        @endif
    @endif
</div>