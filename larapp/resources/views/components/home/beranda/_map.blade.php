<div class="container mt-3">
	<div class="map-wrapper-full">
		<style>
			/* Default: overlay absolute top-left */
			.map-overlay-controls{position:absolute;top:12px;left:12px;z-index:1100;background:rgba(255,255,255,0.95);padding:12px;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,0.12);max-width:360px}
			.map-overlay-controls .map-filter-form{display:flex;flex-direction:column;gap:8px}
			/* On small screens: make overlay a horizontal, full-width bar above the map */
			@media (max-width:768px){
				.map-overlay-controls{position:relative;top:auto;left:auto;right:auto;width:100%;max-width:100%;border-radius:8px;margin-bottom:8px;padding:8px 10px;display:flex;flex-wrap:wrap;align-items:center;gap:8px}
				.map-overlay-controls .map-filter-form{flex-direction:row;gap:8px;width:100%;align-items:center}
				.map-overlay-controls .form-group{margin:0}
			}
		</style>

		<div class="map-overlay-controls" id="mapFilterCard">
			<div class="map-filter-header d-flex w-100 justify-content-between align-items-center" style="gap:8px;margin-bottom:8px;">
				<h6 style="margin:0">Filter Data</h6>
				<div>
					<button id="mapFilterReset" type="button" class="btn btn-sm btn-reset">⟲</button>
				</div>
			</div>
			<form id="mapFilterForm" class="map-filter-form w-100">
				<div class="form-group">
					<label for="mfProvince" class="d-none d-md-block">Provinsi</label>
					<select id="mfProvince" class="form-select" data-placeholder="Pilih Provinsi"></select>
				</div>
				<div class="form-group">
					<label for="mfCity" class="d-none d-md-block">Kota / Kabupaten</label>
					<select id="mfCity" class="form-select disabled-select" disabled data-placeholder="Pilih Kota / Kabupaten"></select>
				</div>
				<div class="form-group">
					<label for="mfWitel" class="d-none d-md-block">Witel</label>
					<select id="mfWitel" class="form-select disabled-select" disabled data-placeholder="Pilih Witel"></select>
				</div>
				<div class="form-group">
					<label for="mfType" class="d-none d-md-block">Jenis</label>
					<select id="mfType" class="form-select">
						<option value="MASJID">Masjid</option>
						<option value="MUSHOLLA">Musholla</option>
					</select>
				</div>
				<div class="form-group text-end d-none d-md-block">
					<button type="submit" class="btn btn-search-map">Cari</button>
				</div>
			</form>
		</div>

		<div id="mainMap" class="main-map"></div>
		<div id="mapStatus" class="map-status" style="display:none"></div>
	</div>

</div>

