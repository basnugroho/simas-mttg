<div>
  <h3 style="margin-top:0">{{ $m->subject }}</h3>
  <div style="color:#6b7280">Dari: {{ $m->name }} &lt;{{ $m->email }}&gt; | Masjid: {{ $m->mosque?->name ?? '-' }} | {{ $m->created_at->toDateTimeString() }}</div>
  <hr>
  <div style="white-space:pre-wrap">{{ $m->message }}</div>
  <hr>
  <form method="POST" action="{{ route('admin.messages.destroy', $m->id) }}" onsubmit="return confirm('Hapus pesan ini?')">@csrf @method('DELETE')<button class="btn btn-danger">Hapus</button></form>
</div>
