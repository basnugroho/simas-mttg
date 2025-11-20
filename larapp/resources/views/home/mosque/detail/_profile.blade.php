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

			<div class="mb-2"><strong>FASILITAS <span class="text-success">90%</span></strong></div>
			<div class="facility-badges">
				@php
					$all = [
						'Tempat Wudhu','Karpet','Lemari Quran','Lemari Sarung / Mukena','AC','Kipas Angin','Lampu / Penerangan','Rak Sepatu','Sandal Wudhu','Tirai Jamaah','Sound System'
					];
					$have = [];
					if(is_array($mosque->facilities)){
						foreach($mosque->facilities as $f){
							$have[] = is_array($f) ? ($f['name'] ?? '') : $f;
						}
					} elseif(is_string($mosque->facilities)){
						$have = explode(',', $mosque->facilities);
					}
				@endphp

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
			<div class="col-8">
				@php
					$cover = $mosque->cover ?? $mosque->image_url ?? null;
					// if there is no image, pick a random fallback from mosque-1..5
					if(!$cover){ $rand = rand(1,5); $cover = asset('images/mosque-'.$rand.'.jpg'); }
				@endphp
				<img src="{{ $cover }}" alt="hero" class="img-fluid rounded" onerror="this.onerror=null;this.src='{{ asset('images/mosque-1.jpg') }}'" />
			</div>
			<div class="col-4 d-flex flex-column gap-2">
				@php
					$thumb = $mosque->image_url ?? null;
					if(!$thumb){ $thumb = asset('images/mosque-2.jpg'); }
				@endphp
				<img src="{{ $thumb }}" alt="thumb1" class="img-fluid rounded" onerror="this.onerror=null;this.src='{{ asset('images/mosque-2.jpg') }}'" />
				<div class="flex-fill rounded d-flex align-items-center justify-content-center bg-dark text-white">+7</div>
			</div>
		</div>
	</div>

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
		// compute availability counts and percentage
		$totalPossible = isset($all) ? count($all) : 0;
		$availableCount = 0;
		// mosque->facilities may be array of arrays (with 'is_available') or list of names
		if(isset($mosque->facilities) && is_array($mosque->facilities)){
			foreach($mosque->facilities as $it){
				if(is_array($it)){
					if(!empty($it['is_available'])) $availableCount++;
				} else {
					// string name
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
							$thumbs[] = $p->path ? asset($p->path) : null;
						}
					}
				}
			}
			// fallback to generic images
			if(empty($thumbs)){
				for($i=1;$i<=4;$i++) $thumbs[] = asset('images/mosque-'.$i.'.jpg');
			}
			$thumbs = array_filter($thumbs);
			foreach(array_slice($thumbs,0,4) as $t){
				echo '<div class="thumb"><img src="'.e($t).'" class="img-fluid rounded" /></div>';
			}
			@endphp
		</div>

		<div class="accordion" id="facilitiesAccordion">
			@foreach($all as $idx => $f)
				@php $haveIt = in_array($f, $have); @endphp
				<div class="accordion-item">
					<h2 class="accordion-header" id="heading{{ $idx }}">
						<button class="accordion-button collapsed d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $idx }}" aria-expanded="false" aria-controls="collapse{{ $idx }}">
							<span>{{ $f }}</span>
							<span class="badge ms-2 {{ $haveIt ? 'bg-success' : 'bg-secondary' }}">{{ $haveIt ? 'Ada' : 'Tidak' }}</span>
						</button>
					</h2>
					<div id="collapse{{ $idx }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $idx }}" data-bs-parent="#facilitiesAccordion">
						<div class="accordion-body">
							Keterangan singkat tentang {{ $f }} jika tersedia.
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>

</div>

</div>
