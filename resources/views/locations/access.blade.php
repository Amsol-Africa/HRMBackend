<x-app-layout>

    @include('clients._access_nav')

    <div class="row">
        <div class="col-md-10">
            @if (request()->routeIs('business.clients.request-access'))
                <div class="card">
                    <div class="card-body">
                        <form action="" id="requestAccessForm">
                            <div class="form-group mb-3">
                                <label for="email">Who do you request access from?</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address">
                            </div>
                            <div class="form-group mb-3">
                                <h6>Are they new to {{ config('app.name') }}?</h6>
                                <p>
                                    Choose a plan for your client who doesn't have an account and add it to this request.
                                </p>
                            </div>
                            <button type="button" onclick="requestAccess(this)" class="btn btn-primary"> <i class="bi bi-check-circle"></i> Send request</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <form action="" id="grantAccessForm">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="email">Who do you want to give access to?</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="modules">Managed services</label>
                                <div class="row" style="gap: 10px">
                                    @foreach($modules as $module)
                                        <div class="col-md-3 border rounded-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="module-{{ $module->id }}" name="modules[]" value="{{ $module->id }}">
                                                <label class="form-check-label" for="module-{{ $module->id }}">
                                                    {{ $module->name }}
                                                    <small class="text-muted">{{ $module->description }}</small>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <button type="button" onclick="grantAccess(this)" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Grant access
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/main/clients.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getClients()
            })
        </script>

    @endpush

</x-app-layout>
