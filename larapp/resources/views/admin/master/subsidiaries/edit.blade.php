<x-admin.layout title="Master - Subsidiaries">
  <div class="p-4">
    <h3>Edit Subsidiary</h3>
    <form method="POST" action="{{ route('admin.subsidiaries.update', $subsidiary->id) }}">
      @csrf @method('PUT')
      @include('admin.master.subsidiaries._form')
      <div style="margin-top:12px">
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('admin.subsidiaries.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</x-admin.layout>
