<x-admin.layout title="Aktivitas Masjid">
  <div class="p-4">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <div>
        @php
          $titleSuffix = '';
          if(isset($selected_mosque) && $selected_mosque){
            $titleSuffix = ' — ' . ($selected_mosque->name ?? ('#' . $selected_mosque->id));
          }
        @endphp
        <h3 style="margin:0">Aktivitas Masjid{{ $titleSuffix }}</h3>
        <div style="font-size:12px;color:#6b7280;margin-top:4px">Masjid · <a href="{{ route('admin.mosques.index') }}">Kelola Masjid</a> / <strong>Aktivitas Masjid</strong></div>
      </div>
        <div>
          @php $createUrl = route('admin.mosque_activities.create'); if(isset($selected_mosque) && $selected_mosque){ $createUrl .= '?mosque=' . $selected_mosque->id; } @endphp
          <a href="{{ $createUrl }}" class="btn btn-primary me-2">Buat Aktivitas</a>
          @if(isset($selected_mosque) && $selected_mosque)
              <a href="{{ route('admin.mosques.index') }}" class="btn btn-secondary">Kembali ke Masjid</a>
          @endif
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div>
      {{-- Create/edit form moved to dedicated pages. Index shows only the table for the selected mosque. --}}
      @php
        $ms = $selected_mosque ?? null;
      @endphp
      <div class="p-4 border rounded" style="background:#fff">
        <h3 class="font-semibold">Tabel Aktivitas Masjid{{ $ms ? (' — ' . ($ms->name ?? ('#' . $ms->id))) : '' }}</h3>

      
        <form method="GET" class="mb-3 flex space-x-2">
          <input type="text" name="filter_mosque" value="{{ request('filter_mosque') }}" placeholder="Cari nama masjid" class="border p-2 w-1/3">
          <input type="date" name="date_from" value="{{ request('date_from') }}" class="border p-2">
          <input type="date" name="date_to" value="{{ request('date_to') }}" class="border p-2">
          <button type="submit" class="bg-gray-200 p-2 rounded">Filter</button>
        </form>

        <div class="overflow-auto">
          <table class="w-full text-sm">
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
    </div>

  </div>
</x-admin.layout>

<!-- Photo modals removed — thumbnails displayed inline in table for simplicity -->

{{-- Edit handled by server via ?edit_assignment; no client edit script required --}}
