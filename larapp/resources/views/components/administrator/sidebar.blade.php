<nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="index.html">
              <i class="ti-dashboard menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.html">
              <i class="ti-map menu-icon"></i>
              <span class="menu-title">Regional</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.mosques.index') }}">
              <i class="ti-home menu-icon"></i>
              <span class="menu-title">Masjid</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.html">
              <i class="ti-layers menu-icon"></i>
              <span class="menu-title">Fasilitas</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.html">
              <i class="ti-time menu-icon"></i>
              <span class="menu-title">Aktivitas</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.html">
              <i class="ti-user menu-icon"></i>
              <span class="menu-title">User BKM</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.html">
              <i class="ti-book menu-icon"></i>
              <span class="menu-title">Artikel</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#unduh-menu" aria-expanded="false" aria-controls="unduh-menu">
              <i class="ti-download menu-icon"></i>
              <span class="menu-title">Unduh Data</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="unduh-menu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('admin.mosques.export', ['type' => 'MASJID']) }}">Unduh Data Masjid</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('admin.mosques.export', ['type' => 'MUSHOLLA']) }}">Unduh Data Mushalla</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.html">
              <i class="ti-email menu-icon"></i>
              <span class="menu-title">Kotak Masuk</span>
            </a>
          </li>
        </ul>
      </nav>