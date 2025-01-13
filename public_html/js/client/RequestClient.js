import { BASE_URL } from './config.js';

class RequestClient {
    constructor() {
        this.baseURL = BASE_URL;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    getHeaders(hasFile = false) {
        const headers = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.csrfToken,
        };

        if (!hasFile) {
            headers['Content-Type'] = 'application/json';
        }

        return headers;
    }

    async request(method, endpoint, data = null) {
        const config = {
            method,
            headers: this.getHeaders(data instanceof FormData),
            credentials: 'include',
        };

        if (method !== 'GET' && !(data instanceof FormData)) {
            data._token = this.csrfToken;
            data = data || {};
            config.body = JSON.stringify(data);
        } else if (method !== 'GET' && data instanceof FormData) {
            data.append('_token', this.csrfToken);
            config.body = data;
        }

        try {
            const response = await fetch(`${this.baseURL}${endpoint}`, config);

            if (!response.ok) {
                const jsonResponse = await response.json();
                const status = response.status;
                const message = jsonResponse.message || 'Something went wrong';
                const errors = jsonResponse.errors || {};

                if (status === 400 || status === 422) {
                    if (errors) {
                        Object.values(errors).forEach(errorMessages => {
                            errorMessages.forEach(errorMessage => {
                                toastr.error(errorMessage);
                            });
                        });
                    } else {
                        toastr.error(message);
                    }
                } else if (status === 401) {
                    Swal.fire('Unauthorized', message, 'error');
                } else if (status === 403) {
                    Swal.fire('Forbidden', message, 'error');
                } else {
                    toastr.error('Something went wrong');
                }
                throw new Error(message);
            }

            return await response.json();

        } catch (error) {
            console.error('Request failed:', error.message);
            throw error;
        }
    }

    get(endpoint) {
        return this.request('GET', endpoint);
    }

    post(endpoint, data) {
        return this.request('POST', endpoint, data);
    }

    put(endpoint, data) {
        return this.request('PUT', endpoint, data);
    }

    delete(endpoint) {
        return this.request('DELETE', endpoint);
    }
}

export default RequestClient;
