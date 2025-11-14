<x-admin.layout title="Create Mosque">
  <div class="p-4">
    <h3>Create Mosque</h3>
    <form method="POST" action="{{ route('admin.mosques.store') }}">
      @csrf
      @include('admin.master.mosques._form')
      <div style="margin-top:12px">
        <button class="btn btn-primary">Create</button>
        <a href="{{ route('admin.mosques.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</x-admin.layout>
