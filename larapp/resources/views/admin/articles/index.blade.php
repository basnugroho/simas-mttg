<x-admin.layout title="Articles">
  <div class="p-4">
    <nav style="font-size:13px;margin-bottom:8px">
      <a href="{{ route('dashboard') }}">Dashboard</a> &raquo; Articles
    </nav>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <div>
        <h3 style="margin:0">Articles</h3>
      </div>
      <div><a href="{{ route('admin.articles.create') }}" class="btn btn-primary">Buat Article</a></div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="p-3" style="background:#fff;border-radius:8px">
      <table class="w-full text-sm" style="width:100%">
        <thead><tr><th class="p-2">Title</th><th class="p-2">Category</th><th class="p-2">Mosque</th><th class="p-2">Status</th><th class="p-2">Creator</th><th class="p-2">Created</th><th class="p-2">Aksi</th></tr></thead>
        <tbody>
          @foreach($items as $it)
            <tr class="border-t">
              <td class="p-2">{{ $it->title }}</td>
              <td class="p-2">{{ $it->category?->name ?? '-' }}</td>
              <td class="p-2">{{ $it->mosque?->name ?? '-' }}</td>
              <td class="p-2">{{ $it->status }}</td>
              <td class="p-2">{{ $it->creator?->name ?? 'User #'.($it->created_by ?? '-') }}</td>
              <td class="p-2">{{ $it->created_at->toDateTimeString() }}</td>
              <td class="p-2">
                <a href="{{ route('admin.articles.edit', $it->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                <a href="{{ route('admin.articles.preview', $it->id) }}" target="_blank" class="btn btn-sm btn-outline-info">Preview</a>
                @if($it->status !== 'PUBLISHED')
                  <form action="{{ route('admin.articles.publish', $it->id) }}" method="POST" style="display:inline">@csrf<button class="btn btn-sm btn-success">Publish</button></form>
                @endif
                <form action="{{ route('admin.articles.destroy', $it->id) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="mt-3">{{ $items->links() }}</div>
    </div>
  </div>
</x-admin.layout>
