<x-admin.layout title="User Management">
  <div class="container admin-content">
    

    @if(session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <h2>User Management</h2>
      <div style="display:flex;gap:8px;align-items:center">
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">Kembali ke Dashboard</a>
      </div>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Approved</th>
          <th>Scope</th>
          <th>Created At</th>
          <th>Last Login</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $u)
          <tr>
            @php $canDelete = in_array($u->id, $manageableUserIds ?? []) || (auth()->user() && auth()->user()->isWebmaster()); @endphp
            <td>{{ $u->id }}</td>
            <td>{{ $u->name }}</td>
            <td>{{ $u->username }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->role }}</td>
            <td>{{ $u->approved ? 'Yes' : 'No' }}</td>
            <td>
              @php
                $scopes = $u->regionsRoles->map(function($ar){ return ($ar->region->name ?? $ar->region_id) . ' (' . ($ar->role_key ?? '') . ')'; })->toArray();
              @endphp
              {!! count($scopes) ? e(implode(', ', $scopes)) : '-' !!}
            </td>
            <td>{{ $u->created_at ? $u->created_at->format('Y-m-d H:i') : '-' }}</td>
            <td>
              @php
                $la = $lastActivities[$u->id] ?? null;
                if ($la) {
                  try { $lastStr = \Carbon\Carbon::createFromTimestamp($la)->toDateTimeString(); } catch (\Throwable $e) { $lastStr = '-'; }
                } else { $lastStr = '-'; }
              @endphp
              {{ $lastStr }}
            </td>
            <td>
              <button type="button" class="btn btn-sm btn-primary btn-priv" data-user-id="{{ $u->id }}">Atur Privilage</button>
              <div class="privilege-panel" id="priv-{{ $u->id }}" style="display:none;margin-top:8px">
                <form method="POST" action="{{ route('admin.users.update', $u->id) }}" class="d-flex gap-2" style="margin-bottom:8px">
                  @csrf
                  <select name="role" class="form-select form-select-sm">
                    <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>user</option>
                    <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>admin</option>
                    <option value="webmaster" {{ $u->role === 'webmaster' ? 'selected' : '' }}>webmaster</option>
                  </select>
                  <label class="form-check-label ms-2">
                    <input type="checkbox" name="approved" value="1" class="form-check-input" {{ $u->approved ? 'checked' : '' }}> Approved
                  </label>
                  <button class="btn btn-sm btn-primary">Save</button>
                </form>

                <form method="POST" action="{{ route('admin.users.roles.store', $u->id) }}">
                  @csrf
                  <div class="d-flex gap-2 align-items-center" style="margin-bottom:8px">
                    <select name="role_key" class="form-select form-select-sm">
                      <option value="admin_regional">Admin Regional</option>
                      <option value="admin_area">Admin Area</option>
                      <option value="admin_witel">Admin Witel</option>
                      <option value="admin_sto">Admin STO</option>
                    </select>
                    <select name="region_ids[]" multiple class="form-select form-select-sm assign-region-select">
                      @foreach($regions as $r)
                        <option value="{{ $r->id }}" data-label="{{ $r->name }} ({{ $r->displayTypeLabel() }})">{{ $r->name }} ({{ $r->displayTypeLabel() }})</option>
                      @endforeach
                    </select>
                    <button class="btn btn-sm btn-success">Assign</button>
                  </div>
                </form>

                <div style="margin-top:6px">
                  @foreach($u->regionsRoles as $ar)
                    <form method="POST" action="{{ route('admin.users.roles.destroy', $ar->id) }}" style="display:inline-block">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger" title="Remove">{{ $ar->role_key }}: {{ $ar->region->name ?? $ar->region_id }}</button>
                    </form>
                  @endforeach
                </div>
              </div>
              @if($canDelete)
                <form method="POST" action="{{ route('admin.users.delete_single', $u->id) }}" class="single-delete-form" style="display:inline-block;margin-left:8px">
                  @csrf
                  <button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
              @endif
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
  @push('scripts')
  <script>
    (function(){
      // AJAX-driven: when role changes, fetch allowed regions from server and populate select.
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

      document.querySelectorAll('form').forEach(form => {
        const roleSel = form.querySelector('select[name="role_key"]');
        const regionSel = form.querySelector('.assign-region-select');
        if (!roleSel || !regionSel) return;

        const placeholder = document.createElement('option');
        placeholder.textContent = 'Loading...'; placeholder.disabled = true;

        async function applyFilter() {
          const role = roleSel.value;
          regionSel.innerHTML = '';
          regionSel.appendChild(placeholder);
          regionSel.disabled = true;

          try {
            const url = new URL("{{ route('admin.allowed_regions') }}", window.location.origin);
            url.searchParams.set('role', role);
            const resp = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!resp.ok) throw new Error('Network');
            const data = await resp.json();
            regionSel.innerHTML = '';
            if (data.all_allowed) {
              // server returned all regions
              data.regions.forEach(r => {
                const opt = document.createElement('option'); opt.value = r.id; opt.textContent = r.label; regionSel.appendChild(opt);
              });
              regionSel.disabled = false;
            } else {
              if (!data.regions || data.regions.length === 0) {
                const el = document.createElement('option'); el.textContent = 'No regions available for selected role'; el.disabled = true; regionSel.appendChild(el);
                regionSel.disabled = true;
              } else {
                data.regions.forEach(r => {
                  const opt = document.createElement('option'); opt.value = r.id; opt.textContent = r.label; regionSel.appendChild(opt);
                });
                regionSel.disabled = false;
              }
            }
          } catch (err) {
            regionSel.innerHTML = ''; const el = document.createElement('option'); el.textContent = 'Error loading regions'; el.disabled = true; regionSel.appendChild(el);
          }
        }

        roleSel.addEventListener('change', applyFilter);
        applyFilter();

        // Add a simple "Select visible" button next to the select if not already present
        if (!form.querySelector('.select-visible-btn')) {
          const btn = document.createElement('button'); btn.type = 'button'; btn.className = 'btn btn-sm btn-outline-secondary select-visible-btn'; btn.style.marginLeft = '6px'; btn.textContent = 'Select visible';
          btn.addEventListener('click', () => {
            Array.from(regionSel.options).forEach(o => { if (!o.disabled) o.selected = true; });
          });
          regionSel.parentNode.insertBefore(btn, regionSel.nextSibling);
        }
      });
    })();
    // toggle privilege panels
    document.querySelectorAll('.btn-priv').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-user-id');
        const panel = document.getElementById('priv-' + id);
        if (!panel) return;
        panel.style.display = (panel.style.display === 'none' || panel.style.display === '') ? 'block' : 'none';
        // scroll into view when opening
        if (panel.style.display === 'block') panel.scrollIntoView({ behavior: 'smooth', block: 'center' });
      });
    });

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
