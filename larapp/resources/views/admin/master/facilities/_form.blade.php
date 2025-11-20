<div class="form-group">
  <label>Name</label>
  <input type="text" name="name" value="{{ old('name', $facility->name ?? '') }}" class="form-control" />
  @error('name')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="form-group">
  <label>Slug</label>
  <input type="text" name="slug" value="{{ old('slug', $facility->slug ?? '') }}" class="form-control" />
  @error('slug')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="form-group">
  <label><input type="checkbox" name="is_required" value="1" {{ old('is_required', $facility->is_required ?? false) ? 'checked' : '' }}> Is required</label>
</div>

<div class="form-group">
  <label>Unit</label>
  <select name="unit_id" class="form-control">
    <option value="">-- none --</option>
    @foreach(\App\Models\FacilityUnit::orderBy('name')->get() as $u)
      <option value="{{ $u->id }}" {{ (string)old('unit_id', $facility->unit_id ?? '') === (string)$u->id ? 'selected' : '' }}>{{ $u->name }}</option>
    @endforeach
  </select>
  @error('unit_id')<div class="text-danger">{{ $message }}</div>@enderror
</div>
