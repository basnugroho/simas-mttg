<div class="form-group">
  <label>Name</label>
  <input type="text" name="name" value="{{ old('name', $region->name ?? '') }}" class="form-control" />
  @error('name')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="form-group">
  <label>Type</label>
  <input type="text" name="type" value="{{ old('type', $region->type ?? '') }}" class="form-control" />
  @error('type')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="form-group">
  <label>Code</label>
  <input type="text" name="code" value="{{ old('code', $region->code ?? '') }}" class="form-control" />
  @error('code')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="form-group">
  <label>Parent</label>
  <select name="parent_id" class="form-control">
    <option value="">-- none --</option>
    @foreach($parents ?? [] as $p)
      <option value="{{ $p->id }}" {{ (old('parent_id', $region->parent_id ?? '') == $p->id) ? 'selected' : '' }}>{{ $p->name }}</option>
    @endforeach
  </select>
  @error('parent_id')<div class="text-danger">{{ $message }}</div>@enderror
</div>
