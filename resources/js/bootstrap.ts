import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// Interceptor untuk memproses error secara global (contohnya 401 unauthenticated)
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            // Opsional: Redirect ke halaman login lewat Vue Router atau window.location
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);
