class TrendsService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async payroll(data) {
        try {
            const response = await this.requestClient.post('/trends/payroll', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async attendance(data) {
        try {
            const response = await this.requestClient.post('/trends/attendance', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async leave(data) {
        try {
            const response = await this.requestClient.post('/trends/leave', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async loans(data) {
        try {
            const response = await this.requestClient.post('/trends/loans', data);
            return response.data;
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

export default TrendsService;
