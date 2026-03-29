<x-guest-layout>
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-8">
        <h2 class="text-xl font-semibold mb-6">Sign In</h2>
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm text-zinc-400 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100 focus:border-indigo-500 focus:ring-indigo-500">
                @error('email') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="block text-sm text-zinc-400 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100 focus:border-indigo-500 focus:ring-indigo-500">
                @error('password') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-white font-medium hover:bg-indigo-700">
                Sign In
            </button>
        </form>
        <p class="text-center text-sm text-zinc-500 mt-4">
            Don't have an account? <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300">Register</a>
        </p>
    </div>
</x-guest-layout>
