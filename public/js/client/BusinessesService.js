class BusinessesService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async post(url, data) {
        try {
            const response = await this.requestClient.post(url, data);
            return response.data;
        } catch (error) {
            console.error(`POST ${url} error:`, error);
            throw error;
        }
    }

    async store(data) {
        return this.post('/businesses/store', data);
    }

    async update(data) {
        return this.post('/businesses/update', data);
    }

    async clients(data) {
        return this.post(`/businesses/${window.currentBusinessSlug}/clients/fetch`, data);
    }

    async requestAccess(data) {
        return this.post(`/businesses/${window.currentBusinessSlug}/clients/request-access`, data);
    }

    async grantAccess(data) {
        return this.post(`/businesses/${window.currentBusinessSlug}/clients/grant-access`, data);
    }

    async saveModules(data) {
        return this.post('/businesses/modules/store', data);
    }

    async fetch(data) {
        return this.post('/businesses/fetch', data);
    }

    async delete(data) {
        return this.post('/businesses/destroy', data);
    }

    async assignModules(data) {
        return this.post(`/businesses/${window.currentBusinessSlug}/clients/${data.get('business_slug')}/modules/assign`, data);
    }

    handleRedirect(route) {
        if (route) {
            setTimeout(() => {
                window.location.href = route;
            }, 1500);
        }
    }
}

export default BusinessesService;