<div>
  <div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-input" value="{{ old('name', $mosque->name ?? '') }}" required />
  </div>
  <div class="mb-3">
    <label class="form-label">Province</label>
    <select name="province_id" class="form-input">
      <option value="">-- Select Province --</option>
      @foreach(($provinces ?? []) as $p)
        <option value="{{ $p->id }}" {{ (old('province_id', $mosque->province_id ?? '') == $p->id) ? 'selected' : '' }}>{{ $p->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Address</label>
    <input type="text" name="address" class="form-input" value="{{ old('address', $mosque->address ?? '') }}" />
  </div>
</div>
