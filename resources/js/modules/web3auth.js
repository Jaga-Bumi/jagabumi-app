import { LibraryLoader, getMetaContent } from "../utils/helpers.js";

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

        // Get configuration from meta tags
        this.config = this.getConfig();

        // Initialize Web3Auth
        await this.initializeWeb3Auth();
    }

    getConfig() {
        return {
            clientId: getMetaContent("web3auth-client-id"),
            network: getMetaContent("web3auth-network") || "sapphire_devnet",
            chain: {
                chainNamespace: "eip155",
                chainId: "0x12C",
                rpcTarget: "https://sepolia.era.zksync.dev",
                displayName: "ZKsync Sepolia Testnet",
                blockExplorer: "https://sepolia.explorer.zksync.io",
                ticker: "ETH",
                tickerName: "Ethereum",
            },
            ui: {
                appName: "JagaBumi",
                mode: "light",
                loginMethodsOrder: ["google", "github"],
                defaultLanguage: "en",
                logoLight: "https://web3auth.io/images/web3authlog.png",
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
