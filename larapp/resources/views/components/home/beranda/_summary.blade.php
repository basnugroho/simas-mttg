@props(['regions' => null, 'summary' => null])
<section class="summary-section mt-2">
	<div class="container">
		<div class="summary-card bg-white p-4 rounded-3 shadow-sm position-relative" style="overflow:visible">
			<div class="row align-items-start">
				<div class="col-md-8">
					<div class="text-muted small">Ringkasan Informasi</div>
					<h3 class="mt-1" style="font-weight:700">Data Masjid dan Musholla</h3>
					<p class="text-muted mb-3">Di wilayah (Witel) Telkom Regional 3 Jatim, Bali, Nusa Tenggara</p>
				</div>
				<div class="col-md-4 text-end">
					<a href="{{ route('masjid') }}" class="btn btn-outline-dark rounded-pill">Lihat Semua</a>
				</div>
			</div>
	
			@php $firstThree = isset($regions) ? $regions->take(3) : collect(); @endphp
			<div class="row g-4 mt-10">
				@forelse($firstThree as $r)
					<div class="col-12 col-md-6 col-lg-3">
						<div class="region-card p-4 rounded-3 bg-white shadow-sm h-100 border" style="border-color:rgba(0,0,0,0.04)">
							<div class="text-danger small">Wilayah</div>
							<h5 class="mt-1" style="font-weight:700">{{ $r->name }}</h5>
							<div class="text-muted small mt-3"> Data Keseluruhan</div>
							<ul class="list-unstyled mt-2 mb-3" style="line-height:1.8">
								<li><i class="bi bi-building me-2"></i> Jumlah Masjid : <strong>{{ $r->masjid_count ?? 0 }}</strong></li>
								<li><i class="bi bi-door-open me-2"></i> Jumlah Musholla : <strong>{{ $r->musholla_count ?? 0 }}</strong></li>
								<li><i class="bi bi-people me-2"></i> Jumlah BKM : <strong>{{ $r->bkm_count ?? 0 }}</strong></li>
							</ul>
							<div class="text-muted small"> Kelengkapan Fasilitas</div>
							<ul class="list-unstyled mt-2 mb-3" style="line-height:1.8">
								<li><i class="bi bi-door-open-fill me-2"></i> Masjid : <strong>{{ $r->complete_masjid_count ?? 0 }}/{{ $r->masjid_count ?? 0 }}</strong></li>
								<li><i class="bi bi-x-square me-2"></i> Musholla : <strong>{{ $r->complete_musholla_count ?? 0 }}/{{ $r->musholla_count ?? 0 }}</strong></li>
							</ul>
							<div class="d-grid">
								<a href="{{ route('masjid', ['province_id' => $r->id, 'witel_id' => '', 'sto_id' => '', 'type' => '', 'facility_id' => '']) }}" class="btn btn-dark rounded-pill">Lihat Selengkapnya</a>
							</div>
						</div>
					</div>
				@empty
					<div class="col-12"><div class="alert alert-light border">Tidak ada data wilayah tersedia.</div></div>
				@endforelse

				@isset($summary)
				<div class="col-12 col-md-6 col-lg-3">
					<div class="region-card p-4 rounded-3 bg-dark text-white h-100 shadow-lg position-relative">
						<div class="text-danger small">Wilayah</div>
						<h5 class="mt-1" style="font-weight:700">Keseluruhan</h5>
						<div class="text-white-50 small mt-3"> Data Keseluruhan</div>
						<ul class="list-unstyled mt-2 mb-3" style="line-height:1.8">
							<li><i class="bi bi-building me-2"></i> Jumlah Masjid : <strong>{{ $summary['masjid_total'] }}</strong></li>
							<li><i class="bi bi-door-open me-2"></i> Jumlah Musholla : <strong>{{ $summary['musholla_total'] }}</strong></li>
							<li><i class="bi bi-people me-2"></i> Jumlah BKM : <strong>{{ $summary['bkm_total'] }}</strong></li>
						</ul>
						<div class="text-white-50 small"> Kelengkapan Fasilitas</div>
						<ul class="list-unstyled mt-2 mb-3" style="line-height:1.8">
							<li><i class="bi bi-door-open-fill me-2"></i> Masjid : <strong>{{ $summary['complete_masjid_total'] }}/{{ $summary['masjid_total'] }}</strong></li>
							<li><i class="bi bi-x-square me-2"></i> Musholla : <strong>{{ $summary['complete_musholla_total'] }}/{{ $summary['musholla_total'] }}</strong></li>
						</ul>
						<div class="d-grid">
							<a href="{{ route('masjid', ['province_id' => $summary['province_id'] ?? '', 'witel_id' => '', 'sto_id' => '', 'type' => '', 'facility_id' => '']) }}" class="btn btn-light rounded-pill">Lihat Selengkapnya</a>
						</div>
					</div>
				</div>
				@endisset
			</div>
		</div>
	</div>
</section>
