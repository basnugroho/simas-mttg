@props(['article' => null, 'related' => null])

<x-home.layout :title="($article->title ?? 'Article Detail') . ' - Simas MTTG'">
    <x-home._navbar />

    <section class="container my-4">
        <div class="row">
            <div class="col-12">
                <div class="mb-3">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">&larr; Kembali</a>
                </div>
            </div>
        </div>

        <div class="row gy-4">
            <main class="col-12 col-lg-8">
                <article class="card shadow-sm rounded-4 p-3">
                    @php
                        $title = $article->title ?? 'Untitled Article';
                        $author = $article->author ?? ($article->author_name ?? 'Admin');
                        $published = isset($article->published_at) ? \Carbon\Carbon::parse($article->published_at)->format('d M Y') : null;
                        $img = $article->image_url ?? asset('images/mosque-1.jpg');
                    @endphp

                    <div class="article-hero mb-3 position-relative">
                        <img src="{{ $img }}" alt="{{ $title }}" class="img-fluid rounded-3 w-100" style="object-fit:cover; max-height:420px;">
                    </div>

                    <header class="mb-3">
                        <h1 class="h3" style="font-weight:700;">{{ $title }}</h1>
                        <div class="text-muted small d-flex gap-3 flex-wrap">
                            <div>By <strong>{{ $author }}</strong></div>
                            @if($published)
                                <div>{{ $published }}</div>
                            @endif
                        </div>
                    </header>

                    <div class="article-body text-muted" style="line-height:1.9; font-size:1rem;">
                        {!! $article->content ?? ($article->summary ?? '<p>Tidak ada konten.</p>') !!}
                    </div>

                    <footer class="mt-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            <div class="text-muted small">Bagikan:</div>
                            <div class="d-flex gap-2">
                                <a class="btn btn-outline-secondary btn-sm" href="#" aria-label="Share to Whatsapp">WA</a>
                                <a class="btn btn-outline-secondary btn-sm" href="#" aria-label="Share to Twitter">TW</a>
                                <a class="btn btn-outline-secondary btn-sm" href="#" aria-label="Share to Facebook">FB</a>
                            </div>
                        </div>
                    </footer>
                </article>
            </main>

            <aside class="col-12 col-lg-4">
                <div class="card shadow-sm rounded-4 p-3 mb-3">
                    <h5 class="mb-2" style="font-weight:700;">Artikel Terkait</h5>
                    <div class="list-group list-group-flush">
                        @if(isset($related) && $related->count())
                            @foreach($related->take(6) as $r)
                                <a href="{{ route('article.show', ['id' => $r->id]) }}" class="list-group-item list-group-item-action">{{ Str::limit($r->title, 70) }}</a>
                            @endforeach
                        @else
                            <div class="text-muted">Tidak ada artikel terkait.</div>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm rounded-4 p-3">
                    <h6 class="mb-2" style="font-weight:700;">Kategori</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="#" class="btn btn-outline-secondary btn-sm">Berita</a>
                        <a href="#" class="btn btn-outline-secondary btn-sm">Kegiatan</a>
                        <a href="#" class="btn btn-outline-secondary btn-sm">Renovasi</a>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <x-home._footer />
</x-home.layout>
