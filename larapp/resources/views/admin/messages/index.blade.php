<x-admin.layout title="Kotak Masuk">
  <div class="p-4">
    <nav style="font-size:13px;margin-bottom:8px">
      <a href="{{ route('dashboard') }}">Dashboard</a> &raquo; Kotak Masuk
    </nav>

    <div>
      <form method="GET" class="mb-3" id="filter-form">
        <div style="display:flex;gap:8px;align-items:flex-end">
          <div><input type="text" name="search" value="{{ request('search') }}" placeholder="Cari subject / pengirim" class="form-control"></div>
          <div><select name="mosque_id" class="form-select"><option value="">-- Semua Masjid --</option>@foreach($mosques as $m)<option value="{{ $m->id }}" @if(request('mosque_id') == $m->id) selected @endif>{{ $m->name }}</option>@endforeach</select></div>
          <div style="display:flex;gap:8px"><input type="date" name="from" value="{{ request('from') }}" class="form-control"><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
          <div><button class="btn btn-sm btn-primary">Filter</button> <a href="{{ route('admin.messages.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a></div>
        </div>
      </form>

      <div style="background:#fff;padding:12px;border-radius:8px">
        <style>
          /* highlight unread rows */
          .message-row.unread td { background: #eef6ff !important; }
          .detail-row td { background: #f8fafc; padding:12px; }
        </style>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
          <div>
            <button id="btn-mark-unread" class="btn btn-sm btn-primary" disabled>Mark as Unread</button>
            <a href="{{ route('admin.messages.index') }}" class="btn btn-sm btn-outline-secondary">Refresh</a>
          </div>
          <div>{{ $items->total() }} pesan</div>
        </div>

        <div style="overflow:auto;max-height:420px">
          <table class="table table-sm">
            <thead>
              <tr>
                <th style="width:40px"><input type="checkbox" id="select-all"></th>
                <th>Subject</th>
                <th>From</th>
                <th>Date</th>
                <th style="width:110px">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($items as $it)
                <tr data-id="{{ $it->id }}" class="message-row @if(!$it->is_read) unread @endif">
                  <td><input type="checkbox" class="msg-checkbox" value="{{ $it->id }}"></td>
                  <td><strong>@if(!$it->is_read)<span class="badge bg-danger">new</span> @endif</strong> <a href="#" class="message-link" data-id="{{ $it->id }}">{{ \Illuminate\Support\Str::limit($it->subject, 80) }}</a></td>
                  <td>{{ $it->name }}<br><small>{{ $it->email }}</small></td>
                  <td>{{ $it->created_at->format('Y-m-d H:i') }}</td>
                  <td>
                    <button class="btn btn-sm btn-primary view-btn" data-id="{{ $it->id }}">Baca Pesan</button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="mt-2">{{ $items->links() }}</div>

        <div id="message-panel" style="background:#f8fafc;padding:12px;border-radius:8px;margin-top:12px;min-height:120px">
          <h5>Detail Pesan</h5>
          <div id="message-panel-inner"><p class="text-muted">Pilih pesan dari tabel untuk melihat detail di sini.</p></div>
        </div>
      </div>
    </div>
  </div>
  <script>window.INBOX_UNREAD_HAS_STATUS = {{ \Illuminate\Support\Facades\Schema::hasColumn('messages','is_read') ? 'true' : 'false' }};</script>
  @push('scripts')
    <script>
      (function(){
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        function updateSidebarUnread(count){
          const unreadEl = document.getElementById('inbox-unread');
          const hasStatus = (typeof window.INBOX_UNREAD_HAS_STATUS !== 'undefined') ? window.INBOX_UNREAD_HAS_STATUS : true;
          const suffix = (!hasStatus) ? ' pesan' : ' pesan belum dibaca';
          const text = (count && count > 0) ? (count + suffix) : '';
          if(unreadEl){
            if(text) unreadEl.innerText = text; else unreadEl.remove();
          } else if(text){
            const link = document.getElementById('inbox-link');
            if(link){
              const span = document.createElement('span'); span.id = 'inbox-unread'; span.style.marginLeft='8px'; span.style.background='#ef4444'; span.style.color='#fff'; span.style.padding='6px 8px'; span.style.borderRadius='999px'; span.style.fontWeight='700'; span.style.fontSize='12px'; span.innerText = text; link.appendChild(span);
            }
          }
        }

        // open single message (via View or subject link) — show inline detail row below the message
        function openMessage(id){
          const row = document.querySelector('tr[data-id="'+id+'"]');
          if(!row) return;
          // if detail already open for this row, toggle close
          const next = row.nextElementSibling;
          if(next && next.classList && next.classList.contains('detail-row')){
            next.remove();
            return;
          }

          fetch("{{ url('admin/messages') }}" + '/' + id + '/open', { method: 'POST', headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'}, body: null })
            .then(r => r.json())
            .then(data => {
              // remove any existing detail rows
              document.querySelectorAll('tr.detail-row').forEach(el=>el.remove());
              // create a detail row and insert after this row
              const tr = document.createElement('tr'); tr.className = 'detail-row';
              const td = document.createElement('td'); td.colSpan = 5; td.innerHTML = data.html;
              tr.appendChild(td);
              row.parentNode.insertBefore(tr, row.nextSibling);

              // update unread badge
              updateSidebarUnread(data.unread);
              // update row UI: mark that row as read (remove badge and unread class)
              if(row){
                row.classList.remove('unread');
                const badge = row.querySelector('.badge.bg-danger'); if(badge) badge.remove();
              }
            }).catch(err => console.error(err));
        }

        // attach handlers
        document.querySelectorAll('.message-link, .view-btn').forEach(el=>{
          el.addEventListener('click', function(e){
            e.preventDefault();
            const id = this.dataset.id;
            if(id) openMessage(id);
          });
        });

        // select all / enabling bulk button
        const selectAll = document.getElementById('select-all');
        const checkboxes = () => Array.from(document.querySelectorAll('.msg-checkbox'));
        const btnMarkUnread = document.getElementById('btn-mark-unread');

        function refreshBulkState(){
          const any = checkboxes().some(cb=>cb.checked);
          if(btnMarkUnread) btnMarkUnread.disabled = !any;
        }

        if(selectAll){
          selectAll.addEventListener('change', function(){
            checkboxes().forEach(cb=> cb.checked = this.checked);
            refreshBulkState();
          });
        }

        document.addEventListener('change', function(e){
          if(e.target && e.target.classList && e.target.classList.contains('msg-checkbox')){
            refreshBulkState();
          }
        });

        // bulk mark unread
        if(btnMarkUnread){
          btnMarkUnread.addEventListener('click', function(e){
            e.preventDefault();
            const ids = checkboxes().filter(cb=>cb.checked).map(cb=>cb.value);
            if(!ids.length) return;
            if(!confirm('Mark '+ids.length+' pesan sebagai UNREAD?')) return;
            fetch("{{ url('admin/messages/mark-unread') }}", { method: 'POST', headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json','Content-Type':'application/json'}, body: JSON.stringify({ ids: ids }) })
              .then(r => r.json())
              .then(data => {
                if(data && data.unread !== undefined) updateSidebarUnread(data.unread);
                // update rows visually: add unread badge and class
                ids.forEach(id=>{
                  const row = document.querySelector('tr[data-id="'+id+'"]');
                  if(row){
                    row.classList.add('unread');
                    const cell = row.querySelector('td:nth-child(2)');
                    if(cell && !cell.querySelector('.badge.bg-danger')){
                      const span = document.createElement('span'); span.className='badge bg-danger'; span.innerText='new';
                      cell.insertBefore(span, cell.firstChild);
                    }
                  }
                });
                // clear selection
                checkboxes().forEach(cb=>cb.checked = false);
                if(selectAll) selectAll.checked = false;
                refreshBulkState();
              }).catch(err => console.error(err));
          });
        }

      })();
    </script>
  @endpush
</x-admin.layout>
