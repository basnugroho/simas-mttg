<x-admin.layout title="User Management">
  <div class="container admin-content">
    <h2>User Management</h2>

    @if(session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <table class="table table-striped">
      <thead>
        <tr><th>#</th><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Approved</th><th>Action</th></tr>
      </thead>
      <tbody>
        @foreach($users as $u)
          <tr>
            <td>{{ $u->id }}</td>
            <td>{{ $u->name }}</td>
            <td>{{ $u->username }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->role }}</td>
            <td>{{ $u->approved ? 'Yes' : 'No' }}</td>
            <td>
              <form method="POST" action="{{ route('admin.users.update', $u->id) }}" class="d-flex gap-2">
                @csrf
                <select name="role" class="form-select form-select-sm">
                  <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>user</option>
                  <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>admin</option>
                  <option value="webmaster" {{ $u->role === 'webmaster' ? 'selected' : '' }}>webmaster</option>
                </select>
                <label class="form-check-label ms-2">
                  <input type="checkbox" name="approved" value="1" class="form-check-input" {{ $u->approved ? 'checked' : '' }}> Approved
                </label>
                <button class="btn btn-sm btn-primary">Save</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    {{ $users->links() }}
  </div>
</x-admin.layout>
