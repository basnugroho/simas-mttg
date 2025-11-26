<div id="gallery" class="mt-4 pt-3 mosque-section">
			{{-- Grid gallery with modal preview --}}
				<style>
					.grid-gallery-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; }
					.grid-item { position:relative; overflow:hidden; border-radius:8px; }
					.grid-item img { width:100%; height:220px; object-fit:cover; display:block; transition:transform .25s, filter .25s; }
					.grid-item .caption { position:absolute; left:0; right:0; bottom:0; padding:0.75rem 1rem; color:#fff; background:linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.45) 40%, rgba(0,0,0,0.6) 100%); font-size:0.95rem; }
					.grid-item:focus-within img, .grid-item:hover img { transform:scale(1.03); }
					@media (max-width:992px){ .grid-gallery-grid { grid-template-columns:repeat(2,1fr); } }
					@media (max-width:576px){ .grid-gallery-grid { grid-template-columns:repeat(1,1fr); } .grid-item img { height:180px; } }
					.modal-gallery-img { width:100%; height:auto; max-height:80vh; object-fit:contain; }
				</style>

				<div class="grid-gallery-grid gallery-thumbs">
					@foreach($images as $idx => $img)
						<div class="grid-item">
							<button type="button" class="btn p-0 border-0 w-100 h-100 open-modal" data-src="{{ $img }}" data-title="Foto {{ $idx + 1 }}" aria-label="Open image {{ $idx + 1 }}">
								<img src="{{ $img }}" alt="Foto {{ $idx + 1 }}" />
								<div class="caption">Foto {{ $idx + 1 }}</div>
							</button>
						</div>
					@endforeach
				</div>

				<!-- Modal -->
				<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered modal-lg">
						<div class="modal-content bg-transparent border-0">
							<div class="modal-body p-0">
								<button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
								<div class="d-flex justify-content-center align-items-center p-3">
									<img src="" alt="" class="modal-gallery-img" />
								</div>
								<div class="px-3 pb-3 text-white"><small class="modal-caption"></small></div>
							</div>
						</div>
					</div>
				</div>

				<script>
					(function(){
												// reuse facility-style lightbox: build carousel inside modal when needed
												const facilityModalId = 'galleryLightbox';
												// create modal markup if not present
												let facilityModal = document.getElementById(facilityModalId);
												if(!facilityModal){
														const tpl = `
<div class="modal fade" id="${facilityModalId}" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-xl">
		<div class="modal-content bg-transparent border-0">
			<div class="modal-body p-0">
				<div id="${facilityModalId}Carousel" class="carousel slide" data-bs-ride="false">
					<div class="carousel-inner" id="${facilityModalId}CarouselInner"></div>
					<button class="carousel-control-prev" type="button" data-bs-target="#${facilityModalId}Carousel" data-bs-slide="prev">
						<span class="carousel-control-prev-icon" aria-hidden="true"></span>
						<span class="visually-hidden">Previous</span>
					</button>
					<button class="carousel-control-next" type="button" data-bs-target="#${facilityModalId}Carousel" data-bs-slide="next">
						<span class="carousel-control-next-icon" aria-hidden="true"></span>
						<span class="visually-hidden">Next</span>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>`;
														document.body.insertAdjacentHTML('beforeend', tpl);
														facilityModal = document.getElementById(facilityModalId);
												}

												// collect image srcs from this gallery
												const galleryEl = document.querySelector('.grid-gallery-grid.gallery-thumbs');
												let galleryImgs = [];
												if(galleryEl){
														galleryImgs = Array.from(galleryEl.querySelectorAll('img')).map(i => i.getAttribute('src'));
														galleryEl.dataset.facilityImages = JSON.stringify(galleryImgs);
												}

												function openGalleryCarousel(imgs, startIndex){
														const inner = document.getElementById(facilityModalId+'CarouselInner');
														inner.innerHTML = '';
														imgs.forEach(function(src, idx){
																const item = document.createElement('div');
																item.className = 'carousel-item' + (idx === startIndex ? ' active' : '');
																const img = document.createElement('img');
																img.src = src;
																img.className = 'd-block w-100 rounded';
																img.style.maxHeight = '80vh';
																img.style.objectFit = 'contain';
																item.appendChild(img);
																inner.appendChild(item);
														});
														const modalEl = document.getElementById(facilityModalId);
														// preserve scroll position
														const scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
														const modal = new bootstrap.Modal(modalEl, { focus: false });
														modal.show();
														// restore scroll after modal shown to counter browser auto-scroll
														modalEl.addEventListener('shown.bs.modal', function handler(){
															window.scrollTo(0, scrollTop);
															// remove handler after first call
															modalEl.removeEventListener('shown.bs.modal', handler);
														});
														const carousel = bootstrap.Carousel.getOrCreateInstance(document.getElementById(facilityModalId+'Carousel'));
														carousel.to(startIndex);
												}

												// wire up grid thumbnails
												document.querySelectorAll('.grid-gallery-grid.gallery-thumbs .grid-item img').forEach(function(img){
														img.style.cursor = 'pointer';
														img.addEventListener('click', function(e){
															// stop other handlers and default that might cause scrolling
															e.preventDefault();
															e.stopPropagation();
															if (e.stopImmediatePropagation) e.stopImmediatePropagation();
															const gallery = e.target.closest('.gallery-thumbs');
															const imgs = JSON.parse(gallery.dataset.facilityImages || '[]');
															const clickedSrc = e.target.getAttribute('src');
															let startIndex = imgs.indexOf(clickedSrc);
															if(startIndex < 0) startIndex = 0;
															// store current scroll and force restore immediately and after modal shown
															const scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
															openGalleryCarousel(imgs, startIndex);
															// force restore if browser scrolled
															window.scrollTo(0, scrollTop);
															setTimeout(()=>{ window.scrollTo(0, scrollTop); }, 50);
														});
												});
					})();
				</script>	
</div>
