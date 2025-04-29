<x-app-layout title="Create Survey for {{ $campaign->name }}">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-9 col-lg-10">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between text-dark">
                        <h5 class="mb-0">Create Survey for {{ $campaign->name }}</h5>
                        <a href="{{ route('business.crm.campaigns.view', ['business' => $currentBusiness->slug, 'campaign' => $campaign->id]) }}"
                            class="btn btn-secondary btn-sm">Back to Campaign</a>
                    </div>
                    <div class="card-body">
                        <form id="surveyCreatorForm"
                            data-action="{{ route('business.crm.surveys.store', ['business' => $currentBusiness->slug, 'campaign' => $campaign->id]) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Survey Fields</label>
                                <div id="fieldsContainer" class="border p-3 rounded">
                                    <!-- Default fields -->
                                    <div class="field-item mb-3 p-3 border rounded" data-id="name">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6>Name (Text)</h6>
                                            <button type="button" class="btn btn-sm btn-danger remove-field"
                                                disabled>Remove</button>
                                        </div>
                                        <div class="mt-2">
                                            <label class="form-label">Label</label>
                                            <input type="text" class="form-control" name="fields[name][label]"
                                                value="Name" readonly>
                                            <input type="hidden" name="fields[name][type]" value="text">
                                            <div class="form-check mt-2">
                                                <input type="checkbox" class="form-check-input"
                                                    name="fields[name][required]" value="1">
                                                <label class="form-check-label">Required</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field-item mb-3 p-3 border rounded" data-id="email">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6>Email (Text)</h6>
                                            <button type="button" class="btn btn-sm btn-danger remove-field"
                                                disabled>Remove</button>
                                        </div>
                                        <div class="mt-2">
                                            <label class="form-label">Label</label>
                                            <input type="text" class="form-control" name="fields[email][label]"
                                                value="Email" readonly>
                                            <input type="hidden" name="fields[email][type]" value="text">
                                            <div class="form-check mt-2">
                                                <input type="checkbox" class="form-check-input"
                                                    name="fields[email][required]" value="1" checked disabled>
                                                <label class="form-check-label">Required</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field-item mb-3 p-3 border rounded" data-id="country">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6>Country (Text)</h6>
                                            <button type="button" class="btn btn-sm btn-danger remove-field"
                                                disabled>Remove</button>
                                        </div>
                                        <div class="mt-2">
                                            <label class="form-label">Label</label>
                                            <input type="text" class="form-control" name="fields[country][label]"
                                                value="Country" readonly>
                                            <input type="hidden" name="fields[country][type]" value="text">
                                            <div class="form-check mt-2">
                                                <input type="checkbox" class="form-check-input"
                                                    name="fields[country][required]" value="1" checked disabled>
                                                <label class="form-check-label">Required</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label">Add New Field</label>
                                    <div class="input-group">
                                        <select class="form-select" id="newFieldType">
                                            <option value="text">Text</option>
                                            <option value="textarea">Textarea</option>
                                            <option value="star">Star Rating</option>
                                        </select>
                                        <button type="button" class="btn btn-outline-primary" id="addField">Add
                                            Field</button>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" id="submitButton">Save Survey</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const form = $('#surveyCreatorForm');
            const fieldsContainer = $('#fieldsContainer');
            let fieldCounter = 0;

            $('#addField').on('click', function() {
                const type = $('#newFieldType').val();
                const fieldId = `custom_${fieldCounter++}`;
                const fieldHtml = `
                    <div class="field-item mb-3 p-3 border rounded" data-id="${fieldId}">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>${type.charAt(0).toUpperCase() + type.slice(1)}</h6>
                            <button type="button" class="btn btn-sm btn-danger remove-field">Remove</button>
                        </div>
                        <div class="mt-2">
                            <label class="form-label">Label</label>
                            <input type="text" class="form-control" name="fields[${fieldId}][label]" required>
                            <input type="hidden" name="fields[${fieldId}][type]" value="${type}">
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" name="fields[${fieldId}][required]" value="1">
                                <label class="form-check-label">Required</label>
                            </div>
                        </div>
                    </div>`;
                fieldsContainer.append(fieldHtml);
            });

            fieldsContainer.on('click', '.remove-field', function() {
                $(this).closest('.field-item').remove();
            });

            form.on('submit', function(e) {
                e.preventDefault();
                const submitButton = $('#submitButton');
                submitButton.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: form.data('action'),
                    method: 'POST',
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        window.location.href = response.redirect_url;
                    },
                    error: function(xhr) {
                        submitButton.prop('disabled', false).text('Save Survey');
                        alert('Error saving survey: ' + (xhr.responseJSON?.message ||
                            'Unknown error'));
                    }
                });
            });
        });
    </script>