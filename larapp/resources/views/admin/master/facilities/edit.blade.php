<x-admin.layout title="Edit Facility">
  <div class="p-4">
    <h3>Edit Facility</h3>
    <form method="POST" action="{{ route('admin.facilities.update', $facility->id) }}">
      @csrf
      @method('PUT')
      @include('admin.master.facilities._form')
      <div style="margin-top:12px">
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('admin.facilities.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</x-admin.layout>
