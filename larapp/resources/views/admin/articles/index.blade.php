@component('components.administrator.layout')
    @slot('title')
        {{ $title ?? 'Articles' }}
    @endslot

    @section('header')
    <div class="col-sm-6"><h3 class="mb-0">Articles</h3></div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Articles</a></li>
        </ol>
    </div>
    @show

    @section('content')
      <div class="card mb-4">
        <div class="card-header">
          <h3 class="card-title">Articles</h3>
        </div>
        <div class="card-body">
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <div></div>
            <div><a href="{{ route('admin.articles.create') }}" class="btn btn-primary">Buat Article</a></div>
          </div>
        </div>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card-body p-0 border-top" style="padding-top:8px;">
          <table class="table table-sm table-striped" role="table" style="width:100%">
            <thead><tr><th>Title</th><th>Category</th><th>Mosque</th><th>Status</th><th>Creator</th><th>Created</th><th style="width:200px">Aksi</th></tr></thead>
            <tbody>
              @foreach($items as $it)
                <tr>
                  <td>{{ $it->title }}</td>
                  <td>{{ $it->category?->name ?? '-' }}</td>
                  <td>{{ $it->mosque?->name ?? '-' }}</td>
                  <td>{{ $it->status }}</td>
                  <td>{{ $it->creator?->name ?? 'User #'.($it->created_by ?? '-') }}</td>
                  <td>{{ $it->created_at->toDateTimeString() }}</td>
                  <td>
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
          <div class="p-3">{{ $items->links() }}</div>
        </div>
      </div>
    @show
@endcomponent
