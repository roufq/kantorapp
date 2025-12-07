# PHASE 2 TODO: FLEXIBLE SHIFT SYSTEM (MINGGU 4â€“6)

## Target Utama (MVP)
- Menyediakan penjadwalan shift per user per tanggal (location-scoped).
- Validasi overlap shift per user/hari + hanya shift yang terhubung ke lokasi.
- Integrasi attendance agar memprioritaskan assignment aktif.
- Laporan dasar assignment/kehadiran per periode.

---

## Minggu 4 — Shift Management Core\n
### Database
- [x] Buat migration `shift_assignments`:
  - `id`, `user_id` (FK), `shift_id` (FK), `date` (date), `status` (enum: scheduled, cancelled, completed), `notes` (nullable), timestamps
  - Index: (`user_id`, `date`), (`shift_id`, `date`)
- [x] Tambah unique rule: kombinasi yang mencegah overlap (cek di app-level jika shift multi-slot)

### Model & Relasi
- [x] Model `ShiftAssignment`
  - Relasi: `user()`, `shift()`
  - Scope: `forLocation($locationId)`
- [x] Tambahkan relasi pada `User` (`shiftAssignments`) dan `Shift` (`assignments`)

### Gates/Policies
- [x] Gate/policy: Admin Lokasi boleh kelola assignment di lokasinya; Super Admin semua
- [x] Validasi server-side: user dan shift harus dalam lokasi yang sama (via `location_shifts`)

### Controller & Routes
- [x] `ShiftAssignmentController` (resource) dengan scoping lokasi
  - index, create, store, edit, update, destroy
- [x] Routes resource + middleware: `role:Super Admin,Admin Lokasi`

### Views (MVP)
- [x] Index: filter by tanggal rentang, user, status
- [x] Create/Edit: pilih user (lokasi sama), pilih shift (terhubung ke lokasi), pilih tanggal; notes opsional
- [x] Quick actions: tombol template (Pagi/Siang/Malam/24H) untuk prefill waktu shift terkait

### Validasi
- [x] Tidak boleh overlap assignment untuk user di hari yang sama (cek terhadap time_slots shift)
- [x] Hanya shift yang terhubung ke lokasi (cek `location_shifts`)
- [x] Tanggal valid (>= today) dengan opsi override untuk admin

### Seeder (opsional)
- [x] Seeder contoh assignment untuk 1â€“2 user per lokasi selama 1 minggu

---

## Minggu 5 - - [x] Rotasi shift sederhana (pola mingguan per tim)
- [x] Handover (catatan serah terima antar shift)
- [x] Break management (durasi istirahat per shift)
- [x] Deteksi konflik lintas hari (overnight) dan multi-slot
- [x] Peningkatan UI (kalender mingguan/per user)

---

## Minggu 6 — Shift-Based Attendance\n
### Integrasi Attendance
- [x] Attendance: jika ada assignment aktif pada tanggal/jam, validasi terhadap assignment
- [x] Fallback: jika tidak ada assignment, gunakan window shift lokasi (behavior saat ini)

### Laporan
- [x] Report assignment per user/periode (XLSX export)
- [x] Report kehadiran berbasis shift

### Notifikasi (opsional)
- [x] Reminder sebelum shift mulai (email/WA push, per lokasi)
- [x] Alert ketidakhadiran terhadap assignment

---

## Testing
- [ ] Unit: model `ShiftAssignment`, validator overlap, scope `forLocation`
- [ ] Unit: gates/policies (manage di lokasi sendiri)
- [x] Integrasi: CRUD assignment (role Admin Lokasi/Super Admin), scoping lokasi
- [x] Integrasi: Attendance terhubung ke assignment (kasus ada/tidak ada assignment)
- [x] Data isolation: Admin Lokasi tidak bisa akses lokasi lain

---

## Deliverables
- [x] Migration + Model shift_assignments
- [x] Controller + Routes + Views dasar
- [x] Validasi anti-overlap dan scoping lokasi
- [x] Integrasi attendance (prioritas assignment)
- [x] Laporan dasar assignment/kehadiran (export XLSX)
- [ ] Test unit & integrasi minimal

## Non-Goals (fase ini)
- Rotasi kompleks, payroll integrasi penuh, handover detail, break rules kompleks, notifikasi canggih â†’ dipindahkan ke fase lanjutan setelah MVP stabil.


