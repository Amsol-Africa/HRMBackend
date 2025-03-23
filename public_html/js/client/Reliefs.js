class ReliefsService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/reliefs/fetch', data);
            return response.data;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to fetch reliefs.', errors: [] } } };
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post('/reliefs/store', data);
            Swal.fire('Congratulations!', response.message, 'success');
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to save relief.', errors: [] } } };
        }
    }

    async update(data) {
        try {
            const id = data.get('relief_id');
            const response = await this.requestClient.post(`/reliefs/${id}/update`, data);
            toastr.info(response.message, "Success");
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to update relief.', errors: [] } } };
        }
    }

    async edit(data) {
        try {
            const response = await this.requestClient.post('/reliefs/edit', data);
            return response.data;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to edit relief.', errors: [] } } };
        }
    }

    async show(data) {
        try {
            const response = await this.requestClient.post('/reliefs/show', data);
            return response.data;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to view relief.', errors: [] } } };
        }
    }

    async delete(data) {
        try {
            const id = data.relief_id;
            const response = await this.requestClient.post(`/reliefs/${id}/destroy`, data);
            toastr.info(response.message, "Success");
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to delete relief.', errors: [] } } };
        }
    }
}

export default ReliefsService;