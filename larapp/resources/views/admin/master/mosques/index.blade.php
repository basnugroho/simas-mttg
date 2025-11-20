<x-admin.layout title="Mosques">
  <div class="p-4">
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-3">
          <div>
            <h3 class="m-0">Mosques</h3>
            <div style="font-size:12px;color:#6b7280;margin-top:4px">Master · <a href="{{ route('dashboard') }}">Dashboard</a> / <strong>Mosques</strong></div>
          </div>
        </div>
    </div>

  {{-- Create button (right) and user info below it; then filters/sort/pagination controls --}}
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
      // prepare display for role: use first role if available
      $primaryRoleLabel = '-';
      if(count($meRoles)){
        $first = $meRoles[0];
        $primaryRoleLabel = $first['role'] . ($first['region'] ? ' / ' . $first['region'] : '');
        if(count($meRoles) > 1) $primaryRoleLabel .= ' +' . (count($meRoles)-1) . ' more';
      }
    @endphp
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px;gap:12px">
      <div style="color:#6b7280;font-size:13px">Scope: shows all mosques; detail/peta available for all, edit/delete locked for out-of-scope</div>
      <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px">
        <div>
          @can('create', \App\Models\Mosque::class)
            <a href="{{ route('admin.mosques.create') }}" class="btn btn-primary">Create Mosque</a>
          @endcan
        </div>
        <div style="font-size:13px;color:#374151">Logged in as: <span style="font-weight:700">{{ $me?->name ?? (Auth::user()?->username ?? 'Guest') }}</span>, <span style="color:#0ea5a4;font-weight:700">Role: {{ $primaryRoleLabel }}</span></div>
      </div>
    </div>

    {{-- Compact search + type filter --}}
    <form method="GET" action="" style="display:flex;gap:8px;align-items:center;margin-bottom:12px">
  <input type="search" name="q" value="{{ request('q', $q ?? '') }}" placeholder="Search..." class="form-input" style="padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;min-width:160px" />
  <select name="type" class="form-select" style="padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;height:34px;min-width:160px">
        <option value="">All types</option>
        <option value="MASJID" {{ (isset($filterType) && $filterType==='MASJID') ? 'selected' : '' }}>Masjid</option>
        <option value="MUSHOLLA" {{ (isset($filterType) && $filterType==='MUSHOLLA') ? 'selected' : '' }}>Musholla</option>
      </select>
      <button class="btn btn-sm btn-primary">Go</button>
      <a href="{{ route('admin.mosques.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
    </form>

    <table class="table">
      <thead>
        <tr>
          <th>#</th>
          <th>
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'dir' => (isset($sort) && $sort==='name' && isset($dir) && $dir==='asc') ? 'desc' : 'asc']) }}">Name @if(isset($sort) && $sort==='name'){!! ' ' . ($dir==='asc' ? '▲' : '▼') !!}@endif</a>
          </th>
          <th style="width:48px;text-align:center">📸</th>
          <th>
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'regional', 'dir' => (isset($sort) && $sort==='regional' && isset($dir) && $dir==='asc') ? 'desc' : 'asc']) }}">Regional @if(isset($sort) && $sort==='regional'){!! ' ' . ($dir==='asc' ? '▲' : '▼') !!}@endif</a>
          </th>
          <th>
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'area', 'dir' => (isset($sort) && $sort==='area' && isset($dir) && $dir==='asc') ? 'desc' : 'asc']) }}">Area @if(isset($sort) && $sort==='area'){!! ' ' . ($dir==='asc' ? '▲' : '▼') !!}@endif</a>
          </th>
          <th>
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'witel', 'dir' => (isset($sort) && $sort==='witel' && isset($dir) && $dir==='asc') ? 'desc' : 'asc']) }}">Witel @if(isset($sort) && $sort==='witel'){!! ' ' . ($dir==='asc' ? '▲' : '▼') !!}@endif</a>
          </th>
          <th>
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'sto', 'dir' => (isset($sort) && $sort==='sto' && isset($dir) && $dir==='asc') ? 'desc' : 'asc']) }}">STO @if(isset($sort) && $sort==='sto'){!! ' ' . ($dir==='asc' ? '▲' : '▼') !!}@endif</a>
          </th>
          <th>
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'completion_percentage', 'dir' => (isset($sort) && $sort==='completion_percentage' && isset($dir) && $dir==='asc') ? 'desc' : 'asc']) }}">Completion @if(isset($sort) && $sort==='completion_percentage'){!! ' ' . ($dir==='asc' ? '▲' : '▼') !!}@endif</a>
          </th>
          <th>
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'daya_tampung', 'dir' => (isset($sort) && $sort==='daya_tampung' && isset($dir) && $dir==='asc') ? 'desc' : 'asc']) }}">Capacity @if(isset($sort) && $sort==='daya_tampung'){!! ' ' . ($dir==='asc' ? '▲' : '▼') !!}@endif</a>
          </th>
          <th style="width:180px">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'dir' => (isset($sort) && $sort==='created_at' && isset($dir) && $dir==='asc') ? 'desc' : 'asc']) }}">Actions @if(isset($sort) && $sort==='created_at'){!! ' ' . ($dir==='asc' ? '▲' : '▼') !!}@endif</a>
          </th>
        </tr>
      </thead>
      <tbody>
        @foreach($mosques as $m)
          <tr>
            <td style="width:40px">{{ $loop->iteration + ($mosques->currentPage()-1)*$mosques->perPage() }}</td>
            <td>{{ $m->name }}</td>
            <td style="text-align:center">@if($m->photos && $m->photos->count()) ✅ @endif</td>
            <td>{{ $m->regional?->name }}</td>
            <td>{{ $m->area?->name }}</td>
            <td>{{ $m->witel?->name }}</td>
            <td>{{ $m->sto?->name }}</td>
            @php
              $pct = intval($m->completion_percentage ?? 0);
              if($pct < 25) { $pctClass = 'low'; }
              elseif($pct < 75) { $pctClass = 'mid'; }
              else { $pctClass = 'high'; }
            @endphp
            <td style="min-width:200px">
              <div style="display:flex;align-items:center;gap:8px">
                <div class="completion-bar" style="flex:1;max-width:220px;--pct: {{ $pct }};">
                  <div class="completion-fill {{ $pctClass }}" style="width:calc(var(--pct) * 1%);"></div>
                </div>
                <div style="width:48px;text-align:right;font-size:12px;color:#374151">{{ $pct }}%</div>
              </div>
            </td>
            <td>{{ $m->daya_tampung ?? '-' }}</td>
            <td style="display:flex;gap:8px;align-items:center;justify-content:flex-end">
              {{-- Detail (chevron) button moved to the leftmost of actions --}}
              @php
                $inScope = true;
                try {
                  if (isset($allowedScope) && is_array($allowedScope)) {
                    $inScope = (
                      (isset($allowedScope['regional']) && in_array($m->regional_id, $allowedScope['regional'])) ||
                      (isset($allowedScope['area']) && in_array($m->area_id, $allowedScope['area'])) ||
                      (isset($allowedScope['witel']) && in_array($m->witel_id, $allowedScope['witel'])) ||
                      (isset($allowedScope['sto']) && in_array($m->sto_id, $allowedScope['sto']))
                    );
                  }
                } catch (\Throwable $__e) { $inScope = true; }
              @endphp
              <button type="button" class="btn btn-sm btn-outline-secondary toggle-detail" data-id="{{ $m->id }}" title="Show details">
                <!-- chevron down icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M5 7l5 5 5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </button>

              {{-- Coordinate button immediately to the right of detail arrow --}}
              <button type="button" class="btn btn-sm btn-secondary btn-view-map" data-id="{{ $m->id }}" title="View map" style="margin-left:6px">
                <!-- map pin icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 11.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </button>

              {{-- Facilities available for all users (opens modal) --}}
              <button type="button" class="btn btn-sm btn-info btn-manage-facilities" data-id="{{ $m->id }}" style="margin-left:6px">Facilities</button>

              {{-- Edit: only enabled when inScope and policy allows; otherwise show lock icon --}}
              @if($inScope)
                @can('update', $m)
                  <a href="{{ route('admin.mosques.edit', $m->id) }}" class="btn btn-sm btn-primary" style="margin-left:6px">Edit</a>
                @else
                  <button class="btn btn-sm" disabled title="You don't have permission to edit" style="margin-left:6px">Edit</button>
                @endcan
              @else
                <button class="btn btn-sm btn-primary" disabled title="Edit disabled — out of your region scope" style="margin-left:6px;opacity:.9;cursor:not-allowed">Edit 🔒</button>
              @endif

              {{-- Delete: only enabled when inScope and policy allows; otherwise show lock icon --}}
              <form action="{{ route('admin.mosques.destroy', $m->id) }}" method="POST" style="display:inline;margin-left:6px">
                @csrf
                @method('DELETE')
                @if($inScope)
                  @can('delete', $m)
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this mosque?')">Delete</button>
                  @else
                    <button class="btn btn-danger btn-sm" disabled title="You don't have permission to delete">Delete</button>
                  @endcan
                @else
                  <button class="btn btn-danger btn-sm" disabled title="Delete disabled — out of your region scope" style="opacity:.9;cursor:not-allowed">Delete 🔒</button>
                @endif
              </form>
            </td>
          </tr>

          {{-- Detail row hidden by default --}}
          <tr class="mosque-detail-row" id="mosque-detail-{{ $m->id }}" style="display:none;background:#fbfbfd">
            <td colspan="10">
              <div class="detail-collapse">
                <div class="detail-columns" style="padding:12px 0">
                <div style="flex:1;min-width:260px">
                  <div><strong>Code:</strong> {{ $m->code ?? '-' }}</div>
                  <div><strong>Type:</strong> {{ $m->type ?? '-' }}</div>
                  <div><strong>Address:</strong> {{ $m->address ?? '-' }}</div>
                  <div><strong>Province / City:</strong> {{ $m->province_id ? ($m->province?->name ?? $m->province_id) : '-' }} / {{ $m->city_id ? ($m->city?->name ?? $m->city_id) : '-' }}</div>
                  <div><strong>Regional / Witel / STO:</strong> {{ $m->regional?->name ?? '-' }} / {{ $m->witel?->name ?? '-' }} / {{ $m->sto?->name ?? '-' }}</div>
                  <div><strong>Tahun didirikan:</strong> {{ $m->tahun_didirikan ?? '-' }}</div>
                  <div><strong>Jumlah BKM:</strong> {{ $m->jml_bkm ?? 0 }}</div>
                  <div><strong>Luas Tanah:</strong> {{ $m->luas_tanah ?? '-' }} m2</div>
                  <div><strong>Daya Tampung:</strong> {{ $m->daya_tampung ?? '-' }}</div>
                  <div><strong>Koordinat:</strong> {{ $m->latitude && $m->longitude ? $m->latitude.','.$m->longitude : '-' }}</div>
                  <div style="margin-top:8px"><strong>Completion:</strong> {{ $m->completion_percentage ?? 0 }}% &middot; <strong>Active:</strong> {{ $m->is_active ? 'Yes' : 'No' }}</div>
                  <div style="margin-top:8px"><strong>Created:</strong> {{ $m->created_at ? $m->created_at->toDateTimeString() : '-' }} &middot; <strong>Updated:</strong> {{ $m->updated_at ? $m->updated_at->toDateTimeString() : '-' }}</div>
                  @if($m->image_url)
                    <div style="margin-top:8px"><strong>Image:</strong><br/><img src="{{ $m->image_url }}" alt="image" style="max-width:200px;border-radius:6px;margin-top:6px"/></div>
                  @endif
                  @if($m->description)
                    <div style="margin-top:8px"><strong>Description:</strong><div style="margin-top:6px;color:#374151">{{ Str::limit($m->description, 400) }}</div></div>
                  @endif
                </div>
                <div class="detail-photos" style="width:240px">
                  <div style="display:flex;flex-direction:column;gap:8px">
                    @foreach($m->photos as $p)
                      <div style="display:flex;flex-direction:column;gap:6px;align-items:center">
                        <img src="{{ asset('storage/' . $p->path) }}" style="width:220px;height:140px;object-fit:cover;border-radius:6px;border:1px solid #e6e6e6" alt="photo" />
                        @if($p->caption)
                          <div style="font-size:12px;color:#374151;max-width:220px;overflow:hidden;text-overflow:ellipsis">{{ $p->caption }}</div>
                        @endif
                        <form method="POST" action="{{ route('admin.mosque_photos.destroy', $p->id) }}" style="margin-top:6px">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                      </div>
                    @endforeach
                  </div>
                </div>
                <div class="detail-map" style="width:320px">
                  @if($m->latitude && $m->longitude)
                    <div id="map-detail-{{ $m->id }}" style="height:200px;border:1px solid #e5e7eb;border-radius:6px"></div>
                  @else
                    <div style="color:#6b7280">No coordinates available</div>
                  @endif
                </div>
              </div>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    {{ $mosques->links() }}
  </div>
  {{-- hidden container with base64-encoded JSON to avoid JS-parser/editor issues --}}
  <!-- Modal for large map preview -->
  <div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Mosque Map</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="modal-map" style="height:480px;border:1px solid #e5e7eb;border-radius:8px"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Facilities manager modal -->
  <div class="modal fade" id="facilitiesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Manage Facilities for <span id="facilities-modal-mosque-name"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="facilities-list" style="display:flex;flex-direction:column;gap:10px;max-height:60vh;overflow:auto"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="facilities-save">Save</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Mosque-facility photo manager modal -->
  <div class="modal fade" id="facilityPhotosModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Photos for <span id="photos-modal-facility-name"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="facility-photos-list" style="display:flex;flex-wrap:wrap;gap:12px"></div>
          <div style="margin-top:12px">
            <input type="file" id="facility-photos-input" multiple accept="image/*" />
            <div id="facility-photos-captions" style="margin-top:8px"></div>
            <button class="btn btn-sm btn-primary" id="facility-photos-upload" style="margin-top:8px">Upload</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="mosques-data" data-mosques="{{ base64_encode(json_encode($mosques->items())) }}" style="display:none"></div>

  @push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <style>
      /* detail collapse slide */
  .detail-collapse{ max-height:0; overflow:hidden; transition: max-height 280ms ease; }
  .detail-collapse.open{ overflow:visible; }
      .toggle-detail{ display:inline-flex; align-items:center; justify-content:center; }
      .detail-columns{ display:flex; gap:18px; align-items:flex-start; flex-wrap:wrap }
  .detail-photos{ box-sizing: border-box; }
  .detail-map{ box-sizing: border-box; }
      @media (max-width:768px){ .detail-columns{ flex-direction:column } .detail-photos, .detail-map{ width:100% !important } }
      /* Completion progress bar styles */
      .completion-bar{ background:#e6e6e6; border-radius:6px; height:12px; overflow:hidden; }
      .completion-fill{ height:100%; width:0; transition: width 360ms ease; border-radius:6px 0 0 6px; }
      .completion-fill.low{ background:#ef4444; } /* red */
      .completion-fill.mid{ background:#f59e0b; } /* amber */
      .completion-fill.high{ background:#10b981; } /* green */
    </style>
  @endpush

  @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // keyboard shortcut: Alt+Shift+D to go back to dashboard
        (function(){ document.addEventListener('keydown', function(e){ if(e.altKey && e.shiftKey && String(e.key).toLowerCase() === 'd'){ window.location = '{{ route("dashboard") }}'; } }); })();

      (function(){
        var decoded = [];
        try{
          var container = document.getElementById('mosques-data');
          var raw = container && container.dataset && container.dataset.mosques ? container.dataset.mosques : '';
          if(raw) decoded = JSON.parse(atob(raw));
        }catch(e){ console.warn('decode mosques error', e); }

        function findMosque(id){
          return decoded.find(function(x){ return String(x.id) === String(id); }) || null;
        }
        // Facilities manager logic (moved out of detail-toggle so Facilities button works without opening details)
        (function(){
          const facilitiesModal = document.getElementById('facilitiesModal');
          const facilitiesList = document.getElementById('facilities-list');
          const facilitiesSaveBtn = document.getElementById('facilities-save');
          const facilitiesModalName = document.getElementById('facilities-modal-mosque-name');
          const photosModal = document.getElementById('facilityPhotosModal');
          const photosList = document.getElementById('facility-photos-list');
          const photosModalName = document.getElementById('photos-modal-facility-name');
          const photosInput = document.getElementById('facility-photos-input');
          const photosUploadBtn = document.getElementById('facility-photos-upload');
          let currentMosqueId = null;
          let currentFacilityId = null;

          function getCsrf(){ const m = document.querySelector('meta[name="csrf-token"]'); return m ? m.getAttribute('content') : '' }

          async function openFacilities(mosqueId){
            currentMosqueId = mosqueId;
            const res = await fetch('/admin/mosques/'+mosqueId+'/facilities', { credentials:'same-origin' });
            const json = await res.json();
            facilitiesList.innerHTML = '';
            if(!json.success) return;
            // set modal title
            const mosque = findMosque(mosqueId);
            facilitiesModalName.innerText = mosque ? mosque.name : ('#'+mosqueId);

            json.facilities.forEach(f => {
              const wrap = document.createElement('div'); wrap.style.borderBottom='1px solid #eee'; wrap.style.padding='8px 0';
              const top = document.createElement('div'); top.style.display='flex'; top.style.gap='12px'; top.style.alignItems='center';
              const chk = document.createElement('input'); chk.type='checkbox'; chk.checked = f.assignment ? !!f.assignment.is_available : false; chk.dataset.fid = f.id;
              const name = document.createElement('div'); name.style.flex='1'; name.innerHTML = `<strong>${f.name}</strong>${f.is_required?'<span style="color:#f59e0b;margin-left:8px;font-size:12px">required</span>':''}`;
              name.style.cursor = 'pointer';
              const qty = document.createElement('input'); qty.type='number'; qty.placeholder='qty'; qty.style.width='100px'; qty.value = f.assignment && f.assignment.quantity ? f.assignment.quantity : '';
              qty.setAttribute('min','0'); qty.setAttribute('step','1');
              qty.addEventListener('input', function(){
                this.value = this.value.replace(/[^0-9]/g,'');
                try{ chk.checked = Number(this.value) > 0; }catch(e){}
              });
              const unit = document.createElement('div'); unit.innerText = f.unit ? f.unit.name : '';
              const note = document.createElement('input'); note.type='text'; note.placeholder='note'; note.style.width='220px'; note.value = f.assignment && f.assignment.note ? f.assignment.note : '';
              const expandToggle = document.createElement('button'); expandToggle.className='btn btn-sm btn-outline-secondary'; expandToggle.innerText='Collapse';
              top.appendChild(chk); top.appendChild(name); top.appendChild(qty); top.appendChild(unit); top.appendChild(note); top.appendChild(expandToggle);
              // if quantity already present, mark checkbox as checked
              try{ chk.checked = Number(qty.value) > 0; }catch(e){}

              const dropArea = document.createElement('div'); dropArea.style.display='block'; dropArea.style.marginTop='8px'; dropArea.style.border='1px dashed #d1d5db'; dropArea.style.padding='12px'; dropArea.style.borderRadius='8px';
              dropArea.innerHTML = `<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px"><div style="color:#6b7280">Drag & drop JPG images here or <button type=\"button\" class=\"btn btn-link btn-browse\" style=\"padding:0\">browse</button></div><div style=\"font-size:12px;color:#9ca3af\">.jpg/.jpeg only — recommended max 5MB</div></div><input type=\"file\" accept=\"image/jpeg,image/jpg\" multiple style=\"display:none\" class=\"facility-photos-input\" /> <div class=\"facility-photo-previews\" style=\"display:flex;gap:12px;flex-wrap:wrap;margin-top:8px\"></div>`;

              const fileInput = dropArea.querySelector('.facility-photos-input');
              const browseBtn = dropArea.querySelector('.btn-browse');
              const previews = dropArea.querySelector('.facility-photo-previews');
              const filesArr = [];

              function createExistingPreview(p){
                const el = document.createElement('div'); el.style.width='160px'; el.style.display='flex'; el.style.flexDirection='column'; el.style.gap='6px';
                const img = document.createElement('img'); img.src = p.path; img.style.width='160px'; img.style.height='110px'; img.style.objectFit='cover'; img.style.borderRadius='6px'; img.style.border='1px solid #e6e6e6';
                const caption = document.createElement('input'); caption.type='text'; caption.placeholder='Caption (optional)'; caption.className='form-input'; caption.style.fontSize='12px'; caption.value = p.caption || '';
                const remove = document.createElement('button'); remove.type='button'; remove.className='btn btn-sm btn-outline-danger'; remove.innerText='Delete';
                el.appendChild(img); el.appendChild(caption); el.appendChild(remove);
                // on caption change (blur), PATCH caption
                caption.addEventListener('blur', async function(){
                  try{
                    await fetch('/admin/mosque-facility-photos/'+p.id, { method: 'PATCH', credentials: 'same-origin', headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': getCsrf() }, body: JSON.stringify({ caption: caption.value }) });
                  }catch(err){ console.warn('caption update failed', err); }
                });
                remove.addEventListener('click', async function(){ if(!confirm('Delete this photo?')) return; try{ const dres = await fetch('/admin/mosque-facility-photos/'+p.id, { method: 'DELETE', credentials:'same-origin', headers: {'X-CSRF-TOKEN': getCsrf()} }); const dj = await dres.json(); if(dj.success) el.remove(); }catch(err){ console.warn('delete photo', err); } });
                return el;
              }

              function createPreview(file){
                const el = document.createElement('div'); el.style.width='160px'; el.style.display='flex'; el.style.flexDirection='column'; el.style.gap='6px';
                const img = document.createElement('img'); img.style.width='160px'; img.style.height='110px'; img.style.objectFit='cover'; img.style.borderRadius='6px'; img.style.border='1px solid #e6e6e6';
                const caption = document.createElement('input'); caption.type='text'; caption.placeholder='Caption (optional)'; caption.className='form-input'; caption.style.fontSize='12px';
                const remove = document.createElement('button'); remove.type='button'; remove.className='btn btn-sm btn-outline-danger'; remove.innerText='Remove';
                el.appendChild(img); el.appendChild(caption); el.appendChild(remove);
                const reader = new FileReader(); reader.onload = function(ev){ img.src = ev.target.result; }; reader.readAsDataURL(file);
                remove.addEventListener('click', function(){ const i = filesArr.indexOf(file); if(i>-1) filesArr.splice(i,1); el.remove(); });
                return {node: el, file: file, captionEl: caption};
              }

              function addFiles(fileList){ Array.from(fileList).forEach(function(f){ const nameLower = (f.name||'').toLowerCase(); if(!(f.type === 'image/jpeg' || nameLower.endsWith('.jpg') || nameLower.endsWith('.jpeg'))){ alert('Only JPG images allowed: '+f.name); return; } filesArr.push(f); const pv = createPreview(f); previews.appendChild(pv.node); }); }

              dropArea.addEventListener('dragover', function(e){ e.preventDefault(); dropArea.style.background='#fbfbfb'; });
              dropArea.addEventListener('dragleave', function(e){ dropArea.style.background=''; });
              dropArea.addEventListener('drop', function(e){ e.preventDefault(); dropArea.style.background=''; if(e.dataTransfer && e.dataTransfer.files) addFiles(e.dataTransfer.files); });
              browseBtn.addEventListener('click', function(){ fileInput.click(); });
              fileInput.addEventListener('change', function(ev){ if(ev.target.files) addFiles(ev.target.files); ev.target.value=''; });

              // load existing photos for this mosque+facility and render in previews
              (async function(){
                try{
                  const resp = await fetch('/admin/mosques/'+mosqueId+'/facilities/'+f.id+'/photos', { credentials:'same-origin' });
                  const pj = await resp.json();
                  if(pj.success && Array.isArray(pj.photos)){
                    pj.photos.forEach(function(p){ const node = createExistingPreview(p); previews.appendChild(node); });
                  }
                }catch(e){ console.warn('load existing facility photos', e); }
              })();

              expandToggle.addEventListener('click', function(){ if(dropArea.style.display==='none'){ dropArea.style.display='block'; expandToggle.innerText='Collapse'; } else { dropArea.style.display='none'; expandToggle.innerText='Expand'; } });

              wrap.appendChild(top); wrap.appendChild(dropArea);

              wrap.__data = { fid: f.id, checkbox: chk, qty: qty, unit: null, note: note, filesArr: filesArr, getCaptions: function(){ return Array.from(previews.querySelectorAll('input[type="text"]')).map(i=>i.value); } };
              facilitiesList.appendChild(wrap);
            });
            const bs = new bootstrap.Modal(facilitiesModal); bs.show();
          }

          facilitiesSaveBtn.addEventListener('click', async function(){
            const payload = { facilities: {} };
            Array.from(facilitiesList.children).forEach(div => {
              const d = div.__data; if(!d) return;
              payload.facilities[d.fid] = { is_available: d.checkbox.checked ? 1 : 0, quantity: d.qty.value || null, note: d.note.value || null };
            });
            try{
              const res = await fetch('/admin/mosques/'+currentMosqueId+'/facilities', { method: 'POST', credentials:'same-origin', headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': getCsrf() }, body: JSON.stringify(payload) });
              const j = await res.json(); if(!j.success){ alert('Save failed'); return; }
              // then upload photos per-facility if any
              for(const div of Array.from(facilitiesList.children)){
                const d = div.__data; if(!d) continue;
                if(d.filesArr && d.filesArr.length){
                  const fd = new FormData();
                  d.filesArr.forEach(f=> fd.append('photos[]', f));
                  const caps = d.getCaptions(); caps.forEach((c, idx)=> fd.append('captions['+idx+']', c));
                  try{
                    const pres = await fetch('/admin/mosques/'+currentMosqueId+'/facilities/'+d.fid+'/photos', { method:'POST', body: fd, headers: { 'X-CSRF-TOKEN': getCsrf() }, credentials:'same-origin' });
                    const pj = await pres.json(); if(!pj.success){ console.warn('photo upload failed for facility', d.fid, pj); }
                  }catch(err){ console.warn('photo upload error', err); }
                }
              }
              // refresh to reflect new completion and photos
              location.reload();
            }catch(e){ console.warn(e); alert('Save failed'); }
          });

          // open photos modal
          async function openPhotosModal(mosqueId, facilityId, facilityName){
            currentFacilityId = facilityId; currentMosqueId = mosqueId;
            photosModalName.innerText = facilityName;
            photosList.innerHTML = '';
            // fetch existing
            try{
              const res = await fetch('/admin/mosques/'+mosqueId+'/facilities/'+facilityId+'/photos', { credentials:'same-origin' });
              const j = await res.json(); if(j.success && Array.isArray(j.photos)){
                j.photos.forEach(p => {
                  const el = document.createElement('div'); el.style.width='180px'; el.style.display='flex'; el.style.flexDirection='column'; el.style.gap='6px';
                  const img = document.createElement('img'); img.src = p.path; img.style.width='180px'; img.style.height='120px'; img.style.objectFit='cover';
                  const cap = document.createElement('div'); cap.style.fontSize='12px'; cap.innerText = p.caption || '';
                  const del = document.createElement('button'); del.className='btn btn-sm btn-outline-danger'; del.innerText='Delete'; del.addEventListener('click', async function(){ if(!confirm('Delete photo?')) return; const dres = await fetch('/admin/mosque-facility-photos/'+p.id, { method:'DELETE', headers: {'X-CSRF-TOKEN':getCsrf()}, credentials:'same-origin' }); const dj = await dres.json(); if(dj.success) el.remove(); });
                  el.appendChild(img); el.appendChild(cap); el.appendChild(del); photosList.appendChild(el);
                });
              }
            }catch(e){ console.warn('load photos', e); }
            const bs = new bootstrap.Modal(photosModal); bs.show();
          }

          photosUploadBtn.addEventListener('click', async function(){
            if(!photosInput.files || !photosInput.files.length) return alert('No files');
            const fd = new FormData(); Array.from(photosInput.files).forEach(f => fd.append('photos[]', f));
            // captions input not implemented per-file here for brevity
            try{
              const res = await fetch('/admin/mosques/'+currentMosqueId+'/facilities/'+currentFacilityId+'/photos', { method:'POST', body: fd, headers: { 'X-CSRF-TOKEN': getCsrf() }, credentials:'same-origin' });
              const j = await res.json(); if(j.success){ alert('Uploaded'); location.reload(); } else { alert('Upload failed'); }
            }catch(e){ console.warn(e); alert('Upload failed'); }
          });

          // delegate click for facilities buttons on table
          document.addEventListener('click', function(e){ const btn = e.target.closest && e.target.closest('.btn-manage-facilities'); if(!btn) return; const id = btn.getAttribute('data-id'); openFacilities(id); });
        })();

        // handle detail toggle with slide animation and initialize map on first open
        var iconDown = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M5 7l5 5 5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        var iconUp = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M5 13l5-5 5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';

        document.addEventListener('click', function(e){
          var btn = e.target.closest && e.target.closest('.toggle-detail');
          if(!btn) return;
          var id = btn.getAttribute('data-id');
          var row = document.getElementById('mosque-detail-' + id);
          if(!row) return;
          var collapse = row.querySelector('.detail-collapse');
          if(!collapse) return;
          var isClosed = !collapse.classList.contains('open');
          if(isClosed){
            // open
            row.style.display = '';
            // allow next tick to measure
            requestAnimationFrame(function(){
              collapse.classList.add('open');
              collapse.style.maxHeight = collapse.scrollHeight + 'px';
            });

            
            btn.innerHTML = iconUp;
            // initialize map when present
            var mapEl = document.getElementById('map-detail-' + id);
            if(mapEl && !mapEl.dataset.inited){
              var mObj = findMosque(id);
              if(mObj && mObj.latitude && mObj.longitude){
                try{
                  var lat = parseFloat(mObj.latitude);
                  var lng = parseFloat(mObj.longitude);
                  var map = L.map(mapEl).setView([lat,lng], 13);
                  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);
                  L.marker([lat,lng]).addTo(map);
                  mapEl.dataset.inited = '1';
                }catch(e){ console.warn('init detail map', id, e); }
              }
            }
          }else{
            // close
            collapse.style.maxHeight = collapse.scrollHeight + 'px';
            // force reflow then set to 0
            void collapse.offsetHeight;
            collapse.style.maxHeight = '0';
            collapse.classList.remove('open');
            btn.innerHTML = iconDown;
            collapse.addEventListener('transitionend', function onEnd(){
              row.style.display = 'none';
              collapse.removeEventListener('transitionend', onEnd);
            });
          }
        });

        // Modal map logic
        var modalMap = null; var modalMarker = null; var modalMapInited = false; var modalCurrentId = null;
        document.addEventListener('click', function(e){
          var btn = e.target.closest && e.target.closest('.btn-view-map');
          if(!btn) return;
          var id = btn.getAttribute('data-id');
          var mObj = findMosque(id);
          if(!mObj || !mObj.latitude || !mObj.longitude){
            alert('No coordinates available for this mosque');
            return;
          }
          // show modal
          var modalEl = document.getElementById('mapModal');
          var bsModal = new bootstrap.Modal(modalEl);
          bsModal.show();
          // init map if needed after modal shown
          modalEl.addEventListener('shown.bs.modal', function onShown(){
            try{
              var lat = parseFloat(mObj.latitude); var lng = parseFloat(mObj.longitude);
              var mapEl = document.getElementById('modal-map');
              if(!modalMapInited){
                modalMap = L.map(mapEl).setView([lat,lng], 14);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(modalMap);
                modalMarker = L.marker([lat,lng]).addTo(modalMap);
                modalMapInited = true;
              }else{
                modalMap.setView([lat,lng], 14);
                if(modalMarker) modalMarker.setLatLng([lat,lng]);
              }
              modalMap.invalidateSize();
            }catch(err){ console.warn('modal map init', err); }
            modalEl.removeEventListener('shown.bs.modal', onShown);
          });
        });
      })();
    </script>
  @endpush
</x-admin.layout>
