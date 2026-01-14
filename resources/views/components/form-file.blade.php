@props([
    'name',
    'label' => null,
    'required' => false,
    'disabled' => false,
    'helpText' => null,
    'preview' => false,
    'previewTarget' => 'imagePreview',
    'horizontal' => false,
    'labelColClass' => 'col-md-3',
    'inputColClass' => 'col-md-9',
    'noMargin' => false,
])

@php
    $inputClass = $attributes->get('class', '');
    $inputClass = trim("form-control $inputClass");
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
    $isInvalid = $hasError ? 'is-invalid' : '';
    $value = old($name, $attributes->get('value'));
@endphp

<div class="form-group @if(!$noMargin) mb-3 @endif @if($horizontal) row @endif">
    @if($label)
        <label for="{{ $id }}" class="form-label @if($horizontal) {{ $labelColClass }} col-form-label @endif">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    @if($horizontal)
        <div class="{{ $inputColClass }}">
    @endif

    <input
        type="file"
        class="{{ $inputClass }} {{ $isInvalid }}"
        id="{{ $id }}"
        name="{{ $name }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->except(['class', 'id', 'name', 'required', 'disabled', 'value'])
->merge(['value' => $value]) }}
    >

    @if ($hasError)
        <div class="invalid-feedback">
            {{ $errors->first($name) }}
        </div>
    @endif

    @if ($helpText)
        <small class="form-text text-muted">{{ $helpText }}</small>
    @endif

    @if ($preview)
        <img id="{{ $previewTarget }}" src="#" alt="Preview Gambar" style="max-height: 150px; margin-top: 10px; @if(!old($name) && !$attributes->get('value')) display: none; @endif" />

        @push('scripts')
            <script>
                document.getElementById('{{ $id }}').addEventListener('change', function(event) {
                    const [file] = event.target.files;
                    if (file) {
                        document.getElementById('{{ $previewTarget }}').src = URL.createObjectURL(file);
                        document.getElementById('{{ $previewTarget }}').style.display = 'block';
                    }
                });
            </script>
        @endpush
    @endif

    @if($horizontal)
        </div>
    @endif
</div>