<x-admin.layout title="Create Category">
  <div class="p-4">
    <h3>Create Category</h3>
    <form method="POST" action="{{ route('admin.categories.store') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-input" value="{{ old('name') }}" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" class="form-input" value="{{ old('slug') }}" />
      </div>
      <button class="btn btn-primary">Create</button>
      <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
  </div>
</x-admin.layout>
