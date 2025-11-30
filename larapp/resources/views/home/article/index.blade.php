<x-home.layout :title="'Simas MTTG - List Article'">
	<x-home._navbar />
 	<x-home.beranda._hero>
        <h3 class="hero-title display-6 mb-2">Article Masjid Telkom Regional 3</h3>
			<p class="mb-4 fw-medium" style="font-size:0.95rem">Telkom Regional 3 (Jawa Timur, Bali dan Nusa Tenggara)</p>
    </x-home._hero>
	<section class="container my-5 mt-4">
		<div class="row">
			<aside class="col-md-3 mb-4">
				<div class="card p-3 shadow-sm">
					<h5 class="mb-3">Filter</h5>
					<form method="GET" action="{{ route('article') }}">
						<div class="mb-3">
							<label class="form-label small">Kategori</label>
							<select name="category_id" class="form-select">
								<option value="">Semua Kategori</option>
								@foreach(($categories ?? collect()) as $c)
									<option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="d-flex gap-2">
							<a href="{{ route('article') }}" class="btn btn-light w-50">Reset</a>
							<button type="submit" class="btn btn-success w-50">Cari</button>
						</div>
					</form>
				</div>
			</aside>
			<main class="col-md-9">
				@if(($articles ?? collect())->count() === 0)
					<div class="alert alert-light border shadow-sm">Belum ada artikel yang dapat ditampilkan.</div>
				@else
					<div class="mosque-scroll-wrapper">
						<div class="row g-3">
							@foreach($articles as $article)
								<div class="col-md-6 col-lg-4">
									<div class="card h-100 shadow-sm position-relative">
										@if($article->image_url)
											<img src="{{ $article->image_url }}" class="card-img-top" alt="{{ $article->title }}" style="object-fit:cover;height:180px;">
										@else
											<div class="bg-light d-flex align-items-center justify-content-center" style="height:180px;">
												<span class="text-muted small">Tidak ada gambar</span>
											</div>
										@endif
										<div class="card-body d-flex flex-column">
											@if($article->category)
												<span class="badge bg-success mb-2">{{ $article->category->name }}</span>
											@endif
											<h6 class="card-title mb-2" style="min-height:3rem;overflow:hidden;">{{ Str::limit($article->title, 70) }}</h6>
											@if($article->mosque)
												<p class="mb-1 small text-muted">{{ $article->mosque->name }}</p>
											@endif
											@if($article->published_at)
												<p class="mb-2 small text-muted">Dipublikasikan {{ $article->published_at->format('d M Y') }}</p>
											@endif
											<p class="card-text small flex-grow-1">{{ Str::limit(strip_tags($article->summary ?? $article->content), 110) }}</p>
											<a href="{{ route('article.show', $article->id) }}" class="mt-2 btn btn-outline-success btn-sm align-self-start">Baca selengkapnya</a>
										</div>
									</div>
								</div>
							@endforeach
							</div>
						</div>
						<div class="mt-3 d-flex justify-content-center">
							{{ $articles->links() }}
						</div>
				@endif
			</main>
		</div>
	</section>
	<x-home._footer />
</x-home.layout>
<!-- Location JS removed: filter only by category -->
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

