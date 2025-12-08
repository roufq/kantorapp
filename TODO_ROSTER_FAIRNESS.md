# TODO: Perbaikan Scheduler Roster (fair dan merata)

- [x] Tambah mekanisme rotasi round-robin per hari dan per slot (pointer tersimpan) agar karyawan kelebihan hari ini diprioritaskan slot pertama besok.
- [x] Sisipkan libur mingguan: tandai weekly off/cuti/izin lebih dulu; pointer rotasi melewati karyawan off tanpa menghukum urutan.
- [x] Terapkan batasan fairness: maksimal shift malam beruntun, hindari transisi malam → pagi; sebar rata shift malam.
- [x] Hitung dan simpan meta distribusi (jumlah shift per karyawan per minggu) untuk scoring prioritas dan laporan admin.
- [x] Tambah prioritas “carry-over” untuk karyawan yang belum dapat shift pada hari sebelumnya.
- [x] Buat laporan transparansi: siapa yang belum dapat jatah minggu ini, serta log alasan penempatan per slot.
- [x] Uji dengan skenario: (a) karyawan > slot, (b) slot > karyawan, (c) ada weekly off/cuti, (d) cek konsistensi pointer antar hari).
- [x] Deduplikasi entri roster per user+date (hindari multiple slot_index/shift_assignment_id ganda untuk hari yang sama).
- [x] Pastikan status libur/cuti/off selalu override fallback shift lokasi (tanpa memunculkan jam default).
- [x] Audit urutan `normalizedSlots()` (LocationShift/Shift) agar index slot konsisten; perbaiki data bila urutan pernah berubah.
- [x] Proteksi transisi berat malam → pagi (hindari penjadwalan user yang baru selesai 22:00–06:00 langsung masuk 06:00–14:00).
- [x] Tangani duplikasi `shift_assignment_id` pada roster hasil clone/rolling agar tidak ada entri berlebih yang menimpa slot.
- [x] Pastikan semua jam/shift di dashboard dan laporan diambil dinamis dari data roster/assignment di database (tanpa hardcode jam atau fallback yang tidak sesuai).
- [x] Validasi perhitungan telat/absen untuk shift overnight (lintas hari) menggunakan interval slot yang menambah hari saat end < start.
- [x] Guard generator roster agar tidak membuat entri tanpa slot valid atau slot_index di luar jangkauan slot yang tersedia.
- [x] Tambah health check/audit rutin: deteksi roster kosong/duplikat/slot out-of-range, serta entri tanpa assignment atau assignment tanpa slot.
