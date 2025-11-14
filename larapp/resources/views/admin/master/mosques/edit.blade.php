<x-admin.layout title="Edit Mosque">
  <div class="p-4">
    <h3>Edit Mosque</h3>
    <form method="POST" action="{{ route('admin.mosques.update', $mosque->id) }}">
      @csrf
      @method('PUT')
      @include('admin.master.mosques._form')
      <div style="margin-top:12px">
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('admin.mosques.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</x-admin.layout>
