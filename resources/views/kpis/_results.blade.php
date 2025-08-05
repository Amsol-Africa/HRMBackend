<div class="container">
    <h5 class="mb-3">{{ $kpi->name }} Results</h5>
    <p><strong>Assigned To:</strong>
        @if($kpi->employee_id)
        Employee: {{ $kpi->employee->user->name ?? 'N/A' }}
        @elseif($kpi->department_id)
        Department: {{ $kpi->department->name ?? 'N/A' }}
        @elseif($kpi->job_category_id)
        Job Category: {{ $kpi->jobCategory->name ?? 'N/A' }}
        @elseif($kpi->location_id)
        Location: {{ $kpi->location->name ?? 'N/A' }}
        @else
        Business: {{ $kpi->business->company_name ?? 'N/A' }}
        @endif
    </p>
    <p><strong>Model Type:</strong> {{ class_basename($kpi->model_type) }}</p>
    <p><strong>Target:</strong> {{ $kpi->target_value ? $kpi->target_value . ' ' . $kpi->comparison_operator : 'N/A' }}
    </p>

    @if($kpi->model_type === 'manual')
    <h6 class="mt-4">Submit/Update Review</h6>
    <form id="kpiReviewForm" method="POST" novalidate>
        @csrf
        <input type="hidden" name="kpi_id" value="{{ $kpi->id }}">
        <input type="hidden" name="review_id" value="{{ isset($review) && $review->id ? $review->id : '' }}">

        <div class="mb-3">
            <label for="rating_value" class="form-label">Rating Value <span class="text-danger">*</span></label>
            <input type="number" class="form-control form-control-sm @error('rating_value') is-invalid @enderror"
                id="rating_value" name="rating_value" placeholder="Enter rating (e.g., 85)" required
                value="{{ old('rating_value', isset($review) ? $review->result_value : '') }}">
            @error('rating_value')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="comment" class="form-label">Comment</label>
            <textarea class="form-control form-control-sm @error('comment') is-invalid @enderror" id="comment"
                name="comment" rows="3"
                placeholder="Optional comment">{{ old('comment', isset($review) ? $review->comment : '') }}</textarea>
            @error('comment')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-sm btn-modern" id="submitReviewBtn">
            {{ isset($review) && $review->id ? 'Update Review' : 'Submit Review' }}
        </button>
        <span id="submitLoading" class="ms-2" style="display: none;">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...
        </span>
    </form>
    @endif

    <h6 class="mt-4">Result History</h6>
    @if($kpi->results->isEmpty())
    <p class="text-muted">No results available.</p>
    @else
    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Value</th>
                    <th>Meets Target</th>
                    <th>Comment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kpi->results as $result)
                <tr>
                    <td>{{ $result->measured_at ? $result->measured_at : 'N/A' }}</td>
                    <td>{{ $result->result_value ?? 'N/A' }}</td>
                    <td>
                        @if($result->meets_target)
                        <span class="badge bg-success">Yes</span>
                        @else
                        <span class="badge bg-warning">No</span>
                        @endif
                    </td>
                    <td>{{ $result->comment ?? 'N/A' }}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger delete-review-btn"
                            data-review-id="{{ $result->id }}" data-kpi-id="{{ $kpi->id }}">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>