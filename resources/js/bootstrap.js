// Global CSRF token setup for fetch requests
window.getCsrfToken = () => {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta?.content || "";
};
