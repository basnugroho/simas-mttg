@component('components.administrator.layout')
  @php
          $titleSuffix = '';
          if(isset($selected_mosque) && $selected_mosque){
            $titleSuffix = ' — ' . ($selected_mosque->name ?? ('#' . $selected_mosque->id));
          }
        @endphp
  @slot('title') Aktivitas Masjid{{ $titleSuffix }} @endslot

   @section('header')
        <div class="col-sm-6"><h3 class="mb-0">Aktivitas Masjid</h3></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="{{ route('admin.mosques.index') }}">Kelola Masjid</a></li>
              <li class="breadcrumb-item active">Aktivitas Masjid</li>
            </ol>
        </div>
        @show

  @section('content')
  
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div>
      {{-- Create/edit form moved to dedicated pages. Index shows only the table for the selected mosque. --}}
      @php
        $ms = $selected_mosque ?? null;
      @endphp
      <div class="card">
        <div class="card-header">List Aktivitas Masjid{{ $ms ? (' — ' . ($ms->name ?? ('#' . $ms->id))) : '' }}
        </div>
        <div class="card-body">

        <form method="GET" style="margin-bottom:12px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
          <input type="text" name="filter_mosque" value="{{ request('filter_mosque') }}" placeholder="Cari nama masjid" class="form-control" style="min-width:160px;flex:1;min-width:0;">
          <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" style="min-width:140px">
          <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" style="min-width:140px">
          <button type="submit" class="btn btn-outline-secondary">Filter</button>

          @php $createUrl = route('admin.mosque_activities.create'); if(isset($selected_mosque) && $selected_mosque){ $createUrl .= '?mosque=' . $selected_mosque->id; } @endphp
          <a href="{{ $createUrl }}" class="btn btn-success me-2" style="margin-left:auto;">Buat Aktivitas</a>
        </form>

        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr class="text-left">
                <th class="p-2">Masjid</th>
                <th class="p-2">Region</th>
                <th class="p-2">Aktivitas</th>
                <th class="p-2">Rutin</th>
                <th class="p-2">Hari</th>
                <th class="p-2">Catatan</th>
                <th class="p-2">Pembuat</th>
                <th class="p-2">Photos</th>
                <th class="p-2">Aktif</th>
                <th class="p-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($assignments as $a)
                <tr>
                  <td class="p-2">{{ $a->mosque_name }}</td>
                  <td class="p-2">{{ $a->region_path ?? '-' }}</td>
                  <td class="p-2">{{ $a->activity_name }}</td>
                  <td class="p-2">{{ !empty($a->is_rutin) ? 'Ya' : 'Tidak' }}</td>
                  <td class="p-2">
                    @php
                      $days = [];
                      if(!empty($a->rutin_days)){
                        if(is_string($a->rutin_days)){
                          $decoded = @json_decode($a->rutin_days, true);
                          if(is_array($decoded)) $days = $decoded;
                        } elseif(is_array($a->rutin_days)){
                          $days = $a->rutin_days;
                        }
                      }
                    @endphp
                    {{ count($days) ? implode(', ', $days) : '-' }}
                  </td>
                  <td class="p-2">{{ $a->note ?? '-' }}</td>
                  <td class="p-2">{{ $a->activity_creator ?? '-' }}</td>
                  <td class="p-2">
                      @if(!empty($a->photos) && count($a->photos))
                        <div style="display:flex;gap:8px;align-items:center">
                          @foreach(array_slice($a->photos,0,3) as $p)
                            @php
                              $path = $p['path'] ?? '';
                              $url = $path;
                              try{
                                if($path && !(strpos($path, 'http') === 0 || strpos($path, '/') === 0)){
                                  $url = Storage::disk('public')->url($path);
                                }
                              } catch(\Throwable $__e) { $url = $path; }
                            @endphp
                            <a href="{{ $url }}" target="_blank" title="Lihat foto">
                              <img src="{{ $url }}" style="width:90px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #e6e6e6" alt="photo" />
                            </a>
                          @endforeach
                          @if(count($a->photos) > 3)
                            <div style="font-size:12px;color:#374151">+{{ count($a->photos) - 3 }}</div>
                          @endif
                        </div>
                      @else
                        -
                      @endif
                  </td>
                  <td class="p-2">
                    <input type="checkbox" class="assignment-active-toggle" data-id="{{ $a->id }}" {{ !empty($a->is_active) ? 'checked' : '' }} />
                  </td>
                  <td class="p-2">
                      <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.mosque_activities.edit', $a->id) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.mosque_activities.destroy', $a->id) }}" style="display:inline">@csrf @method('DELETE') <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus assignment?')">Hapus</button></form>
                  </td>
                </tr>

                <!-- photos inline now; expandable panel removed -->
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $assignments->links() }}
        </div>
        </div>
        @push('head')
          <style>
            @media (max-width:900px){
              form[method="GET"] { flex-direction:column !important; gap:8px; align-items:stretch }
              form[method="GET"] .form-control, form[method="GET"] .btn, form[method="GET"] a.btn { width:100% !important; box-sizing:border-box }
              .table-responsive table td .btn { display:block; width:100%; margin-bottom:6px }
              .table-responsive table td img { width:100%; height:auto; max-width:160px; object-fit:cover }
            }
          </style>
        @endpush
        </div>
      </div>
    </div>

  </div>
  @endsection
@endcomponent

<!-- Photo modals removed — thumbnails displayed inline in table for simplicity -->

{{-- Edit handled by server via ?edit_assignment; no client edit script required --}}
