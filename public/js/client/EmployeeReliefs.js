class EmployeeReliefsService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/employee-reliefs/fetch', data);
            return response.data;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to fetch employee reliefs.', errors: [] } } };
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post('/employee-reliefs/store', data);
            Swal.fire('Congratulations!', response.message, 'success');
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to save employee relief.', errors: [] } } };
        }
    }

    async update(data) {
        try {
            const id = data.get('employee_relief_id');
            const response = await this.requestClient.post(`/employee-reliefs/${id}/update`, data);
            toastr.info(response.message, "Success");
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to update employee relief.', errors: [] } } };
        }
    }

    async edit(data) {
        try {
            const response = await this.requestClient.post('/employee-reliefs/edit', data);
            return response.data;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to edit employee relief.', errors: [] } } };
        }
    }

    async delete(data) {
        try {
            const id = data.employee_relief_id;
            const response = await this.requestClient.post(`/employee-reliefs/${id}/destroy`, data);
            toastr.info(response.message, "Success");
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to delete employee relief.', errors: [] } } };
        }
    }
}

export default EmployeeReliefsService;