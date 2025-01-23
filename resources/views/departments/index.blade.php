<x-app-layout>
    <div class="row g-20">

        <div class="col-md-4">
            <div class="card">
                <div class="card-body" id="departmentsFormContainer">
                    @include('departments._form')
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="row" id="departmentsContainer">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body"> {{ loader() }} </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/main/department.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getDepartments()
            })
        </script>

    @endpush

</x-app-layout>
