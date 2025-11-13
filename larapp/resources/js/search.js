// Fungsi untuk melakukan pencarian masjid/musholla
export async function searchMosques(query, options = {}) {
  const {
    page = 1,
    per_page = 10,
    type = '',
    sort_by = 'name'
  } = options;

  try {
    const params = new URLSearchParams({
      search: query,
      page,
      per_page,
      sort_by
    });

    if (type) {
      params.append('type', type);
    }

    const response = await fetch(`/api/mosques?${params.toString()}`);
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const result = await response.json();
    return result;
  } catch (error) {
    console.error('Error searching mosques:', error);
    throw error;
  }
}

// Fungsi untuk get detail masjid
export async function getMosqueDetail(id) {
  try {
    const response = await fetch(`/api/mosques/${id}`);
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const result = await response.json();
    return result.data;
  } catch (error) {
    console.error('Error fetching mosque detail:', error);
    throw error;
  }
}

// Fungsi untuk format data masjid
export function formatMosqueCard(mosque) {
  return `
    <div class="mosque-card" data-id="${mosque.id}">
      <div class="mosque-card-header">
        <div class="mosque-card-type ${mosque.type.toLowerCase()}">${mosque.type}</div>
        <div class="mosque-card-completion">${mosque.completion_percentage}%</div>
      </div>
      <h3 class="mosque-card-title">${mosque.name}</h3>
      <p class="mosque-card-location">${mosque.address || 'Alamat tidak tersedia'}</p>
      <div class="mosque-card-footer">
        <span class="mosque-card-status ${mosque.is_active ? 'active' : 'inactive'}">
          ${mosque.is_active ? 'Aktif' : 'Tidak Aktif'}
        </span>
        <a href="/mosques/${mosque.id}" class="btn-view-detail">Lihat Detail</a>
      </div>
    </div>
  `;
}

// Initialize search functionality
export function initializeSearch() {
  const searchInput = document.querySelector('.search input[type="text"]');
  const searchButton = document.querySelector('.btn-search');
  const searchResults = document.getElementById('search-results');

  if (!searchInput || !searchButton) {
    return;
  }

  async function performSearch() {
    const query = searchInput.value.trim();

    if (!query) {
      alert('Silakan masukkan kata kunci pencarian');
      return;
    }

    try {
      // Show loading state
      searchButton.disabled = true;
      searchButton.textContent = 'Mencari...';

      const result = await searchMosques(query, {
        per_page: 20,
        sort_by: 'name'
      });

      if (result.success) {
        // Simpan hasil ke session storage untuk ditampilkan di halaman hasil
        sessionStorage.setItem('searchResults', JSON.stringify(result.data));
        sessionStorage.setItem('searchQuery', query);

        // Redirect ke halaman hasil pencarian
        window.location.href = `/search?q=${encodeURIComponent(query)}`;
      } else {
        alert('Pencarian gagal: ' + result.message);
      }
    } catch (error) {
      alert('Error: ' + error.message);
    } finally {
      searchButton.disabled = false;
      searchButton.textContent = 'Cari Data';
    }
  }

  // Handle click button
  searchButton.addEventListener('click', performSearch);

  // Handle enter key
  searchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
      performSearch();
    }
  });
}

// Initialize search when page loads
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeSearch);
} else {
  initializeSearch();
}
