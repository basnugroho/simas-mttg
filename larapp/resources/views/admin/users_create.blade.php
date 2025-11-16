<x-admin.layout title="Create User">
  <div class="container admin-content">
    <h2>Create User</h2>

    @if(session('status'))
      <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" value="{{ old('name') }}" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input name="username" class="form-control" value="{{ old('username') }}" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" value="{{ old('email') }}" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Password (optional)</label>
        <input name="password" type="password" class="form-control" />
        <div class="form-text">If left blank a random password will be generated.</div>
      </div>

      <hr />
      <h4>Assign region privilege (optional)</h4>
      <div class="mb-3">
        <label class="form-label">Target admin role</label>
        <select name="role_key" class="form-select">
          <option value="">(none)</option>
          <option value="admin_regional">Admin Regional</option>
          <option value="admin_area">Admin Area</option>
          <option value="admin_witel">Admin Witel</option>
          <option value="admin_sto">Admin STO</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Regions (multiple)</label>
        <select name="region_ids[]" multiple class="form-select">
          @foreach($regions as $r)
            <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->displayTypeLabel() }})</option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label class="form-check-label">
          <input type="checkbox" name="approved" value="1" class="form-check-input" /> Approved
        </label>
      </div>

      <div style="display:flex;gap:8px">
        <button class="btn btn-primary">Create</button>
        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">Back to Users</a>
      </div>
    </form>
  </div>
</x-admin.layout>
