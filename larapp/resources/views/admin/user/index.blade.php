@component('components.administrator.layout')
    @slot('title')
        {{ $title ?? 'Manage Password' }}
    @endslot

    @section('header')
    <div class="col-sm-6"><h3 class="mb-0">Manage Password</h3></div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Manage Password</a></li>
        </ol>
    </div>
    @show

    @section('content')
      <div class="card mb-4">
        <div class="card-header">
          <h3 class="card-title">Manage Password</h3>
        </div>
        <div class="card-body">
        @if(isset($user))
          @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
          @endif
          @if($errors->any())
            <div class="alert alert-danger">
              <ul style="margin:0;padding-left:18px">
                @foreach($errors->all() as $err)
                  <li>{{ $err }}</li>
                @endforeach
              </ul>
            </div>
          @endif
          <form method="POST" action="{{ route('admin.users.password.update', $user->id) }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">User</label>
              <div>{{ $user->name }} ({{ $user->username }}) &middot; <small>{{ $user->email }}</small></div>
            </div>
            <div class="mb-3">
              <label class="form-label">New Password</label>
              <input name="password" type="password" class="form-control @error('password') is-invalid @enderror" required minlength="6" />
            </div>
            <div class="mb-3">
              <label class="form-label">Confirm Password</label>
              <input name="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" required minlength="6" />
            </div>
            <div style="display:flex;gap:8px">
              <button class="btn btn-primary">Change Password</button>
              <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">Back to Users</a>
            </div>
          </form>
        @else
          <div>Silakan pilih user dari daftar untuk mengubah password.</div>
        @endif
        </div>
       
      </div>
    @show
@endcomponent
