# KantorApp – Multi-Location Attendance & Shift Platform

KantorApp adalah sistem manajemen kehadiran dan shift multi-lokasi berbasis Laravel. Fokusnya adalah kontrol akses per lokasi, validasi GPS, jadwal shift/roster yang adil, serta alur tugas dan persetujuan yang terdokumentasi.

## Fitur Singkat
- Multi-lokasi dengan role `Super Admin`, `Admin Lokasi`, `Karyawan`; pemilihan lokasi untuk Super Admin dan scoping otomatis di seluruh modul.
- Keamanan login: throttling, single-session invalidation, verifikasi email (opsional), dan 2FA (SMS/Email/Authenticator App) dengan backup codes.
- Kehadiran berbasis GPS + shift/roster; menolak check-in di luar jadwal/holiday/weekly off, dan memberi asumsi 8 jam jika belum checkout (hanya untuk tampilan).
- Sistem shift terpisah master vs lokasi: template global, pivot location-shift (override slot, set default), scheduler, kalender roster mingguan, dan proteksi fairness (limit malam beruntun, hindari malam-ke-pagi).
- Tugas & slot wajib: total menit slot = durasi task, total persentase = 100%; progres per slot wajib bukti (foto/dokumen/link) dan approval dengan alasan.
- Target kerja per lokasi/bulan dan rekap jam kerja bulanan (ringkasan target, slot approved, kehadiran, sisa) dengan ekspor PDF bernama sesuai karyawan.
- Lembur, cuti/izin, holiday, weekly off dengan approval dan ekspor; laporan attendance/overtime full-width dengan filter user/tanggal/shift.
- Pesan internal, permintaan pindah lokasi, brand per lokasi (warna/logo/CSS kustom), dan unduhan lampiran aman untuk task/report.

## Hasil Audit Kode (tingkat tinggi)
- **Middleware keamanan**: `SecurityHeaders` (X-Frame/X-Content/Referrer/XSS), `SanitizeInput` (trim/strip tags/collapse spaces, skip list via config), `TwoFactorMiddleware` (blokir akses sebelum 2FA), `RoleMiddleware`, `LocationAware`. Kernel Laravel default tidak ditemukan di repo; pastikan middleware ini ter-register (cek bootstrap/app.php).
- **Login hardening**: Route login custom dengan throttle 5/15 menit + lockout cache, single-session invalidation, guard email verification (env), wajib `location_id` untuk non-Super Admin, 2FA challenge sebelum akses.
- **2FA**: Controller mendukung SMS/Email/App, secret terenkripsi, backup codes dengan konsumsi kode, disable flow, route challenge terpisah.
- **Attendance**: Validasi radius lokasi + approved location change, blok OFF/holiday/leave, prefer shift assignment/roster lalu fallback location shift, grace early 30 menit, blok di luar slot, checkout wajib lokasi valid, cek overtime end time, early checkout perlu approval flag; recap/absence/report scoping per role.
- **Shift/Roster**: Master shift + pivot `LocationShift` (override slot, default flag), weekly roster + export + fairness guard (anti overlap, hindari malam→pagi, deduplikasi slot/index, health-check mentions), scheduler/generator in controller; shift assignment model dipakai untuk attendance/resolusi slot.
- **Tasks/Slots/Progress**: Task wajib slot, self-assign approval rules, slot approval/reject dengan alasan, progres per slot wajib lampiran (foto/dokumen/link) dan validasi rentang progres, unduhan lampiran aman di controller, modal reject transparan di UI.
- **Work target & recap**: Controller menyiapkan target menit per lokasi/karyawan, recap harian bulanan (holiday/WO/leave dihitung) dan export PDF/XLSX; hanya slot approved yang mengurangi target.
- **Lembur/Cuti/Holiday/Weekly Off**: Controller ada untuk CRUD + approval + export; attendance menolak check-in di hari OFF/holiday/leave.
- **Reports & Messages**: Report CRUD + approval + lampiran unduh; pesan internal (list/detail/read/delete by Super Admin).
- **API**: Controllers tersedia (`app/Http/Controllers/Api`) untuk Auth/Task/Report/Attendance/User, tetapi tidak ada `routes/api.php` sehingga belum ter- expose; perlu wiring jika ingin publik/mobile.
- **Konfigurasi keamanan tambahan**: TwoFactorSecret terenkripsi via cast; password hashed; input sanitization global; belum terlihat rate limit khusus per route selain login; audit trail log tidak terpusat (per modul sudah mencatat relasi/approval).

