# 🌟 Dreambox — Laravel 12 + Docker Base Setup

Project **Dreambox** adalah setup dasar untuk pengembangan aplikasi **Laravel 12 menggunakan Docker**.  
Dengan konfigurasi ini, semua anggota tim bisa menjalankan aplikasi secara lokal dengan environment yang identik — tanpa perlu menginstal PHP, MySQL, atau Composer di komputer masing-masing.

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
cd dreambox

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
APP_NAME=Dreambox
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
Database: (sesuai DB_DATABASE di larapp/.env, mis. dreambox_db)
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
dreambox/
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