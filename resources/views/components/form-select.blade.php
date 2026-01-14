@props([
    'name',
    'label' => '',
    'options' => [], // Array asosiatif [value => text] atau array objek [{value: 'val', text: 'label'}]
    'selected' => null,
    'placeholder' => null, // Teks untuk opsi default (jika ada)
    'required' => false,
    'disabled' => false,
    'id' => null,
    'class' => 'form-select', // Kelas default dari Sneat/Bootstrap
    'helpText' => '',
    'multiple' => false,
    'horizontal' => false,
    'labelColClass' => 'col-md-2',
    'inputColClass' => 'col-md-10',
    'noMargin' => false,
])

@php
    $id = $id ?? $name;
    $mainDivClass = $noMargin ? '' : 'mb-3';
    if ($horizontal) {
        $mainDivClass .= ' row';
    }
    $selectName = $multiple ? $name . '[]' : $name;
@endphp

<div class="{{ $mainDivClass }}">
    @if($label && !$horizontal)
        <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @endif

    @if($horizontal)
        <label for="{{ $id }}" class="{{ $labelColClass }} col-form-label">{{ $label }}</label>
        <div class="{{ $inputColClass }}">
    @endif

    <select name="{{ $selectName }}"
            id="{{ $id }}"
            class="{{ $class }} @error($name) is-invalid @enderror @error(str_replace('[]', '', $name)) is-invalid @enderror"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($multiple) multiple @endif
            {{ $attributes }}
    >
        @if($placeholder !== null)
            <option value="" @if(is_null(old($name, $selected))) selected @endif disabled>{{ $placeholder }}</option>
        @endif

        @foreach($options as $key => $option)
            @php
                $optionValue = is_object($option) ? $option->value : (is_array($option) ? $option['value'] : $key);
                $optionText = is_object($option) ? $option->text : (is_array($option) ? $option['text'] : $option);
                $isSelected = false;
                if ($multiple) {
                    $isSelected = in_array($optionValue, old(str_replace('[]', '', $name), is_array($selected) ? $selected : []));
                } else {
                    $isSelected = (string)old($name, $selected) === (string)$optionValue;
                }
            @endphp
            <option value="{{ $optionValue }}" @if($isSelected) selected @endif>
                {{ $optionText }}
            </option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
    @error(str_replace('[]', '', $name))
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