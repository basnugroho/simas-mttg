@php
    // Controller idealnya mengirim $articles (collection Article) terbaru.
    $articles = $articles ?? ($latestArticles ?? collect());
@endphp
<section class="news-section mt-3">
    <div class="container">
        <div class="news-wrap p-0" style="background:transparent;padding:0;">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-3 pb-2">
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <div class="header-left">
                            <h2 class="mb-1" style="font-weight:700;font-size:1.85rem;margin:0;">Informasi Terkini</h2>
                            <div class="sub" style="font-size:.85rem;color:#6b7280;">informasi / berita terkini sekitar masjid Telkom Regional 3</div>
                        </div>
                        <div class="header-right ms-3">
                            <a href="{{ route('article') }}" class="btn btn-outline-dark rounded-pill">Lihat Semua</a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-2 pb-4 px-4">
                    @if($articles->isEmpty())
                        <p class="text-muted m-0">Tidak ada article.</p>
                    @else
                        <div class="news-grid">
                            {{-- Show only 4 latest articles in Informasi Terkini --}}
                            @foreach($articles->take(4) as $a)
                                @php
                                    $defaultImg = asset('images/mosque.webp');
                                    $img = $defaultImg;
                                    if(!empty($a->image_url)){
                                        $candidate = $a->image_url;
                                        if(preg_match('/^https?:\/\//', $candidate)){
                                            $img = $candidate;
                                        } else {
                                            try {
                                                if(\Illuminate\Support\Facades\Storage::disk('public')->exists($candidate)){
                                                    $img = \Illuminate\Support\Facades\Storage::disk('public')->url($candidate);
                                                } elseif(strpos($candidate, 'storage/') === 0) {
                                                    $img = asset($candidate);
                                                } else {
                                                    $img = asset($candidate);
                                                }
                                            } catch (\Throwable $_) {
                                                $img = $defaultImg;
                                            }
                                        }
                                    }
                                    $rel = ($a->published_at ?? null) ? \Carbon\Carbon::parse($a->published_at)->diffForHumans() : '';
                                    $author = $a->author ?? ($a->author_name ?? 'Admin');
                                    $summary = Str::limit($a->summary ?? ($a->content ?? ''), 140);
                                @endphp
                                @php $articleUrl = isset($a->id) ? route('article.show', ['id' => $a->id]) : '#'; @endphp
                                <a href="{{ $articleUrl }}" class="news-card position-relative text-decoration-none text-body">
                                    <span class="badge bg-danger position-absolute" style="top:10px;left:10px;font-size:.55rem;letter-spacing:.05em;padding:.35rem .5rem;">TERKINI</span>
                                    <img src="{{ $img }}" alt="{{ $a->title }}" class="thumb" loading="lazy">
                                    <div class="body">
                                        <div class="news-meta"><span>{{ $author }}</span><span>{{ $rel }}</span></div>
                                        <h3 class="news-title" title="{{ $a->title }}">{{ Str::limit($a->title, 70) }}</h3>
                                        <div class="news-summary">{{ $summary }}</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
