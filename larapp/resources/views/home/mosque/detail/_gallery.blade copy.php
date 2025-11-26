<div id="gallery" class="mt-4 pt-3 mosque-section">
	<div class="card card-sm mb-3">
		<div class="card-body">
			@php
				// Controller may pass an $images array composed of mosque.cover, mosque photos, and facility photos
				if(!isset($images) || !is_array($images)){
					$images = [];
					// prefer related photos from the mosque model
					if(isset($mosque) && $mosque->relationLoaded('photos') && $mosque->photos->count()){
						foreach($mosque->photos as $p){
							if(!empty($p->url)){
								$images[] = $p->url;
							} elseif(!empty($p->path)) {
								$images[] = Storage::url($p->path);
							}
						}
					}
					// fallback to public images if no photos
					if(empty($images)){
						for($i=1;$i<=5;$i++){
							$images[] = asset('images/mosque-'.$i.'.jpg');
						}
					}
				}
				// ensure array is filtered and reindexed
				$images = array_values(array_filter($images));
			@endphp
			{{-- Inline carousel built from gallery images --}}
				<style>
					.grid-gallery { display:flex; flex-direction:column; gap:0.5rem; }
					/* make the carousel area span full card width */
					.grid-gallery-main.bd-example { width:100%; }
					.grid-gallery-main .gallery-main-img { width:100%; max-height:520px; object-fit:cover; border-radius:4px; }
					/* thumbs: single-row, no-wrap, horizontal scroll */
					.grid-gallery-thumbs { display:flex; gap:0.5rem; overflow-x:auto; padding:0.25rem 0; -webkit-overflow-scrolling:touch; white-space:nowrap; }
					.grid-gallery-thumbs::-webkit-scrollbar { height:8px; }
					.grid-gallery-thumbs { scrollbar-width:thin; }
					.grid-gallery-thumbs .grid-thumb { border:0; background:transparent; padding:0; margin:0; flex:0 0 auto; display:inline-block; }
					.grid-gallery-thumbs .grid-thumb img { display:block; width:140px; height:90px; object-fit:cover; border-radius:6px; opacity:0.85; transition:opacity .15s, transform .15s; }
					.grid-gallery-thumbs .grid-thumb.active img { opacity:1; transform:scale(1.03); box-shadow:0 2px 6px rgba(0,0,0,0.15); }
					.grid-gallery-thumbs .grid-thumb:focus { outline:2px solid rgba(59,130,246,0.5); outline-offset:2px; }
					@media (max-width:576px){
						.grid-gallery-thumbs .grid-thumb img { width:100px; height:70px; }
					}
				</style>
				<div class="grid-gallery">
					<div class="grid-gallery-main bd-example">
					<div id="carouselExampleFade" class="carousel slide carousel-fade mb-3" data-bs-ride="false">
						<div class="carousel-inner">
							@foreach($images as $idx => $img)
								<div class="carousel-item @if($idx==0) active @endif">
									<img src="{{ $img }}" class="d-block w-100 gallery-main-img" alt="slide{{ $idx }}">
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
				<div class="grid-gallery-thumbs" role="tablist" aria-label="Gallery thumbnails">
					@foreach($images as $idx => $img)
						<button type="button" class="grid-thumb" data-index="{{ $idx }}" aria-label="Thumbnail {{ $idx + 1 }}">
							<img src="{{ $img }}" alt="thumb{{ $idx }}" />
						</button>
					@endforeach
				</div>
			</div>
			<script>
				(function(){
					const gridThumbs = Array.from(document.querySelectorAll('.grid-gallery-thumbs .grid-thumb'));
					const carouselEl = document.getElementById('carouselExampleFade');
					const bs = carouselEl ? bootstrap.Carousel.getOrCreateInstance(carouselEl) : null;
					const thumbsContainer = document.querySelector('.grid-gallery-thumbs');

					function setActive(index){
						gridThumbs.forEach((b,i)=>b.classList.toggle('active', i===index));
						const active = gridThumbs[index];
						if(active && thumbsContainer){
							const containerRect = thumbsContainer.getBoundingClientRect();
							const activeRect = active.getBoundingClientRect();
							if(activeRect.left < containerRect.left || activeRect.right > containerRect.right){
								const offset = active.offsetLeft - (thumbsContainer.clientWidth/2) + (active.clientWidth/2);
								thumbsContainer.scrollTo({ left: offset, behavior: 'smooth' });
							}
						}
					}

					gridThumbs.forEach((btn, i) => {
						btn.addEventListener('click', () => {
							if(bs){ bs.to(i); }
							setActive(i);
						});
					});

					if(carouselEl){
						carouselEl.addEventListener('slid.bs.carousel', function(e){
							const idx = e.to || 0;
							setActive(idx);
						});
					}

					// initialize
					setTimeout(()=>{ setActive(0); }, 50);
				})();
			</script>


		</div>
	</div>
</div>
