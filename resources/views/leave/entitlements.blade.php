<x-app-layout>
    <div class="row g-20">
        <div class="col-md-12">
            <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                @foreach ($leave_periods as $leave_period)
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {{ $loop->first ? 'active' : '' }}"
                            id="{{ $leave_period->slug }}-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#{{ $leave_period->slug }}"
                            type="button"
                            role="tab"
                            aria-controls="{{ $leave_period->slug }}"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                            data-leave-period-slug="{{ $leave_period->slug }}"  {{-- Add data attribute --}}
                        >
                            {{ $leave_period->name }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $page }}</h5>
                    <a href=""{{ route('business.leave.entitlements.create', $currentBusiness->slug) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-square-dotted me-2"></i> Leave Entitlements
                    </a>
                </div>
                <div class="card-body" id="leaveEntitlementsContainer">
                    {{ loader() }}  {{-- Assuming loader() is a helper function --}}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/main/leave-entitlement.js') }}" type="module"></script>
        <script src="{{ asset('js/main/filter-employees.js') }}" type="module"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const firstTab = document.querySelector('#myTab .nav-link.active');
                if (firstTab) {
                    const firstLeavePeriodSlug = firstTab.dataset.leavePeriodSlug; // Get slug from data attribute
                    getLeaveEntitlements(1, firstLeavePeriodSlug);
                }
            });

             // Modify the click handler as well:
            document.getElementById('myTab').addEventListener('click', function(event) {
                const clickedTab = event.target.closest('.nav-link'); // Get the clicked tab
                if (clickedTab) {
                    const leavePeriodSlug = clickedTab.dataset.leavePeriodSlug;
                    getLeaveEntitlements(1, leavePeriodSlug);
                }
            });


        </script>
    @endpush
</x-app-layout>
