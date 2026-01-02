@component('components.administrator.layout')
    @slot('title')
        {{ $title ?? 'Pengurus Masjid' }}
    @endslot

    @section('header')
        <div class="col-sm-6"><h3 class="mb-0">Pengurus</h3></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item active">Kotak Masuk</a></li>
            </ol>
        </div>  
        @show

    @section('content')


    <div class="card mb-3">
      <div class="card-header">
        <h3 class="card-title">Pengurus Masjid</h3>
      </div>
      <div class="card-body">

        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

        <div class="mb-4">
          <h5>Upload Pengurus</h5>
          <form method="POST" action="{{ route('admin.mosque_managers.store') }}" enctype="multipart/form-data">
            @csrf

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
              <label class="form-label small">Jumlah Pengurus</label>
              <input type="number" name="jumlah_pengurus" class="form-control" value="{{ old('jumlah_pengurus') }}">
            </div>

            <div class="mb-2">
              <label class="form-label small">Nama Ketua Pengurus</label>
              <input type="text" name="ketua_pengurus" class="form-control" value="{{ old('ketua_pengurus') }}">
            </div>

            <div class="mb-2">
              <label class="form-label small">File bukti (PDF)</label>
              <input type="file" name="file" accept="application/pdf" class="form-control">
              <div style="font-size:12px;color:#6b7280;margin-top:6px">Batas server: post_max_size={{ ini_get('post_max_size') }} , upload_max_filesize={{ ini_get('upload_max_filesize') }}. Jika file gagal upload karena terlalu besar, kurangi ukuran file atau hubungi admin.</div>
            </div>

            <div class="mb-2">
              <button class="btn btn-primary">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">Daftar Pengurus</div>
      <div class="card-body p-3">
        <div class="overflow-auto">
          <table class="w-full text-sm" style="width:100%">
            <thead>
              <tr style="text-align:left"><th class="p-2">Nama Masjid</th><th class="p-2">Regions Masjid</th><th class="p-2">Jumlah</th><th class="p-2">Ketua</th><th class="p-2">File</th><th class="p-2">Uploader</th><th class="p-2">Dibuat</th><th class="p-2">Aksi</th></tr>
            </thead>
            <tbody>
              @foreach($items as $it)
                <tr class="border-t">
                  <td class="p-2">{{ $it->mosque?->name ?? '-' }}</td>
                  <td class="p-2">{{ $it->mosque ? $it->mosque->regionPath() : '-' }}</td>
                  <td class="p-2">{{ $it->jumlah_pengurus ?? '-' }}</td>
                  <td class="p-2">{{ $it->ketua_pengurus ?? '-' }}</td>
                  <td class="p-2">
                    @if($it->file_path)
                      <a href="{{ Storage::url($it->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat PDF</a>
                      <a href="{{ Storage::url($it->file_path) }}" download class="btn btn-sm btn-outline-secondary">Unduh</a>
                    @else
                      -
                    @endif
                  </td>
                  <td class="p-2">{{ $it->creator?->name ?? ('User #'.($it->created_by ?? '-')) }}</td>
                  <td class="p-2">{{ $it->created_at->toDateTimeString() }}</td>
                  <td class="p-2">
                    <a href="{{ route('admin.mosque_managers.edit', $it->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                    <form action="{{ route('admin.mosque_managers.destroy', $it->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus item ini?')">
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
        <div class="mt-3">{{ $items->links() }}</div>
      </div>
    </div>

    @show
@endcomponent
