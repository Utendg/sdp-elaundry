@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-aun-navy focus:ring-aun-orange rounded-md shadow-sm']) }}>
