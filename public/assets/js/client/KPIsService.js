class KPIsService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/kpis/fetch', data);
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }

    async update(data) {
        try {
            const response = await this.requestClient.post('/kpis/update', data);
            toastr.info(response.message, "Success");
            this.handleRedirect(response.data.redirect_url);
        } catch (error) {
            console.log(error);
            throw error;
        }
    }

    async edit(data) {
        try {
            const response = await this.requestClient.post('/kpis/edit', data);
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }

    async timelines(data) {
        try {
            const response = await this.requestClient.post('/kpis/timelines', data);
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post('/kpis/store', data);
            toastr.success(response.message, "Success");
        } catch (error) {
            console.log(error);
            throw error;
        }
    }

    async progress(data) {
        try {
            const response = await this.requestClient.post('/kpis/progress', data);
            toastr.success(response.message, "Success");
        } catch (error) {
            console.log(error);
            throw error;
        }
    }

    async delete(data) {
        try {
            const response = await this.requestClient.post('/kpis/destroy', data);
            toastr.info(response.message, "Success");
        } catch (error) {
            console.log(error);
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

export default KPIsService;
