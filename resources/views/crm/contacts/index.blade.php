<x-app-layout title="Contact Submissions">
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between text-white">
                    <h5 class="mb-0">Contact Submissions</h5>
                    <a href="{{ route('business.crm.contacts.create', ['business' => $currentBusiness->slug]) }}"
                        class="btn btn-primary btn-sm">Add Contact</a>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="contactFilter" class="form-control"
                            placeholder="Filter by name, email, or message...">
                    </div>
                    <div id="contactsContainer">
                        {{ loader() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/main/crm-contacts.js') }}" type="module"></script>
    <script>
        $(document).ready(() => getContacts());
    </script>
    @endpush
</x-app-layout>