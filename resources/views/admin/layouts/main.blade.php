<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- <meta http-equiv="Content-Security-Policy" content="style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; "> -->
    <!-- <meta http-equiv="Content-Security-Policy" content="style-src 'self' 'unsafe-inline' https://vuejs.org; script-src 'self' 'unsafe-inline' 'unsafe-eval' <?php echo env('APP_URL'); ?> https://vuejs.org https://github.com; "> -->

    <title>@yield('title') - {{ config('app.name') }}</title>

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Vendor css here -->
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/snackbar.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/offline-theme-chrome.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/nprogress.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/offline-language-english.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/material-icon.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/daterangepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/datepicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/colorpicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/jquery.timepicker.min.css') }}"> -->

    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/snackbar.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/nprogress.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/material-icon.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/daterangepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/datepicker.css')}}">

    <!-- Bootstrap css-->
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />

    <!-- Icons css-->
    <link href="{{ asset('assets/plugins/web-fonts/icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/web-fonts/font-awesome/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/web-fonts/plugin.css') }}" rel="stylesheet" />

    <!-- InternalFileupload css-->
    <link href="{{asset('assets/plugins/fileuploads/css/fileupload.css') }}" rel="stylesheet" type="text/css" />

    <!-- InternalFancy uploader css-->
    <link href="{{asset('assets/plugins/fancyuploder/fancy_fileupload.css') }}" rel="stylesheet" />

    <!-- Style css-->
    <!-- <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/skins.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/dark-style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/colors/default.css') }}" rel="stylesheet">

    <!-- Color css-->
    <link id="theme" rel="stylesheet" type="text/css" media="all" href="{{ asset('assets/css/colors/color.css') }}">

    <!-- Select2 css-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!-- Mutipleselect css-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/multipleselect/multiple-select.css') }}">
    <!-- Sidemenu css-->
    <link href="{{ asset('assets/css/sidemenu/sidemenu.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

    <script>
    var base_url = "{{ route('welcome')}}";
    </script>
    <!-- vue components -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/vue-loading.css') }}">

    <!-- Custom css here -->
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/vendor/croppie.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/admin/font.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/admin/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/admin/dev.css') }}"> -->
    @stack('pageCss')
</head>

<body class="main-body leftmenu">

    <!-- Loader -->
    <!-- <div id="global-loader"> -->
    <!-- <img src="{{ asset('assets/img/loader.svg') }}" class="loader-img" alt="Loader"> -->
    <!-- <div class="vld-icon loader-img"><svg viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" width="64" height="64" stroke="#000"><g fill="none" fill-rule="evenodd"><g transform="translate(1 1)" stroke-width="2"><circle stroke-opacity=".25" cx="18" cy="18" r="18"></circle><path d="M36 18c0-9.94-8.06-18-18-18"><animateTransform attributeName="transform" type="rotate" from="0 18 18" to="360 18 18" dur="0.8s" repeatCount="indefinite"></animateTransform></path></g></g></svg></div> -->
    <!-- </div> -->
    <!-- End Loader -->

    <div class="page" id="app">

        @auth
        @include("admin.common.sidebar")
        @include('admin.common.header')
        @endauth

        @yield('content')

        @stack('pageModals')

        @auth
        <div class="main-footer text-center">
            <div class="container">
                <div class="row row-sm">
                    <div class="col-md-12">
                        <span>Copyright © <?= date("Y"); ?> <a href="javascript:void(0)">Pulpit</a>. Designed by <a
                                href="javascript:void(0)">Pulpit</a> All rights reserved.</span>
                    </div>
                </div>
            </div>
        </div>
        @endauth
    </div>

    @auth
    <div class="modal" tabindex="-1" role="dialog" id="modalLogoutConfirm">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ @trans('logout.confirm') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{ @trans('logout.confirmation') }}?</p>
                    <form id="frmLogout" action="{{ route('logout',['panel' => Session::get('panel')]) }}" method="get">
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn ripple btn-secondary"
                        data-dismiss="modal">{{ @trans('logout.cancel') }}</button>
                    <button type="submit" class="btn ripple btn-primary"
                        id="btnLogoutYes">{{ @trans('logout.logout') }}</button>
                </div>
            </div>
        </div>
    </div>
    @endauth

    <!-- Vendor JS here -->
    <!-- <script type="text/javascript" src="{{ asset('js/vendor/FullCalendar/jquery-3.3.1.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/select2.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/offline.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/nprogress.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/snackbar.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/freeze-table.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/daterangepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/liteuploader.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/jsvalidation.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/tinymce/tinymce.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/datepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/colorpicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/jquery.timepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/js.cookie.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/croppie.min.js') }}"></script> -->

    <!-- Jquery js-->
    <script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/nprogress.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/snackbar.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/freeze-table.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/daterangepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/jsvalidation.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/tinymce/tinymce.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/datepicker.js') }}"></script>


    <!-- Bootstrap js-->
    <script src="{{ asset('assets/plugins/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>

    <!-- Internal Chart.Bundle js-->
    <script src="{{ asset('assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>

    <script src="{{ asset('assets/js/sticky.js') }}"></script>
    <script src="{{ asset('assets/js/tooltip.js') }}"></script>

    @auth
    <!-- Peity js-->
    <script src="{{ asset('assets/plugins/peity/jquery.peity.min.js') }}"></script>


    <!-- Perfect-scrollbar js -->
    <script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>

    <!-- Sidemenu js -->
    <script src="{{ asset('assets/plugins/sidemenu/sidemenu.js') }}"></script>

    <!-- Sidebar js -->
    <script src="{{ asset('assets/plugins/sidebar/sidebar.js') }}"></script>

    <!-- Internal Morris js -->
    <script src="{{ asset('assets/plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/morris.js/morris.min.js') }}"></script>

    <!-- Circle Progress js-->
    <!-- <script src="{{ asset('assets/js/circle-progress.min.js') }}"></script>
    <script src="{{ asset('assets/js/chart-circle.js') }}"></script> -->

    <!-- Internal Dashboard js-->
    <!-- <script src="{{ asset('assets/js/index.js') }}"></script> -->

    <!-- Sticky js -->
    <!-- <script src="{{ asset('assets/js/sticky.js') }}"></script> -->
    @endauth

    <!-- Select2 js-->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->

    <!-- Custom js -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    <!-- Vuejs related -->

    @if(config('app.env') == 'local')
    <script type="text/javascript" src="{{ asset('js/vendor/vue.js') }}"></script>
    @else
    <script type="text/javascript" src="{{ asset('js/vendor/vue.min.js') }}"></script>
    @endif
    <script type="text/javascript" src="{{ asset('js/vendor/axios.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/lodash.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/vue-loading-overlay@3.js') }}"></script>
    <!-- Vuejs related -->

    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>


    <!-- Custom JS here -->
    @auth
    <script type="text/javascript" src="{{ asset('js/admin/custom.js') }}"></script>
    @endauth

    <script type="text/javascript" src="{{ asset('js/admin/dev.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/admin/commonVue.js') }}"></script>

    @stack('pageJs')
    
    
    

</body>

</html>