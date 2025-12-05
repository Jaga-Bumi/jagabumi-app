import { Web3AuthService } from "./modules/web3auth.js";
import {
    UIStateManager,
    getElement,
    getMetaContent,
    fetchJSON,
} from "./utils/helpers.js";

// Manages user authentication flow using Web3Auth
class AuthManager {
    constructor() {
        this.#initializeServices();
        this.#initializeConfig();
        this.#initializeUIManager();
    }

    #initializeServices() {
        this.web3Service = new Web3AuthService();
    }

    #initializeConfig() {
        this.config = {
            apiRoute: getMetaContent("auth-route"),
            csrfToken: getMetaContent("csrf-token"),
        };
    }

    #initializeUIManager() {
        this.uiManager = new UIStateManager({
            authBtn: getElement("auth-btn"),
            buttonContainer: getElement("auth-buttons"),
            loading: getElement("loading"),
            error: getElement("error-box"),
        });
    }

    async init() {
        try {
            this.uiManager.setInitState();
            await this.web3Service.initialize();
            this.#attachEventListeners();
            this.uiManager.setReadyState();
        } catch (error) {
            this.#handleInitializationError(error);
        }
    }

    #attachEventListeners() {
        const authBtn = getElement("auth-btn");
        authBtn?.addEventListener("click", () => this.handleAuth());
    }

    async handleAuth() {
        this.uiManager.setLoadingState();

        try {
            await this.#performAuthentication();
        } catch (error) {
            this.#handleAuthError(error);
        }
    }

    async #performAuthentication() {
        await this.web3Service.ensureFreshSession();
        await this.web3Service.connect();

        const authData = await this.#fetchAuthenticationData();
        await this.#authenticateWithBackend(authData);
    }

    async #fetchAuthenticationData() {
        const [userInfo, walletAddress] = await Promise.all([
            this.web3Service.getUserInfo(),
            this.web3Service.getWalletAddress(),
        ]);

        return { userInfo, walletAddress };
    }

    async #authenticateWithBackend({ userInfo, walletAddress }) {
        const data = await fetchJSON(this.config.apiRoute, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": this.config.csrfToken,
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                wallet_address: walletAddress,
                user_info: userInfo,
            }),
        });

        this.#redirectToDestination(data.redirect);
    }

    #redirectToDestination(url) {
        window.location.href = url;
    }

    #handleInitializationError(error) {
        console.error("Initialization error:", error);
        this.uiManager.showError("Gagal memuat. Silakan refresh halaman.", 0);
    }

    #handleAuthError(error) {
        console.error("Authentication error:", error);

        const errorMessage = this.#getErrorMessage(error);
        this.uiManager.showError(errorMessage);
        this.uiManager.setReadyState();
    }

    #getErrorMessage(error) {
        const errorMessages = {
            "User closed the modal": "Login dibatalkan",
            default: "Login gagal. Silakan coba lagi.",
        };

        return errorMessages[error.message] || errorMessages.default;
    }
}

// Initialize authentication when DOM is ready
function initializeAuth() {
    new AuthManager().init();
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initializeAuth);
} else {
    initializeAuth();
}
