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
            
            @auth
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
            @endauth

            @guest
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded mb-4">
                    <p class="text-yellow-800 text-sm mb-3">
                        ⚠️ Anda dapat melihat page ini, namun untuk melaksanakan Quest, silakan login terlebih dahulu.
                    </p>
                </div>

                <a 
                    href="{{ route('login') }}" 
                    class="inline-flex items-center justify-center w-full px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Login dengan Web3Auth
                </a>
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
