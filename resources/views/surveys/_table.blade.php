<div id="surveysTable" class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
        <thead class="bg-light">
            <tr>
                <th scope="col" class="text-dark fw-semibold">Title</th>
                <th scope="col" class="text-dark fw-semibold">Status</th>
                <th scope="col" class="text-dark fw-semibold">Access Type</th>
                <th scope="col" class="text-dark fw-semibold">Start Date</th>
                <th scope="col" class="text-dark fw-semibold">End Date</th>
                <th scope="col" class="text-dark fw-semibold text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($surveys as $survey)
            <tr>
                <td>{{ $survey->title }}</td>
                <td>
                    <span
                        class="badge {{ $survey->status === 'active' ? 'bg-success' : ($survey->status === 'draft' ? 'bg-warning' : 'bg-danger') }}">
                        {{ ucfirst($survey->status) }}
                    </span>
                </td>
                <td>{{ ucfirst(str_replace('_', ' ', $survey->access_type)) }}</td>
                <td>{{ $survey->start_date ? $survey->start_date->format('Y-m-d') : 'N/A' }}</td>
                <td>{{ $survey->end_date ? $survey->end_date->format('Y-m-d') : 'N/A' }}</td>
                <td class="text-end">
                    <div class="btn-group" role="group">
                        <a href="{{ route('business.surveys.show', [$businessSlug, $survey->id]) }}"
                            class="btn btn-sm btn-outline-primary me-2">
                            <i class="fa fa-eye"></i> View
                        </a>
                        <a href="{{ route('business.surveys.edit', [$businessSlug, $survey->id]) }}"
                            class="btn btn-sm btn-outline-warning me-2">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('business.surveys.preview', [$businessSlug, $survey->id]) }}"
                            class="btn btn-sm btn-outline-info me-2">
                            <i class="fa fa-search"></i> Preview
                        </a>
                        <button class="btn btn-sm btn-outline-danger" data-survey-id="{{ $survey->id }}"
                            onclick="deleteSurvey(this)">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="fa fa-info-circle me-2"></i> No surveys defined yet.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('styles')
<style>
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .btn-group .btn {
        padding: 6px 12px;
    }

    @media (max-width: 576px) {
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .btn-group .btn {
            width: 100%;
            text-align: center;
        }
    }
</style>
@endpush