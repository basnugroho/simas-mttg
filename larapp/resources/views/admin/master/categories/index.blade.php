<x-admin.layout title="Categories">
  <div class="p-4">
    <div class="flex justify-between items-center mb-4">
      <h3>Categories</h3>
      <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Create Category</a>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Slug</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($categories as $cat)
          <tr>
            <td>{{ $loop->iteration + ($categories->currentPage()-1)*$categories->perPage() }}</td>
            <td>{{ $cat->name }}</td>
            <td>{{ $cat->slug }}</td>
            <td>
              <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-sm">Edit</a>
              <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this category?')">Delete</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    {{ $categories->links() }}
  </div>
</x-admin.layout>
