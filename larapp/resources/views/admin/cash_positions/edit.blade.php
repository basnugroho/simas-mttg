@component('components.administrator.layout')
    @slot('title')
        {{ $title ?? 'Edit Cash Position' }}
    @endslot

    @section('content')

  <div class="p-4">
    <div class="card">
      <div class="card-header">Edit Cash Position</div>
      <div class="card-body">
    <form method="POST" action="{{ route('admin.cash_positions.update', $cp->id) }}" enctype="multipart/form-data">
      @csrf
      @method('PATCH')
      <div class="row g-2">
        <div class="col-md-6">
          <label class="form-label small">Periode</label>
          @php
            $currentYear = date('Y');
            $selectedPeriod = old('period_start', $cp->period_start?->format('Y-m').'-01');
          @endphp
          <select name="period_start" class="form-select">
            <option value="">-- Pilih Periode --</option>
            @for($month = 1; $month <= 12; $month++)
              @php
                $periodValue = $currentYear . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
                $monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $periodLabel = $monthNames[$month] . ' ' . $currentYear;
              @endphp
              <option value="{{ $periodValue }}" {{ $selectedPeriod == $periodValue ? 'selected' : '' }}>{{ $periodLabel }}</option>
            @endfor
          </select>
          <small class="text-muted">Contoh: Januari 2025 = periode 01/01/2025 - 31/01/2025</small>
        </div>
      </div>

      <div class="mb-2 mt-2">
        <label class="form-label small">Pilih Masjid</label>
        <select name="mosque_id" class="form-select">
          @foreach($mosques as $m)
            <option value="{{ $m->id }}" {{ $cp->mosque_id == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-2">
        <label class="form-label small">Nominal (Rp)</label>
        <input type="number" step="0.01" name="nominal" class="form-control" value="{{ old('nominal', $cp->nominal) }}">
      </div>

      <div class="mb-2">
        <label class="form-label small">Upload bukti (PDF only)</label>
        <input type="file" name="files[]" accept="application/pdf" multiple />
        <div style="margin-top:8px">
          @if($cp->photos->count())
            <div>Existing files:</div>
            <ul>
              @foreach($cp->photos as $ph)
                <li><a href="{{ Storage::url($ph->path) }}" target="_blank">{{ basename($ph->path) }}</a></li>
              @endforeach
            </ul>
          @endif
        </div>
      </div>

      <div class="mb-2">
        <label class="form-label small">Catatan</label>
        <textarea name="note" class="form-control" rows="3">{{ old('note', $cp->note) }}</textarea>
      </div>

      <div class="mb-2">
        <label class="form-label small">Catatan edit (opsional)</label>
        <textarea name="edit_note" class="form-control" rows="2">{{ old('edit_note') }}</textarea>
      </div>

      <div class="mb-2">
        <button class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('admin.cash_positions.index') }}" class="btn btn-secondary">Batal</a>
      </div>
    </form>
      </div>
    </div>
  </div>

    @show
@endcomponent