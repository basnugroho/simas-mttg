
<style>
/* Responsive adjustments for prayer bar */
.prayer-bar-title{ font-size:1rem; font-weight:600; min-width:0; }
.prayer-bar-title.truncate{ white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
@media(min-width:768px){ .prayer-bar-title{ font-size:1.05rem; } }
</style>

	<div class="container mt-2">
		<div class="row justify-content-center">
			<div class="col-12 col-lg-12">
				<div class="prayer-card" data-city-id="1638">
					<div class="prayer-card-body">
						<div class="d-flex align-items-center justify-content-center mb-2">
							<div class="d-flex align-items-center gap-2 flex-wrap w-100 justify-content-center">
								<div class="prayer-bar-title truncate me-2 mb-1 mb-sm-0 text-center text-sm-start flex-shrink-1">Jadwal Shalat</div>
								<div class="flex-grow-1" style="min-width:0; max-width:420px; position:relative;">
									<input id="prayer-city-search" type="search" class="form-control form-control-sm" placeholder="Cari kota..." style="width:100%; min-width:0;" autocomplete="off" />
									<div id="prayer-city-suggestions" class="list-group position-absolute" style="z-index:9999; width:100%; display:none; max-height:170px; overflow:auto;"></div>
								</div>
								<div class="ms-2"><span id="pt-source" class="prayer-source-badge fallback" title="Sumber data">...</span></div>
							</div>
						</div>
						<div class="row justify-content-center g-4 text-center" id="prayerTimesRow">
							<div class="col-6 col-md-auto"><div class="time-item">Subuh <small id="pt-subuh">...</small></div></div>
							<div class="col-6 col-md-auto"><div class="time-item">Dzuhur <small id="pt-dzuhur">...</small></div></div>
							<div class="col-6 col-md-auto"><div class="time-item">Ashar <small id="pt-ashar">...</small></div></div>
							<div class="col-6 col-md-auto"><div class="time-item">Maghrib <small id="pt-maghrib">...</small></div></div>
							<div class="col-6 col-md-auto"><div class="time-item">Isya <small id="pt-isya">...</small></div></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<!-- JS moved to public/js/beranda.js -->

