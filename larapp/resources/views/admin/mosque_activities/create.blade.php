@component('components.administrator.layout')
 @php $ms = $selected_mosque ?? null; @endphp
    @slot('title') Tambah Aktivitas Masjid{{ $ms ? ' — ' . ($ms->name ?? ('#' . $ms->id)) : '' }} @endslot

     @section('header')
                <div class="col-sm-6"><h3 class="mb-0">Tambah Aktivitas Masjid{{ $ms ? ' — ' . ($ms->name ?? ('#' . $ms->id)) : '' }}</h3></div>
                <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.mosque_activities.index') }}">Aktivitas Masjid</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                </div>
                @show

    @section('content')
  
               

                @include('admin.mosque_activities._form', ['editing' => null])

    @endsection
@endcomponent
