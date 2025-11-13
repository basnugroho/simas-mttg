    <section class="hero">
      <div class="hero-card">
        <div class="hero-media" aria-hidden="true">
          <img src="{{ asset('images/mosque.png') }}" alt="" aria-hidden="true" class="w-full h-full object-cover" />
        </div>
        <div class="hero-content hero-content-center">
          <h2>
            Sistem Informasi Majelis Taklim Telkom Group (MTTG)<br />
            <span class="accent">Majelis Taklim Telkom Group (MTTG)</span>
          </h2>
          <p class="subtitle">Telkom Regional 3 (Jawa Timur, Bali dan Nusa Tenggara)</p>
          <div class="search-wrap search-wrap-center">
            <div class="search">
              <input id="searchMasjid" type="text" placeholder="cari data masjid / musholla" aria-label="Cari data" autocomplete="off" />
              <button class="btn-search" type="button" aria-label="Cari Data">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M10 2a8 8 0 105.293 14.293l4.207 4.207a1 1 0 001.414-1.414l-4.207-4.207A8 8 0 0010 2zm0 2a6 6 0 110 12A6 6 0 0110 4z"/>
                </svg>
                Cari Data
              </button>

              <!-- SUGGESTION DROPDOWN -->
              <ul id="searchSuggestions" class="search-suggestions" role="listbox"></ul>
            </div>
          </div>
        </div>
      </div>
    </section>
    <script>
  // Contoh data statis dulu.
  // Nanti bisa diganti dengan data dari server (AJAX/fetch).
  const MASJID_DATA = [
    "Masjid Telkom Regional 3",
    "Musholla Telkom Kantor Pusat",
    "Masjid Al-Falah Surabaya",
    "Musholla Graha Telkom",
    "Masjid Al-Ikhlas Denpasar",
    "Masjid Baiturrahman Mataram",
    "Musholla STO Telkom Malang",
    "Masjid Raya Telkom Bandung",
  ];

  const input = document.getElementById('searchMasjid');
  const suggestionBox = document.getElementById('searchSuggestions');

  function renderSuggestions(list) {
    if (!list.length) {
      suggestionBox.classList.remove('show');
      suggestionBox.innerHTML = '';
      return;
    }

    suggestionBox.innerHTML = list
      .map(item => `
        <li role="option" data-value="${item}">
          <span class="label">${item}</span>
        </li>
      `)
      .join('');

    suggestionBox.classList.add('show');
  }

  // Event: ketika user mengetik
  input.addEventListener('input', function () {
    const query = this.value.trim().toLowerCase();

    if (!query) {
      suggestionBox.classList.remove('show');
      suggestionBox.innerHTML = '';
      return;
    }

    const filtered = MASJID_DATA
      .filter(name => name.toLowerCase().includes(query))
      .slice(0, 8); // batasi jumlah suggestion

    renderSuggestions(filtered);
  });

  // Event: klik pada suggestion
  suggestionBox.addEventListener('click', function (e) {
    const li = e.target.closest('li[data-value]');
    if (!li) return;

    const value = li.getAttribute('data-value');
    input.value = value;
    suggestionBox.classList.remove('show');
    suggestionBox.innerHTML = '';

    // Di sini bisa langsung trigger submit/pencarian:
    // document.querySelector('.btn-search').click();
  });

  // Event: klik di luar untuk menutup dropdown
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.search')) {
      suggestionBox.classList.remove('show');
    }
  });
</script>
