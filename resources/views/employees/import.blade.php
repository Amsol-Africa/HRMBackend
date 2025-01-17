<x-app-layout>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="" id="importEmployeesForm">
                        <label for="csv_file" class="mb-2">Employees CSV File</label>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="file" name="csv_file" id="csv_file" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary w-100" onclick="importEmployees(this)"> <i class="bi bi-cloud-upload"></i> Import Employees</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/main/employees.js') }}" type="module"></script>
    @endpush

</x-app-layout>
