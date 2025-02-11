<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="base-url" content="{{ config('app.url') }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="stylesheet" href="{{ asset('assets/css/vendor/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/vendor/animate.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/apexcharts.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/jquery-jvectormap-2.0.5.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/swiper-bundle.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/vendor/magnific-popup.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/vendor/icomoon.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/vendor/fontawesome-pro.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/vendor/rating.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/vendor/dropzone.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/dropify.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/vendor/spacing.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/datatables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/buttons.bootstrap5.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/jquery.dataTables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/jquery.timepicker.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/tagify.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/flatpickr.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/jquery-ui.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/fullcalendar.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/ion.rangeSlider.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/simplebar.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/waves.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/nano.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">


        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <link rel="stylesheet" href="https://cdn.datatables.net/v/dt/dt-2.0.8/datatables.min.css"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.default.min.css"/>

    </head>

    <body class="body-area">

        <div class="preloader" id="preloader">
            <div class="loading">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <div class="page__full-wrapper">

            @if (auth()->user()->hasRole('business-admin'))
                @include('layouts.partials.navbar')
            @elseif (auth()->user()->hasRole('business-hr'))
                @include('layouts.partials.navbar')
            @elseif (auth()->user()->hasRole('business-finance'))
                @include('layouts.partials.navbar')
            @elseif (auth()->user()->hasRole('business-employee'))
                @include('layouts.partials.navbar-employee')
            @endif

            <div class="page__body-wrapper">

                @include('layouts.partials.app-header')

                <div class="app__slide-wrapper">
                    {{ $slot }}
                </div>

                @include('layouts.partials.app-footer')

            </div>

        </div>


        <script src="{{ asset('assets/js/vendor/jquery-3.7.0.js') }}"></script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/moment/moment.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
        <script src="https://cdn.datatables.net/v/dt/dt-2.0.8/datatables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.tiny.cloud/1/3kfd12d1nvownxb4sd23epa9oexib4c3nzwidt83yg1m4xew/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

        <script src="{{ asset('assets/js/vendor/isotope.pkgd.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/magnific-popup.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/ajax-form.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/jquery.repeater.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/waypoints.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/dayjs.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/loader.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/jsvectormap.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/world-merc.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/swiper-bundle.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/simplebar-active.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/backtotop.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/smooth-scrollbar.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/cleave.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/jszip.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/pdfmake.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/vfs_fonts.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/buttons.print.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/buttons.colVis.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/steps-form.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/dropify.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/dropzone.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/tinymce.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/custom.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/typeahead.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/bloodhound.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/select2.full.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/jquery.timepicker.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/flatpickr.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/tagify.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/jquery-ui.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/fullcalendar.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/ion.rangeSlider.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/custom-tagify.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/height-equal.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/custom-chart.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/rangeslider-script.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/jquery.barrating.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/rating-script.js') }}"></script>
        <script src="{{ asset('assets/js/main.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/sidebar.js') }}"></script>
        <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>

        <script src="{{ asset('js/init.js') }}"></script>
        <script src="{{ asset('js/main/logout.js') }}" type="module"></script>

        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                const toggleButtons = document.querySelectorAll('[id^="passwordToggle"]');

                toggleButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const fieldId = this.id.replace('toggle', '').toLowerCase();
                        const passwordField = document.getElementById(fieldId);

                        if (passwordField) {
                            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                            passwordField.setAttribute('type', type);
                            this.innerHTML = type === 'password' ? '👁️' : '👁️‍🗨️';
                        }
                    });
                });
            });

            // Select all phone input fields
            const phoneInputFields = document.querySelectorAll(".phone-input-control");

            // Initialize each phone input with country code
            phoneInputFields.forEach((phoneInputField, index) => {
                initializePhoneInput(phoneInputField, index);

                console.log(index)

                phoneInputField.addEventListener("countrychange", function() {
                    const phoneInput = window.intlTelInputGlobals.getInstance(phoneInputField);
                    const selectedCountryData = phoneInput.getSelectedCountryData();
                    const codeField = document.querySelector(`#code${index}`);
                    const countryField = document.querySelector(`#country${index}`);

                    if (codeField) codeField.value = selectedCountryData.dialCode;
                    if (countryField) countryField.value = selectedCountryData.name;
                });
            });

            function initializePhoneInput(phoneInputField, index) {
                const phoneInput = window.intlTelInput(phoneInputField, {
                    preferredCountries: ["ke", "us", "ca"],
                    initialCountry: "auto",
                    nationalMode: true,
                    geoIpLookup: getIp,
                    separateDialCode: true,
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                });

                phoneInputField.addEventListener("countrychange", function() {
                    const selectedCountryData = phoneInput.getSelectedCountryData();
                    const codeField = document.querySelector(`#code${index}`);
                    const countryField = document.querySelector(`#country${index}`);

                    if (codeField) codeField.value = selectedCountryData.dialCode;
                    if (countryField) countryField.value = selectedCountryData.name;
                });
            }

            function getIp(callback) {
                fetch('https://ipinfo.io/json?token=a876c4d470b426', {
                    headers: {
                        'Accept': 'application/json'
                    }
                }).then((resp) => resp.json()).catch(() => {
                    return {
                        country: 'ke',
                    };
                }).then((resp) => callback(resp.country));
            }
        </script>

        @stack('scripts')

    </body>
</html>