## Modul & Rute (berdasarkan kode)
- **Dashboard & Lokasi**: Dashboard terikat lokasi, pemilihan lokasi untuk Super Admin (`LocationSelectionController`), CRUD lokasi & pengaturan (GPS, branding, jam default).
- **Shift & Roster**: Master shift (template global), location-shift pivot (aktifkan/override slot, set default), weekly roster (kalender, export), scheduler/generator, proteksi fairness (anti overlap, hindari malam→pagi, batas malam beruntun, deduplikasi slot).
- **Attendance**: Check-in/out dengan validasi radius + window shift/roster, absence list, recap + export, approval status, guard OFF/holiday/weekly-off.
- **Tasks & Slot**: CRUD task, self-assign dengan approval, slot wajib (menit=durasi, persentase=100%), approval/reject task & slot dengan alasan, progres per slot wajib lampiran, unduhan lampiran foto/dokumen.
- **Work Target & Recap**: Target menit per lokasi/karyawan, recap jam kerja bulanan (ringkasan + detail) dengan export PDF.
- **Overtime**: Pengajuan lembur, approval Admin Lokasi/Super Admin, laporan & export.
- **Cuti/Holiday**: Weekly off, holiday, dan leave; attendance menolak di hari OFF/holiday; kalender cuti/izin.
- **Reports**: CRUD report dengan approval, lampiran yang dapat diunduh; attendance report full width + filter + export, overtime export.
- **Master/Location Admin Tasks**: Tugas khusus Super Admin (Master Tasks) dan tugas Admin Lokasi.
- **Komunikasi & Utilitas**: Pesan internal (list/detail/read/delete), permintaan pindah lokasi dengan approval, sanitasi input global (autoload middleware), sidebar mobile overlay.
- **API Controllers**: Folder `app/Http/Controllers/Api` (Auth/User/Task/Report/Attendance) tersedia sebagai dasar integrasi, namun rute API belum di-define (tidak ada `routes/api.php`).

## Modul & Alur Utama

### Akun & Keamanan
- Role-based access via Spatie Permission; guard tunggal `web`.
- Login: throttling 5x/15 menit, single-session (session lain dihapus saat login), bisa mewajibkan email verified (env flag).
- 2FA: SMS, Email, atau Authenticator App; secret terenkripsi, backup codes, challenge flow sebelum akses.
- Middleware `TwoFactorMiddleware` menjaga akses sampai verifikasi; sanitasi input global disertakan.

### Lokasi & Branding
- CRUD lokasi, location settings (GPS radius, jam kerja default, warna/tema, CSS kustom).
- Super Admin dapat memilih konteks lokasi; Admin Lokasi terikat ke `location_id`.
- Location Admin Tasks dan Master Tasks untuk pekerjaan level lokasi atau global.

### Shift & Roster
- Master Shifts: template global (kode, kategori office/non-office, single/multi slot, break minutes).
- Location Shifts: pivot untuk mengaktifkan shift per lokasi, override slot waktu, set `is_default` untuk office shift.
- Weekly roster: kalender, ekspor per roster, generator/scheduler untuk rolling shift.
- Fairness dan validasi: anti-overlap, batas shift malam beruntun, hindari transisi malam→pagi, hormati weekly off/cuti/holiday, deduplikasi slot/index.
- Attendance dan assignment mengambil slot dari roster/pivot; admin lokasi hanya bisa pakai shift yang diaktifkan di lokasinya.

### Kehadiran
- Check-in/out divalidasi radius GPS + window shift/roster; menolak jika off/holiday atau di luar jam.
- Absence list, recap, laporan kehadiran dengan filter user/tanggal/shift; ekspor XLSX.
- Durasi harian diasumsikan maks 8 jam jika belum checkout (hanya tampilan, tidak mengurangi target).

### Tugas, Slot, dan Progres
- Semua task wajib punya slot; total menit = durasi task dan total persentase = 100%.
- Self-assign karyawan butuh approval Admin Lokasi (fallback Super Admin); self-assign Admin Lokasi butuh Super Admin.
- Progress per slot bisa setelah task disetujui; wajib lampiran (foto/dokumen/link); approve/reject dengan alasan, penolakan task menolak seluruh slot turunannya.
- Lampiran dapat di-preview/diunduh; halaman approval tabel dengan filter lokasi/karyawan dan modal reject transparan.

### Target Kerja & Rekap
- Target menit per lokasi/bulan (opsional per karyawan); hanya slot approved yang mengurangi target.
- Rekap jam kerja bulanan menampilkan ringkasan target vs slot approved vs kehadiran vs sisa, plus detail slot & kehadiran; ekspor PDF memakai nama karyawan.

