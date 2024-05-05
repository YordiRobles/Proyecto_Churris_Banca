
<link href="/css/dashboard.css" rel="stylesheet">
<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Red Social') }}
            </h2>
            <div class="search-container">
                <form action="{{ route('search.users') }}" method="GET">
                    <div class="search-box">
                        <input type="text" name="query" placeholder="Buscar usuario por nombre" class="search-input">
                        <button type="submit" class="search-button"></button>
                    </div>
                </form>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
