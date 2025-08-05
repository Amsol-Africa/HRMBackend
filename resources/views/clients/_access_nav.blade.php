<div class="card mb-3">
    <div class="card-body d-flex justify-content-between align-items-center mb-0">
        <h5 class="mb-0">Account Sharing</h5>
        <div>
            <a href="{{ route('business.clients.request-access', $currentBusiness->slug) }}"
               class="btn {{ request()->routeIs('business.clients.request-access') ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="bi bi-question-circle"></i> Request Access
            </a>
            <a href="{{ route('business.clients.grant-access', $currentBusiness->slug) }}"
               class="btn {{ request()->routeIs('business.clients.grant-access') ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="bi bi-check-circle"></i> Grant Access
            </a>
        </div>
    </div>
</div>
