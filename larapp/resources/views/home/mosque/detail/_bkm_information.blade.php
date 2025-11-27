<div id="bkm" class="mt-4 pt-3 mosque-section">
	<div class="card card-sm mb-3">
		<div class="card-body p-2.5">
			<h6 class="mb-2"><b>BKM / Pengurus</b></h6>
			@php
				$managers = [];
				if(isset($mosque) && $mosque->relationLoaded('managers')){
					$managers = $mosque->managers;
				} elseif(isset($mosque) && method_exists($mosque, 'managers')){
					try{ $managers = $mosque->managers()->get(); }catch(Exception $e){ $managers = []; }
				}
			@endphp

			@if(empty($managers) || $managers->count() == 0)
				<div class="text-muted">Tidak ada data BKM tersedia.</div>
			@else
				@foreach($managers as $m)
					<div class="mb-3">
						<div class="d-flex justify-content-between align-items-start mb-1">
							<div>
								<strong>{{ $m->ketua_pengurus ?? 'Ketua belum disetel' }}</strong>
								<div class="small text-muted">Periode: {{ $m->period_start?->format('Y-m-d') ?? '—' }} sampai {{ $m->period_end?->format('Y-m-d') ?? '—' }}</div>
							</div>
						</div>

						@if(!empty($m->file_path))
							@php
								$pdfUrl = null;
								try{
									if(preg_match('/^https?:\/\//', $m->file_path)){
										$pdfUrl = $m->file_path;
									} else {
										$pdfUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($m->file_path);
									}
								}catch(Exception $e){
									$pdfUrl = null;
								}
							@endphp

							@if($pdfUrl)
								<div class="pdf-embed mb-2" style="height:420px;">
									<object data="{{ $pdfUrl }}" type="application/pdf" width="100%" height="100%">
										<embed src="{{ $pdfUrl }}" type="application/pdf" />
										<p class="small">Browser Anda tidak mendukung menampilkan PDF secara langsung. <a href="{{ $pdfUrl }}" target="_blank" rel="noopener">Klik di sini untuk membuka atau mengunduh file</a>.</p>
									</object>
								</div>

							@else
								<div class="small text-muted">File terdaftar tetapi tidak dapat diakses. <a href="#">Hubungi admin</a></div>
							@endif
						@else
							<div class="small text-muted">Tidak ada file BKM untuk periode ini.</div>
						@endif
					</div>
				@endforeach
			@endif
		</div>
	</div>
</div>

