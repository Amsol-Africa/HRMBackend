class DestinationsService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/destinations/fetch', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async countries(data) {
        try {
            const response = await this.requestClient.post('/countries/fetch', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async editCountry(data) {
        try {
            const response = await this.requestClient.post('/countries/edit', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async saveCountry(data) {
        try {
            const response = await this.requestClient.post('/countries/store', data);
            toastr.success(response.message, "Success");
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async updateCountry(data) {
        try {
            const response = await this.requestClient.post('/countries/update', data);
            toastr.success(response.message, "Success");
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async deleteCountry(data) {
        try {
            const response = await this.requestClient.post('/countries/delete', data);
            toastr.info(response.message, "Success");
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async update(data) {
        try {
            const response = await this.requestClient.post('/destinations/update', data);
            toastr.info(response.message, "Success");
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post('/destinations/store', data);
            toastr.success(response.message, "Success");
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async delete(data) {
        try {
            const response = await this.requestClient.post('/destinations/delete', data);
            toastr.info(response.message, "Success");
        } catch (error) {
            console.log(error)
            throw error;
        }
    }
}

export default DestinationsService;
