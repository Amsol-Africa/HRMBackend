@props(['task'])

<li class="timeline__item d-flex gap-10">
    <div class="timeline__icon"><span><i class="fa-solid fa-share"></i></span></div>
    <div class="timeline__content w-100">
        <div class="d-flex flex-wrap gap-10 align-items-center justify-content-between">
            <h5 class="small">{{ $progress->status()->name }}</h5>
            <span class="bd-badge bg-success">{{ date('jS Y H:i', strtotime($progress->status()->created_at)) }}</span>
        </div>
        <p>{{ $progress->status()->reason ?? 'NA' }}</p>
    </div>
</li>
