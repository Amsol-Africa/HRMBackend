 class TasksService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        try {
            const response = await this.requestClient.post('/tasks/fetch', data);
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }

    async update(data) {
        try {
            const response = await this.requestClient.post('/tasks/update', data);
            toastr.info(response.message, "Success");
            this.handleRedirect(response.data.redirect_url);
        } catch (error) {
            console.log(error);
            throw error;
        }
    }

    async edit(data) {
        try {
            const response = await this.requestClient.post('/tasks/edit', data);
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }

    async timelines(data) {
        try {
            const response = await this.requestClient.post('/tasks/timelines', data);
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }

    async save(data) {
        try {
            const response = await this.requestClient.post('/tasks/store', data);
            toastr.success(response.message, "Success");
        } catch (error) {
            console.log(error);
            throw error;
        }
    }

    async progress(data) {
        try {
            const response = await this.requestClient.post('/tasks/progress', data);
            toastr.success(response.message, "Success");
        } catch (error) {
            console.log(error);
            throw error;
        }
    }

    async delete(data) {
        try {
            const response = await this.requestClient.post('/tasks/destroy', data);
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

export default TasksService;
