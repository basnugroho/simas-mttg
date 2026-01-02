<x-admin.layout title="Edit Pengurus Masjid">
  <div class="p-4">
    <h3>Edit Pengurus Masjid</h3>
    <form method="POST" action="{{ route('admin.mosque_managers.update', $m->id) }}" enctype="multipart/form-data">
      @csrf
      @method('PATCH')

      <div class="mb-2 mt-2">
        <label class="form-label small">Pilih Masjid</label>
        <select name="mosque_id" class="form-select">
          @foreach($mosques as $ms)
            <option value="{{ $ms->id }}" @if($ms->id == $m->mosque_id) selected @endif>{{ $ms->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-2">
        <label class="form-label small">Jumlah Pengurus</label>
        <input type="number" name="jumlah_pengurus" class="form-control" value="{{ old('jumlah_pengurus', $m->jumlah_pengurus) }}">
      </div>

      <div class="mb-2">
        <label class="form-label small">Nama Ketua Pengurus</label>
        <input type="text" name="ketua_pengurus" class="form-control" value="{{ old('ketua_pengurus', $m->ketua_pengurus) }}">
      </div>

      <div class="mb-2">
        <label class="form-label small">File bukti (PDF)</label>
        @if($m->file_path)
          <div><a href="{{ Storage::url($m->file_path) }}" target="_blank">{{ basename($m->file_path) }}</a></div>
        @endif
        <input type="file" name="file" accept="application/pdf" class="form-control">
        <div style="font-size:12px;color:#6b7280;margin-top:6px">Batas server: post_max_size={{ ini_get('post_max_size') }} , upload_max_filesize={{ ini_get('upload_max_filesize') }}.</div>
      </div>

      <div class="mb-2">
        <label class="form-label small">Catatan edit</label>
        <input type="text" name="edit_note" class="form-control" value="{{ old('edit_note') }}">
      </div>

      <div class="mb-2"><button class="btn btn-primary">Simpan</button></div>
    </form>
  </div>
</x-admin.layout>
