<div id="gallery" class="mt-4 pt-3 mosque-section">
	<div class="card card-sm mb-3">
		<div class="card-body">
			@php
				$images = [];
				// prefer related photos from the mosque model
				if(isset($mosque) && $mosque->relationLoaded('photos') && $mosque->photos->count()){
					foreach($mosque->photos as $p){
						$images[] = $p->url ?? ($p->path ? asset($p->path) : null);
					}
				}
				// fallback to public images if no photos
				if(empty($images)){
					for($i=1;$i<=5;$i++){
						$images[] = asset('images/mosque-'.$i.'.jpg');
					}
				}
			@endphp
			{{-- Inline carousel built from gallery images --}}
			<div class="grid-gallery">
				<div class="grid-gallery-main bd-example">
					<div id="carouselExampleFade" class="carousel slide carousel-fade mb-3" data-bs-ride="false">
						<div class="carousel-inner">
							@foreach($images as $idx => $img)
								<div class="carousel-item @if($idx==0) active @endif">
									<img src="{{ $img }}" class="d-block w-50 gallery-main-img" alt="slide{{ $idx }}">
									<div class="carousel-caption d-none d-md-block caption-caption">
										<h5>Foto {{ $idx + 1 }}</h5>
										<p class="small text-light">Keterangan singkat untuk foto {{ $idx + 1 }}</p>
									</div>
								</div>
							@endforeach
						</div>
						<button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							<span class="visually-hidden">Previous</span>
						</button>
						<button class="carousel-control-next" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="visually-hidden">Next</span>
						</button>
					</div>
				</div>
				<div class="grid-gallery-thumbs">
					@foreach($images as $idx => $img)
						<button type="button" class="grid-thumb" data-index="{{ $idx }}" aria-label="Thumbnail {{ $idx + 1 }}">
							<img src="{{ $img }}" alt="thumb{{ $idx }}" />
						</button>
					@endforeach
				</div>
			</div>
			<script>
				(function(){
					const gridThumbs = document.querySelectorAll('.grid-gallery-thumbs .grid-thumb');
					const carouselEl = document.getElementById('carouselExampleFade');
					const bs = carouselEl ? bootstrap.Carousel.getOrCreateInstance(carouselEl) : null;

					gridThumbs.forEach((btn, i) => {
						btn.addEventListener('click', () => {
							if(bs){ bs.to(i); }
							gridThumbs.forEach(b=>b.classList.remove('active'));
							btn.classList.add('active');
						});
					});

					if(carouselEl){
						carouselEl.addEventListener('slid.bs.carousel', function(e){
							const idx = e.to || 0;
							gridThumbs.forEach((b,i)=>b.classList.toggle('active', i===idx));
						});
					}
				})();
			</script>
			<script>
				(function(){
					const carouselEl = document.getElementById('carouselExampleFade');
					const indicatorButtons = document.querySelectorAll('.thumbs-indicators button');
					if(carouselEl){
						carouselEl.addEventListener('slid.bs.carousel', function(e){
							const activeIndex = e.to || 0;
							indicatorButtons.forEach((btn, i) => {
								btn.classList.toggle('active', i === activeIndex);
							});
						});
					}
					indicatorButtons.forEach((btn, i)=>{
						btn.addEventListener('click', ()=>{
							indicatorButtons.forEach(b=>b.classList.remove('active'));
							btn.classList.add('active');
						});
					});
				})();
			</script>


		</div>
	</div>
</div>
