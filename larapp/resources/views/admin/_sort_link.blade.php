@php
  $currentSort = $sort ?? request('sort');
  $currentDir = $dir ?? request('dir', 'desc');
  $newDir = ($currentSort === $key && $currentDir === 'asc') ? 'desc' : 'asc';
  $query = array_merge(request()->query(), ['sort' => $key, 'dir' => $newDir]);
  $url = request()->url() . '?' . http_build_query($query);
@endphp
<a href="{{ $url }}" style="color:inherit;text-decoration:none">{{ $label }} @if(isset($currentSort) && $currentSort === $key)<small style="color:#6b7280">({{ strtoupper($currentDir) }})</small>@endif</a>
