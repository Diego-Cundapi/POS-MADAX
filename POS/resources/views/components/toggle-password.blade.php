@props(['disabled' => false])

@php
    $uniqueId = 'toggle-pw-' . uniqid();
@endphp

<div
    class="relative"
    id="{{ $uniqueId }}"
    style="width: 100%; position: relative;"
>
    <input
        {{ $disabled ? 'disabled' : '' }}
        {!! $attributes->merge(['type' => 'password', 'class' => 'pe-10']) !!}
        style="width: 100%; padding-right: 2.5rem;"
    />
    <button
        type="button"
        class="kt-btn kt-btn-icon kt-btn-ghost size-6 absolute end-2 top-1/2 -translate-y-1/2"
        style="position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 0; display: flex; align-items: center;"
        onclick="togglePassword_{{ str_replace('-', '_', $uniqueId) }}()"
    >
        {{-- Eye icon (visible when password is hidden) --}}
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="lucide lucide-eye toggle-pw-eye-open"
            aria-hidden="true"
        >
            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
            <circle cx="12" cy="12" r="3"></circle>
        </svg>
        {{-- Eye-off icon (visible when password is shown) --}}
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="lucide lucide-eye-off toggle-pw-eye-off"
            style="display: none;"
            aria-hidden="true"
        >
            <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"></path>
            <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"></path>
            <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"></path>
            <path d="m2 2 20 20"></path>
        </svg>
    </button>
</div>

<script>
    function togglePassword_{{ str_replace('-', '_', $uniqueId) }}() {
        var container = document.getElementById('{{ $uniqueId }}');
        var input = container.querySelector('input');
        var eyeOpen = container.querySelector('.toggle-pw-eye-open');
        var eyeOff = container.querySelector('.toggle-pw-eye-off');

        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.style.display = 'none';
            eyeOff.style.display = 'block';
        } else {
            input.type = 'password';
            eyeOpen.style.display = 'block';
            eyeOff.style.display = 'none';
        }
    }
</script>
