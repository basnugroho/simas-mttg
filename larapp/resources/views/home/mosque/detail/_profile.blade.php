<div id="detailmasjidprofile" style="display:none"></div>
<div id="profile" class="mosque-section show profile-wrap">
	<div class="profile-card card card-sm mb-3">
		<div class="card-body p-2.5">
			<h6 class="mb-2">PROFIL</h6>
			<ul class="list-unstyled small mb-0 profile-list">
				<li><span class="k">Nama</span> <span class="sep">:</span> <span class="v">{{ $mosque->name ?? 'Masjid Takkhobbar' }}</span></li>
				<li><span class="k">Tipe</span> <span class="sep">:</span> <span class="v">{{ $mosque->type ?? 'Masjid' }}</span></li>
				<li><span class="k">Didirikan</span> <span class="sep">:</span> <span class="v">{{ $mosque->established ?? 'Tahun 2004' }}</span></li>
				<li><span class="k">Jumlah BKM</span> <span class="sep">:</span> <span class="v">{{ $mosque->bkm_count ?? '10 Orang' }}</span></li>
				<li><span class="k">Luas Tanah</span> <span class="sep">:</span> <span class="v">{{ $mosque->land_area ?? '100 m2' }}</span></li>
				<li><span class="k">Luas Bangunan</span> <span class="sep">:</span> <span class="v">{{ $mosque->building_area ?? '100 m2' }}</span></li>
				<li><span class="k">Daya Tampung</span> <span class="sep">:</span> <span class="v">{{ $mosque->capacity ?? '1.000' }}</span></li>
			</ul>

			<h6 class="mb-2 small text-muted">ALAMAT</h6>
			<ul class="list-unstyled small mb-3 profile-list">
				<li><span class="k">Lokasi</span> <span class="sep">:</span> <span class="v">{{ $mosque->location ?? 'SBU Ketintang' }}</span></li>
				<li><span class="k">Kab. / Kota</span> <span class="sep">:</span> <span class="v">{{ $mosque->city ?? 'Surabaya' }}</span></li>
				<li><span class="k">Provinsi</span> <span class="sep">:</span> <span class="v">{{ $mosque->province ?? 'Jawa Timur' }}</span></li>
				<li><span class="k">Alamat</span> <span class="sep">:</span> <span class="v">{{ $mosque->address ?? 'Jl. Ketintang Baru No. 20, Surabaya, Jawa Timur' }}</span></li>
			</ul>

			<h6 class="mb-2 small text-muted">KONTAK</h6>
			<ul class="list-unstyled small mb-3 profile-list">
				<li><span class="k">Email</span> <span class="sep">:</span> <span class="v">{{ $mosque->email ?? 'masji@gmail.com' }}</span></li>
				<li><span class="k">WA</span> <span class="sep">:</span> <span class="v">{{ $mosque->wa ?? '+628181018171' }}</span></li>
			</ul>

			<div class="mb-2"><strong>FASILITAS <span class="text-success">90%</span></strong></div>
			<div class="facility-badges">
				@php
					$all = [
						'Tempat Wudhu','Karpet','Lemari Quran','Lemari Sarung / Mukena','AC','Kipas Angin','Lampu / Penerangan','Rak Sepatu','Sandal Wudhu','Tirai Jamaah','Sound System'
					];
					$have = is_array($mosque->facilities ?? null) ? $mosque->facilities : (explode(',', $mosque->facilities ?? '') ?: []);
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
				<img src="{{ asset('images/mosque-1.png') }}" alt="hero" class="img-fluid rounded" />
			</div>
			<div class="col-4 d-flex flex-column gap-2">
				<img src="{{ asset('images/mosque-2.png') }}" alt="thumb1" class="img-fluid rounded" />
				<div class="flex-fill rounded d-flex align-items-center justify-content-center bg-dark text-white">+7</div>
			</div>
		</div>
	</div>

	<div class="card card-sm detail-meta mb-3 p-3">
		<h3 class="mb-1">{{ $mosque->name ?? 'Masjid Takkhobbar' }}</h3>
		<div class="text-muted small">{{ $mosque->address ?? 'SBU Ketintang, Surabaya, Jawa Timur' }}</div>

		<p class="mt-3">
			<strong>Sejarah Masjid :</strong><br>
			{{ $mosque->history ?? 'Lorem ipsum is simply dummy text...' }}
		</p>
	</div>
    

	<div class="card card-sm mb-3 p-3">
		<div class="d-flex align-items-center justify-content-between mb-3">
			<strong>Fasilitas 90% :</strong>
			<div class="small text-muted">&nbsp;</div>
		</div>

		<div class="gallery-thumbs d-flex gap-2 mb-3">
			@for($i=1;$i<=4;$i++)
				<div class="thumb">
					<img src="{{ asset('images/mosque-'.$i.'.png') }}" class="img-fluid rounded" />
				</div>
			@endfor
		</div>

		<div class="accordion" id="facilitiesAccordion">
			@foreach($all as $idx => $f)
				<div class="accordion-item">
					<h2 class="accordion-header" id="heading{{ $idx }}">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $idx }}" aria-expanded="false" aria-controls="collapse{{ $idx }}">
							{{ $f }}
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
