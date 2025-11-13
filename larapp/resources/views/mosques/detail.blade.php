@extends('layouts.app')

@section('title', 'Detail Masjid/Musholla')

@section('content')
<div class="mosque-detail-page">
  <div class="container">
    <!-- Back Button -->
    <div class="back-button">
      <a href="{{ route('search') }}">← Kembali ke Hasil Pencarian</a>
    </div>

    <!-- Loading State -->
    <div id="loading" class="loading">
      <span class="spinner"></span>
      <p>Memuat data...</p>
    </div>

    <!-- Error State -->
    <div id="error" class="error-message" style="display: none;">
      <h2>Data tidak ditemukan</h2>
      <p id="error-message"></p>
    </div>

    <!-- Content -->
    <div id="mosque-content" style="display: none;">
      <!-- Header Section -->
      <div class="mosque-header">
        <div class="mosque-header-image">
          <img id="mosque-image" src="" alt="Masjid/Musholla" />
          <div class="mosque-badge">
            <span id="mosque-type" class="badge-type"></span>
            <span id="mosque-status" class="badge-status"></span>
          </div>
        </div>

        <div class="mosque-header-info">
          <h1 id="mosque-name"></h1>
          <div class="mosque-meta">
            <div class="meta-item">
              <span class="label">Jenis:</span>
              <span id="mosque-type-text" class="value"></span>
            </div>
            <div class="meta-item">
              <span class="label">Alamat:</span>
              <span id="mosque-address" class="value"></span>
            </div>
            <div class="meta-item">
              <span class="label">Kelengkapan Fasilitas:</span>
              <div class="progress-bar">
                <div id="completion-progress" class="progress-fill"></div>
              </div>
              <span id="mosque-completion" class="value"></span>
            </div>
          </div>

          <div class="mosque-coordinates" id="coordinates-section" style="display: none;">
            <h3>Lokasi</h3>
            <p>
              <strong>Latitude:</strong> <span id="mosque-latitude"></span><br>
              <strong>Longitude:</strong> <span id="mosque-longitude"></span>
            </p>
          </div>
        </div>
      </div>

      <!-- Description Section -->
      <div class="mosque-description" id="description-section" style="display: none;">
        <h2>Deskripsi</h2>
        <p id="mosque-description"></p>
      </div>

      <!-- Facilities Section -->
      <div class="mosque-facilities">
        <h2>Fasilitas</h2>
        <div id="facilities-list" class="facilities-grid">
          <!-- Facilities will be populated here -->
        </div>
      </div>

      <!-- Map Section -->
      <div class="mosque-map" id="map-section" style="display: none;">
        <h2>Peta Lokasi</h2>
        <div id="map" style="height: 400px; border-radius: 8px; overflow: hidden;"></div>
      </div>
    </div>
  </div>
</div>

<style>
.mosque-detail-page {
  padding: 40px 0;
  min-height: 100vh;
  background-color: #f5f5f5;
}

.back-button {
  margin-bottom: 30px;
}

.back-button a {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #1a7f8f;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s;
}

.back-button a:hover {
  color: #157a8a;
}

.loading {
  text-align: center;
  padding: 60px 20px;
}

.spinner {
  display: inline-block;
  width: 50px;
  height: 50px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #1a7f8f;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-message {
  background: white;
  padding: 40px;
  border-radius: 8px;
  text-align: center;
  color: #d32f2f;
}

.error-message h2 {
  margin-bottom: 10px;
}

.mosque-header {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  margin-bottom: 30px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
  padding: 30px;
}

.mosque-header-image {
  position: relative;
  overflow: hidden;
  border-radius: 8px;
}

.mosque-header-image img {
  width: 100%;
  height: 400px;
  object-fit: cover;
}

