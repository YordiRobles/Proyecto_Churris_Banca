<!--Styles-->
<link href="/css/navigation.css" rel="stylesheet">

@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-gray-900 dark:text-gray-100 focus:outline-none transition duration-150 ease-in-out text-underline'
            : 'inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none hover:text-underline transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
