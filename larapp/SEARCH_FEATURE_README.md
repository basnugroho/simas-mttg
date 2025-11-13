# 🕌 Fitur Pencarian Masjid/Musholla - Quick Start Guide

## 📝 Daftar File yang Dibuat/Dimodifikasi

### JavaScript Files
- ✅ `/resources/js/search.js` - Main search functionality
- ✅ `/resources/js/search-utils.js` - Utility functions dan helpers

### Blade Views
- ✅ `/resources/views/search.blade.php` - Search results page
- ✅ `/resources/views/mosques/detail.blade.php` - Mosque detail page
- ✅ `/resources/views/components/advanced-search.blade.php` - Advanced search component
- ✅ `/resources/views/components/home/hero.blade.php` - Updated with search functionality

### Routes
- ✅ `/routes/web.php` - Added `/search` and `/mosques/{id}` routes

### Documentation
- ✅ `MOSQUE_SEARCH_DOCUMENTATION.md` - Full API & Feature documentation
- ✅ `IMPLEMENTATION_STATUS.md` - Implementation checklist
- ✅ `SEARCH_FEATURE_README.md` - This file

---

## 🚀 Cara Menggunakan

### 1. **Search dari Homepage**

Buka `http://localhost:8000` dan Anda akan melihat search box di hero section.

```
User ketik kata kunci → Click "Cari Data" atau tekan Enter → Auto redirect ke /search?q=keyword
```

### 2. **Lihat Hasil Pencarian**

Halaman akan menampilkan:
- Grid kartu masjid/musholla
- Filter options (jenis, sorting)
- Pagination
- Info jumlah hasil

```
URL: http://localhost:8000/search?q=masjid
```

### 3. **Lihat Detail Masjid**

Klik salah satu kartu untuk melihat detail lengkap.

```
URL: http://localhost:8000/mosques/{id}
```

---

## 🔌 Import di Component Lain

### Menggunakan Search Functions

```javascript
import { searchMosques, getMosqueDetail } from '/js/search.js';

// Cari masjid
const result = await searchMosques('Masjid', { 
  per_page: 20,
  sort_by: 'name'
});

// Get detail
const mosque = await getMosqueDetail(1);
```

### Menggunakan Utils

```javascript
import { SEARCH_CONFIG, formatPercentage, validateSearchQuery } from '/js/search-utils.js';

// Validate input
const validation = validateSearchQuery('Masjid');
if (validation.valid) {
  // Proceed with search
}

// Format percentage
const percent = formatPercentage(85.5); // Output: 85.5%
```

### Include Advanced Search Component

```blade
<!-- Di file Blade manapun -->
@include('components.advanced-search')
```

---

## 📊 API Endpoints

### List Mosques
```bash
GET /api/mosques
```

**Parameters:**
- `search` - Cari by nama/alamat
- `page` - Nomor halaman
- `per_page` - Items per page
- `type` - MASJID atau MUSHOLLA
- `sort_by` - name, completion, newest

**Example:**
```bash
http://localhost:8000/api/mosques?search=masjid&per_page=10&sort_by=completion
```

### Get Detail
```bash
GET /api/mosques/{id}
```

**Example:**
```bash
http://localhost:8000/api/mosques/1
```

---

## 🎨 Customization

### Mengubah Warna

Edit `search-utils.js`:
```javascript
export const SEARCH_CONFIG = {
  COLORS: {
    PRIMARY: '#1a7f8f',        // Ubah primary color
    PRIMARY_DARK: '#157a8a',
    SUCCESS: '#4CAF50',
    ERROR: '#f44336',
  }
};
```

### Mengubah Per Page

Edit `search.blade.php`:
```javascript
const result = await searchMosques(currentQuery, {
  page: currentPage,
  per_page: 12,  // Ubah ini
  type: currentFilters.type,
  sort_by: currentFilters.sort_by
});
```

### Menambah Filter Baru

Edit `resources/views/components/advanced-search.blade.php`:
```html
<!-- Tambahkan filter baru di form -->
<div class="filter-group">
  <label for="filter-new">Filter Baru:</label>
  <select id="filter-new">
    <option value="">Pilih...</option>
  </select>
</div>
```

Kemudian update di JavaScript untuk pass ke API.

---

## 🧪 Testing

### Test via API

```bash
# List mosques
curl "http://localhost:8000/api/mosques"

# Search specific
curl "http://localhost:8000/api/mosques?search=masjid&type=MASJID"

# Get detail
curl "http://localhost:8000/api/mosques/1"
```

### Test via Browser

1. Buka `http://localhost:8000`
2. Type di search box
3. Check Network tab di DevTools untuk melihat API calls
4. Check Console tab untuk error messages

---

## 🐛 Troubleshooting

### Search tidak bekerja

1. **Check API Response**
   - Buka DevTools → Network tab
   - Cari request ke `/api/mosques`
   - Lihat response status dan body

2. **Check Console Errors**
   - Buka DevTools → Console
   - Lihat apakah ada JavaScript errors

3. **Check Database**
   - Pastikan data masjid sudah di-seed
   - Jalankan: `php artisan db:seed`

### Styling tidak benar

1. Pastikan Vite dev server running: `npm run dev`
2. Clear browser cache: Ctrl+Shift+Delete
3. Refresh page: Ctrl+F5

### API 404 error

1. Check routes ada di `/routes/api.php`
2. Pastikan controller method exist
3. Check namespace di controller

---

## 📚 Documentation Files

### 1. `MOSQUE_SEARCH_DOCUMENTATION.md`
Dokumentasi lengkap tentang:
- Komponen utama
- API endpoints
- Alur kerja
- Error handling

### 2. `IMPLEMENTATION_STATUS.md`
Checklist implementasi dan next steps

### 3. `SEARCH_FEATURE_README.md`
File ini - Quick start guide

---

## ✨ Features Overview

| Feature | Status | Notes |
|---------|--------|-------|
| Search by nama | ✅ | Via API parameter `search` |
| Search by alamat | ✅ | Included di search parameter |
| Filter by jenis | ✅ | MASJID atau MUSHOLLA |
| Filter by kelengkapan | ✅ | Min completion percentage |
| Sort options | ✅ | name, completion, newest |
| Pagination | ✅ | Customizable per_page |
| Detail view | ✅ | Full mosque information |
| Facilities list | ✅ | With availability status |
| Responsive design | ✅ | Mobile, tablet, desktop |
| Error handling | ✅ | User-friendly messages |

---

## 🎯 Next Steps

1. **Deploy ke Production**
   - Test semua endpoints
   - Setup CORS jika needed
   - Optimize performance

2. **Tambah Features**
   - Google Maps integration
   - User bookmarks/favorites
   - Advanced filtering
   - Reviews & ratings

3. **Analytics**
   - Track popular searches
   - Monitor API performance
   - User engagement metrics

---

## 📞 Support & Contact

Untuk pertanyaan atau issues:

1. Check documentation files
2. Review browser console errors
3. Check Laravel logs: `storage/logs/laravel.log`
4. Review API responses di DevTools

---

**Created:** November 13, 2025  
**Version:** 1.0  
**Status:** Ready for Production
