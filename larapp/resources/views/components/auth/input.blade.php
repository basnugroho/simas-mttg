@props(['id','name','type'=>'text','placeholder'=>''])
<input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
  class="glass-input w-full rounded-lg px-4 py-3 text-sm text-white placeholder-white/40"
  placeholder="{{ $placeholder }}" {{ $attributes }}>
