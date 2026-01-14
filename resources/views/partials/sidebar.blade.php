<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand my-4">
    <a href="/" class="app-brand-link">
      <span class="app-brand-logo">
        <img src="{{ asset('assets/img/logo/logo.png') }}" alt="logo" width="100%">
      </span>
      <span class="app-brand-text menu-text fw-bolder ms-2"></span>
    </a>
    {{-- <a href="javascript:void(0);" class="layout-menu-toggle text-large ms-auto">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a> --}}
  </div>

  <ul class="menu-inner py-0">
    <!-- Menu Utama -->
    <li class="menu-item">
      <a href="/" class="menu-link">
        <i class="menu-icon icon-base bx bx-tiny-home"></i>
        <div>Dashboard</div>
      </a>
    </li>

    <!-- Bangkom Section -->
    <li class="menu-header small text-uppercase m-0">
      <span class="menu-header-text">Bangkom</span>
    </li>
    <li class="menu-item">
      <a href="/coaching" class="menu-link">
        <i class="menu-icon icon-base bx bx-whiteboard"></i>
        <div>Coaching & Mentoring</div>
      </a>
    </li>
    <li class="menu-item">
      <a href="/bangkom" class="menu-link">
        <i class="menu-icon icon-base bx bx-like"></i>
        <div>Diklat & Pelatihan</div>
      </a>
    </li>
    <li class="menu-item">
      <a href="/kms" class="menu-link">
        <i class="menu-icon icon-base bx bx-book"></i>
        <div>KMS</div>
      </a>
    </li>
    <li class="menu-item">
      <a href="/riwayat" class="menu-link">
        <i class="menu-icon icon-base bx bx-history"></i>
        <div>Riwayat Bangkom</div>
      </a>
    </li>
    <li class="menu-item">
      <a href="/profile" class="menu-link">
        <i class="menu-icon icon-base bx bx-user"></i>
        <div>Profil</div>
      </a>
    </li>

    <!-- Manajemen Bangkom (Hanya admin) -->
    @if (auth()->user()?->roles->contains('name', 'admin'))
    <li class="menu-header small text-uppercase m-0">
      <span class="menu-header-text">Manajemen Bangkom</span>
    </li>
    <li class="menu-item">
      <a href="{{ route('manajemen_bangkom') }}" class="menu-link">
        <i class="menu-icon icon-base bx bx-folder-open"></i>
        <div>Manajemen Pelatihan</div>
      </a>
    </li>
    <li class="menu-item">
      <a href="/verifikasi" class="menu-link">
        <i class="menu-icon icon-base bx bx-check"></i>
        <div>Verifikasi Bangkom</div>
      </a>
    </li>
    @endif

    <!-- Menu Dinamis dari $menus -->
    @foreach ($menus ?? [] as $menu)
      @if ($menu->is_header)
        <li class="menu-header small text-uppercase m-0">
          <span class="menu-header-text">{{ $menu->name }}</span>
        </li>
      @elseif ($menu->children->count())
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="{{ $menu->icon }}"></i>
            <div class="text-truncate">{{ $menu->name }}</div>
          </a>
          <ul class="menu-sub">
            @foreach ($menu->children as $child)
              <li class="menu-item">
                <a href="{{ route($child->route) }}" class="menu-link">
                  <div class="text-truncate">{{ $child->name }}</div>
                </a>
              </li>
            @endforeach
          </ul>
        </li>
      @else
        <li class="menu-item">
          <a href="{{ route($menu->route) }}" class="menu-link">
            <i class="{{ $menu->icon }}"></i>
            <div>{{ $menu->name }}</div>
          </a>
        </li>
      @endif
    @endforeach
  </ul>
</aside>
