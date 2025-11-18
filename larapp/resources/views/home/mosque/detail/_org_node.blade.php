@php
    /**
     * Expect $node array with keys: name, role, photo, title, phone, children (array)
     */
    $name = $node['name'] ?? ($node['nama'] ?? '-');
    $role = $node['role'] ?? ($node['jabatan'] ?? '');
    $photo = $node['photo'] ?? ($node['foto'] ?? null);
    $title = $node['title'] ?? ($node['title'] ?? '');
    $children = $node['children'] ?? [];
@endphp

<li class="org-node">
    <div class="node-card flex flex-col items-center gap-2">
        <div class="avatar w-20 h-20 rounded-full bg-slate-100 overflow-hidden flex items-center justify-center text-lg font-semibold text-slate-700">
            @if(!empty($photo))
                <img src="{{ $photo }}" alt="{{ $name }}" class="w-full h-full object-cover" />
            @else
                {{ strtoupper(substr($name,0,2)) }}
            @endif
        </div>
        <div class="name-box mt-1 text-center">
            <div class="text-xs text-slate-500">{{ $role }}</div>
            <div class="text-sm font-semibold text-slate-800">{{ $name }}</div>
        </div>
    </div>

    @if(!empty($children) && is_array($children))
        <ul>
            @foreach($children as $child)
                @include('home.mosque.detail._org_node', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>
