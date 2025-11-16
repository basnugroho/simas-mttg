<x-admin.layout title="Master - Regions">
  <div class="p-4">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <h3>Regions</h3>
      <div style="display:flex;gap:8px;align-items:center">
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">Kembali ke Dashboard</a>
        <a href="{{ route('admin.regions.create') }}" class="btn btn-sm btn-primary">Create Region</a>
      </div>
    </div>

    <form method="GET" style="margin-bottom:12px;display:flex;gap:8px;align-items:center">
      <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search name" class="form-control" style="width:260px;display:inline-block" />
      <select name="pov" class="form-control" style="width:220px">
        <option value="">-- POV / Ordering --</option>
        @foreach(\App\Models\Regions::POVS as $povKey => $povLabel)
          <option value="{{ $povKey }}" {{ (request('pov') == $povKey) ? 'selected' : '' }}>{{ $povLabel }}</option>
        @endforeach
      </select>
      <select name="level" class="form-control" style="width:160px">
        <option value="">-- Level --</option>
        @foreach(\App\Models\Regions::LEVELS as $lvl)
          <option value="{{ $lvl }}" {{ (request('level') == $lvl) ? 'selected' : '' }}>{{ $lvl }}</option>
        @endforeach
      </select>
      <button class="btn btn-sm btn-secondary">Search</button>
    </form>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <table class="table table-sm">
      <thead>
        <tr>
          <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'id','dir' => (request('sort')=='id' && request('dir')=='asc') ? 'desc' : 'asc']) }}"># @if(request('sort')=='id') ({{ strtoupper(request('dir','asc')) }}) @endif</a></th>
          <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'name','dir' => (request('sort')=='name' && request('dir')=='asc') ? 'desc' : 'asc']) }}">Name @if(request('sort')=='name') ({{ strtoupper(request('dir','asc')) }}) @endif</a></th>
          <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'pov','dir' => (request('sort')=='pov' && request('dir')=='asc') ? 'desc' : 'asc']) }}">POV @if(request('sort')=='pov') ({{ strtoupper(request('dir','asc')) }}) @endif</a></th>
          <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'type','dir' => (request('sort')=='type' && request('dir')=='asc') ? 'desc' : 'asc']) }}">Type @if(request('sort')=='type') ({{ strtoupper(request('dir','asc')) }}) @endif</a></th>
          <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'code','dir' => (request('sort')=='code' && request('dir')=='asc') ? 'desc' : 'asc']) }}">Code @if(request('sort')=='code') ({{ strtoupper(request('dir','asc')) }}) @endif</a></th>
          <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'parent','dir' => (request('sort')=='parent' && request('dir')=='asc') ? 'desc' : 'asc']) }}">Parent @if(request('sort')=='parent') ({{ strtoupper(request('dir','asc')) }}) @endif</a></th>
          <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'level','dir' => (request('sort')=='level' && request('dir')=='asc') ? 'desc' : 'asc']) }}">Level @if(request('sort')=='level') ({{ strtoupper(request('dir','asc')) }}) @endif</a></th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($regions as $r)
        <tr>
          <td>{{ $r->id }}</td>
          <td>{{ $r->name }}</td>
          <td>{{ $r->pov ?? '-' }}</td>
          <td>{{ method_exists($r, 'displayTypeLabel') ? $r->displayTypeLabel() : ($r->type_key ? (\App\Models\Regions::TYPES[$r->type_key] ?? $r->type_key) : ($r->type ?? '')) }}</td>
          <td>{{ $r->code }}</td>
          <td>{{ optional($r->parent)->name }}</td>
          <td>{{ $r->level ?? '-' }}</td>
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
