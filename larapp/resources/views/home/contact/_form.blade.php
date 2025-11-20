
      <div class="container position-relative">
        <!-- Floating info card (separate from contact-wrapper to avoid clipping) -->
        <div id="floating-info-card" class="info-card bg-white shadow p-3" style="max-width:260px; display:none; border-radius:12px;">
          <div class="mb-3">
            <div class="d-flex align-items-center mb-1">
              <i class="bi bi-geo-alt-fill me-2 text-danger"></i>
              <h6 class="mb-0">Alamat</h6>
            </div>
            <small class="text-muted d-block">Telkom Regional 3<br>Jawa Timur, Indonesia</small>
          </div>
          <div class="mb-3">
            <div class="d-flex align-items-center mb-1">
              <i class="bi bi-telephone-fill me-2 text-danger"></i>
              <h6 class="mb-0">Telepon</h6>
            </div>
            <small class="text-muted d-block">+62 31 1234 5678</small>
          </div>
          <div>
            <div class="d-flex align-items-center mb-1">
              <i class="bi bi-clock-fill me-2 text-danger"></i>
              <h6 class="mb-0">Jam</h6>
            </div>
            <small class="text-muted d-block">Senin–Jumat, 09:00 – 18:00</small>
          </div>
        </div>

        <div class="contact-wrapper ms-auto"> 
          <div class="row">
          <!-- Kolom Kiri: Foto + Info -->
          <div class="col-md-5 position-relative">
            <div class="office-photo"></div>
          </div>
          <!-- Kolom Kanan: Form -->
          <div class="col-md-7">
            <div class="h-auto d-flex flex-column justify-content-between p-3 p-md-4 position-relative">
              <div class="mb-2">
                <h4 class="fw-bold mb-1">Contact Form</h4>
              </div>
              @if(session('success'))
                <div id="contact-success-alert" class="alert alert-success d-flex align-items-start gap-3 shadow-sm border-0 rounded-3 p-3" role="alert">
                  <div class="flex-shrink-0">
                    <svg width="24" height="24" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                      <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0z" fill="#d1fae5"/>
                      <path d="M6.00039 10.8L3.20039 8.00001l-0.8 0.8L6.00039 12.4l8-8-0.8-0.8-7.2 7.2z" fill="#059669"/>
                    </svg>
                  </div>
                  <div class="flex-grow-1">
                    <div class="fw-semibold">{{ session('success') }}</div>
                  </div>
                  <button type="button" class="btn-close btn-close-white" aria-label="Close" style="margin-left:8px" onclick="document.getElementById('contact-success-alert')?.remove();"></button>

                  <script>
                    (function(){
                      const a = document.getElementById('contact-success-alert');
                      if (!a) return;
                      // auto-hide after 10 seconds
                      setTimeout(()=>{
                        a.style.transition = 'opacity 0.6s, transform 0.6s';
                        a.style.opacity = '0';
                        a.style.transform = 'translateY(-8px)';
                        setTimeout(()=>{ a.remove(); }, 650);
                      }, 10000);
                    })();
                  </script>
                </div>
              @endif
              <form action="{{ route('contact.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label class="label-muted mb-1" for="mosque_search">Masjid (opsional)</label>
                  <input
                    type="text"
                    id="mosque_search"
                    class="form-control"
                    placeholder="Cari masjid (ketik nama)"
                    autocomplete="off"
                    value="{{ old('mosque_name') }}"
                  />
                  <input type="hidden" name="mosque_id" id="mosque_id" value="{{ old('mosque_id') }}" />
                  <div id="mosque_suggestions" class="list-group position-absolute mt-1" style="z-index:1050; display:none; max-width:420px; max-height:200px; overflow:auto;"></div>
                </div>
                <div class="mb-1">
                  <label class="label-muted mb-1" for="fullName">Nama Lengkap</label>
                  <input
                    type="text"
                    class="form-control @error('name') is-invalid @enderror"
                    id="fullName"
                    name="name"
                    placeholder="Masukkan nama lengkap"
                    value="{{ old('name') }}"
                  />
                  @error('name')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-1">
                  <label class="label-muted mb-1" for="email">Email</label>
                  <input
                    type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    placeholder="nama@email.com"
                    value="{{ old('email') }}"
                  />
                  @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                </div>

                

                <div class="mb-1">
                  <label class="label-muted mb-1" for="message">Pesan Anda</label>
                  <textarea
                    class="form-control @error('message') is-invalid @enderror"
                    id="message"
                    name="message"
                    rows="2"
                    placeholder="Tulis pesan Anda di sini..."
                  >{{ old('message') }}</textarea>
                  @error('message')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                </div>

                

                <button type="submit" class="btn btn-login-gradient rounded-pill px-3 py-1 mb-3 mt-3">
                  Kirim Pesan
                </button>
              </form>
              <script>
                (function(){
                  const input = document.getElementById('mosque_search');
                  const hidden = document.getElementById('mosque_id');
                  const box = document.getElementById('mosque_suggestions');
                  let timer = null;

                  input.addEventListener('input', function(){
                    const q = this.value.trim();
                    hidden.value = '';
                    if (timer) clearTimeout(timer);
                    if (!q) { box.style.display='none'; box.innerHTML=''; return; }
                    timer = setTimeout(()=>{
                      fetch('/search/suggestions?q=' + encodeURIComponent(q))
                        .then(r=>r.json())
                        .then(data=>{
                          box.innerHTML = '';
                          if (!data || data.length===0) { box.style.display='none'; return; }
                          (data.slice(0,7)).forEach(item=>{
                            const el = document.createElement('button');
                            el.type = 'button';
                            el.className = 'list-group-item list-group-item-action';
                            el.textContent = item.name + (item.city ? ' (' + item.city + ')' : '');
                            el.dataset.id = item.id;
                            el.addEventListener('click', ()=>{
                              input.value = item.name + (item.city ? ' (' + item.city + ')' : '');
                              hidden.value = item.id;
                              box.style.display='none';
                            });
                            box.appendChild(el);
                          });
                          box.style.display='block';
                        }).catch(()=>{ box.style.display='none'; });
                    }, 250);
                  });

                  // hide on outside click
                  document.addEventListener('click', function(e){
                    if (!box.contains(e.target) && e.target !== input) { box.style.display='none'; }
                  });
                })();
              </script>
              <script>
                (function(){
                  const photo = document.querySelector('.office-photo');
                  const card = document.getElementById('floating-info-card');

                  function positionCard(){
                    if(!photo || !card) return;
                    const rect = photo.getBoundingClientRect();
                    // only show on larger screens
                    if(window.innerWidth < 768){
                      card.style.display = 'none';
                      return;
                    }
                    card.style.display = 'block';
                    // position card above the photo and centered horizontally over it
                    const container = document.querySelector('.container.position-relative') || document.querySelector('.container');
                    const containerRect = container.getBoundingClientRect();
                    // position card slightly lower and centered vertically over the photo
                    const top = (rect.top - containerRect.top) + (rect.height / 2) - (card.offsetHeight / 2) + 20;
                    card.style.position = 'absolute';
                    card.style.top = Math.max(0, top) + 'px';
                    card.style.left = '0px';
                    card.style.zIndex = 1060;
                  }

                  window.addEventListener('resize', positionCard);
                  window.addEventListener('load', positionCard);
                  window.addEventListener('scroll', positionCard);
                  // initial call
                  setTimeout(positionCard, 80);
                })();
              </script>
            </div>
          </div>
          </div>
        </div>
      </div>