# Deskripsi Fitur Shift

## 1) Shifts (Semua)
- **Apa itu**: Master template shift global. Menyimpan definisi waktu kerja (pagi/siang/malam/24 jam atau custom) tanpa terikat lokasi tertentu.
- **Isi data**: `name`, `code`, `shift_type` (single/multiple), `time_slots` (jam mulai/selesai), `category` (office/non_office), `break_minutes`, status aktif, deskripsi.
- **Tujuan**: Satu sumber kebenaran untuk pola jam kerja. Dapat dipakai ulang oleh banyak lokasi tanpa duplikasi definisi waktu.
- **Akses UI**: Menu “Shifts (Semua)” → buat/edit/hapus shift template. Tidak mengatur ke lokasi langsung.

## 2) Location Shifts
- **Apa itu**: Pengikatan (pivot) antara shift global dengan lokasi. Di sini shift ditempelkan ke lokasi tertentu.
- **Isi data**: `location_id`, `shift_id`, `time_slots` khusus lokasi (opsional override), `category`, `is_default`, `is_active`.
- **Tujuan**:
  - Mengaktifkan/mematikan sebuah shift per lokasi.
  - Override slot waktu bila lokasi punya jam berbeda dari master.
  - Menandai shift default lokasi (digunakan saat penugasan cepat/rotasi).
- **Akses UI**: Menu “Location Shifts” → pilih shift mana yang tersedia di lokasi tertentu dan atur slot/aktif/default per lokasi.

## Alur Pemakaian
1. Buat atau pilih template di **Shifts (Semua)**.
2. Buka **Location Shifts** untuk melampirkan template ke lokasi (dengan slot khusus jika perlu).
3. Penugasan shift ke karyawan (Shift Assignments) memakai **Location Shift** agar selalu terjaga scope lokasinya dan validasi waktu/overlap berjalan dengan benar.

## Kenapa dipisah?
- Menjaga konsistensi jam kerja antar lokasi sambil tetap memberi fleksibilitas override.
- Mempermudah rotasi otomatis (`shifts:rotate`) dan penjadwalan karena selalu memakai data pivot lokasi-shift.
- Memastikan validasi lokasi: admin lokasi hanya bisa memakai shift yang memang diaktifkan untuk lokasinya.***
