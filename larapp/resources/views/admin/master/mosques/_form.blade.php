<div>
  <div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-input" value="{{ old('name', $mosque->name ?? '') }}" required />
  </div>
  <div class="mb-3">
    <label class="form-label">Regional</label>
    <select name="regional_id" class="form-input">
      <option value="">-- Select Regional --</option>
      @foreach(($regionals ?? []) as $r)
        <option value="{{ $r->id }}" {{ (old('regional_id', $mosque->regional_id ?? '') == $r->id) ? 'selected' : '' }}>{{ $r->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Witel</label>
    <select name="witel_id" class="form-input">
      <option value="">-- Select Witel --</option>
      @foreach(($witels ?? []) as $w)
        <option value="{{ $w->id }}" {{ (old('witel_id', $mosque->witel_id ?? '') == $w->id) ? 'selected' : '' }}>{{ $w->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">STO</label>
    <select name="sto_id" class="form-input">
      <option value="">-- Select STO --</option>
      @foreach(($stos ?? []) as $s)
        <option value="{{ $s->id }}" {{ (old('sto_id', $mosque->sto_id ?? '') == $s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Address</label>
    <input type="text" name="address" class="form-input" value="{{ old('address', $mosque->address ?? '') }}" />
  </div>
  <div class="mb-3">
    <label class="form-label">Tahun Didirikan</label>
    <input type="number" name="tahun_didirikan" class="form-input" value="{{ old('tahun_didirikan', $mosque->tahun_didirikan ?? '') }}" />
  </div>
  <div class="mb-3">
    <label class="form-label">Jumlah BKM (pengurus)</label>
    <input type="number" name="jml_bkm" class="form-input" value="{{ old('jml_bkm', $mosque->jml_bkm ?? 0) }}" />
  </div>
  <div class="mb-3">
    <label class="form-label">Luas Tanah (m2)</label>
    <input type="number" step="0.01" name="luas_tanah" class="form-input" value="{{ old('luas_tanah', $mosque->luas_tanah ?? '') }}" />
  </div>
  <div class="mb-3">
    <label class="form-label">Daya Tampung</label>
    <input type="number" name="daya_tampung" class="form-input" value="{{ old('daya_tampung', $mosque->daya_tampung ?? '') }}" />
  </div>
  <div class="mb-3">
    <label class="form-label">Photos (multiple)</label>
    <div id="photo-dropzone" style="border:1px dashed #d1d5db;padding:16px;border-radius:8px;background:#fff;display:flex;flex-direction:column;gap:12px;min-height:200px">
      <div style="display:flex;align-items:center;gap:12px;">
        <div style="flex:1;color:#6b7280">Drag & drop images here or <button type="button" id="photo-browse" class="btn btn-link" style="padding:0">browse</button></div>
        <div style="font-size:12px;color:#9ca3af">jpg, png — max 5MB recommended</div>
      </div>
      <input type="file" id="photos-input" accept="image/*" multiple style="display:none" name="photos[]" />
      <div id="photo-previews" style="display:flex;gap:12px;flex-wrap:wrap"></div>
      <div style="color:#6b7280;font-size:13px">You can add captions per photo after adding them. Photos will be uploaded when you save the mosque.</div>
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Koordinat</label>
    <div id="map-picker" style="height:420px;border:1px solid #e5e7eb;border-radius:8px"></div>
    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $mosque->latitude ?? '') }}">
    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $mosque->longitude ?? '') }}">
    <div style="margin-top:6px;color:#374151;font-size:13px">Koordinat: <span id="coords-display">{{ old('latitude', $mosque->latitude ?? '') }}{{ $mosque->latitude && $mosque->longitude ? ',' : '' }}{{ old('longitude', $mosque->longitude ?? '') }}</span></div>
  </div>
</div>
 
@push('scripts')
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    (function(){
      // Data arrays from server
      const regionals = @json($regionals ?? []);
      const witels = @json($witels ?? []);
      const stos = @json($stos ?? []);

      const selRegional = document.querySelector('select[name="regional_id"]');
      const selWitel = document.querySelector('select[name="witel_id"]');
      const selSto = document.querySelector('select[name="sto_id"]');

      function populate(selectEl, items, placeholder){
        if(!selectEl) return;
        let html = '<option value="">' + placeholder + '</option>';
        html += items.map(i=>`<option value="${i.id}">${i.name}</option>`).join('');
        selectEl.innerHTML = html;
      }

      function filterWitelsByRegional(rid){
        const items = witels.filter(w => Number(w.parent_id) === Number(rid));
        populate(selWitel, items, '-- Select Witel --');
      }

      function filterStosByWitel(wid){
        const items = stos.filter(s => Number(s.parent_id) === Number(wid));
        populate(selSto, items, '-- Select STO --');
      }

      if(selRegional){
        selRegional.addEventListener('change', function(){
          const rid = this.value || null;
          if(rid) filterWitelsByRegional(rid); else populate(selWitel, witels, '-- Select Witel --');
          // clear sto
          populate(selSto, [], '-- Select STO --');
        });
      }
      if(selWitel){
        selWitel.addEventListener('change', function(){
          const wid = this.value || null;
          if(wid) filterStosByWitel(wid); else populate(selSto, [], '-- Select STO --');
        });
      }

      // on load, if regional selected, populate dependent selects and preserve selected values
      document.addEventListener('DOMContentLoaded', function(){
        const curRegional = document.querySelector('select[name="regional_id"]').value || null;
        const curWitel = document.querySelector('select[name="witel_id"]').getAttribute('data-selected') || document.querySelector('select[name="witel_id"]').value || null;
        const curSto = document.querySelector('select[name="sto_id"]').getAttribute('data-selected') || document.querySelector('select[name="sto_id"]').value || null;
        if(curRegional) {
          filterWitelsByRegional(curRegional);
          if(curWitel) try{ document.querySelector('select[name="witel_id"]').value = curWitel; }catch(e){}
          if(document.querySelector('select[name="witel_id"]').value) filterStosByWitel(document.querySelector('select[name="witel_id"]').value);
          if(curSto) try{ document.querySelector('select[name="sto_id"]').value = curSto; }catch(e){}
        }
      });

      // basic client-side validation on submit: ensure selected parent match
      const form = document.currentScript ? document.currentScript.closest('form') : document.querySelector('form');
      // fallback: choose nearest form
      const mosqueForm = form || document.querySelector('form');
      if(mosqueForm){
        mosqueForm.addEventListener('submit', function(e){
          const r = selRegional ? selRegional.value : null;
          const w = selWitel ? selWitel.value : null;
          const s = selSto ? selSto.value : null;
          if(w && r){
            const wObj = witels.find(x=>String(x.id)===String(w));
            if(wObj && String(wObj.parent_id) !== String(r)){
              e.preventDefault(); alert('Witel yang dipilih tidak terkait dengan Regional yang dipilih. Periksa kembali.'); return false;
            }
          }
          if(s && w){
            const sObj = stos.find(x=>String(x.id)===String(s));
            if(sObj && String(sObj.parent_id) !== String(w)){
              e.preventDefault(); alert('STO yang dipilih tidak terkait dengan Witel yang dipilih. Periksa kembali.'); return false;
            }
          }
        });
      }

      // Leaflet map for picking coordinates
      try{
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const coordsDisplay = document.getElementById('coords-display');
        const mapEl = document.getElementById('map-picker');
        if(mapEl){
          const initialLat = parseFloat(latInput?.value) || -7.25;
          const initialLng = parseFloat(lngInput?.value) || 112.75;
          const zoom = (latInput?.value && lngInput?.value) ? 12 : 6;
          const map = L.map(mapEl).setView([initialLat, initialLng], zoom);
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);
          let marker = null;
          if(latInput?.value && lngInput?.value){
            marker = L.marker([initialLat, initialLng], {draggable:true}).addTo(map);
            marker.on('dragend', function(){ const p = marker.getLatLng(); latInput.value = p.lat.toFixed(6); lngInput.value = p.lng.toFixed(6); coordsDisplay.innerText = p.lat.toFixed(6)+','+p.lng.toFixed(6); });
          }
          map.on('click', function(e){
            const p = e.latlng;
            if(!marker) { marker = L.marker(p, {draggable:true}).addTo(map); marker.on('dragend', function(){ const p2 = marker.getLatLng(); latInput.value = p2.lat.toFixed(6); lngInput.value = p2.lng.toFixed(6); coordsDisplay.innerText = p2.lat.toFixed(6)+','+p2.lng.toFixed(6); }); }
            else marker.setLatLng(p);
            latInput.value = p.lat.toFixed(6);
            lngInput.value = p.lng.toFixed(6);
            coordsDisplay.innerText = p.lat.toFixed(6)+','+p.lng.toFixed(6);
          });
        }
      }catch(e){ console.warn('map init error', e); }
    })();
  </script>
  <script>
    // Drag & drop multi-photo with captions. Keeps a hidden file input (photos-input) in sync using DataTransfer.
    (function(){
      var photosInput = document.getElementById('photos-input');
      var previews = document.getElementById('photo-previews');
      var dropzone = document.getElementById('photo-dropzone');

      // local array of files
      var filesArr = [];

      function bytesToSize(bytes){
        var sizes = ['B','KB','MB','GB','TB']; if(bytes==0) return '0 B'; var i = parseInt(Math.floor(Math.log(bytes)/Math.log(1024)),10); return Math.round(bytes/Math.pow(1024,i),2) + ' ' + sizes[i];
      }

      function rebuildInput(){
        // rebuild DataTransfer and assign to input
        try{
          var dt = new DataTransfer();
          filesArr.forEach(function(f){ dt.items.add(f); });
          photosInput.files = dt.files;
        }catch(e){
          console.warn('DataTransfer not available', e);
        }
      }

      function createPreview(file, idx){
          var wrap = document.createElement('div'); wrap.className = 'photo-preview'; wrap.style = 'width:180px';
          var img = document.createElement('img'); img.style = 'width:180px;height:120px;object-fit:cover;border-radius:6px;border:1px solid #e6e6e6';
        var info = document.createElement('div'); info.style = 'margin-top:6px;font-size:12px;color:#374151';
        var caption = document.createElement('input'); caption.type='text'; caption.name='photo_captions[]'; caption.placeholder='Caption (optional)'; caption.className='form-input'; caption.style='width:100%;margin-top:6px;font-size:12px';
        var remove = document.createElement('button'); remove.type='button'; remove.className='btn btn-sm btn-outline-danger'; remove.style='margin-top:6px'; remove.innerText='Remove';

        // file meta
        info.innerText = file.name + ' (' + bytesToSize(file.size) + ')';
        wrap.appendChild(img); wrap.appendChild(info); wrap.appendChild(caption); wrap.appendChild(remove);

        // load preview
        var reader = new FileReader(); reader.onload = function(ev){ img.src = ev.target.result; }; reader.readAsDataURL(file);

        remove.addEventListener('click', function(){
          // remove from filesArr by strict identity
          var remIndex = filesArr.indexOf(file);
          if(remIndex > -1) filesArr.splice(remIndex,1);
          wrap.remove(); rebuildInput();
        });

        return wrap;
      }

      function addFiles(fileList){
        Array.from(fileList).forEach(function(f){
          // optional: validate type/size
          if(!f.type.startsWith('image/')) return;
          filesArr.push(f);
          var p = createPreview(f, filesArr.length-1);
          previews.appendChild(p);
        });
        rebuildInput();
      }

      // drag & drop handlers
      dropzone.addEventListener('dragover', function(e){ e.preventDefault(); dropzone.style.background='#fbfbfb'; });
      dropzone.addEventListener('dragleave', function(e){ dropzone.style.background=''; });
      dropzone.addEventListener('drop', function(e){ e.preventDefault(); dropzone.style.background=''; if(e.dataTransfer && e.dataTransfer.files) addFiles(e.dataTransfer.files); });

      // browse button opens file picker
      document.getElementById('photo-browse').addEventListener('click', function(){ photosInput.click(); });
      photosInput.addEventListener('change', function(e){ if(e.target.files) addFiles(e.target.files); photosInput.value=''; });

    })();
  </script>
@endpush
