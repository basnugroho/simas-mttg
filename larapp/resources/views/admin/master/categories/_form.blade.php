<div>
  <div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-input" value="{{ old('name', $category->name ?? '') }}" required />
  </div>
  <div class="mb-3">
    <label class="form-label">Slug</label>
    <input type="text" name="slug" class="form-input" value="{{ old('slug', $category->slug ?? '') }}" />
  </div>
</div>
