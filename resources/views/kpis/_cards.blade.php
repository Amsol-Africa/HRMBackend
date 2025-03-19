@foreach ($kpis as $kpi)
<div class="col-md-6 col-lg-4 fade-in">
    <div class="card border-0 shadow-sm h-100 transition-hover">
        <div class="card-body d-flex flex-column">
            <!-- Icon and Title -->
            <div class="d-flex align-items-center mb-3">
                <i class="{{ $kpi->getIconClass() }} fa-2x text-primary me-3"></i>
                <h5 class="card-title mb-0 fw-semibold">{{ $kpi->name }}</h5>
            </div>

            <!-- Description -->
            <p class="card-text text-muted small flex-grow-1">{{ $kpi->description }}</p>

            <!-- Target and Result -->
            <div class="mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-medium text-dark">Target:</span>
                    <span class="text-muted">{{ $kpi->comparison_operator }}
                        {{ number_format($kpi->target_value, 2) }}</span>
                </div>
                @if ($kpi->results->isNotEmpty())
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-medium text-dark">Latest:</span>
                    <span class="text-dark">{{ number_format($kpi->results->last()->result_value, 2) }}</span>
                </div>
                <!-- Progress Bar -->
                <div class="progress mt-2" style="height: 8px;">
                    <div class="progress-bar {{ $kpi->results->last()->meets_target ? 'bg-success' : 'bg-danger' }}"
                        role="progressbar" style="width: {{ $kpi->getProgressPercentage() }}%;"
                        aria-valuenow="{{ $kpi->getProgressPercentage() }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <span class="badge {{ $kpi->results->last()->meets_target ? 'bg-success' : 'bg-danger' }} mt-2">
                    {{ $kpi->results->last()->meets_target ? 'Meets Target' : 'Does Not Meet Target' }}
                </span>
                @else
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-medium text-dark">Latest:</span>
                    <span class="text-muted">No results yet</span>
                </div>
                <div class="progress mt-2" style="height: 8px;">
                    <div class="progress-bar bg-secondary" role="progressbar" style="width: 0%;" aria-valuenow="0"
                        aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach