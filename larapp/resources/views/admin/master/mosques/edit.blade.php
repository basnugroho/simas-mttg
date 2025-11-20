<x-admin.layout title="Edit Mosque">
  <div class="p-4">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
      <a href="{{ route('admin.mosques.index') }}" class="btn btn-light" style="display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:8px">&larr; Back</a>
      <h3 style="margin:0">Edit Mosque</h3>
    </div>
    <form method="POST" action="{{ route('admin.mosques.update', $mosque->id) }}" enctype="multipart/form-data">
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
