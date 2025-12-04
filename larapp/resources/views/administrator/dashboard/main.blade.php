@component('components.administrator.layout')
	@slot('title')
		{{ $title ?? 'MTTG - Dashboard' }}
	@endslot

	@section('header')
    <div class="col-sm-6"><h3 class="mb-0"></h3></div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="#">Beranda</a></li>
        </ol>
    </div>
    @show

	@section('content')
		
	@show
@endcomponent
