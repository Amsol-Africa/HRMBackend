class EmployeesService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        const response = await this.requestClient.post('/employees/fetch', data);
        return response.data;
    }

    async save(data) {
        const response = await this.requestClient.post('/employees/store', data);
        return response; // Return full response to access data (employee ID)
    }

    async filter(data) {
        try {
            const response = await this.requestClient.post('/employees/filter', data);
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }

    async update(data) {
        const id = data.get('employee_id');
        const response = await this.requestClient.post(`/employees/${id}/update`, data);
        return response;
    }

    async edit(data) {
        const response = await this.requestClient.post('/employees/edit', data);
        return response.data;
    }

    async delete(data) {
        const id = data.employee_id;
        const response = await this.requestClient.post(`/employees/${id}/destroy`, data);
        return response;
    }

    async view(data) {
        const response = await this.requestClient.post('/employees/view', data);
        return response.data;
    }

    async uploadDocument(employeeId, data) {
        const response = await this.requestClient.post(`/employees/${employeeId}/documents/upload`, data);
        return response;
    }

    async deleteDocument(employeeId, documentId) {
        const response = await this.requestClient.post(`/employees/${employeeId}/documents/${documentId}/delete`, {});
        return response;
    }
}

export default EmployeesService;