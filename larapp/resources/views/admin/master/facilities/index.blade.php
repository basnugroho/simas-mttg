<x-admin.layout title="Master - Facilities">
  <div class="p-4">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <div style="display:flex;align-items:center;gap:12px">
        <div>
          <h3 style="margin:0">Facilities</h3>
          <div style="font-size:12px;color:#6b7280;margin-top:4px">Master · <a href="{{ route('dashboard') }}">Dashboard</a> / <strong>Facilities</strong></div>
        </div>
      </div>
      <div><a href="{{ route('admin.facilities.create') }}" class="btn btn-sm btn-primary">Create Facility</a></div>
    </div>
    <form method="GET" style="margin-bottom:12px">
      <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search name" class="form-control" style="width:260px;display:inline-block" />
      <button class="btn btn-sm btn-secondary">Search</button>
    </form>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <table class="table table-sm">
      <thead>
        <tr>
          <th>#</th>
          @php
            $curSort = $sort ?? request('sort');
            $curDir = $dir ?? request('dir', 'asc');
            $toggle = function($col) use ($curSort, $curDir){
              if($curSort === $col) return $curDir === 'asc' ? 'desc' : 'asc';
              return 'asc';
            };
          @endphp
          <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'name','dir'=>$toggle('name')]) }}">Name @if(($curSort ?? '')==='name')({{ ($curDir ?? '')==='asc' ? '↑' : '↓' }})@endif</a></th>
          <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'slug','dir'=>$toggle('slug')]) }}">Slug @if(($curSort ?? '')==='slug')({{ ($curDir ?? '')==='asc' ? '↑' : '↓' }})@endif</a></th>
          <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'unit','dir'=>$toggle('unit')]) }}">Unit @if(($curSort ?? '')==='unit')({{ ($curDir ?? '')==='asc' ? '↑' : '↓' }})@endif</a></th>
          <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'is_required','dir'=>$toggle('is_required')]) }}">Required @if(($curSort ?? '')==='is_required')({{ ($curDir ?? '')==='asc' ? '↑' : '↓' }})@endif</a></th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $it)
        <tr>
          <td>{{ $it->id }}</td>
          <td>{{ $it->name }}</td>
          <td>{{ $it->slug }}</td>
          <td>{{ $it->unit?->name ?? '-' }}</td>
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
  @push('scripts')
    <script>
      // keyboard shortcut: Alt+Shift+D to go back to dashboard
      (function(){ document.addEventListener('keydown', function(e){ if(e.altKey && e.shiftKey && String(e.key).toLowerCase() === 'd'){ window.location = '{{ route("dashboard") }}'; } }); })();
    </script>
  @endpush
</x-admin.layout>
