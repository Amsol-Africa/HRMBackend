class AllowancesService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/allowances/fetch', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async update(data) {
        try {
            const response = await this.requestClient.post('/allowances/update', data);
            toastr.info(response.message, "Success");
            this.handleRedirect(response.data.redirect_url);
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async edit(data) {
        try {
            const response = await this.requestClient.post('/allowances/edit', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post('/allowances/store', data);
            toastr.success(response.message, "Success");
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async delete(data) {
        try {
            const response = await this.requestClient.post('/allowances/delete', data);
            toastr.info(response.message, "Success");
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

export default AllowancesService;
