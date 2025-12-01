<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-4">Selamat Datang, {{ $user->name }}!</h1>
        
        <div class="bg-green-50 p-4 rounded border border-green-200 mb-4">
            <p class="font-bold text-green-800">Status: Login Berhasil via MySQL</p>
        </div>

        <ul class="list-disc pl-5 space-y-2">
            <li><strong>Email:</strong> {{ $user->email }}</li>
            <li><strong>Verifier ID (Google):</strong> {{ $user->verifier_id }}</li>
            <li><strong>Wallet Address (Auto-Generated):</strong> <code class="bg-gray-200 px-1 rounded">{{ $user->wallet_address }}</code></li>
        </ul>

        <button id="logout-btn" class="mt-6 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
            Logout
        </button>
    </div>

    <script>
        document.getElementById('logout-btn').addEventListener('click', async function() {
            this.disabled = true;
            this.textContent = 'Logging out...';
            
            try {
                try {
                    const libs = [
                        "https://cdn.jsdelivr.net/npm/@web3auth/modal@9.4.0/dist/modal.umd.min.js",
                        "https://cdn.jsdelivr.net/npm/@web3auth/ethereum-provider@9.4.0/dist/ethereumProvider.umd.min.js"
                    ];
                    
                    for (const src of libs) {
                        if (!document.querySelector(`script[src="${src}"]`)) {
                            await new Promise((resolve, reject) => {
                                const s = document.createElement('script');
                                s.src = src;
                                s.async = true;
                                s.onload = resolve;
                                s.onerror = reject;
                                document.body.appendChild(s);
                            });
                        }
                    }

                    const privateKeyProvider = new window.EthereumProvider.EthereumPrivateKeyProvider({
                        config: {
                            chainConfig: {
                                chainNamespace: "eip155",
                                chainId: "0x12c",
                                rpcTarget: "https://sepolia.era.zksync.dev",
                            }
                        }
                    });

                    const web3auth = new window.Modal.Web3Auth({
                        clientId: "BFcEYcKaDaVLDOQXYPk1rpJHxJkxZa0oZsCf22YIoARnC-85o8hMZE3Kboy5V8vkcyMOws3STJQm5HfG01Da20Q",
                        web3AuthNetwork: "sapphire_devnet",
                        privateKeyProvider: privateKeyProvider,
                    });

                    await web3auth.initModal();
                    
                    if (web3auth.connected) {
                        await web3auth.logout();
                    }
                } catch (e) {
                    console.log('Web3Auth cleanup skipped:', e.message);
                }

                // Submit Laravel logout form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("logout") }}';
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                
                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
                
            } catch (error) {
                console.error('Logout error:', error);
                window.location.href = '/login';
            }
        });
    </script>
</body>
</html>