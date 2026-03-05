# TODO

## P0 - Wajib dasar operasional
- [x] Absensi: geofencing + anti fake GPS (akurasi minimum) + foto check-in/out + device ID
- [x] Cuti/izin: saldo cuti per tahun + perhitungan otomatis sisa cuti
- [x] Overtime: approval multi-level + batas jam lembur harian/mingguan
- [x] Shift/roster: validasi konflik jadwal + batas jam kerja
- [x] Laporan inti: rekap absensi harian/bulanan + export Excel/CSV
- [x] Audit trail: log perubahan data kritis (shift, approval, mutasi)

## P1 - Penting untuk efisiensi
- [x] Notifikasi otomatis: approval pending, jadwal masuk, cuti mendekati habis
- [x] KPI dasar: keterlambatan, kehadiran, overtime, produktivitas tugas
- [x] Workflow approval dinamis per departemen/nilai
- [x] Integrasi data karyawan: histori jabatan, mutasi, kontrak kerja
- [x] Model jobdesk & task catalog configurable per perusahaan (tidak hardcode)
- [x] Assignment jobdesk ke karyawan (per karyawan/per lokasi)
- [x] Pembatasan pembuatan tugas: hanya dari jobdesk yang di-assign
- [x] Target output per jobdesk/karyawan (khusus non-shift)
- [x] Aturan jam minimal hadir untuk non-shift (opsional, konfigurasi)
- [x] KPI efisiensi: output vs kapasitas (rumus berbeda untuk shift vs non-shift)
- [x] UI HR: kelola jobdesk & task catalog
- [x] UI HR: target output

## P2 - Penguatan & integrasi lanjutan
- [ ] Integrasi payroll (overtime, potongan, insentif)
- [ ] Integrasi mesin absensi (fingerprint/face)
- [ ] Dashboard eksekutif (tren multi-bulan, per lokasi/divisi)
- [ ] Self-service karyawan: pengajuan koreksi absensi, slip gaji, dokumen
- [ ] Audit & governance HR: riwayat perubahan jobdesk/task/target
- [ ] Template jobdesk/task per industri (opsional, agar setup cepat)
