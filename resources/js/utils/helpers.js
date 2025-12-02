// Web3 Library Loader Utility
export class LibraryLoader {
    constructor(libraries = []) {
        this.libraries = libraries;
        this.loadedScripts = new Set();
    }

    async loadScript(src) {
        if (
            this.loadedScripts.has(src) ||
            document.querySelector(`script[src="${src}"]`)
        ) {
            return Promise.resolve();
        }

        return new Promise((resolve, reject) => {
            const script = document.createElement("script");
            script.src = src;
            script.async = true;
            script.onload = () => {
                this.loadedScripts.add(src);
                resolve();
            };
            script.onerror = () =>
                reject(new Error(`Failed to load script: ${src}`));
            document.body.appendChild(script);
        });
    }

    async loadAll() {
        await Promise.all(this.libraries.map((src) => this.loadScript(src)));
    }
}

// UI State Manager
export class UIStateManager {
    constructor(elements) {
        this.elements = elements;
    }

    show(elementKey) {
        this.elements[elementKey]?.classList.remove("hidden");
    }

    hide(elementKey) {
        this.elements[elementKey]?.classList.add("hidden");
    }

    enable(elementKey) {
        if (this.elements[elementKey]) {
            this.elements[elementKey].disabled = false;
        }
    }

    disable(elementKey) {
        if (this.elements[elementKey]) {
            this.elements[elementKey].disabled = true;
        }
    }

    showError(message, duration = 5000) {
        const errorBox = this.elements.error;
        if (!errorBox) return;

        errorBox.textContent = message;
        this.show("error");

        if (duration > 0) {
            setTimeout(() => this.hide("error"), duration);
        }
    }

    setLoadingState() {
        this.show("loading");
        this.hide("error");
        this.hide("buttonContainer");
    }

    setReadyState() {
        this.hide("loading");
        this.show("buttonContainer");
        this.enable("authBtn");
    }

    setInitState() {
        this.disable("authBtn");
    }
}

// DOM Helper
export function getElement(id) {
    return document.getElementById(id);
}

// Meta tag helper
export function getMetaContent(name) {
    const meta = document.querySelector(`meta[name="${name}"]`);
    return meta?.content || null;
}

// Fetch helper with error handling
export async function fetchJSON(url, options = {}) {
    const response = await fetch(url, {
        method: options.method || "GET",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            ...options.headers,
        },
        body: options.body,
        credentials: "same-origin",
    });

    const data = await response.json();

    if (!response.ok) {
        throw new Error(data.message || "Request failed");
    }

    return data;
}
