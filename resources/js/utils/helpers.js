// Utility class for dynamically loading external JavaScript libraries
export class LibraryLoader {
    #libraries = [];
    #loadedScripts = new Set();

    constructor(libraries = []) {
        this.#libraries = libraries;
    }

    async loadScript(src) {
        if (this.#isScriptLoaded(src)) {
            return Promise.resolve();
        }

        return this.#createScriptElement(src);
    }

    #isScriptLoaded(src) {
        return (
            this.#loadedScripts.has(src) ||
            document.querySelector(`script[src="${src}"]`)
        );
    }

    #createScriptElement(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement("script");
            script.src = src;
            script.async = true;
            script.onload = () => this.#handleScriptLoad(src, resolve);
            script.onerror = () => this.#handleScriptError(src, reject);
            document.body.appendChild(script);
        });
    }

    #handleScriptLoad(src, resolve) {
        this.#loadedScripts.add(src);
        resolve();
    }

    #handleScriptError(src, reject) {
        reject(new Error(`Failed to load script: ${src}`));
    }

    async loadAll() {
        await Promise.all(this.#libraries.map((src) => this.loadScript(src)));
    }
}

// Manages UI state transitions for authentication flow
export class UIStateManager {
    #elements = {};

    constructor(elements) {
        this.#elements = elements;
    }

    show(elementKey) {
        this.#elements[elementKey]?.classList.remove("hidden");
    }

    hide(elementKey) {
        this.#elements[elementKey]?.classList.add("hidden");
    }

    enable(elementKey) {
        const element = this.#elements[elementKey];
        if (element) {
            element.disabled = false;
        }
    }

    disable(elementKey) {
        const element = this.#elements[elementKey];
        if (element) {
            element.disabled = true;
        }
    }

    showError(message, duration = 5000) {
        const errorBox = this.#elements.error;
        if (!errorBox) return;

        this.#setErrorMessage(message);
        this.show("error");

        if (duration > 0) {
            this.#scheduleErrorHide(duration);
        }
    }

    #setErrorMessage(message) {
        const errorMessage = document.getElementById("error-message");
        if (errorMessage) {
            errorMessage.textContent = message;
        } else {
            this.#elements.error.textContent = message;
        }
    }

    #scheduleErrorHide(duration) {
        setTimeout(() => this.hide("error"), duration);
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

// Get DOM element by ID
export function getElement(id) {
    return document.getElementById(id);
}

// Get content from meta tag
export function getMetaContent(name) {
    const meta = document.querySelector(`meta[name="${name}"]`);
    return meta?.content || null;
}

// Fetch JSON data with error handling
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
