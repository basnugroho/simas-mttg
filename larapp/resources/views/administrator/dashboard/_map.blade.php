<div class="col-lg-6">
        <div class="card p-3 shadow-sm" style="height:100%">
          <h6 class="mb-3">Peta Lokasi</h6>
          

          <div id="map" style="height:420px; width:100%;"></div>

          <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
          <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

          <script>
            document.addEventListener('DOMContentLoaded', function(){
              const container = document.getElementById('map');
              if (!container) return;

              if (typeof L === 'undefined') {
                console.error('Leaflet (L) is not loaded');
                return;
              }

              try {
                // Reuse existing map instance if already created elsewhere to avoid "container already initialized" errors
                let map;
                if (window._mapInstance) {
                  map = window._mapInstance;
                  try { map.setView([-8.179, 115.862], 6); } catch(e){}
                } else {
                  map = L.map('map').setView([-8.179, 115.862], 6);
                  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                  }).addTo(map);
                  window._mapInstance = map;
                }

                // Use default Leaflet markers (no custom icons)

                // shared marker layer to manage facility markers from other components
                if (!window._mosqueMarkerLayer) {
                  window._mosqueMarkerLayer = L.layerGroup().addTo(map);
                }

                  // Prefer server-provided incomplete list (passed from DashboardController)
                  try {
                    const serverList = @json($incompleteList ?? []);
                    // Choose initial data source: prefer lastFilteredRows if present (so filters applied before map init win)
                    let initialRows = null;
                    if (Array.isArray(window._lastFilteredRows) && window._lastFilteredRows.length) {
                      initialRows = window._lastFilteredRows;
                    } else if (Array.isArray(serverList) && serverList.length) {
                      initialRows = serverList;
                    }

                    // Clear layer before rendering the chosen dataset
                    window._mosqueMarkerLayer.clearLayers();

                    if (Array.isArray(initialRows) && initialRows.length) {
                      const initialLocations = [];
                      initialRows.forEach(m => {
                        const lat = m.lat ?? m.latitude ?? null;
                        const lng = m.lng ?? m.longitude ?? null;
                        if (!lat || !lng) return;
                        const nlat = Number(lat);
                        const nlng = Number(lng);
                        if (Number.isNaN(nlat) || Number.isNaN(nlng)) return;
                        const marker = L.marker([nlat, nlng]);
                        const html = `<strong>${escapeHtml(m.name)}</strong><br/>`+
                          `Type: ${escapeHtml(m.type || '')}<br/>`+
                          `Completion: ${m.completion_percentage ?? 'N/A'}%<br/>`+
                          `${escapeHtml(m.city || '')} ${escapeHtml(m.province || '')}`;
                        marker.bindPopup(html);
                        window._mosqueMarkerLayer.addLayer(marker);
                        initialLocations.push([nlat, nlng]);
                      });
                      try{ if (initialLocations.length){ map.fitBounds(L.latLngBounds(initialLocations).pad(0.1)); } }catch(e){}
                    } else {
                      // Fallback: fetch from API endpoint and add to shared layer
                      fetch('/api/mosques/incomplete?limit=1000')
                        .then(r => r.json())
                        .then(payload => {
                          const items = payload?.data || [];
                          items.forEach(m => {
                            const lat = m.latitude ?? m.lat ?? null;
                            const lng = m.longitude ?? m.lng ?? null;
                            if (!lat || !lng) return;
                            const nlat = Number(lat);
                            const nlng = Number(lng);
                            if (Number.isNaN(nlat) || Number.isNaN(nlng)) return;
                            const marker = L.marker([nlat, nlng]);
                            const html = `<strong>${escapeHtml(m.name)}</strong><br/>`+
                              `Type: ${escapeHtml(m.type || '')}<br/>`+
                              `Completion: ${m.completion_percentage ?? 'N/A'}%<br/>`+
                              `${escapeHtml(m.city || '')} ${escapeHtml(m.province || '')}`;
                            marker.bindPopup(html);
                            window._mosqueMarkerLayer.addLayer(marker);
                          });
                        }).catch(e => console.error('Failed to load map points', e));
                    }
                  } catch (e) { console.error('Error rendering server list', e); }

              } catch (err) {
                console.error('Map initialization error', err);
              }

              function escapeHtml(str){
                if (!str) return '';
                return String(str).replace(/[&<>\"]/g, function(match){
                  return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'})[match] || match;
                });
              }
            
              // --- Filter population & events (same behavior as beranda map) ---
              function firstEl(ids){ for(const id of ids){ const el = document.getElementById(id); if(el) return el; } return null; }
              const selRegionalMap = firstEl(['mfRegional','facRegional']);
              const selAreaMap = firstEl(['mfArea','facArea']);
              const selWitelMap = firstEl(['mfWitel','facWitel']);
              const selDatelMap = firstEl(['mfDatel','facSto']);
              const selTypeMap = firstEl(['mfType','facType']);
              const resetBtn = firstEl(['mapFilterReset','facReset']);

              async function fetchRegions(params){
                const q = new URLSearchParams(params).toString();
                const res = await fetch(`/api/regions?${q}`);
                if(!res.ok) throw new Error('Gagal memuat wilayah');
                const json = await res.json();
                return json.data || [];
              }

              function setOptions(selectEl, items, placeholder){ if(!selectEl) return; selectEl.replaceChildren(new Option(placeholder || 'Pilih...', '')); items.forEach(it => selectEl.appendChild(new Option(it.name, it.id))); }
              function setDisabled(selectEl, disabled){ if(!selectEl) return; selectEl.disabled = !!disabled; }

              // Load regionals (if select exists)
              if (selRegionalMap) {
                fetchRegions({ level: 'REGIONAL' }).then(list => {
                  setOptions(selRegionalMap, list, 'Semua Regional');
                  setDisabled(selAreaMap, true); setDisabled(selWitelMap, true); setDisabled(selDatelMap, true);
                }).catch(console.error);
              }

              if (selRegionalMap) selRegionalMap.addEventListener('change', async function(){
                const rid = this.value;
                if(!rid){ setOptions(selAreaMap, [], 'Semua Area'); setDisabled(selAreaMap, true); setOptions(selWitelMap, [], 'Semua Witel'); setDisabled(selWitelMap, true); setOptions(selDatelMap, [], 'Semua STO'); setDisabled(selDatelMap, true);
                  window.dispatchEvent(new CustomEvent('mapFilterChange',{ detail: { regional_id: null, area_id: null, witel_id: null, datel_id: null, type: selTypeMap ? selTypeMap.value || null : null } }));
                  return;
                }
                try{
                  const areas = await fetchRegions({ level:'AREA', parent_id: rid });
                  setOptions(selAreaMap, areas, 'Semua Area'); setDisabled(selAreaMap, false);
                  setOptions(selWitelMap, [], 'Semua Witel'); setDisabled(selWitelMap, true);
                  setOptions(selDatelMap, [], 'Semua STO'); setDisabled(selDatelMap, true);
                  window.dispatchEvent(new CustomEvent('mapFilterChange',{ detail: { regional_id: rid, area_id: null, witel_id: null, datel_id: null, type: selTypeMap ? selTypeMap.value || null : null } }));
                }catch(e){ console.error(e); }
              });

              if (selAreaMap) selAreaMap.addEventListener('change', async function(){
                const aid = this.value;
                if(!aid){ setOptions(selWitelMap, [], 'Semua Witel'); setDisabled(selWitelMap, true); setOptions(selDatelMap, [], 'Semua STO'); setDisabled(selDatelMap, true);
                  window.dispatchEvent(new CustomEvent('mapFilterChange',{ detail: { regional_id: selRegionalMap ? selRegionalMap.value || null : null, area_id: null, witel_id: null, datel_id: null, type: selTypeMap ? selTypeMap.value || null : null } }));
                  return; }
                try{
                  const witels = await fetchRegions({ level:'WITEL', parent_id: aid });
                  setOptions(selWitelMap, witels, 'Semua Witel'); setDisabled(selWitelMap, false);
                  setOptions(selDatelMap, [], 'Semua STO'); setDisabled(selDatelMap, true);
                  window.dispatchEvent(new CustomEvent('mapFilterChange',{ detail: { regional_id: selRegionalMap ? selRegionalMap.value || null : null, area_id: aid, witel_id: null, datel_id: null, type: selTypeMap ? selTypeMap.value || null : null } }));
                }catch(e){ console.error(e); }
              });

              if (selWitelMap) selWitelMap.addEventListener('change', async function(){
                const wid = this.value; if(!wid){ setOptions(selDatelMap, [], 'Semua STO'); setDisabled(selDatelMap, true); window.dispatchEvent(new CustomEvent('mapFilterChange',{ detail: { regional_id: selRegionalMap ? selRegionalMap.value || null : null, area_id: selAreaMap ? selAreaMap.value || null : null, witel_id: null, datel_id: null, type: selTypeMap ? selTypeMap.value || null : null } })); return; }
                try{ const stos = await fetchRegions({ level:'STO', parent_id: wid }); setOptions(selDatelMap, stos, 'Semua STO'); setDisabled(selDatelMap, false); window.dispatchEvent(new CustomEvent('mapFilterChange',{ detail: { regional_id: selRegionalMap ? selRegionalMap.value || null : null, area_id: selAreaMap ? selAreaMap.value || null : null, witel_id: wid, datel_id: null, type: selTypeMap ? selTypeMap.value || null : null } })); }catch(e){ console.error(e); }
              });

              if (selDatelMap) selDatelMap.addEventListener('change', function(){ window.dispatchEvent(new CustomEvent('mapFilterChange',{ detail: { regional_id: selRegionalMap ? selRegionalMap.value || null : null, area_id: selAreaMap ? selAreaMap.value || null : null, witel_id: selWitelMap ? selWitelMap.value || null : null, datel_id: selDatelMap.value || null, type: selTypeMap ? selTypeMap.value || null : null } })); });
              if (selTypeMap) selTypeMap.addEventListener('change', function(){ window.dispatchEvent(new CustomEvent('mapFilterChange',{ detail: { regional_id: selRegionalMap ? selRegionalMap.value || null : null, area_id: selAreaMap ? selAreaMap.value || null : null, witel_id: selWitelMap ? selWitelMap.value || null : null, datel_id: selDatelMap ? selDatelMap.value || null : null, type: selTypeMap.value || null } })); });

              if (resetBtn) resetBtn.addEventListener('click', function(){ if (selRegionalMap) selRegionalMap.value=''; if (selAreaMap) selAreaMap.value=''; if (selWitelMap) selWitelMap.value=''; if (selDatelMap) selDatelMap.value=''; if (selTypeMap) selTypeMap.value=''; setDisabled(selAreaMap, true); setDisabled(selWitelMap, true); setDisabled(selDatelMap, true); window.dispatchEvent(new CustomEvent('mapFilterChange',{ detail: { regional_id: null, area_id: null, witel_id: null, datel_id: null, type: null } })); });

              // Ensure facilities partial can receive filter events (it listens to 'mapFilterChange')
              // No further action needed here since we dispatch the same event name used elsewhere.
              // Listen for filtered rows and update markers accordingly
              window.addEventListener('mapFilterChange', function(ev){
                try{
                  const rows = ev?.detail?.rows;
                  if (!window._mosqueMarkerLayer) window._mosqueMarkerLayer = L.layerGroup().addTo(map);
                  // Always clear existing markers first to avoid leftovers
                  window._mosqueMarkerLayer.clearLayers();
                  if (!Array.isArray(rows)){
                    // no rows provided -> nothing to add (clear only)
                    try{ map.setView([-8.179, 115.862], 6); }catch(e){}
                    return;
                  }
                  const markerLocations = [];
                  rows.forEach(m => {
                    const lat = m.lat ?? m.latitude ?? null;
                    const lng = m.lng ?? m.longitude ?? null;
                    if (!lat || !lng) return;
                    const marker = L.marker([lat, lng]);
                    const html = `<strong>${escapeHtml(m.name)}</strong><br/>`+
                      `Type: ${escapeHtml(m.type || '')}<br/>`+
                      `Completion: ${m.completion_percentage ?? 'N/A'}%<br/>`+
                      `${escapeHtml(m.city || '')} ${escapeHtml(m.province || '')}`;
                    marker.bindPopup(html);
                    window._mosqueMarkerLayer.addLayer(marker);
                    markerLocations.push([lat, lng]);
                  });
                  // adjust map view to show filtered markers
                  try{
                    if (markerLocations.length){
                      const bounds = L.latLngBounds(markerLocations);
                      map.fitBounds(bounds.pad(0.1));
                    } else {
                      // if no markers, reset to default view
                      map.setView([-8.179, 115.862], 6);
                    }
                  }catch(e){ console.warn('fitBounds failed for filtered markers', e); }
                }catch(e){ console.error('Error applying filtered markers', e); }
              });
              });
          </script>
        </div>
      </div>