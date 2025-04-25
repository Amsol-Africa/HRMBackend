class RoleService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/roles/fetch', data);
            return response.data;
        } catch (error) {
            console.error('Fetch error:', error);
            throw error;
        }
    }

    async update(data) {
        try {
            const response = await this.requestClient.post('/roles/update', data);
            toastr.success(response.message, "Success");
            return response.data;
        } catch (error) {
            console.error('Update error:', error);
            throw error;
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post('/roles/store', data);
            toastr.success(response.message, "Success");
            return response.data;
        } catch (error) {
            console.error('Save error:', error);
            throw error;
        }
    }

    async delete(data) {
        try {
            const response = await this.requestClient.post('/roles/destroy', data);
            toastr.success(response.message, "Success");
            return response.data;
        } catch (error) {
            console.error('Delete error:', error);
            throw error;
        }
    }

    async assign(data) {
        try {
            const response = await this.requestClient.post('/roles/assign', data);
            toastr.success(response.message, "Success");
            return response.data;
        } catch (error) {
            console.error('Assign error:', error);
            throw error;
        }
    }
}

export default RoleService;