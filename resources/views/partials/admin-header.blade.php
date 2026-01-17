<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a href="javascript:void(0)"
                class="nav-link d-flex align-items-center"
                id="userToggle">

                <img src="{{ auth()->user()->image
                    ? asset('uploads/users/' . auth()->user()->image)
                    : asset('dist/img/user-default.png') }}"
                    class="img-circle elevation-2"
                    style="width:32px;height:32px;object-fit:cover">


                <span class="ml-2 d-none d-md-inline">
                    <b>{{ auth()->user()->name }}</b>
                </span>

                <i class="fas fa-chevron-down ml-1"></i>
            </a>

            {{-- Custom dropdown --}}
            <div id="userDropdown"
                style="display:none; position:absolute; right:15px; top:55px;
                background:#fff; width:200px;
                border-radius:6px;
                box-shadow:0 5px 15px rgba(0,0,0,.15);
                z-index:1050;">

                <div class="p-3 text-center text-muted">
                    Welcome <b>{{ auth()->user()->name }}</b>
                </div>

                <div style="border-top:1px solid #eee;"></div>

                <a href="javascript:void(0)"
                    id="logoutBtn"
                    class="d-block text-center p-2 text-danger">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </a>
            </div>
        </li>

        <form id="logout-form"
            action="{{ route('logout') }}"
            method="POST"
            class="d-none">
            @csrf
        </form>

    </ul>
</nav>
<!-- /.navbar -->