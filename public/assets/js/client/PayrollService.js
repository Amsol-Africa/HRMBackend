class PayrollService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/payroll/fetch', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async slips(data) {
        try {
            const response = await this.requestClient.post('/payroll/slips', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async update(data) {
        try {
            const response = await this.requestClient.post('/payroll/update', data);
            toastr.info(response.message, "Success");
            this.handleRedirect(response.data.redirect_url);
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async edit(data) {
        try {
            const response = await this.requestClient.post('/payroll/edit', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async viewPayslip(data) {
        try {
            const response = await this.requestClient.post('/payroll/slips/show', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async emailPayslip(data) {
        try {
            const response = await this.requestClient.post('/payroll/slips/email', data);
            toastr.success(response.message, "Success");
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async downloadPayslip(data) {
        try {
            const blob = await this.requestClient.post('/payroll/slips/download', data, true);

            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `payslip_${data.payslip}.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);

        } catch (error) {
            console.log(error);
            throw error;
        }
    }


    async save(data) {
        try {
            const response = await this.requestClient.post('/payroll/store', data);
            toastr.success(response.message, "Success");
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async delete(data) {
        try {
            const response = await this.requestClient.post('/payroll/delete', data);
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

    handleRedirectToTab(route) {
        if (route) {
            setTimeout(() => {
                window.open(route, '_blank');
            }, 1500);
        }
    }
}

export default PayrollService;
