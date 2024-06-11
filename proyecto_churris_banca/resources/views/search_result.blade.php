<link href="/css/dashboard.css" rel="stylesheet">
<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="font-title">
                {{ __('Red Social') }}
            </h2>
            <div class="search-container">
                <form action="{{ route('search.users') }}" method="GET">
                    @csrf
                    <div class="search-box">
                        <input type="text" name="query" placeholder="Buscar usuario por nombre" class="search-input" value="{{ old('query') }}">
                        <button type="submit" class="search-button"></button>
                    </div>
                    @if($errors->has('query'))
                        <div class="alert alert-danger">
                            {{ $errors->first('query') }}
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </x-slot>
    <div class="title-container">
        <h2 class="title-result">Usuarios encontrados</h2>
    </div>
    <div class="table-container">
        <table class="user-table">
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