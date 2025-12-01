<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-route" content="{{ route('auth.web3') }}">
    
    <title>Login - JagaBumi</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-50 to-green-50 min-h-screen flex flex-col items-center justify-center font-sans antialiased">

    <main class="w-full max-w-md px-4">
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

            <div id="loading" class="hidden mt-6 text-center" role="status" aria-live="polite">
                <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-green-100 border-t-green-600"></div>
                <p class="text-sm text-slate-600 mt-3 font-medium">Menghubungkan ke Web3...</p>
            </div>

            <div 
                id="error-box" 
                class="hidden mt-4 bg-red-50 text-red-700 text-sm p-4 rounded-lg text-center border border-red-200"
                role="alert"
                aria-live="assertive"
            ></div>
        </article>

        <footer class="mt-8 text-center">
            <p class="text-xs text-slate-500">
                &copy; {{ date('Y') }} JagaBumi. Powered by 
                <span class="font-medium">Web3Auth</span> & 
                <span class="font-medium">ZKsync</span>
            </p>
        </footer>
    </main>

    <script src="{{ asset('js/auth-web3.js') }}" defer></script>
</body>
</html>