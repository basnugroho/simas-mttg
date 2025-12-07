@component('components.administrator.layout')
  @slot('title') Edit Mosque @endslot

   @section('header')
        <div class="col-sm-6"><h3 class="mb-0">Edit Mosque</h3></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="{{ route('admin.mosques.index') }}">Kelola Masjid</a></li>
              <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
        @show

  @section('content')
  <div class="card">
    <div class="card-header">Edit Mosque</div>
    <div class="card-body p-4">

      <form method="POST" action="{{ route('admin.mosques.update', $mosque->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.master.mosques._form')
        <div style="margin-top:12px">
          <button class="btn btn-primary">Save</button>
          <a href="{{ route('admin.mosques.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
  @endsection
@endcomponent
