// OvertimeService.js
class OvertimeService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/overtime/fetch', data);
            return response.data;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to fetch overtime data.', errors: [] } } };
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post('/overtime/store', data);
            Swal.fire('Congratulations!', response.message, 'success');
            this.handleRedirect(response.data?.redirect_url);
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to save overtime.', errors: [] } } };
        }
    }

    async update(data) {
        try {
            const response = await this.requestClient.post('/overtime/update', data);
            toastr.info(response.message, "Success");
            this.handleRedirect(response.data?.redirect_url);
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to update overtime.', errors: [] } } };
        }
    }

    async edit(data) {
        try {
            const response = await this.requestClient.post('/overtime/edit', data);
            return response.data;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to edit overtime.', errors: [] } } };
        }
    }

    async delete(data) {
        try {
            const response = await this.requestClient.post('/overtime/destroy', data);
            toastr.info(response.message, "Success");
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to delete overtime.', errors: [] } } };
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