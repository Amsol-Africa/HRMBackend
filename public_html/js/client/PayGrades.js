// js/client/PayGrades.js
class PayGradesService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/pay-grades/fetch', data);
            return response.data;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to fetch pay grades.', errors: [] } } };
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post('/pay-grades/store', data);
            Swal.fire('Congratulations!', response.message, 'success');
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to save pay grade.', errors: [] } } };
        }
    }

    async update(data) {
        try {
            const id = data.get('pay_grade_id');
            const response = await this.requestClient.post(`/pay-grades/${id}/update`, data);
            toastr.info(response.message, "Success");
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to update pay grade.', errors: [] } } };
        }
    }

    async edit(data) {
        try {
            const response = await this.requestClient.post('/pay-grades/edit', data);
            return response.data;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to edit pay grade.', errors: [] } } };
        }
    }

    async delete(data) {
        try {
            const id = data.pay_grade_id;
            const response = await this.requestClient.post(`/pay-grades/${id}/destroy`, data);
            toastr.info(response.message, "Success");
            return response;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to delete pay grade.', errors: [] } } };
        }
    }

    handleRedirect(route) {
        if (route) {
            setTimeout(() => {
                window.location.href = route;
            }, 1500);
        }
    }
}

export default PayGradesService;