<!-- Advanced Search Component for Masjid/Musholla -->
<!-- Usage: @include('components.advanced-search') -->

<div class="advanced-search-component">
  <div class="search-container">
    <h2 class="search-title">Cari Masjid/Musholla</h2>
    
    <!-- Main Search Bar -->
    <form class="search-form" id="advanced-search-form">
      <div class="search-input-group">
        <div class="search-input-wrapper">
          <input 
            type="text" 
            id="advanced-search-input" 
            class="search-input"
            placeholder="Cari berdasarkan nama atau alamat..."
            aria-label="Cari masjid atau musholla"
            autocomplete="off"
          />
          <button type="submit" class="search-btn" aria-label="Cari">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
              <path d="M10 2a8 8 0 105.293 14.293l4.207 4.207a1 1 0 001.414-1.414l-4.207-4.207A8 8 0 0010 2zm0 2a6 6 0 110 12A6 6 0 0110 4z"/>
            </svg>
            Cari
          </button>
        </div>
        
        <!-- Search Suggestions -->
        <ul id="search-suggestions" class="search-suggestions" style="display: none;">
          <!-- Suggestions will be populated here -->
        </ul>
      </div>

      <!-- Advanced Filters -->
      <details class="filters-details">
        <summary class="filters-summary">
          <span>Filter Lanjutan</span>
          <svg class="details-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M7 10l5 5 5-5z"/>
          </svg>
        </summary>

        <div class="filters-content">
          <!-- Filter by Type -->
          <div class="filter-group">
            <label for="filter-type">Jenis:</label>
            <select id="filter-type" class="filter-select">
              <option value="">Semua Jenis</option>
              <option value="MASJID">Masjid</option>
              <option value="MUSHOLLA">Musholla</option>
            </select>
          </div>

          <!-- Filter by Status -->
          <div class="filter-group">
            <label for="filter-status">Status:</label>
            <select id="filter-status" class="filter-select">
              <option value="">Semua Status</option>
              <option value="active">Aktif</option>
              <option value="inactive">Tidak Aktif</option>
            </select>
          </div>

          <!-- Filter by Completion -->
          <div class="filter-group">
            <label for="filter-completion">Kelengkapan Minimal:</label>
            <div class="slider-container">
              <input 
                type="range" 
                id="filter-completion" 
                class="filter-slider"
                min="0" 
                max="100" 
                step="10"
                value="0"
              />
              <span id="completion-value" class="completion-value">0%</span>
            </div>
          </div>

          <!-- Sort Options -->
          <div class="filter-group">
            <label for="filter-sort">Urutkan:</label>
            <select id="filter-sort" class="filter-select">
              <option value="name">Nama (A-Z)</option>
              <option value="completion">Kelengkapan Tertinggi</option>
              <option value="newest">Terbaru</option>
            </select>
          </div>

          <!-- Filter Buttons -->
          <div class="filter-buttons">
            <button type="submit" class="btn-primary">Terapkan Filter</button>
            <button type="reset" class="btn-secondary">Reset Filter</button>
          </div>
        </div>
      </details>

      <!-- Active Filters Display -->
      <div id="active-filters" class="active-filters" style="display: none;">
        <!-- Active filter tags will be displayed here -->
      </div>
    </form>
  </div>

  <!-- Recent Searches -->
  <div class="recent-searches" id="recent-searches" style="display: none;">
    <h3>Pencarian Terakhir</h3>
    <ul id="recent-searches-list" class="recent-searches-list">
      <!-- Recent searches will be populated here -->
    </ul>
  </div>
</div>

<style>
.advanced-search-component {
  width: 100%;
  margin: 30px 0;
}

.search-container {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.search-title {
  margin: 0 0 20px 0;
  font-size: 1.8rem;
  color: #333;
  text-align: center;
}

.search-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.search-input-group {
  position: relative;
}

.search-input-wrapper {
  display: flex;
  gap: 10px;
  background: #f5f5f5;
  padding: 8px;
  border-radius: 8px;
  border: 2px solid transparent;
  transition: all 0.3s;
}

.search-input-wrapper:focus-within {
  border-color: #1a7f8f;
  background: white;
}

.search-input {
  flex: 1;
  border: none;
  background: transparent;
  padding: 12px 15px;
  font-size: 1rem;
  outline: none;
  color: #333;
}

.search-input::placeholder {
  color: #999;
}

.search-btn {
  padding: 10px 20px;
  background: #1a7f8f;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: background 0.3s;
  font-size: 1rem;
}

.search-btn:hover {
  background: #157a8a;
}

.search-btn svg {
  width: 20px;
  height: 20px;
}

.search-suggestions {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #e0e0e0;
  border-top: none;
  border-radius: 0 0 8px 8px;
  list-style: none;
  padding: 10px 0;
  margin: 0;
  max-height: 300px;
  overflow-y: auto;
  z-index: 10;
}

.search-suggestions li {
  padding: 10px 15px;
  cursor: pointer;
  color: #666;
  transition: background 0.2s;
}

.search-suggestions li:hover {
  background: #f5f5f5;
}

.filters-details {
  cursor: pointer;
}

.filters-summary {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px;
  background: #f9f9f9;
  border-radius: 6px;
  user-select: none;
  font-weight: 600;
  color: #333;
  transition: all 0.3s;
}

.filters-summary:hover {
  background: #f0f0f0;
}

.filters-details[open] .filters-summary {
  background: #e8f5f9;
  color: #1a7f8f;
}

.details-icon {
  width: 20px;
  height: 20px;
  transition: transform 0.3s;
}

.filters-details[open] .details-icon {
  transform: rotate(180deg);
}

.filters-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  padding: 20px 0;
  margin-top: 10px;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.filter-group label {
  font-weight: 600;
  color: #333;
  font-size: 0.95rem;
}

.filter-select,
.filter-slider {
  padding: 10px 12px;
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  font-size: 1rem;
  color: #333;
  background: white;
  cursor: pointer;
  transition: all 0.3s;
}

.filter-select:hover,
.filter-select:focus {
  border-color: #1a7f8f;
  outline: none;
}

.slider-container {
  display: flex;
  gap: 12px;
  align-items: center;
}

.filter-slider {
  flex: 1;
  height: 6px;
  -webkit-appearance: none;
  appearance: none;
  background: #e0e0e0;
  border: none;
  padding: 0;
  cursor: pointer;
}

.filter-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #1a7f8f;
  cursor: pointer;
}

