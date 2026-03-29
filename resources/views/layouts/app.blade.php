<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-zinc-900 text-zinc-100 min-h-screen">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="w-64 bg-zinc-950 border-r border-zinc-800 flex flex-col">
            <div class="p-6 border-b border-zinc-800">
                <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-400">Boilerworks</a>
            </div>
            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 rounded-lg text-sm {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' }}">
                    Dashboard
                </a>
                <a href="{{ route('items.index') }}" class="flex items-center px-3 py-2 rounded-lg text-sm {{ request()->routeIs('items.*') ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' }}">
                    Items
                </a>
                <a href="{{ route('categories.index') }}" class="flex items-center px-3 py-2 rounded-lg text-sm {{ request()->routeIs('categories.*') ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' }}">
                    Categories
                </a>
                @if(config('features.forms'))
                <a href="{{ route('forms.index') }}" class="flex items-center px-3 py-2 rounded-lg text-sm {{ request()->routeIs('forms.*') ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' }}">
                    Forms
                </a>
                @endif
                @if(config('features.workflows'))
                <a href="{{ route('workflows.index') }}" class="flex items-center px-3 py-2 rounded-lg text-sm {{ request()->routeIs('workflows.*') ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' }}">
                    Workflows
                </a>
                @endif
            </nav>
            <div class="p-4 border-t border-zinc-800">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-zinc-400">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-zinc-500 hover:text-white">Logout</button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main content --}}
        <main class="flex-1 p-8">
            @if(session('success'))
                <div class="mb-6 rounded-lg bg-emerald-900/50 border border-emerald-700 p-4 text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 rounded-lg bg-red-900/50 border border-red-700 p-4 text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>
</html>
