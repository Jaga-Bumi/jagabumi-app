import { Web3AuthService } from "./modules/web3auth.js";
import { getMetaContent } from "./utils/helpers.js";

class LogoutManager {
    constructor() {
        this.web3Service = new Web3AuthService();
        this.csrfToken = getMetaContent("csrf-token");
        this.logoutRoute = getMetaContent("logout-route");
    }

    async init() {
        const logoutBtn = document.getElementById("logout-btn");
        if (logoutBtn) {
            logoutBtn.addEventListener("click", () => this.handleLogout());
        }
    }

    async handleLogout() {
        const logoutBtn = document.getElementById("logout-btn");
        if (!logoutBtn) return;

        logoutBtn.disabled = true;
        logoutBtn.textContent = "Logging out...";

        try {
            // Try to cleanup Web3Auth session
            try {
                await this.web3Service.initialize();
                await this.web3Service.logout();
            } catch (e) {
                console.log("Web3Auth cleanup skipped:", e.message);
            }

            // Call Laravel logout endpoint
            const response = await fetch(this.logoutRoute, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": this.csrfToken,
                },
            });

            if (response.ok) {
                window.location.reload();
            } else {
                throw new Error("Logout failed");
            }
        } catch (error) {
            console.error("Logout error:", error);
            logoutBtn.disabled = false;
            logoutBtn.textContent = "Logout";
            alert("Logout gagal. Silakan coba lagi.");
        }
    }

    submitLogoutForm() {
        const form = document.createElement("form");
        form.method = "POST";
        form.action = this.logoutRoute;

        const csrfInput = document.createElement("input");
        csrfInput.type = "hidden";
        csrfInput.name = "_token";
        csrfInput.value = this.csrfToken;

        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Initialize when DOM is ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initLogout);
} else {
    initLogout();
}

function initLogout() {
    const logout = new LogoutManager();
    logout.init();
}
