class BusinessesService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async store(data) {
        try {
            const response = await this.requestClient.post('/businesses/store', data);
            Swal.fire('Congratulations..!', response.message, 'success');
            this.handleRedirect(response.data.redirect_url);
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async clients(data) {
        try {
            const response = await this.requestClient.post('/client-businesses/fetch', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async saveModules(data) {
        try {
            const response = await this.requestClient.post('/businesses/modules/store', data);
            Swal.fire('success..!', response.message, 'success').then(() => {
                this.handleRedirect(response.data.redirect_url);
            });
        } catch (error) {
            console.log(error)
            throw error;
        }
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
