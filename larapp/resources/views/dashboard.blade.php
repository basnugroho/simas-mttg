<x-admin.layout title="MTTG - Dashboard">
  @auth
  <div class="d-flex" id="app-layout" style="min-height:100vh">
    <!-- Left sidebar -->
  <aside id="sidebar" style="width:300px; background:#0b1220; color:#fff; padding:20px; display:flex; flex-direction:column; position:relative; transform:translateX(0); transition: transform .22s ease;">
  <!-- sidebar hide toggle removed to keep sidebar always open -->
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:18px">
          <img src="https://svgshare.com/i/14jG.svg" alt="logo" style="width:44px;height:44px;border-radius:8px;background:#fff;padding:6px;" onerror="this.style.display='none'">
          <div>
            <div style="font-weight:700">MTTG</div>
            <div style="font-size:12px; opacity:.8">Dashboard</div>
          </div>
        </div>

        <nav>
          <ul id="sidebar-menu" style="list-style:none;padding:0;margin:0;">
            <li style="margin-bottom:8px" data-key="dashboard"><a href="#" data-no-action="1" class="menu-link active" style="color:#fff;text-decoration:none;padding:10px 12px;display:flex;align-items:center;gap:10px;border-radius:8px"><span style="width:18px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke="#fff" stroke-width="1.5"/><path d="M7 14h3v-6H7v6zM14 17h3v-10h-3v10z" fill="#fff" opacity="0.95"/></svg></span>Dashboard</a></li>
            <li style="margin-bottom:8px" data-key="master">
              <a href="#" class="menu-link" aria-expanded="false" style="color:#fff;text-decoration:none;padding:10px 12px;display:flex;align-items:center;gap:10px;border-radius:8px">
                <span style="width:18px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><ellipse cx="12" cy="6.5" rx="7" ry="2.5" stroke="#fff" stroke-width="1.2"/><path d="M5 6.5v6c0 1.4 3.1 2.5 7 2.5s7-1.1 7-2.5v-6" stroke="#fff" stroke-width="1.2"/></svg></span>
                Master
              </a>
              <ul class="submenu" style="list-style:none;padding-left:14px;margin:6px 0 0 0;display:none;">
                <li data-key="regions" style="margin-bottom:6px"><a href="{{ route('admin.regions.index') }}" style="color:#cbd5e1;text-decoration:none;padding-left:18px;display:flex;align-items:center;gap:8px"><span style="width:14px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 2l3 6 6 .5-4.5 4 1 6L12 16l-5.5 3.5 1-6L3 8.5 9 8 12 2z" stroke="#cbd5e1" stroke-width="1" fill="none"/></svg></span>Regions</a></li>
                <li data-key="mosques" style="margin-bottom:6px"><a href="{{ route('admin.mosques.index') }}" style="color:#cbd5e1;text-decoration:none;padding-left:18px;display:flex;align-items:center;gap:8px"><span style="width:14px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 2c2 0 4 1.5 4 3.5S14 9 12 11 8 10 8 5.5 10 2 12 2z" stroke="#cbd5e1" stroke-width="1" fill="none"/></svg></span>Mosques</a></li>
                <li data-key="facilities" style="margin-bottom:6px"><a href="{{ route('admin.facilities.index') }}" style="color:#cbd5e1;text-decoration:none;padding-left:18px;display:flex;align-items:center;gap:8px"><span style="width:14px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"><rect x="3" y="7" width="18" height="11" rx="1" stroke="#cbd5e1" stroke-width="1" fill="none"/><path d="M8 7V5a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" stroke="#cbd5e1" stroke-width="1" fill="none"/></svg></span>Facilities</a></li>
                <li data-key="activities" style="margin-bottom:6px"><a href="{{ route('admin.activities.index') }}" style="color:#cbd5e1;text-decoration:none;padding-left:18px;display:flex;align-items:center;gap:8px"><span style="width:14px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 2v6" stroke="#cbd5e1" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 11h16v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-6z" stroke="#cbd5e1" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Activities</a></li>
                <li data-key="subsidiaries" style="margin-bottom:6px"><a href="{{ route('admin.subsidiaries.index') }}" style="color:#cbd5e1;text-decoration:none;padding-left:18px;display:flex;align-items:center;gap:8px"><span style="width:14px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="16" rx="2" stroke="#cbd5e1" stroke-width="1"/><path d="M7 8h10" stroke="#cbd5e1" stroke-width="1" stroke-linecap="round"/></svg></span>Subsidiaries</a></li>
              </ul>
            </li>
            <li style="margin-bottom:8px" data-key="masjid"><a href="{{ route('admin.mosques.index') }}" class="menu-link" style="color:#fff;text-decoration:none;padding:10px 12px;display:flex;align-items:center;gap:10px;border-radius:8px"><span style="width:18px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 2c2 0 4 1.5 4 3.5S14 9 12 11 8 10 8 5.5 10 2 12 2z" stroke="#fff" stroke-width="1.2" fill="none"/></svg></span>Masjid</a></li>
            <!-- Mushalla menu removed -->
            <li style="margin-bottom:8px" data-key="info">
              <div style="color:#94a3b8;padding:10px 12px;display:flex;align-items:center;gap:10px;border-radius:8px;cursor:not-allowed"><span style="width:18px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="16" rx="2" stroke="#94a3b8" stroke-width="1.5"/><path d="M7 8h10" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round"/></svg></span>Info Terkini <span style="margin-left:6px">🚧</span></div>
            </li>
            <li style="margin-bottom:8px" data-key="unduh">
              <div style="color:#94a3b8;padding:10px 12px;display:flex;align-items:center;gap:10px;border-radius:8px;cursor:not-allowed"><span style="width:18px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 3v12" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round"/><path d="M8 11l4 4 4-4" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 21H3" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round"/></svg></span>Unduh Data <span style="margin-left:6px">🚧</span></div>
            </li>
            <li style="margin-bottom:8px" data-key="inbox">
              <div style="color:#94a3b8;padding:10px 12px;display:flex;align-items:center;gap:10px;border-radius:8px;cursor:not-allowed"><span style="width:18px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 8l9 6 9-6" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><rect x="3" y="4" width="18" height="16" rx="2" stroke="#94a3b8" stroke-width="1.5"/></svg></span>Kotak Masuk <span style="margin-left:6px">🚧</span></div>
            </li>
            <li style="margin-bottom:8px" data-key="userbkm"><a href="{{ route('admin.users') }}" class="menu-link" style="color:#fff;text-decoration:none;padding:10px 12px;display:flex;align-items:center;gap:10px;border-radius:8px"><span style="width:18px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10z" stroke="#fff" stroke-width="1.5"/><path d="M4 20v-1a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v1" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>User BKM</a></li>
          </ul>
        </nav>

        <div style="border-top:1px solid rgba(255,255,255,.06); padding-top:12px">
          <!-- bottom area: logout with icon -->
          <form method="POST" action="{{ route('logout') }}" style="margin-top:8px">@csrf
            <button type="submit" style="background:none;border:none;color:#fff;padding:8px 6px;cursor:pointer;text-align:left;display:flex;align-items:center;gap:8px">
              <span style="width:16px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M16 17l5-5-5-5" stroke="#fff" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12H9" stroke="#fff" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 19H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3" stroke="#fff" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
              Logout
            </button>
          </form>
        </div>
      </aside>

  <!-- Main content -->
  <div class="main-content" style="flex:1; padding:22px; background:#f6f7fb">
        <header style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px">
          <div>
            <h1 style="margin:0; font-size:20px">Dashboard</h1>
            <!-- show current page info when a menu is clicked -->
            <div id="current-page-info" style="color:#6b7280; font-size:13px; margin-top:4px"></div>
          </div>
          <div style="display:flex;align-items:center;gap:8px">
            <!-- header toggle hidden because we now have an X button inside the sidebar -->
            <button id="toggleSidebarBtn" style="display:none;padding:6px 10px">Toggle Nav</button>
            <!-- hamburger appears when sidebar is hidden so user can restore it -->
            <button id="sidebarHamburgerBtn" aria-label="Open Nav" title="Open Nav" style="display:none; position:fixed; left:12px; top:12px; z-index:1200; width:44px; height:44px; border-radius:8px; background:#ef4444; color:#fff; border:0; cursor:pointer; font-size:18px; line-height:1">☰</button>
          </div>

          <div style="display:flex; align-items:center; gap:12px">
            <!-- user nav -->
            @php
              $me = auth()->user();
              $meRoles = [];
              if ($me) {
                try {
                  foreach ($me->regionsRoles as $rr) {
                    $meRoles[] = ['role' => $rr->role_key, 'region' => $rr->region?->name ?? $rr->region_id];
                  }
                } catch (\Throwable $__e) { $meRoles = []; }
              }
              // build allowedScope counts: expand all assigned regions and classify by region.level
              $allowedScope = ['regional'=>[], 'area'=>[], 'witel'=>[], 'sto'=>[]];
              if ($me) {
                try {
                  $allExpanded = [];
                  foreach ($me->regionsRoles()->get() as $ar) {
                    $rid = (int)$ar->region_id;
                    try { $desc = \App\Models\Regions::collectDescendantIds($rid); }
                    catch (\Throwable $e) { $desc = [$rid]; }
                    $expanded = is_array($desc) ? $desc : (is_callable([$desc, 'toArray']) ? $desc->toArray() : [$rid]);
                    $allExpanded = array_merge($allExpanded, $expanded);
                  }
                  $allExpanded = array_values(array_unique($allExpanded));

                  if (count($allExpanded)) {
                    $regions = \App\Models\Regions::whereIn('id', $allExpanded)->get(['id','level']);
                    foreach ($regions as $r) {
                      $lvl = strtoupper($r->level ?? '');
                      if ($lvl === 'REGIONAL') $allowedScope['regional'][] = (int)$r->id;
                      elseif ($lvl === 'AREA') $allowedScope['area'][] = (int)$r->id;
                      elseif ($lvl === 'WITEL') $allowedScope['witel'][] = (int)$r->id;
                      elseif ($lvl === 'STO') $allowedScope['sto'][] = (int)$r->id;
                    }
                    foreach (['regional','area','witel','sto'] as $k) {
                      $allowedScope[$k] = array_values(array_unique($allowedScope[$k]));
                    }
                  }
                } catch (\Throwable $__e) { }
              }
            @endphp

            <div style="display:flex; align-items:center; gap:8px; background:#fff;padding:6px 8px;border-radius:999px;box-shadow:0 6px 18px rgba(2,6,23,.06)">
              <img src="https://ui-avatars.com/api/?name={{ urlencode($me?->name ?? '') }}&background=ef4444&color=fff" style="width:30px;height:30px;border-radius:50%">
              <div style="text-align:left;min-width:160px;display:flex;align-items:center;gap:8px">
                <div style="display:flex;align-items:center;gap:8px">
                  <div style="font-weight:600;font-size:13px">{{ $me?->name ?? 'Guest' }}</div>
                  @php $primaryBadge = null; @endphp
                  @if(count($meRoles))
                    @php
                      foreach ($meRoles as $rr) {
                        if ($rr['role'] === 'admin_area') { $primaryBadge = $rr; break; }
                      }
                      if (!$primaryBadge) { $primaryBadge = $meRoles[0] ?? null; }
                    @endphp
                    @if($primaryBadge)
                      @php $bg = $primaryBadge['role'] === 'admin_regional' ? '#10b981' : ($primaryBadge['role'] === 'admin_area' ? '#f59e0b' : ($primaryBadge['role'] === 'admin_witel' ? '#3b82f6' : '#6b7280'));
                      @endphp
                      <span style="background:{{ $bg }};color:#fff;padding:2px 6px;border-radius:999px;font-size:10px;font-weight:600">{{ str_replace('admin_','', $primaryBadge['role']) . ': ' . $primaryBadge['region'] }}</span>
                    @endif
                  @endif
                </div>
                <div style="margin-left:auto;display:flex;gap:6px;align-items:center">
                  <small style="color:#9ca3af;font-size:11px">R</small><span style="background:#ecfdf5;color:#065f46;padding:2px 6px;border-radius:6px;font-weight:700;font-size:11px">{{ count($allowedScope['regional']) }}</span>
                  <small style="color:#9ca3af;font-size:11px">A</small><span style="background:#fff7ed;color:#92400e;padding:2px 6px;border-radius:6px;font-weight:700;font-size:11px">{{ count($allowedScope['area']) }}</span>
                  <small style="color:#9ca3af;font-size:11px">W</small><span style="background:#eff6ff;color:#1e40af;padding:2px 6px;border-radius:6px;font-weight:700;font-size:11px">{{ count($allowedScope['witel']) }}</span>
                  <small style="color:#9ca3af;font-size:11px">S</small><span style="background:#fff1f2;color:#9f1239;padding:2px 6px;border-radius:6px;font-weight:700;font-size:11px">{{ count($allowedScope['sto']) }}</span>
                </div>
              </div>
              <div style="margin-left:6px; position:relative">
                <button id="userToggle" style="background:none;border:0;cursor:pointer;font-size:18px">▾</button>
                <div id="userMenu" style="display:none; position:absolute; right:0; top:28px; background:#fff; border-radius:8px; box-shadow:0 8px 20px rgba(2,6,23,.12); overflow:hidden; padding:8px">
                  <div style="padding:6px 8px; color:#111;">
                    <div style="font-weight:700">{{ Auth::user()->name }}</div>
                    <div style="font-size:13px;color:#6b7280">{{ Auth::user()->email }}</div>
                    <div style="font-size:12px;color:#6b7280;margin-top:6px">Role: {{ Auth::user()->role }}</div>
                  </div>
                  <div style="border-top:1px solid #eee;margin-top:8px;padding-top:8px">
                    <form method="POST" action="{{ route('logout') }}" style="margin:0">
                      @csrf
                      <button type="submit" style="display:block;width:100%;border:0;background:none;padding:8px 12px;text-align:left;cursor:pointer;color:#111">Logout</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </header>

        <!-- small style helpers for menu and cards -->
        <style>
          /* Active menu should have readable white text on red gradient */
          .menu-link.active { background: linear-gradient(90deg,#ef4444 0,#f97316 100%); color: #fff !important; font-weight:600; box-shadow:0 6px 18px rgba(239,68,68,.12); }
          /* hide sidebar by sliding it left; width/padding will be handled by JS to restore exact original values */
          aside.sidebar-hidden { transform: translateX(-340px) !important; pointer-events: none; }
          /* when nav is hidden and hamburger is visible, nudge the page content right so hamburger doesn't cover it */
          /* tune padding to match 300px sidebar and hamburger width so content returns to normal when restored */
          .nav-hidden .main-content { padding-left: 88px; }
          .nav-hidden .main-content header { padding-left: 60px; }
          .nav-hidden .main-content header h1 { margin-left: 0; }
          .summary-cards { display:flex; gap:12px; margin-bottom:18px; }
          .summary-card { background:#fff;padding:14px;border-radius:10px; box-shadow:0 8px 24px rgba(2,6,23,.04); flex:1; display:flex; flex-direction:column; }
          .summary-card.total { flex: .9; align-items:center; justify-content:center; }
          @media (max-width:900px){ .summary-cards{flex-direction:column} }
        </style>

        <!-- summary cards (regions + total) -->
        <div class="summary-cards">
          <div class="summary-card">
            <div style="font-size:12px;color:#6b7280">Jawa Timur</div>
            <div style="font-weight:700;font-size:22px; display:flex; align-items:center; gap:10px">
              <span style="width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; background:#f3f4f6; border-radius:8px">
                <!-- building icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 21h18" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M6 21V9" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M10 21V5" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M14 21V11" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M18 21V7" stroke="#111" stroke-width="1.2" stroke-linecap="round"/></svg>
              </span>
              <span><span id="jt-count">—</span> <small style="font-size:12px;color:#6b7280">Total</small></span>
            </div>
            <div style="font-size:12px;color:#6b7280">Masjid: <span id="jt-masjid">—</span> | Mushalla: <span id="jt-mushalla">—</span></div>
          </div>
          <div class="summary-card">
            <div style="font-size:12px;color:#6b7280">Bali</div>
            <div style="font-weight:700;font-size:22px; display:flex; align-items:center; gap:10px">
              <span style="width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; background:#f3f4f6; border-radius:8px">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 21h18" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M6 21V9" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M10 21V5" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M14 21V11" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M18 21V7" stroke="#111" stroke-width="1.2" stroke-linecap="round"/></svg>
              </span>
              <span><span id="bali-count">—</span> <small style="font-size:12px;color:#6b7280">Total</small></span>
            </div>
            <div style="font-size:12px;color:#6b7280">Masjid: <span id="bali-masjid">—</span> | Mushalla: <span id="bali-mushalla">—</span></div>
          </div>
          <div class="summary-card">
            <div style="font-size:12px;color:#6b7280">Nusa Tenggara</div>
            <div style="font-weight:700;font-size:22px; display:flex; align-items:center; gap:10px">
              <span style="width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; background:#f3f4f6; border-radius:8px">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 21h18" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M6 21V9" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M10 21V5" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M14 21V11" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M18 21V7" stroke="#111" stroke-width="1.2" stroke-linecap="round"/></svg>
              </span>
              <span><span id="nt-count">—</span> <small style="font-size:12px;color:#6b7280">Total</small></span>
            </div>
            <div style="font-size:12px;color:#6b7280">Masjid: <span id="nt-masjid">—</span> | Mushalla: <span id="nt-mushalla">—</span></div>
          </div>
          <div class="summary-card total" style="background:#000;color:#fff">
            <div style="font-size:12px;color:#cbd5e1">Total Keseluruhan</div>
            <div style="font-weight:800;font-size:28px; display:flex; align-items:center; gap:10px"><span style="width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; background:#111; border-radius:8px"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 21h18" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/><path d="M6 21V9" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/><path d="M10 21V5" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/><path d="M14 21V11" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/><path d="M18 21V7" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/></svg></span><span id="total-count">—</span></div>
            <div style="font-size:12px;color:#cbd5e1">Masjid: <span id="total-masjid">—</span> | Mushalla: <span id="total-mushalla">—</span></div>
          </div>
        </div>

        <!-- Top charts area -->
        <div style="display:flex; gap:18px; margin-bottom:18px">
          <div id="stackedCard" style="flex:2; background:#0f1724;padding:18px;border-radius:12px; box-shadow:0 8px 24px rgba(2,6,23,.04); color:#e5e7eb">
            <h4 style="margin:0 0 12px 0; color:#e5e7eb">Data Masjid & Mushalla Jatim, Bali dan Nusa Tenggara</h4>
            <canvas id="stackedBar" height="320" style="display:block; width:100%; height:320px;"></canvas>
          </div>
          <div style="flex:1; display:flex; flex-direction:column; gap:12px">
            <div style="background:#fff;padding:12px;border-radius:12px; box-shadow:0 8px 24px rgba(2,6,23,.04)">
              <div id="pie1-total" style="text-align:center;font-weight:700;font-size:18px;margin-bottom:8px;color:#111">— Masjid</div>
              <div style="padding:6px;border-radius:8px;background:#fff"><canvas id="pie1" height="120"></canvas></div>
            </div>
            <!-- Mushalla pie removed per request to lift table/map -->
          </div>
        </div>

        <!-- bottom area: table + map -->
        <div style="display:flex; gap:18px">
          <div style="flex:1; background:#fff;padding:12px;border-radius:12px; box-shadow:0 8px 24px rgba(2,6,23,.04); max-height:420px; overflow:auto">
            <h4 style="margin-top:0">Masjid / Mushalla (Fasilitas Dummy)</h4>
            <table class="table table-sm">
              <thead>
                <tr><th>#</th><th>Nama</th><th>Lokasi</th><th>Kelengkapan</th></tr>
              </thead>
              <tbody>
                @for($i=1;$i<=10;$i++)
                  <tr>
                    <td>{{ $i }}</td>
                    <td>Masjid Contoh {{ $i }}</td>
                    <td>Kota {{ ['Surabaya','Malang','Bali','Denpasar','Mataram'][($i-1)%5] }}</td>
                    <td>{{ rand(60,100) }}%</td>
                  </tr>
                @endfor
              </tbody>
            </table>
          </div>

          <div style="flex:1; background:#fff;padding:12px;border-radius:12px; box-shadow:0 8px 24px rgba(2,6,23,.04);">
            <h4 style="margin-top:0">Peta</h4>
            <div id="map" style="height:360px;border-radius:8px;overflow:hidden"></div>
          </div>
        </div>

      </div>
    </div>

    <!-- scripts for charts and map -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
      // register datalabels plugin
      if(window && window.Chart && window.ChartDataLabels){
        Chart.register(window.ChartDataLabels);
      }
    </script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
      // user menu toggle
      document.getElementById('userToggle').addEventListener('click', function(){
        const m = document.getElementById('userMenu');
        m.style.display = m.style.display === 'block' ? 'none' : 'block';
      });

      // sidebar toggle (persisted)
      (function(){
        // toggle sidebar visibility using the X button inside the sidebar
        const innerBtn = document.getElementById('sidebarToggleX');
        const headerBtn = document.getElementById('toggleSidebarBtn');
        const hamburgerBtn = document.getElementById('sidebarHamburgerBtn');
        const sidebar = document.getElementById('sidebar');
        if(!sidebar) return;

        // store original inline width/padding immediately so AJAX hide can restore them later
        try{
          if(!sidebar.dataset.origWidth) sidebar.dataset.origWidth = sidebar.style.width || getComputedStyle(sidebar).width || '300px';
          if(!sidebar.dataset.origPadding) sidebar.dataset.origPadding = sidebar.style.padding || getComputedStyle(sidebar).padding || '20px';
        }catch(e){}
        // also store main-content original padding so we can fully restore layout (avoid residual offset)
        try{
          const main = document.querySelector('.main-content');
          if(main && !main.dataset.origPadding) main.dataset.origPadding = main.style.paddingLeft || getComputedStyle(main).paddingLeft || '22px';
        }catch(e){}

        function updateControls(){
          const hidden = sidebar.classList.contains('sidebar-hidden');
          // show hamburger when hidden, hide inner X; reverse when visible
          if(hamburgerBtn) hamburgerBtn.style.display = hidden ? 'block' : 'none';
          if(innerBtn) innerBtn.style.display = hidden ? 'none' : 'block';
          // add/remove a body class so CSS can nudge the title when hamburger is visible
          try{ if(hidden) document.documentElement.classList.add('nav-hidden'); else document.documentElement.classList.remove('nav-hidden'); }catch(e){}
        }

        function setHidden(hidden){
          // store original inline width/padding once so we can restore them exactly
          try{
            if(!sidebar.dataset.origWidth) sidebar.dataset.origWidth = sidebar.style.width || getComputedStyle(sidebar).width || '320px';
            if(!sidebar.dataset.origPadding) sidebar.dataset.origPadding = sidebar.style.padding || getComputedStyle(sidebar).padding || '20px';
          }catch(e){}

          if(hidden){
            // collapse visually by setting inline width/padding to 0 and add class for transform/pointer-events
            try{ sidebar.style.width = '0px'; sidebar.style.padding = '0px'; }catch(e){}
            sidebar.classList.add('sidebar-hidden');
            // nudge main content to compensate (also recorded earlier)
            try{ const main = document.querySelector('.main-content'); if(main) main.style.paddingLeft = '88px'; }catch(e){}
          } else {
            // remove class then restore inline width/padding from stored originals
            sidebar.classList.remove('sidebar-hidden');
            try{
              // restore inline width/padding exactly as recorded at init
              sidebar.style.width = (sidebar.dataset.origWidth || '300px');
              sidebar.style.padding = (sidebar.dataset.origPadding || '20px');
              // clear any inline transform that might interfere
              sidebar.style.transform = '';
            }catch(e){}
            // also remove any nav-hidden adjustments on the document so main content returns to normal
            try{ document.documentElement.classList.remove('nav-hidden'); }catch(e){}
            // restore main-content padding to original value so layout exactly returns to previous state
            try{
              const main = document.querySelector('.main-content');
              if(main){
                if(main.dataset.origPadding) main.style.paddingLeft = main.dataset.origPadding;
                else main.style.paddingLeft = '22px';
                // ensure flex layout can recompute properly
                main.style.flex = main.style.flex || '1 1 auto';
                main.style.width = '';
                // force a layout reflow so the browser recalculates sizes
                void main.offsetWidth;
              }
            }catch(e){}
          }

          // sidebar hidden preference persists disabled: keep sidebar always open
          updateControls();
          // let the layout settle then trigger a resize/reflow so charts and maps recompute to the new width
          try{
            setTimeout(()=>{
              // tell browser to recalc layout
              void document.body.offsetWidth;
              // dispatch a resize event for libraries that listen to it
              window.dispatchEvent(new Event('resize'));
              // Chart.js instances (if present) should be resized
              if(window.stackedBar && typeof window.stackedBar.resize === 'function') window.stackedBar.resize();
              if(window.pie1 && typeof window.pie1.resize === 'function') window.pie1.resize();
              // Leaflet map, if present, needs invalidateSize
              if(window.map && typeof window.map.invalidateSize === 'function') window.map.invalidateSize();
            }, 260);
          }catch(e){}
        }

        // initialize: force sidebar to be visible (disable persisted hide)
        try{ setHidden(false); }catch(e){ updateControls(); }

        if(innerBtn) innerBtn.addEventListener('click', function(e){
          e.stopPropagation();
          const hidden = sidebar.classList.contains('sidebar-hidden');
          setHidden(!hidden);
        });

        if(hamburgerBtn) hamburgerBtn.addEventListener('click', function(e){
          e.stopPropagation();
          setHidden(false);
        });

        // also allow header button (hidden) to toggle for compatibility
        if(headerBtn) headerBtn.addEventListener('click', function(){
          const hidden = sidebar.classList.contains('sidebar-hidden');
          setHidden(!hidden);
        });
      })();

      // AJAX page loader removed — navigation will use full page loads.
      (function(){
        // No-op: the previous SPA-like AJAX page loader was removed to keep navigation simple and to avoid
        // dynamic content replacement when clicking sidebar items (notably Dashboard).
        // Use regular links and full page navigation.
      })();

      // sidebar menu active handling (persist in localStorage) and submenu toggle
      (function(){
        const menu = document.getElementById('sidebar-menu');
        if(!menu) return;
        function setActive(key){
          // remove active from all top-level menu links
          menu.querySelectorAll('a.menu-link').forEach(a=> a.classList.remove('active'));
          // try to find an li with matching data-key (at top-level or submenu)
          const targetLi = menu.querySelector('li[data-key="'+key+'"]');
          if(targetLi){
            // if target is a submenu item, highlight its parent top-level link too
            const topLevel = targetLi.closest('ul')?.closest('li');
            if(topLevel){
              const topA = topLevel.querySelector('a.menu-link');
              if(topA) topA.classList.add('active');
            }
            const a = targetLi.querySelector('a') || targetLi.querySelector('a.menu-link');
            if(a) a.classList.add('active');
          }
          try{ localStorage.setItem('simas_active', key); }catch(e){}
        }

        // Attach click handlers to top-level menu links only
        menu.querySelectorAll('a.menu-link').forEach(a=>{
          a.addEventListener('click', function(e){
            const parentLi = a.closest('li');
            const submenu = parentLi ? parentLi.querySelector('.submenu') : null;
            // if this item has a submenu, toggle it instead of navigating
            if(submenu){
              e.preventDefault();
              const isOpen = submenu.style.display === 'block';
              submenu.style.display = isOpen ? 'none' : 'block';
              a.setAttribute('aria-expanded', String(!isOpen));
              try{ localStorage.setItem('simas_submenu_master_open', String(!isOpen)); }catch(err){}
              // mark top-level active when opened
              if(!isOpen) setActive(parentLi.dataset.key || 'master');
              return;
            }
            // If it's a normal link (no submenu), let it navigate but record active state
            // find nearest li with data-key to set active
            let key = parentLi?.dataset?.key;
            if(!key){
              // maybe it's a submenu li; find the closest li with data-key
              const closestKeyLi = a.closest('li[data-key]');
              key = closestKeyLi ? closestKeyLi.dataset.key : null;
            }
            if(key) try{ localStorage.setItem('simas_active', key); }catch(e){}
          });
        });

        // restore saved active and submenu state
        const saved = localStorage.getItem('simas_active') || 'dashboard';
        setActive(saved);
        const masterOpen = localStorage.getItem('simas_submenu_master_open');
        const masterLi = menu.querySelector('li[data-key="master"]');
        if(masterLi){
          const submenu = masterLi.querySelector('.submenu');
          const a = masterLi.querySelector('a.menu-link');
          if(submenu){
            if(masterOpen === 'true') { submenu.style.display = 'block'; if(a) a.setAttribute('aria-expanded','true'); }
            else { submenu.style.display = 'none'; if(a) a.setAttribute('aria-expanded','false'); }
          }
        }
      })();

      // stacked/grouped bar chart (Masjid & Mushalla per region)
      const ctx = document.getElementById('stackedBar').getContext('2d');
      const stackedBar = new Chart(ctx, {
        type: 'bar',
        data: {
          // x-axis: regions
          labels: ['Jawa Timur','Bali','Nusa Tenggara'],
          datasets: [
            // Masjid stack (shows Lengkap then Belum Lengkap stacked)
            { label: 'Masjid (Lengkap)', data: [5,2,3], backgroundColor: '#ef4444', stack: 'masjid' },
            { label: 'Masjid (Belum Lengkap)', data: [3,1,1], backgroundColor: '#fca5a5', stack: 'masjid' },
            // Mushalla stack
            { label: 'Mushalla (Lengkap)', data: [4,3,2], backgroundColor: '#10b981', stack: 'mushalla' },
            { label: 'Mushalla (Belum Lengkap)', data: [3,1,4], backgroundColor: '#9fe6c9', stack: 'mushalla' },
          ]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              display: true,
              position: 'bottom',
              labels: { usePointStyle: true, pointStyle: 'circle', color: '#e5e7eb' }
            },
            tooltip: { enabled: true }
          },
          // grouped stacks: x not stacked so different 'stack' groups are side-by-side,
          // y stacked so datasets with same stack value stack on top of each other
          scales: {
            x: { stacked: false, ticks: { color: '#cbd5e1' }, grid: { color: 'rgba(255,255,255,0.03)' } },
            y: { stacked: true, beginAtZero:true, ticks: { color: '#cbd5e1' }, grid: { color: 'rgba(255,255,255,0.03)' } }
          },
          // spacing tweaks so two stacks per region look balanced
          datasets: {
            bar: { categoryPercentage: 0.6, barPercentage: 0.9 }
          }
        }
      });

      // pies with data labels (values only) and right-side legend

  const pie1 = new Chart(document.getElementById('pie1'), {
        type: 'doughnut',
        data: { labels:['Jawa Timur','Bali','Nusa Tenggara'], datasets:[{data:[8,3,4], backgroundColor:['#3b82f6','#ef4444','#10b981']}] },
        options:{
          responsive:true,
          plugins: {
            legend: { position: 'right', labels: { usePointStyle: true, pointStyle: 'circle' } },
            datalabels: {
              color: '#fff',
              formatter: (value) => { return value; },
              font: { weight: '600', size: 12 }
            }
          }
        },
        plugins: [ChartDataLabels]
      });

      // set pie total text above each pie (will run after both pies are created)

      // compute and set pie total for Masjid
      try{
        const sumPie = (chart) => (chart && chart.data && chart.data.datasets[0].data || []).reduce((a,b)=>a+Number(b||0),0);
        const total1 = sumPie(pie1);
        const el1 = document.getElementById('pie1-total');
        if(el1) { el1.innerText = total1 + ' Masjid'; el1.style.fontSize = '18px'; el1.style.fontWeight = '700'; }
      }catch(e){ console.warn('set pie totals failed', e); }

      // Ensure bar chart doesn't show datalabels and update summary cards from datasets
      try{
  if(stackedBar && stackedBar.data && stackedBar.data.datasets){
          // explicitly disable datalabels for bar
          if(stackedBar.options.plugins) stackedBar.options.plugins.datalabels = { display: false };

          // compute sums per region
          const ds = stackedBar.data.datasets;
          const regions = stackedBar.data.labels; // ['Jawa Timur','Bali','Nusa Tenggara']
          const idMap = ['jt','bali','nt'];
          let grandTotal = 0;
          let grandLengkap = 0;
          idMap.forEach((id, idx) => {
            let total = 0, masjid = 0, mushalla = 0;
            ds.forEach(d => {
              const v = Number(d.data[idx] || 0);
              total += v;
              if(/Masjid/i.test(d.label)) masjid += v;
              if(/Mushalla|Mushola|Musholla/i.test(d.label)) mushalla += v;
            });
            grandTotal += total;
            // accumulate totals for grand breakdown
            // attach per-region values to DOM
            const countEl = document.getElementById(id + '-count');
            const masjidEl = document.getElementById(id + '-masjid');
            const mushEl = document.getElementById(id + '-mushalla');
            if(countEl) countEl.innerText = total;
            if(masjidEl) masjidEl.innerText = masjid;
            if(mushEl) mushEl.innerText = mushalla;
            // sum grand breakdown
            const grandMasjidEl = document.getElementById('total-masjid');
            const grandMushEl = document.getElementById('total-mushalla');
            if(!window.__grandMasjid) window.__grandMasjid = 0;
            if(!window.__grandMush) window.__grandMush = 0;
            window.__grandMasjid += masjid;
            window.__grandMush += mushalla;
          });
          const totalEl = document.getElementById('total-count');
          if(totalEl) totalEl.innerText = grandTotal;
          const grandMasjidEl = document.getElementById('total-masjid');
          const grandMushEl = document.getElementById('total-mushalla');
          if(grandMasjidEl) grandMasjidEl.innerText = window.__grandMasjid || 0;
          if(grandMushEl) grandMushEl.innerText = window.__grandMush || 0;
        }
      }catch(e){ console.warn('update cards failed', e); }

      // leaflet map (dummy)
      const map = L.map('map').setView([-7.25,112.75],7);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
      // example marker removed
    </script>

  @else
    <div class="container py-6">
      <div class="card p-4">
        <h4>Autentikasi Diperlukan</h4>
        <p>Anda perlu <a href="{{ route('login') }}">login</a> untuk mengakses dashboard.</p>
      </div>
    </div>
  @endauth
</x-admin.layout>