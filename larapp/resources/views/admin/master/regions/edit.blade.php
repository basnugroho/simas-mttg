<x-admin.layout title="Edit Region">
  <div class="p-4">
    <h3>Edit Region</h3>
    <form method="POST" action="{{ route('admin.regions.update', $region->id) }}">
      @csrf
      @method('PUT')
      @include('admin.master.regions._form')
      <div style="margin-top:12px">
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('admin.regions.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</x-admin.layout>
