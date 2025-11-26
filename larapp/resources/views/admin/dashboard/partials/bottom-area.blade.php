<div style="display:flex; gap:18px">
  <div style="flex:1; background:#fff;padding:12px;border-radius:12px; box-shadow:0 8px 24px rgba(2,6,23,.04); max-height:420px; overflow:auto">
    <h4 style="margin-top:0">Masjid / Mushalla</h4>
    <table class="table table-sm">
      <thead>
        <tr><th>#</th><th>Nama</th><th>Lokasi</th><th>Kelengkapan</th></tr>
      </thead>
      <tbody>
        @php
          try {
            $dashboardMosques = \App\Models\Mosque::with(['regional','area','witel','sto','province','city'])
              ->orderBy('name')
              ->limit(10)
              ->get();
          } catch (\Throwable $__e) {
            $dashboardMosques = collect();
          }
        @endphp

        @if($dashboardMosques->count())
          @foreach($dashboardMosques as $idx => $m)
            <tr>
              <td>{{ $idx + 1 }}</td>
              <td>{{ $m->name ?? '-' }}</td>
              <td>{{ method_exists($m, 'regionPath') ? $m->regionPath() : ($m->region?->name ?? '-') }}</td>
              <td>{{ is_null($m->completion_percentage) ? '-' : (intval($m->completion_percentage) . '%') }}</td>
            </tr>
          @endforeach
        @else
          <tr><td colspan="4">No mosques found.</td></tr>
        @endif
      </tbody>
    </table>
  </div>

  <div style="flex:1; background:#fff;padding:12px;border-radius:12px; box-shadow:0 8px 24px rgba(2,6,23,.04);">
    <h4 style="margin-top:0">Peta</h4>
    <div id="map" style="height:360px;border-radius:8px;overflow:hidden"></div>
  </div>
</div>