.filter-slider::-moz-range-thumb {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #1a7f8f;
  cursor: pointer;
  border: none;
}

.completion-value {
  min-width: 40px;
  text-align: center;
  font-weight: 600;
  color: #1a7f8f;
}

.filter-buttons {
  display: flex;
  gap: 10px;
  grid-column: 1 / -1;
}

.btn-primary,
.btn-secondary {
  flex: 1;
  padding: 12px 20px;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 1rem;
}

.btn-primary {
  background: #1a7f8f;
  color: white;
}

.btn-primary:hover {
  background: #157a8a;
}

.btn-secondary {
  background: #e0e0e0;
  color: #333;
}

.btn-secondary:hover {
  background: #d0d0d0;
}

.active-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.filter-tag {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #e3f2fd;
  color: #1976d2;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 600;
}

.filter-tag .remove-tag {
  cursor: pointer;
  font-size: 1.2rem;
  line-height: 1;
  transition: color 0.3s;
}

.filter-tag .remove-tag:hover {
  color: #d32f2f;
}

.recent-searches {
  margin-top: 30px;
}

.recent-searches h3 {
  margin: 0 0 12px 0;
  font-size: 1rem;
  color: #666;
  font-weight: 600;
}

.recent-searches-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.recent-searches-list li {
  background: #f5f5f5;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s;
}

.recent-searches-list li:hover {
  background: #1a7f8f;
  color: white;
}

@media (max-width: 768px) {
  .search-container {
    padding: 20px;
  }

  .search-title {
    font-size: 1.4rem;
  }

  .search-input {
    font-size: 16px; /* Prevent zoom on mobile */
  }

  .filters-content {
    grid-template-columns: 1fr;
  }

  .filter-buttons {
    flex-direction: column;
  }

  .search-btn {
    padding: 12px 16px;
  }
}
</style>

<script type="module">
import { searchMosques, SEARCH_CONFIG } from '/js/search-utils.js';

const searchForm = document.getElementById('advanced-search-form');
const searchInput = document.getElementById('advanced-search-input');
const typeFilter = document.getElementById('filter-type');
const completionSlider = document.getElementById('filter-completion');
const completionValue = document.getElementById('completion-value');
const sortFilter = document.getElementById('filter-sort');

// Update completion value display
completionSlider.addEventListener('input', (e) => {
  completionValue.textContent = e.target.value + '%';
});

// Handle form submission
searchForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const query = searchInput.value.trim();
  if (!query) {
    alert('Silakan masukkan kata kunci pencarian');
    return;
  }

  // Build search URL
  const params = new URLSearchParams({
    q: query,
    type: typeFilter.value || '',
    min_completion: completionSlider.value,
    sort_by: sortFilter.value
  });

  window.location.href = `/search?${params.toString()}`;
});

// Save recent search
searchInput.addEventListener('blur', () => {
  const query = searchInput.value.trim();
  if (query) {
    const recentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
    if (!recentSearches.includes(query)) {
      recentSearches.unshift(query);
      if (recentSearches.length > 5) {
        recentSearches.pop();
      }
      localStorage.setItem('recentSearches', JSON.stringify(recentSearches));
    }
  }
});

// Load recent searches
function loadRecentSearches() {
  const recentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
  if (recentSearches.length > 0) {
    const recentDiv = document.getElementById('recent-searches');
    const list = document.getElementById('recent-searches-list');
    
    list.innerHTML = recentSearches.map(search => `
      <li onclick="document.getElementById('advanced-search-input').value = '${search}'; document.getElementById('advanced-search-form').submit();">
        ${search}
      </li>
    `).join('');
    
    recentDiv.style.display = 'block';
  }
}

loadRecentSearches();
</script>
