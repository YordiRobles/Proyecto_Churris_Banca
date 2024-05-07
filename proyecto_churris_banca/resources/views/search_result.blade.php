

<link href="/css/dashboard.css" rel="stylesheet">
<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="font-title">
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
    <div>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td><a href="{{ route('user.show', $user->name) }}">{{ $user->name }}</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>