<x-app-layout>
    <div class="row g-20">
        <div class="col-md-10" id="employeesFormContainer">
            @include('employees._form')
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="{{ asset('js/main/employees.js') }}" type="module"></script>
    @endpush
</x-app-layout>
