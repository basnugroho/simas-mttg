@php use Illuminate\Support\Facades\Schema; @endphp
@if(Schema::hasColumn('regions', 'level'))
<div class="form-group">
  <label>Level</label>
  <select name="level" class="form-control" required>
    @php
      // Prefer explicit level, fall back to type_key or legacy type mapping so edit forms for older records still show a value
      $curLevel = old('level', $region->level ?? $region->type_key ?? \App\Models\Regions::legacyToTypeKey($region->type ?? null) ?? '');
    @endphp
    <option value="" disabled {{ $curLevel === '' ? 'selected' : '' }}>-- select level --</option>
    @php $levelsForSelect = $allowedLevels ?? \App\Models\Regions::LEVELS; @endphp
    @foreach($levelsForSelect as $lvl)
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
  {{-- Administration Type: allow explicit selection of legacy DB enum values to avoid NULL inserts --}}
  @php
    $typeOptions = array_keys(\App\Models\Regions::LEGACY_MAP);
    $curType = old('type', $region->type ?? (isset($region->type_key) ? \App\Models\Regions::typeKeyToLegacy($region->type_key) : ''));
  @endphp
  <div class="form-group">
    <label>Administration Type</label>
    <select name="type" id="type-select" class="form-control">
      <option value="">-- select administration type --</option>
      @foreach($typeOptions as $to)
        @php $label = \App\Models\Regions::legacyToTypeKey($to) ? (\App\Models\Regions::TYPES[\App\Models\Regions::legacyToTypeKey($to)] ?? $to) : $to; @endphp
        <option value="{{ $to }}" {{ $curType === $to ? 'selected' : '' }}>{{ $label }} ({{ $to }})</option>
      @endforeach
    </select>
    @error('type')<div class="text-danger">{{ $message }}</div>@enderror
  </div>
  @error('type')<div class="text-danger">{{ $message }}</div>@enderror
</div>

@push('scripts')
<script>
  (function(){
    // When Level changes, derive type_key and legacy `type` automatically.
    var levelSelect = document.querySelector('select[name="level"]');
  var typeKeyHidden = document.getElementById('type-key-hidden');
  var typeSelect = document.getElementById('type-select');
  if (!levelSelect || !typeKeyHidden || !typeSelect) return;

    var legacyMap = {
      AREA: 'PROVINCE',
      STO: 'CITY',
      WITEL: 'WITEL'
    };

    function syncFromLevel(){
      var lvl = (levelSelect.value || '').toUpperCase();
      // set canonical type_key to the level
      typeKeyHidden.value = lvl;

      // set legacy `type` selection when mapping exists
      var legacy = legacyMap[lvl] || '';
      if (!legacy) {
        // clear selection but keep select present so user can pick
        try { typeSelect.value = ''; } catch(e){}
      } else {
        try { typeSelect.value = legacy; } catch(e){}
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

    // Cache original parent options so we can rebuild the select deterministically
    var originalParentOptions = Array.from(parentSelect.options).map(function(o){
      return { value: o.value, text: o.textContent, level: o.getAttribute('data-level') || '' };
    });

    function filterParentsByLevel(){
      var childLevel = levelSelect.value || '';
      var parentLevel = parentLevelMap[childLevel] || null;
      var selectedVal = parentSelect.value || '';

      // debug logging to help identify why select may appear as free-text in some browsers
      try{ console.debug('[regions.form] childLevel=', childLevel, 'parentLevel=', parentLevel, 'selectedParent=', selectedVal, 'originalOptions=', originalParentOptions.length); }catch(e){}

      // If there is no parentLevel defined for this level, hide the whole parent selector
      if (!parentLevel) {
        parentGroup.style.display = 'none';
        // keep selected value but don't allow changing it
        parentSelect.innerHTML = '';
        var ph = document.createElement('option'); ph.value = ''; ph.text = '-- none --'; parentSelect.appendChild(ph);
        if (selectedVal) {
          var selOpt = document.createElement('option'); selOpt.value = selectedVal; selOpt.text = selectedVal; selOpt.selected = true; parentSelect.appendChild(selOpt);
        }
        return;
      }

      // otherwise ensure parent group is visible and rebuild options to only matching level (plus current selection)
      parentGroup.style.display = '';
      parentSelect.innerHTML = '';
      var ph = document.createElement('option'); ph.value = ''; ph.text = '-- none --'; parentSelect.appendChild(ph);
      originalParentOptions.forEach(function(opt){
        if (!opt.value) return;
        if (opt.value === selectedVal || (opt.level || '').toUpperCase() === parentLevel.toUpperCase()){
          var el = document.createElement('option'); el.value = opt.value; el.text = opt.text; el.setAttribute('data-level', opt.level || '');
          if (opt.value === selectedVal) el.selected = true;
          parentSelect.appendChild(el);
        }
      });
      try{ console.debug('[regions.form] rebuilt options count=', parentSelect.options.length); }catch(e){}
      // if rebuild resulted in only placeholder (or single option), fallback to full options so user always has choices
      if (parentSelect.options.length <= 1) {
        // restore full original list
        parentSelect.innerHTML = '';
        var ph2 = document.createElement('option'); ph2.value = ''; ph2.text = '-- none --'; parentSelect.appendChild(ph2);
        originalParentOptions.forEach(function(opt){
          var el2 = document.createElement('option'); el2.value = opt.value; el2.text = opt.text; el2.setAttribute('data-level', opt.level || '');
          if (opt.value === selectedVal) el2.selected = true;
          parentSelect.appendChild(el2);
        });
        try{ console.debug('[regions.form] fallback to full options, count=', parentSelect.options.length); }catch(e){}
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
  <div style="font-size:12px;color:#6b7280;margin-bottom:6px">(debug: parents server-side: {{ count($parents ?? []) }}, current parent_id: {{ $region->parent_id ?? 'none' }})</div>
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
