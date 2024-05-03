<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">

  <title>Tools And Equipment Monitoring System</title>


  <link rel="stylesheet" href="{{ asset('js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
  <link rel="stylesheet" href="{{ asset('js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">
  <link rel="stylesheet" href="{{ asset('js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">
  @yield('css')

  {{-- <link rel="stylesheet" id="css-main" href="{{asset('js/codebase.min.css')}}"> --}}


  <!-- Modules -->


  @vite(['resources/sass/main.scss', 'resources/js/codebase/app.js'])

  <!-- Alternatively, you can also include a specific color theme after the main stylesheet to alter the default color theme of the template -->
  {{-- @vite(['resources/sass/main.scss', 'resources/sass/codebase/themes/corporate.scss', 'resources/js/codebase/app.js']) --}}

</head>

<body>
  <!-- Page Container -->
  <!--
    Available classes for #page-container:

    SIDEBAR & SIDE OVERLAY

      'sidebar-r'                                 Right Sidebar and left Side Overlay (default is left Sidebar and right Side Overlay)
      'sidebar-mini'                              Mini hoverable Sidebar (screen width > 991px)
      'sidebar-o'                                 Visible Sidebar by default (screen width > 991px)
      'sidebar-o-xs'                              Visible Sidebar by default (screen width < 992px)
      'sidebar-dark'                              Dark themed sidebar

      'side-overlay-hover'                        Hoverable Side Overlay (screen width > 991px)
      'side-overlay-o'                            Visible Side Overlay by default

      'enable-page-overlay'                       Enables a visible clickable Page Overlay (closes Side Overlay on click) when Side Overlay opens

      'side-scroll'                               Enables custom scrolling on Sidebar and Side Overlay instead of native scrolling (screen width > 991px)

    HEADER

      ''                                          Static Header if no class is added
      'page-header-fixed'                         Fixed Header

    HEADER STYLE

      ''                                          Classic Header style if no class is added
      'page-header-modern'                        Modern Header style
      'page-header-dark'                          Dark themed Header (works only with classic Header style)
      'page-header-glass'                         Light themed Header with transparency by default
                                                  (absolute position, perfect for light images underneath - solid light background on scroll if the Header is also set as fixed)
      'page-header-glass page-header-dark'        Dark themed Header with transparency by default
                                                  (absolute position, perfect for dark images underneath - solid dark background on scroll if the Header is also set as fixed)

    MAIN CONTENT LAYOUT

      ''                                          Full width Main Content if no class is added
      'main-content-boxed'                        Full width Main Content with a specific maximum width (screen width > 1200px)
      'main-content-narrow'                       Full width Main Content with a percentage width (screen width > 1200px)

    DARK MODE

      'sidebar-dark page-header-dark dark-mode'   Enable dark mode (light sidebar/header is not supported with dark mode)
  -->
  <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-modern main-content-boxed">

    <!-- Sidebar -->
    <!--
      Helper classes

      Adding .smini-hide to an element will make it invisible (opacity: 0) when the sidebar is in mini mode
      Adding .smini-show to an element will make it visible (opacity: 1) when the sidebar is in mini mode
        If you would like to disable the transition, just add the .no-transition along with one of the previous 2 classes

      Adding .smini-hidden to an element will hide it when the sidebar is in mini mode
      Adding .smini-visible to an element will show it only when the sidebar is in mini mode
      Adding 'smini-visible-block' to an element will show it (display: block) only when the sidebar is in mini mode
    -->
    <nav id="sidebar">
      <!-- Sidebar Content -->
      <div class="sidebar-content">
        <!-- Side Header -->
        <div class="content-header justify-content-lg-center">
          <!-- Logo -->
          <div>
            <span class="smini-visible fw-bold tracking-wide fs-lg">
              c<span class="text-primary">b</span>
            </span>
            <a class="link-fx fw-bold tracking-wide mx-auto" href="/dashboard">
              <span class="smini-hidden">
                <img src="{{asset('media/logo.png')}}" width="170" alt="">
              </span>
            </a>
          </div>
          <!-- END Logo -->

          <!-- Options -->
          <div>
            <!-- Close Sidebar, Visible only on mobile screens -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <button type="button" class="btn btn-sm btn-alt-danger d-lg-none" data-toggle="layout" data-action="sidebar_close">
              <i class="fa fa-fw fa-times"></i>
            </button>
            <!-- END Close Sidebar -->
          </div>
          <!-- END Options -->
        </div>
        <!-- END Side Header -->

        <!-- Sidebar Scrolling -->
        <div class="js-sidebar-scroll">
          <!-- Side User -->

          <!-- END Side User -->

          <!-- Side Navigation -->
          <div class="content-side content-side-full">
            <ul class="nav-main">
              <li class="nav-main-item">
                <a class="nav-main-link{{ request()->is('dashboard') ? ' active' : '' }}" href="/dashboard">
                  <i class="nav-main-link-icon fa fa-house-user"></i>
                  <span class="nav-main-link-name">Dashboard</span>
                </a>
              </li>

              @if (Auth::user()->user_type_id == 2)
              <li class="nav-main-item">
                <a class="nav-main-link{{ request()->is('pages/rftte') ? ' active' : '' }}" href="/pages/rftte">
                  <i class="nav-main-link-icon fa fa-box-open"></i>
                  <span class="nav-main-link-name">RFTTE</span>
                </a>
              </li>
              <li class="nav-main-item">
                <a class="nav-main-link{{ request()->is('pages/pullout_warehouse') ? ' active' : '' }}" href="/pages/pullout_warehouse">
                  <i class="nav-main-link-icon fa fa-building-circle-arrow-right"></i>
                  <span class="nav-main-link-name">Pull-Out</span>
                </a>
              </li>
              @endif

              @if (Auth::user()->user_type_id == 3 || Auth::user()->user_type_id == 4 || Auth::user()->user_type_id == 5)
              <li class="nav-main-item">
                <a class="nav-main-link{{ request()->is('pages/my_te') ? ' active' : '' }}" href="/pages/my_te">
                  <i class="nav-main-link-icon fa fa-screwdriver-wrench"></i>
                  <span class="nav-main-link-name">
                    Tools and Equipment</span>
                </a>
              </li>
              <li class="nav-main-item{{ request()->is('') ? ' open' : '' }}">
                <a class="nav-main-link nav-main-link-submenu{{ request()->is('pages/pullout_ongoing', 'pages/pullout_completed') ? ' active' : '' }}" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                  <i class="nav-main-link-icon fa fa-arrows-turn-right"></i>
                  <span class="nav-main-link-name">Pull-Out Request</span>
                </a>
                <ul class="nav-main-submenu">
                  <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('pages/pullout_ongoing') ? ' active' : '' }}" href="/pages/pullout_ongoing">
                      <span class="nav-main-link-name">Ongoing</span>
                    </a>
                  </li>
                  <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('pages/pullout_completed') ? ' active' : '' }}" href="/pages/pullout_completed">
                      <span class="nav-main-link-name">Completed</span>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-main-item{{ request()->is('') ? ' open' : '' }}">
                <a class="nav-main-link nav-main-link-submenu{{ request()->is('pages/request_ongoing', 'pages/request_completed') ? ' active' : '' }}" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                  <i class="nav-main-link-icon fa fa-file-pen"></i>
                  <span class="nav-main-link-name">TEIS Request</span>
                </a>
                <ul class="nav-main-submenu">
                  <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('pages/request_ongoing') ? ' active' : '' }}" href="/pages/request_ongoing">
                      <span class="nav-main-link-name">Ongoing</span>
                    </a>
                  </li>
                  <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('pages/request_completed') ? ' active' : '' }}" href="/pages/request_completed">
                      <span class="nav-main-link-name">Completed</span>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-main-item">
                <a class="nav-main-link{{ request()->is('pages') ? ' active' : '' }}" href="/pages/my_te">
                  <i class="nav-main-link-icon fa fa-building-circle-arrow-right"></i>
                  <span class="nav-main-link-name">Site to Site Transfer</span>
                </a>
              </li>
              @endif
              <li class="nav-main-heading">Tools and Equipment from</li>
              <li class="nav-main-item">
                <a class="nav-main-link{{ request()->is('pages/warehouse') ? ' active' : '' }}" href="/view_warehouse">
                  <i class="nav-main-link-icon fa fa-warehouse"></i>
                  <span class="nav-main-link-name">Warehouse</span>
                </a>
              </li>
              <li class="nav-main-item">
                <a class="nav-main-link{{ request()->is('pages/project_site') ? ' active' : '' }}" href="/pages/project_site">
                  <i class="nav-main-link-icon fa fa-building"></i>
                  <span class="nav-main-link-name">Project Site</span>
                </a>
              </li>
            </ul>
          </div>
          <!-- END Side Navigation -->
        </div>
        <!-- END Sidebar Scrolling -->
      </div>
      <!-- Sidebar Content -->
    </nav>
    <!-- END Sidebar -->

    <!-- Header -->
    <header id="page-header">
      <!-- Header Content -->
      <div class="content-header">
        <!-- Left Section -->
        <div class="space-x-1">
          <!-- Toggle Sidebar -->
          <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
          <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="layout" data-action="sidebar_toggle">
            <i class="fa fa-fw fa-bars"></i>
          </button>
          <!-- END Toggle Sidebar -->

          <!-- Open Search Section -->
          <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
          <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="layout" data-action="header_search_on">
            <i class="fa fa-fw fa-search"></i>
          </button>
          <!-- END Open Search Section -->
        </div>
        <!-- END Left Section -->

        <!-- Right Section -->
        <div class="space-x-1">
          <!-- User Dropdown -->
          <div class="dropdown d-inline-block">
            <button type="button" class="btn btn-sm btn-alt-secondary" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-user d-sm-none"></i>
              <span class="d-none d-sm-inline-block fw-semibold">{{Auth::user()->fullname}}</span>
              <i class="fa fa-angle-down opacity-50 ms-1"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-0" aria-labelledby="page-header-user-dropdown">
              <div class="px-2 py-3 bg-body-light rounded-top">
                <h5 class="h6 text-center mb-0">
                  {{Auth::user()->fullname}}
                </h5>
              </div>
              <div class="p-2">
                <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="javascript:void(0)">
                  <span>Profile</span>
                  <i class="fa fa-fw fa-user opacity-25"></i>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1" href="{{route('logout')}}">
                  <span>Sign Out</span>
                  <i class="fa fa-fw fa-sign-out-alt opacity-25"></i>
                </a>
              </div>
            </div>
          </div>
          <!-- END User Dropdown -->

          <!-- Notifications -->
          <div class="dropdown d-inline-block">
            <button type="button" class="btn btn-sm btn-alt-secondary" id="page-header-notifications" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-bell animated swing loop"></i>
              <span class="text-xl text-primary">&bull;</span>
            </button>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications">
              <div class="px-2 py-3 bg-body-light rounded-top">
                <h5 class="h6 text-center mb-0">
                  Notifications
                </h5>
              </div>
              <ul class="nav-items my-2 fs-sm">
                <li>
                  <a class="text-dark d-flex py-2" href="javascript:void(0)">
                    <div class="flex-shrink-0 me-2 ms-3">
                      <i class="fa fa-fw fa-check text-success"></i>
                    </div>
                    <div class="flex-grow-1 pe-2">
                      <p class="fw-medium mb-1">You’ve upgraded to a VIP account successfully!</p>
                      <div class="text-muted">15 min ago</div>
                    </div>
                  </a>
                </li>
                <li>
                  <a class="text-dark d-flex py-2" href="javascript:void(0)">
                    <div class="flex-shrink-0 me-2 ms-3">
                      <i class="fa fa-fw fa-exclamation-triangle text-warning"></i>
                    </div>
                    <div class="flex-grow-1 pe-2">
                      <p class="fw-medium mb-1">Please check your payment info since we can’t validate them!</p>
                      <div class="text-muted">50 min ago</div>
                    </div>
                  </a>
                </li>
                <li>
                  <a class="text-dark d-flex py-2" href="javascript:void(0)">
                    <div class="flex-shrink-0 me-2 ms-3">
                      <i class="fa fa-fw fa-times text-danger"></i>
                    </div>
                    <div class="flex-grow-1 pe-2">
                      <p class="fw-medium mb-1">Web server stopped responding and it was automatically restarted!</p>
                      <div class="text-muted">4 hours ago</div>
                    </div>
                  </a>
                </li>
                <li>
                  <a class="text-dark d-flex py-2" href="javascript:void(0)">
                    <div class="flex-shrink-0 me-2 ms-3">
                      <i class="fa fa-fw fa-exclamation-triangle text-warning"></i>
                    </div>
                    <div class="flex-grow-1 pe-2">
                      <p class="fw-medium mb-1">Please consider upgrading your plan. You are running out of space.</p>
                      <div class="text-muted">16 hours ago</div>
                    </div>
                  </a>
                </li>
                <li>
                  <a class="text-dark d-flex py-2" href="javascript:void(0)">
                    <div class="flex-shrink-0 me-2 ms-3">
                      <i class="fa fa-fw fa-plus text-primary"></i>
                    </div>
                    <div class="flex-grow-1 pe-2">
                      <p class="fw-medium mb-1">New purchases! +$250</p>
                      <div class="text-muted">1 day ago</div>
                    </div>
                  </a>
                </li>
              </ul>
              <div class="p-2 bg-body-light rounded-bottom">
                <a class="dropdown-item text-center mb-0" href="javascript:void(0)">
                  <i class="fa fa-fw fa-flag opacity-50 me-1"></i> View All
                </a>
              </div>
            </div>
          </div>
          <!-- END Notifications -->

        </div>
        <!-- END Right Section -->
      </div>
      <!-- END Header Content -->

      <!-- Header Search -->
      <div id="page-header-search" class="overlay-header bg-body-extra-light">
        <div class="content-header">
          <form class="w-100" action="/dashboard" method="POST">
            @csrf
            <div class="input-group">
              <!-- Close Search Section -->
              <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
              <button type="button" class="btn btn-secondary" data-toggle="layout" data-action="header_search_off">
                <i class="fa fa-fw fa-times"></i>
              </button>
              <!-- END Close Search Section -->
              <input type="text" class="form-control" placeholder="Search or hit ESC.." id="page-header-search-input" name="page-header-search-input">
              <button type="submit" class="btn btn-secondary">
                <i class="fa fa-fw fa-search"></i>
              </button>
            </div>
          </form>
        </div>
      </div>
      <!-- END Header Search -->

      <!-- Header Loader -->
      <div id="page-header-loader" class="overlay-header bg-primary">
        <div class="content-header">
          <div class="w-100 text-center">
            <i class="far fa-sun fa-spin text-white"></i>
          </div>
        </div>
      </div>
      <!-- END Header Loader -->
    </header>
    <!-- END Header -->

    
    <!-- Main Container -->
    <main id="main-container">
      <div class="content">
        <h2 style="margin-bottom: -10px;">@yield('content-title')</h2>
      </div>
      @yield('content')
    </main>
    <!-- END Main Container -->

    <!-- Footer -->
    <footer id="page-footer">
      <div class="content py-3">
        <div class="row fs-sm">
          <div class="col-sm-6 order-sm-2 py-1 text-center text-sm-end">
            
          </div>
          <div class="col-sm-6 order-sm-1 py-1 text-center text-sm-start">
            {{-- <a class="fw-semibold" href="/" target="_blank">Codebase</a> &copy; <span data-toggle="year-copy"></span> --}}
          </div>
        </div>
      </div>
    </footer>
    <!-- END Footer -->
  </div>
  <!-- END Page Container -->

  <script src="{{asset('js/lib/jquery.min.js')}}"></script>
  <script src="{{ asset('js/plugins/datatables/dataTables.min.js') }}"></script>
  <script src="{{ asset('js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
  <script src="{{asset('js/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
  <script src="{{ asset('js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
  {{-- <script src="{{asset('js/plugins/datatables-buttons/dataTables.buttons.min.js')}}"></script>
  <script src="{{asset('js/plugins/datatables-buttons-bs5/js/buttons.bootstrap5.min.js')}}"></script>
  <script src="{{asset('js/plugins/datatables-buttons-jszip/jszip.min.js')}}"></script>
  <script src="{{asset('js/plugins/datatables-buttons-pdfmake/pdfmake.min.js')}}"></script>
  <script src="{{asset('js/plugins/datatables-buttons-pdfmake/vfs_fonts.js')}}"></script>
  <script src="{{asset('js/plugins/datatables-buttons/buttons.print.min.js')}}"></script>
  <script src="{{asset('js/plugins/datatables-buttons/buttons.html5.min.js')}}"></script> --}}
  {{-- <script src="{{asset('js/codebase.app.min.js')}}"></script> --}}

  @yield('js')
</body>

</html>
