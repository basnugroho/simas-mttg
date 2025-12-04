@component('components.administrator.layout')
    @slot('title')
        {{ $title ?? 'Activities' }}
    @endslot

    @section('header')
    <div class="col-sm-6"><h3 class="mb-0">Activities</h3></div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Activities</a></li>
        </ol>
    </div>
    @show

    @section('content')
      <div class="card mb-4">
        <div class="card-header">
          <h3 class="card-title">Activities</h3>
        </div>
        <div class="card-body">
          <form method="GET" style="margin-bottom:12px;display:flex;gap:8px;align-items:center;">
            <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search activity" class="form-control" style="width:260px;display:inline-block" />
            <button class="btn btn-sm btn-secondary">Search</button>
            <a href="{{ route('admin.activities.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset filters">Reset</a>
            <div style="margin-left:auto;"><a href="{{ route('admin.activities.create') }}" class="btn btn-sm btn-primary">Create Activity</a></div>
          </form>
        </div>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card-body p-0 border-top" style="padding-top:8px;">
        <table class="table table-sm table-striped">
          <thead>
            <tr><th style="width:10px">#</th><th>Name</th><th>Category</th><th>Slug</th><th style="width:120px">Actions</th></tr>
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
      </div>
    @show
@endcomponent
