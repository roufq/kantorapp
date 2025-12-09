# Task Slot & KPI Design

## Scope (v1)
- Task durasi disimpan dalam menit (`duration_minutes`).
- Task dipecah ke slot progres; total persentase slot = 100%, total menit slot <= `duration_minutes`.
- Approval per slot oleh Admin Lokasi / Super Admin; progres task naik sesuai slot yang disetujui.
- Lampiran slot (wajib minimal 1): foto, dokumen, atau link bukti.
- Riwayat perubahan slot (audit trail) wajib tercatat.
- Rekap jam kerja bulanan per karyawan per lokasi; sumber jam: slot approved + attendance (keduanya).
- Akses: Super Admin lihat semua; Admin Lokasi hanya lokasi sendiri.

## Data Model (usulan tabel/kolom baru)
- `tasks` (tambahan):
  - `duration_minutes` int (total menit target).
- `task_slots`:
  - `id`, `task_id`, `name`, `percentage` (int, 0-100), `minutes` (int, >=1), `order` (int), `created_by`.
  - `status` enum(`pending`,`approved`,`rejected`), `approved_by`, `approved_at`, `rejection_reason`.
- `task_slot_attachments`:
  - `id`, `task_slot_id`, `type` enum(`photo`,`document`,`link`), `path_or_url`, `uploaded_by`.
  - Validasi: minimal satu attachment per slot submission.
- `task_slot_history` (audit):
  - `id`, `task_slot_id`, `action` enum(`created`,`updated`,`deleted`,`approved`,`rejected`), `data_before`, `data_after`, `actor_id`, `created_at`.
- `employee_work_recaps` (bulanan):
  - `id`, `employee_id`, `location_id`, `year`, `month`,
  - `slot_minutes_approved` (sum minutes slot approved),
  - `attendance_minutes` (sum jam kerja attendance, jika ingin digabung),
  - `total_minutes` (computed atau disimpan),
  - `meta` (json untuk detail per task/slot).

## Aturan & Validasi
- Total `percentage` slot per task harus 100%.
- Total `minutes` slot tidak boleh melebihi `tasks.duration_minutes`; boleh kurang (buffer).
- Slot hanya dapat di-approve jika ada minimal 1 attachment (foto/dokumen/link).
- Persentase progres task = sum persen slot berstatus approved; task selesai jika 100% slot approved.
- Admin Lokasi hanya boleh approve slot untuk task yang assignee-nya di lokasi mereka; Super Admin bebas.
- Audit setiap perubahan slot (buat/ubah/hapus) + approval/reject tercatat di `task_slot_history`.
- Reject wajib alasan (`rejection_reason`), tampil di riwayat.

## Alur Proses
1) Pembuat task set `duration_minutes`.
2) Tambah beberapa slot: isi nama, persen, menit; cek akumulasi persen=100, menit<=duration.
3) Assignee upload progres per slot dengan lampiran (minimal satu). Status tetap `pending`.
4) Admin Lokasi/Super Admin review:
   - Approve: slot status `approved`, catat `approved_by/at`, progres task bertambah persen slot.
   - Reject: wajib isi alasan, status `rejected`.
5) Task dianggap selesai jika seluruh slot approved (100%).
6) Rekap bulanan: jalankan job harian/bulanan yang menulis `employee_work_recaps` dari slot approved + attendance.

## UI/UX (ringkas)
- Form Task: field durasi (menit); bagian slot (list) dengan add/remove row (nama, persen, menit).
- Halaman Task: daftar slot dengan status, persen, menit; tombol upload bukti & submit per slot.
- Riwayat slot: tampil badge approved/rejected/pending + alasan reject + link lampiran + audit minimal ringkas.
- Halaman Approval: tampil slot pending sesuai hak akses; tombol Approve/Reject (reject wajib alasan).
- Rekap: filter lokasi/bulan; tabel per karyawan (slot_minutes_approved, attendance_minutes, total_minutes) sesuai role.

## Catatan Implementasi
- Perlu migrasi baru untuk tabel-tabel di atas dan penambahan kolom `duration_minutes`.
- Endpoint/route tambahan untuk CRUD slot, submit attachment, approve/reject slot.
- Seeder/rollback: pastikan tidak memutus data task lama (slot bisa diisi default 1 slot 100% jika task lama).
- Penghitungan progres task pindah ke basis slot (sum persen approved), bukan input manual.

## Urutan Prioritas Pengerjaan
1) Skema & migrasi data ✅ (kolom duration_minutes + tabel slot/attachments/history/recap)
   - Tambah kolom `duration_minutes` (tanpa menghapus `due_date`).
   - Buat tabel `task_slots`, `task_slot_attachments`, `task_slot_history`, `employee_work_recaps`.
   - Validasi: total persen slot = 100%; total menit slot <= `duration_minutes`.
2) Logika backend slot & approval ✅
   - CRUD slot (add/remove/update) dengan validasi persen/menit.
   - Submit bukti per slot (foto/dokumen/link; minimal satu).
   - Approve/Reject per slot (Admin Lokasi/Super Admin), reject wajib alasan, catat audit.
   - Progres task = sum persen slot approved; task selesai jika 100% slot approved.
3) UI/UX task & approval ✅ (form task sudah ada durasi+slot add/remove)
   - Form Task: input durasi (menit), due date tetap; section slot (add/remove row).
   - Detail Task: daftar slot, status, persen, menit; unggah bukti per slot.
   - Approval page/riwayat: tombol Approve/Reject dengan alasan wajib.
4) Rekap jam kerja bulanan ✅
   - Job harian/bulanan isi `employee_work_recaps` (slot approved + attendance jika dipakai).
   - Laporan/filter: lokasi, bulan, karyawan; akses sesuai role.
5) Pengujian & fallback ✅
   - Uji validasi persen/menit, upload bukti minimal, akses approval, progres 100%.
   - Migrasi data lama: slot default 100% untuk task lama (command `php artisan tasks:backfill-slots`, opsi `--dry-run` tersedia).

## Tabel Rekap Jam Kerja Bulanan (per lokasi & karyawan)
- Nama: `employee_work_recaps`.
- Kolom:
  - `id`
  - `employee_id`
  - `location_id`
  - `year` (int)
  - `month` (int)
  - `slot_minutes_approved` (total menit dari task slot yang approved bulan itu, lokasi karyawan)
  - `attendance_minutes` (opsional: menit dari attendance jika ingin digabung)
  - `total_minutes` (cached: slot + attendance)
  - `meta` JSON (opsional: detail per task/slot)
  - timestamps
- Aturan:
  - Diisi via job (harian/bulanan) menarik data slot approved dan/atau attendance sesuai lokasi & karyawan.
  - Super Admin bisa lihat semua; Admin Lokasi hanya lokasi sendiri.
  - Filter minimal: lokasi, bulan, karyawan; tampilkan jam/menit total.
