@component('components.administrator.layout')
    @slot('title')
        {{ $title ?? 'MTTG - Regional' }}
    @endslot

    @section('header')
    <div class="col-sm-6"><h3 class="mb-0">Regional</h3></div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active"><a href="{{ route('admin.regions.index') }}">Regional</a></li>
          <li class="breadcrumb-item active" aria-current="page">Edit Regional</li>
        </ol>
    </div>
    @show
    @section('content')       
      <div class="card">
        <div class="card-header">Edit Regional</div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.regions.update', $region->id) }}">
            @csrf
            @method('PUT')
            @include('admin.master.regions._form')
            <div style="margin-top:12px">
              <button class="btn btn-primary">Save</button>
              <a href="{{ route('admin.regions.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
         </form>
        </div>
      </div>
    @show
@endcomponent

