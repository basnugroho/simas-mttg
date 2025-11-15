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
