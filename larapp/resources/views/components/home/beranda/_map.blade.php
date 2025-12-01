<div class="container mt-3">
	<div class="map-wrapper-full">
		<style>
			/* Default: overlay absolute top-left */
			.map-overlay-controls{position:absolute;top:12px;left:12px;z-index:1100;background:rgba(255,255,255,0.95);padding:12px;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,0.12);max-width:360px}
			.map-overlay-controls .map-filter-form{display:flex;flex-direction:column;gap:8px}
			/* On small screens: make overlay a horizontal, full-width bar above the map */
			@media (max-width:768px){
				.map-overlay-controls{position:relative;top:auto;left:auto;right:auto;width:100%;max-width:100%;border-radius:8px;margin-bottom:8px;padding:8px 10px;display:flex;flex-wrap:wrap;align-items:center;gap:8px}
				.map-overlay-controls .map-filter-form{flex-direction:row;gap:8px;width:100%;align-items:center}
				.map-overlay-controls .form-group{margin:0}
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
					<label for="mfDatel" class="d-none d-md-block">Datel</label>
					<select id="mfDatel" class="form-select disabled-select" disabled data-placeholder="Pilih Datel"></select>
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

	// Load regional initially (type PROVINCE, level REGIONAL)
	fetchRegions({ type: 'PROVINCE', level: 'REGIONAL' })
		.then(list => {
			setOptions(selRegional, list, selRegional.dataset.placeholder || 'Pilih Regional');
			setDisabled(selArea, true);
			setDisabled(selWitel, true);
			setDisabled(selDatel, true);
			window.dispatchEvent(new CustomEvent('mapFilterChange', { detail: {
				regional_id: null, area_id: null, witel_id: null,
				type: document.getElementById('mfType')?.value || null
			}}));
		})
		.catch(console.error);

	// When regional changes, load areas (type PROVINCE, level AREA)
	selRegional.addEventListener('change', async function(){
		const regionalId = this.value;
		if(!regionalId){
			setOptions(selArea, [], selArea.dataset.placeholder || 'Pilih Area');
			setOptions(selWitel, [], selWitel.dataset.placeholder || 'Pilih Witel');
			setOptions(selDatel, [], selDatel.dataset.placeholder || 'Pilih Datel');
			setDisabled(selArea, true);
			setDisabled(selWitel, true);
			setDisabled(selDatel, true);
			return;
		}
		try{
			const areas = await fetchRegions({ type: 'PROVINCE', level: 'AREA', parent_id: regionalId });
			setOptions(selArea, areas, selArea.dataset.placeholder || 'Pilih Area');
			setDisabled(selArea, false);
			// Reset witel & datel
			setOptions(selWitel, [], selWitel.dataset.placeholder || 'Pilih Witel');
			setDisabled(selWitel, true);
			setOptions(selDatel, [], selDatel.dataset.placeholder || 'Pilih Datel');
			setDisabled(selDatel, true);
			window.dispatchEvent(new CustomEvent('mapFilterChange', { detail: {
				regional_id: selRegional.value || null,
				area_id: null, witel_id: null,
				type: document.getElementById('mfType')?.value || null
			}}));
		}catch(err){ console.error(err); }
	});

	// When area changes, load witels (type WITEL, level WITEL)
	selArea.addEventListener('change', async function(){
		const areaId = this.value;
		if(!areaId){
			setOptions(selWitel, [], selWitel.dataset.placeholder || 'Pilih Witel');
			setDisabled(selWitel, true);
			setOptions(selDatel, [], selDatel.dataset.placeholder || 'Pilih Datel');
			setDisabled(selDatel, true);
			return;
		}
		try{
			const witels = await fetchRegions({ type: 'WITEL', level: 'WITEL', parent_id: areaId });
			setOptions(selWitel, witels, selWitel.dataset.placeholder || 'Pilih Witel');
			setDisabled(selWitel, false);
			// reset datel
			setOptions(selDatel, [], selDatel.dataset.placeholder || 'Pilih Datel');
			setDisabled(selDatel, true);
			window.dispatchEvent(new CustomEvent('mapFilterChange', { detail: {
				regional_id: selRegional.value || null,
				area_id: selArea.value || null,
				witel_id: null,
				type: document.getElementById('mfType')?.value || null
			}}));
		}catch(err){ console.error(err); }
	});

	// When witel changes, load datels (type CITY, level STO)
	selWitel.addEventListener('change', async function(){
		const witelId = this.value;
		if(!witelId){
			setOptions(selDatel, [], selDatel.dataset.placeholder || 'Pilih Datel');
			setDisabled(selDatel, true);
			return;
		}
		try{
			const datels = await fetchRegions({ type: 'CITY', level: 'STO', parent_id: witelId });
			setOptions(selDatel, datels, selDatel.dataset.placeholder || 'Pilih Datel');
			setDisabled(selDatel, false);
			window.dispatchEvent(new CustomEvent('mapFilterChange', { detail: {
				regional_id: selRegional.value || null,
				area_id: selArea.value || null,
				witel_id: selWitel.value || null,
				type: document.getElementById('mfType')?.value || null
			}}));
		}catch(err){ console.error(err); }

	// Broadcast on type change as well
	document.getElementById('mfType').addEventListener('change', function(){
		window.dispatchEvent(new CustomEvent('mapFilterChange', { detail: {
			regional_id: selRegional.value || null,
			area_id: selArea.value || null,
			witel_id: selWitel.value || null,
			type: this.value || null
		}}));
	});
	});
});
</script>
