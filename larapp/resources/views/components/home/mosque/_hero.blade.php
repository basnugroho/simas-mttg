<div class="container my-4 mt-2">
		<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
			<div class="position-relative">
				<img src="{{ isset($mosque) && $mosque->cover ? asset('images/'.$mosque->cover) : asset('images/mosque.webp') }}" alt="cover" class="w-100" style="height:220px;object-fit:cover;" />
				<div class="position-absolute" style="left:30px;bottom:-46px;">
					<div class="bg-white p-1 rounded" style="box-shadow:0 6px 18px rgba(0,0,0,0.08);">
						<img src="{{ isset($mosque) && $mosque->image ? asset('images/'.$mosque->image) : asset('images/mosque-1.png') }}" alt="avatar" class="rounded" style="width:96px;height:96px;object-fit:cover;" />
					</div>
				</div>
			</div>
			<div class="card-body pt-3">
				<div class="d-flex align-items-center justify-content-between" style="padding-left:130px;">
					<div>
						<h3 class="mb-1">{{ $mosque->name ?? 'Masjid Takkhobar' }}</h3>
					</div>
					<div class="text-muted small d-none d-md-flex align-items-center gap-3">
						<div class="d-flex align-items-center"><i class="bi bi-building me-1"></i> {{ $mosque->location ?? 'SBU Ketintang' }}</div>
						<div class="d-flex align-items-center"><i class="bi bi-geo-alt me-1"></i> {{ $mosque->city ?? 'Surabaya' }}</div>
						<div class="d-flex align-items-center"><i class="bi bi-book me-1"></i> {{ $mosque->province ?? 'Jawa Timur' }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>