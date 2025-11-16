@php use Illuminate\Support\Facades\Schema; @endphp
@if(Schema::hasColumn('regions', 'level'))
<div class="form-group">
  <label>Level</label>
  <select name="level" class="form-control" required>
    @php $curLevel = old('level', $region->level ?? ''); @endphp
    <option value="" disabled {{ $curLevel === '' ? 'selected' : '' }}>-- select level --</option>
    @foreach(\App\Models\Regions::LEVELS as $lvl)
      <option value="{{ $lvl }}" {{ $curLevel === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
    @endforeach
  </select>
  
  @error('level')<div class="text-danger">{{ $message }}</div>@enderror
</div>
@endif

@if($errors->has('db'))
  <div class="alert alert-danger">{{ $errors->first('db') }}</div>
@endif

<div class="form-group">
  <label>Name</label>
  <input type="text" name="name" value="{{ old('name', $region->name ?? '') }}" class="form-control" />
  @error('name')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="form-group">
  <label>Code</label>
  <input type="text" name="code" id="code-input" value="{{ old('code', $region->code ?? '') }}" class="form-control" />
  <div class="form-text" id="code-help-text"></div>
  @error('code')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="form-group">
  {{-- For now we default to Telkom (Old) POV and hide the POV selector from create UI --}}
  <input type="hidden" name="pov" value="TELKOM_OLD" />

  {{-- type_key is derived from Level; keep a hidden input so controller receives it. --}}
  <input type="hidden" name="type_key" id="type-key-hidden" value="{{ old('type_key', $region->type_key ?? $region->level ?? '') }}" />
  {{-- Keep legacy `type` in sync via hidden input for DB enum compatibility. --}}
  <input type="hidden" name="type" id="type-hidden" value="{{ old('type', $region->type ?? '') }}" />
  @error('type')<div class="text-danger">{{ $message }}</div>@enderror
</div>

@push('scripts')
<script>
  (function(){
    // When Level changes, derive type_key and legacy `type` automatically.
    var levelSelect = document.querySelector('select[name="level"]');
    var typeKeyHidden = document.getElementById('type-key-hidden');
    var typeHidden = document.getElementById('type-hidden');
    if (!levelSelect || !typeKeyHidden || !typeHidden) return;

    var legacyMap = {
      AREA: 'PROVINCE',
      STO: 'CITY',
      WITEL: 'WITEL'
    };

    function syncFromLevel(){
      var lvl = (levelSelect.value || '').toUpperCase();
      // set canonical type_key to the level
      typeKeyHidden.value = lvl;

      // set legacy `type` when mapping exists
      var legacy = legacyMap[lvl] || '';
      if (!legacy) {
        typeHidden.removeAttribute('name');
        typeHidden.value = '';
        typeHidden.disabled = true;
      } else {
        typeHidden.setAttribute('name', 'type');
        typeHidden.disabled = false;
        typeHidden.value = legacy;
      }
    }

    levelSelect.addEventListener('change', syncFromLevel);
    // sync initially
    syncFromLevel();
    // also sync on form submit
    var form = levelSelect.closest('form');
    if (form) form.addEventListener('submit', syncFromLevel);
  })();
</script>
<script>
  (function(){
    // Filter parent options based on selected level (one level up in hierarchy)
    var levelSelect = document.querySelector('select[name="level"]');
  var parentSelect = document.querySelector('select[name="parent_id"]');
  var parentGroup = document.getElementById('parent-group');
  if (!levelSelect || !parentSelect || !parentGroup) return;

    var parentLevelMap = {
      'AREA': 'REGIONAL',
      'WITEL': 'AREA',
      'STO': 'WITEL'
      // REGIONAL / OTHER -> no parent
    };

    function filterParentsByLevel(){
      var childLevel = levelSelect.value || '';
      var parentLevel = parentLevelMap[childLevel] || null;

      // If there is no parentLevel defined for this level, hide the whole parent selector
      if (!parentLevel) {
        parentGroup.style.display = 'none';
        // clear any previously selected parent to avoid saving an invalid parent relationship
        parentSelect.value = '';
        // also disable all options (except the placeholder) to be safe
        for (var i=0;i<parentSelect.options.length;i++){
          var opt = parentSelect.options[i];
          if (!opt.value) { opt.hidden = false; opt.disabled = false; continue; }
          opt.hidden = true; opt.disabled = true;
        }
        return;
      }

      // otherwise ensure parent group is visible and only matching-level parents are selectable
      parentGroup.style.display = '';
      for (var i=0;i<parentSelect.options.length;i++){
        var opt = parentSelect.options[i];
        if (!opt.value) { opt.hidden = false; opt.disabled = false; continue; }
        var lvl = opt.getAttribute('data-level') || '';
        if (lvl.toUpperCase() === parentLevel.toUpperCase()) {
          opt.hidden = false; opt.disabled = false;
        } else {
          opt.hidden = true; opt.disabled = true;
        }
      }
    }

    levelSelect.addEventListener('change', filterParentsByLevel);
    // run once on load
    filterParentsByLevel();
  })();
</script>
  <script>
    (function(){
      // Show contextual CODE format hints depending on selected level
      var levelSelect = document.querySelector('select[name="level"]');
      var codeHelp = document.getElementById('code-help-text');
      if (!levelSelect || !codeHelp) return;

      function updateCodeHelp(){
        var lvl = (levelSelect.value || '').toUpperCase();
        if (lvl === 'WITEL'){
          codeHelp.innerHTML = 'Format: <strong>WITEL-[Tiga huruf singkatan Witel]</strong>, contoh <code>WITEL-SBU</code>';
        } else if (lvl === 'STO'){
          codeHelp.innerHTML = 'Format: <strong>[Tiga Huruf Kode STO]</strong>, contoh <code>RKT</code>';
        } else {
          codeHelp.innerHTML = '';
        }
      }

      levelSelect.addEventListener('change', updateCodeHelp);
      // init
      updateCodeHelp();
    })();
  </script>
@endpush

<div class="form-group" id="parent-group">
  <label>Parent</label>
  <select name="parent_id" class="form-control">
    <option value="">-- none --</option>
    @foreach($parents ?? [] as $p)
      @php $typeLabel = method_exists($p, 'displayTypeLabel') ? $p->displayTypeLabel() : ($p->type ?? ''); @endphp
      @php $pTypeKey = $p->type_key ?? \App\Models\Regions::legacyToTypeKey($p->type ?? null); @endphp
      @php $pLevel = $p->level ?? ''; @endphp
      <option value="{{ $p->id }}" data-type-key="{{ $pTypeKey }}" data-level="{{ $pLevel }}" {{ (old('parent_id', $region->parent_id ?? '') == $p->id) ? 'selected' : '' }}>{{ $p->name }}{{ $typeLabel ? " ({$typeLabel})" : '' }}</option>
    @endforeach
  </select>
  @error('parent_id')<div class="text-danger">{{ $message }}</div>@enderror
</div>

{{-- POV mappings are hidden for now to simplify the create UI (we default to Telkom Old) --}}
