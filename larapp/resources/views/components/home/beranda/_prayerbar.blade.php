
	<div class="container mt-2">
		<div class="row justify-content-center">
			<div class="col-12 col-lg-12">
				<div class="prayer-card" data-city-id="1638">
					<div class="prayer-card-body">
						<div class="d-flex align-items-center justify-content-center mb-2">
							<div class="d-flex align-items-center gap-2">
									<div class="prayer-bar-title">Informasi / Jadwal Shalat</div>
									<div style="min-width:320px; position:relative;">
										<input id="prayer-city-search" type="search" class="form-control form-control-sm" placeholder="Cari kota..." style="min-width:220px;" autocomplete="off" />
										<div id="prayer-city-suggestions" class="list-group position-absolute" style="z-index:9999; width:100%; display:none; max-height:170px; overflow:auto;"></div>
									</div>
                                
								<div><span id="pt-source" class="prayer-source-badge fallback" title="Sumber data">...</span></div>
							</div>
						</div>
						<div class="row justify-content-center g-4 text-center" id="prayerTimesRow">
							<div class="col-6 col-md-auto"><div class="time-item">Subuh <small id="pt-subuh">...</small></div></div>
							<div class="col-6 col-md-auto"><div class="time-item">Dzuhur <small id="pt-dzuhur">...</small></div></div>
							<div class="col-6 col-md-auto"><div class="time-item">Ashar <small id="pt-ashar">...</small></div></div>
							<div class="col-6 col-md-auto"><div class="time-item">Maghrib <small id="pt-maghrib">...</small></div></div>
							<div class="col-6 col-md-auto"><div class="time-item">Isya <small id="pt-isya">...</small></div></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<script>
