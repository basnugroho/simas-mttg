@component('components.administrator.layout')
    @slot('title')
        {{ $title ?? 'Regional' }}
    @endslot

    @section('header')
    <div class="col-sm-6"><h3 class="mb-0">Regional</h3></div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Regional</a></li>
        </ol>
    </div>
    @show

    @section('content')
     @php
          $me = auth()->user();
          $effective = [];
          try { if($me) $effective = $me->getEffectiveRegionIds(); } catch(\Throwable $__e) { $effective = []; }
          $canCreateAny = $me && ($me->isWebmaster() || count($effective)>0);
        @endphp
 <div class="card mb-4">
        <div class="card-header">
          <h3 class="card-title">List Regional</h3>
        </div>
        <div class="card-body">
          <form method="GET" style="margin-bottom:12px;display:flex;gap:8px;align-items:center;">

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
      <a href="{{ route('admin.regions.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset filters">Reset</a>
      
      <div style="margin-left:auto;">
       @if($canCreateAny)
          <a href="{{ route('admin.regions.create') }}" class="btn btn-sm btn-primary">Create Region</a>
        @else
          <button class="btn btn-sm btn-primary" disabled title="Create disabled: no allowed parent region">Create Region 🔒</button>
        @endif
      </div>
    </form>
        </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="card-body p-0 border-top" style="padding-top:8px;">
    <table class="table table-sm table-striped" role="table">
      <thead>
        <tr>
          <th>
            <a class="text-dark text-decoration-none" style="color:#000;text-decoration:none;" href="{{ request()->fullUrlWithQuery(['sort'=>'id','dir' => (request('sort')=='id' && request('dir')=='asc') ? 'desc' : 'asc']) }}">
              #
              @if(request('sort')=='id')
                @if(request('dir')=='asc') ▲ @else ▼ @endif
              @endif
            </a>
          </th>
          <th>
            <a class="text-dark text-decoration-none" style="color:#000;text-decoration:none;" href="{{ request()->fullUrlWithQuery(['sort'=>'name','dir' => (request('sort')=='name' && request('dir')=='asc') ? 'desc' : 'asc']) }}">
              Name
              @if(request('sort')=='name')
                @if(request('dir')=='asc') ▲ @else ▼ @endif
              @endif
            </a>
          </th>
          <th>
            <a class="text-dark text-decoration-none" style="color:#000;text-decoration:none;" href="{{ request()->fullUrlWithQuery(['sort'=>'pov','dir' => (request('sort')=='pov' && request('dir')=='asc') ? 'desc' : 'asc']) }}">
              POV
              @if(request('sort')=='pov')
                @if(request('dir')=='asc') ▲ @else ▼ @endif
              @endif
            </a>
          </th>
          <th>
            <a class="text-dark text-decoration-none" style="color:#000;text-decoration:none;" href="{{ request()->fullUrlWithQuery(['sort'=>'type','dir' => (request('sort')=='type' && request('dir')=='asc') ? 'desc' : 'asc']) }}">
              Type
              @if(request('sort')=='type')
                @if(request('dir')=='asc') ▲ @else ▼ @endif
              @endif
            </a>
          </th>
          <th>
            <a class="text-dark text-decoration-none" style="color:#000;text-decoration:none;" href="{{ request()->fullUrlWithQuery(['sort'=>'code','dir' => (request('sort')=='code' && request('dir')=='asc') ? 'desc' : 'asc']) }}">
              Code
              @if(request('sort')=='code')
                @if(request('dir')=='asc') ▲ @else ▼ @endif
              @endif
            </a>
          </th>
          <th>
            <a class="text-dark text-decoration-none" style="color:#000;text-decoration:none;" href="{{ request()->fullUrlWithQuery(['sort'=>'parent','dir' => (request('sort')=='parent' && request('dir')=='asc') ? 'desc' : 'asc']) }}">
              Parent
              @if(request('sort')=='parent')
                @if(request('dir')=='asc') ▲ @else ▼ @endif
              @endif
            </a>
          </th>
          <th>
            <a class="text-dark text-decoration-none" style="color:#000;text-decoration:none;" href="{{ request()->fullUrlWithQuery(['sort'=>'level','dir' => (request('sort')=='level' && request('dir')=='asc') ? 'desc' : 'asc']) }}">
              Level
              @if(request('sort')=='level')
                @if(request('dir')=='asc') ▲ @else ▼ @endif
              @endif
            </a>
          </th>
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

    @show
@endcomponent
  @push('scripts')
    <script>
      // keyboard shortcut: Alt+Shift+D to go back to dashboard
      (function(){ document.addEventListener('keydown', function(e){ if(e.altKey && e.shiftKey && String(e.key).toLowerCase() === 'd'){ window.location = '{{ route("dashboard") }}'; } }); })();
    </script>
  @endpush

