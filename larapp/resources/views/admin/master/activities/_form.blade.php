<div style="max-width:560px">
  <div class="form-group">
    <label>Name</label>
    <input type="text" name="activity_name" value="{{ old('activity_name', $activity->activity_name ?? '') }}" class="form-control" />
  </div>
  <div class="form-group" style="margin-top:8px">
    <label>Slug (optional)</label>
    <input type="text" name="slug" value="{{ old('slug', $activity->slug ?? '') }}" class="form-control" />
  </div>
</div>
