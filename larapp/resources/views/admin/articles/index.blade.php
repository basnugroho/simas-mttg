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
          <div style="display:flex;justify-content:space-between;align-items:center;gap:12px">
            <div style="flex:1;min-width:0">
              <form method="GET" action="" class="d-flex gap-2">
                <select name="category_id" class="form-select" style="max-width:220px">
                  <option value="">All categories</option>
                  @foreach(($categories ?? []) as $c)
                    <option value="{{ $c->id }}" @if(request('category_id') == $c->id) selected @endif>{{ $c->name }}</option>
                  @endforeach
                </select>
                <select name="mosque_id" class="form-select" style="max-width:220px">
                  <option value="">All mosques</option>
                  @foreach(($mosques ?? []) as $m)
                    <option value="{{ $m->id }}" @if(request('mosque_id') == $m->id) selected @endif>{{ $m->name }}</option>
                  @endforeach
                </select>
                <select name="status" class="form-select" style="max-width:160px">
                  <option value="">Any status</option>
                  <option value="DRAFT" @if(request('status')==='DRAFT') selected @endif>Draft</option>
                  <option value="PUBLISHED" @if(request('status')==='PUBLISHED') selected @endif>Published</option>
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" style="max-width:160px">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" style="max-width:160px">
                <button class="btn btn-outline-secondary" type="submit">Filter</button>
                <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">Reset</a>
              </form>
            </div>
            <div style="margin-left:8px"><a href="{{ route('admin.articles.create') }}" class="btn btn-primary">Buat Article</a></div>
          </div>
        </div>
       
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
                    <a href="{{ route('admin.articles.preview', $it->id) }}" target="_blank" class="btn btn-sm btn-info">Preview</a>
                    @if($it->status !== 'PUBLISHED')
                      <form action="{{ route('admin.articles.publish', $it->id) }}" method="POST" style="display:inline">@csrf<button class="btn btn-sm btn-success">Publish</button></form>
                    @endif
                    <form action="{{ route('admin.articles.destroy', $it->id) }}" method="POST" class="d-inline delete-article-form" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
          <div class="p-3">{{ $items->links() }}</div>
        </div>
      </div>
    @show
    @push('scripts')
      <script>
        (function(){
          document.addEventListener('DOMContentLoaded', function(){
            const forms = document.querySelectorAll('.delete-article-form');
            forms.forEach(function(f){
              f.addEventListener('submit', function(e){
                e.preventDefault();
                const title = this.closest('tr')?.querySelector('td')?.innerText || 'this article';
                if(confirm('Apakah Anda yakin ingin menghapus artikel: "' + title.trim() + '" ?')){
                  this.submit();
                }
              });
            });
          });
        })();
      </script>
    @endpush
@endcomponent
