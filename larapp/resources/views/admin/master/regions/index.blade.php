<x-admin.layout title="Master - Regions">
  <div class="p-4">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <h3>Regions</h3>
      <div><a href="{{ route('admin.regions.create') }}" class="btn btn-sm btn-primary">Create Region</a></div>
    </div>

    <form method="GET" style="margin-bottom:12px">
      <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search name" class="form-control" style="width:260px;display:inline-block" />
      <button class="btn btn-sm btn-secondary">Search</button>
    </form>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <table class="table table-sm">
      <thead><tr><th>#</th><th>Name</th><th>Type</th><th>Code</th><th>Parent</th><th>Actions</th></tr></thead>
      <tbody>
        @foreach($regions as $r)
        <tr>
          <td>{{ $r->id }}</td>
          <td>{{ $r->name }}</td>
          <td>{{ $r->type }}</td>
          <td>{{ $r->code }}</td>
          <td>{{ optional($r->parent)->name }}</td>
          <td>
            <a href="{{ route('admin.regions.edit', $r->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
            <form method="POST" action="{{ route('admin.regions.destroy', $r->id) }}" style="display:inline-block" onsubmit="return confirm('Delete region?')">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-danger">Delete</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    {{ $regions->withQueryString()->links() }}
  </div>
  </div>
</x-admin.layout>
