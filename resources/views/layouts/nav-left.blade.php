<aside class="main-sidebar sidebar-light-info shadow">
    <div class="d-flex justify-content-center align-items-center py-3">
        <a href="{{ url('/') }}">
            <img src="{{ asset('logo.png') }}" width="50px">
        </a>
    </div>
    @if (Auth::user()->level == 9999)
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item has-treeview">
                    <a href="{{ route('home') }}" class="nav-link home">
                        <i class="nav-icon fas fa-home"></i>
                        <p>{{ __('Dashboard') }}</p>
                    </a>
                    <a href="{{ route('admin.student') }}" class="nav-link warehouse">
                        <i class="nav-icon far fa-dot-circle "></i>
                        <p>{{ __('Student Management') }}</p>
                    </a>
                    <a href="{{ route('admin.lecturers') }}" class="nav-link warehouse">
                        <i class="nav-icon far fa-dot-circle"></i>
                        <p>{{ __('Instructor Management') }}</p>
                    </a>
                    <a href="{{ route('admin.department') }}" class="nav-link warehouse">
                        <i class="nav-icon far fa-dot-circle"></i>
                        <p>{{ __('Department manager') }}</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    @endif
</aside>
