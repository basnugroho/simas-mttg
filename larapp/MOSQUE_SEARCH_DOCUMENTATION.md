# Dokumentasi Fitur Pencarian Masjid/Musholla

## Gambaran Umum

Fitur pencarian masjid/musholla menghubungkan frontend dengan backend API yang telah dibuat di `MosqueController`. Sistem ini memungkinkan pengguna untuk mencari, memfilter, dan melihat detail masjid/musholla.

## Komponen Utama

### 1. **API Endpoints** (Backend)

#### GET /api/mosques
- **Deskripsi**: Mendapatkan daftar masjid/musholla dengan pencarian dan filter
- **Parameters**:
  - `search` (string): Kata kunci pencarian berdasarkan nama atau alamat
  - `page` (integer): Nomor halaman (default: 1)
  - `per_page` (integer): Jumlah data per halaman (default: 10)
  - `type` (string): Filter berdasarkan jenis (MASJID atau MUSHOLLA)
  - `province_id` (integer): Filter berdasarkan provinsi
  - `city_id` (integer): Filter berdasarkan kota
  - `witel_id` (integer): Filter berdasarkan witel
  - `min_completion` (integer): Filter minimal persentase kelengkapan (0-100)
  - `sort_by` (string): Urutkan berdasarkan (name, completion, newest)

**Response**:
```json
{
  "success": true,
  "message": "Daftar masjid/musholla",
  "data": {
    "items": [
      {
        "id": 1,
        "name": "Masjid Al-Ikhlas",
        "type": "MASJID",
        "address": "Jl. Merdeka No. 123",
        "completion_percentage": 85,
        "is_active": true
      }
    ],
    "meta": {
      "current_page": 1,
      "per_page": 10,
      "total": 50,
      "last_page": 5
    }
  }
}
```

#### GET /api/mosques/{id}
- **Deskripsi**: Mendapatkan detail lengkap sebuah masjid/musholla
- **Response**:
```json
{
  "success": true,
  "message": "Detail masjid/musholla",
  "data": {
    "id": 1,
    "name": "Masjid Al-Ikhlas",
    "code": "MJD001",
    "type": "MASJID",
    "address": "Jl. Merdeka No. 123",
    "latitude": -6.2088,
    "longitude": 106.8456,
    "image_url": "https://...",
    "description": "Deskripsi masjid...",
    "completion_percentage": 85,
    "is_active": true,
    "facilities": [
      {
        "id": 1,
        "facility_name": "Tempat Wudhu",
        "is_available": true,
        "note": null
      }
    ]
  }
}
```

### 2. **Frontend Files**

#### `/resources/js/search.js`
File JavaScript utama yang menangani:
- `searchMosques(query, options)`: Function async untuk melakukan request ke API
- `getMosqueDetail(id)`: Function untuk mendapatkan detail masjid
- `formatMosqueCard(mosque)`: Function untuk format tampilan kartu masjid
- `initializeSearch()`: Function untuk inisialisasi event listeners pada hero search

**Contoh Penggunaan**:
```javascript
import { searchMosques, getMosqueDetail } from '/js/search.js';

// Pencarian
const result = await searchMosques('Masjid Al-Ikhlas', {
  page: 1,
  per_page: 20,
  type: 'MASJID',
  sort_by: 'name'
});

// Detail
const mosque = await getMosqueDetail(1);
```

#### `/resources/views/components/home/hero.blade.php`
- Komponen hero dengan search box
- Menampilkan placeholder input dengan ikon pencarian
- Mengimport file search.js untuk functionality

#### `/resources/views/search.blade.php`
Halaman hasil pencarian dengan fitur:
- Menampilkan hasil pencarian dalam grid layout
- Filter berdasarkan jenis (MASJID/MUSHOLLA)
- Sort berdasarkan nama, kelengkapan, atau terbaru
- Pagination
- Reset filter

#### `/resources/views/mosques/detail.blade.php`
Halaman detail masjid dengan menampilkan:
- Informasi dasar (nama, jenis, alamat, dll)
- Gambar masjid
- Deskripsi
- Fasilitas lengkap dengan status ketersediaan
- Koordinat dan peta lokasi
- Persentase kelengkapan fasilitas

### 3. **Routes**

#### Web Routes (`/routes/web.php`)
```php
Route::view('/search', 'search')->name('search');
Route::get('/mosques/{id}', function ($id) {
    return view('mosques.detail', ['id' => $id]);
})->name('mosque.detail');
```

#### API Routes (`/routes/api.php`)
```php
Route::get('mosques', [MosqueController::class, 'index']);
Route::get('mosques/{id}', [MosqueController::class, 'show']);
Route::get('mosques/{id}/facilities', [MosqueController::class, 'facilities']);
```

## Alur Kerja

### 1. **Pencarian dari Hero Section**
```
User ketik di search box hero
    ↓
Click "Cari Data" atau tekan Enter
    ↓
Function performSearch() di search.js dijalankan
    ↓
Request ke /api/mosques dengan query parameter
    ↓
Simpan hasil ke sessionStorage
    ↓
Redirect ke /search?q=keyword
```

### 2. **Menampilkan Hasil Pencarian**
```
Page load: /search
    ↓
Ambil query dari URL params
    ↓
Call searchMosques() API
    ↓
Display hasil dalam grid
    ↓
User bisa filter dan sort
```

### 3. **Melihat Detail Masjid**
```
User klik kartu masjid
    ↓
Redirect ke /mosques/{id}
    ↓
Call getMosqueDetail() API
    ↓
Display informasi lengkap
    ↓
Load peta jika ada koordinat
```

## Fitur-Fitur

### ✓ Pencarian
- Pencarian by nama dan alamat
- Real-time search dari hero section

### ✓ Filter
- Filter by jenis (MASJID/MUSHOLLA)
- Filter by provinsi, kota, witel
- Filter by kelengkapan fasilitas minimal

### ✓ Sorting
- Sort by nama (A-Z)
- Sort by kelengkapan fasilitas (tertinggi)
- Sort by terbaru

### ✓ Pagination
- Navigasi halaman
- Info jumlah hasil dan halaman saat ini

### ✓ Detail View
- Informasi lengkap masjid
- Daftar fasilitas dengan status
- Koordinat GPS
- Peta interaktif
- Status aktif/tidak aktif

## Styling

### Warna Utama
- Primary: `#1a7f8f` (teal)
- Secondary: `#157a8a` (teal lebih gelap)
- Success: `#4CAF50` (hijau)
- Error: `#f44336` (merah)

### Responsive Design
- Desktop: Grid 3 kolom
- Tablet: Grid 2 kolom
- Mobile: Grid 1 kolom

## Error Handling

Sistem menangani error untuk:
- Network error
- Data tidak ditemukan (404)
- Validasi input
- Timeout

## Performance Optimization

- Lazy loading untuk gambar
- Session storage untuk caching hasil pencarian
- Efficient API calls dengan pagination
- Smooth scrolling saat navigasi

## Browser Compatibility

- Chrome/Edge: ✓
- Firefox: ✓
- Safari: ✓
- Mobile browsers: ✓

## Notes

- API memerlukan CORS configuration yang tepat
- Session storage digunakan untuk cache hasil pencarian
- Koordinat GPS dapat diintegrasikan dengan Google Maps API atau Leaflet.js
- Image URL harus absolute URL atau relative path yang valid
