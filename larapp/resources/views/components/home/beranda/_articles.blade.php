@php
    // Controller idealnya mengirim $articles (collection Article) terbaru.
    $articles = $articles ?? ($latestArticles ?? collect());
    // Fallback contoh jika kosong (4 dummy)
    if($articles->isEmpty()){
        $articles = collect([
            (object)['title'=>'Peningkatan Aktivitas Keagamaan di Wilayah Timur','summary'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla facilisi. Donec non risus in purus pretium tincidunt. Sed et...','image_url'=>asset('images/mosque.png'),'published_at'=>now()->subDays(18),'author'=>'Admin Regional'],
            (object)['title'=>'Program Renovasi Masjid Meningkatkan Kenyamanan Jamaah','summary'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nec felis id urna dignissim eleifend. Mauris efficitur, eros u...','image_url'=>asset('images/mosque.png'),'published_at'=>now()->subMonths(2),'author'=>'Redaksi Pusat'],
            (object)['title'=>'Gotong Royong Warga dalam Pembangunan Musholla Baru','summary'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur pharetra ex sit amet diam posuere, non tempus velit...','image_url'=>asset('images/mosque.png'),'published_at'=>now()->subMonths(3),'author'=>'Tim Dokumentasi'],
            (object)['title'=>'Kegiatan Sosial Bersama BKM di Bulan Ramadhan','summary'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque a ante ut lectus imperdiet imperdiet. Fusce id risus et nun...','image_url'=>asset('images/mosque.png'),'published_at'=>now()->subMonths(4),'author'=>'Kontributor Lapangan'],
        ]);
    }
@endphp
<section class="news-section mt-0">
    <div class="container">
        <div class="news-wrap p-0" style="background:transparent;padding:0;">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                {{-- Header removed as requested: no Informasi Terkini, subtext, or Lihat Semua button --}}
                <div class="card-body pt-2 pb-4 px-4">
                    <div class="news-scroll" style="max-height: calc( (320px * 3) + 32px ); overflow-y:auto; padding-right:8px;">
                        <div class="news-grid">
                        {{-- Show up to 9 latest articles (3 rows) --}}
                        @foreach($articles->take(9) as $a)
                            @php
                                $defaultImg = asset('images/mosque.webp');
                                $img = $defaultImg;
                                if(!empty($a->image_url)){
                                    $candidate = $a->image_url;
                                    // absolute URL
                                    if(preg_match('/^https?:\/\//', $candidate)){
                                        $img = $candidate;
                                    } else {
                                        try {
                                            // if storage disk public contains it
                                            if(\Illuminate\Support\Facades\Storage::disk('public')->exists($candidate)){
                                                $img = \Illuminate\Support\Facades\Storage::disk('public')->url($candidate);
                                            } elseif(strpos($candidate, 'storage/') === 0) {
                                                // already a public storage path
                                                $img = asset($candidate);
                                            } else {
                                                // fallback: assume asset path
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
                                <img src="{{ $img }}" alt="{{ $a->title }}" class="thumb" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ asset('images/mosque.webp') }}'">
                                <div class="body">
                                    <div class="news-meta"><span>{{ $author }}</span><span>{{ $rel }}</span></div>
                                    <h3 class="news-title" title="{{ $a->title }}">{{ Str::limit($a->title, 70) }}</h3>
                                    <div class="news-summary">{{ $summary }}</div>
                                </div>
                            </a>
                        @endforeach
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</section>
