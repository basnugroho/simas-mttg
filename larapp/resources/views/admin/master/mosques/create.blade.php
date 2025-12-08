@component('components.administrator.layout')
  @slot('title') Create Mosque @endslot

   @section('header')
        <div class="col-sm-6"><h3 class="mb-0">Create Mosque</h3></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="{{ route('admin.mosques.index') }}">Kelola Masjid</a></li>
              <li class="breadcrumb-item active">Create</li>
            </ol>
        </div>
        @show

  @section('content')
  
    <div class="card">
      <div class="card-header">Create Mosque</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.mosques.store') }}" enctype="multipart/form-data">
          @csrf
          @include('admin.master.mosques._form')

          <div style="margin-top:12px">
            <button class="btn btn-primary">Create</button>
            <a href="{{ route('admin.mosques.index') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>

  @endsection
@endcomponent
