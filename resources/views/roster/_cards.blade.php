<div class="row g-3">
    @forelse ($rosters as $roster)
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h5 class="card-title text-primary">{{ $roster->name }}</h5>
                <p class="card-text">
                    <strong>Period:</strong> {{ $roster->start_date->format('Y-m-d') }} to
                    {{ $roster->end_date->format('Y-m-d') }}<br>
                    <strong>Status:</strong> <span
                        class="badge bg-{{ $roster->status === 'draft' ? 'secondary' : ($roster->status === 'published' ? 'success' : 'danger') }}">{{ ucfirst($roster->status) }}</span><br>
                    <strong>Assignments:</strong> {{ $roster->assignments->count() }}<br>
                    <strong>Overtime Hours:</strong> {{ number_format($roster->assignments->sum('overtime_hours'), 2) }}
                </p>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm edit-roster"
                        data-roster="{{ $roster->slug }}">Edit</button>
                    <button class="btn btn-outline-danger btn-sm delete-roster"
                        data-roster="{{ $roster->slug }}">Delete</button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <h5 class="text-muted">No rosters available at the moment.</h5>
            </div>
        </div>
    </div>
    @endforelse
</div>