<x-admin.layout title="Management User BKM">
  <div class="container admin-content">
    

    @if(session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <div style="display:flex;flex-direction:column;justify-content:center;gap:6px">
        <h2 style="margin:0">Management User BKM</h2>
        <div style="font-size:12px;color:#6b7280;margin-top:0;">Master · <a href="{{ route('dashboard') }}">Dashboard</a> / <strong>Management User BKM</strong></div>
      </div>
      <div style="display:flex;gap:8px;align-items:center">
        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">Create User</a>
      </div>
    </div>

    {{-- Search + Filter area: separate Search form and Filter form per request --}}
    <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px;flex-wrap:wrap">
      {{-- Search form: only submits 'q' --}}
      <form id="search-form" method="GET" action="{{ route('admin.users') }}" style="display:flex;align-items:center;gap:6px">
        <input type="search" name="q" placeholder="Search name, username, email" value="{{ request('q') }}" class="form-control form-control-sm" style="min-width:260px" />
        <button class="btn btn-sm btn-primary" type="submit">Search</button>
      </form>

      {{-- Filter form: submits filter_role and filter_region. Reset is placed next to Filter per request. --}}
      <form id="filter-form" method="GET" action="{{ route('admin.users') }}" style="display:flex;align-items:center;gap:8px">
        <div style="display:flex;align-items:center;gap:6px">
          <label class="small" style="margin-bottom:0">Role</label>
          <select name="filter_role" id="filter-role-select" class="form-select form-select-sm">
            <option value="">All</option>
            @php $roleOptions = ['admin_regional' => 'Admin Regional', 'admin_area' => 'Admin Area', 'admin_witel' => 'Admin Witel', 'admin_sto' => 'Admin STO']; @endphp
            @foreach($roleOptions as $rk => $rl)
              <option value="{{ $rk }}" {{ request('filter_role') === $rk ? 'selected' : '' }}>{{ $rl }}</option>
            @endforeach
          </select>
        </div>
        <div style="display:flex;align-items:center;gap:6px">
          <label class="small" style="margin-bottom:0">Scope</label>
          <select name="filter_region" id="filter-region-select" class="form-select form-select-sm">
            <option value="">All</option>
            {{-- options populated by JS based on selected role (deterministic mapping) --}}
          </select>
        </div>

        <div style="display:flex;align-items:center;gap:6px;margin-left:6px">
          <button type="submit" class="btn btn-sm btn-primary">Filter</button>
          <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
      </form>
    </div>

    <table class="table">
      <thead>
        <tr>
          @php
            $currentSort = request('sort');
            $currentDir = request('dir','desc');
            function sortLink($col, $label) {
              $currentSort = request('sort');
              $currentDir = request('dir','desc');
              $newdir = ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc';
              $url = request()->fullUrlWithQuery(['sort' => $col, 'dir' => $newdir]);
              $arrow = '';
              if ($currentSort === $col) $arrow = ($currentDir === 'asc') ? ' ▲' : ' ▼';
              return '<a href="' . e($url) . '">' . e($label) . $arrow . '</a>';
            }
          @endphp
          <th>{!! sortLink('id', '#') !!}</th>
          <th>{!! sortLink('name', 'Name') !!}</th>
          <th>{!! sortLink('username', 'Username') !!}</th>
          <th>region_roles</th>
          <th>Approved</th>
          <th style="max-width:260px;">Scope</th>
          <th style="white-space:nowrap">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $u)
          <tr>
            @php $canDelete = in_array($u->id, $manageableUserIds ?? []) || (auth()->user() && auth()->user()->isWebmaster()); @endphp
            <td>{{ $u->id }}</td>
            <td>{{ $u->name }}</td>
            <td>{{ $u->username }}</td>
            <td>
              @php
                // Prefer the explicit account role on users table (e.g. webmaster) first.
                // Only fall back to region_roles (user_region_roles) when the account role is not webmaster.
                $badgeClassMap = [
                  'webmaster' => 'bg-dark text-white',
                  'admin_regional' => 'bg-success text-white',
                  'admin_area' => 'bg-warning text-dark',
                  'admin_witel' => 'bg-primary text-white',
                  'admin_sto' => 'bg-secondary text-white',
                ];
                if (isset($u->role) && $u->role === 'webmaster') {
                  $userRegionRoles = ['webmaster'];
                } else {
                  $userRegionRoles = $u->regionsRoles->pluck('role_key')->filter()->unique()->values()->toArray();
                }
              @endphp
              @if(count($userRegionRoles))
                @foreach($userRegionRoles as $rr)
                  @php $cls = $badgeClassMap[$rr] ?? 'bg-light text-dark'; @endphp
                  <span class="badge {{ $cls }}" style="font-size:0.75em;padding:3px 6px;margin-right:4px">{{ $rr }}</span>
                @endforeach
              @else
                -
              @endif
            </td>
            <td>
              <div class="form-check form-switch" style="display:flex;align-items:center;gap:8px">
                <input type="checkbox" class="form-check-input approved-toggle" data-user-id="{{ $u->id }}" data-role="{{ $u->role }}" id="approved-toggle-{{ $u->id }}" {{ $u->approved ? 'checked' : '' }}>
                <label class="form-check-label small" for="approved-toggle-{{ $u->id }}">{{ $u->approved ? 'Yes' : 'No' }}</label>
                <span class="approved-spinner" id="approved-spinner-{{ $u->id }}" style="display:none;margin-left:6px">⏳</span>
              </div>
            </td>
            <td>
              @php
                // Render scopes; include Witel badges compacted for Surabaya Utara
                $roles = $u->regionsRoles->map(function($ar){
                  return ['label' => ($ar->region->name ?? $ar->region_id), 'role_key' => ($ar->role_key ?? '')];
                })->toArray();
                $scopeLabels = [];
                foreach($roles as $r) {
                  if ($r['role_key'] === 'admin_witel') continue; // witel will be shown as badges
                  $scopeLabels[] = $r['label'] . ($r['role_key'] ? ' (' . $r['role_key'] . ')' : '');
                }
                $witels = $u->regionsRoles->filter(fn($ar) => ($ar->role_key ?? '') === 'admin_witel')->map(fn($ar) => $ar->region->name ?? null)->filter()->unique()->values()->toArray();
              @endphp
              <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center">
                  @php
                    // Build badge items with role_key so we can color them per-type and keep size uniform
                    $badgeItems = $u->regionsRoles->map(function($ar){
                        return ['label' => ($ar->region->name ?? $ar->region_id), 'role_key' => ($ar->role_key ?? '')];
                    })->unique(fn($v) => $v['label'])->values()->toArray();
                    $countBadges = count($badgeItems);
                    $limit = 3;
                    $colorMap = ['admin_witel' => 'bg-primary text-white', 'admin_area' => 'bg-warning text-dark', 'admin_regional' => 'bg-success text-white', 'admin_sto' => 'bg-secondary text-white'];
                  @endphp
                  @if($countBadges)
                    <div style="display:flex;align-items:center;gap:6px;max-width:240px;overflow:hidden;white-space:nowrap">
                      <div style="display:flex;gap:6px;align-items:center;">
                        @foreach(array_slice($badgeItems, 0, $limit) as $sc)
                          @php $cls = $colorMap[$sc['role_key']] ?? 'bg-light text-dark'; @endphp
                          <span class="badge {{ $cls }}" style="font-size:0.85em;padding:3px 8px;border:1px solid #ddd">{{ $sc['label'] }}</span>
                        @endforeach
                      </div>
                      @if($countBadges > $limit)
                        {{-- store full badges payload on button for overlay rendering --}}
                        <button type="button" class="btn btn-sm btn-outline-secondary scope-expand-btn" data-badges='@json($badgeItems)' data-count="{{ $countBadges - $limit }}">+{{ $countBadges - $limit }}</button>
                      @endif
                    </div>
                  @else
                    -
                  @endif
              </div>
            </td>
            {{-- Created At and Last Login moved to Detail panel; main table keeps fewer columns per request --}}
            <td>
              <button type="button" class="btn btn-sm btn-primary btn-priv" data-user-id="{{ $u->id }}">Privilage</button>
              <button type="button" class="btn btn-sm btn-info btn-detail" data-user-id="{{ $u->id }}" style="margin-left:6px">Detail</button>
              @php
                $assignedByRole = $u->regionsRoles->groupBy('role_key')->map(function($g){
                    return $g->pluck('region_id')->map(fn($v)=>(int)$v)->values()->toArray();
                })->toArray();
              @endphp
              {{-- privilege panel moved to expandable table row below; placeholder kept here --}}
              <div style="display:inline-block;" data-has-priv-panel="true"></div>
              @if($canDelete)
                <form method="POST" action="{{ route('admin.users.delete_single', $u->id) }}" class="single-delete-form" style="display:inline-block;margin-left:8px">
                  @csrf
                  <button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
              @endif
            </td>
          </tr>
          <tr id="privrow-{{ $u->id }}" class="privilege-row" style="display:none">
            <td colspan="7" style="padding:12px 16px;background:#fafafa;border-top:1px solid #eee">
              <div class="privilege-panel-inner" data-assigned='@json($assignedByRole)'>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                  <strong>Privilage: {{ $u->name }} ({{ $u->username }})</strong>
                  <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-close-priv">Close</button>
                  </div>
                </div>

                <form method="POST" action="{{ route('admin.users.update', $u->id) }}" style="margin-bottom:8px">
                  @csrf
                  <div class="mb-2">
                    <label class="form-label small">Account role</label>
                    <select name="role" class="form-select form-select-sm">
                      <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>user</option>
                      <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>admin</option>
                      <option value="webmaster" {{ $u->role === 'webmaster' ? 'selected' : '' }}>webmaster</option>
                    </select>
                  </div>
                  <div class="form-check form-switch mb-2">
                    <input type="checkbox" name="approved" value="1" class="form-check-input" {{ $u->approved ? 'checked' : '' }} id="approved-{{ $u->id }}">
                    <label class="form-check-label small" for="approved-{{ $u->id }}">Approved</label>
                  </div>
                  <div>
                    <button class="btn btn-sm btn-primary">Save</button>
                  </div>
                </form>

                <form method="POST" action="{{ route('admin.users.roles.store', $u->id) }}">
                  @csrf
                  <div class="mb-2">
                    <label class="form-label small">Assign privilege</label>
                    <div style="display:flex;flex-direction:column;gap:8px">
                      @php $roleLabels = ['admin_regional' => 'Admin Regional', 'admin_area' => 'Admin Area', 'admin_witel' => 'Admin Witel', 'admin_sto' => 'Admin STO']; @endphp
                      <select name="role_key" class="form-select form-select-sm role-key-select">
                        @foreach($roleLabels as $rk => $rl)
                          @php $allowed = $allowedByTargetRole[$rk] ?? null; @endphp
                          @if(is_null($allowed) || (is_array($allowed) && count($allowed) > 0))
                            <option value="{{ $rk }}">{{ $rl }}</option>
                          @endif
                        @endforeach
                      </select>
                      <div style="margin-top:6px">
                        <div class="mb-1"><label><input type="checkbox" class="region-select-all" /> Select all</label></div>
                        <div class="assign-region-container" style="max-height:240px;overflow:auto;border:1px solid #eee;padding:6px;border-radius:4px;background:#fff;">
                          <!-- populated by JS -->
                        </div>
                      </div>
                    </div>
                  </div>
                  <div>
                    <button class="btn btn-sm btn-success">Assign</button>
                  </div>
                </form>

                <div class="mt-2">
                  <label class="form-label small">Existing assignments</label>
                  <div style="display:flex;flex-wrap:wrap;gap:6px">
                    @foreach($u->regionsRoles as $ar)
                      <form method="POST" action="{{ route('admin.users.roles.destroy', $ar->id) }}" style="display:inline-block">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" title="Remove">{{ $ar->role_key }}: {{ $ar->region->name ?? $ar->region_id }}</button>
                      </form>
                    @endforeach
                    @if(count($u->regionsRoles) === 0)
                      <div style="color:#666">No assignments</div>
                    @endif
                  </div>
                </div>
              </div>
            </td>
          </tr>
          <tr id="detailrow-{{ $u->id }}" class="detail-row" style="display:none">
            <td colspan="7" style="padding:12px 16px;background:#fff;border-top:1px solid #eee">
              @php
                $createdAtFull = $u->created_at ? $u->created_at->toDateTimeString() : '-';
                $la = $lastActivities[$u->id] ?? null;
                $lastLoginFull = $la ? (\Carbon\Carbon::createFromTimestamp($la)->toDateTimeString()) : '-';
                // assigned_by: query the latest assignment record for this user (if any) from DB to be robust
                $assignedBy = null;
                $assignedAt = null;
                $assignerName = null;
                $assignerRoleDisplay = null;
                try {
                  $latestRecord = \DB::table('user_region_roles')->where('user_id', $u->id)->orderByDesc('created_at')->orderByDesc('id')->first();
                } catch (\Throwable $e) {
                  $latestRecord = null;
                }
                if ($latestRecord) {
                  // use created_by as the assigner (stored as user id) and created_at as assigned timestamp
                  $assignedBy = $latestRecord->created_by ?? null;
                  $assignedAt = isset($latestRecord->created_at) ? (string)$latestRecord->created_at : null;
                  // resolve assigner name if numeric id
                  if ($assignedBy && is_numeric($assignedBy)) {
                    try {
                      $assUser = \App\Models\User::find((int)$assignedBy);
                      if ($assUser) {
                        $assignerName = $assUser->name ?? ($assUser->username ?? ('user#' . $assUser->id));
                        if (($assUser->role ?? null) === 'webmaster') {
                          $assignerRoleDisplay = 'webmaster';
                        } else {
                          // try to pick one region role_key for display
                          $assignerRoleDisplay = $assUser->regionsRoles->pluck('role_key')->filter()->unique()->values()->first() ?? ($assUser->role ?? null);
                        }
                      } else {
                        $assignerName = (string)$assignedBy;
                      }
                    } catch (\Throwable $e) {
                      $assignerName = (string)$assignedBy;
                    }
                  } elseif ($assignedBy) {
                    // assigned_by stored as string
                    $assignerName = (string)$assignedBy;
                  }
                }
                // collect region ids for this user's region roles to find mosques
                $regionIds = $u->regionsRoles->pluck('region_id')->filter()->unique()->values()->toArray();
                $mosqueNames = [];
                if (count($regionIds)) {
                  try {
                    $mosqueNames = \DB::table('mosques')->whereIn('region_id', $regionIds)->pluck('name')->take(20)->toArray();
                  } catch (\Throwable $e) { $mosqueNames = []; }
                }
              @endphp
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                <strong>Detail: {{ $u->name }} ({{ $u->username }})</strong>
                <div><button type="button" class="btn btn-sm btn-outline-secondary btn-close-detail">Close</button></div>
              </div>
              <div style="display:flex;gap:18px;flex-wrap:wrap">
                <div style="min-width:220px">
                  <div style="font-weight:600">Created At</div>
                  <div style="color:#333">{{ $createdAtFull }}</div>
                </div>
                <div style="min-width:220px">
                  <div style="font-weight:600">Email</div>
                  <div style="color:#333">{{ $u->email }}</div>
                </div>
                <div style="min-width:220px">
                  <div style="font-weight:600">Last Login</div>
                  <div style="color:#333">{{ $lastLoginFull }}</div>
                </div>
                <div style="min-width:220px">
                  <div style="font-weight:600">Assigned by</div>
                    <div style="color:#333">
                      @if($assignerName)
                        <div>{{ $assignerName }}@if($assignerRoleDisplay) ({{ $assignerRoleDisplay }})@endif</div>
                        @if($assignedAt)
                          <div style="font-size:0.85em;color:#666;margin-top:4px">Assigned at: {{ $assignedAt }}</div>
                        @endif
                      @else
                        -
                      @endif
                    </div>
                </div>
                <div style="min-width:220px">
                  <div style="font-weight:600">Unapproved by</div>
                  <div style="color:#333" id="unapproved-info-{{ $u->id }}">
                    @if($u->unapproved_by)
                      @php
                        try {
                          $ub = \App\Models\User::find((int)$u->unapproved_by);
                        } catch (\Throwable $e) { $ub = null; }
                      @endphp
                      @if($ub)
                        <div>{{ $ub->name ?? $ub->username ?? ('user#' . $ub->id) }}</div>
                        @if($u->unapproved_at)
                          <div style="font-size:0.85em;color:#666;margin-top:4px">Unapproved at: {{ $u->unapproved_at }}</div>
                        @endif
                      @else
                        <div>{{ $u->unapproved_by }}</div>
                      @endif
                    @else
                      -
                    @endif
                  </div>
                </div>
                <div style="flex:1;min-width:280px">
                  <div style="font-weight:600">Mosques in user's scope</div>
                  <div style="color:#333">
                    @if(count($mosqueNames))
                      @foreach($mosqueNames as $m)
                        <div>- {{ $m }}</div>
                      @endforeach
                    @else
                      -
                    @endif
                  </div>
                </div>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    {{ $users->links() }}
  </div>
  
  <!-- Confirmation modal for delete -->
  <div id="confirm-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:1200;">
    <div style="width:520px; margin:80px auto; background:#fff; padding:18px; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.2);">
      <h4 id="confirm-modal-title">Konfirmasi Hapus</h4>
      <p id="confirm-modal-body">Anda yakin ingin menghapus user terpilih? Aksi ini tidak dapat dibatalkan.</p>
      <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px">
        <button id="confirm-cancel" class="btn btn-sm btn-outline-secondary">Batal</button>
        <button id="confirm-ok" class="btn btn-sm btn-danger">Hapus</button>
      </div>
    </div>
  </div>
  <!-- Scope overlay popup (centered) -->
  <div id="scope-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1500;">
    <div style="max-width:760px; margin:80px auto; background:#fff; padding:14px; border-radius:8px; box-shadow:0 12px 40px rgba(0,0,0,.3);">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
        <strong>Full Scope</strong>
        <button id="scope-overlay-close" class="btn btn-sm btn-outline-secondary">Close</button>
      </div>
      <div id="scope-overlay-content" style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
        <!-- badges will be injected here -->
      </div>
    </div>
  </div>
  <!-- privilege panels are inline per-row (expandable) -->
  @push('scripts')
  <script>
    (function(){
      // Helper: populate checkbox list into a container element
      async function populateRegionCheckboxes(role, container) {
        container.innerHTML = '';
        if (!role) {
          const el = document.createElement('div'); el.textContent = 'Pilih role terlebih dahulu'; el.style.color = '#666'; container.appendChild(el); return;
        }

        const url = new URL("{{ route('admin.allowed_regions') }}", window.location.origin);
        url.searchParams.set('role', role);
        try {
          const resp = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' });
          if (!resp.ok) throw new Error('Network');
          const data = await resp.json();
          if (!data.regions || data.regions.length === 0) {
            const el = document.createElement('div'); el.textContent = 'No regions available for selected role'; el.style.color = '#666'; container.appendChild(el); return;
          }
            // try to read existing assignments for this panel (grouped by role_key) from data-assigned
            let assignedMap = {};
            try {
              const wrapper = container.closest ? (container.closest('.privilege-panel') || container.closest('.privilege-panel-inner') || container.closest('.privilege-row')) : null;
              if (wrapper && wrapper.dataset && wrapper.dataset.assigned) assignedMap = JSON.parse(wrapper.dataset.assigned || '{}');
            } catch (e) { assignedMap = {}; }
          const assignedForRole = assignedMap[role] || [];
          data.regions.forEach(r => {
            const id = 'reg-' + r.id + '-' + Math.random().toString(36).slice(2,6);
            const row = document.createElement('div'); row.style.marginBottom = '6px';
            const chk = document.createElement('input'); chk.type = 'checkbox'; chk.name = 'region_ids[]'; chk.value = r.id; chk.id = id; chk.style.marginRight = '8px';
            // auto-check if this region is already assigned for the same role on this user
            try { if (assignedForRole.includes(r.id)) chk.checked = true; } catch (e) {}
            const label = document.createElement('label'); label.htmlFor = id; label.textContent = r.label;
            row.appendChild(chk); row.appendChild(label);
            container.appendChild(row);
          });
          // if there's a select-all checkbox near this container, wire it
          try {
            const wrapper = container.closest ? (container.closest('.privilege-panel') || container.closest('.privilege-panel-inner') || container.closest('.privilege-row')) : null;
            const selectAllEl = wrapper ? wrapper.querySelector('.region-select-all') : null;
            if (selectAllEl) {
              // ensure unchecking/clearing previous listeners is safe by replacing with a fresh one
              selectAllEl.checked = Array.from(container.querySelectorAll('input[type=checkbox][name="region_ids[]"]')).every(cb => cb.checked);
              selectAllEl.addEventListener('change', () => {
                Array.from(container.querySelectorAll('input[type=checkbox][name="region_ids[]"]')).forEach(cb => cb.checked = selectAllEl.checked);
              });
            }
          } catch (e) {
            // ignore
          }
        } catch (err) {
          const el = document.createElement('div'); el.textContent = 'Error loading regions'; el.style.color = 'red'; container.appendChild(el);
        }
      }

      // initialize checkbox-driven region lists for forms on the page (create page)
      const createRegionContainer = document.getElementById('create-assign-region-container');
      const createSelectAll = document.getElementById('create-region-select-all');
      const createAction = "{{ route('admin.users.store') }}";
      const createRoleSel = document.querySelector(`form[action="${createAction}"] select[name="role_key"]`);
      if (createRegionContainer && createSelectAll) {
        createSelectAll.addEventListener('change', () => {
          Array.from(createRegionContainer.querySelectorAll('input[type=checkbox][name="region_ids[]"]')).forEach(cb => cb.checked = createSelectAll.checked);
        });
      }
      // if create form role select exists, populate its container when changed
      if (createRoleSel && createRegionContainer) {
        createRoleSel.addEventListener('change', async () => {
          await populateRegionCheckboxes(createRoleSel.value, createRegionContainer);
        });
        // initial population if a role is preselected
        (async () => { if (createRoleSel.value) await populateRegionCheckboxes(createRoleSel.value, createRegionContainer); })();
      }

      // Helper: prefer user's assigned role_key (if any) and set select value
      function pickAssignedRoleForPanel(panel, roleSel) {
        if (!panel || !roleSel) return;
        try {
          const assignedMap = panel.dataset && panel.dataset.assigned ? JSON.parse(panel.dataset.assigned || '{}') : {};
          const assignedKeys = Object.keys(assignedMap || {}).filter(k => Array.isArray(assignedMap[k]) && assignedMap[k].length > 0);
          if (assignedKeys.length) {
            // pick the first role_key that exists in the select options
            for (const k of assignedKeys) {
              const opt = Array.from(roleSel.options).find(o => o.value === k);
              if (opt) { roleSel.value = k; return; }
            }
          }
        } catch (e) { /* ignore parse errors */ }
      }

      // For each privilege-panel-inner (now placed inside expandable rows), wire role select -> checkbox container and select-all
      document.querySelectorAll('.privilege-panel-inner').forEach(panel => {
        const roleSel = panel.querySelector('.role-key-select');
        const container = panel.querySelector('.assign-region-container');
        const selectAll = panel.querySelector('.region-select-all');
        if (roleSel && container) {
          // prefer selecting assigned role if present
          pickAssignedRoleForPanel(panel, roleSel);
          // initial populate will occur when the row is opened; but pre-populate if row is visible
          if (panel.closest('.privilege-row') && panel.closest('.privilege-row').style.display === 'table-row') populateRegionCheckboxes(roleSel.value, container);
          roleSel.addEventListener('change', async () => { await populateRegionCheckboxes(roleSel.value, container); });
        }
        if (selectAll && container) {
          selectAll.addEventListener('change', () => { Array.from(container.querySelectorAll('input[type=checkbox]')).forEach(cb => cb.checked = selectAll.checked); });
        }
      });

      // helper that calls populateRegionCheckboxes and returns its promise
      function awaitPopulate(role, container, panelInner) {
        return populateRegionCheckboxes(role, container);
      }

      // Close button inside expanded panel
      document.addEventListener('click', function(ev){
        const closeBtn = ev.target.closest ? ev.target.closest('.btn-close-priv') : null;
        if (!closeBtn) return;
        const row = closeBtn.closest ? closeBtn.closest('.privilege-row') : null;
        if (row) row.style.display = 'none';
      });

      // Ensure any legacy floating panels are hidden, and toggle privilege panels: show/hide the expandable table row below the user row
      document.addEventListener('DOMContentLoaded', function(){
        // hide any legacy floating panels left in the DOM
        document.querySelectorAll('.privilege-panel, .privilege-panel-inner').forEach(el => el.style.display = 'none');
        // ensure privilege rows are hidden by default
        document.querySelectorAll('.privilege-row').forEach(r => r.style.display = 'none');

        // Attach direct click handlers on buttons as a robust fallback (some environments may block delegated click)
        document.querySelectorAll('.btn-priv').forEach(function(btn){
          btn.addEventListener('click', function(ev){
            ev.preventDefault();
            const id = btn.getAttribute('data-user-id');
            console.log('priv button clicked', id);
            const row = document.getElementById('privrow-' + id);
            console.log('found row element:', row);
            if (!row) return;
            // determine current visibility robustly
            const computed = window.getComputedStyle(row);
            const isOpen = (row.style.display === 'table-row') || (computed && computed.display === 'table-row');
            // hide other rows
            document.querySelectorAll('.privilege-row').forEach(r => { if (r !== row) r.style.display = 'none'; });
            // hide legacy panels
            document.querySelectorAll('.privilege-panel').forEach(p => p.style.display = 'none');
            if (!isOpen) {
              const panelInner = row.querySelector('.privilege-panel-inner');
              const roleSel = panelInner ? panelInner.querySelector('.role-key-select') : null;
              const container = panelInner ? panelInner.querySelector('.assign-region-container') : null;
              // prefer user's assigned role (if any) before populating
              try { if (panelInner && roleSel) pickAssignedRoleForPanel(panelInner, roleSel); } catch (e) { /* ignore */ }
              console.log('panelInner, roleSel, container:', !!panelInner, !!roleSel, !!container);
              if (roleSel && container) {
                // ensure population starts but don't block UI
                awaitPopulate(roleSel.value, container, panelInner).catch(err => console.error('populate error', err));
              }
              // toggle display: try clearing inline style first, then force table-row
              row.style.display = '';
              if (window.getComputedStyle(row).display === 'none') row.style.display = 'table-row';
              // ensure the cell and inner panel are visible (defensive for CSS overrides)
              try {
                const firstTd = row.querySelector('td');
                if (firstTd) {
                  firstTd.style.display = '';
                  if (window.getComputedStyle(firstTd).display === 'none') firstTd.style.display = 'table-cell';
                }
                const inner = row.querySelector('.privilege-panel-inner');
                if (inner) {
                  inner.style.display = 'block';
                }
                console.log('row display after open:', window.getComputedStyle(row).display, 'td display:', firstTd ? window.getComputedStyle(firstTd).display : 'no-td');
                console.log('row bbox:', row.getBoundingClientRect(), 'td bbox:', firstTd ? firstTd.getBoundingClientRect() : null);
              } catch (e) { console.error('visibility guard error', e); }
              try { row.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch (e) {}
            } else {
              row.style.display = 'none';
              console.log('row closed');
            }
          });
        });
        // detail expand/collapse buttons
        document.querySelectorAll('.btn-detail').forEach(function(btn){
          btn.addEventListener('click', function(ev){
            ev.preventDefault();
            const id = btn.getAttribute('data-user-id');
            console.log('detail button clicked', id);
            const row = document.getElementById('detailrow-' + id);
            if (!row) return;
            const computed = window.getComputedStyle(row);
            const isOpen = (row.style.display === 'table-row') || (computed && computed.display === 'table-row');
            // close other detail rows
            document.querySelectorAll('.detail-row').forEach(r => { if (r !== row) r.style.display = 'none'; });
            if (!isOpen) {
              // open
              row.style.display = '';
              if (window.getComputedStyle(row).display === 'none') row.style.display = 'table-row';
              const firstTd = row.querySelector('td'); if (firstTd) { firstTd.style.display = ''; if (window.getComputedStyle(firstTd).display === 'none') firstTd.style.display = 'table-cell'; }
              try { row.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch (e) {}
            } else {
              row.style.display = 'none';
            }
          });
        });

        // close detail buttons
        document.querySelectorAll('.btn-close-detail').forEach(function(btn){
          btn.addEventListener('click', function(ev){ ev.preventDefault(); const row = btn.closest('.detail-row'); if (row) row.style.display = 'none'; });
        });
      });

      // Toggle behavior is handled by direct handlers attached on DOMContentLoaded to avoid duplicate firing.

      // Inline approved toggle handling (AJAX)
      document.addEventListener('change', function(ev){
        const cb = ev.target.closest ? ev.target.closest('.approved-toggle') : null;
        if (!cb) return;
        const userId = cb.getAttribute('data-user-id');
        const role = cb.getAttribute('data-role') || '';
        const checked = cb.checked;
        const spinner = document.getElementById('approved-spinner-' + userId);
        if (spinner) spinner.style.display = 'inline-block';
        cb.disabled = true;
        // build URL template and replace
        const tmpl = "{{ route('admin.users.toggle_approved', ':id') }}";
        const url = tmpl.replace(':id', userId);
        const token = "{{ csrf_token() }}";
        fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({ approved: checked ? '1' : '0' })
        }).then(async res => {
          if (!res.ok) {
            const txt = await res.text();
            throw new Error('HTTP ' + res.status + ': ' + txt);
          }
          return res.json();
        }).then(json => {
          if (json && json.success) {
            // update label near checkbox
            try {
              const lbl = document.querySelector('label[for="approved-toggle-' + userId + '"]');
              if (lbl) lbl.textContent = json.approved ? 'Yes' : 'No';
              // also update detail row's Unapproved info if present
              const uaInfo = document.getElementById('unapproved-info-' + userId);
              if (uaInfo) {
                if (json.unapproved_by) {
                  // show id for now; server stores unapproved_by id. If you want the name, we'd need additional data from server.
                  let html = '';
                  if (json.unapproved_at) html += '<div>' + (json.unapproved_by || '') + '</div><div style="font-size:0.85em;color:#666;margin-top:4px">Unapproved at: ' + json.unapproved_at + '</div>';
                  else html = '<div>' + (json.unapproved_by || '') + '</div>';
                  uaInfo.innerHTML = html;
                } else {
                  uaInfo.innerHTML = '-';
                }
              }
            } catch (e) { console.error(e); }
          } else {
            throw new Error('Unexpected response');
          }
        }).catch(err => {
          alert('Gagal mengubah status approved: ' + (err.message || err));
          // revert checkbox
          cb.checked = !checked;
        }).finally(() => {
          if (spinner) spinner.style.display = 'none';
          cb.disabled = false;
        });
      });

      // Populate scope (region) select based on chosen role deterministically
      try {
        @php
          $regionsJs = $regions->map(function($r){ return ['id' => $r->id, 'name' => $r->name, 'level' => $r->level]; })->values()->toArray();
        @endphp
        const regions = @json($regionsJs);
        const roleToLevel = { 'admin_regional': 'REGIONAL', 'admin_area': 'AREA', 'admin_witel': 'WITEL', 'admin_sto': 'STO' };
        const roleSel = document.getElementById('filter-role-select');
        const regionSel = document.getElementById('filter-region-select');

        function populateRegionOptionsForRole(role) {
          // clear existing (keep the All option)
          const currentVal = regionSel.value;
          while (regionSel.options.length > 1) regionSel.remove(1);
          if (!role) return;
          const level = roleToLevel[role];
          if (!level) return;
          const filtered = regions.filter(r => r.level === level || (r.level && r.level.toString().toUpperCase() === level));
          filtered.forEach(r => {
            const opt = document.createElement('option'); opt.value = r.id; opt.text = r.name; regionSel.appendChild(opt);
          });
          // restore previous selection if still present
          try { regionSel.value = currentVal; } catch(e){}
        }

        if (roleSel && regionSel) {
          roleSel.addEventListener('change', function(){ populateRegionOptionsForRole(roleSel.value); });
          // initial populate based on current request
          (function(){ const initial = roleSel.value; if (initial) populateRegionOptionsForRole(initial); })();
        }
      } catch(e) { console.error('region populate error', e); }

  // open scope overlay when clicking +N button
      document.addEventListener('click', function(ev){
        const btn = ev.target.closest ? ev.target.closest('.scope-expand-btn') : null;
        if (!btn) return;
        ev.preventDefault();
        const payload = btn.getAttribute('data-badges');
        if (!payload) return;
        let items = [];
        try { items = JSON.parse(payload); } catch(e) { items = []; }
        const content = document.getElementById('scope-overlay-content');
        if (!content) return;
        content.innerHTML = '';
        items.forEach(function(it){
          const span = document.createElement('span');
          const cls = (it.role_key === 'admin_witel') ? 'badge bg-primary text-white' : (it.role_key === 'admin_area') ? 'badge bg-warning text-dark' : (it.role_key === 'admin_regional') ? 'badge bg-success text-white' : (it.role_key === 'admin_sto') ? 'badge bg-secondary text-white' : 'badge bg-light text-dark';
          span.className = cls;
          span.style.cssText = 'font-size:0.85em;padding:3px 8px;border:1px solid #ddd;margin-right:6px;margin-bottom:6px';
          span.textContent = it.label;
          content.appendChild(span);
        });
        document.getElementById('scope-overlay').style.display = 'block';
      });
      const scopeClose = document.getElementById('scope-overlay-close');
      if (scopeClose) scopeClose.addEventListener('click', function(){ document.getElementById('scope-overlay').style.display = 'none'; });

      // DOMContentLoaded behavior not required for .btn-priv since delegated click handles toggling
    })();

    // single-row delete confirmation modal handler
    (function(){
      function showConfirmForForm(form, message){
        const modal = document.getElementById('confirm-modal');
        const body = document.getElementById('confirm-modal-body');
        const ok = document.getElementById('confirm-ok');
        const cancel = document.getElementById('confirm-cancel');
        if (!modal || !body || !ok || !cancel) return alert(message);
        body.textContent = message;
        modal.style.display = '';

        function cleanup(){
          modal.style.display = 'none';
          ok.removeEventListener('click', onOk);
          cancel.removeEventListener('click', onCancel);
        }

        function onOk(){ cleanup(); form.submit(); }
        function onCancel(){ cleanup(); }

        ok.addEventListener('click', onOk);
        cancel.addEventListener('click', onCancel);
      }

      document.querySelectorAll('.single-delete-form').forEach(f => {
        f.addEventListener('submit', function(ev){
          ev.preventDefault();
          showConfirmForForm(f, 'Hapus user ini?');
        });
      });
    })();
  </script>
  @endpush
</x-admin.layout>
