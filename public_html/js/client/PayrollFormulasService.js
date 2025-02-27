class PayrollFormulasService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/payroll-formulas/fetch', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async create(data) {
        try {
            const response = await this.requestClient.post('/payroll-formulas/create', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async show(data) {
        try {
            const response = await this.requestClient.post('/payroll-formulas/show', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async update(data) {
        try {
            const response = await this.requestClient.post('/payroll-formulas/update', data);
            toastr.info(response.message, "Success");
            this.handleRedirect(response.data.redirect_url);
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async edit(data) {
        try {
            const response = await this.requestClient.post('/payroll-formulas/edit', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post('/payroll-formulas/store', data);
            toastr.success(response.message, "Success");
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async delete(data) {
        try {
            const response = await this.requestClient.post('/payroll-formulas/delete', data);
            toastr.info(response.message, "Success");
        } catch (error) {
            console.log(error)
            throw error;
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

export default PayrollFormulasService;
