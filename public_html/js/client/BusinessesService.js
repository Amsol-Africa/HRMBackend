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

    async update(data) {
        try {
            const response = await this.requestClient.post('/businesses/update', data);
            Swal.fire('Update Successful..!', response.message, 'success');
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

    async requestAccess(data) {
        try {
            const response = await this.requestClient.post('/client-businesses/request-access', data);
            Swal.fire('Success..!', response.message, 'success');
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async grantAccess(data) {
        try {
            const response = await this.requestClient.post('/client-businesses/grant-access', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async saveModules(data) {
        try {
            const response = await this.requestClient.post('/businesses/modules/store', data);
            Swal.fire('Success..!', response.message, 'success').then(() => {
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
