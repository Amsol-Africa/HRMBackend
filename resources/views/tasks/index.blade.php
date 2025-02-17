<x-app-layout>
    <div class="row g-20">

        <div class="col-md-4">
            <div class="card">
                <div class="card-body" id="tasksFormContainer">
                    @include('tasks._form')
                </div>
            </div>
        </div>

        <div class="col-md-8 mt-3">
            <div class="row" id="tasksContainer">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body"> {{ loader() }} </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script src="{{ asset('js/main/tasks.js') }}" type="module"></script>
    <script>
    $(document).ready(() => {
        getTasks()
    })
    </script>
    @endpush

</x-app-layout>