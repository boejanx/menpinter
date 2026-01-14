@props([
    'id',
    'title' => null,
    'size' => null, // sm, lg, xl, fullscreen
    'centered' => false,
    'scrollable' => false,
    'staticBackdrop' => false,
    'hasForm' => false,
    'formId' => null,
    'method' => 'POST',
    'action' => '#',
])

@php
    $dialogClasses = [];

    if ($size) {
        $dialogClasses[] = match($size) {
            'sm' => 'modal-sm',
            'lg' => 'modal-lg',
            'xl' => 'modal-xl',
            'fullscreen' => 'modal-fullscreen',
            default => '',
        };
    }

    if ($centered) $dialogClasses[] = 'modal-dialog-centered';
    if ($scrollable) $dialogClasses[] = 'modal-dialog-scrollable';

    $httpMethod = strtoupper($method);
    $spoofMethod = !in_array($httpMethod, ['GET', 'POST']);
@endphp

<div
    class="modal fade"
    id="{{ $id }}"
    tabindex="-1"
    aria-labelledby="{{ $id }}Label"
    aria-hidden="true"
    @if($staticBackdrop)
        data-bs-backdrop="static"
        data-bs-keyboard="false"
    @endif
>
    <div class="modal-dialog {{ implode(' ', $dialogClasses) }}">
        <div class="modal-content">
            @if($hasForm)
                <form method="{{ $httpMethod === 'GET' ? 'GET' : 'POST' }}" action="{{ $action }}" id="{{ $formId }}" enctype="multipart/form-data">
                    @csrf
                    @if($spoofMethod)
                        @method($httpMethod)
                    @endif
            @endif

            @if($title)
                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
            @endif

            <div class="modal-body">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @else
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    @if($hasForm)
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    @endif
                </div>
            @endisset

            @if($hasForm)
                </form>
            @endif
        </div>
    </div>
</div>
