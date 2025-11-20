<div style="max-width:560px">
  <div class="form-group">
    <label>Name</label>
    <input type="text" name="activity_name" value="{{ old('activity_name', $activity->activity_name ?? '') }}" class="form-control" />
  </div>
  <div class="form-group" style="margin-top:8px">
    <label>Category</label>
    <select name="category" class="form-control">
      @php $cur = old('category', $activity->category ?? 'ghairu_mahdhah'); @endphp
      <option value="mahdhah" {{ $cur === 'mahdhah' ? 'selected' : '' }}>Mahdhah</option>
      <option value="ghairu_mahdhah" {{ $cur === 'ghairu_mahdhah' ? 'selected' : '' }}>Ghairu Mahdhah</option>
    </select>
  </div>
  <div class="form-group" style="margin-top:8px">
    <label>Slug (optional)</label>
    <input type="text" name="slug" value="{{ old('slug', $activity->slug ?? '') }}" class="form-control" />
  </div>
</div>
