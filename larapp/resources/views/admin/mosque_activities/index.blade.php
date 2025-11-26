<x-admin.layout title="Aktivitas Masjid">
  <div class="p-4">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <div>
        <h3 style="margin:0">Aktivitas Masjid</h3>
        <div style="font-size:12px;color:#6b7280;margin-top:4px">Admin · <a href="{{ route('dashboard') }}">Dashboard</a> / <strong>Aktivitas Masjid</strong></div>
      </div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div>
      <div id="assign" class="mb-3" style="background:#fff;padding:12px;border-radius:8px;box-shadow:0 8px 24px rgba(2,6,23,.04);">
        <h5>Buat Aktivitas Masjid</h5>
        <form method="POST" action="{{ route('admin.mosque_activities.store') }}" enctype="multipart/form-data">
          @csrf
          <div class="mb-2">
            <label class="form-label small">Aktivitas</label>
            <select name="activity_id" class="form-select">
              @foreach($activities as $a)
                <option value="{{ $a->id }}">{{ $a->activity_name }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-2">
            <label class="form-label small">Pilih Masjid</label>
            <select id="assign-mosque-select" name="mosque_ids[]" multiple class="form-select" style="min-height:120px">
              @foreach($mosques as $m)
                <option value="{{ $m->id }}" {{ in_array($m->id, $selected) ? 'selected' : '' }}>{{ $m->name }} — {{ $m->city?->name ?? $m->province?->name ?? '-' }}</option>
              @endforeach
            </select>
          </div>

          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label small">Mulai (datetime)</label>
              <input type="datetime-local" name="event_start" class="form-control" value="{{ request('event_start') ?? '' }}">
            </div>
            <div class="col-md-6">
              <label class="form-label small">Selesai (datetime)</label>
              <input type="datetime-local" name="event_end" class="form-control" value="{{ request('event_end') ?? '' }}">
            </div>
          </div>

          <div class="mb-2 mt-2">
            <label class="form-label small">Catatan</label>
            <textarea name="note" class="form-control" rows="3" placeholder="Catatan (opsional)"></textarea>
          </div>

          <div class="mb-2">
            <label class="form-label small">Foto (drag & drop atau klik) — tambahkan caption untuk masing-masing foto</label>
            <div id="photo-drop" class="border rounded p-3" style="min-height:120px;display:flex;flex-direction:column;gap:8px;position:relative;">
              <div style="flex:1;display:flex;align-items:center;justify-content:center;color:#6b7280">Tarik gambar ke sini atau gunakan tombol "Browse" untuk memilih file</div>
              <div id="photo-drop-debug" style="position:absolute;top:8px;left:8px;background:#10b981;color:#fff;padding:4px 8px;border-radius:6px;font-size:12px;display:none;z-index:50">JS loaded</div>
              <div style="display:flex;justify-content:center;gap:8px">
                <button type="button" id="photos-browse-btn" class="btn btn-sm btn-outline-secondary">Browse</button>
                <button type="button" id="photos-clear-btn" class="btn btn-sm btn-outline-danger">Clear</button>
              </div>
              <!-- hidden file input created inside drop area for reliable click triggering -->
              <input class="photos-input" type="file" name="photos[]" multiple accept="image/*" style="display:none">
            </div>
            <div id="photo-previews" class="mt-2" style="display:flex;flex-wrap:wrap;gap:8px"></div>
          </div>

          <div class="mb-2">
            <button class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>

      <div class="p-4 border rounded" style="background:#fff">
        <h3 class="font-semibold">Tabel Aktivitas Masjid</h3>
        <form method="GET" class="mb-3 flex space-x-2">
          <input type="text" name="filter_mosque" value="{{ request('filter_mosque') }}" placeholder="Cari nama masjid" class="border p-2 w-1/3">
          <input type="date" name="date_from" value="{{ request('date_from') }}" class="border p-2">
          <input type="date" name="date_to" value="{{ request('date_to') }}" class="border p-2">
          <button type="submit" class="bg-gray-200 p-2 rounded">Filter</button>
        </form>

        <div class="overflow-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="text-left">
                <th class="p-2">Masjid</th>
                <th class="p-2">Region</th>
                <th class="p-2">Aktivitas</th>
                <th class="p-2">Mulai</th>
                <th class="p-2">Selesai</th>
                <th class="p-2">Catatan</th>
                <th class="p-2">Photos</th>
                <th class="p-2">Pembuat</th>
                <th class="p-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($assignments as $a)
              <tr class="border-t align-middle" data-assignment-id="{{ $a->id }}">
                <td class="p-2">{{ $a->mosque_name }}</td>
                <td class="p-2" style="min-width:220px">{{ $a->region_path ?? (trim(($a->city_name ?? '') . ' / ' . ($a->province_name ?? ''))) }}</td>
                <td class="p-2">{{ $a->activity_name }}</td>
                <td class="p-2">{{ $a->event_start ? \Carbon\Carbon::parse($a->event_start)->toDateTimeString() : '-' }}</td>
                <td class="p-2">{{ $a->event_end ? \Carbon\Carbon::parse($a->event_end)->toDateTimeString() : '-' }}</td>
                <td class="p-2">{{ $a->note }}</td>
                <td class="p-2 text-center"><button type="button" class="btn btn-sm btn-outline-secondary toggle-photos" data-target="photos-{{ $a->id }}">▾</button></td>
                <td class="p-2">{{ $a->activity_creator ?? '-' }}</td>
                <td class="p-2">
                  <form action="{{ route('admin.mosque_activities.destroy', $a->id) }}" method="POST" onsubmit="return confirm('Hapus assignment?')">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600 text-sm">Hapus</button>
                  </form>
                </td>
              </tr>
              <tr id="photos-{{ $a->id }}" class="photo-panel" style="display:none;background:#fafafa">
                <td colspan="9" style="padding:10px">
                  <div style="display:flex;gap:12px;flex-wrap:wrap">
                    @if(!empty($a->photos) && count($a->photos))
                      @foreach($a->photos as $p)
                        <div style="width:160px;border:1px solid #e5e7eb;padding:6px;border-radius:6px;background:#fff">
                          <img src="{{ Storage::url($p['path']) }}" style="width:100%;height:96px;object-fit:cover;display:block">
                          @if(!empty($p['caption']))<div style="font-size:12px;margin-top:6px">{{ $p['caption'] }}</div>@endif
                        </div>
                      @endforeach
                    @else
                      <div style="color:#6b7280">Tidak ada foto.</div>
                    @endif
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $assignments->links() }}
        </div>
      </div>
    </div>

  </div>
