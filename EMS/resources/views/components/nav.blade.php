<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">

        {{-- Logo --}}
        @auth
        <a class="navbar-brand" href="{{ url('admin/dashboard') }}">
            <img src="{{ asset('uploads/Logo.png') }}" width="215" height="30%"
                class="d-inline-block align-center" id="nav-image-logo" alt="Logo">
        </a>
        @else
        <span class="navbar-brand">
            <img src="{{ asset('uploads/Logo.png') }}" width="215" height="30%"
                class="d-inline-block align-center" id="nav-image-logo" alt="Logo">
        </span>
        @endauth


        {{-- Mobile Toggle --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Navbar Links --}}
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto">

                @auth
                {{-- Home --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}"
                        href="{{ url('admin/dashboard') }}">Home</a>
                </li>

                {{-- Initial Data Dropdown --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::is('event_create','event_type','employees') ? 'active' : '' }}"
                        href="#" id="initialDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Initial Data
                        <i class='bx bx-chevron-down' style="margin-top: 4px;margin-left:2px"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="initialDropdown">
                        <li><a class="dropdown-item {{ request()->is('event_create') ? 'dropdown_active' : '' }}" href="{{ url('/event_create') }}">Initial Event Types</a></li>
                        <li><a class="dropdown-item {{ request()->is('event_type') ? 'dropdown_active' : '' }}" href="{{ url('/event_type') }}">Initial Events</a></li>
                        <li><a class="dropdown-item {{ request()->is('employees') ? 'dropdown_active' : '' }}" href="{{ url('/employees') }}">Employees</a></li>
                    </ul>
                </li>

                {{-- Process Data Dropdown --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::is('space_events*','space_event*','assgn_employees','reports/registeredlist') ? 'active' : '' }}"
                        href="#" id="processDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Process Data
                        <i class='bx bx-chevron-down' style="margin-top: 4px;margin-left:2px"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="processDropdown">
                        <li><a class="dropdown-item {{ request()->is('space_events') ? 'dropdown_active' : '' }}" href="{{ url('/space_events') }}">Manage Events</a></li>
                        <li><a class="dropdown-item {{ request()->is('assgn_employees') ? 'dropdown_active' : '' }}" href="{{ url('/assgn_employees') }}">Assigned Employees</a></li>
                    </ul>
                </li>

                {{-- Competitions --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::is('students','competitions','artcompetitors') ? 'active' : '' }}"
                        href="#" id="competitionsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Competitions
                        <i class='bx bx-chevron-down' style="margin-top: 4px;margin-left:2px"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="competitionsDropdown">
                        <li><a class="dropdown-item {{ request()->is('students') ? 'dropdown_active' : '' }}" href="{{ url('/students') }}">Water Rocket</a></li>
                        <li><a class="dropdown-item {{ request()->is('artcompetitors') ? 'dropdown_active' : '' }}" href="{{ url('/artcompetitors') }}">Art Competition</a></li>
                    </ul>
                </li>

                {{-- Documents --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::is('admin/invitations') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Documents
                        <i class='bx bx-chevron-down' style="margin-top: 4px;margin-left:2px"></i>
                    </a>
                    <ul class="dropdown-menu">
                        {{-- <li><a class="dropdown-item {{ request()->is('admin/certificates') ? 'dropdown_active' : '' }}" href="{{url('admin/certificates')}}">Certificates</a>
                </li> --}}
                <li><a class="dropdown-item {{ request()->is('admin/invitations') ? 'dropdown_active' : '' }}" href="{{url('admin/invitations')}}">Invitations</a></li>

            </ul>
            </li>

            {{-- Help --}}
            <!-- <li class="nav-item"> 
                    <a class="nav-link {{ Request::is('admin/help') ? 'active' : '' }}"
                        href="{{ url('admin/help') }}">Help</a>
                </li>-->

            {{-- Profile Dropdown --}}
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                    id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class='bx bxs-user-circle' style="font-size:30px; color:#000; margin-right:10px;"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="{{ url('/admin/settings') }}">Settings</a></li>
                    <li><a class="dropdown-item" href="{{ url('logout') }}">Logout</a></li>
                </ul>
            </li>
            @endauth

            </ul>
        </div>
    </div>
</nav>