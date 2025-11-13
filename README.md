# SIMAS MTTG - Laravel Project

## Deskripsi Sistem

Sistem Informasi Management Masjid (SIMAS) MTTG Jatim–Bali–Nusa Tenggara adalah aplikasi backend untuk mengelola data masjid dan kegiatan terkait di wilayah Jawa Timur, Bali, dan Nusa Tenggara. Fungsionalitas utama meliputi pendaftaran dan pengelolaan masjid, data fasilitas, jadwal sholat/iqomah, artikel/pengumuman, dan pencarian lokasi berdasarkan wilayah. Sistem ini dibangun untuk memudahkan pengelolaan data provinsi/kabupaten/kota serta menyediakan API bagi frontend dan integrasi pihak ketiga.

Tech stack utama: Laravel (PHP), Vite (assets), MySQL, dan Docker untuk pengembangan lokal.

## 🚀 Quick Start (Development)

1. **Install [Docker Desktop](https://www.docker.com/products/docker-desktop) di laptop Anda.**

Prasyarat
- Docker Desktop
- Git
- (Opsional) Node.js + npm — hanya jika ingin menjalankan Vite di mesin lokal

Langkah singkat (Quick start)
1. Clone repository:
  ```bash
  git clone https://github.com/basnugroho/simas-mttg.git
  cd simas-mttg
  ```
2. Salin file env Laravel (sesuaikan bila perlu):
  ```bash
  cp larapp/.env.example larapp/.env
  # Edit larapp/.env jika perlu mengubah kredensial DB
  ```
3. Jalankan Docker (pertama kali dengan build):
  ```bash
  docker compose up -d --build
  ```
4. Setelah container hidup, jalankan migrasi (entrypoint mungkin sudah otomatis menjalankan migrasi):
  ```bash
  docker compose exec app php artisan migrate --force
  docker compose exec app php artisan db:seed --force   # opsional
  ```

Port standar (default)
- Aplikasi (Apache/PHP): http://localhost:3001
- Adminer (antarmuka DB): http://localhost:8081
- Vite (dev server, saat `npm run dev`): http://localhost:5173

Frontend (hot reload)
- Jalankan Vite di host (opsional) atau gunakan service `vite` di docker:
  ```bash
  cd larapp
  npm install
  npm run dev -- --host 0.0.0.0 --port 5173
  ```

Perintah umum
- Reset dan buat ulang container + volume (fresh DB):
  ```bash
  docker compose down -v
  docker compose up -d --build
  ```
- Menjalankan perintah artisan di container:
  ```bash
  docker compose exec app php artisan <perintah>
  ```

Penanganan masalah cepat
- Jika inisialisasi DB gagal: periksa `larapp/.env` dan `docker compose logs db`.
- Jika CSS/JS tidak berubah saat development: pastikan Vite berjalan dan port 5173 bisa diakses.
- Jika instalasi composer bermasalah saat build image, entrypoint akan mencoba `composer install` yang aman saat runtime.

Alur Git singkat
- Kembangkan fitur pada branch turunan dari `development`.
- Buka PR ke `development` untuk review, lalu merge ke `main` untuk rilis.

Keamanan / `.gitignore`
- Jangan commit: `larapp/.env`, `larapp/vendor/`, `larapp/node_modules/`.

Langkah berikutnya (opsional)
- Saya dapat menambah `CONTRIBUTING.md` singkat atau mengubah konfigurasi menjadi menggunakan user DB non-root — beri tahu saya pilihan Anda.

Jika Anda ingin, saya bisa langsung push perubahan README ini ke branch `development`.