</x-admin.layout>

@push('scripts')
  <script>
    console.log('activity uploader script block loaded');
    // On page load, if URL contains mosque_ids params, preselect them in the assign form and scroll to it
    (function(){
      try{
        const params = new URLSearchParams(window.location.search);
        const values = params.getAll('mosque_ids[]');
        if(values && values.length){
          const sel = document.getElementById('assign-mosque-select');
          if(sel){
            Array.from(sel.options).forEach(o=>{ if(values.includes(o.value)) o.selected = true; });
            const el = document.getElementById('assign'); if(el){ el.scrollIntoView({behavior:'smooth'}); }
          }
        }
      }catch(e){}
    })();

    // Toggle photos panels in the assignments table
    (function(){
      document.addEventListener('click', function(e){
        const t = e.target.closest('.toggle-photos');
        if(!t) return;
        const targetId = t.getAttribute('data-target');
        const panel = document.getElementById(targetId);
        if(!panel) return;
        panel.style.display = panel.style.display === 'none' ? 'table-row' : 'none';
      });
    })();

    // Drag & drop + previews for photo uploads with per-photo caption inputs
    (function(){
      console.log('activity uploader init');
      const drop = document.getElementById('photo-drop');
      // hidden file input inside drop area (created in DOM markup)
      const input = drop.querySelector('.photos-input');
      const previews = document.getElementById('photo-previews');
      // current file list kept in a DataTransfer
      let dt = new DataTransfer();
      // captions aligned by index with dt.files
      let captions = [];

      function renderPreviews(){
        previews.innerHTML = '';
        Array.from(dt.files).forEach((file, idx)=>{
          const wrap = document.createElement('div');
          wrap.style.width = '160px'; wrap.style.border = '1px solid #e5e7eb'; wrap.style.padding='6px'; wrap.style.borderRadius='6px'; wrap.style.background='#fff';
          const img = document.createElement('img');
          img.style.width='100%'; img.style.height='96px'; img.style.objectFit='cover'; img.style.display='block';
          const reader = new FileReader();
          reader.onload = function(e){ img.src = e.target.result; };
          reader.readAsDataURL(file);

          const caption = document.createElement('input');
          caption.type='text'; caption.name='photo_captions[]'; caption.placeholder='Caption (opsional)'; caption.className='form-control mt-2';
          caption.style.width='100%';
          caption.value = captions[idx] || '';
          caption.addEventListener('input', (ev)=>{ captions[idx] = ev.target.value; });

          const remove = document.createElement('button');
          remove.type='button'; remove.textContent='Hapus'; remove.className='btn btn-sm btn-outline-danger mt-2';
          remove.style.width='100%';
          remove.addEventListener('click', ()=>{
            // remove file at idx
            const newDt = new DataTransfer();
            Array.from(dt.files).forEach((f,i)=>{ if(i!==idx) newDt.items.add(f); });
            dt = newDt; input.files = dt.files;
            // remove caption at idx to keep alignment
            captions.splice(idx, 1);
            renderPreviews();
          });

          wrap.appendChild(img);
          wrap.appendChild(caption);
          wrap.appendChild(remove);
          previews.appendChild(wrap);
        });
      }

      // Use browse button to trigger the hidden input inside drop area
      const browseBtn = (drop && drop.querySelector('#photos-browse-btn')) || document.getElementById('photos-browse-btn');
      if (browseBtn) browseBtn.addEventListener('click', ()=> { console.log('photos-browse-btn clicked'); if(input) input.click(); });
      const clearBtn = drop.querySelector('#photos-clear-btn') || document.getElementById('photos-clear-btn');
      if (clearBtn) clearBtn.addEventListener('click', ()=>{ dt = new DataTransfer(); if(input) input.files = dt.files; captions = []; renderPreviews(); });
      if(drop){
        // show debug badge so user can see JS ran
        const dbg = document.getElementById('photo-drop-debug'); if(dbg){ dbg.style.display = 'block'; }
        drop.addEventListener('dragover', (e)=>{ e.preventDefault(); drop.style.background='#f8fafc'; });
        drop.addEventListener('dragleave', ()=>{ drop.style.background='transparent'; });
        drop.addEventListener('drop', (e)=>{
          e.preventDefault(); drop.style.background='transparent';
          const files = Array.from(e.dataTransfer.files).filter(f=>f.type.startsWith('image/'));
          files.forEach(f=> { dt.items.add(f); captions.push(''); });
          if(input) input.files = dt.files; renderPreviews();
        });
      } else {
        console.warn('photo drop element not found');
      }

      if(input){
        input.addEventListener('change', (e)=>{
        const files = Array.from(e.target.files).filter(f=>f.type.startsWith('image/'));
        // rebuild dt to include new selections appended
        const newDt = new DataTransfer();
        Array.from(dt.files).forEach(f=> newDt.items.add(f));
        files.forEach(f=> { newDt.items.add(f); captions.push(''); });
        dt = newDt; input.files = dt.files; renderPreviews();
        });
      }
      // Global safeguard: prevent files dragged to the window from opening in a new tab
      // This helps when a drag lands outside the drop area and the browser defaults to opening the file
      window.addEventListener('dragover', function(e){ e.preventDefault(); });
      window.addEventListener('drop', function(e){ e.preventDefault(); });
    })();
  </script>
@endpush
