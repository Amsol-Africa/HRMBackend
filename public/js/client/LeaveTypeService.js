class LeaveTypeService {
  constructor(requestClient) {
    this.requestClient = requestClient;
  }

  async fetch(data) {
    const res = await this.requestClient.post('/leave-types/fetch', data);
    return res.data;
  }

  async edit(data) {
    // Always post FormData with `slug` so backend validator passes
    const fd = new FormData();
    const slug =
      data?.slug ??
      data?.leave ??
      data?.leave_type_slug;

    if (!slug) throw new Error('Missing leave type slug');

    fd.append('slug', slug);
    const res = await this.requestClient.post('/leave-types/edit', fd);
    return res.data; // HTML fragment
  }

  async show(data) {
    const res = await this.requestClient.post('/leave-types/show', data);
    return res.data;
  }

  async save(data) {
    const res = await this.requestClient.post('/leave-types/store', data);
    toastr.success(res.message || 'Saved', 'Success');
  }

  async update(data) {
    const res = await this.requestClient.post('/leave-types/update', data);
    toastr.info(res.message || 'Updated', 'Success');
  }

  async delete(data) {
    const res = await this.requestClient.post('/leave-types/delete', data);
    toastr.info(res.message || 'Deleted', 'Success');
  }
}

export default LeaveTypeService;
