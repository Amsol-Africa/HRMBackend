class JobApplicantService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/applicants/fetch', data);
            return response.data;
        } catch (error) {
            console.error('Fetch error:', error);
            throw error;
        }
    }

    async filter(data) {
        try {
            const response = await this.requestClient.post('/applicants/filter', data);
            return response.data;
        } catch (error) {
            console.error('Filter error:', error);
            throw error;
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post('/applicants/store', data);
            toastr.success(response.message, "Success");
            return response.data;
        } catch (error) {
            console.error('Save error:', error);
            throw error;
        }
    }

    async update(data) {
        try {
            const response = await this.requestClient.post('/applicants/update', data);
            toastr.info(response.message, "Success");
            return response.data;
        } catch (error) {
            console.error('Update error:', error);
            throw error;
        }
    }

    async edit(data) {
        try {
            const response = await this.requestClient.post('/applicants/edit', data);
            return response.data;
        } catch (error) {
            console.error('Edit error:', error);
            throw error;
        }
    }

    async delete(data) {
        try {
            const response = await this.requestClient.post('/applicants/destroy', data);
            toastr.info(response.message, "Success");
            return response.data;
        } catch (error) {
            console.error('Delete error:', error);
            throw error;
        }
    }

    async downloadDocument(data) {
        try {
            const response = await this.requestClient.post(
                '/applicants/download-document',
                data,
                { responseType: 'blob' }
            );
            return response;
        } catch (error) {
            console.error('Download error:', error);
            throw error;
        }
    }

    async export(data) {
        try {
            const response = await this.requestClient.post('/applicants/export', data, true);
            return response;
        } catch (error) {
            console.error('Export error:', error);
            throw error;
        }
    }
}

export default JobApplicantService;