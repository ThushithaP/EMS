<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="{{ config('app.url') }}plugin/datatable/datatables.min.css" rel="stylesheet">
    <link href="{{ config('app.url') }}css/sidebar.css" rel="stylesheet">
    <link href="{{ config('app.url') }}css/pagecontent.css" rel="stylesheet">
    <link href="{{ config('app.url') }}css/datatable.css" rel="stylesheet">
    <link href="{{ config('app.url') }}css/modal.css" rel="stylesheet">
    <!-- <link href="{{ config('app.url') }}plugin/datatables/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" /> -->
    <link href="{{ config('app.url') }}plugin/datatables/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />


    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <script src="{{ config('app.url') }}plugin/jquery/dist/jquery.min.js" type="text/javascript"></script>
    <script src="{{ config('app.url') }}plugin/datatable/datatables.min.js" type="text/javascript"></script>
    <script src="{{ config('app.url') }}plugin/bootbox/dist/bootbox.min.js" type="text/javascript"></script>
    <script src="{{ config('app.url') }}plugin/formvalidation/js/formValidation.popular.min.js" type="text/javascript"></script>
    <script src="{{ config('app.url') }}plugin/formvalidation/js/framework/bootstrap.min.js" type="text/javascript"></script>
    <script src="{{ config('app.url') }}plugin/sweetalert/js/sweetalert2.all.min.js" type="text/javascript"></script>
    <!-- <script src="{{ config('app.url') }}plugin/datatables/js/jquery.dataTables.min.js" type="text/javascript"></script> -->
	<script src="{{ config('app.url') }}plugin/datatables/js/dataTables.responsive.min.js" type="text/javascript"></script>


    <script type="text/javascript">

        function toggleSidebar() {
            var sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('sidebar-toggle');
            var menuNames = document.querySelectorAll('.menu-name');
            menuNames.forEach(menu => {
                menu.classList.toggle('d-none');
            });  
            var content = document.querySelector('.content');
            var isSidebarToggled = sidebar.classList.contains('sidebar-toggle');

            content.style.marginLeft = isSidebarToggled ? '60px' : '190px';
        }

        //For global use

        function showMessage(icon,message) {
            const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
            });
            Toast.fire({
                icon: icon,
                title: message,
                showCloseButton: true,
            });
        }

        function confirmCustomPopup(title, message, buttonType, callback, noAction ) {
            if (typeof(noAction)==='undefined') noAction = function(){};
        
            messageDialog = bootbox.dialog({
                message: message,
                title: title,
                className: "t-modal",
                buttons: {
                    danger: {
                    label: (buttonType == 'Y/N' ? 'No' : 'Cancel'),
                        className: "t-no-btn btn-sm",
                        callback: function() {
                            noAction();
                            messageDialog.modal('hide');
                        }
                    },
                    main: {
                        label: (buttonType == 'Y/N' ? 'Yes' : 'OK'),
                        className: "t-yes-btn btn-sm",
                        callback: function() {
                            callback();
                        }
                    }
                }
            });
        }

        function sendAjaxRequest(submit_url, params) {
            $.ajax({
                type: "POST",
                url: submit_url,
                data: params,
                success: function (response) {
                    try {
                        if(response.success) {
                            showMessage('success',response.message);
                            refreshDataTable();
                        } else if (response.success == false) {
                            showMessage('error',response.message);
                        } else {
                            showMessage('error','Unknown error occured.');
                        }
                    } catch (e) {
                        showMessage('error','Unknown error occured.');
                    }
                },
                error: function (e) {
                    showMessage('error','Unknown error occured.');
                }
            });
        }

    </script>

    <style>
        .sidebar-toggle{
            width: 70px !important;
        }
        .t-nav{
            background-color: #597272 !important;
        }
        .t-toggle-icon{
            color: var(--bs-navbar-brand-color);
            font-size: 20px !important;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm t-nav">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <a class=""  data-toggle="sidebar-collapse" aria-expanded="false" type="button" href="javascript:toggleSidebar()">
                    <i class="bi bi-list t-toggle-icon"></i>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <img class="rounded-circle" width="40px" src="@if(Auth::user()->image) {{ asset(Auth::user()->image) }} @else {{ asset('image/user.png') }} @endif" alt="">
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->first_name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        Profile
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="" style="max-height: 100vh; overflow-y: auto;">
            @yield('content')
        </main>
    </div>
</body>
</html>
