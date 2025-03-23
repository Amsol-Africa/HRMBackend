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
        Swal.fire('Success!', response.message, 'success');
        return response;
    }

    async filter(data) {
        try {
            const response = await this.requestClient.post('/employees/filter', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async update(data) {
        const id = data.get('employee_id');
        const response = await this.requestClient.post(`/employees/${id}/update`, data);
        toastr.info(response.message, "Success");
        return response;
    }

    async edit(data) {
        const response = await this.requestClient.post('/employees/edit', data);
        return response.data;
    }

    async delete(data) {
        const id = data.employee_id;
        const response = await this.requestClient.post(`/employees/${id}/destroy`, data);
        toastr.info(response.message, "Success");
        return response;
    }

    async view(data) {
        const response = await this.requestClient.post('/employees/view', data);
        return response.data;
    }
}

export default EmployeesService;