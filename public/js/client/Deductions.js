class DeductionsService {
    constructor(requestClient) {
        this.requestClient = requestClient;
        this.resource = '/deductions';
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post(`${this.resource}/fetch`, data);
            return response.data;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to fetch deductions.', errors: [] } } };
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post(`${this.resource}/store`, data);
            Swal.fire('Congratulations!', response.message, 'success');
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to save deduction.', errors: [] } } };
        }
    }

    async edit(data) {
        try {
            const response = await this.requestClient.post(`${this.resource}/edit`, data);
            return response.data;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to edit deduction.', errors: [] } } };
        }
    }

    async update(data) {
        try {
            const id = data.get('deduction_id');
            const response = await this.requestClient.post(`${this.resource}/${id}/update`, data);
            toastr.info(response.message, "Success");
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to update deduction.', errors: [] } } };
        }
    }

    async delete(data) {
        try {
            const id = data.deduction_id;
            const response = await this.requestClient.post(`${this.resource}/${id}/destroy`, data);
            toastr.info(response.message, "Success");
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to delete deduction.', errors: [] } } };
        }
    }
}

export default DeductionsService;