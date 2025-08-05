<x-app-layout>
    <div class="row g-20">
        <h1 class="mb-3">Job Categories</h1>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" id="card-header">Create a job category</h5>
                </div>
                <div class="card-body" id="jobCategoriesFormContainer">
                    @include('job-categories._form')
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card-body p-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-2">Job Categories</h5>
                </div>
                <div class="table-responsive" id="jobCategoriesContainer">
                    <div class="text-center py-4">{{ loader() }}</div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/main/job-categories.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getJobCategories()
            })
        </script>
    @endpush

</x-app-layout>