### Lembur, Cuti, Holiday
- Overtime request dengan approval (Admin Lokasi atau fallback Super Admin), laporan dan ekspor XLSX.
- Weekly off, holiday, dan leave dicatat; attendance menolak check-in pada hari off/holiday; kalender cuti/izin tersedia.

### Laporan & Ekspor
- Attendance report full width dengan filter user/tanggal/shift + ekspor XLSX.
- Overtime report & export, work target listings (filter per lokasi/karyawan).
- Roster export per entri; work recap PDF; lampiran laporan dapat diunduh.

### Komunikasi & Utility
- Pesan internal (daftar percakapan, detail, tandai sudah dibaca, hapus oleh Super Admin).
- Permintaan pindah lokasi dengan approval Admin Lokasi/Super Admin.
- Sidebar mobile-friendly dengan overlay, tema per lokasi via CSS variable dan tema opsional `public/themes/{theme}.css`.
- Geocoder Leaflet default negara `id`; override dengan `window.APP_GEO_COUNTRY_CODES`.

### Backlog Prioritas (berdasarkan TODO)
- API publik (JWT/Sanctum) dan dokumentasi OpenAPI.
- APM/error tracking (Sentry/New Relic), notifikasi real-time yang lebih kaya.
- Mobile/offline mode, payroll & HR integration, penguatan anti-spoofing GPS lanjutan.

## Teknologi
- Laravel 12, PHP 8.2+
- Blade + AdminLTE (Bootstrap 5)
- Spatie Laravel Permission (role), Laravel Reverb (WebSockets, optional), Maatwebsite Excel (export), Barryvdh DomPDF (PDF)
- Queue: database/Redis; Session/Cache: database (default)

## Prasyarat Server
- PHP 8.2+ dengan ekstensi: pdo_mysql, mbstring, xml, curl, zip, gd, fileinfo
- MySQL 8.0+ / MariaDB 10.5+
- Node.js 18+ untuk build aset
- RAM ≥2GB (4GB disarankan), storage ≥20GB

## Instalasi (Development)
1) Clone repo  
   ```bash
   git clone <repository-url>
   cd kantorapp
   ```
2) Install dependency PHP & JS  
   ```bash
   composer install
   npm install
   ```
3) Salin environment & key  
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4) Konfigurasi database dan seed  
   ```bash
   php artisan migrate --seed
   php artisan db:seed --class=RoleSeeder   # memastikan role Super Admin/Admin Lokasi/Karyawan (guard web)
   ```
5) Build aset  
   ```bash
   npm run build   # atau npm run dev
   ```
6) Jalankan aplikasi  
   ```bash
   php artisan serve
   ```

> Catatan: gunakan versi PHP CLI yang sama dengan web server (minimal 8.2) agar Composer tidak gagal karena platform check.

### Script Pengembang
- `composer run setup` – setup cepat (install, keygen, migrate, npm build).
- `composer run dev` – serve app + queue listener + Vite dev server secara paralel.
- `composer run test` – clear config cache lalu jalanin test.

## Penggunaan Singkat
- **Karyawan**: cek-in/out via halaman attendance, ikuti shift/roster lokasi; ajukan tugas self-assign (menunggu approval), unggah progres dengan lampiran; ajukan lembur/cuti.
- **Admin Lokasi**: kelola task/slot, approve progres/attendance/lembur/cuti, atur roster mingguan lokasi (shift yang diaktifkan di lokasi), kelola target & rekap kerja, kelola settings lokasi.
- **Super Admin**: semua di atas plus membuat master shift, mengatur location-shift, promosi/demosi admin lokasi, branding lokasi, master tasks, dan memilih konteks lokasi untuk bekerja.

## Pengujian
- Jalankan `composer run test` atau `php artisan test`.
- Tersedia feature test 2FA (`tests/Feature/TwoFactorAuthTest.php`); tambahkan test baru untuk modul lain sesuai kebutuhan.

## Deployment
Ikuti panduan lengkap di `DEPLOYMENT_GUIDE.md`. Ringkas: install dependency prod (`composer install --no-dev --optimize-autoloader`, `npm run build`), migrate+seed dengan `--force`, cache config/route/view, dan jalankan worker queue bila dipakai.

## Dukungan & Dokumentasi
- Panduan deployment: `DEPLOYMENT_GUIDE.md`
- Rencana/roadmap keamanan & shift: `TODO_SECURITY_ENHANCEMENT.md`, `TODO_SHIFT`, `TODO_PHASE2_SHIFT_SYSTEM.md`, `deskripsi_fitur_shift.md`, `TODO_ROSTER_FAIRNESS.md`
- Pertanyaan/isu: buka tiket di repositori atau hubungi tim internal.
