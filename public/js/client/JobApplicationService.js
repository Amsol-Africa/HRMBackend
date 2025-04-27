class JobApplicationService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/applications/fetch', data);
            return response.data;
        } catch (error) {
            throw new Error(error.response?.data?.message || 'Failed to fetch applications');
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post('/applications/store', data);
            return response.data;
        } catch (error) {
            throw new Error(error.response?.data?.message || 'Failed to save application');
        }
    }

    async update(data) {
        try {
            const response = await this.requestClient.post('/applications/update', data);
            return response.data;
        } catch (error) {
            throw new Error(error.response?.data?.message || 'Failed to update application');
        }
    }

    async edit(data) {
        try {
            const response = await this.requestClient.post('/applications/edit', data);
            return response.data;
        } catch (error) {
            throw new Error(error.response?.data?.message || 'Failed to edit application');
        }
    }

    async delete(data) {
        try {
            const response = await this.requestClient.post('/applications/destroy', data);
            return response.data;
        } catch (error) {
            throw new Error(error.response?.data?.message || 'Failed to delete applications');
        }
    }

    async updateStage(data) {
        try {
            const response = await this.requestClient.post('/applications/update-stage', data);
            return response.data;
        } catch (error) {
            throw new Error(error.response?.data?.message || 'Failed to update stage');
        }
    }

    async shortlist(data) {
        try {
            const response = await this.requestClient.post('/applications/shortlist', data);
            return response.data;
        } catch (error) {
            throw new Error(error.response?.data?.message || 'Failed to shortlist applications');
        }
    }

    async scheduleInterview(data) {
        try {
            const response = await this.requestClient.post('/applications/schedule-interview', data);
            return response.data;
        } catch (error) {
            throw new Error(error.response?.data?.message || 'Failed to schedule interview');
        }
    }

    async export(data) {
        try {
            const response = await this.requestClient.post('/applications/export', data, true, { responseType: 'blob' });
            return response;
        } catch (error) {
            throw new Error(error.response?.data?.message || 'Failed to export applications');
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
            console.error('Download error in service:', error);
            throw error;
        }
    }
}

export default JobApplicationService;