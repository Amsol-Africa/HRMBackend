class DownloadsService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/downloads/fetch', data);
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }

    async update(data) {
        try {
            const response = await this.requestClient.post('/downloads/update', data);
            toastr.info(response.message, "Success");
            this.handleRedirect(response.data.redirect_url);
        } catch (error) {
            console.log(error);
            throw error;
        }
    }

    async edit(data) {
        try {
            const response = await this.requestClient.post('/downloads/edit', data);
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }

    async download(data) {
        try {
            const blob = await this.requestClient.post('/downloads', data, true);

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

    async delete(data) {
        try {
            const response = await this.requestClient.post('/downloads/delete', data);
            toastr.info(response.message, "Success");
        } catch (error) {
            console.log(error);
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

export default DownloadsService;
