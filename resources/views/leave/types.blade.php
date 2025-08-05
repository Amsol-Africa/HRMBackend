<x-app-layout>
    <div class="row g-20">

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="" method="post" id="leaveTypeForm">

                        <div class="form-group mb-3">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" placeholder="Leave Name"
                                class="form-control">
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Leave description"></textarea>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="requires_approval">Requires approval</label>
                                <select name="requires_approval" id="requires_approval" class="form-select">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="is_paid">Is paid</label>
                                <select name="is_paid" id="is_paid" class="form-select">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="allowance_accruable">Allowance accruable</label>
                                <select name="allowance_accruable" id="allowance_accruable" class="form-select">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="allows_half_day">Allows half day</label>
                                <select name="allows_half_day" id="allows_half_day" class="form-select">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="requires_attachment">Requires attachment</label>
                                <select name="requires_attachment" id="requires_attachment" class="form-select">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="max_continuous_days">Max continuous days</label>
                                <input type="number" name="max_continuous_days" id="max_continuous_days"
                                    class="form-control">
                            </div>

                            <div class="col-md-12">
                                <label for="min_notice_days">Min notice days</label>
                                <input type="number" name="min_notice_days" id="min_notice_days" class="form-control">
                            </div>
                        </div>

                        <h6 class="mt-3">Leave Policies</h6>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="department">Department</label>
                                <select name="department" id="department" class="form-select">
                                    <option value="all">All</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->slug }}"> {{ $department->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="job_category">Job categories</label>
                                <select name="job_category" id="job_category" class="form-select">
                                    <option value="all">All</option>
                                    @foreach ($job_categories as $job_category)
                                        <option value="{{ $job_category->slug }}"> {{ $job_category->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="gender_applicable">Gender applicable</label>
                                <select name="gender_applicable" id="gender_applicable" class="form-select">
                                    <option value="all">All</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="prorated_for_new_employees">Prorated for new employees</label>
                                <select name="prorated_for_new_employees" id="prorated_for_new_employees"
                                    class="form-select">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="default_days">Default days</label>
                                <input type="number" name="default_days" id="default_days" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="accrual_frequency">Accrual frequency</label>
                                <select name="accrual_frequency" id="accrual_frequency" class="form-select">
                                    <option value="monthly">{{ ucfirst('monthly') }}</option>
                                    <option value="quarterly">{{ ucfirst('quarterly') }}</option>
                                    <option value="yearly">{{ ucfirst('yearly') }}</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="accrual_amount">Accrual amount</label>
                                <input type="number" name="accrual_amount" id="accrual_amount"
                                    class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label for="max_carryover_days">Max carryover days</label>
                                <input type="number" name="max_carryover_days" id="max_carryover_days"
                                    class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label for="minimum_service_days_required">minimum service days required</label>
                                <input type="number" name="minimum_service_days_required"
                                    id="minimum_service_days_required" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label for="effective_date">Effective date</label>
                                <input type="text" class="form-control datepicker" id="effective_date"
                                    name="effective_date" required placeholder="Effective date">
                            </div>
                            <div class="col-md-6">
                                <label for="end_date">End date</label>
                                <input type="text" class="form-control datepicker" id="end_date" name="end_date"
                                    required placeholder="End date">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary w-100" onclick="saveLeaveType(this)">
                                    Save Leave Type </button>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div id="leaveTypeContainer">
                <div class="card">
                    <div class="card-body"> {{ loader() }} </div>
                </div>
            </div>
        </div>

    </div>
    @push('scripts')
        @include('modals.leave-type')
        <script src="{{ asset('js/main/leave-type.js') }}" type="module"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof getLeaveType === 'function') {
                    getLeaveType();
                }
                const input = document.getElementById('name');
                const availableTypes = @json(getLeaveTypeNames());

                if (typeof $ !== 'undefined' && $.fn.autocomplete) {
                    $('#name').autocomplete({
                        source: availableTypes,
                        minLength: 1,
                    });
                } else {
                    console.error('jQuery or jQuery UI is not loaded. Autocomplete will not work.');
                }
            });
        </script>
    @endpush

</x-app-layout>
