<x-admin.layout title="Buat Article">
  <div class="p-4">
    <nav style="font-size:13px;margin-bottom:8px">
      <a href="{{ route('dashboard') }}">Dashboard</a> &raquo; <a href="{{ route('admin.articles.index') }}">Articles</a> &raquo; Create
    </nav>
    <h3>Buat Article</h3>
    <form method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="mb-2">
        <label class="form-label small">Title</label>
        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
      </div>
      <div class="mb-2">
        <label class="form-label small">Category</label>
        <select name="category_id" class="form-select">
          <option value="">-- Pilih Kategori --</option>
          @foreach($categories as $c)
            <option value="{{ $c->id }}">{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-2">
        <label class="form-label small">Masjid (opsional)</label>
        <select name="mosque_id" class="form-select">
          <option value="">-- Pilih Masjid --</option>
          @foreach($mosques as $m)
            <option value="{{ $m->id }}">{{ $m->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-2">
        <label class="form-label small">Summary</label>
        <textarea name="summary" class="form-control" rows="3">{{ old('summary') }}</textarea>
      </div>
      <div class="mb-2">
        <label class="form-label small">Content</label>
        <input id="x-content" type="hidden" name="content" value="{{ old('content') }}">
        <trix-editor input="x-content"></trix-editor>
      </div>
      <div class="mb-2">
        <label class="form-label small">Image (Preview)</label>
        <div style="display:flex;gap:8px;align-items:center">
          <input type="file" accept="image/*" name="image" id="article-image-input" class="form-control-file">
          <img id="article-image-preview" src="{{ old('image_url') }}" style="max-height:80px;display:{{ old('image_url') ? 'block' : 'none' }}"> 
        </div>
      </div>
      <div class="mb-2">
        <button class="btn btn-outline-secondary" name="action" value="save">Simpan (Draft)</button>
        <button class="btn btn-primary" name="action" value="publish">Publish</button>
      </div>
    </form>
  </div>

  @push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"></script>
    <script>
      (function(){
        const input = document.getElementById('article-image-input');
        const preview = document.getElementById('article-image-preview');
        if(input){
          input.addEventListener('change', function(){
            const f = this.files && this.files[0];
            if(!f) { preview.style.display='none'; preview.src=''; return; }
            const reader = new FileReader();
            reader.onload = function(e){ preview.src = e.target.result; preview.style.display = 'block'; }
            reader.readAsDataURL(f);
          });
        }

        // Trix inline image upload
        document.addEventListener('trix-attachment-add', function(event){
          const attachment = event.attachment;
          if (attachment.file) uploadTrixAttachment(attachment);
        });

        function uploadTrixAttachment(attachment){
          const file = attachment.file;
          const form = new FormData();
          form.append('image', file);
          const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
          fetch("{{ route('admin.articles.upload_image') }}", { method: 'POST', headers: {'X-CSRF-TOKEN': token}, body: form })
            .then(r => r.json())
            .then(data => {
              attachment.setAttributes({ url: data.url, href: data.url });
            }).catch(err => { console.error('Trix upload failed', err); });
        }
      })();
    </script>
  @endpush
</x-admin.layout>
