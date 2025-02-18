class OvertimeService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/overtime/fetch', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post('/overtime/store', data);
            Swal.fire('Congratulations..!', response.message, 'success');
            this.handleRedirect(response.data.redirect_url);
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async update(data) {
        try {
            const response = await this.requestClient.post('/overtime/update', data);
            toastr.info(response.message, "Success");
            this.handleRedirect(response.data.redirect_url);
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async edit(data) {
        try {
            const response = await this.requestClient.post('/overtime/edit', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async delete(data) {
        try {
            const response = await this.requestClient.post('/overtime/delete', data);
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

export default OvertimeService;
