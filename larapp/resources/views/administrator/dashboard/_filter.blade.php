<div class="row mt-1 g-3">
  <div class="col-12">
        <div class="card p-2 shadow-sm">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <h5 class="mb-0">Peta Sebaran Masjid / Musholla</h5>
              <small class="text-muted">Filter cepat untuk menampilkan data pada tabel dan peta</small>
            </div>
            <div>
              <form id="facFiltersForm" method="GET" action="" class="d-flex gap-2 align-items-center">
                <select id="facRegional" name="regional_id" class="form-select form-select-sm" style="min-width:140px;"><option value="">Semua Regional</option></select>
                <select id="facArea" name="area_id" class="form-select form-select-sm" style="min-width:140px;" disabled><option value="">Semua Area</option></select>
                <select id="facWitel" name="witel_id" class="form-select form-select-sm" style="min-width:140px;" disabled><option value="">Semua Witel</option></select>
                <select id="facSto" name="sto_id" class="form-select form-select-sm" style="min-width:140px;" disabled><option value="">Semua STO</option></select>
                <select id="facType" name="type" class="form-select form-select-sm" style="min-width:120px;"><option value="">Jenis (Semua)</option><option value="masjid">Masjid</option><option value="mushalla">Mushalla</option></select>
                <button id="facReset" type="button" class="btn btn-sm btn-outline-secondary">Reset</button>
              </form>
            </div>
          </div>
        </div>
      </div>
</div>