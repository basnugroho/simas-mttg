<div id="activities" class="mosque-section card shadow-sm mb-3">
	<div class="card-header bg-white d-flex justify-content-between align-items-center">
		<h5 class="mb-0">Aktifitas Masjid</h5>
	</div>
	<div class="card-body">
		@if($activities->isEmpty())
			<p class="text-muted mb-0">Belum ada aktifitas yang tercatat untuk masjid ini.</p>
		@else
			<div class="list-group list-group-flush">
				@foreach($activities as $activity)
					<div class="list-group-item px-0">
						<div class="d-flex justify-content-between align-items-start">
							<div>
								<div class="fw-semibold">{{ $activity->name ?? $activity->title ?? 'Aktifitas' }}</div>
								@if(!empty($activity->description))
									<div class="small text-muted mt-1">{{ $activity->description }}</div>
								@endif
							</div>
							@if(!empty($activity->pivot) && !empty($activity->pivot->note))
								<div class="ms-3 small text-muted text-end">
									<div>Catatan:</div>
									<div>{{ $activity->pivot->note }}</div>
								</div>
							@endif
						</div>
						@if(!empty($activity->pivot) && !empty($activity->pivot->created_at))
							<div class="small text-muted mt-1">Ditambahkan: {{ \Illuminate\Support\Carbon::parse($activity->pivot->created_at)->format('d M Y') }}</div>
						@endif
					</div>
				@endforeach
			</div>
		@endif
	</div>
</div>
