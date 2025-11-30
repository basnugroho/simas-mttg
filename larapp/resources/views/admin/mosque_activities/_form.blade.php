@php
    $isEdit = isset($editing) && $editing;
    // determine current mosque context: prefer selected_mosque passed from controller,
    // then check editing model, then fall back to request query (mosque or mosque_ids[])
    $context_mosque = $selected_mosque ?? null;
    if (!$context_mosque && $isEdit && (!empty($editing->mosque_id) || !empty($editing->mosque_ids_array))) {
        $mid = !empty($editing->mosque_id) ? $editing->mosque_id : (is_array($editing->mosque_ids_array) && count($editing->mosque_ids_array) ? $editing->mosque_ids_array[0] : null);
        if ($mid) {
            try { $context_mosque = \App\Models\Mosque::find($mid); } catch(\Throwable $__e) { $context_mosque = null; }
        }
    }
    if (!$context_mosque) {
        $rq = request();
        if ($rq->query('mosque')) {
            try{ $context_mosque = \App\Models\Mosque::find((int)$rq->query('mosque')); } catch(\Throwable $__e) { $context_mosque = null; }
        } elseif ($rq->query('mosque_ids')) {
            $ids = (array) $rq->query('mosque_ids');
            if (count($ids)) {
                try{ $context_mosque = \App\Models\Mosque::find((int)$ids[0]); } catch(\Throwable $__e) { $context_mosque = null; }
            }
        }
    }
@endphp
<form method="POST" action="{{ $isEdit ? route('admin.mosque_activities.update', $editing->id) : route('admin.mosque_activities.store') }}" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PATCH')
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Pilih Aktivitas</label>
                <select name="activity_id" class="form-select" required>
                    <option value="">-- Pilih Aktivitas --</option>
                    @foreach($activities as $act)
                        <option value="{{ $act->id }}" @if(old('activity_id', $isEdit ? ($editing->activity_id ?? null) : null) == $act->id) selected @endif>{{ $act->activity_name ?? $act->name ?? ('#' . $act->id) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Mosque selection: hide displayed mosque name if context exists (title already shows it) --}}
            <div class="mb-3">
                @if($context_mosque)
                    <input type="hidden" name="mosque_ids[]" value="{{ $context_mosque->id }}">
                @else
                    <label class="form-label">Masjid</label>
                    <select name="mosque_ids[]" class="form-select" multiple required>
                        @foreach($mosques as $m)
                            <option value="{{ $m->id }}" @if(in_array($m->id, old('mosque_ids', $isEdit ? $editing->mosque_ids_array : []))) selected @endif>{{ $m->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Rutin?</label>
                <select name="is_rutin" id="is_rutin" class="form-select">
                    <option value="0" @if(old('is_rutin', $isEdit ? $editing->is_rutin : 0) == 0) selected @endif>Tidak</option>
                    <option value="1" @if(old('is_rutin', $isEdit ? $editing->is_rutin : 0) == 1) selected @endif>Ya</option>
                </select>
            </div>

            <div class="mb-3" id="rutin-days-wrap" style="display: {{ old('is_rutin', $isEdit ? $editing->is_rutin : 0) ? 'block' : 'none' }};">
                <label class="form-label">Hari Rutin (pilih banyak)</label>
                <select name="rutin_days[]" class="form-select" multiple>
                    @php $days = ['senin'=>'Senin','selasa'=>'Selasa','rabu'=>'Rabu','kamis'=>'Kamis','jumat'=>'Jumat','sabtu'=>'Sabtu','minggu'=>'Minggu']; @endphp
                    @foreach($days as $k=>$label)
                        <option value="{{ $k }}" @if(in_array($k, old('rutin_days', $isEdit ? ($editing->rutin_days_array ?? []) : []))) selected @endif>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Aktif</label>
                <select name="is_active" class="form-select">
                    <option value="1" @if(old('is_active', $isEdit ? $editing->is_active : 1) == 1) selected @endif>Ya</option>
                    <option value="0" @if(old('is_active', $isEdit ? $editing->is_active : 1) == 0) selected @endif>Tidak</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Foto (boleh pilih banyak)</label>
                <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
            </div>

            @if($isEdit && $editing->photos && $editing->photos->count())
                <div class="mb-3">
                    <label class="form-label">Foto saat ini</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($editing->photos as $photo)
                            <div class="border p-1 text-center">
                                <img src="{{ asset('storage/' . $photo->path) }}" style="width:120px;height:80px;object-fit:cover;display:block;margin-bottom:6px;">
                                <form method="POST" action="{{ route('admin.mosque_activity_photos.destroy', $photo->id) }}" onsubmit="return confirm('Hapus foto ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="text-end">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Buat' }}</button>
                @php
                    $backUrl = route('admin.mosque_activities.index');
                    if(isset($selected_mosque) && $selected_mosque){
                        $backUrl .= '?mosque_ids[]=' . $selected_mosque->id;
                    }
                @endphp
                <a href="{{ $backUrl }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        var isRutin = document.getElementById('is_rutin');
        var wrap = document.getElementById('rutin-days-wrap');
        if(isRutin){
            isRutin.addEventListener('change', function(){
                wrap.style.display = this.value == '1' ? 'block' : 'none';
            });
        }
    });
</script>
