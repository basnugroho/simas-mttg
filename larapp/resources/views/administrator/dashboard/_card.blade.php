<div class="row g-3">
      {{-- Render province cards dynamically from $summary (except 'total') --}}
      @php
        $provinceColors = [
          'jawa_timur' => 'bg-danger',
          'bali' => 'bg-warning',
          'nusa_tenggara' => 'bg-info',
        ];
      @endphp

      @foreach($summary ?? [] as $key => $vals)
        @if($key === 'total')
          @continue
        @endif
        <div class="col-lg-3 col-md-6">
          <div class="card p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px">
                  <i class="fa fa-map"></i>
                </div>
                <div>
                  <h6 class="mb-1"><b>{{ ucfirst(str_replace('_',' ', $key)) }}</b></h6> {{ $vals['masjid'] ?? 0 + $vals['mushalla'] ?? 0 }}
                  <div class="text-muted small">Masjid: <strong>{{ $vals['masjid'] ?? 0 }}</strong> &nbsp; Mushalla: <strong>{{ $vals['mushalla'] ?? 0 }}</strong></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endforeach
      <div class="col-lg-3 col-md-6">
        <div class="card p-3 shadow-sm bg-dark text-white">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <div class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px">
                <i class="fa fa-list"></i>
              </div>
              <div>
                <h6 class="mb-1">Keseluruhan</h6>
                {{ ($summary['total']['masjid'] ?? 0) + ($summary['total']['mushalla'] ?? 0) }}
                <div class="small" style="color:white">Masjid: <strong>{{ $summary['total']['masjid'] ?? 0 }}</strong> &nbsp; Mushalla: <strong>{{ $summary['total']['mushalla'] ?? 0 }}</strong></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>