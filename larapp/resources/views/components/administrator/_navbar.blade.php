<nav class="app-header navbar navbar-expand bg-body">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="bi bi-list"></i>
              </a>
            </li>
            <li class="nav-item d-none d-md-block"><a href="/" class="nav-link">Home</a></li>
          </ul>
          <ul class="navbar-nav ms-auto">           
        
           
            <!--end::Fullscreen Toggle-->
            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
              @php $user = auth()->user(); @endphp
              <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <img
                  src="{{ asset('images/logo.png') }}"
                  class="user-image rounded-circle shadow"
                  alt="User Image"
                />
                <span class="d-none d-md-inline">{{ $user?->name ?? 'User' }}</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <!--begin::User Image-->
                <li class="user-header">
                  <img
                    src="{{ asset('images/logo.png') }}"
                    class="rounded-circle shadow"
                    alt="User Image"
                  />
                  <p>
                   {{ $user?->name ?? 'User' }}
                    <br>
                    @php
                      
                      $assignedMap = [];
                      $roleLabels = [
                        'admin_regional' => 'Admin Regional',
                        'admin_area' => 'Admin Area',
                        'admin_witel' => 'Admin Witel',
                        'admin_sto' => 'Admin STO',
                      ];
                      try {
                        if ($user) {
                          foreach ($user->regionsRoles as $rr) {
                            $rk = $rr->role_key ?? null;
                            if (! $rk) continue;
                            $label = $roleLabels[$rk] ?? $rk;
                            $regionName = null;
                            try { $regionName = $rr->region->name ?? null; } catch (\Throwable $e) { $regionName = null; }
                            if (! $regionName) $regionName = $rr->region_id ?? null;
                            if ($regionName) {
                              $assignedMap[$label][] = $regionName;
                            }
                          }
                        }
                      } catch (\Throwable $e) { $assignedMap = []; }
                    @endphp
                    <small>@if(count($assignedMap)) @foreach($assignedMap as $label => $names){{ $label }}: {{ implode(', ', array_values(array_unique($names))) }}@if(! $loop->last); @endif @endforeach @endif</small>
                  </p>
                </li>
                <!--end::User Image-->
                <!--begin::Menu Body-->
                <li class="user-body">
                  <div class="row">
                    <div class="d-flex align-items-center justify-content-between">
                    <div>
                      <a href="{{ route('admin.users.password.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary">Manage Password</a>
                    </div>
                    <div>
                      <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger">Sign out</button>
                      </form>
                    </div>
                  </div>
                  </div>
                </li>
                <!--end::Menu Body-->
                <!--begin::Menu Footer-->
                <li class="user-footer px-3 py-2">
                  
                </li>
                <!--end::Menu Footer-->
              </ul>
            </li>
            <!--end::User Menu Dropdown-->
          </ul>
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>
      <!-- mobile overlay element (toggled via small script) -->
      <div id="admin-mobile-overlay" aria-hidden="true" onclick="document.body.classList.remove('sidebar-open')"></div>
      <script>
        (function(){
          // lightweight toggle for mobile when AdminLTE isn't initializing overlay
          document.addEventListener('click', function(e){
            const toggle = e.target.closest('[data-lte-toggle="sidebar"]');
            if(!toggle) return;
            e.preventDefault();
            document.body.classList.toggle('sidebar-open');
          });
        })();
      </script>