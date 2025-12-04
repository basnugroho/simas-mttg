@component('components.administrator.layout')
    @slot('title')
        {{ $title ?? 'MTTG - Activities' }}
    @endslot

    @section('header')
    <div class="col-sm-6"><h3 class="mb-0">Activities</h3></div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active"><a href="{{ route('admin.activities.index') }}">Activities</a></li>
          <li class="breadcrumb-item active" aria-current="page">Create Activity</li>
        </ol>
    </div>
    @show
    @section('content')       
      <div class="card">
        <div class="card-header">Create Activity</div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.activities.store') }}">
            @csrf
            @include('admin.master.activities._form')
            <div style="margin-top:12px">
              <button class="btn btn-primary">Save</button>
              <a href="{{ route('admin.activities.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    @show
@endcomponent
