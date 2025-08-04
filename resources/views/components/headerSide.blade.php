<header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
                <a class="nav-link sidebartoggler " id="headerCollapse" href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>
        </ul>
        <div class="navbar-collapse justify-content-end px-0" id="navbarNav" style="border-bottom: 1px solid #dee2e6;">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">

                <li class="nav-item dropdown">
                    <a class="nav-link " href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <span class="d-flex">
                            <i class="ti ti-user-circle" style="font-size: 30px; !important"></i>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                        aria-labelledby="drop2">
                        <div class="message-body">
                            <a href="javascript:void(0)"
                                class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-user fs-6"></i>
                                <p class="mb-0 fs-5">Meu perfil</p>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-outline-primary mx-3 mt-2 d-block">Sair</button>
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>