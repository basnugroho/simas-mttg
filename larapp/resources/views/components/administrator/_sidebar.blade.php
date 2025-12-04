  <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul
              class="nav sidebar-menu flex-column"
              data-lte-toggle="treeview"
              role="navigation"
              aria-label="Main navigation"
              data-accordion="false"
              id="navigation"
            >
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-speedometer2"></i>
                  <p>Dashboard</p>
                </a>
              </li>
              <li class="nav-item {{ request()->routeIs('admin.mosques.*') || request()->routeIs('admin.cash_positions.*') || request()->routeIs('admin.mosque_managers.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('admin.mosques.*') || request()->routeIs('admin.cash_positions.*') || request()->routeIs('admin.mosque_managers.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-building"></i>
                  <p>
                    Masjid
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ route('admin.mosques.index') }}" class="nav-link {{ request()->routeIs('admin.mosques.*') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-building"></i>
                      <p>Kelola Masjid</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('admin.cash_positions.index') }}" class="nav-link {{ request()->routeIs('admin.cash_positions.*') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-wallet2"></i>
                      <p>Cash</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('admin.mosque_managers.index') }}" class="nav-link {{ request()->routeIs('admin.mosque_managers.*') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-people"></i>
                      <p>Pengurus Masjid</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.articles.index') }}" class="nav-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-journal-text"></i>
                  <p>Article</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.mosques.export') }}" class="nav-link {{ request()->routeIs('admin.mosques.export') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-download"></i>
                  <p>Unduh Data</p>
                </a>
              </li>
              @php
                $unreadMessagesCount = 0;
                try {
                  $me = auth()->user();
                  $allExpanded = [];
                  if ($me) {
                    foreach ($me->regionsRoles()->get() as $ar) {
                      $rid = (int)$ar->region_id;
                      try { $desc = \App\Models\Regions::collectDescendantIds($rid); }
                      catch (\Throwable $__e) { $desc = [$rid]; }
                      $expanded = is_array($desc) ? $desc : (is_callable([$desc,'toArray']) ? $desc->toArray() : [$rid]);
                      $allExpanded = array_merge($allExpanded, $expanded);
                    }
                    $allExpanded = array_values(array_unique($allExpanded));
                  }
                  if (\Illuminate\Support\Facades\Schema::hasColumn('messages','is_read')) {
                    if (!empty($allExpanded)) {
                      $mq = \App\Models\Mosque::query();
                      $mq->whereIn('regional_id', $allExpanded)
                         ->orWhereIn('area_id', $allExpanded)
                         ->orWhereIn('witel_id', $allExpanded)
                         ->orWhereIn('sto_id', $allExpanded);
                      $mosqueIds = $mq->pluck('id')->toArray();
                      if (count($mosqueIds)) {
                        $unreadMessagesCount = \App\Models\Message::whereIn('mosque_id', $mosqueIds)->where('is_read', false)->count();
                      }
                    } else {
                      $unreadMessagesCount = \App\Models\Message::where('is_read', false)->count();
                    }
                  }
                } catch (\Throwable $e) { $unreadMessagesCount = 0; }
              @endphp
              <li class="nav-item">
                <a href="{{ route('admin.messages.index') }}" class="nav-link {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}" id="inbox-link-sidebar">
                  <i class="nav-icon bi bi-inbox"></i>
                  <p>Kotak Masuk <span id="inbox-badge-sidebar">@if($unreadMessagesCount) <span class="badge bg-danger ms-2">{{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}</span> @endif</span></p>
                </a>
              </li>
              <li class="nav-header">WEB MASTER</li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-gear"></i>
                  <p>
                    Master
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ route('admin.regions.index') }}" class="nav-link {{ request()->routeIs('admin.regions.*') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-map"></i>
                      <p>Regional</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('admin.facilities.index') }}" class="nav-link {{ request()->routeIs('admin.facilities.*') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-tools"></i>
                      <p>Fasilitas</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('admin.activities.index') }}" class="nav-link {{ request()->routeIs('admin.activities.*') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-calendar-event"></i>
                      <p>Aktifitas</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('admin.subsidiaries.index') }}" class="nav-link {{ request()->routeIs('admin.subsidiaries.*') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-journal"></i>
                      <p>Sub Diaries</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-people"></i>
                      <p>User BKM</p>
                    </a>
                  </li>
                </ul>
              </li>     
            </ul>
            <!--end::Sidebar Menu-->
            </nav>
            <script>
              (function(){
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                async function fetchUnread(){
                  try{
                    const res = await fetch('{{ url('admin/messages/unread-count') }}', { headers: { 'X-CSRF-TOKEN': token, 'Accept':'application/json' } });
                    if(!res.ok) return;
                    const data = await res.json();
                    const n = Number(data?.unread || 0);
                    const container = document.getElementById('inbox-badge-sidebar');
                    if(!container) return;
                    container.innerHTML = n ? ('<span class="badge bg-danger ms-2">' + (n>99 ? '99+' : n) + '</span>') : '';
                  }catch(e){ console.warn('unread fetch failed', e); }
                }
                // initial fetch after DOM ready
                document.addEventListener('DOMContentLoaded', function(){ fetchUnread(); setInterval(fetchUnread, 60*1000); });
              })();
            </script>