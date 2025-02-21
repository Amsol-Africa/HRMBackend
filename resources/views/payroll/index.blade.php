<x-app-layout>

    <div class="col-md-12">
        <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" onclick="getPayrolls()" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="true">{{ $currentBusiness->company_name }}</button>
            </li>
            @foreach ($locations as $location)
                <li class="nav-item" role="presentation">
                    <button class="nav-link" onclick="getPayrolls(1, '{{ $location->slug }}')" data-location="{{ $location->slug }}" id="on-{{ $location->slug }}-tab" data-bs-toggle="tab" data-bs-target="#on-{{ $location->slug }}" type="button" role="tab" aria-controls="on-{{ $location->slug }}" aria-selected="false">{{{ $location->name }}}</button>
                </li>
            @endforeach
        </ul>

        <div class="row g-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body" id="payrollsContainer">
                        {{ loader() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/main/payroll.js') }}" type="module"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                getPayrolls();
            });
        </script>

    @endpush

</x-app-layout>
