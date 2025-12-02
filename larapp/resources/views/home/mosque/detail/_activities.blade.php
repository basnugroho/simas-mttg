<div id="activities" class="mosque-section card shadow-sm mb-3">
	<div class="card-header bg-white d-flex justify-content-between align-items-center">
		<h5 class="mb-0">Aktivitas Masjid</h5>
	</div>
	<div class="card-body">
		@if($activities->isEmpty())
			<p class="text-muted mb-0">Belum ada aktivitas yang tercatat untuk masjid ini.</p>
		@else
			<div class="table-responsive">
				<table class="table table-sm align-middle">
					<thead class="table-light">
						<tr>
							<th style="width:25%">Aktivitas</th>
							<th style="width:8%">Rutin</th>
							<th style="width:20%">Hari</th>
							<th style="width:27%">Catatan</th>
							<th style="width:20%">Pembuat</th>
						</tr>
					</thead>
					<tbody>
						@foreach($activities as $activity)
							@php
								$pivot = $activity->pivot ?? null;
								$isRutin = $pivot?->is_rutin ? 'Ya' : 'Tidak';
								// rutin_days disimpan sebagai teks (JSON atau comma-separated)
								$hariRaw = $pivot?->rutin_days;
								$hariList = [];
								if (!empty($hariRaw)) {
									if (str_starts_with(trim($hariRaw), '[')) {
										$decoded = json_decode($hariRaw, true);
										if (is_array($decoded)) { $hariList = $decoded; }
									} else {
										$hariList = array_filter(array_map('trim', explode(',', $hariRaw)));
									}
								}
								$hariStr = $hariList ? implode(', ', $hariList) : '-';
								$catatan = $pivot?->note ?: '-';
								$creator = $activity->creator->name ?? '-';
							@endphp
							<tr>
								<td>
									<div class="fw-semibold">{{ $activity->activity_name ?? $activity->name ?? $activity->title ?? 'Aktivitas' }}</div>
									@if(!empty($activity->description))
										<div class="text-muted small">{{ \Illuminate\Support\Str::limit($activity->description, 100) }}</div>
									@endif
									@if($pivot?->event_start || $pivot?->event_end)
										<div class="small text-muted mt-1">
											@if($pivot?->event_start)
												Mulai: {{ \Illuminate\Support\Carbon::parse($pivot->event_start)->format('d M Y H:i') }}
											@endif
											@if($pivot?->event_end)
												<span class="ms-2">Selesai: {{ \Illuminate\Support\Carbon::parse($pivot->event_end)->format('d M Y H:i') }}</span>
											@endif
										</div>
									@endif
								</td>
								<td><span class="badge {{ $pivot?->is_rutin ? 'bg-success' : 'bg-secondary' }}">{{ $isRutin }}</span></td>
								<td>{{ $hariStr }}</td>
								<td class="small">{{ \Illuminate\Support\Str::limit($catatan, 80) }}</td>
								<td class="small">{{ \Illuminate\Support\Str::limit($creator, 40) }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		@endif
	</div>
</div>
