<div class="container mt-3">
	<div class="map-wrapper-full">
		<style>
			/* Default: overlay absolute top-left */
			.map-overlay-controls{position:absolute;top:12px;left:12px;z-index:1100;background:rgba(255,255,255,0.95);padding:12px;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,0.12);max-width:360px}
			.map-overlay-controls .map-filter-form{display:flex;flex-direction:column;gap:8px}
			/* On small screens: make overlay a horizontal, full-width bar above the map */
			@media (max-width:768px){
				.map-overlay-controls{position:relative;top:auto;left:auto;right:auto;width:100%;max-width:100%;border-radius:8px;margin-bottom:8px;padding:8px 10px;display:flex;flex-wrap:wrap;align-items:center;gap:8px}
				.map-overlay-controls .map-filter-form{flex-direction:row;gap:8px;width:100%;align-items:center;flex-wrap:wrap}
				.map-overlay-controls .form-group{margin:0;flex:1 1 120px;min-width:120px}
				.map-overlay-controls .form-group select.form-select{min-width:0}
				.map-overlay-controls label{display:none}
				/* compact controls on mobile */
				.map-overlay-controls .form-group .form-select{padding:.35rem .5rem;font-size:.85rem}
				.map-overlay-controls .btn-search-map{padding:.45rem .6rem;font-size:.9rem}
				/* ensure the reset/filter buttons stay visible */
				.map-filter-header h6{font-size:1rem}
			}
		</style>

		<div class="map-overlay-controls" id="mapFilterCard">
			<div class="map-filter-header d-flex w-100 justify-content-between align-items-center" style="gap:8px;margin-bottom:8px;">
				<h6 style="margin:0">Filter Data</h6>
				<div class="d-flex align-items-center" style="gap:6px;">
					<button class="btn btn-sm btn-outline-secondary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#mapFilterCollapse" aria-expanded="false" aria-controls="mapFilterCollapse">Filter</button>
					<button id="mapFilterReset" type="button" class="btn btn-sm btn-reset">⟲</button>
				</div>
			</div>
			<div id="mapFilterCollapse" class="collapse show d-md-block">
			<form id="mapFilterForm" class="map-filter-form w-100">
				<div class="form-group">
					<label for="mfRegional" class="d-none d-md-block">Regional</label>
					<select id="mfRegional" class="form-select" data-placeholder="Pilih Regional"></select>
				</div>
				<div class="form-group">
					<label for="mfArea" class="d-none d-md-block">Area</label>
					<select id="mfArea" class="form-select disabled-select" disabled data-placeholder="Pilih Area"></select>
				</div>
				<div class="form-group">
					<label for="mfWitel" class="d-none d-md-block">Witel</label>
					<select id="mfWitel" class="form-select disabled-select" disabled data-placeholder="Pilih Witel"></select>
				</div>
				<div class="form-group">
					<label for="mfDatel" class="d-none d-md-block">STO</label>
					<select id="mfDatel" class="form-select disabled-select" disabled data-placeholder="Pilih STO"></select>
				</div>
				<div class="form-group">
					<label for="mfType" class="d-none d-md-block">Jenis</label>
					<select id="mfType" class="form-select">
						<option value="MASJID">Masjid</option>
						<option value="MUSHOLLA">Musholla</option>
					</select>
				</div>
				<div class="form-group text-end d-none d-md-block">
					<button type="submit" class="btn btn-search-map">Cari</button>
				</div>
				<!-- Mobile: show search button in the filter bar -->
				<div class="form-group text-end d-md-none" style="width:100%;">
					<button type="submit" class="btn btn-search-map w-100">Cari</button>
				</div>
			</form>
			</div>
		</div>

		<div id="mainMap" class="main-map"></div>
		<div id="mapStatus" class="map-status" style="display:none"></div>
	</div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
	var toggleBtn = document.querySelector('[data-bs-target="#mapFilterCollapse"]');
	var collapseEl = document.getElementById('mapFilterCollapse');
	if(toggleBtn && collapseEl){
		toggleBtn.addEventListener('click', function(e){
			// Fallback toggle if Bootstrap's Collapse JS is not loaded
			if(typeof bootstrap === 'undefined' || !bootstrap.Collapse){
				e.preventDefault();
				collapseEl.classList.toggle('show');
			}
		});
	}

	// Populate dropdowns
	const selRegional = document.getElementById('mfRegional');
	const selArea = document.getElementById('mfArea');
	const selWitel = document.getElementById('mfWitel');
	const selDatel = document.getElementById('mfDatel');

	function setOptions(selectEl, items, placeholder){
		const opts = [new Option(placeholder || 'Pilih...', '')];
		items.forEach(it => opts.push(new Option(it.name, it.id)));
		selectEl.replaceChildren(...opts);
	}

	function setDisabled(selectEl, disabled){
		selectEl.disabled = !!disabled;
		selectEl.classList.toggle('disabled-select', !!disabled);
	}

	async function fetchRegions(params){
		const q = new URLSearchParams(params).toString();
		const res = await fetch(`/api/regions?${q}`);
		if(!res.ok) throw new Error('Gagal memuat data wilayah');
		const json = await res.json();
		return json.data || [];
	}

	// Load regional initially (level REGIONAL)
	fetchRegions({ level: 'REGIONAL' })
		.then(list => {
			setOptions(selRegional, list, selRegional.dataset.placeholder || 'Pilih Regional');
			setDisabled(selArea, true);
			setDisabled(selWitel, true);
			setDisabled(selDatel, true);
			// Clear existing markers when filter initializes
			if(window.clearMapMarkers) try{ window.clearMapMarkers(); }catch(e){}
			window.dispatchEvent(new CustomEvent('mapClearMarkers'));
			window.dispatchEvent(new CustomEvent('mapFilterChange', { detail: {
				regional_id: null, area_id: null, witel_id: null,
				type: document.getElementById('mfType')?.value || null
			}}));
		})
		.catch(console.error);

	// When regional changes, load areas (level AREA)
	selRegional.addEventListener('change', async function(){
		const regionalId = this.value;
		if(!regionalId){
			setOptions(selArea, [], selArea.dataset.placeholder || 'Pilih Area');
			setOptions(selWitel, [], selWitel.dataset.placeholder || 'Pilih Witel');
			setOptions(selDatel, [], selDatel.dataset.placeholder || 'Pilih STO');
			setDisabled(selArea, true);
			setDisabled(selWitel, true);
			setDisabled(selDatel, true);
			return;
		}
		try{
			const areas = await fetchRegions({ level: 'AREA', parent_id: regionalId });
			setOptions(selArea, areas, selArea.dataset.placeholder || 'Pilih Area');
			setDisabled(selArea, false);
			// Reset witel & datel
			setOptions(selWitel, [], selWitel.dataset.placeholder || 'Pilih Witel');
			setDisabled(selWitel, true);
			setOptions(selDatel, [], selDatel.dataset.placeholder || 'Pilih STO');
			setDisabled(selDatel, true);
			// Clear markers before applying new Area selection
			if(window.clearMapMarkers) try{ window.clearMapMarkers(); }catch(e){}
			window.dispatchEvent(new CustomEvent('mapClearMarkers'));
			window.dispatchEvent(new CustomEvent('mapFilterChange', { detail: {
				regional_id: selRegional.value || null,
				area_id: null, witel_id: null,
				type: document.getElementById('mfType')?.value || null
			}}));
		}catch(err){ console.error(err); }
	});

	// When area changes, load witels (level WITEL)
	selArea.addEventListener('change', async function(){
		const areaId = this.value;
		const areaText = this.options[this.selectedIndex]?.text || '';
		if(!areaId){
			setOptions(selWitel, [], selWitel.dataset.placeholder || 'Pilih Witel');
			setDisabled(selWitel, true);
			setOptions(selDatel, [], selDatel.dataset.placeholder || 'Pilih STO');
			setDisabled(selDatel, true);
			return;
		}
		try{
			const witels = await fetchRegions({ level: 'WITEL', parent_id: areaId });
			setOptions(selWitel, witels, selWitel.dataset.placeholder || 'Pilih Witel');
			setDisabled(selWitel, false);
			// reset datel
			setOptions(selDatel, [], selDatel.dataset.placeholder || 'Pilih STO');
			setDisabled(selDatel, true);

			// If area is Jawa Timur, force type to MASJID
			const typeSel = document.getElementById('mfType');
			if(areaText.trim().toLowerCase() === 'jawa timur'){
				if(typeSel) typeSel.value = 'MASJID';
			}
			// Clear markers before applying new Witel options
			if(window.clearMapMarkers) try{ window.clearMapMarkers(); }catch(e){}
			window.dispatchEvent(new CustomEvent('mapClearMarkers'));
			window.dispatchEvent(new CustomEvent('mapFilterChange', { detail: {
				regional_id: selRegional.value || null,
				area_id: selArea.value || null,
				witel_id: null,
				type: typeSel?.value || null
			}}));
		}catch(err){ console.error(err); }
	});

	// When witel changes, load STOs (level STO)
	selWitel.addEventListener('change', async function(){
		const witelId = this.value;
		if(!witelId){
			setOptions(selDatel, [], selDatel.dataset.placeholder || 'Pilih STO');
			setDisabled(selDatel, true);
			return;
		}
		try{
			const stos = await fetchRegions({ level: 'STO', parent_id: witelId });
			setOptions(selDatel, stos, selDatel.dataset.placeholder || 'Pilih STO');
			setDisabled(selDatel, false);
			// Clear markers before applying selected STOs
			if(window.clearMapMarkers) try{ window.clearMapMarkers(); }catch(e){}
			window.dispatchEvent(new CustomEvent('mapClearMarkers'));
			window.dispatchEvent(new CustomEvent('mapFilterChange', { detail: {
				regional_id: selRegional.value || null,
				area_id: selArea.value || null,
				witel_id: selWitel.value || null,
				type: document.getElementById('mfType')?.value || null
			}}));
		}catch(err){ console.error(err); }
	});

	// Broadcast on type change as well
	document.getElementById('mfType').addEventListener('change', function(){
		// Clear markers before applying type change
		if(window.clearMapMarkers) try{ window.clearMapMarkers(); }catch(e){}
		window.dispatchEvent(new CustomEvent('mapClearMarkers'));
		window.dispatchEvent(new CustomEvent('mapFilterChange', { detail: {
			regional_id: selRegional.value || null,
			area_id: selArea.value || null,
			witel_id: selWitel.value || null,
			type: this.value || null
		}}));
	});

	// Map data loader: listen for filter changes and fetch mosques
	async function fetchMosques(params){
		const q = new URLSearchParams(params).toString();
		const res = await fetch(`/api/mosques?${q}`);
		if(!res.ok) throw new Error('Gagal memuat data masjid');
		const json = await res.json();
		return json.data || [];
	}

	function renderMosqueMarkers(features){
		// If a global renderer exists, delegate to it
		if(typeof window.renderMosqueMarkers === 'function'){
			try{ window.renderMosqueMarkers(features); return; }catch(e){ console.error(e); }
		}
		// Leaflet fallback renderer: place markers by latitude/longitude
		if(window.L){
			if(!window._mosqueMarkerLayer){
				window._mosqueMarkerLayer = L.layerGroup();
				if(window._mapInstance){ window._mosqueMarkerLayer.addTo(window._mapInstance); }
			}
			window._mosqueMarkerLayer.clearLayers();
			features.forEach(f => {
				if(f.latitude && f.longitude){
					const m = L.marker([f.latitude, f.longitude], { title: f.name || '' });
					m.bindPopup(`<strong>${f.name||''}</strong><br/>${f.address||''}`);
					window._mosqueMarkerLayer.addLayer(m);
				}
			});
			return;
		}
		// Fallback: log only
		console.log('Mosque features', features);
	}

	window.addEventListener('mapFilterChange', async function(ev){
		const d = ev.detail || {};
		try{
			// Build query ensuring area_id is preferred if present
			const query = {};
			if(d.type) query.type = d.type; // MASJID/MUSHOLLA
			if(d.datel_id) query.datel_id = d.datel_id; // optional if supported
			if(d.witel_id) query.witel_id = d.witel_id; // optional if supported
			if(d.area_id) query.area_id = d.area_id; // primary filter requested
			if(!query.area_id && d.regional_id) query.regional_id = d.regional_id;

			// Clear existing markers before render
			if(window.clearMapMarkers) try{ window.clearMapMarkers(); }catch(e){}
			window.dispatchEvent(new CustomEvent('mapClearMarkers'));

			const mosquesResp = await fetchMosques(query);
			// Normalize to array of items (supports paginated response shape)
			const mosques = Array.isArray(mosquesResp.items) ? mosquesResp.items : mosquesResp;
			renderMosqueMarkers(mosques);
		}catch(err){
			console.error(err);
			const statusEl = document.getElementById('mapStatus');
			if(statusEl){ statusEl.textContent = 'Gagal memuat data masjid'; statusEl.style.display='block'; }
		}
	});
});
</script>
