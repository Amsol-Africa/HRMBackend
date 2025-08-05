class RostersService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/business/' + window.activeBusinessSlug + '/roster/fetch', data);
            return response.data;
        } catch (error) {
            console.error("Error fetching rosters:", error);
            throw error;
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post('/business/' + window.activeBusinessSlug + '/roster/store', data);
            return response;
        } catch (error) {
            console.error("Error saving roster:", error);
            throw error;
        }
    }

    async update(data) {
        try {
            const response = await this.requestClient.post('/business/' + window.activeBusinessSlug + '/roster/update', data);
            return response;
        } catch (error) {
            console.error("Error updating roster:", error);
            throw error;
        }
    }

    async edit(data) {
        try {
            const response = await this.requestClient.post('/business/' + window.activeBusinessSlug + '/roster/edit', data);
            return response.data;
        } catch (error) {
            console.error("Error editing roster:", error);
            throw error;
        }
    }

    async delete(data) {
        try {
            const response = await this.requestClient.post('/business/' + window.activeBusinessSlug + '/roster/delete', data);
            return response;
        } catch (error) {
            console.error("Error deleting roster:", error);
            throw error;
        }
    }

    async notify(data) {
        try {
            const response = await this.requestClient.post('/business/' + window.activeBusinessSlug + '/roster/notify', data);
            return response;
        } catch (error) {
            console.error("Error notifying assignments:", error);
            throw error;
        }
    }

    async generateReport(filters) {
        try {
            const response = await this.requestClient.post('/business/' + window.activeBusinessSlug + '/roster/reports', filters);
            return response.data;
        } catch (error) {
            console.error("Error generating report:", error);
            throw error;
        }
    }

    async export(filters) {
        try {
            const response = await this.requestClient.post('/business/' + window.activeBusinessSlug + '/roster/export', filters, { responseType: 'blob' });
            const url = window.URL.createObjectURL(new Blob([response]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `roster-report.${filters.format}`);
            document.body.appendChild(link);
            link.click();
            link.remove();
        } catch (error) {
            console.error("Error exporting data:", error);
            throw error;
        }
    }

    async fetchCalendarEvents(data) {
        try {
            const response = await this.requestClient.post('/business/' + window.activeBusinessSlug + '/roster/calendar', data);
            return response.data;
        } catch (error) {
            console.error("Error fetching calendar events:", error);
            throw error;
        }
    }
}

export default RostersService;