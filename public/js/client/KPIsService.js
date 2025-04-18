class KPIsService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/kpis/fetch', data);
            if (!response.success) throw new Error(response.message || 'Failed to fetch KPIs');
            return response;
        } catch (error) {
            console.error('Fetch KPIs error:', error);
            throw error;
        }
    }

    async fetchCards(data) {
        try {
            const response = await this.requestClient.post('/kpis/fetch-cards', data);
            if (!response.success) throw new Error(response.message || 'Failed to fetch KPI cards');
            return response;
        } catch (error) {
            console.error('Fetch KPI cards error:', error);
            throw error;
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post('/kpis/store', data);
            if (!response.success) throw new Error(response.message || 'Failed to save KPI');
            return response;
        } catch (error) {
            console.error('Save KPI error:', error);
            throw error;
        }
    }

    async edit(data) {
        try {
            const response = await this.requestClient.post('/kpis/edit', data);
            if (!response.success) throw new Error(response.message || 'Failed to load edit form');
            return response;
        } catch (error) {
            console.error('Edit KPI error:', error);
            throw error;
        }
    }

    async update(data) {
        try {
            const response = await this.requestClient.post('/kpis/update', data);
            if (!response.success) throw new Error(response.message || 'Failed to update KPI');
            return response;
        } catch (error) {
            console.error('Update KPI error:', error);
            throw error;
        }
    }

    async review(data) {
        try {
            const response = await this.requestClient.post('/kpis/review', data);
            if (!response.success) throw new Error(response.message || 'Failed to review KPI');
            return response;
        } catch (error) {
            console.error('Review KPI error:', error);
            throw error;
        }
    }

    async updateReview(data) {
        try {
            const response = await this.requestClient.post('/kpis/update-review', data);
            if (!response.success) throw new Error(response.message || 'Failed to update review');
            return response;
        } catch (error) {
            console.error('Update review error:', error);
            throw error;
        }
    }

    async deleteReview(data) {
        try {
            const response = await this.requestClient.post('/kpis/delete-review', data);
            if (!response.success) throw new Error(response.message || 'Failed to delete review');
            return response;
        } catch (error) {
            console.error('Delete review error:', error);
            throw error;
        }
    }

    async delete(data) {
        try {
            const response = await this.requestClient.post('/kpis/destroy', data);
            if (!response.success) throw new Error(response.message || 'Failed to delete KPI');
            return response;
        } catch (error) {
            console.error('Delete KPI error:', error);
            throw error;
        }
    }

    async results(data) {
        try {
            const response = await this.requestClient.post('/kpis/results', data);
            if (!response.success) throw new Error(response.message || 'Failed to load results');
            return response;
        } catch (error) {
            console.error('Results KPI error:', error);
            throw error;
        }
    }
}

export default KPIsService;