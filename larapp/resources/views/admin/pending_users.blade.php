<x-admin.layout title="Pending Registrations">
  <div class="container mt-6 admin-content">
    <h2>Pending Registrations</h2>

    @if(session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if($pending->isEmpty())
      <p>Tidak ada pendaftar yang menunggu.</p>
    @else
      <table class="table">
        <thead>
          <tr><th>#</th><th>Name</th><th>Username</th><th>Email</th><th>Action</th></tr>
        </thead>
        <tbody>
          @foreach($pending as $u)
            <tr>
              <td>{{ $u->id }}</td>
              <td>{{ $u->name }}</td>
              <td>{{ $u->username }}</td>
              <td>{{ $u->email }}</td>
              <td>
                <form method="POST" action="{{ route('admin.approve', $u->id) }}">
                  @csrf
                  <button class="btn btn-primary">Approve & Promote</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</x-admin.layout>
