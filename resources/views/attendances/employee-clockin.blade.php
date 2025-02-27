<x-app-layout>
    <div class="row g-20">

        <div class="col-md-5">

            <div class="card__wrapper">
                <div class="employee__wrapper text-center">
                    <div class="employee__thumb_2 mb-15 overflow-hidden">
                        <a href=""><img src="{{ auth()->user()->getImageUrl() }}" alt="image"></a>
                    </div>
                    <div class="employee__content">
                        <div class="employee__meta mb-15">
                            <h4 class="mb-8"><a href="">{{ auth()->user()->name }}</a></h4>
                            <p>{{ formatStatus(session('active_role')) }}</p>
                        </div>
                        <div class="employee__btn">

                            <div class="row g-2">
                                <div class="col-md-6">
                                    <button type="button" class="btn w-100 btn-primary" onclick="clockIn(this)" data-employee="{{ auth()->user()->id }}">Check In</button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn w-100 btn-danger" onclick="clockOut(this)" data-employee="{{ auth()->user()->id }}">Check Out</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-7" id="clockinsContainer">

        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/main/attendances.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getClockins();
            });
        </script>
    @endpush

</x-app-layout>
