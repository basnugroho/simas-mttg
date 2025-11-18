(function(){
	const input = document.getElementById('searchInput');
	const box = document.getElementById('autocomplete');
	let controller; let lastQuery=''; let hideTimeout;
	let lastSuggestions = [];
	let selectedId = null;
	function fetchSuggestions(q){
		if(!q || q.length < 2){ box.classList.add('d-none'); box.innerHTML=''; return; }
		if(controller){ controller.abort(); }
		controller = new AbortController();
		fetch(`search/suggestions?q=${encodeURIComponent(q)}`, {signal:controller.signal})
			.then(r=>r.json())
			.then(data=>{
				if(input.value !== q) return; // stale
				if(!data.length){ box.innerHTML = `<div class='autocomplete-empty'>Tidak ada saran</div>`; box.classList.remove('d-none'); return; }
				lastSuggestions = data || [];
				box.innerHTML = data.map(item=>{
					const city = item.city ? String(item.city).replace(/&/g,'&amp;').replace(/</g,'&lt;') : '';
					const name = item.name ? String(item.name).replace(/&/g,'&amp;').replace(/</g,'&lt;') : '';
					const type = item.type ? String(item.type).replace(/&/g,'&amp;').replace(/</g,'&lt;') : '';
					const display = `${name}`;
					return `<div class='autocomplete-item' data-id="${item.id}" data-name="${name}"><span>${display}</span><small>${city} | ${type}</small></div>`;
				}).join('');
				box.classList.remove('d-none');
			})
			.catch(()=>{ box.innerHTML = `<div class='autocomplete-empty'>Tidak ada saran</div>`; box.classList.remove('d-none'); });
	}
	input.addEventListener('input', e=>{
		const q = e.target.value.trim();
		// user typed after selecting suggestion -> clear selectedId
		if(selectedId) selectedId = null;
		if(q===lastQuery) return; lastQuery=q; fetchSuggestions(q);
	});
	input.addEventListener('focus', ()=>{ if(input.value.trim().length>=2) fetchSuggestions(input.value.trim()); });
	box.addEventListener('click', e=>{
		const item = e.target.closest('.autocomplete-item');
		if(!item) return;
		const id = item.getAttribute('data-id');
		const name = item.getAttribute('data-name');
		// Fill input and remember selected id; do NOT redirect yet.
		input.value = name || '';
		selectedId = (id || id === '0') ? id : null;
		box.classList.add('d-none'); box.innerHTML='';
	});

	document.getElementById('searchForm').addEventListener('submit', function(ev){ ev.preventDefault(); if(input.value.trim()===''){ return; } submitDynamic(); });
	document.addEventListener('click', e=>{
		if(e.target===input || box.contains(e.target)) return; box.classList.add('d-none');
	});
	// --- Dynamic Prayer Times ---
	function formatTime(v){
		if(!v) return '-';
		const m = String(v).match(/(\d{1,2}):(\d{2})/);
		if(!m) return '-';
		let h = m[1].padStart(2,'0');
		let min = m[2];
		return h+':'+min;
	}

	async function loadPrayerTimes(){
		const prayerCard = document.querySelector('.prayer-card');
		const cityId = prayerCard ? prayerCard.getAttribute('data-city-id') : null;
		// Build date in YYYY-MM-DD (today)
		const today = new Date();
		const pad = n => String(n).padStart(2,'0');
		const dateStr = `${today.getFullYear()}-${pad(today.getMonth()+1)}-${pad(today.getDate())}`;

		// Try external MyQuran API if we have a city id
		if(cityId){
			try{
				const res = await fetch(`https://api.myquran.com/v2/sholat/jadwal/${cityId}/${dateStr}`);
				if(res.ok){
					const json = await res.json();
					// Expected path: json.data.jadwal.[subuh,dzuhur,ashar,maghrib,isya]
					const j = json && json.data && json.data.jadwal ? json.data.jadwal : null;
					if(j){
						const mapping = {
							subuh: j.subuh,
							dzuhur: j.dzuhur || j.dzuhur, // keep naming
							ashar: j.ashar,
							maghrib: j.maghrib,
							isya: j.isya
						};
						['subuh','dzuhur','ashar','maghrib','isya'].forEach(k => {
							const el = document.getElementById('pt-'+k);
							if(el){ el.textContent = formatTime(mapping[k]); }
						});
						const sourceEl = document.getElementById('pt-source');
						if(sourceEl){ sourceEl.textContent = 'Kemenag RI'; sourceEl.className = 'prayer-source-badge api'; sourceEl.title = 'Sumber: Kemenag RI'; }
						return; // done
					}
				}
			}catch(err){
				console.warn('MyQuran API failed, falling back', err);
			}
		}

		// Fallback to local endpoint
		try{
			const res2 = await fetch('/prayer-times');
			const data = await res2.json();
			if(data && data.times){
				const mapping = data.times;
				['subuh','dzuhur','ashar','maghrib','isya'].forEach(k => {
					const el = document.getElementById('pt-'+k);
					if(el){ el.textContent = formatTime(mapping[k]); }
				});
				const sourceEl = document.getElementById('pt-source');
				if(sourceEl){
					const src = (data.source||'fallback').toLowerCase();
					sourceEl.textContent = src === 'db' ? 'DB' : (src === 'api' ? 'API' : 'N/A');
					sourceEl.className = 'prayer-source-badge '+src;
					sourceEl.title = 'Sumber: '+src;
				}
				return;
			}
		}catch(e){
			console.warn('Local prayer-times endpoint failed', e);
		}

		// If all fails, show placeholders
		['subuh','dzuhur','ashar','maghrib','isya'].forEach(k => {
			const el = document.getElementById('pt-'+k);
			if(el){ el.textContent = '-'; }
		});
		const sourceEl = document.getElementById('pt-source');
		if(sourceEl){ sourceEl.textContent = 'N/A'; sourceEl.className='prayer-source-badge fallback'; }
	}
	loadPrayerTimes();

	// Attempt to resolve a search query to a masjid id and redirect
	async function submitDynamic(){
		const q = input.value ? input.value.trim() : '';
		if(!q) return;
		// If user previously selected a suggestion, use its id
		if(selectedId){
			window.location.href = `/masjid/${selectedId}`;
			return;
		}
		// try exact match from last fetched suggestions
		if(Array.isArray(lastSuggestions) && lastSuggestions.length){
			const match = lastSuggestions.find(s => (s.name||'').toLowerCase() === q.toLowerCase());
			if(match && (match.id || match.id === 0)){
				window.location.href = `/masjid/${match.id}`;
				return;
			}
		}
		// otherwise, query suggestions endpoint live and use first result if any
		try{
			const res = await fetch(`search/suggestions?q=${encodeURIComponent(q)}`);
			if(res.ok){
				const data = await res.json();
				if(Array.isArray(data) && data.length){
					const first = data[0];
					if(first && (first.id || first.id === 0)){
						window.location.href = `/masjid/${first.id}`;
						return;
					}
				}
			}
		}catch(e){ /* ignore */ }
		// fallback: go to standard search results page
		window.location.href = `/search?q=${encodeURIComponent(q)}`;
	}

	// --- Dynamic Facilities Overview ---
	const provinceSel = document.getElementById('filterProvince');
	const citySel = document.getElementById('filterCity');
	const completenessSel = document.getElementById('filterCompleteness');
	const applyBtn = document.getElementById('filterApply');
	const masjidGrid = document.getElementById('masjidGrid');
	const mushollaGrid = document.getElementById('mushollaGrid');

	function facilityCard(item){
		const imgSrc = '/images/mosque-1.png';
		const img = imgSrc;
		return `<div class="facility-card modern shadow-sm">\n`+
			`<img src="${img}" alt="Foto ${item.name}" loading="lazy">\n`+
			`<div class="fc-body">\n`+
				`<h6 class="fc-title" title="${item.name}">${item.name}</h6>\n`+
				`<div class="fc-sub">${item.loc}</div>\n`+
				`<div class="fc-label">Fasilitas</div>\n`+
				`<div class="fc-progress-wrap">\n`+
					`<div class="progress"><div class="progress-bar" role="progressbar" style="width:${item.pct}%" aria-valuenow="${item.pct}" aria-valuemin="0" aria-valuemax="100"></div></div>\n`+
					`<div class="fc-pct">${item.pct}%</div>\n`+
				`</div>\n`+
			`</div>\n`+
		`</div>`;
	}

	function setLoading(){
		// only show loading if grid is currently empty
		if(!masjidGrid.innerHTML.trim()) masjidGrid.innerHTML = `<div class='text-muted small'>Memuat data...</div>`;
		if(!mushollaGrid.innerHTML.trim()) mushollaGrid.innerHTML = `<div class='text-muted small'>Memuat data...</div>`;
	}

	function loadFacilities(){
		// don't aggressively clear server-rendered content; show loading only when empty
		setLoading();
		const params = new URLSearchParams();
		// Only append params if a non-empty value is selected (empty means 'all')
		if(provinceSel && provinceSel.value) params.append('province_id', provinceSel.value);
		if(citySel && citySel.value) params.append('city_id', citySel.value);
		if(completenessSel && completenessSel.value){
			const val = completenessSel.value.replace(/[^0-9]/g,'');
			if(val) params.append('completeness', val);
		}
		fetch(`/api/facilities/overview?${params.toString()}`)
			.then(r=>r.json())
			.then(json=>{
				// Controller for /api/facilities returns {data: [...]}
				const data = json && (json.data || json) ;
				if(!data){
					// don't erase server-rendered content when API returns nothing
					return;
				}
				// try to extract masjid/musholla arrays if API provides grouped response
				if(data.masjid || data.musholla){
					if(data.masjid){ masjidGrid.innerHTML = data.masjid.length ? data.masjid.map(facilityCard).join('') : `<div class='text-muted small'>Tidak ada data</div>`; }
					if(data.musholla){ mushollaGrid.innerHTML = data.musholla.length ? data.musholla.map(facilityCard).join('') : `<div class='text-muted small'>Tidak ada data</div>`; }
					return;
				}
				// Fallback: if API returns flat list of facilities, don't touch the grids
			})
			.catch(()=>{
				// keep server-rendered lists intact; only show a warning in console
				console.warn('Failed to load facilities overview from API');
			});
	}

	if(applyBtn){ applyBtn.addEventListener('click', loadFacilities); }
	[provinceSel, citySel, completenessSel].forEach(el=>{ if(el) el.addEventListener('change', loadFacilities); });

	// Populate city select when province changes (for facility filters)
	if(provinceSel){
		provinceSel.addEventListener('change', async function(e){
			const id = e.target.value;
			if(!citySel) return;
			citySel.innerHTML = `<option value="">Semua Kota / Kabupaten</option>`;
			if(!id) return; // all selected -> keep only default
			try{
				const res = await fetch(`/api/regions?parent_id=${id}`);
				const json = await res.json();
				const cities = json.data || [];
				cities.forEach(c=>{ const opt = document.createElement('option'); opt.value = c.id; opt.textContent = c.name; citySel.appendChild(opt); });
			}catch(err){ console.warn('Gagal memuat daftar kota', err); }
		});
	}
	// Do not auto-load facilities on page load to avoid overwriting server-rendered content

	// --- Map & Filters ---
	// Map container may be named 'map' (dashboard view) or 'mainMap' (frontend).
	const mapContainerId = document.getElementById('mainMap') ? 'mainMap' : (document.getElementById('map') ? 'map' : null);
	// expose map variable to outer scope so marker functions can use it
	let map = null;
	if(mapContainerId){
		function initMap(){
			if(typeof L === 'undefined') return; // should not happen
			map = L.map(mapContainerId).setView([-7.25,112.75],7);
			L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
			// example marker removed to avoid placeholder popup
			return map;
		}

		if(typeof L === 'undefined'){
			// Dynamically load Leaflet CSS and JS only when map container exists
			const css = document.createElement('link');
			css.rel = 'stylesheet';
			css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
			document.head.appendChild(css);
			const s = document.createElement('script');
			s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
			s.onload = ()=> {
				try{ initMap(); }catch(e){ console.warn('Inisialisasi map gagal', e); }
			};
			document.head.appendChild(s);
		} else {
			initMap();
		}
	} else {
		// no map on this page — skip map initialization
	}

	// --- Map filter reset control ---
	const mapFilterReset = document.getElementById('mapFilterReset');
	// Note: map filter toggle removed — filter stays visible always.
	if(mapFilterReset){
		mapFilterReset.addEventListener('click', function(){
			// reset selects/inputs in the form
			const form = document.getElementById('mapFilterForm');
			if(!form) return;
			form.reset();
			// disable selects that are marked disabled by default
			const city = document.getElementById('mfCity'); if(city){ city.innerHTML = '<option value="">Pilih Kota / Kabupaten</option>'; city.classList.add('disabled-select'); city.disabled = true; }
			const witel = document.getElementById('mfWitel'); if(witel){ witel.innerHTML = '<option value="">Pilih Witel</option>'; witel.classList.add('disabled-select'); witel.disabled = true; }
			// trigger marker reload
			if(typeof fetchMarkers === 'function') fetchMarkers();
		});
	}

	const provinceSelect = document.getElementById('mfProvince');
	const citySelect = document.getElementById('mfCity');
	const witelSelect = document.getElementById('mfWitel');
	const typeSelect = document.getElementById('mfType');
	const queryInput = document.getElementById('mfQuery');
	const filterForm = document.getElementById('mapFilterForm');

	function resetSelect(sel, placeholder){
		sel.innerHTML = `<option value="">${placeholder}</option>`;
	}

	function enableSelect(sel){ sel.classList.remove('disabled-select'); sel.disabled = false; }
	function disableSelect(sel){ sel.classList.add('disabled-select'); sel.disabled = true; }

	async function fetchRegions(){
		try{
			const res = await fetch('/api/regions');
			const json = await res.json();
			if(!json.data){ resetSelect(provinceSelect,'Pilih Provinsi'); return; }
			// Assuming regions contain type field: filter provinces
			const provinces = json.data.filter(r=> (r.type||'').toUpperCase()==='PROVINCE');
			resetSelect(provinceSelect,'Pilih Provinsi');
			provinces.forEach(p=>{ const opt=document.createElement('option'); opt.value=p.id; opt.textContent=p.name; provinceSelect.appendChild(opt); });
		}catch(e){ resetSelect(provinceSelect,'Gagal memuat'); }
	}

	if(provinceSelect){
		provinceSelect.addEventListener('change', async e=>{
		const id = e.target.value;
		resetSelect(citySelect,'Pilih Kota / Kabupaten');
		resetSelect(witelSelect,'Pilih Witel');
		if(!id){ disableSelect(citySelect); disableSelect(witelSelect); return; }
		try{
			// Fetch cities by province (correct param parent_id)
			const res = await fetch(`/api/regions?parent_id=${id}`);
			const json = await res.json();
			const cities = json.data || [];
			if(cities.length){ enableSelect(citySelect); cities.forEach(c=>{ const opt=document.createElement('option'); opt.value=c.id; opt.textContent=c.name; citySelect.appendChild(opt); }); }
			else{ disableSelect(citySelect); }
		}catch{ disableSelect(citySelect); }
		});
	}

	if(citySelect){
		citySelect.addEventListener('change', async e=>{
		const id = e.target.value;
		resetSelect(witelSelect,'Pilih Witel');
		if(!id){ disableSelect(witelSelect); return; }
		try{
			const res = await fetch(`/api/regions?parent_id=${id}`);
			const json = await res.json();
			const witels = json.data || [];
			if(witels.length){ enableSelect(witelSelect); witels.forEach(w=>{ const opt=document.createElement('option'); opt.value=w.id; opt.textContent=w.name; witelSelect.appendChild(opt); }); }
			else{ disableSelect(witelSelect); }
		}catch{ disableSelect(witelSelect); }
		});
	}

	const mapStatusEl = document.getElementById('mapStatus');
	function showMapStatus(msg, type='info'){
		if(!mapStatusEl) return;
		mapStatusEl.textContent = msg;
		mapStatusEl.style.display = 'block';
		mapStatusEl.style.background = type==='error' ? 'rgba(255,240,240,.95)' : 'rgba(255,255,255,.9)';
		mapStatusEl.style.color = type==='error' ? '#b91c1c' : '#555';
		clearTimeout(mapStatusEl._timeout);
		mapStatusEl._timeout = setTimeout(()=>{ mapStatusEl.style.display='none'; }, 4000);
	}

	async function fetchMarkers(extraParams={}){
		const params = new URLSearchParams();
		if(provinceSelect && provinceSelect.value) params.append('province_id', provinceSelect.value);
		if(citySelect && citySelect.value) params.append('city_id', citySelect.value);
		if(witelSelect && witelSelect.value) params.append('witel_id', witelSelect.value);
		if(typeSelect && typeSelect.value) params.append('type', typeSelect.value);
		if(queryInput && queryInput.value && queryInput.value.trim()) params.append('search', queryInput.value.trim()); // gunakan "search" sesuai API
		Object.entries(extraParams).forEach(([k,v])=>{ if(v!==undefined&&v!==null&&v!=='') params.set(k,v); });
		showMapStatus('Memuat lokasi...');
		try{
			const res = await fetch(`/api/mosques?per_page=200&${params.toString()}`); // ambil lebih banyak untuk peta
			const json = await res.json();
			const items = (json.data && Array.isArray(json.data.items)) ? json.data.items : ((json.data && json.data.items) ? json.data.items : []);
			addMarkers(items);
			showMapStatus(`${items.length} lokasi ditampilkan`,'ok');
		}catch(err){
			console.warn('Gagal memuat marker', err);
			showMapStatus('Gagal memuat lokasi','error');
		}
	}

	if(filterForm){
		filterForm.addEventListener('submit', async e=>{
			e.preventDefault();
			fetchMarkers();
		});
	}

	// markersLayer is created only when Leaflet map is available
	let markersLayer = null;
	function addMarkers(items){
		if(!map || typeof L === 'undefined') return; // can't add markers without map
		if(!markersLayer) markersLayer = L.layerGroup().addTo(map);
		markersLayer.clearLayers();

		// prepare icons (cached)
		if(!window.__simas_icons){
			const base = window.location && window.location.origin ? window.location.origin : '';
			window.__simas_icons = {
				masjid: L.icon({ iconUrl: base + '/images/mosque-map-icon.png', iconSize: [34, 34], iconAnchor: [17, 34], popupAnchor: [0, -30] }),
				musholla: L.icon({ iconUrl: base + '/images/mushalla-map-icon.png', iconSize: [34, 34], iconAnchor: [17, 34], popupAnchor: [0, -30] }),
				default: L.icon({ iconUrl: base + '/images/mosque.png', iconSize: [34,34], iconAnchor: [17,34], popupAnchor: [0,-30] })
			};
		}

		items.forEach(m=>{
			if(!m.latitude || !m.longitude) return;
			const lat = parseFloat(m.latitude);
			const lng = parseFloat(m.longitude);
			if(Number.isNaN(lat) || Number.isNaN(lng)) return;
			const t = (m.type||'').toUpperCase();
			const icon = t === 'MUSHOLLA' ? window.__simas_icons.musholla : (t === 'MASJID' ? window.__simas_icons.masjid : window.__simas_icons.default);
			const marker = L.marker([lat, lng], { icon });
			marker.bindPopup(`<strong>${m.name}</strong><br><small>${m.address||''}</small>`);
			markersLayer.addLayer(marker);
		});
		if(items.length){
			const bounds = markersLayer.getBounds();
			if(bounds.isValid()) map.fitBounds(bounds.pad(0.15));
		}
	}

	fetchRegions();
	// Load awal marker
	fetchMarkers();
})();
