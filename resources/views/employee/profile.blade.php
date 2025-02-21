<x-app-layout>

    @include('employee._form')

    @push('scripts')
        <script src="{{ asset('js/main/employee_profile.js') }}" type="module"></script>
    @endpush

</x-app-layout>
