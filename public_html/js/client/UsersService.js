class UsersService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/users/fetch', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async generateUsername(data) {
        try {
            const response = await this.requestClient.post('/users/username', data);
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async update(data) {
        try {
            const response = await this.requestClient.post('/users/update', data);
            toastr.info(response.message, "Success");
            return response.data;
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post('/users/store', data);
            toastr.success(response.message, "Success");
        } catch (error) {
            console.log(error)
            throw error;
        }
    }

    async delete(data) {
        try {
            const response = await this.requestClient.post('/users/delete', data);
            toastr.info(response.message, "Success");
        } catch (error) {
            console.log(error)
            throw error;
        }
    }
}

export default UsersService;
