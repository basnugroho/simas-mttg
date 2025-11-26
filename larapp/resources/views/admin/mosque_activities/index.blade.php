<x-admin.layout title="Aktivitas Masjid">
  <div class="p-4">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <div>
        <h3 style="margin:0">Aktivitas Masjid</h3>
        <div style="font-size:12px;color:#6b7280;margin-top:4px">Admin · <a href="{{ route('dashboard') }}">Dashboard</a> / <strong>Aktivitas Masjid</strong></div>
      </div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div>
      <div id="assign" class="mb-3" style="background:#fff;padding:12px;border-radius:8px;box-shadow:0 8px 24px rgba(2,6,23,.04);">
        <h5>Buat Aktivitas Masjid</h5>
        <form method="POST" action="{{ route('admin.mosque_activities.store') }}" enctype="multipart/form-data">
          @csrf
          <div class="row g-2 align-items-end">
            <div class="col-md-3">
              <label class="form-label small">Aktivitas</label>
              <select name="activity_id" class="form-select">
                @foreach($activities as $a)
                  <option value="{{ $a->id }}">{{ $a->activity_name }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label small">Pilih Masjid</label>
              <select id="assign-mosque-select" name="mosque_ids[]" multiple class="form-select" style="min-height:40px">
                @foreach($mosques as $m)
                  <option value="{{ $m->id }}" {{ in_array($m->id, $selected) ? 'selected' : '' }}>{{ $m->name }} — {{ $m->city?->name ?? $m->province?->name ?? '-' }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label small">Mulai (datetime)</label>
              <input type="datetime-local" name="event_start" class="form-control" value="{{ request('event_start') ?? '' }}">
            </div>

            <div class="col-md-3">
              <label class="form-label small">Selesai (datetime)</label>
              <input type="datetime-local" name="event_end" class="form-control" value="{{ request('event_end') ?? '' }}">
            </div>

            <div class="col">
              <label class="form-label small">Catatan</label>
              <textarea name="note" class="form-control" rows="2" placeholder="Catatan (opsional)"></textarea>
            </div>

            <div class="col-12 mt-2">
              <label class="form-label small">Foto (boleh banyak)</label>
              <input type="file" name="photos[]" multiple accept="image/*" class="form-control">
            </div>

            <div class="col-auto">
              <button class="btn btn-primary">Simpan</button>
            </div>
          </div>
        </form>
      </div>

      <div class="p-4 border rounded" style="background:#fff">
        <h3 class="font-semibold">Tabel Aktivitas Masjid</h3>
        <form method="GET" class="mb-3 flex space-x-2">
          <input type="text" name="filter_mosque" value="{{ request('filter_mosque') }}" placeholder="Cari nama masjid" class="border p-2 w-1/3">
          <input type="date" name="date_from" value="{{ request('date_from') }}" class="border p-2">
          <input type="date" name="date_to" value="{{ request('date_to') }}" class="border p-2">
          <button type="submit" class="bg-gray-200 p-2 rounded">Filter</button>
        </form>

        <div class="overflow-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="text-left">
                <th class="p-2">Masjid</th>
                <th class="p-2">Region</th>
                <th class="p-2">Aktivitas</th>
                <th class="p-2">Catatan</th>
                <th class="p-2">Dibuat</th>
                <th class="p-2">Pembuat</th>
                <th class="p-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($assignments as $a)
              <tr class="border-t">
                <td class="p-2">{{ $a->mosque_name }}</td>
                <td class="p-2" style="min-width:220px">{{ $a->region_path ?? (trim(($a->city_name ?? '') . ' / ' . ($a->province_name ?? ''))) }}</td>
                <td class="p-2">{{ $a->activity_name }}</td>
                <td class="p-2">{{ $a->note }}</td>
                <td class="p-2">{{ \Carbon\Carbon::parse($a->created_at)->toDateTimeString() }}</td>
                <td class="p-2">{{ $a->activity_creator ?? '-' }}</td>
                <td class="p-2">
                  <form action="{{ route('admin.mosque_activities.destroy', $a->id) }}" method="POST" onsubmit="return confirm('Hapus assignment?')">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600 text-sm">Hapus</button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $assignments->links() }}
        </div>
      </div>
    </div>

  </div>
</x-admin.layout>

@push('scripts')
  <script>
    // On page load, if URL contains mosque_ids params, preselect them in the assign form and scroll to it
    (function(){
      try{
        const params = new URLSearchParams(window.location.search);
        const values = params.getAll('mosque_ids[]');
        if(!values || !values.length) return;
      const sel = document.getElementById('assign-mosque-select');
        if(sel){
          Array.from(sel.options).forEach(o=>{ if(values.includes(o.value)) o.selected = true; });
          // scroll to assign panel
          const el = document.getElementById('assign'); if(el){ el.scrollIntoView({behavior:'smooth'}); }
        }
      }catch(e){}
    })();
  </script>
@endpush
