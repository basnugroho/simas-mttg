@component('components.administrator.layout')
  @slot('title')
    {{ $title ?? 'MTTG - Dashboard' }}
  @endslot

  @section('header')
  <div class="col-sm-6"><h3 class="mb-0">Dashboard</h3></div>
  <div class="col-sm-6">
    <ol class="breadcrumb float-sm-end">
      <li class="breadcrumb-item"><a href="#">Beranda</a></li>
    </ol>
  </div>
  @show

  @section('content')
  <div class="container-fluid">
    @include('administrator.dashboard._card')

    @include('administrator.dashboard._charts')

    @include('administrator.dashboard._filter')

    <div class="row mt-1 g-3 align-items-stretch">
      @include('administrator.dashboard._facilities')
      
      @include('administrator.dashboard._map')
    </div>
  </div>

  @endsection

  @push('head')
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="" crossorigin="" />
  <style>
    /* dark-ish card look similar to screenshot */
    body .card { border-radius: .5rem }
  </style>
  @endpush

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function(){
    // Plugin: draw value labels on each pie slice and total in center
    const sliceLabelPlugin = {
      id: 'sliceLabelPlugin',
      afterDraw(chart) {
        if (!chart || !chart.config) return;
        const type = chart.config.type;
        if (type !== 'doughnut' && type !== 'pie') return;
        const ctx = chart.ctx;
        const datasets = chart.data && chart.data.datasets ? chart.data.datasets : [];
        if (!datasets.length) return;

        datasets.forEach((dataset, dsIndex) => {
          const meta = chart.getDatasetMeta(dsIndex);
          if (!meta || !meta.data) return;

          // draw slice labels (value) at slice centre with automatic contrast
          function parseColorToRgb(col){
            if (!col) return null;
            // hex
            if (col[0] === '#'){
              const hex = col.replace('#','');
              const bigint = parseInt(hex.length===3? hex.split('').map(c=>c+c).join('') : hex, 16);
              return { r: (bigint >> 16) & 255, g: (bigint >> 8) & 255, b: bigint & 255 };
            }
            // rgb(a)
            const m = col.match(/rgba?\(([^)]+)\)/);
            if (m){
              const parts = m[1].split(',').map(s=>parseFloat(s));
              return { r: parts[0], g: parts[1], b: parts[2] };
            }
            return null;
          }

          function luminance(rgb){
            if (!rgb) return 0;
            // simple luminance
            return 0.2126*rgb.r + 0.7152*rgb.g + 0.0722*rgb.b;
          }

          ctx.save();
          meta.data.forEach((arc, i) => {
            const val = dataset.data[i];
            if (val === null || val === undefined) return;
            const start = arc.startAngle;
            const end = arc.endAngle;
            const mid = (start + end) / 2;
            const r = (arc.outerRadius + (arc.innerRadius || 0)) / 2;
            const x = arc.x + Math.cos(mid) * r;
            const y = arc.y + Math.sin(mid) * r;

            // determine slice color
            let sliceColor = null;
            try {
              const ds = chart.data.datasets[dsIndex];
              if (ds && ds.backgroundColor){
                sliceColor = Array.isArray(ds.backgroundColor) ? ds.backgroundColor[i] : ds.backgroundColor;
              }
            } catch(e){ sliceColor = null; }
            if (!sliceColor){
              try { sliceColor = arc.options && arc.options.backgroundColor; } catch(e) { sliceColor = null; }
            }

            const rgb = parseColorToRgb(String(sliceColor || ''));
            const lum = luminance(rgb);
            const textColor = lum > 180 ? '#111827' : '#ffffff';

            ctx.fillStyle = textColor;
            ctx.font = '600 11px system-ui, Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(String(val), x, y);
          });
          ctx.restore();

          // draw center total for the first dataset only
          if (dsIndex === 0) {
            const total = dataset.data.reduce((s, v) => s + (parseFloat(v) || 0), 0);
            const anyArc = meta.data && meta.data[0];
            const centerX = anyArc ? anyArc.x : (chart.width / 2);
            const centerY = anyArc ? anyArc.y : (chart.height / 2);
            ctx.save();
            ctx.fillStyle = '#111827';
            ctx.font = '700 16px system-ui, Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(String(total), centerX, centerY);
            ctx.restore();
          }
        });
      }
    };
    // register plugin
    if (window.Chart && !Chart.registry.plugins.get('sliceLabelPlugin')) {
      Chart.register(sliceLabelPlugin);
    }
    // Area Pie (masjid & musholla) and Bar chart fallback
    const areaPieData = @json($areaPieData ?? null);
    const barData = @json($barData ?? []);

    if (areaPieData) {
      const labels = areaPieData.labels || [];
      const masjidDs = (areaPieData.datasets && areaPieData.datasets[0]) ? areaPieData.datasets[0].data : [];
      const mushollaDs = (areaPieData.datasets && areaPieData.datasets[1]) ? areaPieData.datasets[1].data : [];

      const masjidEl = document.getElementById('masjidPie');
      const mushollaEl = document.getElementById('mushollaPie');
      function defaultPalette(n){
       const palette = ['#1f77b4','#ff7f0e','#2ca02c','#d62728','#9467bd','#8c564b','#e377c2','#7f7f7f','#bcbd22','#17becf'];
        return Array.from({length:n}, (_,i)=>palette[i % palette.length]);
      }
      // map area-specific colors
      function areaColorMap(label){
        const map = {
          'Jawa Timur': '#434E78',
          'jawa_timur': '#434E78',
          'Bali': '#F7E396',
          'bali': '#F7E396',
          'Nusa Tenggara': '#E97F4A',
          'nusa_tenggara': '#E97F4A'
        };
        return map[label] || null;
      }
      if (masjidEl) {
        const ctx = (masjidEl.getContext && masjidEl.getContext('2d')) || (masjidEl.getContext ? masjidEl.getContext('2d') : null);
        if (ctx) {
           // construct background colors with area-specific overrides
           let bg = [];
           for (let i=0;i<labels.length;i++){
             const c = areaColorMap(labels[i]) || (areaPieData.datasets[0] && areaPieData.datasets[0].backgroundColor && areaPieData.datasets[0].backgroundColor[i]) || defaultPalette(labels.length)[i];
             bg.push(c);
           }
           new Chart(ctx, { type:'doughnut', data: { labels: labels, datasets:[{ data: masjidDs, backgroundColor: bg }] }, options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'right',display:true, labels:{usePointStyle:true,boxWidth:8,padding:6}}} } });
        }
      }
      if (mushollaEl) {
        const ctx = (mushollaEl.getContext && mushollaEl.getContext('2d')) || (mushollaEl.getContext ? mushollaEl.getContext('2d') : null);
        if (ctx) {
          let bg = [];
            const rawBg = (areaPieData.datasets[1] && areaPieData.datasets[1].backgroundColor && areaPieData.datasets[1].backgroundColor.length) ? areaPieData.datasets[1].backgroundColor : defaultPalette(labels.length);
            // filter out zero-value entries so they are not shown on the chart
            const fLabels = [];
            const fData = [];
            const fBg = [];
            for (let i=0;i<labels.length;i++){
              const v = parseFloat(mushollaDs[i]) || 0;
              if (v === 0) continue; // skip zeros
              fLabels.push(labels[i]);
              fData.push(v);
              fBg.push(areaColorMap(labels[i]) || rawBg[i] || defaultPalette(labels.length)[i]);
            }
            new Chart(ctx, { type:'doughnut', data: { labels: fLabels, datasets:[{ data: fData, backgroundColor: fBg }] }, options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'right',display:true, labels:{usePointStyle:true,boxWidth:8,padding:6}}} } });
        }
      }
    } else {
      const barEl = document.getElementById('barChart');
      if (barEl) {
        const barCtx = (barEl.getContext && barEl.getContext('2d')) || (barEl.getContext ? barEl.getContext('2d') : null);
        if (barCtx) new Chart(barCtx, {
          type: 'bar',
          data: barData,
          options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}} }
        });
      }
    }

    // Masjid completeness counts chart (100% vs <100%)
    const masjidCompleteData = @json($masjidCompleteChart ?? null);
    const masjidCompleteEl = document.getElementById('masjidCompleteChart');
    if (masjidCompleteEl && masjidCompleteData) {
      const mctx = (masjidCompleteEl.getContext && masjidCompleteEl.getContext('2d')) || (masjidCompleteEl.getContext ? masjidCompleteEl.getContext('2d') : null);
      if (mctx) {
        // enable datalabels only when plugin is available; pass plugin instance explicitly
        const _masjidPlugins = (window.ChartDataLabels ? [ChartDataLabels] : []);
        const _masjidOptions = {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: true, position: 'bottom' },
            tooltip: { callbacks: { label: function(ctx){ return ctx.dataset.label + ': ' + (Math.round(ctx.parsed.x || ctx.parsed.y || 0)); } } }
          },
          scales: {
            x: { stacked: true, beginAtZero:true, ticks:{ precision:0, stepSize:1, callback: function(val){ return Math.round(val); } } },
            y: { stacked: false }
          },
          datasets: { bar: { categoryPercentage: 0.7, barPercentage: 0.9, borderRadius: 6 } }
        };
        if (window.ChartDataLabels) {
          _masjidOptions.plugins.datalabels = { display: true, color: '#fff', formatter: (v)=> (v||v===0)?String(Math.round(v)):'', font:{weight:'700',size:11} };
        }
        new Chart(mctx, {
          type: 'bar',
          data: masjidCompleteData,
          options: Object.assign({}, _masjidOptions, {
            // grouped vertical bars (x axis = areas)
            plugins: Object.assign({}, _masjidOptions.plugins, { tooltip: { callbacks: { label: function(ctx){ return ctx.dataset.label + ': ' + (Math.round(ctx.parsed.y || ctx.parsed.x || 0)); } } } }),
            scales: {
              x: { stacked: false, ticks: { color: '#cbd5e1' }, grid: { color: 'rgba(255,255,255,0.03)' } },
              y: { stacked: false, beginAtZero: true, ticks: { precision:0, stepSize:1, callback: function(v){ return Math.round(v); } }, grid: { color: 'rgba(255,255,255,0.03)' } }
            }
          }),
          plugins: _masjidPlugins
        });
      }
    }

    // Donut charts
    const donutMasjidData = @json($donutMasjid ?? null);
    if (donutMasjidData) {
      const donutMasjidEl = document.getElementById('donutMasjid');
      const donutMasjid = donutMasjidEl && ((donutMasjidEl.getContext && donutMasjidEl.getContext('2d')) || (donutMasjidEl.getContext ? donutMasjidEl.getContext('2d') : null));
      if (donutMasjid) new Chart(donutMasjid, Object.assign({ type: 'doughnut' }, donutMasjidData, { options: Object.assign({ cutout:'70%' }, donutMasjidData.options || {}) }));
    }

    const donutMushollaData = @json($donutMusholla ?? null);
    if (donutMushollaData) {
      const donutMushollaEl = document.getElementById('donutMusholla');
      const donutMusholla = donutMushollaEl && ((donutMushollaEl.getContext && donutMushollaEl.getContext('2d')) || (donutMushollaEl.getContext ? donutMushollaEl.getContext('2d') : null));
      if (donutMusholla) new Chart(donutMusholla, Object.assign({ type: 'doughnut' }, donutMushollaData, { options: Object.assign({ cutout:'70%' }, donutMushollaData.options || {}) }));
    }

    // Map initialization and marker rendering are handled in the `_map.blade.php` partial.
  });
  </script>
  @endpush

@endcomponent
