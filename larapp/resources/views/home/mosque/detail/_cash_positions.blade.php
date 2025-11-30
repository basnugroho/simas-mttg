<div id="cash-positions" class="mosque-section card shadow-sm mb-3">
	<div class="card-header bg-white d-flex justify-content-between align-items-center">
		<h5 class="mb-0">Cash Masjid</h5>
	</div>
	<div class="card-body">
		@if($cashPositions->isEmpty())
			<p class="text-muted mb-0">Belum ada data cash position untuk masjid ini.</p>
		@else
			<div class="table-responsive">
				<table class="table table-sm align-middle mb-0">
					<thead>
						<tr>
							<th>Periode</th>
							<th class="text-end">Nominal</th>
							<th>Keterangan</th>
						</tr>
					</thead>
					<tbody>
						@foreach($cashPositions as $cp)
							<tr>
								<td>
									@if($cp->period_start && $cp->period_end)
										{{ $cp->period_start->format('d M Y') }} - {{ $cp->period_end->format('d M Y') }}
									@elseif($cp->period_start)
										{{ $cp->period_start->format('d M Y') }}
									@else
										-
									@endif
								</td>
								<td class="text-end">
									{{ number_format($cp->nominal ?? 0, 0, ',', '.') }}
								</td>
								<td>
									{{ $cp->note ?? '-' }}
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		@endif
	</div>
</div>
