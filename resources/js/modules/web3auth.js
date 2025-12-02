import { LibraryLoader, getMetaContent, fetchJSON } from "../utils/helpers.js";

// External library CDN URLs
const EXTERNAL_LIBS = [
    "https://cdn.jsdelivr.net/npm/@web3auth/modal@9.4.0/dist/modal.umd.min.js",
    "https://cdn.jsdelivr.net/npm/@web3auth/ethereum-provider@9.4.0/dist/ethereumProvider.umd.min.js",
    "https://cdn.jsdelivr.net/npm/web3@4.0.3/dist/web3.min.js",
];

export class Web3AuthService {
    constructor() {
        this.web3auth = null;
        this.config = null;
        this.loader = new LibraryLoader(EXTERNAL_LIBS);
    }

    async initialize() {
        // Load external libraries
        await this.loader.loadAll();

        // Fetch configuration from backend
        this.config = await this.fetchConfig();

        // Initialize Web3Auth
        await this.initializeWeb3Auth();
    }

    async fetchConfig() {
        const configRoute = getMetaContent("config-route");
        if (!configRoute) {
            throw new Error("Configuration route not found");
        }

        const config = await fetchJSON(configRoute);

        return {
            clientId: config.clientId,
            network: config.network,
            chain: {
                chainNamespace: config.chain.chain_namespace,
                chainId: config.chain.chain_id,
                rpcTarget: config.chain.rpc_target,
                displayName: config.chain.display_name,
                blockExplorer: config.chain.block_explorer,
                ticker: config.chain.ticker,
                tickerName: config.chain.ticker_name,
            },
            ui: {
                appName: config.ui.app_name,
                mode: config.ui.mode,
                loginMethodsOrder: config.ui.login_methods_order,
                defaultLanguage: config.ui.default_language,
                logoLight: config.ui.logo_light,
            },
        };
    }

    async initializeWeb3Auth() {
        const privateKeyProvider =
            new window.EthereumProvider.EthereumPrivateKeyProvider({
                config: { chainConfig: this.config.chain },
            });

        this.web3auth = new window.Modal.Web3Auth({
            clientId: this.config.clientId,
            web3AuthNetwork: this.config.network,
            privateKeyProvider,
            uiConfig: this.config.ui,
        });

        await this.web3auth.initModal();
    }

    async ensureFreshSession() {
        if (this.web3auth.connected) {
            await this.web3auth.logout();
        }
    }

    async connect() {
        await this.web3auth.connect({
            loginProvider: "google",
            extraLoginOptions: { prompt: "select_account" },
        });
    }

    async getUserInfo() {
        return await this.web3auth.getUserInfo();
    }

    async getWalletAddress() {
        const web3 = new Web3(this.web3auth.provider);
        const accounts = await web3.eth.getAccounts();
        return accounts[0];
    }

    async logout() {
        if (this.web3auth?.connected) {
            await this.web3auth.logout();
        }
    }
}
