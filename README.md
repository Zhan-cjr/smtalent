# SM Talent - Sistem Asesmen Psikotes & Rekrutmen Karyawan

Aplikasi berbasis web modern untuk pengelolaan seleksi karyawan, ujian psikotes online publik, penjadwalan tes & wawancara (8 aspek), perankingan otomatis (60% Psikotes : 40% Interview), serta manajemen akun tim HRD.

> **Created with excellence by ZhanSoft**  
> Instagram: [@amn4ll](https://www.instagram.com/amn4ll?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw%3D%3D)

---

## 🚀 Fitur Utama

### 1. Kandidat / Peserta Tes (Akses Publik Tanpa Login)
* **Akses Publik**: Pelamar tidak perlu membuat akun/password, cukup memilih lowongan dan mengisi identitas diri.
* **Pembatasan Jadwal Tes**: Tombol ujian hanya dapat diakses sesuai jadwal tes yang telah ditentukan oleh HRD.
* **Layar Ujian Psikotes Responsif**:
  * Timer pengerjaan ujian otomatis dengan autosave.
  * Tampilan ramah perangkat mobile (HP) dengan laci daftar nomor soal (*drawer palette*).
  * Skala Likert 5 poin yang nyaman disentuh di smartphone.
* **Cek Status Kelulusan**: Halaman mandiri bagi kandidat untuk memantau status seleksi menggunakan nomor WhatsApp / Email / Token.

### 2. Tim HRD & Interviewer (Panel Admin)
* **Dashboard & Statistik Seleksi**: Ringkasan jumlah pelamar, status seleksi, dan grafik rekrutmen.
* **Manajemen Jadwal Psikotes**: Buka / tutup periode sesi ujian psikotes per paket posisi.
* **Manajemen Tim HRD (User Management)**: Kelola akun staf HRD dan interviewer dengan hak akses login aman.
* **Sistem Wawancara Multi-HRD (Anti-Bentrok)**:
  * Pemilihan kandidat langsung dengan pencarian real-time.
  * Urutan prioritas kandidat yang pertama kali menyelesaikan psikotes berada di paling atas.
  * Penguncian sesi wawancara (ketika HRD 1 memilih kandidat A, HRD lain tidak dapat mengambil kandidat yang sama).
  * Penilaian 8 aspek kompetensi kualitatif & kuantitatif skala 0–100.
* **Perankingan & Kalkulasi Nilai Akhir**: Bobot otomatis 60% Skor Psikotes + 40% Skor Interview.
* **Export Laporan**: Rekap hasil seleksi ke Excel dan cetak PDF profil kandidat.
* **Audit Trail**: Rekam jejak seluruh aktivitas penting di dalam sistem.

---

## 🛠️ Tech Stack
* **Backend**: Laravel 12 (PHP 8.3)
* **Frontend**: Blade Templating, Tailwind CSS, Alpine.js, Vite
* **Database**: MySQL / SQLite
* **Testing**: PHPUnit / Pest Feature & Unit Tests

---

## 💻 Panduan Instalasi Lokal

1. **Clone Repository**:
   ```bash
   git clone <repository-url>
   cd PSIKOTES
   ```

2. **Install Dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Sesuaikan konfigurasi database pada `.env`.*

4. **Migrasi & Seeding Database**:
   ```bash
   php artisan migrate --seed
   ```

5. **Build Aset Frontend**:
   ```bash
   npm run build
   # atau untuk mode dev: npm run dev
   ```

6. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Akses aplikasi di browser: `http://localhost:8000`

---

## 🔐 Akun Default HRD
* **Email**: `hrd@example.com`
* **Password**: `Password123!`

---

## 📄 Lisensi
Hak Cipta &copy; 2026 **SM Talent** by **ZhanSoft**.
