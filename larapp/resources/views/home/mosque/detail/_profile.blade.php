<div id="detailmasjidprofile" style="display:none"></div>
<div id="profile" class="mosque-section show profile-wrap">
	<div class="profile-card card card-sm mb-3">
		<div class="card-body p-2.5">
			<h6 class="mb-2">PROFIL</h6>
			<ul class="list-unstyled small mb-0 profile-list">
				<li><span class="k">Nama</span> <span class="sep">:</span> <span class="v">{{ $mosque->name ?? 'Masjid Takkhobbar' }}</span></li>
				<li><span class="k">Tipe</span> <span class="sep">:</span> <span class="v">{{ $mosque->type ?? 'Masjid' }}</span></li>
				<li><span class="k">Didirikan</span> <span class="sep">:</span> <span class="v">{{ $mosque->established ?? ($mosque->established_at ?? 'Tahun 2004') }}</span></li>
				<li><span class="k">Jumlah BKM</span> <span class="sep">:</span> <span class="v">{{ $mosque->bkm_count ?? '—' }}</span></li>
				<li><span class="k">Luas Tanah</span> <span class="sep">:</span> <span class="v">{{ $mosque->land_area ?? '—' }}</span></li>
				<li><span class="k">Luas Bangunan</span> <span class="sep">:</span> <span class="v">{{ $mosque->building_area ?? '—' }}</span></li>
				<li><span class="k">Daya Tampung</span> <span class="sep">:</span> <span class="v">{{ $mosque->capacity ?? '—' }}</span></li>
			</ul>

			<h6 class="mb-2 small text-muted">ALAMAT</h6>
			<ul class="list-unstyled small mb-3 profile-list">
				<li><span class="k">Lokasi</span> <span class="sep">:</span> <span class="v">{{ $mosque->location ?? ($mosque->region_name ?? '—') }}</span></li>
				<li><span class="k">Kab. / Kota</span> <span class="sep">:</span> <span class="v">{{ $mosque->city->name ?? ($mosque->city_name ?? '—') }}</span></li>
				<li><span class="k">Provinsi</span> <span class="sep">:</span> <span class="v">{{ $mosque->province->name ?? ($mosque->province_name ?? '—') }}</span></li>
				<li><span class="k">Alamat</span> <span class="sep">:</span> <span class="v">{!! nl2br(e($mosque->address ?? '—')) !!}</span></li>
			</ul>

			<h6 class="mb-2 small text-muted">KONTAK</h6>
			<ul class="list-unstyled small mb-3 profile-list">
				<li><span class="k">Email</span> <span class="sep">:</span> <span class="v">{{ $mosque->email ?? '—' }}</span></li>
				<li><span class="k">WA</span> <span class="sep">:</span> <span class="v">{{ $mosque->wa ?? '—' }}</span></li>
			</ul>

			@php
				// ambil daftar fasilitas dari tabel facilities (model Facility)
				try {
					$all = \App\Models\Facility::query()
						->orderBy('name')
						->pluck('name')
						->toArray();
				} catch (Exception $e) {
					// fallback jika DB tidak tersedia
					$all = [
						'Tempat Wudhu','Karpet','Lemari Quran','Lemari Sarung / Mukena','AC','Kipas Angin','Lampu / Penerangan','Rak Sepatu','Sandal Wudhu','Tirai Jamaah','Sound System'
					];
				}
				$have = [];
				if(is_array($mosque->facilities)){
					foreach($mosque->facilities as $f){
						$have[] = is_array($f) ? ($f['name'] ?? '') : $f;
					}
				} elseif(is_string($mosque->facilities)){
					$have = array_map('trim', explode(',', $mosque->facilities));
				}

				// compute availability counts and percentage (used in multiple places)
				$totalPossible = isset($all) ? count($all) : 0;
				$availableCount = 0;
				if(isset($mosque->facilities) && is_array($mosque->facilities)){
					foreach($mosque->facilities as $it){
						if(is_array($it)){
							if(!empty($it['is_available'])) $availableCount++;
						} else {
							if(in_array($it, $all)) $availableCount++;
						}
					}
				} elseif(is_string($mosque->facilities)){
					$parts = explode(',', $mosque->facilities);
					foreach($parts as $p) if(in_array(trim($p), $all)) $availableCount++;
				}
				$percentage = $mosque->completion_percentage ?? ($totalPossible ? round($availableCount / $totalPossible * 100) : 0);
				$availableCount = (int) $availableCount;
				$totalPossible = (int) $totalPossible;
			@endphp

			<div class="mb-2"><strong>FASILITAS <span class="text-success">{{ $percentage }}%</span></strong></div>
			<div class="facility-badges">
				@foreach($all as $f)
					@php $haveIt = in_array($f, $have); @endphp
					<span class="badge facility @if($haveIt) available @else missing @endif">{{ $f }}</span>
				@endforeach
			</div>
		</div>
	</div>

	<div class="profile-detail">
	<div class="detail-hero card card-sm mb-3">
		<div class="row g-2">
			@php
				// build list of mosque photos from relation (use Storage public url)
				$heroPhotos = [];
				if ($mosque->relationLoaded('photos') && $mosque->photos->count()){
					foreach($mosque->photos as $p){
						$src = null;
						if(!empty($p->path)){
							try{ $src = \Illuminate\Support\Facades\Storage::disk('public')->url($p->path); }catch(Exception $e){ $src = null; }
						} elseif(!empty($p->url)){
							$src = $p->url;
						}
						if($src) {
							$heroPhotos[] = [
								'src' => $src,
								'caption' => $p->caption ?? $p->note ?? null,
							];
						}
					}
				}
				// fallback to db accessors
				if(empty($heroPhotos)){
					$src = $mosque->db_image_url ?? $mosque->public_image_url ?? asset('images/mosque-1.jpg');
					$heroPhotos[] = ['src' => $src, 'caption' => null];
				}
				$totalHero = count($heroPhotos);
			@endphp

			<div class="col-8">
				@if($totalHero > 1)
					<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
						<div class="carousel-inner">
							@foreach($heroPhotos as $i => $hp)
								<div class="carousel-item @if($i==0) active @endif" data-hero-index="{{ $i }}">
									<img src="{{ $hp['src'] }}" class="d-block w-100 rounded hero-img" style="height:360px; object-fit:cover;" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/mosque-1.jpg') }}'" />
									@if(!empty($hp['caption']))
										<div class="carousel-caption d-none d-md-block text-start bg-dark bg-opacity-50 rounded px-2 py-1">
											<span>{{ $hp['caption'] }}</span>
										</div>
									@endif
								</div>
							@endforeach
						</div>
						<button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
						</button>
						<button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
						</button>
					</div>
				@else
					<img src="{{ $heroPhotos[0]['src'] }}" class="img-fluid rounded hero-img" style="height:360px; object-fit:cover;" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/mosque-1.jpg') }}'" />
					@if(!empty($heroPhotos[0]['caption']))
						<div class="mt-2 small text-muted">{{ $heroPhotos[0]['caption'] }}</div>
					@endif
				@endif
			</div>
			<div class="col-4 d-flex flex-column gap-2">
				@php
					// show up to 2 thumbnails (skip the first/main) preserving keys
					$thumbs = array_slice($heroPhotos, 1, 2, true);
					$remaining = $totalHero - 1 - count($thumbs);
				@endphp
				@if(count($thumbs))
					@foreach($thumbs as $k => $t)
						<img data-hero-index="{{ $k }}" src="{{ $t['src'] }}" class="img-fluid rounded hero-thumb" style="height:120px; object-fit:cover;" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/mosque-2.jpg') }}'" />
					@endforeach
				@endif
				<div class="flex-fill rounded d-flex align-items-center justify-content-center bg-dark text-white">
					@if($remaining > 0)
						+{{ $remaining }}
					@else
						{{ $totalHero }}
					@endif
				</div>
			</div>
		</div>
	</div>