.mosque-badge {
  position: absolute;
  top: 15px;
  right: 15px;
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.badge-type,
.badge-status {
  padding: 8px 12px;
  border-radius: 4px;
  font-size: 0.85rem;
  font-weight: 600;
  color: white;
}

.badge-type {
  background-color: rgba(26, 127, 143, 0.9);
}

.badge-type.musholla {
  background-color: rgba(255, 193, 7, 0.9);
  color: #333;
}

.badge-status {
  background-color: rgba(76, 175, 80, 0.9);
}

.badge-status.inactive {
  background-color: rgba(244, 67, 54, 0.9);
}

.mosque-header-info h1 {
  font-size: 2.5rem;
  color: #333;
  margin: 0 0 20px 0;
}

.mosque-meta {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.meta-item {
  display: flex;
  gap: 15px;
  flex-direction: column;
}

.meta-item .label {
  font-weight: 600;
  color: #666;
  font-size: 0.95rem;
}

.meta-item .value {
  color: #333;
  font-size: 1rem;
  line-height: 1.5;
}

.progress-bar {
  width: 100%;
  height: 8px;
  background-color: #e0e0e0;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #4CAF50, #45a049);
  border-radius: 4px;
}

.mosque-coordinates {
  background: #f9f9f9;
  padding: 15px;
  border-radius: 8px;
  margin-top: 15px;
}

.mosque-coordinates h3 {
  margin: 0 0 10px 0;
  font-size: 1rem;
  color: #333;
}

.mosque-coordinates p {
  margin: 0;
  color: #666;
  font-size: 0.95rem;
  line-height: 1.6;
}

.mosque-description {
  background: white;
  padding: 30px;
  border-radius: 8px;
  margin-bottom: 30px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.mosque-description h2 {
  color: #333;
  margin-bottom: 15px;
  font-size: 1.5rem;
}

.mosque-description p {
  color: #666;
  line-height: 1.6;
  white-space: pre-wrap;
  word-break: break-word;
}

.mosque-facilities {
  background: white;
  padding: 30px;
  border-radius: 8px;
  margin-bottom: 30px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.mosque-facilities h2 {
  color: #333;
  margin-bottom: 20px;
  font-size: 1.5rem;
}

.facilities-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 15px;
}

.facility-item {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 15px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #f9f9f9;
  transition: all 0.3s;
}

.facility-item:hover {
  border-color: #1a7f8f;
  background: #f0f8f9;
}

.facility-info {
  flex: 1;
}

.facility-name {
  font-weight: 600;
  color: #333;
  margin-bottom: 5px;
}

.facility-note {
  font-size: 0.85rem;
  color: #999;
}

.facility-status {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  color: white;
  text-align: center;
  flex-shrink: 0;
}

.facility-status.available {
  background-color: #4CAF50;
}

.facility-status.unavailable {
  background-color: #f44336;
}

.facility-status span {
  font-size: 0.75rem;
}

.mosque-map {
  background: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.mosque-map h2 {
  color: #333;
  margin-bottom: 20px;
  font-size: 1.5rem;
}

@media (max-width: 768px) {
  .mosque-header {
    grid-template-columns: 1fr;
    padding: 20px;
    gap: 20px;
  }

  .mosque-header-image img {
    height: 300px;
  }

  .mosque-header-info h1 {
    font-size: 1.8rem;
  }

  .facilities-grid {
    grid-template-columns: 1fr;
  }

  .mosque-facilities,
  .mosque-description,
  .mosque-map {
    padding: 20px;
  }
}

.no-facilities {
  text-align: center;
  padding: 30px;
  color: #999;
}
</style>

<script type="module">
import { getMosqueDetail } from '/js/search.js';

const mosqueId = new URLSearchParams(window.location.search).get('id') || 
                  window.location.pathname.split('/').pop();
const loadingDiv = document.getElementById('loading');
const errorDiv = document.getElementById('error');
const errorMessage = document.getElementById('error-message');
const contentDiv = document.getElementById('mosque-content');

async function loadMosqueDetail() {
  try {
    const mosque = await getMosqueDetail(mosqueId);
    displayMosqueDetail(mosque);
  } catch (error) {
    loadingDiv.style.display = 'none';
    errorDiv.style.display = 'block';
    errorMessage.textContent = error.message || 'Gagal memuat data masjid';
  }
}

function displayMosqueDetail(mosque) {
  // Basic info
  document.getElementById('mosque-name').textContent = mosque.name;
  document.getElementById('mosque-type-text').textContent = mosque.type;
  document.getElementById('mosque-address').textContent = mosque.address || 'Alamat tidak tersedia';
  document.getElementById('mosque-completion').textContent = mosque.completion_percentage + '%';
  
  // Progress bar
  document.getElementById('completion-progress').style.width = mosque.completion_percentage + '%';

  // Badge
  const typeElement = document.querySelector('.badge-type');
  const statusElement = document.querySelector('.badge-status');
  typeElement.textContent = mosque.type;
  if (mosque.type === 'MUSHOLLA') {
    typeElement.classList.add('musholla');
  }
  statusElement.textContent = mosque.is_active ? 'Aktif' : 'Tidak Aktif';
  if (!mosque.is_active) {
    statusElement.classList.add('inactive');
  }

  // Image
  const imageElement = document.getElementById('mosque-image');
  if (mosque.image_url) {
    imageElement.src = mosque.image_url;
  } else {
    imageElement.src = '/images/mosque.png'; // fallback image
  }

  // Description
  const descriptionSection = document.getElementById('description-section');
  if (mosque.description) {
    document.getElementById('mosque-description').textContent = mosque.description;
    descriptionSection.style.display = 'block';
  }

  // Coordinates
  const coordinatesSection = document.getElementById('coordinates-section');
  if (mosque.latitude && mosque.longitude) {
    document.getElementById('mosque-latitude').textContent = mosque.latitude;
    document.getElementById('mosque-longitude').textContent = mosque.longitude;
    coordinatesSection.style.display = 'block';
    loadMap(mosque.latitude, mosque.longitude, mosque.name);
  }

  // Facilities
  displayFacilities(mosque.facilities);

  loadingDiv.style.display = 'none';
  contentDiv.style.display = 'block';
}

function displayFacilities(facilities) {
  const facilitiesList = document.getElementById('facilities-list');
  
  if (!facilities || facilities.length === 0) {
    facilitiesList.innerHTML = '<div class="no-facilities">Fasilitas tidak tersedia</div>';
    return;
  }

  facilitiesList.innerHTML = facilities.map(facility => `
    <div class="facility-item">
      <div class="facility-info">
        <div class="facility-name">${facility.facility_name}</div>
        ${facility.note ? `<div class="facility-note">${facility.note}</div>` : ''}
      </div>
      <div class="facility-status ${facility.is_available ? 'available' : 'unavailable'}">
        <span>${facility.is_available ? '✓' : '✗'}</span>
      </div>
    </div>
  `).join('');
}

function loadMap(lat, lon, name) {
  const mapSection = document.getElementById('map-section');
  mapSection.style.display = 'block';

  // Check if Leaflet is available, if not we'll use a simple iframe
  if (typeof L !== 'undefined') {
    setTimeout(() => {
      const map = L.map('map').setView([lat, lon], 15);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
      }).addTo(map);

      L.marker([lat, lon]).addTo(map)
        .bindPopup(name)
        .openPopup();
    }, 100);
  } else {
    // Fallback to Google Maps iframe
    const mapElement = document.getElementById('map');
    mapElement.innerHTML = `
      <iframe 
        width="100%" 
        height="100%" 
        frameborder="0" 
        src="https://www.google.com/maps/embed/v1/place?key=YOUR_GOOGLE_MAPS_API_KEY&q=${lat},${lon}"
        style="border-radius: 8px;">
      </iframe>
    `;
  }
}

// Load mosque detail when page loads
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', loadMosqueDetail);
} else {
  loadMosqueDetail();
}
</script>
@endsection
