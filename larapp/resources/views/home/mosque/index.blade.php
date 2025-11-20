<x-home.layout :title="'Simas MTTG - Detail Masjid'">
	<x-home._navbar />
 	<x-home.beranda._hero>
        <h3 class="hero-title display-6 mb-2">Data Masjid Telkom Regional 3</h3>
			<p class="mb-4 fw-medium" style="font-size:0.95rem">Telkom Regional 3 (Jawa Timur, Bali dan Nusa Tenggara)</p>
    </x-home._hero>
	<section class="container my-5 mt-4">
		<div class="row">
			<aside class="col-md-3 mb-4">
				<div class="card p-3 shadow-sm">
					<h5 class="mb-3">Filter</h5>
					<form method="GET" action="{{ route('masjid') }}">
						<div class="mb-2">
							<label class="form-label small">Provinsi</label>
							<select name="province_id" class="form-select">
								<option value="">Semua Provinsi</option>
								@foreach($provinces ?? collect() as $p)
									<option value="{{ $p->id }}" {{ request()->query('province_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
								@endforeach
							</select>
						</div>
						<!-- Kota / Kabupaten removed per request -->
						<div class="mb-2">
							<label class="form-label small">Witel</label>
							<select name="witel_id" class="form-select" data-selected="{{ request()->query('witel_id') }}">
								<option value="">Semua Witel</option>
								@foreach($witels ?? collect() as $w)
									<option value="{{ $w->id }}" {{ request()->query('witel_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="mb-2">
							<label class="form-label small">STO</label>
							<select name="sto_id" class="form-select" data-selected="{{ request()->query('sto_id') }}">
								<option value="">Semua STO</option>
								@foreach($stos ?? collect() as $s)
									<option value="{{ $s->id }}" {{ request()->query('sto_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="mb-2">
							<label class="form-label small">Tipe</label>
							<select name="type" class="form-select">
								<option value="">Semua Tipe</option>
								<option value="MASJID" {{ strtoupper(request()->query('type') ?? '') === 'MASJID' ? 'selected' : '' }}>Masjid</option>
								<option value="MUSHOLLA" {{ strtoupper(request()->query('type') ?? '') === 'MUSHOLLA' ? 'selected' : '' }}>Mushalla</option>
							</select>
						</div>
						<div class="mb-2">
							<label class="form-label small">Fasilitas</label>
							<select name="facility_id" class="form-select">
								<option value="">Semua Fasilitas</option>
								@foreach($facilities ?? collect() as $f)
									<option value="{{ $f->id }}" {{ request()->query('facility_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
								@endforeach
							</select>
						</div>
							
						<div class="d-flex gap-2">
							<a href="{{ route('masjid') }}" class="btn btn-light w-50">Reset</a>
							<button type="submit" class="btn btn-success w-50">Cari</button>
						</div>
					</form>
				</div>
			</aside>
			<main class="col-md-9">
				<div class="mosque-scroll-wrapper">
					<div class="row g-4">
					@forelse($mosques as $mosque)
						@php
							$img = $mosque->image_url ? asset($mosque->image_url) : asset('images/mosque-1.jpg');
						@endphp
						<div class="col-md-6 col-lg-4">
							<a href="{{ route('mosque.show', $mosque->id) }}" class="text-decoration-none text-dark">
								<div class="card h-100 shadow-sm position-relative">
									<img src="{{ $mosque->image_url ? asset($mosque->image_url) : asset('images/mosque-1.jpg') }}" onerror="this.onerror=null;this.src='{{ asset('images/mosque-1.jpg') }}'" class="card-img-top" style="height:150px; object-fit:cover;" alt="{{ $mosque->name }}">
									<span class="type-badge {{ (strtoupper($mosque->type ?? '') === 'MUSHOLLA') ? 'badge-mushalla' : 'badge-masjid' }}">{{ strtoupper($mosque->type ?? 'MASJID') }}</span>
									<div class="card-body">
										<div class="d-flex justify-content-between align-items-start mb-1">
											<h6 class="card-title mb-0">{{ $mosque->name }}</h6>
											<div class="text-end small text-muted">{{ $mosque->witel->name ?? $mosque->city->name ?? $mosque->province->name ?? '' }}</div>
										</div>
										<div class="d-flex justify-content-between align-items-start mb-2">
											<div class="text-muted small">{!! nl2br(e($mosque->address)) !!}</div>
											<div class="text-end small text-muted ms-2">{{ $mosque->sto->name ?? '' }}</div>
										</div>
										@if(isset($mosque->completion_percentage))
										<div class="d-flex justify-content-between align-items-center mb-1">
											<div class="small text-danger">Kelengkapan</div>
											<div class="small text-muted">{{ $mosque->completion_percentage }}%</div>
										</div>
										<div class="progress" style="height:6px;">
											<div class="progress-bar bg-danger" role="progressbar" style="width: {{ $mosque->completion_percentage }}%;" aria-valuenow="{{ $mosque->completion_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
										@endif
									</div>
								</div>
							</a>
						</div>
					@empty
						<div class="col-12">
							<p class="text-muted">Tidak ada data masjid.</p>
						</div>
					@endforelse
					</div>
				</div>
				<div class="mt-4">
					{{ $mosques->links() }}
				</div>
				
				</div>
			</main>
		</div>
	</section>
	<x-home._footer />
</x-home.layout>
<script>
document.addEventListener('DOMContentLoaded', function () {
	const provinceSel = document.querySelector('select[name=province_id]');
	const witelSel = document.querySelector('select[name=witel_id]');
	const stoSel = document.querySelector('select[name=sto_id]');

	function emptySelect(sel, placeholder) {
		sel.innerHTML = '';
		const opt = document.createElement('option');
		opt.value = '';
		opt.textContent = placeholder || 'Pilih...';
		sel.appendChild(opt);
	}

	async function fetchChildren(parentId, level) {
		if (!parentId) return [];
		try {
				const base = '{{ route("admin.regions.children") }}';
				const url = new URL(base, window.location.origin);
				url.searchParams.set('parent_id', parentId);
			if (level) url.searchParams.set('level', level);
			const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
			if (!res.ok) return [];
			const data = await res.json();
			return Array.isArray(data) ? data : [];
		} catch (e) {
			return [];
		}
	}

	async function onProvinceChange() {
		const pid = provinceSel.value;
		emptySelect(witelSel, 'Semua Witel');
		if (!pid) return;
		// Fetch direct children for witel level
		const witels = await fetchChildren(pid, 'WITEL');
		if (Array.isArray(witels) && witels.length) {
			witels.forEach(w => {
				const o = document.createElement('option'); o.value = w.id; o.textContent = w.name; witelSel.appendChild(o);
			});
		}
	}

	async function onWitelChange() {
		if (!stoSel) return;
		const wid = witelSel.value;
		emptySelect(stoSel, 'Semua STO');
		if (!wid) return;
		const stos = await fetchChildren(wid, 'STO');
		if (Array.isArray(stos) && stos.length) {
			stos.forEach(s => {
				const o = document.createElement('option'); o.value = s.id; o.textContent = s.name; stoSel.appendChild(o);
			});
		}
	}

	if (provinceSel) {
		provinceSel.addEventListener('change', onProvinceChange);
		// If there is an initial province selected (from query), trigger load and select existing city/witel
		const initialProvince = provinceSel.value;
		if (initialProvince) {
			// capture currently selected witel and sto to reselect after load
			const selWitel = witelSel.getAttribute('data-selected') || '{{ request()->query('witel_id') }}';
			const selSto = stoSel ? (stoSel.getAttribute('data-selected') || '{{ request()->query('sto_id') }}') : null;
			// Use the children endpoint to populate selects so the options match selected province
			onProvinceChange().then(async () => {
				if (selWitel) { witelSel.value = selWitel; }
				// populate STOs after witel is set
				if (selWitel && stoSel) {
					await onWitelChange();
					if (selSto) { stoSel.value = selSto; }
				}
			});
		}

		// Wire witel change to populate STOs
		if (witelSel) {
			witelSel.addEventListener('change', onWitelChange);
		}
	}
});
</script>
<style>
/* Scroll wrapper for mosque cards: will be sized by JS to show up to 9 cards */
.mosque-scroll-wrapper { overflow-y: auto; }
.mosque-card { min-height: 260px; }
.type-badge {
	position: absolute;
	left: 8px;
	top: 8px;
	background: #dc2626; /* default red - mushalla fallback */
	color: white;
	padding: 0.18rem 0.5rem;
	font-size: 0.65rem;
	font-weight: 600;
	border-radius: 0.25rem;
	z-index: 10;
}
.badge-masjid { background: #dc2626; color: #ffffff; }
.badge-mushalla {
	background: #ffffff; /* white */
	color: #dc2626; /* red text */
	border: 1px solid #dc2626;
	padding: 0.18rem 0.6rem;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
	function sizeMosqueWrapper() {
		const wrapper = document.querySelector('.mosque-scroll-wrapper');
		const card = document.querySelector('.mosque-card');
		if (!wrapper) return;
		// Determine approximate card height (fallback to 320px)
		const cardHeight = card ? card.getBoundingClientRect().height : 320;
		// We want to show at most 9 cards -> compute rows: 3 cards per row (col-lg-4), so 3 rows
		const rowsToShow = 3; // 3 rows x 3 columns = 9 cards
		const gap = 16; // bootstrap g-4 gap is 1.5rem ~24px, but use 16 as conservative
		const desired = (cardHeight * rowsToShow) + (gap * (rowsToShow - 1));
		wrapper.style.maxHeight = desired + 'px';
	}
	// Add mosque-card class to each card element
	document.querySelectorAll('.col-md-6.col-lg-4 > .card').forEach(c => c.classList.add('mosque-card'));
	sizeMosqueWrapper();
	window.addEventListener('resize', sizeMosqueWrapper);
});
</script>

