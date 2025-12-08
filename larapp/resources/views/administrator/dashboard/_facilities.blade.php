<div class="col-lg-6">
  <div class="card p-3 shadow-sm h-100">
    <h6 class="mb-3">Masjid / Musholla (Fasilitas Belum Lengkap)</h6>
    <div class="row">
      <div class="col-12">
        
        <div class="table-responsive" style="max-height:360px;overflow-y:auto;">
          <table class="table table-sm mb-0">
            <thead>
              <tr>
                <th style="min-width:50px">Nama</th>
                <th style="min-width:100px">Witel / STO</th>
                <th style="width:10px">Jenis</th>
                <th class="text-end" style="width:110px">% Lengkap</th>
              </tr>
            </thead>
            <tbody>
              @php
                $masjids = $incompleteList->filter(function($m){ return strtolower($m->type ?? 'masjid') !== 'musholla'; });
                $mushollas = $incompleteList->filter(function($m){ return strtolower($m->type ?? 'masjid') === 'musholla'; });
                // Merge and keep order by ascending completion_percentage
                $rows = $masjids->merge($mushollas)->sortBy(function($m){ return $m->completion_percentage ?? 0; });

                // Prepare a JSON-friendly array for client-side filtering (includes coordinates)
                $facRows = $rows->map(function($m){
                  $stoName = optional($m->sto)->name;
                  $witelName = optional($m->witel)->name;
                  if ($witelName && $stoName) {
                    $areaLabel = 'Witel ' . $witelName . ' - STO ' . $stoName;
                  } elseif ($stoName) {
                    $areaLabel = 'STO ' . $stoName;
                  } elseif ($witelName) {
                    $areaLabel = 'Witel ' . $witelName;
                  } else {
                    $areaLabel = optional($m->area)->name ?? (method_exists($m, 'regionPath') ? $m->regionPath() : '');
                  }
                  return [
                    'id' => $m->id ?? null,
                    'name' => $m->name ?? '',
                    'type' => strtolower($m->type ?? 'masjid'),
                    'regional_id' => optional($m->regional)->id ?? null,
                    'area_id' => optional($m->area)->id ?? null,
                    'witel_id' => optional($m->witel)->id ?? null,
                    'sto_id' => optional($m->sto)->id ?? null,
                    'area_label' => $areaLabel,
                    'witel_label' => optional($m->witel)->name ?? '',
                    'sto_label' => optional($m->sto)->name ?? '',
                    'city' => $m->city ?? '',
                    'province' => $m->province ?? '',
                    'completion_percentage' => $m->completion_percentage ?? $m->completionPercentage ?? null,
                    'lat' => $m->latitude ?? $m->lat ?? null,
                    'lng' => $m->longitude ?? $m->lng ?? null,
                  ];
                })->values();
              @endphp

              @if($facRows->isEmpty())
                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada masjid atau musholla yang data fasilitasnya kosong.</td></tr>
              @else
                @foreach($rows as $m)
                @php
                $pct = (int)($m->completion_percentage ?? $m->completionPercentage ?? 0);
                // Build a concise area label preferring "Witel X - STO Y" when available.
                $stoName = optional($m->sto)->name;
                $witelName = optional($m->witel)->name;
                if ($witelName && $stoName) {
                  $area_label = 'Witel ' . $witelName . ' - STO ' . $stoName;
                } elseif ($stoName) {
                  $area_label = 'STO ' . $stoName;
                } elseif ($witelName) {
                  $area_label = 'Witel ' . $witelName;
                } else {
                  $area_label = optional($m->area)->name ?? (method_exists($m, 'regionPath') ? $m->regionPath() : '-');
                }
                $typeLabel = strtolower($m->type ?? 'masjid') === 'musholla' ? 'Musholla' : 'Masjid';
                // progress bar color thresholds
                if ($pct >= 90) { $bar = 'bg-success'; }
                elseif ($pct >= 60) { $bar = 'bg-info'; }
                elseif ($pct >= 30) { $bar = 'bg-warning'; }
                else { $bar = 'bg-danger'; }
              @endphp
              <tr>
                <td>{{ $m->name }}</td>
                <td>{{ $area_label }}</td>
                <td><span class="badge bg-secondary">{{ $typeLabel }}</span></td>
                <td class="text-end">
                  <div class="d-flex align-items-center" style="gap:.5rem;justify-content:flex-end;">
                    <div style="flex:1;max-width:160px;background:#e9ecef;border-radius:.25rem;overflow:hidden;">
                      <div style="height:10px;width:{{ max(0,min(100,$pct)) }}%;" class="{{ $bar }}" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div style="min-width:38px;text-align:right;font-variant-numeric:tabular-nums">{{ $pct }}%</div>
                  </div>
                </td>
              </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const rows = @json($facRows ?? []);
  const selRegional = document.getElementById('facRegional');
  const selArea = document.getElementById('facArea');
  const selWitel = document.getElementById('facWitel');
  const selSto = document.getElementById('facSto');
  const selType = document.getElementById('facType');
  const resetBtn = document.getElementById('facReset');

  function setOptions(selectEl, items, placeholder){
    selectEl.replaceChildren(new Option(placeholder || 'Pilih...', ''));
    items.forEach(it => selectEl.appendChild(new Option(it.name, it.id)));
  }

  async function fetchRegions(params){
    const q = new URLSearchParams(params).toString();
    const res = await fetch(`/api/regions?${q}`);
    if(!res.ok) throw new Error('Gagal memuat wilayah');
    const json = await res.json();
    return json.data || [];
  }

  // Load regionals
  fetchRegions({ level: 'REGIONAL' }).then(list => {
    setOptions(selRegional, list, 'Semua Regional');
  }).catch(console.error);

  selRegional.addEventListener('change', async function(){
    const rid = this.value;
    if(!rid){ setOptions(selArea, [], 'Semua Area'); selArea.disabled = true; setOptions(selWitel, [], 'Semua Witel'); selWitel.disabled = true; setOptions(selSto, [], 'Semua STO'); selSto.disabled = true; applyFilters(); return; }
    try{
      const areas = await fetchRegions({ level:'AREA', parent_id: rid });
      setOptions(selArea, areas, 'Semua Area'); selArea.disabled = false; setOptions(selWitel, [], 'Semua Witel'); selWitel.disabled = true; setOptions(selSto, [], 'Semua STO'); selSto.disabled = true; applyFilters();
    }catch(e){ console.error(e); }
  });

  selArea.addEventListener('change', async function(){
    const aid = this.value;
    if(!aid){ setOptions(selWitel, [], 'Semua Witel'); selWitel.disabled = true; setOptions(selSto, [], 'Semua STO'); selSto.disabled = true; applyFilters(); return; }
    try{ const witels = await fetchRegions({ level:'WITEL', parent_id: aid }); setOptions(selWitel, witels, 'Semua Witel'); selWitel.disabled = false; setOptions(selSto, [], 'Semua STO'); selSto.disabled = true; applyFilters(); }catch(e){ console.error(e); }
  });

  selWitel.addEventListener('change', async function(){
    const wid = this.value; if(!wid){ setOptions(selSto, [], 'Semua STO'); selSto.disabled = true; applyFilters(); return; }
    try{ const stos = await fetchRegions({ level:'STO', parent_id: wid }); setOptions(selSto, stos, 'Semua STO'); selSto.disabled = false; applyFilters(); }catch(e){ console.error(e); }
  });

  selSto.addEventListener('change', applyFilters);
  selType.addEventListener('change', applyFilters);
  resetBtn.addEventListener('click', function(){ selRegional.value=''; selArea.value=''; selWitel.value=''; selSto.value=''; selType.value=''; selArea.disabled=true; selWitel.disabled=true; selSto.disabled=true; applyFilters(); });

  function applyFilters(){
    const r = selRegional.value || null;
    const a = selArea.value || null;
    const w = selWitel.value || null;
    const s = selSto.value || null;
    const t = (selType.value || null);

    const tbody = document.querySelector('.table-responsive table tbody');
    if(!tbody) return;
    tbody.replaceChildren();

    const filtered = rows.filter(row => {
      if (r && String(row.regional_id) !== String(r)) return false;
      if (a && String(row.area_id) !== String(a)) return false;
      if (w && String(row.witel_id) !== String(w)) return false;
      if (s && String(row.sto_id) !== String(s)) return false;
      if (t && String(row.type) !== String(t)) return false;
      return true;
    });

    if (!filtered.length){
      const tr = document.createElement('tr');
      tr.innerHTML = '<td colspan="4" class="text-center text-muted py-4">Belum ada masjid atau musholla yang sesuai filter.</td>';
      tbody.appendChild(tr);
      return;
    }

    filtered.forEach(row => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${escapeHtml(row.name)}</td>
        <td>${escapeHtml(row.area_label || row.city || '')}</td>
        <td><span class="badge bg-secondary">${escapeHtml(row.type || '')}</span></td>
        <td class="text-end">
          <div class="d-flex align-items-center" style="gap:.5rem;justify-content:flex-end;">
            <div style="flex:1;max-width:160px;background:#e9ecef;border-radius:.25rem;overflow:hidden;">
              <div style="height:10px;width:${Math.max(0,Math.min(100,row.completion_percentage||0))}%;" class="${progressClass(row.completion_percentage)}"></div>
            </div>
            <div style="min-width:38px;text-align:right;font-variant-numeric:tabular-nums">${row.completion_percentage ?? ''}%</div>
          </div>
        </td>
      `;
      tbody.appendChild(tr);
    });
    // Notify map (if present) about the filtered rows so markers can be updated
    try{
      const rowsForMap = filtered.map(r => ({
        id: r.id,
        name: r.name,
        type: r.type,
        lat: r.lat,
        lng: r.lng,
        completion_percentage: r.completion_percentage,
        city: r.city,
        province: r.province,
      }));
      window._lastFilteredRows = rowsForMap;
      window.dispatchEvent(new CustomEvent('mapFilterChange', { detail: { rows: rowsForMap } }));
    }catch(e){ /* ignore if window not available */ }
  }

  function progressClass(p){ p = Number(p) || 0; if(p>=90) return 'bg-success'; if(p>=60) return 'bg-info'; if(p>=30) return 'bg-warning'; return 'bg-danger'; }

  function escapeHtml(s){ if(!s) return ''; return String(s).replace(/[&<>"]/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m]); }); }

  // initial render
  applyFilters();
});
</script>
@endpush