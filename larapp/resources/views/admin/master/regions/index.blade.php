<x-admin.layout title="Master - Regions">
  <div class="p-4">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <div style="display:flex;align-items:center;gap:12px">
        <div>
          <h3 style="margin:0">Regions</h3>
          <div style="font-size:12px;color:#6b7280;margin-top:4px">Master · <a href="{{ route('dashboard') }}">Dashboard</a> / <strong>Regions</strong></div>
        </div>
      </div>
      <div style="display:flex;gap:8px;align-items:center">
        @php
          $me = auth()->user();
          $effective = [];
          try { if($me) $effective = $me->getEffectiveRegionIds(); } catch(\Throwable $__e) { $effective = []; }
          $canCreateAny = $me && ($me->isWebmaster() || count($effective)>0);
        @endphp
        @if($canCreateAny)
          <a href="{{ route('admin.regions.create') }}" class="btn btn-sm btn-primary">Create Region</a>
        @else
          <button class="btn btn-sm btn-primary" disabled title="Create disabled: no allowed parent region">Create Region 🔒</button>
        @endif
      </div>
    </div>

    <form method="GET" style="margin-bottom:12px;display:flex;gap:8px;align-items:center">
      <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search name" class="form-control" style="width:260px;display:inline-block" />
      <select name="pov" class="form-control" style="width:220px">
        <option value="">-- POV / Ordering --</option>
        @foreach(\App\Models\Regions::POVS as $povKey => $povLabel)
          <option value="{{ $povKey }}" {{ (isset($pov) && $pov === $povKey) ? 'selected' : '' }}>{{ $povLabel }}</option>
        @endforeach
      </select>
      <select name="level" class="form-control" style="width:160px">
        <option value="">-- Level --</option>
        <option value="ALL" {{ (isset($level) && $level === 'ALL') ? 'selected' : '' }}>All</option>
        @foreach(\App\Models\Regions::LEVELS as $lvl)
          <option value="{{ $lvl }}" {{ (isset($level) && $level === $lvl) ? 'selected' : '' }}>{{ $lvl }}</option>
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
        @if(isset($regions) && $regions->count())
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
            @php
              // `getEffectiveRegionIds()` already includes assigned regions and their descendants,
              // so it's sufficient to test the region's id against the cached effective set.
              $inScope = ($me && $me->isWebmaster()) || in_array((int)$r->id, $effective);
            @endphp
            @if($inScope)
              <a href="{{ route('admin.regions.edit', $r->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
              <form method="POST" action="{{ route('admin.regions.destroy', $r->id) }}" style="display:inline-block" onsubmit="return confirm('Delete region?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger">Delete</button>
              </form>
            @else
              <button class="btn btn-sm btn-outline-secondary" disabled title="Edit disabled: region outside your scope">Edit 🔒</button>
              <button class="btn btn-sm btn-danger" disabled title="Delete disabled: region outside your scope">Delete 🔒</button>
            @endif
          </td>
        </tr>
          @endforeach
        @else
          <tr>
            <td colspan="8" class="text-center" style="color:#6b7280;padding:18px">No regions found. You can <a href="{{ route('admin.regions.create') }}">create a region</a> if you have permission.</td>
          </tr>
        @endif
      </tbody>
    </table>

    {{ $regions->withQueryString()->links() }}
  </div>
  </div>

  @push('scripts')
    <script>
      // keyboard shortcut: Alt+Shift+D to go back to dashboard
      (function(){ document.addEventListener('keydown', function(e){ if(e.altKey && e.shiftKey && String(e.key).toLowerCase() === 'd'){ window.location = '{{ route("dashboard") }}'; } }); })();
    </script>
  @endpush
</x-admin.layout>
