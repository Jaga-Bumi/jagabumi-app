import { LibraryLoader, getMetaContent } from "../utils/helpers.js";

/**
 * External library CDN URLs for Web3Auth and Web3.js
 */
const EXTERNAL_LIBS = [
    "https://cdn.jsdelivr.net/npm/@web3auth/modal@9.4.0/dist/modal.umd.min.js",
    "https://cdn.jsdelivr.net/npm/@web3auth/ethereum-provider@9.4.0/dist/ethereumProvider.umd.min.js",
    "https://cdn.jsdelivr.net/npm/web3@4.0.3/dist/web3.min.js",
];

/**
 * Chain configuration constants
 */
const CHAIN_CONFIG = {
    chainNamespace: "eip155",
    chainId: "0x12C",
    rpcTarget: "https://sepolia.era.zksync.dev",
    displayName: "ZKsync Sepolia Testnet",
    blockExplorer: "https://sepolia.explorer.zksync.io",
    ticker: "ETH",
    tickerName: "Ethereum",
};

/**
 * UI configuration constants
 */
const UI_CONFIG = {
    appName: "JagaBumi",
    mode: "light",
    loginMethodsOrder: ["google", "github"],
    defaultLanguage: "en",
    logoLight: "https://web3auth.io/images/web3authlog.png",
};

/**
 * Service class for managing Web3Auth authentication
 */
export class Web3AuthService {
    #web3auth = null;
    #config = null;
    #loader = null;

    constructor() {
        this.#loader = new LibraryLoader(EXTERNAL_LIBS);
    }

    async initialize() {
        await this.#loadExternalLibraries();
        this.#loadConfiguration();
        await this.#initializeWeb3Auth();
    }

    async #loadExternalLibraries() {
        await this.#loader.loadAll();
    }

    #loadConfiguration() {
        this.#config = {
            clientId: getMetaContent("web3auth-client-id"),
            network: getMetaContent("web3auth-network") || "sapphire_devnet",
            chain: CHAIN_CONFIG,
            ui: UI_CONFIG,
        };
    }

    async #initializeWeb3Auth() {
        const privateKeyProvider = this.#createPrivateKeyProvider();
        this.#web3auth = this.#createWeb3AuthInstance(privateKeyProvider);
        await this.#web3auth.initModal();
    }

    #createPrivateKeyProvider() {
        return new window.EthereumProvider.EthereumPrivateKeyProvider({
            config: { chainConfig: this.#config.chain },
        });
    }

    #createWeb3AuthInstance(privateKeyProvider) {
        return new window.Modal.Web3Auth({
            clientId: this.#config.clientId,
            web3AuthNetwork: this.#config.network,
            privateKeyProvider,
            uiConfig: this.#config.ui,
        });
    }

    async ensureFreshSession() {
        if (this.#web3auth?.connected) {
            await this.#web3auth.logout();
        }
    }

    async connect() {
        await this.#web3auth.connect({
            loginProvider: "google",
            extraLoginOptions: { prompt: "select_account" },
        });
    }

    async getUserInfo() {
        return await this.#web3auth.getUserInfo();
    }

    async getWalletAddress() {
        const web3 = new Web3(this.#web3auth.provider);
        const accounts = await web3.eth.getAccounts();
        return accounts[0];
    }

    async logout() {
        if (this.#web3auth?.connected) {
            await this.#web3auth.logout();
        }
    }

    get isConnected() {
        return this.#web3auth?.connected || false;
    }
}
