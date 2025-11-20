<x-admin.layout title="Create User">
  <div class="container admin-content">
    <div style="display:flex;justify-content:space-between;align-items:center;">
      <div>
        <a href="{{ url()->previous() ?? route('admin.users') }}" class="btn btn-sm btn-outline-secondary" style="margin-right:8px">← Back</a>
        <strong>Create User</strong>
      </div>
      <div style="font-size:0.9em;color:#666;text-align:right">
        <div>Logged in as: <strong>{{ auth()->user()->username ?? auth()->user()->email ?? '—' }}</strong></div>
        <div style="margin-top:4px">
          <span style="text-transform:capitalize; font-weight:600">{{ $myRole ?? auth()->user()->role ?? '—' }}</span>
          @if(!empty($myAssignments))
            &middot;
            <small style="color:#666">
              @foreach($myAssignments as $rk => $names)
                @php $label = ['admin_regional' => 'Regional', 'admin_area' => 'Area', 'admin_witel' => 'Witel', 'admin_sto' => 'STO'][$rk] ?? $rk; @endphp
                <span title="{{ $label }}">{{ $label }}: {{ implode(', ', $names) }}</span>
                @if(!$loop->last) | @endif
              @endforeach
            </small>
          @endif
        </div>
      </div>
    </div>

    @if(session('status'))
      <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" value="{{ old('name') }}" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input name="username" class="form-control" value="{{ old('username') }}" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" value="{{ old('email') }}" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Password (optional)</label>
        <input name="password" type="password" class="form-control" />
        <div class="form-text">If left blank a random password will be generated.</div>
      </div>

      <hr />
      <h4>Assign region privilege (optional)</h4>
      <div class="mb-3">
        <label class="form-label">Target admin role</label>
  <select name="role_key" id="create-role-key" class="form-select" onchange="window.callFetchAndRenderRegions && window.callFetchAndRenderRegions(this.value)">
          <option value="">(none)</option>
          {{-- Role choices limited by $allowedByTargetRole computed in controller --}}
          @php
            $roleLabels = ['admin_regional' => 'Admin Regional', 'admin_area' => 'Admin Area', 'admin_witel' => 'Admin Witel', 'admin_sto' => 'Admin STO'];
            // determine which target roles should be offered to this assigner based on their assigned role_keys
            $roleChildren = ['admin_regional' => ['admin_area'], 'admin_area' => ['admin_witel'], 'admin_witel' => ['admin_sto']];
            $availableTargets = [];
            // Prefer the explicit account role stored on users table (e.g. webmaster) first. This avoids
            // misclassifying users who have webmaster as their account-level role but also have region roles.
            $accountRole = auth()->user()->role ?? null;
            if ($accountRole === 'webmaster') {
              $availableTargets = array_keys($roleLabels);
            } elseif (isset($myRole) && $myRole === 'webmaster') {
              // fallback to the computed myRole (if controller provided it)
              $availableTargets = array_keys($roleLabels);
            } else {
              $assignerKeys = array_keys($myAssignments ?? []);
              foreach ($assignerKeys as $ak) {
                if (isset($roleChildren[$ak])) {
                  foreach ($roleChildren[$ak] as $child) $availableTargets[] = $child;
                }
              }
              $availableTargets = array_values(array_unique($availableTargets));
            }
          @endphp
          @foreach($roleLabels as $k => $label)
            @php $allowed = $allowedByTargetRole[$k] ?? null; @endphp
            @if((is_null($allowed) || (is_array($allowed) && count($allowed) > 0)) && in_array($k, $availableTargets))
              <option value="{{ $k }}">{{ $label }}</option>
            @endif
          @endforeach
        </select>
  {{-- safe wrapper always present so the button can be called even before the main script loads --}}
  <script>
          // resilient wrapper: prefer the shared function if present, otherwise execute a local
          // fetch+render implementation so the button always works (no polling hang).
          window.callFetchAndRenderRegions = async function(role){
            try {
              if (window.fetchAndRenderRegionsForRole) return window.fetchAndRenderRegionsForRole(role);
            } catch(e){ console.error('callFetchAndRenderRegions immediate check error', e); }
            console.debug('fetchAndRenderRegionsForRole not present, wrapper will perform fetch for role', role);
            // do a minimal in-place fetch + render so button always works
            const container = document.getElementById('create-assign-region-container');
            const noRegions = document.getElementById('create-no-regions');
            const selectAll = document.getElementById('create-region-select-all');
            if (!container) return console.warn('create assign container not found in DOM yet');
            container.innerHTML = '';
            if (noRegions) noRegions.style.display = 'none';
            if (selectAll) { selectAll.checked = false; selectAll.disabled = true; }
            if (!role) {
              const el = document.createElement('div'); el.textContent = 'Pilih role terlebih dahulu untuk memilih region.'; el.style.color = '#666'; container.appendChild(el);
              return;
            }
            const url = new URL("{{ route('admin.allowed_regions') }}", window.location.origin);
            url.searchParams.set('role', role);
            try {
              console.debug('wrapper fetching', url.toString());
              const resp = await fetch(url.toString(), { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
              console.debug('wrapper fetch status', resp.status);
              if (!resp.ok) throw new Error('Network');
              const data = await resp.json();
              const regs = data.regions || [];
              if (!regs || regs.length === 0) { if (noRegions) noRegions.style.display = ''; return; }
              regs.forEach(r => {
                const id = 'cr-' + r.id + '-' + Math.random().toString(36).slice(2,6);
                const row = document.createElement('div'); row.style.marginBottom = '6px';
                const chk = document.createElement('input'); chk.type = 'checkbox'; chk.name = 'region_ids[]'; chk.value = r.id; chk.id = id; chk.style.marginRight = '8px';
                const label = document.createElement('label'); label.htmlFor = id; label.textContent = r.label || r.id;
                row.appendChild(chk); row.appendChild(label);
                container.appendChild(row);
              });
              if (selectAll) { selectAll.disabled = false; selectAll.checked = false; }
            } catch (e) {
              console.error('wrapper fetch error', e);
              container.innerHTML = '<div style="color:red">Error loading regions</div>';
            }
          };
  </script>
        <div class="form-text">Pilih role untuk user yang akan dibuat. Opsi hanya yang berada dalam cakupan Anda.</div>
      </div>

      <div class="mb-3" id="create-regions-block">
        <label class="form-label">Regions (multiple)</label>
        <div class="mb-1"><label><input type="checkbox" id="create-region-select-all" /> Select all</label></div>
        <div id="create-assign-region-container" style="max-height:260px;overflow:auto;border:1px solid #eee;padding:6px;border-radius:4px;background:#fff;">
          {{-- populated dynamically via AJAX from admin.allowed_regions endpoint to match Users -> Atur Privilage behavior --}}
        </div>
        <div id="create-no-regions" style="color:#666;display:none;margin-top:6px">No regions available for selected role</div>
      </div>

      {{-- Auto-approve users created from this form; do not show checkbox --}}
      <input type="hidden" name="approved" value="1" />

      <div style="display:flex;gap:8px">
        <button class="btn btn-primary">Create</button>
        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">Back to Users</a>
      </div>
    </form>
  </div>
</x-admin.layout>

@push('scripts')
<script>
  (function(){
    const roleSel = document.getElementById('create-role-key');
    const container = document.getElementById('create-assign-region-container');
    let allowedByTargetRole = {};
    let allRegions = {};
    if (container) {
      try {
        allowedByTargetRole = JSON.parse(container.dataset.allowed || '{}');
      } catch (e) { allowedByTargetRole = {}; }
      try {
        allRegions = JSON.parse(container.dataset.all || '{}');
      } catch (e) { allRegions = {}; }
    }
  const selectAll = document.getElementById('create-region-select-all');
    const noRegions = document.getElementById('create-no-regions');
    const regionsBlock = document.getElementById('create-regions-block');

    // renderRegionsForRole removed; server pre-renders per-role blocks in the DOM and JS will toggle visibility

    // New behaviour: fetch allowed regions for the selected target role from the same endpoint
    // used by the users->Atur Privilage panel, so behaviour is consistent.
    async function fetchAndRenderRegionsForRole(role) {
      container.innerHTML = '';
      noRegions.style.display = 'none';
      selectAll.checked = false;
      selectAll.disabled = true;
      if (!role) {
        const el = document.createElement('div'); el.textContent = 'Pilih role terlebih dahulu untuk memilih region.'; el.style.color = '#666'; container.appendChild(el);
        return;
      }

      const url = new URL("{{ route('admin.allowed_regions') }}", window.location.origin);
      url.searchParams.set('role', role);
      try {
        const resp = await fetch(url.toString(), { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
        if (!resp.ok) throw new Error('Network');
        const data = await resp.json();
        const regs = data.regions || [];
        if (!regs || regs.length === 0) {
          noRegions.style.display = '';
          return;
        }
        regs.forEach(r => {
          const id = 'cr-' + r.id + '-' + Math.random().toString(36).slice(2,6);
          const row = document.createElement('div'); row.style.marginBottom = '6px';
          const chk = document.createElement('input'); chk.type = 'checkbox'; chk.name = 'region_ids[]'; chk.value = r.id; chk.id = id; chk.style.marginRight = '8px';
          const label = document.createElement('label'); label.htmlFor = id; label.textContent = r.label || r.id;
          row.appendChild(chk); row.appendChild(label);
          container.appendChild(row);
        });
        selectAll.disabled = false;
        selectAll.checked = false;
      } catch (e) {
        container.innerHTML = '<div style="color:red">Error loading regions</div>';
      }
    }

    if (roleSel) {
      roleSel.addEventListener('change', function(){ if (typeof fetchAndRenderRegionsForRole === 'function') fetchAndRenderRegionsForRole(roleSel.value); });
      // initial load (if a role is preselected and the function is ready)
      try { if (typeof fetchAndRenderRegionsForRole === 'function') fetchAndRenderRegionsForRole(roleSel.value); } catch(e){ /* ignore */ }
    }

    // expose for manual debugging
    if (typeof window !== 'undefined') {
      window.fetchAndRenderRegionsForRole = fetchAndRenderRegionsForRole;
    }
    // load button removed; regions are fetched automatically on role change

    if (selectAll) {
      selectAll.addEventListener('change', function(){
        const checks = container.querySelectorAll('input[type=checkbox][name="region_ids[]"]');
        checks.forEach(c => c.checked = selectAll.checked);
      });
    }
  })();
</script>
@endpush
