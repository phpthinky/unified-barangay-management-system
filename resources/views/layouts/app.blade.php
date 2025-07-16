<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Barangay') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body { overflow-x: hidden; }
        .sidebar { height: 100vh; position: fixed; top: 0; left: 0; padding-top: 60px; background: #f8f9fa; width: 220px; }
        .content { margin-left: 220px; padding: 20px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top px-4 shadow-sm">
        <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name', 'Online Barangay Management System') }}</a>

        <div class="ms-auto d-flex align-items-center">
            @if(session()->has('impersonate'))
                <span class="badge bg-warning me-2">Impersonating: {{ auth()->user()->name }}</span>
                <a href="{{ route('impersonate.stop') }}" class="btn btn-sm btn-danger me-2">Stop</a>
            @endif

            @auth
                <span class="me-3"><a class="nav-link" href="{{ route('profile') }}">{{ auth()->user()->name }}</a></span>
                <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-danger"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">@csrf</form>
            @endauth
        </div>
    </nav>
{{-- sidebar.blade.php (include inside layouts/app) --}}
<div class="sidebar border-end">
  <ul class="nav flex-column p-3">

      {{-- RESIDENTS DROPDOWN --}}
      <li class="nav-item mt-2">
          <a class="nav-link d-flex justify-content-between align-items-center"
             data-bs-toggle="collapse" href="#residentsCollapse">
             <span><i class="bi bi-people"></i> Residents</span>
             <i class="bi bi-caret-down-fill small"></i>
          </a>
          <div class="collapse" id="residentsCollapse">
              <ul class="list-unstyled ps-3">

      {{-- MAIN DASHBOARD --}}
      <li class="nav-item mb-2">
          <a href="{{ route('dashboard') }}"
             class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
             <i class="bi bi-house-door"></i> Dashboard
          </a>
      </li>

                  <li class="nav-item mb-2">
                      <a class="nav-link {{ request()->routeIs('requests.*') ? 'active' : '' }}"
                         href="{{ route('requests.index') }}">
                         <i class="bi bi-file-earmark-text"></i> Requests
                      </a>
                  </li>
                  <li class="nav-item mb-2">
                      <a class="nav-link {{ request()->routeIs('complaints.*') ? 'active' : '' }}"
                         href="{{ route('complaints.index') }}">
                         <i class="bi bi-exclamation-circle"></i> Complaints
                      </a>
                  </li>
              </ul>
          </div>
      </li>

      {{-- ABC DROPDOWN --}}
      <li class="nav-item mt-2">
          <a class="nav-link d-flex justify-content-between align-items-center"
             data-bs-toggle="collapse" href="#abcCollapse">
             <span><i class="bi bi-shield-lock"></i> ABC</span>
             <i class="bi bi-caret-down-fill small"></i>
          </a>
          <div class="collapse" id="abcCollapse">
              <ul class="list-unstyled ps-3">
                  <li class="nav-item mb-2">
                      <a class="nav-link {{ request()->routeIs('abc.*') ? 'active' : '' }}"
                         href="{{ route('abc.index') }}">
                         <i class="bi bi-people-fill"></i> Directory
                      </a>
                  </li>
                  <li class="nav-item mb-2">
                      <a class="nav-link"
                         href="{{ route('officials.archive') }}">
                         <i class="bi bi-archive"></i> Archive Officials
                      </a>
                  </li>
              </ul>
          </div>
      </li>

      {{-- Captain DROPDOWN --}}
      <li class="nav-item mt-2">
          <a class="nav-link d-flex justify-content-between align-items-center"
             data-bs-toggle="collapse" href="#amdinCollapse">
             <span><i class="bi bi-shield"></i> Admin</span>
             <i class="bi bi-caret-down-fill small"></i>
          </a>
          <div class="collapse" id="amdinCollapse">
              <ul class="list-unstyled ps-3">


                  <li class="nav-item mb-2">
                      <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
               <i class="bi bi-speedometer"></i> Dashboard
            </a>

                  </li>

                  <li class="nav-item mb-2">
                      <a class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}"
                         href="{{ route('resident.list') }}">
                         <i class="bi bi-people-fill"></i> Residents
                      </a>
                  </li>
                  <li class="nav-item mb-2">
                      <a class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}"
                         href="{{ route('reports.index') }}">
                         <i class="bi bi-bar-chart-line"></i> Reports
                      </a>
                  </li>

                  <li class="nav-item mb-2">
                      <a class="nav-link {{ request()->routeIs('offline') ? 'active' : '' }}"
                         href="{{ route('offline') }}">
                         <i class="bi bi-wifi-off"></i> Offline
                      </a>
                  </li>

                  <li class="nav-item mb-2">
                      <a class="nav-link {{ request()->routeIs('logs.demo') ? 'active' : '' }}"
                         href="{{ route('logs.demo') }}">
                         <i class="bi bi-journal-text"></i> Logs
                      </a>
                  </li>

                  <li class="nav-item mb-2">
                      <a class="nav-link {{ request()->routeIs('permits.index') ? 'active' : '' }}"
                         href="{{ route('permits.index') }}">
                         <i class="bi bi-briefcase"></i> Permits
                      </a>
                  </li>

                  <li class="nav-item mb-2">
                      <a class="nav-link {{ request()->routeIs('settings.notifications') ? 'active' : '' }}"
                         href="{{ route('settings.notifications') }}">
                         <i class="bi bi-bell"></i> SMS / Email
                      </a>
                  </li>
              </ul>
          </div>
      </li>

      {{-- SINGLE LINKS --}}

      {{-- SETTINGS DROPDOWN --}}
      <li class="nav-item mt-2">
          <a class="nav-link d-flex justify-content-between align-items-center"
             data-bs-toggle="collapse" href="#settingsCollapse">
             <span><i class="bi bi-gear"></i> Settings</span>
             <i class="bi bi-caret-down-fill small"></i>
          </a>
          <div class="collapse" id="settingsCollapse">
              <ul class="list-unstyled ps-3">
                  <li class="nav-item mb-2">
                      <a class="nav-link {{ request()->routeIs('requests.*') ? 'active' : '' }}"
                         href="{{ route('requests.index') }}">
                         <i class="bi bi-globe  "></i> Site Layout
                      </a>
                  </li>
                  <li class="nav-item mb-2">
                      <a class="nav-link {{ request()->routeIs('complaints.*') ? 'active' : '' }}"
                         href="{{ route('complaints.index') }}">
                         <i class="bi bi-people-fill  "></i> Manage Users
                      </a>
                  </li>
              </ul>
          </div>
      </li>


  </ul>
</div>



    <main class="content py-4 pt-5">
        <div class="container-fluid pt-4">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @yield('content')
        </div>
        
    </main>
<!-- In your @push('scripts') or in layout.blade.php -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script src="{{ asset('js/app.js') }}"></script>
        @stack('scripts')
</body>

</body>
</html>
