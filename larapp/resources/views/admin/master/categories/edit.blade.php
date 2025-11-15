<x-admin.layout title="Edit Category">
  <div class="p-4">
    <h3>Edit Category</h3>
    <form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
      @csrf
      @method('PUT')
      @include('admin.master.categories._form')
      <div style="margin-top:12px">
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</x-admin.layout>
