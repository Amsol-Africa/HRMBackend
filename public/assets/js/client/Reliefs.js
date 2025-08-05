class ReliefsService {
    constructor(requestClient) {
        this.requestClient = requestClient;
    }

    async fetch(data) {
        const response = await this.requestClient.post('/reliefs/fetch', data);
        return response.data;
    }

    async save(data) {
        const response = await this.requestClient.post('/reliefs/store', data);
        return response.data;
    }

    async update(data, slug) {
        const response = await this.requestClient.post(`/reliefs/${slug}/update`, data);
        return response.data;
    }

    async edit(data) {
        const response = await this.requestClient.post(`/reliefs/${data.slug || ''}/edit`, data);
        return response.data;
    }

    async show(data) {
        const response = await this.requestClient.post(`/reliefs/${data.slug}/show`, data);
        return response.data;
    }

    async delete(data) {
        const response = await this.requestClient.post(`/reliefs/${data.slug}/destroy`, data);
        return response.data;
    }
}

export default ReliefsService;