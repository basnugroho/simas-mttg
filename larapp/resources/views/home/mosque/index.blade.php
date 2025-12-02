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
								<label class="form-label small">Regional</label>
								<select name="regional_id" class="form-select">
									<option value="">Semua Regional</option>
									@foreach($regionals ?? collect() as $r)
										<option value="{{ $r->id }}" {{ request()->query('regional_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
									@endforeach
								</select>
							</div>
						<div class="mb-2">
								<label class="form-label small">Area</label>
							<select name="area_id" class="form-select" data-selected="{{ request()->query('area_id') ?? request()->query('province_id') }}">
								<option value="">Semua Area</option>
								@foreach($provinces ?? collect() as $p)
									<option value="{{ $p->id }}" {{ (request()->query('area_id') ?? request()->query('province_id')) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
								@endforeach
							</select>
						</div>
						<!-- Kota / Kabupaten removed per request -->
						<div class="mb-2">
							<label class="form-label small">Witel</label>
							<select name="witel_id" class="form-select" data-selected="{{ request()->query('witel_id') ?? request()->query('witel_id') }}">
								<option value="">Semua Witel</option>
								@foreach($witels ?? collect() as $w)
									<option value="{{ $w->id }}" {{ request()->query('witel_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="mb-2">
							<label class="form-label small">STO</label>
							<select name="sto_id" class="form-select" data-selected="{{ request()->query('sto_id')  }}">
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
					<div class="card p-3 shadow-sm mt-3">
						<h6 class="mb-2">Unduh Data</h6>
						<p class="small text-muted mb-3">Unduh data masjid atau mushalla dalam format CSV.</p>
						<div class="d-grid gap-2">
							<a href="{{ route('admin.mosques.export', ['type' => 'MASJID']) }}" class="btn btn-sm" style="background-color:#dc2626;color:#ffffff;">
								<i class="bi bi-download"></i> Unduh Masjid
							</a>
							<a href="{{ route('admin.mosques.export', ['type' => 'MUSHOLLA']) }}" class="btn btn-sm" style="background-color:#ffffff;color:#dc2626;border:1px solid #dc2626;">
								<i class="bi bi-download"></i> Unduh Mushalla
							</a>
						</div>
					</div>
			</aside>
			<main class="col-md-9">
				<div class="mosque-scroll-wrapper">
					<div class="row g-4">
					@forelse($mosques as $mosque)
						@php $img = $mosque->db_image_url ?? asset('images/mosque-1.jpg'); @endphp
						<div class="col-md-6 col-lg-4">
							<a href="{{ route('mosque.show', $mosque->id) }}" class="text-decoration-none text-dark">
								<div class="card h-100 shadow-sm position-relative">
									<img src="{{ $img }}" onerror="this.onerror=null;this.src='{{ asset('images/mosque-1.jpg') }}'" class="card-img-top" style="height:150px; object-fit:cover;" alt="{{ $mosque->name }}">
									<span class="type-badge {{ (strtoupper($mosque->type ?? '') === 'MUSHOLLA') ? 'badge-mushalla' : 'badge-masjid' }}">{{ strtoupper($mosque->type ?? 'MASJID') }}</span>
									<div class="card-body">
										<div class="d-flex justify-content-between align-items-start mb-1">
											<h6 class="card-title mb-0">{{ $mosque->name }}</h6>
											<div class="text-end small text-muted">{{ $mosque->witel->name ?? $mosque->city->name ?? $mosque->province->name ?? '' }}</div>
										</div>
										<div class="d-flex justify-content-between align-items-start mb-2">
											<div class="text-muted small">{!! nl2br(e(\Illuminate\Support\Str::limit($mosque->address, 21))) !!}</div>
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
					<div class="row align-items-center">
						<div class="col-12 d-flex justify-content-center">
							{{ $mosques->onEachSide(1)->links() }}
						</div>
					</div>
				</div>
				
				</div>
			</main>
		</div>
	</section>
	<x-home._footer />
</x-home.layout>
<script>
document.addEventListener('DOMContentLoaded', function () {
	const regionalSel = document.querySelector('select[name=regional_id]');
	const provinceSel = document.querySelector('select[name=area_id]');
	const witelSel = document.querySelector('select[name=witel_id]');
	const stoSel = document.querySelector('select[name=sto_id]');
	const typeSel = document.querySelector('select[name=type]');
	const facilitySel = document.querySelector('select[name=facility_id]');

	// Sync data-selected attribute (used by some UI helpers) with current value after user changes
	function syncDataSelected(sel) {
		if (!sel) return;
		sel.setAttribute('data-selected', sel.value);
	}
	[witelSel, stoSel, provinceSel, regionalSel].forEach(sel => {
		if (!sel) return;
		sel.addEventListener('change', () => syncDataSelected(sel));
	});

	function updateLabel(select) {
		if (!select) return;
		const wrapper = select.closest('.mb-2');
		if (!wrapper) return;
		const label = wrapper.querySelector('label.form-label');
		if (!label) return;
		if (!label.dataset.original) {
			label.dataset.original = label.textContent.trim();
		}
		if (select.value) {
			const optText = (select.selectedOptions[0]?.textContent || '').trim();
			label.textContent = label.dataset.original + ': ' + optText;
		} else {
			label.textContent = label.dataset.original;
		}
	}

	function bindDynamicLabel(select) {
		if (!select) return;
		updateLabel(select);
		select.addEventListener('change', () => updateLabel(select));
	}

	[regionalSel, provinceSel, witelSel, stoSel, typeSel, facilitySel].forEach(bindDynamicLabel);

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

	async function onRegionalChange() {
		const rid = regionalSel.value;
		// Reset downstream selects
		emptySelect(provinceSel, 'Semua Area');
		emptySelect(witelSel, 'Semua Witel');
		if (stoSel) emptySelect(stoSel, 'Semua STO');
		if (!rid) return;
		const areas = await fetchChildren(rid, 'AREA');
		if (Array.isArray(areas) && areas.length) {
			areas.forEach(a => {
				const o = document.createElement('option'); o.value = a.id; o.textContent = a.name; provinceSel.appendChild(o);
			});
		}
	}

	async function onProvinceChange() {
		const pid = provinceSel.value;
		const currentSelectedWitel = witelSel.getAttribute('data-selected');
		emptySelect(witelSel, 'Semua Witel');
		if (!pid) {
			// If no province selected, try to keep previously selected witel visible
			if (currentSelectedWitel) {
				const opt = document.createElement('option');
				opt.value = currentSelectedWitel;
				opt.textContent = 'Terpilih';
				witelSel.appendChild(opt);
				witelSel.value = currentSelectedWitel;
				updateLabel(witelSel);
			}
			return;
		}
		// Fetch direct children for witel level
		const witels = await fetchChildren(pid, 'WITEL');
		if (Array.isArray(witels) && witels.length) {
			let hasSelected = false;
			witels.forEach(w => {
				const o = document.createElement('option'); o.value = w.id; o.textContent = w.name; witelSel.appendChild(o);
				if (String(w.id) === String(currentSelectedWitel)) { hasSelected = true; }
			});
			// Reselect previously chosen witel if it exists in the new list
			if (currentSelectedWitel) {
				if (hasSelected) {
					witelSel.value = currentSelectedWitel;
				} else {
					// If previous selection does not belong to this province, append it to preserve user context
					const o = document.createElement('option');
					o.value = currentSelectedWitel;
					o.textContent = 'Terpilih';
					witelSel.appendChild(o);
					witelSel.value = currentSelectedWitel;
				}
				updateLabel(witelSel);
			}
		}
	}

	async function onWitelChange() {
		if (!stoSel) return;
		const wid = witelSel.value;
		const currentSelectedSto = stoSel.getAttribute('data-selected');
		emptySelect(stoSel, 'Semua STO');
		if (!wid) {
			// No witel selected: keep previously chosen STO visible if any
			if (currentSelectedSto) {
				const opt = document.createElement('option');
				opt.value = currentSelectedSto;
				opt.textContent = 'Terpilih';
				stoSel.appendChild(opt);
				stoSel.value = currentSelectedSto;
				updateLabel(stoSel);
			}
			return;
		}
		const stos = await fetchChildren(wid, 'STO');
		if (Array.isArray(stos) && stos.length) {
			let hasSelected = false;
			stos.forEach(s => {
				const o = document.createElement('option'); o.value = s.id; o.textContent = s.name; stoSel.appendChild(o);
				if (String(s.id) === String(currentSelectedSto)) { hasSelected = true; }
			});
			// Reselect previously chosen STO if present; otherwise, append it to preserve context
			if (currentSelectedSto) {
				if (hasSelected) {
					stoSel.value = currentSelectedSto;
				} else {
					const o = document.createElement('option');
					o.value = currentSelectedSto;
					o.textContent = 'Terpilih';
					stoSel.appendChild(o);
					stoSel.value = currentSelectedSto;
				}
				updateLabel(stoSel);
			}
		}
	}

	if (regionalSel) {
		regionalSel.addEventListener('change', onRegionalChange);
		const initialRegional = regionalSel.value;
		if (initialRegional && provinceSel) {
			const selArea = provinceSel.getAttribute('data-selected') || '{{ request()->query('area_id') ?? request()->query('province_id') }}';
			onRegionalChange().then(async () => {
				if (selArea) { provinceSel.value = selArea; }
				if (selArea) { await onProvinceChange(); }
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

	// Initial restoration when Area/Witel are empty but URL has selections
	(function restoreInitialSelections(){
		const selWitel = witelSel ? witelSel.getAttribute('data-selected') : null;
		const selSto = stoSel ? stoSel.getAttribute('data-selected') : null;
		// If no area selected but we have a witel from query, ensure it's visible
		if (provinceSel && !provinceSel.value && selWitel) {
			onProvinceChange().then(async () => {
				// Province change will insert the selected Witel as 'Terpilih' when none
				witelSel.value = selWitel;
				updateLabel(witelSel);
				if (stoSel) {
					await onWitelChange();
					if (selSto) {
						stoSel.value = selSto;
						updateLabel(stoSel);
					}
				}
			});
		} else if (witelSel && !witelSel.value && selWitel) {
			// If witel select is empty but we have selection, ensure it's added
			onProvinceChange().then(() => {
				witelSel.value = selWitel;
				updateLabel(witelSel);
				if (stoSel) {
					onWitelChange().then(() => {
						if (selSto) { stoSel.value = selSto; updateLabel(stoSel); }
					});
				}
			});
		} else if (stoSel && !stoSel.value && selSto) {
			// As a last resort, if STO is empty but we have selection, ensure it's added
			onWitelChange().then(() => {
				stoSel.value = selSto;
				updateLabel(stoSel);
			});
		}
	})();
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
<style>
/* Keep pagination summary on a single line */
.pagination-summary { white-space: nowrap; }
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

