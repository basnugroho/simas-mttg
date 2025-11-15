<section class="facility-section mt-5">
	<div class="container">
		<div class="row mb-3 align-items-center g-2">
			<div class="col-12 col-md-5 col-lg-6">
				<div class="fw-semibold" style="font-size:1.1rem;">Kelengkapan Fasilitas</div>
				<div class="text-muted" style="font-size:.95rem;">Kelengkapan fasilitas masjid / mushalla (Jatim, Bali, Nusra)</div>
			</div>
			<div class="col-12 col-md-7 col-lg-6">
				<div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
					<select id="filterProvince" class="form-select form-select-sm w-auto" style="min-width:150px;">
						<option>Provinsi: Jawa Timur</option>
						<option>Bali</option>
						<option>Nusa Tenggara</option>
					</select>
					<select id="filterCity" class="form-select form-select-sm w-auto" style="min-width:120px;">
						<option>Surabaya</option>
						<option>Kediri</option>
						<option>Malang</option>
					</select>
					<select id="filterCompleteness" class="form-select form-select-sm w-auto" style="min-width:120px;">
						<option>Kelengkapan</option>
						<option>100%</option>
						<option>>= 80%</option>
					</select>
					<button id="filterApply" class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-search"></i></button>
				</div>
			</div>
		</div>
		<div class="dual-facility-card fixed-height mb-3">
			<div class="row">
				<div class="split-col col-12 col-lg-6 pe-lg-3">
					<div class="d-flex align-items-center mb-2">
						<i class="bi bi-moon-stars fs-3 me-2 text-secondary"></i>
						<div>
							<div class="fw-bold" style="font-size:1rem;">Data Masjid TREG 3</div>
							<div class="text-muted small">Informasi semua masjid di TREG 3</div>
						</div>
					</div>
					<div class="facility-list">
						<div id="masjidGrid" class="facility-grid"></div>
					</div>
					<div class="text-center mt-2"><a href="#" class="more-link">Lihat Lebih Lengkap</a></div>
				</div>
				<div class="split-col col-12 col-lg-6 ps-lg-3">
					<div class="d-flex align-items-center mb-2">
						<i class="bi bi-moon-stars fs-3 me-2 text-secondary"></i>
						<div>
							<div class="fw-bold" style="font-size:1.1rem;">Data Mushalla TREG 3</div>
							<div class="text-muted small">Informasi semua mushalla di TREG 3</div>
						</div>
					</div>
					<div class="facility-list">
						<div id="mushollaGrid" class="facility-grid"></div>
					</div>
					<div class="text-center mt-2"><a href="#" class="more-link">Lihat Lebih Lengkap</a></div>
				</div>
			</div>
		</div>
	</div>
</section>
