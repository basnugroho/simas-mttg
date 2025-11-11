# BE Simas MTTG - Laravel API Project

## 🚀 Quick Start (Development)

1. **Install [Docker Desktop](https://www.docker.com/products/docker-desktop) di laptop Anda.**
2. **Clone repo ini:**
   ```bash
   git clone <repo-url>
   cd be-simas-mttg
   ```
3. **Copy file environment:**
   ```bash
   cp larapp/.env.example larapp/.env
   ```
   Edit `larapp/.env` untuk set DB credentials (default: DB_DATABASE=mttg_db, DB_USERNAME=root, DB_PASSWORD=rootpassword).
4. **Jalankan Docker Compose:**
   ```bash
   docker compose up -d --build
   ```
5. **Masuk ke container untuk instalasi dan setup:**
   ```bash
   docker compose exec app bash
   composer install
   php artisan migrate
   php artisan l5-swagger:generate
   exit
   ```
6. **Untuk hot reload frontend (Vite):**
   Jalankan di host atau container:
   ```bash
   cd larapp
   npm install
   npm run dev -- --host 0.0.0.0 --port 5173
   ```
   Biarkan terminal terbuka untuk auto-refresh CSS/JS.
7. **Akses aplikasi:**
   - Laravel API & Swagger: http://localhost:3001 (redirect ke /api/documentation)
   - Adminer (DB UI): http://localhost:8081
   - Vite dev server (jika hot reload): http://localhost:5173

## ⚡ Production Build
Untuk build production image:
```bash
docker compose -f docker-compose.prod.yml up -d --build
```

Atau build manual:
```bash
docker build -f Dockerfile.prod -t simas-mttg-prod .
```

## 📝 Catatan Penting
- Tidak perlu install PHP, Composer, Node.js di laptop. Semua sudah di container.
- Jika ingin reset database, jalankan:
  ```bash
  docker compose down -v
  docker compose up -d --build
  ```
- Untuk migrasi database:
  ```bash
  docker compose exec app php artisan migrate
  ```
- Untuk regenerate Swagger:
  ```bash
  docker compose exec app php artisan l5-swagger:generate
  ```

## 🛠️ Troubleshooting
- Jika CSS/JS tidak muncul saat development, pastikan:
  - Sudah jalankan `npm run dev -- --host 0.0.0.0 --port 5173` di host atau container
  - Port 5173 sudah accessible
- Jika error database, cek `larapp/.env` dan jalankan ulang migrasi
- Jika Swagger tidak update, regenerate docs

---

Kontribusi? Ajukan Pull Request ke branch development.

## 🛠️ Smooth Dev → Main → Deploy (CI/CD)

### Alur Branch & Pull Request
1. Kerjakan fitur/bugfix di branch turunan dari `development`.
2. Pastikan lulus uji lokal (`composer install`, `npm ci`, `npm run build`, `php artisan test`).
3. Buka Pull Request dari branch tersebut ke `development` untuk review internal.
4. Setelah siap rilis, buka Pull Request dari `development` ke `main`. Merge ke `main` akan memicu pipeline produksi otomatis.

### Pipeline GitHub Actions
Workflow `ci-cd.yml` berjalan otomatis:
- **PR ke `development`/`main`**: menjalankan composer install, build assets, dan PHPUnit dengan SQLite in-memory.
- **Push ke `main`**: membangun image `Dockerfile`, push ke GHCR, lalu (opsional) SSH ke server dan menjalankan:
   - `docker run` image terbaru
   - `php artisan migrate --force`
   - `php artisan config:cache` dan `route:cache`

### Secret yang Dibutuhkan di GitHub
Tambahkan secret berikut pada repository settings:
- `GHCR_USERNAME` & `GHCR_TOKEN`: kredensial GHCR (token minimal _write:packages_).
- `SSH_HOST`, `SSH_USER`, `SSH_KEY` (dan opsional `SSH_PORT`, `SSH_KNOWN_HOSTS`): akses ke server produksi.
- `PROD_ENV_FILE`: path file env di server (default `/opt/be-simas-mttg/.env.prod`).

### Persiapan di Server Produksi
1. Siapkan Docker + Docker Compose.
2. Simpan env Laravel di server, misalnya `/opt/be-simas-mttg/.env.prod` (isi dengan `APP_ENV=production`, `APP_DEBUG=false`, kredensial DB, dsb).
3. Pastikan port publik (misal 3001) sudah dibuka.
4. Workflow akan membuat/replace kontainer bernama `simas-mttg-prod` dan menjalankan migrasi.

### Jika Perlu Deploy Manual
Apabila pipeline dinonaktifkan, jalankan secara manual di server:
```bash
docker pull ghcr.io/<org>/be-simas-mttg:<tag>
docker stop simas-mttg-prod || true
docker rm simas-mttg-prod || true
docker run -d --name simas-mttg-prod -p 3001:80 --env-file /opt/be-simas-mttg/.env.prod ghcr.io/<org>/be-simas-mttg:<tag>
docker exec simas-mttg-prod bash -lc "cd /var/www/html && php artisan migrate --force"
docker exec simas-mttg-prod bash -lc "cd /var/www/html && php artisan config:cache"
```

---

## ⚙️ 1. Prasyarat

Sebelum mulai, pastikan sudah terpasang:
- 🐳 Docker Desktop
- 📦 Git
- 💻 Node.js + npm (untuk build aset/Frontend)

---

## 🚀 2. Setup Project Lokal

### 2.1 Clone Repository
```bash
git clone repo ini
cd be-simas-mttg

2.2 Salin File Environment untuk Docker
bash
Copy code
cp .env.docker .env
File .env di root dibaca Docker Compose, bukan Laravel.

2.3 Jalankan Docker Compose
bash
Copy code
docker compose up --build -d
Container yang akan jalan:

Service	Port	Deskripsi
php-apache	8081	Web server Laravel atau port lain sesukamu di local
db	3306	MySQL database container atau port lain sesukamu di local
adminer	8080	GUI database management atau port lain sesukamu di local

2.4 Install Laravel (Pertama Kali Saja)
bash
Copy code
docker compose exec php-apache bash
cd /var/www/html
composer create-project --prefer-dist laravel/laravel .
exit
Laravel otomatis terpasang ke folder larapp/ (ter-mount ke /var/www/html).

2.5 Konfigurasi Environment Laravel
bash
Copy code
cd larapp
cp .env.example .env
Edit larapp/.env agar cocok dengan database container:

env
Copy code
APP_NAME=nama_app
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8081 (pakek port contoh sesuain kalo kamu ganti)

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=[sesuai isi contoh .env.docker]
DB_USERNAME=[sesuai isi contoh .env.docker]
DB_PASSWORD=[sesuai isi contoh .env.docker]

2.6 Generate APP_KEY & Migrasi Database
bash
Copy code
cd ..
docker compose exec -w /var/www/html php-apache php artisan key:generate
docker compose exec -w /var/www/html php-apache php artisan migrate --seed
2.7 Jalankan Aplikasi
Service	URL
Laravel App	http://localhost:8081
Adminer	http://localhost:8080

Login Adminer (buat obrak abrik database):

makefile
Copy code
Server: db
Username: (sesuai DB_USERNAME di larapp/.env, mis. laravel)
Password: (sesuai DB_PASSWORD di larapp/.env, mis. ChangeMe)
Database: (sesuai DB_DATABASE di larapp/.env, mis. mttg_db)
👥 3. Workflow Pengembangan Tim
3.1 Struktur Branch
Branch	Tujuan
main	Stable/Production release
development	Integrasi fitur sebelum rilis
feature/...	Branch individu untuk tiap fitur

3.2 Alur Kerja Git
3.2.1 Pindah ke development & update:

bash
Copy code
git checkout development
git pull origin development
3.2.2 Buat branch fitur:

bash
Copy code
git checkout -b feature/nama-fitur
3.2.3 Commit & push perubahan:

bash
Copy code
git add .
git commit -m "Deskripsi singkat fitur"
git push -u origin feature/nama-fitur
3.2.4 Pull Request:

Buka PR feature/nama-fitur ➜ development.

Setelah review & test, merge ke development.

3.2.5 Rilis ke production:

Buka PR development ➜ main, lalu merge.

🧩 4. Struktur Project
bash
Copy code
be-simas-mttg/
├── Dockerfile                # Build image PHP-Apache
├── docker-compose.yml        # Definisi container (php, db, adminer)
├── .env.docker               # Template environment Docker
├── .env                      # Environment aktif (JANGAN di-commit)
├── larapp/
│   ├── app/
│   ├── artisan
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── routes/
│   ├── vendor/               # di-ignore Git
│   ├── node_modules/         # di-ignore Git
│   └── .env                  # di-ignore Git
└── README.md
🛡️ 5. Keamanan & .gitignore
Pastikan .gitignore di root berisi:

gitignore
Copy code
# Environment
.env
larapp/.env

# Dependencies
larapp/vendor/
larapp/node_modules/

# System / Editor
.DS_Store
.idea/
.vscode/
⚠️ Jangan pernah commit .env, vendor/, atau node_modules.

🧠 6. Tips Tambahan
Jika port 80/3306 sudah dipakai di host, ubah mapping di docker-compose.yml, misalnya:

yaml
Copy code
services:
  php-apache:
    ports:
      - "8081:80"   # host:container
  db:
    ports:
      - "3307:3306" # host:container
Setiap anggota tim menjalankan Docker di lokal masing-masing (database tidak saling berbagi; tiap orang punya volume sendiri).

❤️ 7. Kontribusi
Buat branch dengan format feature/nama-fitur.

Commit dan push perubahanmu.

Ajukan Pull Request ke branch development.