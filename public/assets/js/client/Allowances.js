class AllowancesService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/allowances/fetch', data);
            return response.data;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to fetch allowances.', errors: [] } } };
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post('/allowances/store', data);
            Swal.fire('Congratulations!', response.message, 'success');
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to save allowance.', errors: [] } } };
        }
    }

    async update(data) {
        try {
            const id = data.get('allowance_id');
            const response = await this.requestClient.post(`/allowances/${id}/update`, data);
            toastr.info(response.message, "Success");
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to update allowance.', errors: [] } } };
        }
    }

    async edit(data) {
        try {
            const response = await this.requestClient.post('/allowances/edit', data);
            return response.data;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to edit allowance.', errors: [] } } };
        }
    }

    async delete(data) {
        try {
            const id = data.allowance_id;
            const response = await this.requestClient.post(`/allowances/${id}/destroy`, data);
            toastr.info(response.message, "Success");
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to delete allowance.', errors: [] } } };
        }
    }
}

export default AllowancesService;