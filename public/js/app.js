/* MedFlow - App JS */
// Axios is loaded via CDN in layouts
// Alpine.js is loaded via CDN in layouts

// Set up Axios defaults
if (window.axios) {
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    }
}