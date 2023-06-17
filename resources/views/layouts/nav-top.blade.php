<nav class="main-header navbar navbar-expand navbar-white navbar-light shadow-sm justify-content-between">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('home') }}" class="nav-link">{{ __('Home') }}</a>
        </li>
    </ul>
    <ul class="navbar-nav">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                {{ Auth::user()->name }}
            </a>
            {{-- <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-address-card"></i>
                <!-- <span class="badge badge-warning navbar-badge">15</span> -->
            </a> --}}
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        {{ __('Logout') }}
                    </button>
                </form>
            </div>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-language"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('home.changeLanguage', ['vi']) }}" class="dropdown-item">
                    Tiếng Việt
                </a>
                <a href="{{ route('home.changeLanguage', ['en']) }}" class="dropdown-item">
                    English
                </a>
            </div>
        </li>
    </ul>
</nav>