<script>
(function(){
	const ptEl = document.getElementById('prayer-times');
	const tzLabelEl = document.getElementById('tz-label');
	// if prayer-times UI is not present on this page, skip prayer-times wiring
	if(!ptEl) return;

	function mapZoneToLabel(tz){
		if(!tz) return '(WIB)';
		if(tz === 'Asia/Jakarta' || tz.includes('Jakarta')) return '(WIB)';
		if(tz === 'Asia/Makassar' || tz.includes('Makassar')) return '(WITA)';
		if(tz === 'Asia/Jayapura' || tz.includes('Jayapura')) return '(WIT)';
		return '(WIB)';
	}

	function formatTime(t){
		if(!t) return '—';
		// remove potential (+xx) suffix returned by some APIs
		return t.replace(/\s*\(.*\)$/, '');
	}

	function renderTimes(data){
		const msgEl = document.getElementById('prayer-times-msg');
		if(!data){ ptEl.innerText = 'Jadwal tidak tersedia'; if(msgEl) msgEl.innerText = ''; return; }
		if(data.error){ ptEl.innerText = 'Gagal memuat jadwal'; if(msgEl) msgEl.innerText = 'Silakan coba lagi nanti.'; return; }

		const t = data.times || {};
		if(tzLabelEl) tzLabelEl.innerText = mapZoneToLabel(data.timezone) + ' ' + (data.timezone || '');
		ptEl.innerHTML = `\
			<div class="d-flex justify-content-between"><span>Subuh</span><strong>${formatTime(t.subuh)}</strong></div>\
			<div class="d-flex justify-content-between"><span>Dzuhur</span><strong>${formatTime(t.dzuhur)}</strong></div>\
			<div class="d-flex justify-content-between"><span>Ashar</span><strong>${formatTime(t.ashar)}</strong></div>\
			<div class="d-flex justify-content-between"><span>Maghrib</span><strong>${formatTime(t.maghrib)}</strong></div>\
			<div class="d-flex justify-content-between"><span>Isya</span><strong>${formatTime(t.isya)}</strong></div>\
		`;

		if(msgEl) msgEl.innerText = `Data untuk tanggal ${data.date || ''}`;
	}

	function fetchTimes(lat, lon, tz){
		const msgEl = document.getElementById('prayer-times-msg');
		ptEl.innerText = 'Memuat jadwal...';
		if(msgEl) msgEl.innerText = '';
		let url = '/api/prayertimes';
		const params = new URLSearchParams();
		if(lat) params.set('lat', lat);
		if(lon) params.set('lon', lon);
		if(tz) params.set('timezone', tz);
		if(Array.from(params).length) url += '?'+params.toString();
		fetch(url).then(r=>r.json()).then(renderTimes).catch(e=>{ ptEl.innerText = 'Gagal memuat jadwal'; if(msgEl) msgEl.innerText = 'Periksa koneksi Anda.'; console.error(e); });
	}

	let userTz = null;
	try{ userTz = Intl.DateTimeFormat().resolvedOptions().timeZone; }catch(e){ userTz = null; }

	// helper to trigger fetch using either detected or selected timezone
	function doFetchWithGeo(useTz){
		if(navigator.geolocation){
			navigator.geolocation.getCurrentPosition(function(pos){
				fetchTimes(pos.coords.latitude, pos.coords.longitude, useTz);
			}, function(err){
				fetchTimes(-7.257472, 112.752088, useTz || 'Asia/Jakarta');
			}, { timeout: 5000 });
		} else {
			fetchTimes(-7.257472, 112.752088, useTz || 'Asia/Jakarta');
		}
	}

	// initial load: auto-detect timezone
	doFetchWithGeo(userTz);

	// wire up manual controls (if present)
	const tzSelect = document.getElementById('tz-select');
	const tzRefresh = document.getElementById('tz-refresh');
	if(tzSelect){
		tzSelect.addEventListener('change', function(){
			const v = this.value || (Intl ? Intl.DateTimeFormat().resolvedOptions().timeZone : 'Asia/Jakarta');
			doFetchWithGeo(v);
		});
	}
	if(tzRefresh){
		tzRefresh.addEventListener('click', function(){
			const selected = tzSelect ? (tzSelect.value || null) : null;
			doFetchWithGeo(selected || userTz);
		});
	}

})();
</script>

	<div class="card card-sm detail-meta mb-3 p-3">
		<h3 class="mb-1">{{ $mosque->name ?? 'Masjid Takkhobbar' }}</h3>
		<div class="text-muted small">{{ $mosque->address ?? 'SBU Ketintang, Surabaya, Jawa Timur' }}</div>

		<p class="mt-3">
			<strong>Deskripsi Masjid :</strong><br>
			{{ $mosque->description ?? $mosque->short_description ?? 'Lorem ipsum is simply dummy text...' }}
		</p>
	</div>
    

	<div class="card card-sm mb-3 p-3">
		@php
		// $availableCount, $totalPossible and $percentage already computed above
		@endphp
		<div class="d-flex align-items-center justify-content-between mb-3">
			<strong>Fasilitas: {{ $availableCount }} / {{ $totalPossible }} </strong>
			<div class="small text-muted">{{ $percentage }}%</div>
		</div>

		<div class="gallery-thumbs d-flex gap-2 mb-3">
			@php
			$thumbs = [];
			// prefer photos from mosqueFacility photos if available
			if(isset($mosque->mosqueFacility) && $mosque->mosqueFacility->count()){
				foreach($mosque->mosqueFacility as $mf){
							if($mf->photos && $mf->photos->count()){
						foreach($mf->photos as $p){
							if(!empty($p->url)){
								$thumbs[] = $p->url;
							} elseif(!empty($p->path)) {
								try {
									$thumbs[] = \Illuminate\Support\Facades\Storage::disk('public')->url($p->path);
								} catch (Exception $e) {
									$thumbs[] = null;
								}
							} else {
								$thumbs[] = null;
							}
						}
					}
				}
			}
			// fallback to generic images
			foreach(array_slice($thumbs,0,4) as $t){
				echo '<div class="thumb"><img src="'.e($t).'" data-src="'.e($t).'" loading="lazy" class="img-fluid rounded" /></div>';
			}
			@endphp
		</div>

		@php
			// map mosqueFacility by facility name for quick lookup
			$mfMap = [];
			if(isset($mosque->mosqueFacility) && $mosque->mosqueFacility->count()){
				foreach($mosque->mosqueFacility as $mf){
					$key = $mf->facility->name ?? ($mf->name ?? null);
					if($key) $mfMap[$key] = $mf;
				}
			}
		@endphp

		<div class="accordion" id="facilitiesAccordion">
			@foreach($all as $idx => $f)
				@php
					$haveIt = in_array($f, $have);
					$mf = $mfMap[$f] ?? null;
					$qty = null;
					if($mf && isset($mf->quantity)){
						$qty = $mf->quantity;
					}
				@endphp
				<div class="accordion-item">
					<h2 class="accordion-header" id="heading{{ $idx }}">
						<button class="accordion-button collapsed d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $idx }}" aria-expanded="false" aria-controls="collapse{{ $idx }}">
							<span>{{ $f }}</span>
							@if($haveIt)
								<span class="badge ms-2 bg-success">Ada : {{ $qty ?? '1' }}</span>
							@else
								<span class="badge ms-2 bg-secondary">Tidak Ada</span>
							@endif
						</button>
					</h2>
					<div id="collapse{{ $idx }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $idx }}" data-bs-parent="#facilitiesAccordion">
						<div class="accordion-body">
							@php
								$mf = $mfMap[$f] ?? null;
								$facImgs = [];
								if($mf && $mf->relationLoaded('photos') && $mf->photos->count()){
									foreach($mf->photos as $pf){
										if(!empty($pf->path)){
											try{ $facImgs[] = \Illuminate\Support\Facades\Storage::disk('public')->url($pf->path); }catch(Exception $e){ }
										} elseif(!empty($pf->url)) {
											$facImgs[] = $pf->url;
										}
									}
								}
								// determine how many images to show: prefer quantity if set
								$showCount = null;
								if($mf && isset($mf->quantity) && is_numeric($mf->quantity) && $mf->quantity > 0){
									$showCount = (int) $mf->quantity;
								}
								if($showCount === null) $showCount = count($facImgs);
								@endphp
								@if(!empty($facImgs))
									<div class="gallery-thumbs d-flex gap-2 mb-3">
										@foreach(array_slice($facImgs, 0, $showCount) as $fi)
											<div class="thumb"><img src="{{ $fi }}" data-src="{{ $fi }}" loading="lazy" class="img-fluid rounded" style="max-height:160px; object-fit:cover;" onerror="this.onerror=null;this.src='{{ asset('images/mosque-2.jpg') }}'"/></div>
										@endforeach
									</div>
								@endif
								<p>{!! nl2br(e($mf->note ?? 'Keterangan singkat tentang '.$f.' jika tersedia.')) !!}</p>
							</div>
						</div>
				</div>
			@endforeach
		</div>
	</div>

