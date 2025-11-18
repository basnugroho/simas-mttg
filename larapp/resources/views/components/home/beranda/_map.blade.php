<section class="map-section mt-3">
	<div class="container">
			<div id="map-wrapper" class="map-wrapper split">
				<div class="map-left" style="flex:0 0 30%;">
					<div class="map-filter-card" id="mapFilterCard">
						<div class="map-filter-header" style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:8px">
							<h6 style="margin:0">Filter Data</h6>
							<div style="display:flex;gap:6px;align-items:center">
								<button id="mapFilterToggle" type="button" class="btn btn-sm" style="background:transparent;border:0;cursor:pointer">☰</button>
								<button id="mapFilterReset" type="button" class="btn btn-sm" style="background:transparent;border:0;cursor:pointer">⟲</button>
							</div>
						</div>
						<form id="mapFilterForm" class="d-flex flex-column gap-3">
							<div>
								<label for="mfProvince">Provinsi</label>
								<select id="mfProvince" class="form-select" data-placeholder="Pilih Provinsi"></select>
							</div>
							<div>
								<label for="mfCity">Kota / Kabupaten</label>
								<select id="mfCity" class="form-select disabled-select" disabled data-placeholder="Pilih Kota / Kabupaten"></select>
							</div>
							<div>
								<label for="mfWitel">Witel</label>
								<select id="mfWitel" class="form-select disabled-select" disabled data-placeholder="Pilih Witel"></select>
							</div>
							<div>
								<label for="mfType">Jenis</label>
								<select id="mfType" class="form-select">
									<option value="MASJID">Masjid</option>
									<option value="MUSHOLLA">Musholla</option>
								</select>
							</div>
							<div>
								<label for="mfQuery">Cari</label>
								<input id="mfQuery" type="text" class="form-control" placeholder="Cari nama / lokasi..." />
							</div>
							<div>
								<button type="submit" class="btn btn-search-map">Cari</button>
							</div>
						</form>
					</div>
				</div>
				<div class="map-right" style="flex:1 1 70%; position:relative;">
					<div id="mapStatus" style="position:absolute;top:10px;right:10px;z-index:11;background:rgba(255,255,255,.9);padding:6px 10px;border-radius:6px;font-size:.65rem;letter-spacing:.05em;font-weight:600;color:#555;display:none"></div>
					<div id="mainMap"></div>
				</div>
			</div>
	</div>
</section>
