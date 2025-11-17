<x-admin.layout title="MTTG - Dashboard">
  @auth
    <div class="d-flex" style="min-height:100vh">
      <!-- Left sidebar -->
      <aside style="width:260px; background:#0b1220; color:#fff; padding:20px; display:flex; flex-direction:column;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:18px">
          <img src="https://svgshare.com/i/14jG.svg" alt="logo" style="width:44px;height:44px;border-radius:8px;background:#fff;padding:6px;" onerror="this.style.display='none'">
          <div>
            <div style="font-weight:700">MTTG</div>
            <div style="font-size:12px; opacity:.8">Dashboard</div>
          </div>
        </div>

        <nav>
          <ul id="sidebar-menu" style="list-style:none;padding:0;margin:0;">
            <li style="margin-bottom:8px" data-key="dashboard"><a href="#" class="menu-link" style="color:#fff;text-decoration:none;padding:10px 12px;display:flex;align-items:center;gap:10px;border-radius:8px"><span style="width:18px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 11.5L12 4l9 7.5" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Dashboard</a></li>
            <li style="margin-bottom:8px" data-key="master"><a href="{{ route('admin.regions.index') }}" class="menu-link" style="color:#fff;text-decoration:none;padding:10px 12px;display:flex;align-items:center;gap:10px;border-radius:8px"><span style="width:18px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 11h16v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-6z" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Master</a></li>
            <li style="margin-bottom:8px" data-key="masjid"><a href="#" class="menu-link" style="color:#fff;text-decoration:none;padding:10px 12px;display:flex;align-items:center;gap:10px;border-radius:8px"><span style="width:18px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 2v6" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 11h16v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-6z" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Masjid</a></li>
            <li style="margin-bottom:8px" data-key="mushalla"><a href="#" class="menu-link" style="color:#fff;text-decoration:none;padding:10px 12px;display:flex;align-items:center;gap:10px;border-radius:8px"><span style="width:18px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3" stroke="#fff" stroke-width="1.5"/><path d="M5 20c2-4 5-6 7-6s5 2 7 6" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Mushalla</a></li>
            <li style="margin-bottom:8px" data-key="info"><a href="#" class="menu-link" style="color:#fff;text-decoration:none;padding:10px 12px;display:flex;align-items:center;gap:10px;border-radius:8px"><span style="width:18px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="16" rx="2" stroke="#fff" stroke-width="1.5"/><path d="M7 8h10" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg></span>Info Terkini</a></li>
            <li style="margin-bottom:8px" data-key="unduh"><a href="#" class="menu-link" style="color:#fff;text-decoration:none;padding:10px 12px;display:flex;align-items:center;gap:10px;border-radius:8px"><span style="width:18px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 3v12" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/><path d="M8 11l4 4 4-4" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 21H3" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg></span>Unduh Data</a></li>
            <li style="margin-bottom:8px" data-key="inbox"><a href="#" class="menu-link" style="color:#fff;text-decoration:none;padding:10px 12px;display:flex;align-items:center;gap:10px;border-radius:8px"><span style="width:18px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 8l9 6 9-6" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><rect x="3" y="4" width="18" height="16" rx="2" stroke="#fff" stroke-width="1.5"/></svg></span>Kotak Masuk</a></li>
            <li style="margin-bottom:8px" data-key="userbkm"><a href="#" class="menu-link" style="color:#fff;text-decoration:none;padding:10px 12px;display:flex;align-items:center;gap:10px;border-radius:8px"><span style="width:18px;display:inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10z" stroke="#fff" stroke-width="1.5"/><path d="M4 20v-1a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v1" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>User BKM</a></li>
          </ul>
        </nav>

        <div style="border-top:1px solid rgba(255,255,255,.06); padding-top:12px">
          <a href="#" style="display:block;color:#fff;text-decoration:none;padding:8px 6px">Setting</a>
          <a href="{{ route('admin.users') }}" style="display:block;color:#fff;text-decoration:none;padding:8px 6px">User Admin</a>
          <form method="POST" action="{{ route('logout') }}" style="margin-top:8px">@csrf<button type="submit" style="background:none;border:none;color:#fff;padding:8px 6px;cursor:pointer;text-align:left">Logout</button></form>
        </div>
      </aside>

      <!-- Main content -->
      <div style="flex:1; padding:22px; background:#f6f7fb">
        <header style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px">
          <div>
            <h1 style="margin:0; font-size:20px">Dashboard</h1>
            <small style="color:#6b7280">Selamat datang, {{ Auth::user()->name }}</small>
          </div>

          <div style="display:flex; align-items:center; gap:12px">
            <!-- user nav -->
            <div style="display:flex; align-items:center; gap:8px; background:#fff;padding:8px 10px;border-radius:999px;box-shadow:0 6px 18px rgba(2,6,23,.06)">
              <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=ef4444&color=fff" style="width:38px;height:38px;border-radius:50%">
              <div style="text-align:left">
                <div style="font-weight:600">{{ Auth::user()->name }}</div>
                <div style="font-size:12px;color:#6b7280">{{ Auth::user()->role }}</div>
              </div>
              <div style="margin-left:6px; position:relative">
                <button id="userToggle" style="background:none;border:0;cursor:pointer;font-size:18px">▾</button>
                <div id="userMenu" style="display:none; position:absolute; right:0; top:28px; background:#fff; border-radius:8px; box-shadow:0 8px 20px rgba(2,6,23,.12); overflow:hidden">
                  <a href="{{ route('admin.users') }}" style="display:block;padding:8px 12px; text-decoration:none; color:#111">User Admin</a>
                  <form method="POST" action="{{ route('logout') }}" style="margin:0">
                    @csrf
                    <button type="submit" style="display:block;width:100%;border:0;background:none;padding:8px 12px;text-align:left;cursor:pointer">Logout</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </header>

        <!-- small style helpers for menu and cards -->
        <style>
          .menu-link.active { background: linear-gradient(90deg,#f33 0,#f65 100%); opacity: .12; }
          .summary-cards { display:flex; gap:12px; margin-bottom:18px; }
          .summary-card { background:#fff;padding:14px;border-radius:10px; box-shadow:0 8px 24px rgba(2,6,23,.04); flex:1; display:flex; flex-direction:column; }
          .summary-card.total { flex: .9; align-items:center; justify-content:center; }
          @media (max-width:900px){ .summary-cards{flex-direction:column} }
        </style>

        <!-- summary cards (regions + total) -->
        <div class="summary-cards">
          <div class="summary-card">
            <div style="font-size:12px;color:#6b7280">Jawa Timur</div>
            <div style="font-weight:700;font-size:22px; display:flex; align-items:center; gap:10px">
              <span style="width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; background:#f3f4f6; border-radius:8px">
                <!-- building icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 21h18" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M6 21V9" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M10 21V5" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M14 21V11" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M18 21V7" stroke="#111" stroke-width="1.2" stroke-linecap="round"/></svg>
              </span>
              <span><span id="jt-count">—</span> <small style="font-size:12px;color:#6b7280">Total</small></span>
            </div>
            <div style="font-size:12px;color:#6b7280">Masjid: <span id="jt-masjid">—</span> | Mushalla: <span id="jt-mushalla">—</span></div>
          </div>
          <div class="summary-card">
            <div style="font-size:12px;color:#6b7280">Bali</div>
            <div style="font-weight:700;font-size:22px; display:flex; align-items:center; gap:10px">
              <span style="width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; background:#f3f4f6; border-radius:8px">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 21h18" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M6 21V9" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M10 21V5" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M14 21V11" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M18 21V7" stroke="#111" stroke-width="1.2" stroke-linecap="round"/></svg>
              </span>
              <span><span id="bali-count">—</span> <small style="font-size:12px;color:#6b7280">Total</small></span>
            </div>
            <div style="font-size:12px;color:#6b7280">Masjid: <span id="bali-masjid">—</span> | Mushalla: <span id="bali-mushalla">—</span></div>
          </div>
          <div class="summary-card">
            <div style="font-size:12px;color:#6b7280">Nusa Tenggara</div>
            <div style="font-weight:700;font-size:22px; display:flex; align-items:center; gap:10px">
              <span style="width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; background:#f3f4f6; border-radius:8px">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 21h18" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M6 21V9" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M10 21V5" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M14 21V11" stroke="#111" stroke-width="1.2" stroke-linecap="round"/><path d="M18 21V7" stroke="#111" stroke-width="1.2" stroke-linecap="round"/></svg>
              </span>
              <span><span id="nt-count">—</span> <small style="font-size:12px;color:#6b7280">Total</small></span>
            </div>
            <div style="font-size:12px;color:#6b7280">Masjid: <span id="nt-masjid">—</span> | Mushalla: <span id="nt-mushalla">—</span></div>
          </div>
          <div class="summary-card total" style="background:#000;color:#fff">
            <div style="font-size:12px;color:#cbd5e1">Total Keseluruhan</div>
            <div style="font-weight:800;font-size:28px; display:flex; align-items:center; gap:10px"><span style="width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; background:#111; border-radius:8px"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 21h18" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/><path d="M6 21V9" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/><path d="M10 21V5" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/><path d="M14 21V11" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/><path d="M18 21V7" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/></svg></span><span id="total-count">—</span></div>
            <div style="font-size:12px;color:#cbd5e1">Masjid: <span id="total-masjid">—</span> | Mushalla: <span id="total-mushalla">—</span></div>
          </div>
        </div>

        <!-- Top charts area -->
        <div style="display:flex; gap:18px; margin-bottom:18px">
          <div id="stackedCard" style="flex:2; background:#0f1724;padding:18px;border-radius:12px; box-shadow:0 8px 24px rgba(2,6,23,.04); color:#e5e7eb">
            <h4 style="margin:0 0 12px 0; color:#e5e7eb">Data Masjid & Mushalla Jatim, Bali dan Nusa Tenggara</h4>
            <canvas id="stackedBar" height="220"></canvas>
          </div>
          <div style="flex:1; display:flex; flex-direction:column; gap:12px">
            <div style="background:#fff;padding:12px;border-radius:12px; box-shadow:0 8px 24px rgba(2,6,23,.04)">
              <div id="pie1-total" style="text-align:center;font-weight:700;font-size:18px;margin-bottom:8px;color:#111">— Masjid</div>
              <div style="padding:6px;border-radius:8px;background:#fff"><canvas id="pie1" height="120"></canvas></div>
            </div>
            <div style="background:#fff;padding:12px;border-radius:12px; box-shadow:0 8px 24px rgba(2,6,23,.04)">
              <div id="pie2-total" style="text-align:center;font-weight:700;font-size:18px;margin-bottom:8px;color:#111">— Mushalla</div>
              <div style="padding:6px;border-radius:8px;background:#fff"><canvas id="pie2" height="120"></canvas></div>
            </div>
          </div>
        </div>

        <!-- bottom area: table + map -->
        <div style="display:flex; gap:18px">
          <div style="flex:1; background:#fff;padding:12px;border-radius:12px; box-shadow:0 8px 24px rgba(2,6,23,.04); max-height:420px; overflow:auto">
            <h4 style="margin-top:0">Masjid / Mushalla (Fasilitas Dummy)</h4>
            <table class="table table-sm">
              <thead>
                <tr><th>#</th><th>Nama</th><th>Lokasi</th><th>Kelengkapan</th></tr>
              </thead>
              <tbody>
                @for($i=1;$i<=10;$i++)
                  <tr>
                    <td>{{ $i }}</td>
                    <td>Masjid Contoh {{ $i }}</td>
                    <td>Kota {{ ['Surabaya','Malang','Bali','Denpasar','Mataram'][($i-1)%5] }}</td>
                    <td>{{ rand(60,100) }}%</td>
                  </tr>
                @endfor
              </tbody>
            </table>
          </div>

          <div style="flex:1; background:#fff;padding:12px;border-radius:12px; box-shadow:0 8px 24px rgba(2,6,23,.04);">
            <h4 style="margin-top:0">Peta</h4>
            <div id="map" style="height:360px;border-radius:8px;overflow:hidden"></div>
          </div>
        </div>

      </div>
    </div>

    <!-- scripts for charts and map -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
      // register datalabels plugin
      if(window && window.Chart && window.ChartDataLabels){
        Chart.register(window.ChartDataLabels);
      }
    </script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
      // user menu toggle
      document.getElementById('userToggle').addEventListener('click', function(){
        const m = document.getElementById('userMenu');
        m.style.display = m.style.display === 'block' ? 'none' : 'block';
      });

      // sidebar menu active handling (persist in localStorage)
      (function(){
        const menu = document.getElementById('sidebar-menu');
        if(!menu) return;
        function setActive(key){
          menu.querySelectorAll('li').forEach(li=>{
            const a = li.querySelector('a.menu-link');
            if(!a) return;
            if(li.dataset.key === key){
              a.classList.add('active');
            } else {
              a.classList.remove('active');
            }
          });
          try{ localStorage.setItem('simas_active', key); }catch(e){}
        }
        menu.querySelectorAll('li').forEach(li=>{
          li.addEventListener('click', function(e){ e.preventDefault(); setActive(li.dataset.key); });
        });
        const saved = localStorage.getItem('simas_active') || 'dashboard';
        setActive(saved);
      })();

      // stacked/grouped bar chart (Masjid & Mushalla per region)
      const ctx = document.getElementById('stackedBar').getContext('2d');
      const stackedBar = new Chart(ctx, {
        type: 'bar',
        data: {
          // x-axis: regions
          labels: ['Jawa Timur','Bali','Nusa Tenggara'],
          datasets: [
            // Masjid stack (shows Lengkap then Belum Lengkap stacked)
            { label: 'Masjid (Lengkap)', data: [5,2,3], backgroundColor: '#ef4444', stack: 'masjid' },
            { label: 'Masjid (Belum Lengkap)', data: [3,1,1], backgroundColor: '#fca5a5', stack: 'masjid' },
            // Mushalla stack
            { label: 'Mushalla (Lengkap)', data: [4,3,2], backgroundColor: '#10b981', stack: 'mushalla' },
            { label: 'Mushalla (Belum Lengkap)', data: [3,1,4], backgroundColor: '#9fe6c9', stack: 'mushalla' },
          ]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              display: true,
              position: 'bottom',
              labels: { usePointStyle: true, pointStyle: 'circle', color: '#e5e7eb' }
            },
            tooltip: { enabled: true }
          },
          // grouped stacks: x not stacked so different 'stack' groups are side-by-side,
          // y stacked so datasets with same stack value stack on top of each other
          scales: {
            x: { stacked: false, ticks: { color: '#cbd5e1' }, grid: { color: 'rgba(255,255,255,0.03)' } },
            y: { stacked: true, beginAtZero:true, ticks: { color: '#cbd5e1' }, grid: { color: 'rgba(255,255,255,0.03)' } }
          },
          // spacing tweaks so two stacks per region look balanced
          datasets: {
            bar: { categoryPercentage: 0.6, barPercentage: 0.9 }
          }
        }
      });

      // pies with data labels (values only) and right-side legend

  const pie1 = new Chart(document.getElementById('pie1'), {
        type: 'doughnut',
        data: { labels:['Jawa Timur','Bali','Nusa Tenggara'], datasets:[{data:[8,3,4], backgroundColor:['#3b82f6','#ef4444','#10b981']}] },
        options:{
          responsive:true,
          plugins: {
            legend: { position: 'right', labels: { usePointStyle: true, pointStyle: 'circle' } },
            datalabels: {
              color: '#fff',
              formatter: (value) => { return value; },
              font: { weight: '600', size: 12 }
            }
          }
        },
        plugins: [ChartDataLabels]
      });

      // set pie total text above each pie (will run after both pies are created)

      const pie2 = new Chart(document.getElementById('pie2'), {
        type: 'doughnut',
        data: { labels:['Jawa Timur','Bali','Nusa Tenggara'], datasets:[{data:[6,7,4], backgroundColor:['#f59e0b','#ef4444','#3b82f6']}] },
        options:{
          responsive:true,
          plugins: {
            legend: { position: 'right', labels: { usePointStyle: true, pointStyle: 'circle' } },
            datalabels: {
              color: '#fff',
              formatter: (value) => { return value; },
              font: { weight: '600', size: 12 }
            }
          }
        },
        plugins: [ChartDataLabels]
      });

      // compute and set pie totals after both charts are ready
      try{
        const sumPie = (chart) => (chart && chart.data && chart.data.datasets[0].data || []).reduce((a,b)=>a+Number(b||0),0);
        const total1 = sumPie(pie1);
        const total2 = sumPie(pie2);
        const el1 = document.getElementById('pie1-total');
        const el2 = document.getElementById('pie2-total');
        if(el1) { el1.innerText = total1 + ' Masjid'; el1.style.fontSize = '18px'; el1.style.fontWeight = '700'; }
        if(el2) { el2.innerText = total2 + ' Mushalla'; el2.style.fontSize = '18px'; el2.style.fontWeight = '700'; }
      }catch(e){ console.warn('set pie totals failed', e); }

      // Ensure bar chart doesn't show datalabels and update summary cards from datasets
      try{
        if(stackedBar && stackedBar.data && stackedBar.data.datasets){
          // explicitly disable datalabels for bar
          if(stackedBar.options.plugins) stackedBar.options.plugins.datalabels = { display: false };

          // compute sums per region
          const ds = stackedBar.data.datasets;
          const regions = stackedBar.data.labels; // ['Jawa Timur','Bali','Nusa Tenggara']
          const idMap = ['jt','bali','nt'];
          let grandTotal = 0;
          let grandLengkap = 0;
          idMap.forEach((id, idx) => {
            let total = 0, masjid = 0, mushalla = 0;
            ds.forEach(d => {
              const v = Number(d.data[idx] || 0);
              total += v;
              if(/Masjid/i.test(d.label)) masjid += v;
              if(/Mushalla|Mushola|Musholla/i.test(d.label)) mushalla += v;
            });
            grandTotal += total;
            // accumulate totals for grand breakdown
            // attach per-region values to DOM
            const countEl = document.getElementById(id + '-count');
            const masjidEl = document.getElementById(id + '-masjid');
            const mushEl = document.getElementById(id + '-mushalla');
            if(countEl) countEl.innerText = total;
            if(masjidEl) masjidEl.innerText = masjid;
            if(mushEl) mushEl.innerText = mushalla;
            // sum grand breakdown
            const grandMasjidEl = document.getElementById('total-masjid');
            const grandMushEl = document.getElementById('total-mushalla');
            if(!window.__grandMasjid) window.__grandMasjid = 0;
            if(!window.__grandMush) window.__grandMush = 0;
            window.__grandMasjid += masjid;
            window.__grandMush += mushalla;
          });
          const totalEl = document.getElementById('total-count');
          if(totalEl) totalEl.innerText = grandTotal;
          const grandMasjidEl = document.getElementById('total-masjid');
          const grandMushEl = document.getElementById('total-mushalla');
          if(grandMasjidEl) grandMasjidEl.innerText = window.__grandMasjid || 0;
          if(grandMushEl) grandMushEl.innerText = window.__grandMush || 0;
        }
      }catch(e){ console.warn('update cards failed', e); }

      // leaflet map (dummy)
      const map = L.map('map').setView([-7.25,112.75],7);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
      L.marker([-7.25,112.75]).addTo(map).bindPopup('Lokasi contoh: Jawa Timur').openPopup();
    </script>

  @else
    <div class="container py-6">
      <div class="card p-4">
        <h4>Autentikasi Diperlukan</h4>
        <p>Anda perlu <a href="{{ route('login') }}">login</a> untuk mengakses dashboard.</p>
      </div>
    </div>
  @endauth
</x-admin.layout>
