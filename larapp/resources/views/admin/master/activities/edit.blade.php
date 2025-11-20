<x-admin.layout title="Master - Activities">
  <div class="p-4">
    <h3>Edit Activity</h3>
    <form method="POST" action="{{ route('admin.activities.update', $activity->id) }}">
      @csrf @method('PUT')
      @include('admin.master.activities._form')
      <div style="margin-top:12px">
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('admin.activities.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</x-admin.layout>
