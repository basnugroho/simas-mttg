<x-admin.layout title="Edit Aktivitas Masjid">
    <div class="p-4">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            @php $ms = $selected_mosque ?? null; @endphp
            <h3 style="margin:0">Edit Aktivitas Masjid{{ $ms ? ' — ' . ($ms->name ?? ('#' . $ms->id)) : '' }}</h3>
            @php $backUrl = route('admin.mosque_activities.index'); if($ms) $backUrl .= '?mosque_ids[]=' . $ms->id; @endphp
            <a href="{{ $backUrl }}" class="btn btn-secondary">Kembali</a>
        </div>

        @include('admin.mosque_activities._form', ['editing' => $editing])
    </div>
</x-admin.layout>
