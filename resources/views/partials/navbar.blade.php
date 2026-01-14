<nav class="layout-navbar container-fluid navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">

    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="icon-base bx bx-menu icon-md"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">

        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item navbar-search-wrapper mb-0">
                <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
                    <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete"></span>
                </a>
            </div>
        </div>

        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-md-auto">

            <!-- Style Switcher -->
            <li class="nav-item dropdown me-2 me-xl-0">
                <a class="nav-link dropdown-toggle hide-arrow" data-bs-toggle="dropdown" href="javascript:void(0);" id="nav-theme">
                    <i class="icon-base bx bx-sun icon-md theme-icon-active"></i>
                    <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
                </a>
                <ul aria-labelledby="nav-theme-text" class="dropdown-menu dropdown-menu-end">
                    <li>
                        <button aria-pressed="false" class="dropdown-item align-items-center active" data-bs-theme-value="light" type="button">
                            <span><i class="icon-base bx bx-sun icon-md me-3" data-icon="sun"></i>Light</span>
                        </button>
                    </li>
                    <li>
                        <button aria-pressed="true" class="dropdown-item align-items-center" data-bs-theme-value="dark" type="button">
                            <span><i class="icon-base bx bx-moon icon-md me-3" data-icon="moon"></i>Dark</span>
                        </button>
                    </li>
                    <li>
                        <button aria-pressed="false" class="dropdown-item align-items-center" data-bs-theme-value="system" type="button">
                            <span><i class="icon-base bx bx-desktop icon-md me-3" data-icon="desktop"></i>System</span>
                        </button>
                    </li>
                </ul>
            </li>
            <!-- / Style Switcher-->

            <!-- User -->
            <li class="nav-item navbar-dropdown mx-2">
                <a class="nav-link dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" href="javascript:void(0);">
                    <div class="avatar avatar-online">
                        <img alt class="rounded-circle" src="../../assets/img/logo/logo.gif" />
                    </div>
                </a>
            </li>
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" href="javascript:void(0);">
                    <span class="d-none d-xl-inline-block text-body fw-normal">
                        {{ auth()->user()?->name ?? '-' }}
                    </span><br>
                    <span class="small">
                        {{ auth()->user()?->email ?? '-' }}
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="pages-account-settings-account.html">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img alt class="w-px-40 h-auto rounded-circle" src="../../assets/img/logo/logo.gif" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ auth()->user()?->name ?? '-' }}</h6>
                                    <small class="text-body-secondary">{{ auth()->user()?->email ?? '-' }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="/profile"> <i class="icon-base bx bx-user icon-md me-3"></i><span>Profil</span> </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <a class="dropdown-item" href="keluar" onclick="event.preventDefault(); this.closest('form').submit();"> <i
                                    class="icon-base fas fa-power-off icon-sm me-3"></i><span>{{ __('Log Out') }}</span> </a>
                        </form>
                    </li>
                </ul>
            </li>
            <!--/ User -->

        </ul>
    </div>

</nav>
