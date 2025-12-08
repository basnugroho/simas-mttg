@component('components.administrator.layout')
    @slot('title') {{ $title ?? 'Preview Article' }} @endslot

    @section('header')
    <div class="col-sm-6"><h3 class="mb-0">Preview Article</h3></div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('admin.articles.index') }}">Articles</a></li>
          <li class="breadcrumb-item active">Preview</li>
        </ol>
    </div>
    @show

    @section('content')
      <div class="card">
        <div class="card-header">Preview Article</div>
        <div class="card-body p-4">
          <div style="display:flex;gap:18px;align-items:flex-start">
            <div style="flex:1">
              <h2 style="margin-top:0">{{ $article->title }}</h2>
              <div style="color:#6b7280;margin-bottom:8px">{{ $article->summary }}</div>
              @if($article->image_url)
                <div style="margin-bottom:12px"><img src="{{ $article->image_url }}" alt="" style="max-width:100%;border-radius:8px"></div>
              @endif
              <div class="article-content">{!! $article->content !!}</div>
            </div>
            <aside style="width:260px">
              <div style="background:#fff;padding:12px;border-radius:8px;box-shadow:0 6px 18px rgba(2,6,23,.04)">
                <div><strong>Status:</strong> {{ $article->status }}</div>
                <div><strong>Creator:</strong> {{ $article->creator?->name ?? 'User #' . ($article->created_by ?? '-') }}</div>
                <div><strong>Created:</strong> {{ $article->created_at->toDateTimeString() }}</div>
                @if($article->published_at)<div><strong>Published:</strong> {{ $article->published_at->toDateTimeString() }}</div>@endif
                <div style="margin-top:8px">
                  <form method="POST" action="{{ route('admin.articles.publish', $article->id) }}">@csrf
                    @if($article->status !== 'PUBLISHED')
                      <button class="btn btn-primary">Publish</button>
                    @endif
                  </form>
                </div>
              </div>
            </aside>
          </div>
        </div>
      </div>
    @show

@endcomponent
