@component('components.administrator.layout')
    @slot('title')
        {{ $title ?? 'MTTG - Subsidiaries' }}
    @endslot

    @section('header')
    <div class="col-sm-6"><h3 class="mb-0">Subsidiaries</h3></div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active"><a href="{{ route('admin.subsidiaries.index') }}">Subsidiaries</a></li>
          <li class="breadcrumb-item active" aria-current="page">Create Subsidiary</li>
        </ol>
    </div>
    @show
    @section('content')       
      <div class="card">
        <div class="card-header">Create Subsidiary</div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.subsidiaries.store') }}">
            @csrf
            @include('admin.master.subsidiaries._form')
            <div style="margin-top:12px">
              <button class="btn btn-primary">Save</button>
              <a href="{{ route('admin.subsidiaries.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    @show
@endcomponent
