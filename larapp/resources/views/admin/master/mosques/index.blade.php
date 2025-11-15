<x-admin.layout title="Mosques">
  <div class="p-4">
    <div class="flex justify-between items-center mb-4">
      <h3>Mosques</h3>
      <a href="{{ route('admin.mosques.create') }}" class="btn btn-primary">Create Mosque</a>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Regional</th>
          <th>Witel</th>
          <th>STO</th>
          <th>Tahun</th>
          <th>BKM</th>
          <th>Capacity</th>
          <th style="width:180px">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($mosques as $m)
          <tr>
            <td style="width:40px">{{ $loop->iteration + ($mosques->currentPage()-1)*$mosques->perPage() }}</td>
            <td>{{ $m->name }}</td>
            <td>{{ $m->regional?->name }}</td>
            <td>{{ $m->witel?->name }}</td>
            <td>{{ $m->sto?->name }}</td>
            <td>{{ $m->tahun_didirikan ?? '-' }}</td>
            <td>{{ $m->jml_bkm ?? 0 }}</td>
            <td>{{ $m->daya_tampung ?? '-' }}</td>
            <td style="display:flex;gap:8px;align-items:center;justify-content:flex-end">
              <a href="{{ route('admin.mosques.edit', $m->id) }}" class="btn btn-sm">Edit</a>
              <form action="{{ route('admin.mosques.destroy', $m->id) }}" method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this mosque?')">Delete</button>
              </form>
              <button type="button" class="btn btn-sm btn-secondary btn-view-map" data-id="{{ $m->id }}" title="View map" style="margin-left:6px">
                <!-- map pin icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 11.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </button>
              <button type="button" class="btn btn-sm btn-outline-secondary toggle-detail" data-id="{{ $m->id }}" title="Show details" style="margin-left:6px">
                <!-- chevron down icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M5 7l5 5 5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </button>
            </td>
          </tr>

          {{-- Detail row hidden by default --}}
          <tr class="mosque-detail-row" id="mosque-detail-{{ $m->id }}" style="display:none;background:#fbfbfd">
            <td colspan="9">
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
    </style>
  @endpush

  @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
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
