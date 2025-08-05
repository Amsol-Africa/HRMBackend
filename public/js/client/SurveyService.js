class SurveyService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(url, data) {
        try {
            const response = await this.requestClient.post(url, data);
            return response.data;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to fetch surveys.', errors: [] } } };
        }
    }

    async save(url, data) {
        try {
            const response = await this.requestClient.post(url, data);
            return response.data;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to save survey.', errors: [] } } };
        }
    }

    async delete(url, data) {
        try {
            const response = await this.requestClient.post(url, data);
            return response.data;
        } catch (error) {
            throw { response: error.response || { data: { message: 'Failed to delete survey.', errors: [] } } };
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

export default SurveyService;