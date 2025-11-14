<x-admin.layout title="Master - Facilities">
  <div class="p-4">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <h3>Facilities</h3>
      <div><a href="{{ route('admin.facilities.create') }}" class="btn btn-sm btn-primary">Create Facility</a></div>
    </div>
    <form method="GET" style="margin-bottom:12px">
      <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search name" class="form-control" style="width:260px;display:inline-block" />
      <button class="btn btn-sm btn-secondary">Search</button>
    </form>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <table class="table table-sm">
      <thead><tr><th>#</th><th>Name</th><th>Slug</th><th>Required</th><th>Actions</th></tr></thead>
      <tbody>
        @foreach($items as $it)
        <tr>
          <td>{{ $it->id }}</td>
          <td>{{ $it->name }}</td>
          <td>{{ $it->slug }}</td>
          <td>{{ $it->is_required ? 'Yes' : 'No' }}</td>
          <td>
            <a href="{{ route('admin.facilities.edit', $it->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
            <form method="POST" action="{{ route('admin.facilities.destroy', $it->id) }}" style="display:inline-block" onsubmit="return confirm('Delete facility?')">
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
