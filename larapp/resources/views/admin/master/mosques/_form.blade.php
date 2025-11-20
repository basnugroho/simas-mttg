<div>
  <style>
    .form-row{ display:flex; align-items:flex-start; gap:12px; margin-bottom:12px; }
    .form-row label{ width:160px; min-width:120px; font-weight:600; margin-top:6px; }
    .form-row .field{ flex:1; }
    @media (max-width:800px){ .form-row{ flex-direction:column } .form-row label{ width:auto; } }
  </style>

  <div class="form-row">
    <label class="form-label">Name</label>
    <div class="field"><input type="text" name="name" class="form-input" value="{{ old('name', $mosque->name ?? '') }}" required /></div>
  </div>

  <div class="form-row">
    <label class="form-label">Type</label>
    <div class="field">
      <select name="type" class="form-input">
        <option value="">-- Select Type --</option>
        <option value="MASJID" {{ (old('type', $mosque->type ?? '') == 'MASJID') ? 'selected' : '' }}>Masjid</option>
        <option value="MUSHOLLA" {{ (old('type', $mosque->type ?? '') == 'MUSHOLLA') ? 'selected' : '' }}>Mushalla</option>
      </select>
    </div>
  </div>

  <div class="form-row">
    <label class="form-label">Regional</label>
    <div class="field">
  <select name="regional_id" class="form-input" data-selected="{{ old('regional_id', $mosque->regional_id ?? ($lockedValues['regional_id'] ?? '')) }}" data-locked="{{ $lockedValues['regional_id'] ?? '' }}" @if(!empty($lockedFields) && in_array('regional_id', $lockedFields)) disabled @endif>
        @if(!empty($lockedLabels['regional_id']))
          <option value="{{ $lockedValues['regional_id'] }}">{{ $lockedLabels['regional_id'] }}</option>
        @endif
        <option value="">-- Select Regional --</option>
        @foreach(($regionals ?? []) as $r)
          <option value="{{ $r->id }}" {{ (old('regional_id', $mosque->regional_id ?? '') == $r->id) ? 'selected' : '' }}>{{ $r->name }}</option>
        @endforeach
      </select>
      @if(!empty($lockedValues['regional_id']) && (!empty($lockedFields) && in_array('regional_id', $lockedFields)))
        <input type="hidden" name="regional_id" value="{{ old('regional_id', $mosque->regional_id ?? $lockedValues['regional_id']) }}" />
      @endif
      <div class="field-error" data-for="regional_id" style="display:none;color:#dc2626;font-size:13px;margin-top:6px"></div>
    </div>
  </div>

  <div class="form-row">
    <label class="form-label">Area</label>
    <div class="field">
  <select name="area_id" class="form-input" data-selected="{{ old('area_id', $mosque->area_id ?? ($lockedValues['area_id'] ?? '')) }}" data-locked="{{ $lockedValues['area_id'] ?? '' }}" @if(!empty($lockedFields) && in_array('area_id', $lockedFields)) disabled @endif>
        @if(!empty($lockedLabels['area_id']))
          <option value="{{ $lockedValues['area_id'] }}">{{ $lockedLabels['area_id'] }}</option>
        @endif
        <option value="">-- Select Area --</option>
        {{-- If the controller loaded a subtree (edit page), populate area options server-side so the select shows immediately. --}}
        @if(isset($regions) && $regions->count())
          @foreach($regions->where('level','AREA')->sortBy('name') as $a)
            <option value="{{ $a->id }}" {{ (old('area_id', $mosque->area_id ?? '') == $a->id) ? 'selected' : '' }}>{{ $a->name }}</option>
          @endforeach
        @endif
      </select>
      @if(!empty($lockedValues['area_id']) && (!empty($lockedFields) && in_array('area_id', $lockedFields)))
        <input type="hidden" name="area_id" value="{{ old('area_id', $mosque->area_id ?? $lockedValues['area_id']) }}" />
      @endif
      <div class="field-error" data-for="area_id" style="display:none;color:#dc2626;font-size:13px;margin-top:6px"></div>
    </div>
  </div>

  <div class="form-row">
    <label class="form-label">Witel</label>
    <div class="field">
  <select name="witel_id" class="form-input" data-selected="{{ old('witel_id', $mosque->witel_id ?? ($lockedValues['witel_id'] ?? '')) }}" data-locked="{{ $lockedValues['witel_id'] ?? '' }}" @if(!empty($lockedFields) && in_array('witel_id', $lockedFields)) disabled @endif>
        @if(!empty($lockedLabels['witel_id']))
          <option value="{{ $lockedValues['witel_id'] }}">{{ $lockedLabels['witel_id'] }}</option>
        @endif
        <option value="">-- Select Witel --</option>
        @foreach(($witels ?? []) as $w)
          <option value="{{ $w->id }}" {{ (old('witel_id', $mosque->witel_id ?? '') == $w->id) ? 'selected' : '' }}>{{ $w->name }}</option>
        @endforeach
      </select>
      @if(!empty($lockedValues['witel_id']) && (!empty($lockedFields) && in_array('witel_id', $lockedFields)))
        <input type="hidden" name="witel_id" value="{{ old('witel_id', $mosque->witel_id ?? $lockedValues['witel_id']) }}" />
      @endif
    </div>
  </div>

  <div class="form-row">
    <label class="form-label">STO</label>
    <div class="field">
  <select name="sto_id" class="form-input" data-selected="{{ old('sto_id', $mosque->sto_id ?? ($lockedValues['sto_id'] ?? '')) }}" data-locked="{{ $lockedValues['sto_id'] ?? '' }}" @if(!empty($lockedFields) && in_array('sto_id', $lockedFields)) disabled @endif>
        @if(!empty($lockedLabels['sto_id']))
          <option value="{{ $lockedValues['sto_id'] }}">{{ $lockedLabels['sto_id'] }}</option>
        @endif
        <option value="">-- Select STO --</option>
        @foreach(($stos ?? []) as $s)
          <option value="{{ $s->id }}" {{ (old('sto_id', $mosque->sto_id ?? '') == $s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
        @endforeach
      </select>
      @if(!empty($lockedValues['sto_id']) && (!empty($lockedFields) && in_array('sto_id', $lockedFields)))
        <input type="hidden" name="sto_id" value="{{ old('sto_id', $mosque->sto_id ?? $lockedValues['sto_id']) }}" />
      @endif
    </div>
  </div>

  <div class="form-row">
    <label class="form-label">Address</label>
    <div class="field"><input type="text" name="address" class="form-input" value="{{ old('address', $mosque->address ?? '') }}" /></div>
  </div>

  <div class="form-row">
    <label class="form-label">Tahun Didirikan</label>
    <div class="field"><input type="number" name="tahun_didirikan" class="form-input" value="{{ old('tahun_didirikan', $mosque->tahun_didirikan ?? '') }}" /></div>
  </div>

  <div class="form-row">
    <label class="form-label">Jumlah BKM (pengurus)</label>
    <div class="field"><input type="number" name="jml_bkm" class="form-input" value="{{ old('jml_bkm', $mosque->jml_bkm ?? 0) }}" /></div>
  </div>

  <div class="form-row">
    <label class="form-label">Luas Tanah (m2)</label>
    <div class="field"><input type="number" step="0.01" name="luas_tanah" class="form-input" value="{{ old('luas_tanah', $mosque->luas_tanah ?? '') }}" /></div>
  </div>

  <div class="form-row">
    <label class="form-label">Daya Tampung</label>
    <div class="field"><input type="number" name="daya_tampung" class="form-input" value="{{ old('daya_tampung', $mosque->daya_tampung ?? '') }}" /></div>
  </div>

  <div class="form-row">
    <label class="form-label">Photos (multiple)</label>
    <div class="field">
      <div id="photo-dropzone" style="border:1px dashed #d1d5db;padding:16px;border-radius:8px;background:#fff;display:flex;flex-direction:column;gap:12px;min-height:120px">
        <div style="display:flex;align-items:center;gap:12px;">
          <div style="flex:1;color:#6b7280">Drag & drop images here or <button type="button" id="photo-browse" class="btn btn-link" style="padding:0">browse</button></div>
          <div style="font-size:12px;color:#9ca3af">jpg, png — max 5MB recommended</div>
        </div>

        {{-- Existing uploaded photos (edit mode) --}}
        @if(!empty($mosque) && $mosque->exists && $mosque->photos && $mosque->photos->count())
          <div id="existing-photo-list" style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:8px">
            @foreach($mosque->photos as $photo)
              <div class="existing-photo" data-photo-id="{{ $photo->id }}" style="width:180px">
                <div style="position:relative">
                  <img src="{{ asset('storage/'.$photo->path) }}" style="width:180px;height:120px;object-fit:cover;border-radius:6px;border:1px solid #e6e6e6" alt="photo-{{ $photo->id }}" />
                </div>
                <div style="margin-top:6px;font-size:12px;color:#374151">
                  <label style="display:flex;align-items:center;gap:8px"><input type="checkbox" name="delete_photos[]" value="{{ $photo->id }}"> Delete</label>
                  <input type="text" name="existing_captions[{{ $photo->id }}]" class="form-input" value="{{ old('existing_captions.'.$photo->id, $photo->caption ?? '') }}" placeholder="Caption (optional)" style="margin-top:6px;font-size:12px;width:100%" />
                </div>
              </div>
            @endforeach
          </div>
        @endif

        <input type="file" id="photos-input" accept="image/*" multiple style="display:none" name="photos[]" />
        <div id="photo-previews" style="display:flex;gap:12px;flex-wrap:wrap"></div>
        <div style="color:#6b7280;font-size:13px">You can add captions per photo after adding them. Photos will be uploaded when you save the mosque.</div>
      </div>
    </div>
  </div>

  <div class="form-row">
    <label class="form-label">Koordinat</label>
    <div class="field">
      <div id="map-picker" style="height:320px;border:1px solid #e5e7eb;border-radius:8px"></div>
      <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $mosque->latitude ?? '') }}">
      <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $mosque->longitude ?? '') }}">
      <div style="margin-top:6px;color:#374151;font-size:13px">Koordinat: <span id="coords-display">{{ old('latitude', $mosque->latitude ?? '') }}{{ $mosque->latitude && $mosque->longitude ? ',' : '' }}{{ old('longitude', $mosque->longitude ?? '') }}</span></div>
    </div>
  </div>

