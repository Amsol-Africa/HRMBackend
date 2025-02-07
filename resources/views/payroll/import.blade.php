<x-app-layout>

    <div class="row mb-3">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="" id="importEmployeesForm">
                        <label for="csv_file" class="mb-2">Payrolls CSV File</label>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="file" name="csv_file" id="csv_file" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary w-100" onclick="importPayrolls(this)"> <i class="bi bi-cloud-upload"></i> Import Payrolls</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row align-items-center justify-content-center">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body mb-0 text-center">
                    <a href="#" class="btn btn-outline-info w-100">
                        <i class="bi bi-file-earmark-arrow-down me-2"></i> Download CSV Template
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body mb-0 text-center">
                    <a href="#" class="btn btn-outline-primary w-100">
                        <i class="bi bi-file-earmark-excel me-2"></i> Download XLSX Template
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/main/payrolls.js') }}" type="module"></script>
    @endpush

</x-app-layout>
