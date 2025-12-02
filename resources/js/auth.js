import { Web3AuthService } from "./modules/web3auth.js";
import {
    UIStateManager,
    getElement,
    getMetaContent,
    fetchJSON,
} from "./utils/helpers.js";

class AuthManager {
    constructor() {
        this.web3Service = new Web3AuthService();
        this.apiRoute = getMetaContent("auth-route");
        this.csrfToken = getMetaContent("csrf-token");

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
            this.attachEventListeners();
            this.uiManager.setReadyState();
        } catch (error) {
            this.uiManager.showError("Failed to load. Please refresh.", 0);
        }
    }

    attachEventListeners() {
        const authBtn = getElement("auth-btn");
        authBtn?.addEventListener("click", () => this.handleAuth());
    }

    async handleAuth() {
        this.uiManager.setLoadingState();

        try {
            await this.web3Service.ensureFreshSession();
            await this.web3Service.connect();

            const authData = await this.fetchAuthenticationData();
            await this.authenticateWithBackend(authData);
        } catch (error) {
            this.handleAuthError(error);
        }
    }

    async fetchAuthenticationData() {
        const userInfo = await this.web3Service.getUserInfo();
        const walletAddress = await this.web3Service.getWalletAddress();
        return { userInfo, walletAddress };
    }

    async authenticateWithBackend({ userInfo, walletAddress }) {
        const data = await fetchJSON(this.apiRoute, {
            method: "POST",
            headers: { "X-CSRF-TOKEN": this.csrfToken },
            body: JSON.stringify({
                wallet_address: walletAddress,
                user_info: userInfo,
            }),
        });

        window.location.href = data.redirect;
    }

    handleAuthError(error) {
        const message =
            error.message === "User closed the modal"
                ? "Login cancelled"
                : "Login failed. Please try again.";

        this.uiManager.showError(message);
        this.uiManager.setReadyState();
    }
}

// Initialize
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        new AuthManager().init();
    });
} else {
    new AuthManager().init();
}