</div>
 
@push('scripts')
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    (function(){
  // Data arrays from server
  const regionals = <?php echo json_encode($regionals ?? [], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
  const witels = <?php echo json_encode($witels ?? [], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
  const stos = <?php echo json_encode($stos ?? [], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
  // Current user scope info (optional) passed from controller
  const myRole = <?php echo json_encode($myRole ?? null, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
  const myAssignments = <?php echo json_encode($myAssignments ?? [], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
    // for create we avoid shipping full regions payload; if available, regionsAll will be non-empty
    const regionsAll = <?php echo json_encode($regions ?? [], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;

      // build a map id -> parent_id for ancestry lookup (only when regionsAll provided)
      const parentMap = {};
      if(Array.isArray(regionsAll) && regionsAll.length > 0){ regionsAll.forEach(r => { parentMap[String(r.id)] = r.parent_id == null ? null : String(r.parent_id); }); }
      const hasParentMap = Object.keys(parentMap).length > 0;

      function isDescendantOf(id, ancestorId){
        if(!id || !ancestorId) return false;
        if(!Object.keys(parentMap).length) return false;
        let cur = String(id);
        const target = String(ancestorId);
        while(cur){ if(cur === target) return true; cur = parentMap[cur] || null; }
        return false;
      }

      // select elements will be queried at init-time so this code also works when the form is injected via AJAX
      let selRegional, selArea, selWitel, selSto;

      function populate(selectEl, items, placeholder){
        if(!selectEl) return;
        let html = '<option value="">' + placeholder + '</option>';
        html += items.map(i=>`<option value="${i.id}">${i.name}</option>`).join('');
        selectEl.innerHTML = html;
      }

      async function filterAreasByRegional(rid){
        if(!selArea) return;
        if(Array.isArray(regionsAll) && regionsAll.length){
          const items = regionsAll.filter(r => (r.level && String(r.level).toUpperCase() === 'AREA') && isDescendantOf(r.id, rid));
          populate(selArea, items, '-- Select Area --');
          return;
        }
        try{
          // Prefer descendants search first (handles intermediate levels reliably, same as Edit page local subtree behavior)
          console.debug('filterAreasByRegional: attempting descendants for', rid);
          const res2 = await fetch('{{ route("admin.regions.children") }}?parent_id='+encodeURIComponent(rid)+'&level=AREA&descendants=1', { headers: { 'X-Requested-With':'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' });
          if(res2.ok){ const json2 = await res2.json(); if(Array.isArray(json2) && json2.length){ console.debug('filterAreasByRegional: descendants returned', json2.length); populate(selArea, json2 || [], '-- Select Area --'); return; } }
          // fallback: try direct children of regional with level=AREA
          console.debug('filterAreasByRegional: descendants empty, trying direct children for', rid);
          const res = await fetch('{{ route("admin.regions.children") }}?parent_id='+encodeURIComponent(rid)+'&level=AREA', { headers: { 'X-Requested-With':'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' });
          if(res.ok){ const json = await res.json(); populate(selArea, json || [], '-- Select Area --'); return; }
          populate(selArea, [], '-- Select Area --');
        }catch(e){ console.warn('fetch areas failed', e); populate(selArea, [], '-- Select Area --'); }
      }

      async function filterWitelsByAreaOrRegional(areaId, regionalId){
        if(!selWitel) return;
        if(Array.isArray(regionsAll) && regionsAll.length){
          let items = [];
          if(areaId){ items = witels.filter(w => isDescendantOf(w.id, areaId)); }
          else if(regionalId){ items = witels.filter(w => isDescendantOf(w.id, regionalId)); }
          else items = witels.slice();
          populate(selWitel, items, '-- Select Witel --');
          return;
        }
        try{
          if(areaId){
            const r = await fetch('{{ route("admin.regions.children") }}?parent_id='+encodeURIComponent(areaId)+'&level=WITEL', { headers: { 'X-Requested-With':'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' });
            if(r.ok){ const j = await r.json(); if(Array.isArray(j) && j.length){ populate(selWitel,j,'-- Select Witel --'); return; } }
          }
          if(regionalId){ const r2 = await fetch('{{ route("admin.regions.children") }}?parent_id='+encodeURIComponent(regionalId)+'&level=WITEL&descendants=1', { headers: { 'X-Requested-With':'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' }); if(r2.ok){ const j2 = await r2.json(); populate(selWitel,j2 || [],'-- Select Witel --'); return; } }
          populate(selWitel, [], '-- Select Witel --');
        }catch(e){ console.warn('fetch witels failed', e); populate(selWitel, [], '-- Select Witel --'); }
      }

      async function filterStosByWitel(wid){
        if(!selSto) return;
        if(Array.isArray(regionsAll) && regionsAll.length){
          const items = stos.filter(s => isDescendantOf(s.id, wid));
          populate(selSto, items, '-- Select STO --');
          return;
        }
        try{
          const r = await fetch('{{ route("admin.regions.children") }}?parent_id='+encodeURIComponent(wid)+'&level=STO', { headers: { 'X-Requested-With':'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' });
          if(r.ok){ const j = await r.json(); populate(selSto, j || [], '-- Select STO --'); return; }
          populate(selSto, [], '-- Select STO --');
        }catch(e){ console.warn('fetch stos failed', e); populate(selSto, [], '-- Select STO --'); }
      }

      // initializer that will run on full page load or when the page is injected via SPA loader
      async function initDependentRegionSelects(){
        selRegional = document.querySelector('select[name="regional_id"]');
        selArea = document.querySelector('select[name="area_id"]');
        selWitel = document.querySelector('select[name="witel_id"]');
        selSto = document.querySelector('select[name="sto_id"]');

        // attach listeners
        if(selRegional){
          selRegional.addEventListener('change', async function(){
            const rid = this.value || null;
            if(rid) {
              populate(selArea, [], '-- Select Area --');
              populate(selWitel, [], '-- Select Witel --');
              populate(selSto, [], '-- Select STO --');
              await filterAreasByRegional(rid);
              try{ selArea.value = ''; }catch(e){}
              await filterWitelsByAreaOrRegional(null, rid);
            } else {
              populate(selArea, [], '-- Select Area --');
              populate(selWitel, witels, '-- Select Witel --');
              populate(selSto, [], '-- Select STO --');
            }
          });
        }
        if(selArea){
          selArea.addEventListener('change', function(){
            const aid = this.value || null;
            const rid = selRegional ? (selRegional.value || null) : null;
            if(aid) filterWitelsByAreaOrRegional(aid, rid);
            else filterWitelsByAreaOrRegional(null, rid);
            populate(selSto, [], '-- Select STO --');
          });
        }
        if(selWitel){
          selWitel.addEventListener('change', function(){
            const wid = this.value || null;
            if(wid) filterStosByWitel(wid); else populate(selSto, [], '-- Select STO --');
          });
        }

        // Apply server-determined locks (if any). Controller may pass lockedValues via blade variables.
        try{
          let lockedRegional = selRegional ? selRegional.getAttribute('data-locked') || null : null;
          let lockedArea = selArea ? selArea.getAttribute('data-locked') || null : null;
          let lockedWitel = selWitel ? selWitel.getAttribute('data-locked') || null : null;
          let lockedSto = selSto ? selSto.getAttribute('data-locked') || null : null;

          // remember original disabled state so we can reapply after population
          const origAreaDisabled = selArea ? selArea.disabled : false;
          const origWitelDisabled = selWitel ? selWitel.disabled : false;
          const origStoDisabled = selSto ? selSto.disabled : false;

          // Fallback: if controller didn't provide a locked value but the select is disabled
          // and the server passed `myAssignments`, use that to prefill the single assigned id.
          try{
            if((!lockedRegional || lockedRegional === '') && selRegional && selRegional.disabled){
              if(myAssignments && myAssignments['admin_regional'] && myAssignments['admin_regional'].length === 1){ lockedRegional = String(myAssignments['admin_regional'][0]); }
            }
            if((!lockedArea || lockedArea === '') && selArea && selArea.disabled){
              if(myAssignments && myAssignments['admin_area'] && myAssignments['admin_area'].length === 1){ lockedArea = String(myAssignments['admin_area'][0]); }
            }
            if((!lockedWitel || lockedWitel === '') && selWitel && selWitel.disabled){
              if(myAssignments && myAssignments['admin_witel'] && myAssignments['admin_witel'].length === 1){ lockedWitel = String(myAssignments['admin_witel'][0]); }
            }
            if((!lockedSto || lockedSto === '') && selSto && selSto.disabled){
              if(myAssignments && myAssignments['admin_sto'] && myAssignments['admin_sto'].length === 1){ lockedSto = String(myAssignments['admin_sto'][0]); }
            }
          }catch(e){ /* ignore */ }

          // If regional locked, set and populate dependents. Temporarily enable dependent selects so
          // they can be populated even when server rendered them disabled.
          if(lockedRegional){
            try{ selRegional.value = String(lockedRegional); selRegional.disabled = true; }catch(e){}
            if(selArea) selArea.disabled = false;
            if(selWitel) selWitel.disabled = false;
            await filterAreasByRegional(lockedRegional);
            // if controller provided an explicit lockedArea, apply it; otherwise leave selection blank
            if(lockedArea){ try{ selArea.value = String(lockedArea); }catch(e){} }
            await filterWitelsByAreaOrRegional(lockedArea || null, lockedRegional);
          }

          // If area locked (explicit), set and disable; if not explicit but original was disabled, re-disable
          if(lockedArea){
            try{ selArea.value = String(lockedArea); }catch(e){}
            try{ selArea.disabled = true; }catch(e){}
            await filterWitelsByAreaOrRegional(lockedArea, lockedRegional || null);
          } else if (origAreaDisabled) {
            // re-disable area if server originally had it disabled
            try{ selArea.disabled = true; }catch(e){}
          }

          // If witel locked, set and disable, then populate stos
          if(lockedWitel){
            try{ selWitel.value = String(lockedWitel); }catch(e){}
            try{ selWitel.disabled = true; }catch(e){}
            await filterStosByWitel(lockedWitel);
          } else if (origWitelDisabled) {
            try{ selWitel.disabled = true; }catch(e){}
          }

          // If sto locked, set and disable
          if(lockedSto){ try{ selSto.value = String(lockedSto); selSto.disabled = true; }catch(e){} }
          else if (origStoDisabled) { try{ selSto.disabled = true; }catch(e){} }
        }catch(e){ console.warn('apply locks failed', e); }

        // on init, if regional selected, populate dependent selects and preserve selected values
        const curRegional = selRegional ? (selRegional.getAttribute('data-selected') || selRegional.value || null) : null;
        const curArea = selArea ? (selArea.getAttribute('data-selected') || null) : null;
        const curWitel = selWitel ? (selWitel.getAttribute('data-selected') || selWitel.value || null) : null;
        const curSto = selSto ? (selSto.getAttribute('data-selected') || selSto.value || null) : null;

        if(curRegional){
          await filterAreasByRegional(curRegional);
          if(curArea) try{ selArea.value = curArea; }catch(e){}
          const chosenArea = (selArea && selArea.value) || curArea || null;
          await filterWitelsByAreaOrRegional(chosenArea, curRegional);
          if(curWitel) try{ selWitel.value = curWitel; }catch(e){}
          const chosenWitel = (selWitel && selWitel.value) || curWitel || null;
          if(chosenWitel) await filterStosByWitel(chosenWitel);
          if(curSto) try{ selSto.value = curSto; }catch(e){}
        }
      }

      // run immediately or when DOM ready
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function(){ initDependentRegionSelects().catch(e=>console.warn('init regions failed', e)); });
      } else {
        // already ready
        initDependentRegionSelects().catch(e=>console.warn('init regions failed', e));
      }

      // register with SPA page-init registry if available so injected pages also run this initializer
      try{ if(window && typeof window.registerPageInit === 'function'){ window.registerPageInit(window.location.pathname, initDependentRegionSelects); } }catch(e){}


      // basic client-side validation on submit: inline messages under fields
      const form = document.currentScript ? document.currentScript.closest('form') : document.querySelector('form');
      // fallback: choose nearest form
      const mosqueForm = form || document.querySelector('form');

      function showFieldError(name, msg){
        try{
          const el = document.querySelector('.field-error[data-for="'+name+'"]');
          if(!el) return;
          el.innerText = msg || '';
          el.style.display = msg ? 'block' : 'none';
        }catch(e){}
      }
      function clearFieldError(name){ showFieldError(name, ''); }

      // clear errors when user changes selects
      [selRegional, selArea, selWitel, selSto].forEach(s => { if(s) s.addEventListener('change', function(){ clearFieldError(this.name); }); });

      if(mosqueForm){
        mosqueForm.addEventListener('submit', function(e){
          // clear previous errors
          ['regional_id','area_id','witel_id','sto_id'].forEach(k=>clearFieldError(k));
          let hasError = false;
          const r = selRegional ? selRegional.value || null : null;
          const a = selArea ? selArea.value || null : null;
          const w = selWitel ? selWitel.value || null : null;
          const s = selSto ? selSto.value || null : null;

          // validate area belongs to regional (only when we have parent map available)
          if(hasParentMap && a && r){
            if(!isDescendantOf(a, r)){
              showFieldError('area_id', 'Area yang dipilih tidak terkait dengan Regional.'); hasError = true;
            }
          }
          // validate witel belongs to area or regional
          if(hasParentMap && w){
            if(a){ if(!isDescendantOf(w, a)){ showFieldError('witel_id','Witel tidak terkait dengan Area yang dipilih.'); hasError = true; } }
            else if(r){ if(!isDescendantOf(w, r)){ showFieldError('witel_id','Witel tidak terkait dengan Regional yang dipilih.'); hasError = true; } }
          }
          // validate sto belongs to witel
          if(hasParentMap && s && w){ if(!isDescendantOf(s, w)){ showFieldError('sto_id','STO tidak terkait dengan Witel yang dipilih.'); hasError = true; } }

          if(hasError){ e.preventDefault(); return false; }
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

        // attach file object for later collection by the submit fallback
        try{ wrap.__fileObj = file; }catch(e){}
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
    // expose the filesArr and a helper to the outer scope so submit handler can attach them to FormData
    (function(){
      // collector: read attached File objects from preview nodes (we attach them when creating previews)
      window.__collectPreviewFiles = function(){
        var previews = document.getElementById('photo-previews');
        var files = [];
        if(!previews) return files;
        Array.from(previews.children).forEach(function(el){ if(el && el.__fileObj) files.push(el.__fileObj); });
        return files;
      };
    })();

    // Intercept form submit: if there are client-managed files (previews with attached File objects), submit via fetch with FormData
    (function(){
      var form = document.currentScript ? document.currentScript.closest('form') : document.querySelector('form');
      var mosqueForm = form || document.querySelector('form');
      if(!mosqueForm) return;
      mosqueForm.addEventListener('submit', function(e){
        try{
          var previews = document.getElementById('photo-previews');
          if(!previews) return; // no previews UI
          // gather file objects attached to preview nodes
          var files = [];
          Array.from(previews.children).forEach(function(el){ if(el && el.__fileObj) files.push(el.__fileObj); });
          if(!files.length) return; // no client-managed files, proceed with normal submit

          // prevent normal submit and send via fetch
          e.preventDefault();
          var fd = new FormData(mosqueForm);
          // append files from previews
          files.forEach(function(f){ fd.append('photos[]', f, f.name); });

          // when sending FormData for files, always use POST so PHP/Laravel can parse uploaded files.
          // If the form intends to be PUT/PATCH, include the _method override in the FormData.
          // determine original intent (PUT/PATCH) and ensure Laravel sees the _method override
          var origMethodEl = mosqueForm.querySelector('input[name="_method"]');
          var origMethod = origMethodEl ? (origMethodEl.value || mosqueForm.method || 'POST') : (mosqueForm.method || 'POST');
          var httpMethod = 'POST'; // always send FormData via POST so PHP receives files
          if(origMethod && String(origMethod).toUpperCase() !== 'POST'){
            // always append the _method key (no leading spaces) so Laravel can detect it
            try{ fd.append('_method', origMethod); }catch(e){ /* ignore append errors */ }
          }
          fetch(mosqueForm.action, { method: httpMethod, body: fd, credentials: 'same-origin' })
            .then(function(res){
              if(res.redirected){ window.location = res.url; return; }
              if(res.ok){ window.location.reload(); return; }
              window.location.reload();
            }).catch(function(err){ console.warn('Submit failed', err); window.location.reload(); });
        }catch(err){ console.warn('submit handler error', err); }
      });
    })();
  </script>
@endpush
