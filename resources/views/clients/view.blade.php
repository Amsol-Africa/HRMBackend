<x-app-layout>
    <meta name="business-slug" content="{{ session('active_business_slug') }}">
    <div class="container py-5">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom">
                <h2 class="mb-0 fw-bold">{{ $clientBusiness->company_name }}</h2>
                <a href="{{ route('business.clients.index', [session('active_business_slug')]) }}"
                    class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
            <div class="card-body p-4">
                <!-- Alerts -->
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="row g-4">
                    <!-- Business Info -->
                    <div class="col-lg-6">
                        <div class="card h-100 border-0 rounded-3">
                            <div class="card-body p-4">
                                <h6 class="card-title fw-bold mb-3 text-primary">Business Information</h6>
                                <ul class="list-group list-group-flush bg-transparent">
                                    <li class="list-group-item bg-transparent px-0 py-2 border-bottom"><span
                                            class="fw-medium">Name:</span> <span
                                            class="float-end text-dark">{{ $clientBusiness->company_name }}</span></li>
                                    <li class="list-group-item bg-transparent px-0 py-2 border-bottom"><span
                                            class="fw-medium">Industry:</span> <span
                                            class="float-end text-dark">{{ ucfirst($clientBusiness->industry ?? 'N/A') }}</span>
                                    </li>
                                    <li class="list-group-item bg-transparent px-0 py-2 border-bottom"><span
                                            class="fw-medium">Phone:</span> <span
                                            class="float-end text-dark">{{ $clientBusiness->phone ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item bg-transparent px-0 py-2 border-bottom"><span
                                            class="fw-medium">Country:</span> <span
                                            class="float-end text-dark">{{ $clientBusiness->country ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item bg-transparent px-0 py-2 border-bottom"><span
                                            class="fw-medium">Company Size:</span> <span
                                            class="float-end text-dark">{{ $clientBusiness->company_size ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item bg-transparent px-0 py-2 border-bottom">
                                        <span class="fw-medium">Status:</span>
                                        <span class="float-end">
                                            @if (is_null($clientBusiness->verified))
                                            <span class="badge rounded-pill bg-secondary">Unknown</span>
                                            @elseif ($clientBusiness->verified)
                                            <span class="badge rounded-pill bg-success">Verified</span>
                                            @else
                                            <span class="badge rounded-pill bg-warning text-dark">Pending</span>
                                            @endif
                                        </span>
                                    </li>
                                    <li class="list-group-item bg-transparent px-0 py-2 border-bottom"><span
                                            class="fw-medium">Registration No:</span> <span
                                            class="float-end text-dark">{{ $clientBusiness->registration_no ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item bg-transparent px-0 py-2 border-bottom"><span
                                            class="fw-medium">Tax PIN:</span> <span
                                            class="float-end text-dark">{{ $clientBusiness->tax_pin_no ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item bg-transparent px-0 py-2 border-bottom"><span
                                            class="fw-medium">Business License:</span> <span
                                            class="float-end text-dark">{{ $clientBusiness->business_license_no ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item bg-transparent px-0 py-2"><span
                                            class="fw-medium">Physical Address:</span> <span
                                            class="float-end text-dark">{{ $clientBusiness->physical_address ?? 'N/A' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Creator Info & Documents -->
                    <div class="col-lg-6">
                        <div class="card h-100 border-0 rounded-3">
                            <div class="card-body p-4">
                                <h6 class="card-title fw-bold mb-3 text-primary">Creator Details</h6>
                                <ul class="list-group list-group-flush bg-transparent mb-4">
                                    <li class="list-group-item bg-transparent px-0 py-2 border-bottom"><span
                                            class="fw-medium">Name:</span> <span
                                            class="float-end text-dark">{{ $clientBusiness->user->name ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item bg-transparent px-0 py-2 border-bottom"><span
                                            class="fw-medium">Email:</span> <span
                                            class="float-end text-dark">{{ $clientBusiness->user->email ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item bg-transparent px-0 py-2 border-bottom"><span
                                            class="fw-medium">Phone:</span> <span
                                            class="float-end text-dark">{{ $clientBusiness->user->phone ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item bg-transparent px-0 py-2 border-bottom"><span
                                            class="fw-medium">Created:</span> <span
                                            class="float-end text-dark">{{ $clientBusiness->created_at->format('d M Y H:i') }}</span>
                                    </li>
                                    <li class="list-group-item bg-transparent px-0 py-2"><span
                                            class="fw-medium">Updated:</span> <span
                                            class="float-end text-dark">{{ $clientBusiness->updated_at->format('d M Y H:i') }}</span>
                                    </li>
                                </ul>

                                <h6 class="card-title fw-bold mb-3 text-primary">Documents</h6>
                                @if ($clientBusiness->media->isEmpty())
                                <p class="text-muted fst-italic">No documents uploaded</p>
                                @else
                                <ul class="list-group list-group-flush bg-transparent">
                                    @foreach ($clientBusiness->media as $media)
                                    <li
                                        class="list-group-item bg-transparent px-0 py-2 border-bottom d-flex align-items-center">
                                        <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                        <a href="{{ $media->getUrl() }}" target="_blank"
                                            class="text-decoration-none text-truncate">{{ $media->file_name }}</a>
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Section -->
                <div class="row g-4 mt-2">
                    <div class="col-12">
                        <div class="card border-0 rounded-3 mt-4">
                            <div class="card-body p-4">
                                <h6 class="card-title fw-bold mb-3 text-primary">Actions</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        @if (!$clientBusiness->verified)
                                        <button class="btn btn-success w-100 rounded-pill"
                                            onclick="verifyBusiness(this, '{{ $clientBusiness->slug }}')">
                                            <i class="bi bi-check-circle me-2"></i> Verify Business
                                        </button>
                                        @else
                                        <button class="btn btn-danger w-100 rounded-pill"
                                            onclick="deactivateBusiness(this, '{{ $clientBusiness->slug }}')">
                                            <i class="bi bi-x-circle me-2"></i> Deactivate Business
                                        </button>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-outline-info w-100 rounded-pill"
                                            onclick="impersonateBusiness('{{ $clientBusiness->slug }}')">
                                            <i class="bi bi-person-lines-fill me-2"></i> Impersonate Business
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modules Assignment -->
                <div class="card border-0 rounded-3 mt-4">
                    <div class="card-body p-4">
                        <h6 class="card-title fw-bold mb-3 text-primary">Assign Modules</h6>
                        <form id="modulesForm-{{ $clientBusiness->slug }}">
                            @csrf
                            <input type="hidden" name="business_slug" value="{{ $clientBusiness->slug }}">
                            <div class="row g-3">
                                @foreach ($modules as $module)
                                <div class="col-md-3 col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="modules[]"
                                            value="{{ $module->id }}" id="module-{{ $module->id }}"
                                            {{ $clientBusiness->modules->contains($module->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module-{{ $module->id }}">
                                            {{ $module->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" onclick="assignModules(this, '{{ $clientBusiness->slug }}')"
                                class="btn btn-primary rounded-pill mt-4">
                                <i class="bi bi-save me-2"></i> Save Modules
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="remarksModal-{{ $clientBusiness->slug }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow border-0 rounded-3">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Remarks</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <textarea class="form-control border rounded-3" id="remarks-{{ $clientBusiness->slug }}" rows="4"
                        placeholder="Enter remarks"></textarea>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary rounded-pill"
                        onclick="submitRemarks('{{ $clientBusiness->slug }}')">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/main/clients.js') }}" type="module"></script>
    @endpush
</x-app-layout>