// Web3Auth Configuration
const WEB3_CONFIG = {
    clientId:
        "BFcEYcKaDaVLDOQXYPk1rpJHxJkxZa0oZsCf22YIoARnC-85o8hMZE3Kboy5V8vkcyMOws3STJQm5HfG01Da20Q",
    network: "sapphire_devnet",
    chain: {
        chainNamespace: "eip155",
        chainId: "0x12c",
        rpcTarget: "https://sepolia.era.zksync.dev",
        displayName: "ZKsync Sepolia Testnet",
        blockExplorer: "https://sepolia.explorer.zksync.io/",
        ticker: "ETH",
        tickerName: "Ethereum",
    },
    ui: {
        appName: "JagaBumi",
        mode: "light",
        loginMethodsOrder: ["google"],
        defaultLanguage: "en",
        logoLight: "https://web3auth.io/images/w3a-L-Favicon-1.svg",
    },
};

// External library CDN URLs
const EXTERNAL_LIBS = [
    "https://cdn.jsdelivr.net/npm/@web3auth/modal@9.4.0/dist/modal.umd.min.js",
    "https://cdn.jsdelivr.net/npm/@web3auth/ethereum-provider@9.4.0/dist/ethereumProvider.umd.min.js",
    "https://cdn.jsdelivr.net/npm/web3@4.0.3/dist/web3.min.js",
];

// UI State Constants
const UI_STATE = {
    INIT: "INIT",
    READY: "READY",
    LOADING: "LOADING",
};

// Manages Web3Auth integration with Google OAuth and ZKsync
class JagaBumiAuth {
    constructor() {
        this.web3auth = null;
        this.apiRoute = this.getMetaContent("auth-route");
        this.csrfToken = this.getMetaContent("csrf-token");

        // Cache DOM elements
        this.elements = {
            authBtn: this.getElement("auth-btn"),
            buttonContainer: this.getElement("auth-buttons"),
            loading: this.getElement("loading"),
            error: this.getElement("error-box"),
        };
    }

    // Get element by ID with error handling
    getElement(id) {
        return document.getElementById(id);
    }

    // Get meta tag content
    getMetaContent(name) {
        const meta = document.querySelector(`meta[name="${name}"]`);
        return meta?.content || null;
    }

    // Dynamically load external script
    loadScript(src) {
        return new Promise((resolve, reject) => {
            // Check if script already loaded
            if (document.querySelector(`script[src="${src}"]`)) {
                return resolve();
            }

            const script = document.createElement("script");
            script.src = src;
            script.async = true;
            script.onload = () => resolve();
            script.onerror = () =>
                reject(new Error(`Failed to load script: ${src}`));

            document.body.appendChild(script);
        });
    }

    // Toggle UI state
    toggleUI(state) {
        const { loading, error, buttonContainer, authBtn } = this.elements;

        const states = {
            [UI_STATE.LOADING]: () => {
                loading?.classList.remove("hidden");
                error?.classList.add("hidden");
                buttonContainer?.classList.add("hidden");
            },
            [UI_STATE.READY]: () => {
                loading?.classList.add("hidden");
                buttonContainer?.classList.remove("hidden");
                if (authBtn) authBtn.disabled = false;
            },
            [UI_STATE.INIT]: () => {
                if (authBtn) authBtn.disabled = true;
            },
        };

        states[state]?.();
    }

    // Initialize Web3Auth system
    async init() {
        try {
            this.toggleUI(UI_STATE.INIT);

            await this.loadExternalLibraries();
            await this.initializeWeb3Auth();
            this.attachEventListeners();

            this.toggleUI(UI_STATE.READY);
            console.log("JagaBumi Auth System Ready");
        } catch (error) {
            console.error("Initialization failed:", error);
            this.showError(
                "Failed to load security system. Please refresh the page."
            );
        }
    }

    // Load external libraries
    async loadExternalLibraries() {
        await Promise.all(EXTERNAL_LIBS.map((src) => this.loadScript(src)));
    }

    // Initialize Web3Auth instance
    async initializeWeb3Auth() {
        const privateKeyProvider =
            new window.EthereumProvider.EthereumPrivateKeyProvider({
                config: { chainConfig: WEB3_CONFIG.chain },
            });

        this.web3auth = new window.Modal.Web3Auth({
            clientId: WEB3_CONFIG.clientId,
            web3AuthNetwork: WEB3_CONFIG.network,
            privateKeyProvider,
            uiConfig: WEB3_CONFIG.ui,
        });

        await this.web3auth.initModal();
    }

    // Attach event listeners to UI elements
    attachEventListeners() {
        this.elements.authBtn?.addEventListener("click", () =>
            this.handleAuth()
        );
    }

    // Handle authentication flow
    async handleAuth() {
        console.log("Starting authentication...");
        this.toggleUI(UI_STATE.LOADING);

        try {
            await this.ensureFreshSession();
            await this.connectToWeb3Auth();

            const authData = await this.fetchAuthenticationData();
            await this.authenticateWithBackend(authData);
        } catch (error) {
            console.error("Authentication error:", error);
            this.handleAuthError(error);
        }
    }

    // Ensure fresh session by logging out if already connected
    async ensureFreshSession() {
        if (this.web3auth.connected) {
            console.log("Cleaning previous session...");
            await this.web3auth.logout();
        }
    }

    // Connect to Web3Auth with Google
    async connectToWeb3Auth() {
        const connectOptions = {
            loginProvider: "google",
            extraLoginOptions: {
                prompt: "select_account",
            },
        };

        await this.web3auth.connect(connectOptions);
    }

    // Fetch authentication data from Web3Auth
    async fetchAuthenticationData() {
        const userInfo = await this.web3auth.getUserInfo();
        const { idToken } = await this.web3auth.authenticateUser();
        const walletAddress = await this.getWalletAddress();

        console.log("Processing for:", userInfo.email);

        return { userInfo, idToken, walletAddress };
    }

    // Get wallet address from Web3 provider
    async getWalletAddress() {
        const web3 = new Web3(this.web3auth.provider);
        const accounts = await web3.eth.getAccounts();
        return accounts[0];
    }

    // Handle authentication errors
    handleAuthError(error) {
        const userMessage =
            error.message === "User closed the modal"
                ? "Login cancelled"
                : error.message || "Authentication failed. Please try again.";

        this.showError(userMessage);
        this.toggleUI(UI_STATE.READY);
    }

    // Authenticate with Laravel backend
    async authenticateWithBackend({ userInfo, idToken, walletAddress }) {
        console.log("Sending request to server...");

        try {
            const response = await fetch(this.apiRoute, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": this.csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    id_token: idToken,
                    wallet_address: walletAddress,
                    user_info: userInfo,
                }),
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || "Server error occurred");
            }

            console.log(`${data.message}`);
            this.redirectToDashboard(data.redirect);
        } catch (error) {
            console.error("Backend error:", error);
            throw error;
        }
    }

    // Redirect to dashboard
    redirectToDashboard(url) {
        window.location.href = url;
    }

    // Display error message to user
    showError(message) {
        const errorBox = this.elements.error;
        if (!errorBox) return;

        errorBox.textContent = message;
        errorBox.classList.remove("hidden");

        // Auto-hide error after 5 seconds
        setTimeout(() => {
            errorBox.classList.add("hidden");
        }, 5000);
    }
}

// Initialize authentication system when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    const auth = new JagaBumiAuth();
    auth.init().catch((error) => {
        console.error("Fatal error during initialization:", error);
    });
});
