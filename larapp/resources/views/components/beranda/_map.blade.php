<section class="map-section">
	<div class="container">
		<div class="map-wrapper">
			<div class="map-filter-card" id="mapFilterCard">
				<h6>Filter Data</h6>
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
			<div id="mapStatus" style="position:absolute;top:10px;right:10px;z-index:11;background:rgba(255,255,255,.9);padding:6px 10px;border-radius:6px;font-size:.65rem;letter-spacing:.05em;font-weight:600;color:#555;display:none"></div>
			<div id="mainMap"></div>
		</div>
	</div>
</section>
