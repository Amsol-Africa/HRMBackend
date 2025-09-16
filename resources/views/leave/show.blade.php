<x-app-layout>
    @php
        /** @var \App\Models\LeaveRequest $leave */
        $statusName = !is_null($leave->rejection_reason)
            ? 'rejected'
            : (!is_null($leave->approved_by) ? 'approved' : 'pending');

        $statusColors = [
            'pending'  => ['icon' => 'fa fa-clock',         'color' => '#ffc107', 'label' => 'Pending'],
            'approved' => ['icon' => 'fa fa-check-circle',  'color' => '#28a745', 'label' => 'Approved'],
            'rejected' => ['icon' => 'fa fa-times-circle',  'color' => '#dc3545', 'label' => 'Rejected'],
        ];

        $isOwner    = optional(auth()->user()->employee)->id === (int) $leave->employee_id;

        // Active role awareness (employee should never see approver buttons)
        $activeRole     = session('active_role');
        $isApproverRole = in_array($activeRole, ['head-of-department','business-hr','business-admin','business-head'], true);

        // Ask the model; it already encodes the approver logic
        $canApprove = $isApproverRole && method_exists($leave, 'canUserApprove')
            ? $leave->canUserApprove(auth()->user())
            : false;

        $levelsTotal   = (int) optional($leave->leaveType)->approval_levels ?: 0;
        $levelsCurrent = (int) ($leave->current_approval_level ?? 0);
        $progressPct   = $levelsTotal > 0 ? min(100, round(($levelsCurrent / $levelsTotal) * 100)) : ($statusName === 'approved' ? 100 : 0);

        // Timeline
        $timeline = [];
        $timeline[] = [
            'name'       => 'submitted',
            'title'      => 'Leave Submitted',
            'at'         => optional($leave->created_at)->format('Y-m-d H:i'),
            'reason'     => null,
            'icon'       => 'fa fa-paper-plane',
            'colorClass' => 'text-primary',
        ];

        foreach ((array) ($leave->approval_history ?? []) as $hist) {
            $timeline[] = [
                'name'       => 'approval_level_' . ($hist['level'] ?? '?'),
                'title'      => 'Approval Level ' . ($hist['level'] ?? '?'),
                'at'         => isset($hist['approved_at']) ? \Carbon\Carbon::parse($hist['approved_at'])->format('Y-m-d H:i') : null,
                'reason'     => null,
                'icon'       => 'fa fa-check',
                'colorClass' => 'text-success',
                'meta'       => 'Approver ID: ' . ($hist['approver_id'] ?? '—'),
            ];
        }

        if ($statusName === 'rejected') {
            $timeline[] = [
                'name'       => 'rejected',
                'title'      => 'Rejected',
                'at'         => optional($leave->updated_at)->format('Y-m-d H:i'),
                'reason'     => $leave->rejection_reason,
                'icon'       => 'fa fa-times-circle',
                'colorClass' => 'text-danger',
            ];
        } elseif ($statusName === 'approved') {
            $timeline[] = [
                'name'       => 'approved',
                'title'      => 'Approved',
                'at'         => optional($leave->approved_at)->format('Y-m-d H:i'),
                'reason'     => null,
                'icon'       => 'fa fa-check-circle',
                'colorClass' => 'text-success',
            ];
        }
    @endphp

    <div class="row g-3">
        {{-- Approve / Reject only for approver roles AND when model says you can approve --}}
        @if($isApproverRole && $canApprove && $statusName === 'pending')
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="mb-3">Manage Request</h6>
                        <div class="d-grid gap-2">
                            <button type="button"
                                    onclick="manageLeave(this)"
                                    data-action="approve"
                                    data-leave="{{ $leave->reference_number }}"
                                    class="btn btn-success">
                                <i class="fa-solid fa-check me-1"></i> Approve Leave
                            </button>

                            <button type="button"
                                    onclick="manageLeave(this)"
                                    data-action="reject"
                                    data-leave="{{ $leave->reference_number }}"
                                    class="btn btn-danger">
                                <i class="fa-solid fa-ban me-1"></i> Deny Leave
                            </button>
                        </div>

                        @if($levelsTotal > 0)
                            <div class="mt-4">
                                <small class="text-muted">Approval Progress</small>
                                <div class="progress" style="height:10px;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ $progressPct }}%;"
                                         aria-valuenow="{{ $progressPct }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small>Level {{ $levelsCurrent }} / {{ $levelsTotal }}</small>
                                    <small>{{ $progressPct }}%</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Main details --}}
        <div class="{{ ($isApproverRole && $canApprove && $statusName === 'pending') ? 'col-md-8' : 'col-md-12' }}">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <div>
                            <h5 class="mb-1">Leave Request #{{ $leave->reference_number }}</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge"
                                      style="background-color: {{ $statusColors[$statusName]['color'] ?? '#6c757d' }}">
                                    <i class="{{ $statusColors[$statusName]['icon'] ?? 'fa fa-info-circle' }} me-1"></i>
                                    {{ $statusColors[$statusName]['label'] ?? ucfirst($statusName) }}
                                </span>
                                @if($leave->is_tentative)
                                    <span class="badge bg-secondary">Tentative</span>
                                @endif
                                @if($leave->requires_documentation && !$leave->attachment)
                                    <span class="badge bg-warning text-dark">Documentation Required</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Created: {{ optional($leave->created_at)->format('Y-m-d H:i') }}</small>
                            <small class="text-muted d-block">Updated: {{ optional($leave->updated_at)->format('Y-m-d H:i') }}</small>
                        </div>
                    </div>

                    <hr>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="text-muted mb-3">Request Summary</h6>
                                <dl class="row mb-0">
                                    <dt class="col-5">Employee</dt>
                                    <dd class="col-7">{{ optional(optional($leave->employee)->user)->name ?? '—' }}</dd>

                                    <dt class="col-5">Leave Type</dt>
                                    <dd class="col-7">{{ optional($leave->leaveType)->name ?? '—' }}</dd>

                                    <dt class="col-5">Start Date</dt>
                                    <dd class="col-7">{{ optional($leave->start_date)->format('Y-m-d') }}</dd>

                                    <dt class="col-5">End Date</dt>
                                    <dd class="col-7">{{ optional($leave->end_date)->format('Y-m-d') }}</dd>

                                    <dt class="col-5">Total Days</dt>
                                    <dd class="col-7">{{ number_format((float)$leave->total_days, 2) }}</dd>

                                    <dt class="col-5">Half Day</dt>
                                    <dd class="col-7">
                                        @if($leave->half_day)
                                            Yes ({{ $leave->half_day_type ? ucfirst($leave->half_day_type) : 'N/A' }})
                                        @else
                                            No
                                        @endif
                                    </dd>

                                    <dt class="col-5">Reason</dt>
                                    <dd class="col-7">{{ $leave->reason ?? '—' }}</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="text-muted mb-3">Attachments & Docs</h6>

                                @if($leave->attachment)
                                    <div class="mb-2">
                                        <a class="btn btn-outline-primary btn-sm"
                                           href="{{ asset('storage/' . $leave->attachment) }}"
                                           target="_blank" download>
                                            <i class="fa-solid fa-download me-1"></i> Download Attachment
                                        </a>
                                    </div>
                                @else
                                    <p class="text-muted mb-2">No attachment uploaded.</p>
                                @endif

                                {{-- Upload later (owner only) --}}
                                @if($isOwner && $leave->requires_documentation && !$leave->attachment && $statusName !== 'rejected')
                                    <form action="{{ route('leave.upload-document') }}"
                                          method="post" enctype="multipart/form-data" class="mt-3">
                                        @csrf
                                        <input type="hidden" name="reference_number" value="{{ $leave->reference_number }}">
                                        <div class="mb-2">
                                            <label class="form-label">Upload Required Document</label>
                                            <input class="form-control" type="file" name="attachment"
                                                   accept=".pdf,.jpg,.png,.doc,.docx" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa-solid fa-upload me-1"></i> Upload
                                        </button>
                                    </form>
                                @endif

                                @if($leave->approved_by && $leave->approved_at)
                                    <hr>
                                    <div>
                                        <small class="text-muted d-block">Final Approval</small>
                                        <small>By User ID: {{ $leave->approved_by }} at {{ $leave->approved_at->format('Y-m-d H:i') }}</small>
                                    </div>
                                @endif

                                @if($statusName === 'rejected' && $leave->rejection_reason)
                                    <hr>
                                    <div>
                                        <small class="text-muted d-block">Rejection Reason</small>
                                        <div class="text-danger">{{ $leave->rejection_reason }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($levelsTotal > 0)
                        <div class="mt-3">
                            <small class="text-muted d-block mb-1">
                                Approval Progress ({{ $levelsCurrent }} / {{ $levelsTotal }})
                            </small>
                            <div class="progress" style="height:10px;">
                                <div class="progress-bar" role="progressbar"
                                     style="width: {{ $progressPct }}%;"
                                     aria-valuenow="{{ $progressPct }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Timeline</h6>
                </div>
                <div class="card-body">
                    @forelse($timeline as $item)
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3">
                                <i class="{{ $item['icon'] }} {{ $item['colorClass'] }} fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $item['title'] }}</div>
                                <small class="text-muted d-block">{{ $item['at'] ?? '—' }}</small>
                                @if(!empty($item['meta']))
                                    <small class="text-muted d-block">{{ $item['meta'] }}</small>
                                @endif
                                @if(!empty($item['reason']))
                                    <div class="mt-1">Reason: {{ $item['reason'] }}</div>
                                @endif
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr class="my-2">
                        @endif
                    @empty
                        <p class="text-muted mb-0">No timeline data available.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/main/leave.js') }}" type="module"></script>
    @endpush
</x-app-layout>
