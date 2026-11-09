@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'form-input' . ($disabled ? ' opacity-50 cursor-not-allowed bg-gray-50' : '')]) }}>
