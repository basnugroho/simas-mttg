<x-admin.layout title="Cash Positions">
  <div class="p-4">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <div>
        <h3 style="margin:0">Cash Positions</h3>
        <div style="font-size:12px;color:#6b7280;margin-top:4px">Admin · <a href="{{ route('dashboard') }}">Dashboard</a> / <strong>Cash</strong></div>
      </div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="mb-4 p-3" style="background:#fff;border-radius:8px">
      <h5>Upload Posisi Cash Terakhir</h5>
      <form method="POST" action="{{ route('admin.cash_positions.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label small">Periode Mulai</label>
            <input type="date" name="period_start" class="form-control" value="{{ old('period_start') }}">
          </div>
          <div class="col-md-6">
            <label class="form-label small">Periode Selesai (opsional)</label>
            <input type="date" name="period_end" class="form-control" value="{{ old('period_end') }}">
          </div>
        </div>

        <div class="mb-2 mt-2">
          <label class="form-label small">Pilih Masjid</label>
          <select name="mosque_id" class="form-select">
            <option value="">-- Pilih Masjid --</option>
            @foreach($mosques as $m)
              <option value="{{ $m->id }}">{{ $m->name }} — {{ $m->city?->name ?? $m->province?->name ?? '-' }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-2 mt-2">
          <label class="form-label small">Nominal (Rp)</label>
          <input type="number" step="0.01" name="nominal" class="form-control" value="{{ old('nominal') }}" placeholder="0.00">
        </div>

        <div class="mb-2">
          <label class="form-label small">File bukti (multiple) — upload file, bukan foto</label>
          <div id="cash-photo-drop" class="border rounded p-3" style="min-height:120px;display:flex;flex-direction:column;gap:8px;position:relative;">
            <div style="flex:1;display:flex;align-items:center;justify-content:center;color:#6b7280">Tarik file ke sini atau gunakan tombol "Browse" untuk memilih file</div>
            <div style="display:flex;justify-content:center;gap:8px">
              <button type="button" id="cash-photos-browse" class="btn btn-sm btn-outline-secondary">Browse</button>
              <button type="button" id="cash-photos-clear" class="btn btn-sm btn-outline-danger">Clear</button>
            </div>
            <input class="cash-files-input" type="file" name="files[]" multiple style="display:none">
          </div>
          <div id="cash-photo-previews" class="mt-2" style="display:flex;flex-direction:column;gap:8px"></div>
        </div>

        <div class="mb-2">
          <label class="form-label small">Catatan</label>
          <textarea name="note" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-2">
          <button class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>

    <div class="p-3" style="background:#fff;border-radius:8px">
      <h5>Daftar Posisi Cash</h5>
      <form method="GET" class="mb-3" action="{{ route('admin.cash_positions.index') }}">
        <div style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap">
          <div style="min-width:220px">
            <label class="form-label small">Filter Masjid</label>
            <select name="mosque_id" class="form-select">
              <option value="">-- Semua Masjid (sesuai wilayah) --</option>
              @foreach($mosques as $m)
                <option value="{{ $m->id }}" @if(isset($selected_mosque) && $selected_mosque == $m->id) selected @endif>{{ $m->name }} — {{ $m->city?->name ?? $m->province?->name ?? '-' }}</option>
              @endforeach
            </select>
          </div>

          <div style="min-width:180px">
            <label class="form-label small">Periode dari</label>
            <input type="date" name="period_from" class="form-control" value="{{ $filter_period_from ?? '' }}">
          </div>
          <div style="min-width:180px">
            <label class="form-label small">Periode sampai</label>
            <input type="date" name="period_to" class="form-control" value="{{ $filter_period_to ?? '' }}">
          </div>

          <div>
            <label class="form-label small">&nbsp;</label>
            <div>
              <button class="btn btn-sm btn-primary" type="submit">Filter</button>
              <a href="{{ route('admin.cash_positions.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
          </div>
        </div>
      </form>
      <div class="overflow-auto">
        <table class="w-full text-sm" style="width:100%">
          <thead>
            <tr style="text-align:left">
              <th class="p-2">@include('admin._sort_link', ['label' => 'Periode', 'key' => 'period_start'])</th>
              <th class="p-2">@include('admin._sort_link', ['label' => 'Nominal', 'key' => 'nominal'])</th>
              <th class="p-2">@include('admin._sort_link', ['label' => 'Pembuat', 'key' => 'created_at'])</th>
              <th class="p-2">Masjid</th>
              <th class="p-2">Catatan</th>
              <th class="p-2">Files</th>
              <th class="p-2">@include('admin._sort_link', ['label' => 'Created', 'key' => 'created_at'])</th>
              <th class="p-2">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($positions as $p)
              <tr class="border-t">
                <td class="p-2">{{ $p->period_start ? $p->period_start->toDateString() : '-' }} @if($p->period_end) — {{ $p->period_end->toDateString() }}@endif</td>
                <td class="p-2">{{ isset($p->nominal) ? number_format($p->nominal, 2, ',', '.') : '-' }}</td>
                <td class="p-2">{{ $p->creator?->name ?? ('User #'.($p->created_by ?? '-')) }}</td>
                <td class="p-2">{{ $p->mosque?->name ?? '-' }}</td>
                <td class="p-2">{{ Str::limit($p->note ?? '-', 120) }}</td>
                <td class="p-2">
                  @if($p->photos && $p->photos->count())
                    <div style="display:flex;flex-direction:column;gap:6px">
                    @foreach($p->photos as $ph)
                      <a href="{{ Storage::url($ph->path) }}" target="_blank" style="font-size:13px;color:#0b69a3">{{ basename($ph->path) }}</a>
                    @endforeach
                    </div>
                  @else
                    -
                  @endif
                </td>
                <td class="p-2">{{ $p->created_at->toDateTimeString() }}</td>
                <td class="p-2">
                  <a href="{{ route('admin.cash_positions.edit', $p->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                  <form action="{{ route('admin.cash_positions.destroy', $p->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus posisi cash ini? (akan disembunyikan)')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Hapus</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $positions->links() }}</div>
    </div>
  </div>
</x-admin.layout>

@push('scripts')
  <script>
    (function(){
      const drop = document.getElementById('cash-photo-drop');
      const input = drop ? drop.querySelector('.cash-files-input') : null;
      const previews = document.getElementById('cash-photo-previews');
      let dt = new DataTransfer();

      function humanSize(bytes){ if(!bytes) return '0 B'; const units=['B','KB','MB','GB']; let i=0; while(bytes>=1024 && i<units.length-1){ bytes/=1024; i++; } return Math.round(bytes*10)/10 + ' ' + units[i]; }

      function render(){ previews.innerHTML=''; Array.from(dt.files).forEach((file, idx)=>{
        const wrap = document.createElement('div'); wrap.style.display='flex'; wrap.style.alignItems='center'; wrap.style.justifyContent='space-between'; wrap.style.padding='8px'; wrap.style.border='1px solid #e5e7eb'; wrap.style.borderRadius='6px'; wrap.style.background='#fff'; wrap.style.maxWidth='620px';
        const left = document.createElement('div'); left.style.display='flex'; left.style.flexDirection='column';
        const name = document.createElement('div'); name.style.fontSize='13px'; name.style.color='#111'; name.textContent = file.name;
        const meta = document.createElement('div'); meta.style.fontSize='12px'; meta.style.color='#6b7280'; meta.textContent = humanSize(file.size);
        left.appendChild(name); left.appendChild(meta);
        const right = document.createElement('div'); right.style.display='flex'; right.style.gap='8px';
        const remove = document.createElement('button'); remove.type='button'; remove.className='btn btn-sm btn-outline-danger'; remove.textContent='Hapus'; remove.addEventListener('click', ()=>{ const nd = new DataTransfer(); Array.from(dt.files).forEach((f,i)=>{ if(i!==idx) nd.items.add(f); }); dt = nd; if(input) input.files = dt.files; render(); });
        right.appendChild(remove);
        wrap.appendChild(left); wrap.appendChild(right);
        previews.appendChild(wrap);
      }) }

      const browse = document.getElementById('cash-photos-browse'); if(browse) browse.addEventListener('click', ()=>{ if(input) input.click(); });
      const clearBtn = document.getElementById('cash-photos-clear'); if(clearBtn) clearBtn.addEventListener('click', ()=>{ dt = new DataTransfer(); if(input) input.files = dt.files; render(); });
      if(drop){ drop.addEventListener('dragover', e=>{ e.preventDefault(); drop.style.background='#fbfbfb'; }); drop.addEventListener('dragleave', ()=>{ drop.style.background=''; }); drop.addEventListener('drop', e=>{ e.preventDefault(); drop.style.background=''; const files = Array.from(e.dataTransfer.files); files.forEach(f=> { dt.items.add(f); }); if(input) input.files = dt.files; render(); }); }
      if(input){ input.addEventListener('change', e=>{ const files = Array.from(e.target.files); const nd = new DataTransfer(); Array.from(dt.files).forEach(f=> nd.items.add(f)); files.forEach(f=> nd.items.add(f)); dt = nd; if(input) input.files = dt.files; render(); }); }
      // prevent default drop anywhere to avoid opening files in new tab
      window.addEventListener('dragover', e=> e.preventDefault()); window.addEventListener('drop', e=> e.preventDefault());
    })();
  </script>
@endpush
