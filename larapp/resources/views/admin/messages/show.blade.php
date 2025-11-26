<x-admin.layout title="Message">
  <div class="p-4">
    <nav style="font-size:13px;margin-bottom:8px">
      <a href="{{ route('dashboard') }}">Dashboard</a> &raquo; <a href="{{ route('admin.messages.index') }}">Kotak Masuk</a> &raquo; Baca
    </nav>

    <div style="display:flex;gap:18px">
      <div style="width:360px">
        <div style="background:#fff;padding:8px;border-radius:8px">
          <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.messages.index') }}">Kembali</a>
        </div>
      </div>
      <div style="flex:1">
        <div style="background:#fff;padding:18px;border-radius:8px">
          <h3 style="margin-top:0">{{ $m->subject }}</h3>
          <div style="color:#6b7280">Dari: {{ $m->name }} &lt;{{ $m->email }}&gt; | Masjid: {{ $m->mosque?->name ?? '-' }} | {{ $m->created_at->toDateTimeString() }}</div>
          <hr>
          <div style="white-space:pre-wrap">{{ $m->message }}</div>
          <hr>
          <form method="POST" action="{{ route('admin.messages.destroy', $m->id) }}" onsubmit="return confirm('Hapus pesan ini?')">@csrf @method('DELETE')<button class="btn btn-danger">Hapus</button></form>
        </div>
      </div>
    </div>
  </div>
</x-admin.layout>