</div>

</div>
<style>
/* facility thumbs sizing */
.gallery-thumbs .thumb img { width: 100%; height: 96px; object-fit: cover; display:block; }
.gallery-thumbs .thumb { width: calc(25% - 12px); }
@media (max-width: 768px) { .gallery-thumbs .thumb { width: calc(50% - 8px); } }
/* hero caption and thumbnail */
.carousel-caption { bottom: 12px; left: 12px; right: auto; }
.hero-thumb { cursor: pointer; }
.hero-img { min-height: 120px; }
</style>

<!-- Carousel Lightbox modal -->
<div class="modal fade" id="facilityLightbox" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-xl">
		<div class="modal-content bg-transparent border-0">
			<div class="modal-body p-0">
				<div id="facilityCarousel" class="carousel slide" data-bs-ride="false">
					<div class="carousel-inner" id="facilityCarouselInner"></div>
					<button class="carousel-control-prev" type="button" data-bs-target="#facilityCarousel" data-bs-slide="prev">
						<span class="carousel-control-prev-icon" aria-hidden="true"></span>
						<span class="visually-hidden">Previous</span>
					</button>
					<button class="carousel-control-next" type="button" data-bs-target="#facilityCarousel" data-bs-slide="next">
						<span class="carousel-control-next-icon" aria-hidden="true"></span>
						<span class="visually-hidden">Next</span>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
	// build arrays of images per facility from DOM at runtime
	document.querySelectorAll('.gallery-thumbs').forEach(function(g){
		// collect image srcs and store on parent element for quick access
		var imgs = Array.from(g.querySelectorAll('img')).map(i => i.getAttribute('src'));
		g.dataset.facilityImages = JSON.stringify(imgs);
	});

	function openFacilityCarousel(imgs, startIndex){
		var inner = document.getElementById('facilityCarouselInner');
		inner.innerHTML = '';
		imgs.forEach(function(src, idx){
			var item = document.createElement('div');
			item.className = 'carousel-item' + (idx === startIndex ? ' active' : '');
			var img = document.createElement('img');
			img.src = src;
			img.className = 'd-block w-100 rounded';
			img.style.maxHeight = '80vh';
			img.style.objectFit = 'contain';
			item.appendChild(img);
			inner.appendChild(item);
		});
		var modalEl = document.getElementById('facilityLightbox');
		var modal = new bootstrap.Modal(modalEl);
		modal.show();
		// reset carousel to the requested slide
		var carousel = bootstrap.Carousel.getOrCreateInstance(document.getElementById('facilityCarousel'));
		carousel.to(startIndex);
	}

	// wire thumbnails: find gallery ancestor and index
	document.querySelectorAll('.gallery-thumbs .thumb img').forEach(function(img){
		img.style.cursor = 'pointer';
		img.addEventListener('click', function(e){
			var gallery = e.target.closest('.gallery-thumbs');
			var imgs = JSON.parse(gallery.dataset.facilityImages || '[]');
			var clickedSrc = e.target.getAttribute('src');
			var startIndex = imgs.indexOf(clickedSrc);
			if(startIndex < 0) startIndex = 0;
			openFacilityCarousel(imgs, startIndex);
		});
	});

	// wire hero thumbnails to jump hero carousel
	document.querySelectorAll('.hero-thumb').forEach(function(t){
		t.addEventListener('click', function(e){
			var idx = parseInt(e.target.getAttribute('data-hero-index') || 0, 10);
			var heroEl = document.getElementById('heroCarousel');
			if(!heroEl) return;
			var carousel = bootstrap.Carousel.getOrCreateInstance(heroEl);
			carousel.to(idx);
		});
	});
});
</script>
