<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-zinc-900 text-zinc-100 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-4xl font-bold text-indigo-400 mb-4">Boilerworks</h1>
        <p class="text-zinc-400 mb-8">Laravel + Livewire Full-Stack Template</p>
        <div class="space-x-4">
            @auth
            <a href="{{ route('dashboard') }}" class="rounded-lg bg-indigo-600 px-6 py-3 text-white font-medium hover:bg-indigo-700">Dashboard</a>
            @else
            <a href="{{ route('login') }}" class="rounded-lg bg-indigo-600 px-6 py-3 text-white font-medium hover:bg-indigo-700">Sign In</a>
            <a href="{{ route('register') }}" class="rounded-lg border border-zinc-600 px-6 py-3 text-zinc-300 font-medium hover:bg-zinc-800">Register</a>
            @endauth
        </div>
    </div>
</body>
</html>