// Prayer bar logic: detect location or use default SURABAYA (api_id 1638)
(function(){
	const citySearch = document.getElementById('prayer-city-search');
	const suggestionsBox = document.getElementById('prayer-city-suggestions');
	const ptSource = document.getElementById('pt-source');

	// currently selected city (object with id, api_id, name, province, tz)
	let currentCity = { id: null, api_id: '1638', name: 'KOTA SURABAYA', province: 'JAWA TIMUR', tz: 'WIB' };

	const STORAGE_KEY = 'prayer_selected_city_v1';

	function saveSelectedCity(){
		try{
			localStorage.setItem(STORAGE_KEY, JSON.stringify(currentCity));
		}catch(e){ console.warn('localStorage not available', e); }
	}

	function loadSelectedCity(){
		try{
			const raw = localStorage.getItem(STORAGE_KEY);
			if(!raw) return null;
			return JSON.parse(raw);
		}catch(e){ return null; }
	}

	function setTimesFromApi(apiId, tz){
		if(!apiId){
			console.warn('setTimesFromApi called without apiId, falling back to SURABAYA');
			fallbackToSurabaya();
			return;
		}
		console.log('setTimesFromApi:', { apiId: apiId, tz: tz });
		const today = new Date();
		const yyyy = today.getFullYear();
		const mm = String(today.getMonth()+1).padStart(2,'0');
		const dd = String(today.getDate()).padStart(2,'0');
		const dateStr = `${yyyy}-${mm}-${dd}`;
		ptSource.textContent = 'Memuat...';

		const url = `https://api.myquran.com/v2/sholat/jadwal/${encodeURIComponent(apiId)}/${dateStr}`;
		console.log('Fetching MyQuran URL:', url);
		fetch(url)
			.then(r=>{
				if(!r.ok){
					throw new Error('HTTP ' + r.status);
				}
				return r.json();
			})
			.then(data=>{
				console.log('MyQuran response:', data);
				if(data && data.data && data.data.jadwal){
					const jadwal = data.data.jadwal;
					const tzLabel = tz ? ' ' + tz : (currentCity && currentCity.tz ? ' ' + currentCity.tz : '');
					document.getElementById('pt-subuh').textContent = (jadwal.subuh || '-') + tzLabel;
					document.getElementById('pt-dzuhur').textContent = (jadwal.dzuhur || '-') + tzLabel;
					document.getElementById('pt-ashar').textContent = (jadwal.ashar || '-') + tzLabel;
					document.getElementById('pt-maghrib').textContent = (jadwal.maghrib || '-') + tzLabel;
					document.getElementById('pt-isya').textContent = (jadwal.isya || '-') + tzLabel;
					ptSource.textContent = 'KEMENAG RI';
				} else {
					ptSource.textContent = 'Sumber tidak tersedia';
					console.warn('No jadwal in MyQuran response', data);
				}
			})
			.catch(err=>{
				ptSource.textContent = 'Error';
				console.error('Error fetching MyQuran', err);
			});
	}

	function setSelectedCityObject(c){
		if(!c) return;
		currentCity = {
			id: c.id || null,
			api_id: c.api_id || (c.dataset && c.dataset.apiId) || null,
			name: c.name || c.textContent || citySearch.value,
			province: c.province || (c.dataset && c.dataset.province) || null,
			tz: c.tz || (c.dataset && c.dataset.tz) || 'WIB'
		};
		console.log('setSelectedCityObject:', currentCity);
		// store api_id so frontend code can call external API directly
		const prayerCardEl = document.querySelector('.prayer-card');
		prayerCardEl.dataset.cityId = currentCity.api_id || '';
		// also store timezone label for other scripts
		prayerCardEl.dataset.cityTz = currentCity.tz || '';
		// update search input to reflect selection
		citySearch.value = currentCity.name + (currentCity.province ? ' — ' + currentCity.province : '');
		// hide suggestions
		suggestionsBox.style.display = 'none';
		// persist
		saveSelectedCity();
		setTimesFromApi(currentCity.api_id, currentCity.tz);
		// notify other scripts (e.g., beranda.js) that city changed
		try{ window.dispatchEvent(new CustomEvent('prayer-city-changed', { detail: currentCity })); }catch(e){}
	}

	// City search autocomplete (calls our backend endpoint)
	let searchTimeout = null;
	citySearch.addEventListener('input', function(e){
		const q = this.value.trim();
		clearTimeout(searchTimeout);
		suggestionsBox.style.display = 'none';
		suggestionsBox.innerHTML = '';
		if(q.length < 2) return;
		searchTimeout = setTimeout(()=>{
			fetch(`/api/sholat-cities?q=${encodeURIComponent(q)}`)
				.then(r=>r.json())
				.then(results=>{
					if(!results || results.length === 0){
						suggestionsBox.style.display = 'none';
						return;
					}
					// populate suggestions
					suggestionsBox.innerHTML = '';
					results.forEach(c=>{
						const item = document.createElement('button');
						item.type = 'button';
						item.className = 'list-group-item list-group-item-action';
						item.textContent = (c.name || '') + ' — ' + (c.province || '');
						item.dataset.id = c.id;
						item.dataset.apiId = c.api_id;
						item.dataset.tz = c.tz || '';
						item.dataset.name = c.name || '';
						item.dataset.province = c.province || '';
						item.addEventListener('click', function(){
							setSelectedCityObject({ id: this.dataset.id, api_id: this.dataset.apiId, name: this.dataset.name, province: this.dataset.province, tz: this.dataset.tz });
						});
						suggestionsBox.appendChild(item);
					});
					suggestionsBox.style.display = 'block';
				})
				.catch(err=>console.error(err));
		}, 300);
	});

	// hide suggestions when clicking outside
	document.addEventListener('click', function(e){
		if(!suggestionsBox.contains(e.target) && e.target !== citySearch){
			suggestionsBox.style.display = 'none';
		}
	});

	// Geolocation: try to detect and map to nearest city by name using reverse geocoding to get city name
	// If denied or fails, default to SURABAYA (api_id 1638)
	function fallbackToSurabaya(){
		setSelectedCityObject({ id: null, api_id: '1638', name: 'KOTA SURABAYA', province: 'JAWA TIMUR', tz: 'WIB' });
	}

	// on init: if there's a stored city, use it and skip geolocation
	const stored = loadSelectedCity();
	if(stored){
		if(stored.api_id){
			setSelectedCityObject(stored);
		} else if(stored.name){
			// try to resolve saved city to obtain api_id
			fetch(`/api/sholat-cities?q=${encodeURIComponent(stored.name)}`)
				.then(r=>r.json())
				.then(results=>{
					if(results && results.length>0){
						// try to find exact id match first
						let found = null;
						if(stored.id){
							found = results.find(x=>String(x.id)===String(stored.id));
						}
						if(!found){
							// try exact name match
							found = results.find(x=> (x.name||'').toLowerCase() === (stored.name||'').toLowerCase());
						}
						// fallback to first result
						if(!found) found = results[0];

						if(found){
							setSelectedCityObject(found);
							return;
						}
					}
					fallbackToSurabaya();
				})
				.catch(err=>{ console.error(err); fallbackToSurabaya(); });
		} else {
			fallbackToSurabaya();
		}
	} else if(navigator.geolocation){
		navigator.geolocation.getCurrentPosition(function(pos){
			const lat = pos.coords.latitude;
			const lon = pos.coords.longitude;
			// Use Nominatim reverse geocoding to get city name (no-key, but limited usage)
			fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`)
				.then(r=>r.json())
				.then(info=>{
					const city = info.address && (info.address.city || info.address.town || info.address.village || info.address.county || info.address.state_district);
					if(city){
						// query our backend for this city
						fetch(`/api/sholat-cities?q=${encodeURIComponent(city)}`)
							.then(r=>r.json())
							.then(results=>{
								if(results && results.length>0){
									// pick first match
									const c = results[0];
									setSelectedCityObject(c);
								} else {
									fallbackToSurabaya();
								}
							})
							.catch(err=>{ console.error(err); fallbackToSurabaya(); });
					} else {
						fallbackToSurabaya();
					}
				})
				.catch(err=>{ console.error(err); fallbackToSurabaya(); });

		}, function(err){
			// permission denied or error
			fallbackToSurabaya();
		}, {timeout:5000});
	} else {
		fallbackToSurabaya();
	}

})();
</script>

