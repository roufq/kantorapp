# Tugas: Alih Fungsi “Shifts (Semua)” untuk Jadwal Non-Office & Libur Bergilir

Tujuan: menu Shifts (Semua) menjadi pusat penjadwalan cepat untuk karyawan non-office (multi-slot per hari) dan set libur mingguan bergilir per lokasi, tanpa mengganggu alur Location Shifts yang sudah simpel.

## Scope Utama
- Membuat jadwal masuk karyawan non-office per lokasi berbasis template multi-slot (pagi/siang/malam/custom).
- Menyusun libur mingguan bergilir (weekly off) otomatis per tim/lokasi.
- Mempercepat input jadwal (assignments) dari Shifts (Semua) → push ke Location Shifts/Shift Assignments.

## Pekerjaan Teknis
1) UI/UX “Shifts (Semua)” sebagai Scheduler Non-Office
   - Form pilih lokasi, pilih template shift (multi-slot) yang sudah ada di Location Shifts.
   - Tambah panel “Jadwal Karyawan” untuk memilih karyawan (filter lokasi) + tanggal rentang + slot yang dipakai per hari.
   - Tambah tombol “Generate Jadwal” yang menulis ke `shift_assignments` (terikat `location_shift_id`) dengan validasi overlap.

2) Preset & Clone
   - Sediakan preset jam (Pagi/Siang/Malam/24h atau custom) di Shifts (Semua) khusus non-office.
   - Tombol “Salin jadwal minggu ini ke minggu depan” per lokasi/tim.

3) Libur Bergilir
   - Input pola rotasi libur (misal: setiap 6 hari kerja, 1 hari off; atau jadwal off by team).
   - Generator: sisipkan assignment “OFF”/status libur untuk karyawan sesuai pola, hindari overlap dengan shift aktif.
   - Simpan konfigurasi libur per lokasi (tabel/pengaturan) agar repeatable.

4) Validasi & Integrasi
   - Pastikan setiap assignment hasil generator terikat `location_shift_id` (pivot) dan lokasi benar.
   - Cegah overlap (multi-slot/hari) dan cek kompatibilitas dengan aturan existing (attendance, notifikasi).
   - Hook ke notifikasi (opsional): kirim jadwal mingguan + reminder.

5) Aksi Tambahan
   - Sembunyikan/limit menu Shifts (Semua) untuk Admin Lokasi jika tidak perlu; Super Admin sebagai operator generator.
   - Tambahkan export jadwal non-office per lokasi (CSV/XLSX) setelah generate.

## Deliverables
- UI generator jadwal di Shifts (Semua) dengan flow pilih lokasi → pilih karyawan → pilih/preset slot → generate.
- Logika libur bergilir tersimpan per lokasi + output assignment OFF.
- Endpoint/command untuk clone/rollover jadwal mingguan.
- Dokumentasi singkat cara pakai di README atau di halaman UI (tooltip/help).
