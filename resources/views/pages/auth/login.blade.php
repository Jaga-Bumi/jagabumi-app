<x-layouts.app :mainClass="'bg-gradient-to-br from-slate-50 to-green-50 min-h-screen flex flex-col items-center justify-center'" :showFooter="true">
    <x-slot name="title">Login - JagaBumi</x-slot>
    
    <div class="w-full max-w-md px-4">
        <article class="bg-white p-8 rounded-2xl shadow-xl border border-slate-200">
            <header class="text-center mb-8">
                <h1 class="text-4xl font-bold text-green-700 mb-2">JagaBumi</h1>
                <p class="text-slate-600 text-sm">Start real action, earn eternal certificates.</p>
            </header>

            <div id="auth-buttons" class="space-y-4">
                <button 
                    id="auth-btn" 
                    type="button"
                    class="w-full flex items-center justify-center gap-3 bg-green-600 text-white font-semibold py-3.5 px-6 rounded-xl hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed group"
                    aria-label="Login"
                >
                    <span>Login</span>
                </button>
                
                <p class="text-center text-xs text-slate-500">
                    Login otomatis jika sudah terdaftar, atau daftar akun baru
                </p>
            </div>

            <div id="loading" class="hidden mt-6">
                <x-loading-spinner message="Menghubungkan ke Web3..." />
            </div>

            <x-error-box id="error-box" class="mt-4" />
        </article>
    </div>

    @push('scripts')
        <meta name="auth-route" content="{{ route('auth.web3') }}">
        <meta name="config-route" content="{{ route('web3.config') }}">
        @vite(['resources/js/auth.js'])
    @endpush
</x-layouts.app>
