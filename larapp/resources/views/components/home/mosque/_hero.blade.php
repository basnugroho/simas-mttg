@props(['mosque' => null])
<div class="container my-4 mt-2">
		<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
			@php
				// determine cover and avatar URLs
				$coverUrl = null;
				$avatarUrl = null;
				if(isset($mosque)){
					if(!empty($mosque->cover)){
						$c = $mosque->cover;
						if(preg_match('/^https?:\/\//i', $c) || strpos($c, '/') === 0){
							$coverUrl = $c;
						} else {
							$coverUrl = asset($c);
						}
					} elseif(!empty($mosque->image_url)){
						$c = $mosque->image_url;
						if(preg_match('/^https?:\/\//i', $c) || strpos($c, '/') === 0){
							$coverUrl = $c;
						} else {
							$coverUrl = asset($c);
						}
					}
					if(!empty($mosque->image_url)){
						$a = $mosque->image_url;
						if(preg_match('/^https?:\/\//i', $a) || strpos($a, '/') === 0){
							$avatarUrl = $a;
						} else {
							$avatarUrl = asset($a);
						}
					}
				}
				if(!$coverUrl){ $coverUrl = asset('images/mosque.webp'); }
				if(!$avatarUrl){ $avatarUrl = asset('images/mosque-1.jpg'); }
			@endphp
			<div class="position-relative">
				<img src="{{ $coverUrl }}" alt="cover" class="w-100" style="height:220px;object-fit:cover;" onerror="this.onerror=null;this.src='{{ asset('images/mosque.webp') }}'" />
				<div class="position-absolute" style="left:30px;bottom:-46px;">
					<div class="bg-white p-1 rounded" style="box-shadow:0 6px 18px rgba(0,0,0,0.08);">
						<img src="{{ $avatarUrl }}" alt="avatar" class="rounded" style="width:96px;height:96px;object-fit:cover;" onerror="this.onerror=null;this.src='{{ asset('images/mosque-1.jpg') }}'" />
					</div>
				</div>
			</div>
			<div class="card-body pt-3">
				<div class="d-flex align-items-center justify-content-between" style="padding-left:130px;">
					<div>
						<h3 class="mb-1">{{ $mosque->name ?? 'Masjid Takkhobar' }}</h3>
						<div class="text-muted small d-md-none">{{ $mosque->city->name ?? $mosque->city ?? '' }}</div>
					</div>
					<div class="text-muted small d-none d-md-flex align-items-center gap-3">
						<div class="d-flex align-items-center"><i class="bi bi-geo me-1"></i> Area {{ $mosque->province->name ?? $mosque->province ?? '—' }}</div>
						<div class="d-flex align-items-center"><i class="bi bi-diagram-3 me-1"></i> Witel {{ $mosque->witel->name ?? $mosque->witel ?? '—' }}</div>
						<div class="d-flex align-items-center"><i class="bi bi-hdd-network me-1"></i> STO {{ $mosque->sto->name ?? $mosque->sto ?? '—' }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>