<x-layouts.app :showHeader="true">
    <x-slot name="title">Dashboard - JagaBumi</x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
            @auth
                <button 
                    id="logout-btn" 
                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Logout
                </button>
            @endauth
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white p-6 rounded-lg shadow">
            <h1 class="text-2xl font-bold mb-4">Selamat Datang, {{ $user->name }}!</h1>
            
            <div class="bg-green-50 p-4 rounded border border-green-200 mb-4">
                <p class="font-bold text-green-800">Status: Login Berhasil via MySQL</p>
            </div>

            <ul class="list-disc pl-5 space-y-2">
                <li><strong>Email:</strong> {{ $user->email }}</li>
                <li><strong>Verifier ID (Google):</strong> {{ $user->verifier_id }}</li>
                <li>
                    <strong>Wallet Address (Auto-Generated):</strong> 
                    <code class="bg-gray-200 px-1 rounded">{{ $user->wallet_address }}</code>
                </li>
            </ul>

            @guest
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded">
                    <p class="text-yellow-800 text-sm">
                        ⚠️ Anda dapat melihat page ini, namun untuk melaksanakan Quest, silakan login terlebih dahulu.
                    </p>
                </div>
            @endguest
        </div>
    </div>

    @auth
        @push('scripts')
            <meta name="logout-route" content="{{ route('logout') }}">
            @vite(['resources/js/logout.js'])
        @endpush
    @endauth
</x-layouts.app>
