<x-admin.layout title="Master - Activities">
  <div class="p-4">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <div style="display:flex;align-items:center;gap:12px">
        <div>
          <h3 style="margin:0">Activities</h3>
          <div style="font-size:12px;color:#6b7280;margin-top:4px">Master · <a href="{{ route('dashboard') }}">Dashboard</a> / <strong>Activities</strong></div>
        </div>
      </div>
      <div><a href="{{ route('admin.activities.create') }}" class="btn btn-sm btn-primary">Create Activity</a></div>
    </div>
    <form method="GET" style="margin-bottom:12px">
      <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search activity" class="form-control" style="width:260px;display:inline-block" />
      <button class="btn btn-sm btn-secondary">Search</button>
    </form>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <table class="table table-sm">
      <thead>
        <tr><th>#</th><th>Name</th><th>Category</th><th>Slug</th><th>Actions</th></tr>
      </thead>
      <tbody>
        @foreach($items as $it)
        <tr>
          <td>{{ $it->id }}</td>
          <td>{{ $it->activity_name }}</td>
          <td>{{ $it->category === 'mahdhah' ? 'Mahdhah' : 'Ghairu Mahdhah' }}</td>
          <td>{{ $it->slug ?? '-' }}</td>
          <td>
            <a href="{{ route('admin.activities.edit', $it->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
            <form method="POST" action="{{ route('admin.activities.destroy', $it->id) }}" style="display:inline-block" onsubmit="return confirm('Delete activity?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger">Delete</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    {{ $items->withQueryString()->links() }}
  </div>
</x-admin.layout>
