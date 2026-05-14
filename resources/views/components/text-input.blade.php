@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-gray-700 focus:ring-gray-700 rounded-md shadow-sm']) }}>
