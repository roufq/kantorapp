-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 03 Apr 2026 pada 09.57
-- Versi server: 8.4.3
-- Versi PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `kantorapp`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `approval_rules`
--

CREATE TABLE `approval_rules` (
  `id` bigint UNSIGNED NOT NULL,
  `scope` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'task',
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_value` int UNSIGNED NOT NULL DEFAULT '0',
  `approval_level` enum('location_admin','super_admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'location_admin',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `approval_rules`
--

INSERT INTO `approval_rules` (`id`, `scope`, `department`, `min_value`, `approval_level`, `is_active`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'task', NULL, 0, 'location_admin', 1, NULL, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(2, 'task', 'IT', 50, 'super_admin', 1, NULL, '2026-03-05 05:21:15', '2026-03-05 05:21:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `shift_id` bigint UNSIGNED DEFAULT NULL,
  `shift_assignment_id` bigint UNSIGNED DEFAULT NULL,
  `check_in_time` timestamp NOT NULL,
  `check_out_time` timestamp NULL DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gps_accuracy_in` decimal(8,2) DEFAULT NULL,
  `gps_accuracy_out` decimal(8,2) DEFAULT NULL,
  `check_in_photo_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_out_photo_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_late` tinyint(1) NOT NULL DEFAULT '0',
  `requires_approval` tinyint(1) NOT NULL DEFAULT '0',
  `approval_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `attendances`
--

INSERT INTO `attendances` (`id`, `user_id`, `location_id`, `shift_id`, `shift_assignment_id`, `check_in_time`, `check_out_time`, `location`, `device_id`, `device_user_agent`, `gps_accuracy_in`, `gps_accuracy_out`, `check_in_photo_path`, `check_out_photo_path`, `is_late`, `requires_approval`, `approval_status`, `created_at`, `updated_at`, `approved_by`) VALUES
(1, 21, NULL, NULL, NULL, '2025-10-24 13:48:06', '2025-10-24 13:49:13', '-7.7721818,110.3562405', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 'approved', '2025-10-24 13:48:06', '2025-10-24 13:49:13', NULL),
(2, 22, 1, 1, NULL, '2025-11-17 02:31:50', NULL, '-7.812318,110.3505976', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 'approved', '2025-11-17 02:31:50', '2025-11-17 02:31:50', NULL),
(3, 1, 1, 1, NULL, '2025-12-03 02:48:49', NULL, '-7.8123228,110.3505768', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 'approved', '2025-12-03 02:48:49', '2025-12-03 02:48:49', NULL),
(4, 27, 5, 2, 312, '2025-12-08 08:09:19', NULL, '-7.8123113,110.3506166', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 'approved', '2025-12-08 08:09:19', '2025-12-08 08:09:19', NULL),
(5, 26, 5, 2, 316, '2025-12-10 10:02:58', NULL, '-7.8122935,110.3506329', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 'approved', '2025-12-10 10:02:58', '2025-12-10 10:02:58', NULL),
(6, 26, 5, 2, 385, '2025-12-20 03:06:26', NULL, '-7.8122894,110.3506403', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 'approved', '2025-12-20 03:06:26', '2025-12-20 03:06:26', NULL),
(7, 1, 1, 1, NULL, '2025-12-22 09:19:27', NULL, '-7.8123595,110.350612', 'dev-9c79nso7xg8mjgxguxr', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 20.10, NULL, 'attendances/checkin/c6ea68e2-c578-4d60-b766-69f6bf21313f.jpg', NULL, 1, 0, 'approved', '2025-12-22 09:19:27', '2025-12-22 09:19:27', NULL),
(8, 18, 1, 1, NULL, '2025-12-23 02:43:35', NULL, '-7.8123258,110.350629', 'dev-px6g0snjzjmjhz2hy2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 21.60, NULL, 'attendances/checkin/ba972909-9da3-4676-8ca1-df56f468b5f7.jpg', NULL, 1, 0, 'approved', '2025-12-23 02:43:35', '2025-12-23 02:43:35', NULL),
(9, 1, 1, 1, NULL, '2025-12-23 02:46:00', NULL, '-7.8123601,110.3506142', 'dev-4zbvf0ngl4nmjhzfqq1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 24.90, NULL, 'attendances/checkin/d2e16890-93bf-4191-90f4-c796c567d014.jpg', NULL, 1, 0, 'approved', '2025-12-23 02:46:00', '2025-12-23 02:46:00', NULL),
(10, 2, 1, 1, NULL, '2025-12-23 06:53:37', NULL, '-7.8123451,110.3506151', 'dev-wv1s7x0xu4imji89hx3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 18.27, NULL, 'attendances/checkin/8f407757-6f78-429f-980c-3975a9277e2a.jpg', NULL, 1, 0, 'approved', '2025-12-23 06:53:37', '2025-12-23 06:53:37', NULL),
(11, 1, 1, 1, NULL, '2025-12-29 03:24:48', NULL, '-7.8123998,110.3505833', 'dev-4zbvf0ngl4nmjhzfqq1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 24.90, NULL, 'attendances/checkin/05c7bda6-4e23-4a1a-8757-c744aed81c1f.jpg', NULL, 1, 0, 'approved', '2025-12-29 03:24:48', '2025-12-29 03:24:48', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `actor_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auditable_id` bigint UNSIGNED DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `meta` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `actor_id`, `action`, `auditable_type`, `auditable_id`, `old_values`, `new_values`, `meta`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 13, 'overtime_approval_updated', 'App\\Models\\OvertimeApproval', 3, '{\"notes\": \"ghfghdfghdfghdfgh\", \"status\": \"rejected\", \"approved_at\": \"2025-12-23T03:38:55.000000Z\"}', '{\"notes\": \"ghfghdfghdfghdfgh\", \"status\": \"rejected\", \"approved_at\": \"2025-12-23T03:39:30.000000Z\"}', '{\"level\": 1, \"user_id\": 2, \"overtime_id\": 2}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-23 03:39:30', '2025-12-23 03:39:30'),
(2, 13, 'task_approved', 'App\\Models\\Task', 27, '{\"approved_at\": null, \"approved_by\": null, \"approval_note\": null, \"approval_status\": \"pending\"}', '{\"approved_at\": \"2025-12-23T06:19:41.000000Z\", \"approved_by\": 13, \"approval_status\": \"approved\"}', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-23 06:19:41', '2025-12-23 06:19:41'),
(3, 13, 'task_approved', 'App\\Models\\Task', 29, '{\"approved_at\": null, \"approved_by\": null, \"approval_note\": null, \"approval_status\": \"pending\"}', '{\"approved_at\": \"2025-12-23T06:51:05.000000Z\", \"approved_by\": 13, \"approval_status\": \"approved\"}', NULL, '192.168.1.88', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-23 06:51:05', '2025-12-23 06:51:05'),
(4, 13, 'task_approved', 'App\\Models\\Task', 28, '{\"approved_at\": null, \"approved_by\": null, \"approval_note\": null, \"approval_status\": \"pending\"}', '{\"approved_at\": \"2025-12-23T06:51:11.000000Z\", \"approved_by\": 13, \"approval_status\": \"approved\"}', NULL, '192.168.1.88', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-23 06:51:11', '2025-12-23 06:51:11'),
(5, 13, 'leave_status_updated', 'App\\Models\\EmployeeLeave', 3, '{\"status\": \"pending\", \"approved_by\": null}', '{\"status\": \"approved\", \"approved_by\": 13}', NULL, '192.168.1.88', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-23 06:59:03', '2025-12-23 06:59:03'),
(6, 13, 'task_slot_approved', 'App\\Models\\TaskSlot', 68, '{\"status\": \"pending\", \"approved_by\": null}', '{\"status\": \"approved\", \"approved_by\": 13}', '{\"task_id\": 28}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 05:08:09', '2025-12-27 05:08:09'),
(7, 13, 'task_slot_approved', 'App\\Models\\TaskSlot', 69, '{\"status\": \"pending\", \"approved_by\": null}', '{\"status\": \"approved\", \"approved_by\": 13}', '{\"task_id\": 29}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 05:09:34', '2025-12-27 05:09:34'),
(8, 13, 'task_slot_approved', 'App\\Models\\TaskSlot', 70, '{\"status\": \"pending\", \"approved_by\": null}', '{\"status\": \"approved\", \"approved_by\": 13}', '{\"task_id\": 29}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 05:09:34', '2025-12-27 05:09:34'),
(9, 13, 'task_slot_approved', 'App\\Models\\TaskSlot', 71, '{\"status\": \"pending\", \"approved_by\": null}', '{\"status\": \"approved\", \"approved_by\": 13}', '{\"task_id\": 29}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 05:09:34', '2025-12-27 05:09:34'),
(10, 13, 'task_approved', 'App\\Models\\Task', 30, '{\"approved_at\": null, \"approved_by\": null, \"approval_note\": null, \"approval_status\": \"pending\"}', '{\"approved_at\": \"2025-12-27T05:16:23.000000Z\", \"approved_by\": 13, \"approval_status\": \"approved\"}', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 05:16:23', '2025-12-27 05:16:23'),
(11, 13, 'task_rejected', 'App\\Models\\Task', 31, '{\"approved_at\": null, \"approved_by\": null, \"approval_note\": null, \"approval_status\": \"pending\"}', '{\"approved_at\": \"2025-12-27T05:25:18.000000Z\", \"approved_by\": 13, \"approval_note\": \"fghdfgdgdfgdfgdfgdfgdgdfg\", \"approval_status\": \"rejected\"}', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 05:25:18', '2025-12-27 05:25:18'),
(12, 13, 'task_approved', 'App\\Models\\Task', 31, '{\"approved_at\": null, \"approved_by\": null, \"approval_note\": \"fghdfgdgdfgdfgdfgdfgdgdfg\", \"approval_status\": \"pending\"}', '{\"approved_at\": \"2025-12-27T05:26:28.000000Z\", \"approved_by\": 13, \"approval_status\": \"approved\"}', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 05:26:28', '2025-12-27 05:26:28'),
(13, 13, 'task_slot_rejected', 'App\\Models\\TaskSlot', 74, '{\"status\": \"pending\", \"approved_by\": null, \"rejection_reason\": null}', '{\"status\": \"rejected\", \"approved_by\": 13, \"rejection_reason\": \"gdgfgdfgdfg\"}', '{\"task_id\": 31}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 05:33:30', '2025-12-27 05:33:30'),
(14, 13, 'task_slot_approved', 'App\\Models\\TaskSlot', 74, '{\"status\": \"pending\", \"approved_by\": null}', '{\"status\": \"approved\", \"approved_by\": 13}', '{\"task_id\": 31}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 05:34:01', '2025-12-27 05:34:01'),
(15, 13, 'leave_status_updated', 'App\\Models\\EmployeeLeave', 4, '{\"status\": \"pending\", \"approved_by\": null}', '{\"status\": \"approved\", \"approved_by\": 13}', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-29 03:16:51', '2025-12-29 03:16:51'),
(16, 13, 'leave_status_updated', 'App\\Models\\EmployeeLeave', 4, '{\"status\": \"approved\", \"approved_by\": 13}', '{\"status\": \"approved\", \"approved_by\": 13}', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-29 03:16:54', '2025-12-29 03:16:54'),
(17, 13, 'leave_status_updated', 'App\\Models\\EmployeeLeave', 4, '{\"status\": \"approved\", \"approved_by\": 13}', '{\"status\": \"approved\", \"approved_by\": 13}', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-29 03:16:58', '2025-12-29 03:16:58'),
(18, 13, 'leave_status_updated', 'App\\Models\\EmployeeLeave', 5, '{\"status\": \"pending\", \"approved_by\": null}', '{\"status\": \"approved\", \"approved_by\": 13}', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-29 03:19:51', '2025-12-29 03:19:51'),
(19, 13, 'location_change_status_updated', 'App\\Models\\LocationChangeRequest', 4, '{\"status\": \"pending\", \"approved_by\": null}', '{\"status\": \"approved\", \"approved_by\": 13}', '{\"user_id\": 18, \"is_permanent\": 0, \"target_location_id\": 5, \"original_location_id\": 1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-29 03:33:15', '2025-12-29 03:33:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1775185611),
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1775185611;', 1775185611),
('laravel-cache-6be66e693b005f7e585da30871bf82b11d0119ec', 'i:1;', 1775118301),
('laravel-cache-6be66e693b005f7e585da30871bf82b11d0119ec:timer', 'i:1775118301;', 1775118301),
('laravel-cache-dash:notices:today:13:2026-04-02', 'a:0:{}', 1775121015),
('laravel-cache-dash:notices:today:13:2026-04-03', 'a:0:{}', 1775186152),
('laravel-cache-dash:notices:upcoming:13:2026-04-02', 'a:0:{}', 1775121015),
('laravel-cache-dash:notices:upcoming:13:2026-04-03', 'a:0:{}', 1775186152),
('laravel-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:0:{}s:11:\"permissions\";a:0:{}s:5:\"roles\";a:0:{}}', 1775199345);

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `divisions`
--

CREATE TABLE `divisions` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `divisions`
--

INSERT INTO `divisions` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'HR', '2025-10-23 09:54:45', '2025-10-23 09:54:45'),
(2, 'IT', '2025-10-23 09:54:45', '2025-10-23 09:54:45'),
(3, 'Finance', '2025-10-23 09:54:45', '2025-10-23 09:54:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employees`
--

CREATE TABLE `employees` (
  `id` bigint UNSIGNED NOT NULL,
  `master_id` bigint UNSIGNED DEFAULT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `schedule_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `default_shift_id` bigint UNSIGNED DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departemen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `tanggal_masuk_kerja` date DEFAULT NULL,
  `divisi_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employees`
--

INSERT INTO `employees` (`id`, `master_id`, `location_id`, `schedule_type`, `default_shift_id`, `nama`, `email`, `telepon`, `alamat`, `jabatan`, `departemen`, `tanggal_lahir`, `tanggal_masuk_kerja`, `divisi_id`, `created_at`, `updated_at`) VALUES
(1, 13, 4, 'fixed', NULL, 'John Doe', 'john@example.com', '123456789', 'Address 1', 'Manager', 'HR', '1990-01-01', NULL, 1, '2025-10-23 09:54:45', '2025-10-23 09:54:51'),
(2, 14, 4, 'fixed', NULL, 'Jane Smith', 'jane@example.com', '987654321', 'Address 2', 'Developer', 'IT', '1992-05-15', NULL, 2, '2025-10-23 09:54:45', '2025-10-24 14:15:15'),
(3, 14, 1, 'fixed', NULL, 'JJ_MAIN Employee 1', 'jj_main.employee1@example.com', '08857436964', 'Jl. Sugeng Jeroni No.54, Patangpuluhan, Wirobrajan, Kota Yogyakarta, Daerah Istimewa Yogyakarta 55251', 'Staff', 'General', '1995-01-01', NULL, 1, '2025-10-23 09:54:45', '2025-11-09 04:57:28'),
(4, 14, 1, 'fixed', NULL, 'JJ_MAIN Employee 2', 'jj_main.employee2@example.com', '08341856849', 'Jl. Sugeng Jeroni No.54, Patangpuluhan, Wirobrajan, Kota Yogyakarta, Daerah Istimewa Yogyakarta 55251', 'Staff', 'General', '1995-01-01', NULL, 1, '2025-10-23 09:54:45', '2025-10-24 14:15:15'),
(5, 13, 1, 'fixed', NULL, 'JJ_MAIN Employee 3', 'jj_main.employee3@example.com', '08486509309', 'Jl. Sugeng Jeroni No.54, Patangpuluhan, Wirobrajan, Kota Yogyakarta, Daerah Istimewa Yogyakarta 55251', 'Staff', 'General', '1995-01-01', NULL, 3, '2025-10-23 09:54:45', '2025-10-24 14:15:15'),
(6, 13, 2, 'fixed', NULL, 'FACT_BDG Employee 1', 'fact_bdg.employee1@example.com', '08674149042', 'Jl. Industri No. 15, Bandung, Jawa Barat 40135', 'Staff', 'General', '1995-01-01', NULL, 3, '2025-10-23 09:54:46', '2025-11-09 04:57:29'),
(7, 15, 2, 'fixed', NULL, 'FACT_BDG Employee 2', 'fact_bdg.employee2@example.com', '08308888320', 'Jl. Industri No. 15, Bandung, Jawa Barat 40135', 'Staff', 'General', '1995-01-01', NULL, 2, '2025-10-23 09:54:46', '2025-10-24 14:15:15'),
(8, 14, 2, 'fixed', NULL, 'FACT_BDG Employee 3', 'fact_bdg.employee3@example.com', '08767700035', 'Jl. Industri No. 15, Bandung, Jawa Barat 40135', 'Staff', 'General', '1995-01-01', NULL, 1, '2025-10-23 09:54:46', '2025-11-09 04:57:29'),
(9, 13, 3, 'fixed', NULL, 'BRANCH_SBY Employee 1', 'branch_sby.employee1@example.com', '08585582565', 'Jl. Tunjungan No. 25, Surabaya, Jawa Timur 60275', 'Staff', 'General', '1995-01-01', NULL, 3, '2025-10-23 09:54:46', '2025-11-09 04:57:29'),
(10, 15, 3, 'fixed', NULL, 'BRANCH_SBY Employee 2', 'branch_sby.employee2@example.com', '08545121048', 'Jl. Tunjungan No. 25, Surabaya, Jawa Timur 60275', 'Staff', 'General', '1995-01-01', NULL, 3, '2025-10-23 09:54:47', '2025-10-24 14:15:15'),
(11, 15, 3, 'fixed', NULL, 'BRANCH_SBY Employee 3', 'branch_sby.employee3@example.com', '08381882814', 'Jl. Tunjungan No. 25, Surabaya, Jawa Timur 60275', 'Staff', 'General', '1995-01-01', NULL, 1, '2025-10-23 09:54:47', '2025-10-23 09:54:51'),
(12, 15, 4, 'fixed', NULL, 'WH_SMG Employee 1', 'wh_smg.employee1@example.com', '08115834841', 'Jl. Logistik No. 8, Semarang, Jawa Tengah 50241', 'Staff', 'General', '1995-01-01', NULL, 2, '2025-10-23 09:54:47', '2025-11-09 04:57:29'),
(13, 13, 4, 'fixed', NULL, 'WH_SMG Employee 2', 'wh_smg.employee2@example.com', '08658652537', 'Jl. Logistik No. 8, Semarang, Jawa Tengah 50241', 'Staff', 'General', '1995-01-01', NULL, 3, '2025-10-23 09:54:48', '2025-10-24 14:15:15'),
(14, 13, 4, 'fixed', NULL, 'WH_SMG Employee 3', 'wh_smg.employee3@example.com', '08315100632', 'Jl. Logistik No. 8, Semarang, Jawa Tengah 50241', 'Staff', 'General', '1995-01-01', NULL, 2, '2025-10-23 09:54:48', '2025-11-09 04:57:29'),
(15, 13, 4, 'fixed', NULL, 'KOS Employee 1', 'kos.employee1@example.com', '08276858973', 'kos ku', 'Staff', 'General', '1995-01-01', NULL, 3, '2025-10-24 14:15:11', '2025-11-09 04:57:29'),
(16, 14, 4, 'fixed', NULL, 'KOS Employee 2', 'kos.employee2@example.com', '08177948188', 'kos ku', 'Staff', 'General', '1995-01-01', NULL, 2, '2025-10-24 14:15:12', '2025-10-24 14:15:15'),
(17, 14, 4, 'fixed', NULL, 'KOS Employee 3', 'kos.employee3@example.com', '08112453719', 'kos ku', 'Staff', 'General', '1995-01-01', NULL, 2, '2025-10-24 14:15:12', '2025-11-09 04:57:29'),
(18, NULL, 5, 'fixed', NULL, 'nvs_employee 1', 'nvs_employee1@example.com', '45645646456456', 'gfhdfgdfgdfgdfgdfgdf', 'nanggur', 'dfgdfgdfg', '2003-02-02', '2025-01-28', 2, '2025-12-08 04:12:07', '2025-12-08 04:12:07'),
(19, NULL, 5, 'fixed', NULL, 'nvs_employee 2', 'nvs_employee2@example.com', '23423423423', 'dfgdgdgfdfgdfgd', 'nanggur', 'dfgdfgdfg', '2000-12-12', '2024-12-12', 1, '2025-12-08 04:13:45', '2025-12-08 04:13:45'),
(20, NULL, 5, 'fixed', NULL, 'nvs_employee 3', 'nvs_employee3@example.com', '232312123123', 'sdfsdfsdfsdfsdfsdfsdfsdfs', 'sdfsdsf', 'dfgdfgdfgsdfsfsdfsdfsdf', '2004-12-12', '2022-12-12', 2, '2025-12-08 04:14:49', '2025-12-20 02:10:18'),
(21, NULL, 5, 'fixed', NULL, 'nvs_employee 4', 'nvs_employee4@example.com', '45645646456456', 'dfdfsdfsdfsdfsdfsdf', 'nanggur', 'dfgdfgdfg', '2002-01-20', '2025-12-12', 2, '2025-12-20 02:10:02', '2025-12-20 02:10:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_absences`
--

CREATE TABLE `employee_absences` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'alfa',
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_contracts`
--

CREATE TABLE `employee_contracts` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `contract_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','ended','terminated') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employee_contracts`
--

INSERT INTO `employee_contracts` (`id`, `employee_id`, `contract_type`, `start_date`, `end_date`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'PKWT', '2025-09-05', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(2, 2, 'PKWT', '2025-09-05', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(3, 3, 'PKWT', '2025-09-05', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(4, 4, 'PKWT', '2025-09-05', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(5, 5, 'PKWT', '2025-09-05', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(6, 6, 'PKWT', '2025-09-05', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(7, 7, 'PKWT', '2025-09-05', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(8, 8, 'PKWT', '2025-09-05', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(9, 9, 'PKWT', '2025-09-05', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(10, 10, 'PKWT', '2025-09-05', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(11, 11, 'PKWT', '2025-09-05', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(12, 12, 'PKWT', '2025-09-05', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(13, 13, 'PKWT', '2025-09-05', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(14, 14, 'PKWT', '2025-09-05', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(15, 15, 'PKWT', '2025-09-05', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(16, 16, 'PKWT', '2025-09-05', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(17, 17, 'PKWT', '2025-09-05', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(18, 18, 'PKWT', '2025-01-28', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(19, 19, 'PKWT', '2024-12-12', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(20, 20, 'PKWT', '2022-12-12', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(21, 21, 'PKWT', '2025-12-12', '2026-09-05', 'active', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_jobdesk_assignments`
--

CREATE TABLE `employee_jobdesk_assignments` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `jobdesk_id` bigint UNSIGNED NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '1',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employee_jobdesk_assignments`
--

INSERT INTO `employee_jobdesk_assignments` (`id`, `employee_id`, `jobdesk_id`, `is_primary`, `start_date`, `end_date`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(2, 2, 5, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(3, 3, 2, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(4, 4, 2, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(5, 5, 2, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(6, 6, 3, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(7, 7, 3, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(8, 8, 3, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(9, 9, 4, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(10, 10, 4, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(11, 11, 4, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(12, 12, 5, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(13, 13, 5, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(14, 14, 5, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(15, 15, 5, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(16, 16, 5, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(17, 17, 5, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(18, 18, 6, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(19, 19, 6, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(20, 20, 6, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(21, 21, 6, 1, '2026-01-05', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_leaves`
--

CREATE TABLE `employee_leaves` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `type` enum('sick','annual','unpaid','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `attachment_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employee_leaves`
--

INSERT INTO `employee_leaves` (`id`, `user_id`, `approved_by`, `location_id`, `start_date`, `end_date`, `type`, `reason`, `status`, `attachment_path`, `created_at`, `updated_at`) VALUES
(1, 4, 13, 2, '2025-12-07', '2025-12-08', 'sick', 'dadasdasd', 'approved', NULL, '2025-12-07 14:50:40', '2025-12-07 14:56:27'),
(2, 5, 13, 2, '2025-12-07', '2025-12-11', 'annual', 'ertertertert', 'approved', NULL, '2025-12-07 14:56:01', '2025-12-07 14:56:39'),
(3, 1, 13, 1, '2025-12-26', '2025-12-27', 'annual', 'ksjfhskjdfhs', 'approved', NULL, '2025-12-23 06:57:20', '2025-12-23 06:59:03'),
(4, 18, 13, 1, '2025-12-29', '2025-12-31', 'annual', 'sdsdfsdfsdf', 'approved', NULL, '2025-12-29 03:16:02', '2025-12-29 03:16:51'),
(5, 13, 13, NULL, '2025-12-29', '2025-12-31', 'annual', 'fghfghfgh', 'approved', NULL, '2025-12-29 03:19:40', '2025-12-29 03:19:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_leave_balances`
--

CREATE TABLE `employee_leave_balances` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `year` int UNSIGNED NOT NULL,
  `annual_quota` int UNSIGNED NOT NULL DEFAULT '12',
  `carry_over` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employee_leave_balances`
--

INSERT INTO `employee_leave_balances` (`id`, `user_id`, `year`, `annual_quota`, `carry_over`, `created_at`, `updated_at`) VALUES
(1, 13, 2025, 12, 0, '2025-12-22 09:40:52', '2025-12-22 09:40:52'),
(2, 1, 2025, 12, 0, '2025-12-23 06:56:48', '2025-12-23 06:56:48'),
(3, 18, 2025, 12, 0, '2025-12-29 03:15:46', '2025-12-29 03:15:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_position_histories`
--

CREATE TABLE `employee_position_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employee_position_histories`
--

INSERT INTO `employee_position_histories` (`id`, `employee_id`, `title`, `department`, `start_date`, `end_date`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'Manager', 'HR', '2025-03-05', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(2, 2, 'Developer', 'IT', '2025-03-05', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(3, 3, 'Staff', 'General', '2025-03-05', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(4, 4, 'Staff', 'General', '2025-03-05', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(5, 5, 'Staff', 'General', '2025-03-05', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(6, 6, 'Staff', 'General', '2025-03-05', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(7, 7, 'Staff', 'General', '2025-03-05', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(8, 8, 'Staff', 'General', '2025-03-05', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(9, 9, 'Staff', 'General', '2025-03-05', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(10, 10, 'Staff', 'General', '2025-03-05', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(11, 11, 'Staff', 'General', '2025-03-05', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(12, 12, 'Staff', 'General', '2025-03-05', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(13, 13, 'Staff', 'General', '2025-03-05', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(14, 14, 'Staff', 'General', '2025-03-05', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(15, 15, 'Staff', 'General', '2025-03-05', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(16, 16, 'Staff', 'General', '2025-03-05', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(17, 17, 'Staff', 'General', '2025-03-05', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(18, 18, 'nanggur', 'dfgdfgdfg', '2025-01-28', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(19, 19, 'nanggur', 'dfgdfgdfg', '2024-12-12', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(20, 20, 'sdfsdsf', 'dfgdfgdfgsdfsfsdfsdfsdf', '2022-12-12', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(21, 21, 'nanggur', 'dfgdfgdfg', '2025-12-12', NULL, NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_shift_schedules`
--

CREATE TABLE `employee_shift_schedules` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `day_of_week` tinyint UNSIGNED NOT NULL,
  `shift_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_tasks`
--

CREATE TABLE `employee_tasks` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `task_catalog_id` bigint UNSIGNED DEFAULT NULL,
  `assigned_by` bigint UNSIGNED NOT NULL,
  `assigned_to` bigint UNSIGNED NOT NULL,
  `status` enum('pending','in_progress','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `progress` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `requires_approval` tinyint(1) NOT NULL DEFAULT '0',
  `approval_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `approval_level` enum('none','location_admin','super_admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_note` text COLLATE utf8mb4_unicode_ci,
  `due_date` date DEFAULT NULL,
  `duration_minutes` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employee_tasks`
--

INSERT INTO `employee_tasks` (`id`, `title`, `description`, `task_catalog_id`, `assigned_by`, `assigned_to`, `status`, `progress`, `requires_approval`, `approval_status`, `approval_level`, `approved_by`, `approved_at`, `approval_note`, `due_date`, `duration_minutes`, `created_at`, `updated_at`, `photo_path`, `document_path`) VALUES
(1, 'asdadad', 'asdasdasdasdasd', NULL, 13, 1, 'completed', 100, 0, 'approved', 'none', NULL, NULL, NULL, '2025-12-04', NULL, '2025-11-29 10:59:21', '2025-12-09 09:38:47', 'tasks/photos/R4GG74wwyqMHYmu0L3fRTTKdHg4FaqCHqIQbq80q.png', NULL),
(2, 'asdasdasdads', 'asdasdasd', NULL, 13, 13, 'completed', 100, 0, 'approved', 'none', NULL, NULL, NULL, '2025-12-03', NULL, '2025-11-29 10:59:36', '2025-12-09 09:38:47', 'tasks/photos/aRz8VXPh74nmHnslKj0mZ2O6Dj5pSsRKEIE99x2b.png', NULL),
(3, 'dfsdfsdfsdf', 'sdfsdfsfdsdf', NULL, 13, 20, 'completed', 100, 0, 'approved', 'none', NULL, NULL, NULL, '2025-12-06', NULL, '2025-11-29 11:53:29', '2025-12-09 09:38:47', NULL, NULL),
(4, 'hgfhgfhgfhgsdfsdfsdf', 'jhgjhgjhgjhgsdfsdf', NULL, 13, 2, 'completed', 100, 0, 'approved', 'none', NULL, NULL, NULL, '2025-12-06', NULL, '2025-11-29 12:26:38', '2025-12-09 09:38:47', 'tasks/photos/3vFlkpcB8qrdQbh36deFDuRpPCDr24wvDHsqv0Lz.png', NULL),
(5, 'jhgjhgjhgjhg', 'jhgjhgjhgjhgjh', NULL, 13, 13, 'completed', 100, 0, 'approved', 'none', NULL, NULL, NULL, '2025-12-06', NULL, '2025-11-29 12:27:29', '2025-12-09 09:38:47', 'tasks/photos/wGYCWWuRqTUAkocMG6aq6fXxORiKvK5GKaNgcNkf.png', NULL),
(6, 'sdfsdfsdf', 'sdfsdfsdfdf', NULL, 13, 1, 'completed', 100, 0, 'approved', 'none', NULL, NULL, NULL, '2025-12-12', NULL, '2025-11-30 03:51:34', '2025-12-09 09:38:47', 'tasks/photos/3ShyMiZtfU5M4L3TqaISe0iD6ljpjo0PIegxjSTO.png', NULL),
(7, 'sdfsdfsdfs', 'dfsfsdfsdf', NULL, 13, 13, 'completed', 100, 0, 'approved', 'none', NULL, NULL, NULL, '2025-12-12', NULL, '2025-11-30 03:54:14', '2025-12-09 09:38:47', 'tasks/photos/CrAQz6Ey8FIgcfR5undQOTGiZeVT0c1HTcCUrvUj.png', NULL),
(8, 'Membuat beban kerja', 'blabla', NULL, 1, 1, 'completed', 100, 0, 'approved', 'none', NULL, NULL, NULL, '2025-12-04', NULL, '2025-12-03 02:52:06', '2025-12-09 09:38:47', NULL, NULL),
(9, 'sdfsdfsd', 'sfdsdfsdf', NULL, 7, 7, 'completed', 100, 0, 'approved', 'none', NULL, NULL, NULL, '2025-12-12', NULL, '2025-12-03 05:05:23', '2025-12-03 05:21:10', 'tasks/photos/Jjjq1ZW6nlC6xaX630pCV3OW0tyDZYfY7wmvjLvi.jpg', NULL),
(10, 'Riset New Produk', 'Riset produk kosmetik dengan nilai penjualan per bulan di atas 300 juta dan belum ada top of mind.', NULL, 1, 1, 'completed', 100, 0, 'approved', 'none', NULL, NULL, NULL, '2025-12-11', NULL, '2025-12-09 02:52:13', '2025-12-09 09:38:47', NULL, NULL),
(11, 'Membuat Target Omzet Untuk Setiap Project', 'Demi kemudahan tracking project, apakah rugi/tidak dan lanjut/tidak, perlu dibuat target omzet untuk selanjutnya dilakukan evaluasi setiap pekannya.', NULL, 1, 1, 'completed', 100, 0, 'approved', 'none', NULL, NULL, NULL, '2025-12-12', NULL, '2025-12-09 02:57:07', '2025-12-09 09:38:47', NULL, NULL),
(12, 'sdfsdfs', 'sdfsdfs', NULL, 13, 26, 'in_progress', 20, 0, 'approved', 'none', NULL, NULL, NULL, '2025-12-18', 240, '2025-12-09 08:12:23', '2025-12-22 03:05:59', 'tasks/photos/l1mgGHp9elrwy93e3wnVJwjH7TrbjiWBYH3Oj14A.jpg', NULL),
(13, 'resep makasan', 'sdfsdfsf', NULL, 26, 26, 'completed', 100, 1, 'approved', 'super_admin', 13, '2025-12-10 03:39:33', NULL, '2025-12-15', 10080, '2025-12-09 11:29:23', '2025-12-10 06:04:46', 'tasks/photos/rpy8wy9zhtqaDM0A9Gz4c27pgOX33KDtwqjbk3PZ.jpg', NULL),
(14, 'asdasdasdsdfsfsdf', 'asdasdassdfsdfsdf', NULL, 26, 26, 'in_progress', 50, 1, 'approved', 'super_admin', 13, '2025-12-15 07:39:31', NULL, '2025-12-18', 240, '2025-12-10 03:41:09', '2025-12-15 08:49:00', 'tasks/photos/2Spy8EvraraZl9IbWCUcE3FnZX0RHz6Jp33mMLmq.jpg', 'tasks/documents/7eaH8UwW8PSSOcVHmPxXYxl8Hcoj7SJHJuLIuZ2Q.pdf'),
(15, 'dfgdfgdfg', 'dfgdfgdfg', NULL, 26, 26, 'completed', 100, 1, 'approved', 'super_admin', 13, '2025-12-10 05:04:46', NULL, '2025-12-14', 240, '2025-12-10 05:01:38', '2025-12-10 07:33:25', 'tasks/photos/KMm92QH5jTDuzl91AEXWCIemCAGrKdv1jFs55FHm.jpg', NULL),
(16, 'sdfsdf', 'sdfsdf', NULL, 13, 27, 'pending', 0, 0, 'approved', 'none', 13, '2025-12-12 08:28:04', NULL, '2025-12-13', 240, '2025-12-12 08:28:04', '2025-12-12 08:28:04', NULL, NULL),
(17, 'sdfsdf', 'sdfsdf', NULL, 13, 27, 'pending', 0, 0, 'approved', 'none', 13, '2025-12-12 08:28:48', NULL, '2025-12-13', 240, '2025-12-12 08:28:48', '2025-12-12 08:28:48', NULL, NULL),
(18, 'test 1', 'melkukan update task', NULL, 13, 28, 'pending', 0, 0, 'approved', 'none', 13, '2025-12-12 08:37:40', NULL, '2025-12-20', 240, '2025-12-12 08:37:40', '2025-12-12 08:37:40', 'tasks/photos/lbErLT2TDWFGda5q4zjzcE2aubzmKUMjW9lCb8Ot.jpg', NULL),
(19, 'membuat resep masker', 'jkasksdfkjshdfsdf', NULL, 13, 27, 'pending', 0, 0, 'approved', 'none', 13, '2025-12-13 01:18:05', NULL, '2025-12-15', 240, '2025-12-13 01:18:05', '2025-12-13 01:18:05', 'tasks/photos/2fivHpKUsLs3cdegIszQAGMkg1vLIgETyWBOHwEy.jpg', NULL),
(20, 'dfsdfsf', 'sdfsdf', NULL, 13, 27, 'pending', 0, 0, 'approved', 'none', 13, '2025-12-13 01:23:26', NULL, '2025-12-23', 240, '2025-12-13 01:23:26', '2025-12-22 03:52:20', 'tasks/photos/vqd5YYLfLeoqHuZu9t4xPyqIJYUjoQCLmxaFYmhb.jpg', NULL),
(24, 'resep masker', 'serserser', NULL, 13, 27, 'pending', 0, 0, 'approved', 'none', 13, '2025-12-13 01:30:32', NULL, '2025-12-23', 240, '2025-12-13 01:30:32', '2025-12-22 02:58:28', NULL, NULL),
(25, 'dsfsdfsdf', 'sdfsdfsdf', NULL, 13, 28, 'pending', 0, 0, 'approved', 'none', 13, '2025-12-22 03:54:46', NULL, '2025-12-25', 240, '2025-12-22 03:54:46', '2025-12-22 03:54:46', NULL, NULL),
(26, 'fgdgdg', 'dgfdgd', NULL, 13, 26, 'pending', 0, 0, 'approved', 'none', 13, '2025-12-23 06:17:45', NULL, '2025-12-26', 240, '2025-12-23 06:17:45', '2025-12-23 06:17:45', NULL, NULL),
(27, 'sdfsfsfsdf', 'sdfsdfsdf', NULL, 1, 1, 'pending', 0, 1, 'approved', 'super_admin', 13, '2025-12-23 06:19:41', NULL, '2025-12-26', 240, '2025-12-23 06:18:51', '2025-12-23 06:19:41', NULL, NULL),
(28, 'interview user', 'sdfsfs', NULL, 1, 1, 'completed', 100, 1, 'approved', 'super_admin', 13, '2025-12-23 06:51:11', NULL, '2025-12-23', 30, '2025-12-23 06:48:21', '2025-12-27 05:08:09', NULL, NULL),
(29, 'mencari vendor untuk produksi box', 'sdfsdfsdfsd', NULL, 1, 1, 'completed', 100, 1, 'approved', 'super_admin', 13, '2025-12-23 06:51:05', NULL, '2025-12-26', 60, '2025-12-23 06:50:08', '2025-12-27 05:09:34', NULL, NULL),
(30, 'hjfhfhfgh', 'fghfhfhgfh', NULL, 26, 26, 'pending', 0, 1, 'approved', 'super_admin', 13, '2025-12-27 05:16:23', NULL, '2025-12-28', 60, '2025-12-27 05:15:32', '2025-12-27 05:16:23', NULL, NULL),
(31, 'ghjghjfghfhfgh', 'ghjghjghjfghfgh', NULL, 18, 18, 'in_progress', 50, 1, 'approved', 'super_admin', 13, '2025-12-27 05:26:28', NULL, '2025-12-30', 60, '2025-12-27 05:17:46', '2025-12-27 05:34:01', NULL, NULL),
(32, 'Administrasi Harian - JJ_MAIN Employee 1', 'Dummy task dari catalog.', 4, 13, 1, 'completed', 100, 0, 'approved', 'none', 13, '2026-03-05 05:21:15', NULL, '2026-03-12', 120, '2026-03-05 05:21:15', '2026-03-05 05:21:15', NULL, NULL),
(33, 'Layanan Pelanggan - JJ_MAIN Employee 2', 'Dummy task dari catalog.', 3, 13, 2, 'completed', 100, 0, 'approved', 'none', 13, '2026-03-05 05:21:15', NULL, '2026-03-12', 120, '2026-03-05 05:21:15', '2026-03-05 05:21:15', NULL, NULL),
(34, 'Audit Data - JJ_MAIN Employee 3', 'Dummy task dari catalog.', 2, 13, 3, 'completed', 100, 0, 'approved', 'none', 13, '2026-03-05 05:21:15', NULL, '2026-03-12', 120, '2026-03-05 05:21:15', '2026-03-05 05:21:15', NULL, NULL),
(35, 'Administrasi Harian - FACT_BDG Employee 1', 'Dummy task dari catalog.', 4, 13, 4, 'completed', 100, 0, 'approved', 'none', 13, '2026-03-05 05:21:15', NULL, '2026-03-12', 120, '2026-03-05 05:21:15', '2026-03-05 05:21:15', NULL, NULL),
(36, 'Administrasi Harian - FACT_BDG Employee 2', 'Dummy task dari catalog.', 4, 13, 5, 'completed', 100, 0, 'approved', 'none', 13, '2026-03-05 05:21:15', NULL, '2026-03-12', 120, '2026-03-05 05:21:15', '2026-03-05 05:21:15', NULL, NULL),
(37, 'asdasdasd', 'asdfasdfasfeadfefadfaefaedfadsae', 10, 13, 7, 'pending', 0, 0, 'approved', 'none', 13, '2026-03-05 06:31:17', NULL, '2026-03-13', 150, '2026-03-05 06:31:17', '2026-03-05 06:31:17', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_transfers`
--

CREATE TABLE `employee_transfers` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `from_location_id` bigint UNSIGNED DEFAULT NULL,
  `to_location_id` bigint UNSIGNED DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employee_transfers`
--

INSERT INTO `employee_transfers` (`id`, `employee_id`, `from_location_id`, `to_location_id`, `effective_date`, `reason`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 5, '2026-02-05', 'Rotasi lokasi (dummy).', 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee_work_recaps`
--

CREATE TABLE `employee_work_recaps` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `year` smallint UNSIGNED NOT NULL,
  `month` tinyint UNSIGNED NOT NULL,
  `slot_minutes_approved` int UNSIGNED NOT NULL DEFAULT '0',
  `attendance_minutes` int UNSIGNED NOT NULL DEFAULT '0',
  `total_minutes` int UNSIGNED NOT NULL DEFAULT '0',
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employee_work_recaps`
--

INSERT INTO `employee_work_recaps` (`id`, `employee_id`, `location_id`, `year`, `month`, `slot_minutes_approved`, `attendance_minutes`, `total_minutes`, `meta`, `created_at`, `updated_at`) VALUES
(3, 1, 4, 2026, 3, 600, 1200, 1800, NULL, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(4, 2, 4, 2026, 3, 600, 1200, 1800, NULL, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(5, 3, 1, 2026, 3, 600, 1200, 1800, NULL, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(6, 4, 1, 2026, 3, 600, 1200, 1800, NULL, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(7, 5, 1, 2026, 3, 600, 1200, 1800, NULL, '2026-03-05 05:21:15', '2026-03-05 05:21:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `holidays`
--

CREATE TABLE `holidays` (
  `id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_national` tinyint(1) NOT NULL DEFAULT '1',
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `holidays`
--

INSERT INTO `holidays` (`id`, `date`, `name`, `is_national`, `location_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '2025-12-31', 'tahun baru', 1, NULL, 1, '2025-12-23 07:00:34', '2025-12-23 07:00:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobdesks`
--

CREATE TABLE `jobdesks` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `role_scope` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `min_attendance_minutes` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `jobdesks`
--

INSERT INTO `jobdesks` (`id`, `name`, `description`, `role_scope`, `location_id`, `created_by`, `is_active`, `min_attendance_minutes`, `created_at`, `updated_at`) VALUES
(1, 'General Office', 'Jobdesk umum untuk karyawan non-shift.', 'Karyawan', NULL, 13, 1, 360, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(2, 'Operasional - JJ_MAIN', 'Jobdesk operasional per lokasi.', 'Karyawan', 1, 13, 1, 420, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(3, 'Operasional - FACT_BDG', 'Jobdesk operasional per lokasi.', 'Karyawan', 2, 13, 1, 420, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(4, 'Operasional - BRANCH_SBY', 'Jobdesk operasional per lokasi.', 'Karyawan', 3, 13, 1, 420, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(5, 'Operasional - KOS', 'Jobdesk operasional per lokasi.', 'Karyawan', 4, 13, 1, 420, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(6, 'Operasional - NVS', 'Jobdesk operasional per lokasi.', 'Karyawan', 5, 13, 1, 420, '2026-03-05 05:21:15', '2026-03-05 05:21:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobdesk_output_targets`
--

CREATE TABLE `jobdesk_output_targets` (
  `id` bigint UNSIGNED NOT NULL,
  `jobdesk_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED DEFAULT NULL,
  `year` smallint UNSIGNED NOT NULL,
  `month` tinyint UNSIGNED NOT NULL,
  `unit` enum('minutes','points','weight') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'points',
  `target_value` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `jobdesk_output_targets`
--

INSERT INTO `jobdesk_output_targets` (`id`, `jobdesk_id`, `employee_id`, `year`, `month`, `unit`, `target_value`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 2026, 3, 'points', 60, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(2, 2, NULL, 2026, 3, 'points', 60, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(3, 3, NULL, 2026, 3, 'points', 60, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(4, 4, NULL, 2026, 3, 'points', 60, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(5, 5, NULL, 2026, 3, 'points', 60, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(6, 6, NULL, 2026, 3, 'points', 60, '2026-03-05 05:21:15', '2026-03-05 05:21:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"8f5bffcc-8193-402d-9ff5-31596bfa327a\",\"displayName\":\"App\\\\Events\\\\MessageSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":17:{s:5:\\\"event\\\";O:22:\\\"App\\\\Events\\\\MessageSent\\\":1:{s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Message\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";b:1;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1764677295,\"delay\":null}', 0, NULL, 1764677295, 1764677295),
(2, 'default', '{\"uuid\":\"10ce7bf1-f12c-4c01-aa28-878b191831c1\",\"displayName\":\"App\\\\Events\\\\MessageSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":17:{s:5:\\\"event\\\";O:22:\\\"App\\\\Events\\\\MessageSent\\\":1:{s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Message\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";b:1;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1764677354,\"delay\":null}', 0, NULL, 1764677354, 1764677354),
(3, 'default', '{\"uuid\":\"d64f28e3-0ba5-4c8e-9b85-2ac45e97b095\",\"displayName\":\"App\\\\Events\\\\MessageSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":17:{s:5:\\\"event\\\";O:22:\\\"App\\\\Events\\\\MessageSent\\\":1:{s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Message\\\";s:2:\\\"id\\\";i:3;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";b:1;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1764683393,\"delay\":null}', 0, NULL, 1764683393, 1764683393),
(4, 'default', '{\"uuid\":\"0d47bb02-5dc8-4619-b769-664bbe633f15\",\"displayName\":\"App\\\\Events\\\\MessageSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":17:{s:5:\\\"event\\\";O:22:\\\"App\\\\Events\\\\MessageSent\\\":1:{s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Message\\\";s:2:\\\"id\\\";i:4;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";b:1;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1765084537,\"delay\":null}', 0, NULL, 1765084537, 1765084537),
(5, 'default', '{\"uuid\":\"22cbe833-bb20-449f-a996-c0bdd755c79d\",\"displayName\":\"App\\\\Events\\\\MessageSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":17:{s:5:\\\"event\\\";O:22:\\\"App\\\\Events\\\\MessageSent\\\":1:{s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Message\\\";s:2:\\\"id\\\";i:5;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";b:1;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1765084546,\"delay\":null}', 0, NULL, 1765084546, 1765084546),
(6, 'default', '{\"uuid\":\"e9b5bbc0-56dc-4df1-9191-ac5e0d3c8cc6\",\"displayName\":\"App\\\\Notifications\\\\TaskSlotSubmittedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:13;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\TaskSlotSubmittedNotification\\\":4:{s:53:\\\"\\u0000App\\\\Notifications\\\\TaskSlotSubmittedNotification\\u0000task\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\Task\\\";s:2:\\\"id\\\";i:14;s:9:\\\"relations\\\";a:1:{i:0;s:8:\\\"assignee\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:53:\\\"\\u0000App\\\\Notifications\\\\TaskSlotSubmittedNotification\\u0000slot\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:19:\\\"App\\\\Models\\\\TaskSlot\\\";s:2:\\\"id\\\";i:27;s:9:\\\"relations\\\";a:2:{i:0;s:4:\\\"task\\\";i:1;s:13:\\\"task.assignee\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:64:\\\"\\u0000App\\\\Notifications\\\\TaskSlotSubmittedNotification\\u0000submittedByRole\\\";s:8:\\\"karyawan\\\";s:2:\\\"id\\\";s:36:\\\"4f03c21f-88f0-4bc1-8134-4c0d515026be\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1765788341,\"delay\":null}', 0, NULL, 1765788341, 1765788341),
(7, 'default', '{\"uuid\":\"d6e76b03-4804-4ace-b725-6e707febb9f4\",\"displayName\":\"App\\\\Notifications\\\\TaskSlotSubmittedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:14;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\TaskSlotSubmittedNotification\\\":4:{s:53:\\\"\\u0000App\\\\Notifications\\\\TaskSlotSubmittedNotification\\u0000task\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\Task\\\";s:2:\\\"id\\\";i:14;s:9:\\\"relations\\\";a:1:{i:0;s:8:\\\"assignee\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:53:\\\"\\u0000App\\\\Notifications\\\\TaskSlotSubmittedNotification\\u0000slot\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:19:\\\"App\\\\Models\\\\TaskSlot\\\";s:2:\\\"id\\\";i:27;s:9:\\\"relations\\\";a:2:{i:0;s:4:\\\"task\\\";i:1;s:13:\\\"task.assignee\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:64:\\\"\\u0000App\\\\Notifications\\\\TaskSlotSubmittedNotification\\u0000submittedByRole\\\";s:8:\\\"karyawan\\\";s:2:\\\"id\\\";s:36:\\\"97852e86-99be-4d7b-b860-91349d8370d2\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1765788341,\"delay\":null}', 0, NULL, 1765788341, 1765788341),
(8, 'default', '{\"uuid\":\"8f3862dd-7963-4c15-bcb4-1640fa6ab6e1\",\"displayName\":\"App\\\\Notifications\\\\TaskSlotSubmittedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:15;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\TaskSlotSubmittedNotification\\\":4:{s:53:\\\"\\u0000App\\\\Notifications\\\\TaskSlotSubmittedNotification\\u0000task\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\Task\\\";s:2:\\\"id\\\";i:14;s:9:\\\"relations\\\";a:1:{i:0;s:8:\\\"assignee\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:53:\\\"\\u0000App\\\\Notifications\\\\TaskSlotSubmittedNotification\\u0000slot\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:19:\\\"App\\\\Models\\\\TaskSlot\\\";s:2:\\\"id\\\";i:27;s:9:\\\"relations\\\";a:2:{i:0;s:4:\\\"task\\\";i:1;s:13:\\\"task.assignee\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:64:\\\"\\u0000App\\\\Notifications\\\\TaskSlotSubmittedNotification\\u0000submittedByRole\\\";s:8:\\\"karyawan\\\";s:2:\\\"id\\\";s:36:\\\"1029e3b8-58c0-4288-881e-42befa0b1ffd\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1765788341,\"delay\":null}', 0, NULL, 1765788341, 1765788341),
(9, 'default', '{\"uuid\":\"432810d4-4cfd-4ab0-ad67-70fee9be6c59\",\"displayName\":\"App\\\\Events\\\\MessageSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":17:{s:5:\\\"event\\\";O:22:\\\"App\\\\Events\\\\MessageSent\\\":1:{s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Message\\\";s:2:\\\"id\\\";i:10;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";b:1;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1766046339,\"delay\":null}', 0, NULL, 1766046339, 1766046339),
(10, 'default', '{\"uuid\":\"c4a0fbce-ef72-4a7e-b1ad-12dd756b8fc8\",\"displayName\":\"App\\\\Events\\\\MessageSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":17:{s:5:\\\"event\\\";O:22:\\\"App\\\\Events\\\\MessageSent\\\":1:{s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Message\\\";s:2:\\\"id\\\";i:11;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";b:1;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1766047495,\"delay\":null}', 0, NULL, 1766047495, 1766047495),
(11, 'default', '{\"uuid\":\"9825a3a3-bcad-47f9-a525-095c0eadcf0f\",\"displayName\":\"App\\\\Events\\\\MessageSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":17:{s:5:\\\"event\\\";O:22:\\\"App\\\\Events\\\\MessageSent\\\":1:{s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Message\\\";s:2:\\\"id\\\";i:12;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";b:1;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1766047511,\"delay\":null}', 0, NULL, 1766047511, 1766047511),
(12, 'default', '{\"uuid\":\"81d8fe59-cde2-4140-9ebb-bc72b676d618\",\"displayName\":\"App\\\\Events\\\\MessageSent\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":17:{s:5:\\\"event\\\";O:22:\\\"App\\\\Events\\\\MessageSent\\\":1:{s:7:\\\"message\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Message\\\";s:2:\\\"id\\\";i:44;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";b:1;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1766812061,\"delay\":null}', 0, NULL, 1766812061, 1766812061);

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `locations`
--

CREATE TABLE `locations` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_logo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Asia/Jakarta',
  `primary_color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `radius` decimal(8,2) NOT NULL DEFAULT '50.00',
  `settings` json DEFAULT NULL,
  `custom_css_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_js_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `shift_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `locations`
--

INSERT INTO `locations` (`id`, `name`, `brand_name`, `brand_logo_url`, `code`, `address`, `timezone`, `primary_color`, `secondary_color`, `latitude`, `longitude`, `radius`, `settings`, `custom_css_url`, `custom_js_url`, `is_active`, `shift_enabled`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Nava Group', NULL, NULL, 'JJ_MAIN', 'Jl. Sugeng Jeroni No.54, Patangpuluhan, Wirobrajan, Kota Yogyakarta, Daerah Istimewa Yogyakarta 55251', 'Asia/Jakarta', NULL, NULL, -7.81218341, 110.35052119, 50.00, '{\"overtime_rate\": 1.5, \"break_duration\": 60, \"work_hours_per_day\": 8}', NULL, NULL, 1, 1, 0, '2025-10-23 09:54:45', '2025-10-23 09:54:45'),
(2, 'Factory Bandung', NULL, NULL, 'FACT_BDG', 'Jl. Industri No. 15, Bandung, Jawa Barat 40135', 'Asia/Jakarta', NULL, NULL, -6.91750000, 107.61910000, 150.00, '{\"overtime_rate\": 2, \"break_duration\": 30, \"work_hours_per_day\": 12}', NULL, NULL, 1, 1, 0, '2025-10-23 09:54:45', '2025-10-23 09:54:45'),
(3, 'Branch Office Surabaya', NULL, NULL, 'BRANCH_SBY', 'Jl. Tunjungan No. 25, Surabaya, Jawa Timur 60275', 'Asia/Jakarta', NULL, NULL, -7.25750000, 112.75210000, 80.00, '{\"overtime_rate\": 1.5, \"break_duration\": 60, \"work_hours_per_day\": 8}', NULL, NULL, 1, 1, 0, '2025-10-23 09:54:45', '2025-10-23 09:54:45'),
(4, 'kos', 'kos', NULL, 'KOS', 'kos ku', 'Asia/Jakarta', NULL, NULL, -7.77227912, 110.35619039, 50.00, '{\"overtime_rate\": 1.75, \"break_duration\": 45, \"work_hours_per_day\": 12}', NULL, NULL, 1, 1, 0, '2025-10-23 09:54:45', '2025-10-24 13:46:19'),
(5, 'nava shift', 'nava shift', NULL, 'NVS', NULL, 'Asia/Jakarta', NULL, NULL, -7.81216599, 110.35048038, 50.00, '[]', NULL, NULL, 1, 1, 0, '2025-12-08 04:10:38', '2025-12-08 04:10:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `location_change_requests`
--

CREATE TABLE `location_change_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `original_location_id` bigint UNSIGNED NOT NULL,
  `target_location_id` bigint UNSIGNED NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `is_permanent` tinyint(1) NOT NULL DEFAULT '0',
  `request_date` date NOT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `location_change_requests`
--

INSERT INTO `location_change_requests` (`id`, `user_id`, `original_location_id`, `target_location_id`, `reason`, `status`, `is_permanent`, `request_date`, `approved_by`, `created_at`, `updated_at`) VALUES
(1, 7, 3, 1, 'dfsdfsdfsfdsfd', 'approved', 0, '2025-12-01', 13, '2025-12-01 12:26:24', '2025-12-01 12:26:24'),
(2, 7, 3, 4, 'adasdasdasdasd', 'approved', 0, '2025-12-01', 13, '2025-12-01 12:28:51', '2025-12-01 12:29:13'),
(3, 16, 3, 4, 'dfsdfsfsfsdfsdsdfsdf', 'rejected', 0, '2025-12-07', 13, '2025-12-07 01:48:50', '2025-12-16 04:40:43'),
(4, 18, 1, 5, 'dgdfgdfgdfg', 'approved', 0, '2025-12-29', 13, '2025-12-29 03:32:39', '2025-12-29 03:33:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `location_settings`
--

CREATE TABLE `location_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` json NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `location_settings`
--

INSERT INTO `location_settings` (`id`, `location_id`, `key`, `value`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, 5, 'default_shift_id', '\"2\"', 'number', NULL, '2025-12-08 04:10:38', '2025-12-08 04:10:38'),
(2, 5, 'notify_pending_approvals_time', '\"\\\"00:00\\\"\"', 'string', NULL, '2025-12-23 04:27:06', '2025-12-23 04:27:06'),
(3, 5, 'notify_shift_h1_time', '\"\\\"00:00\\\"\"', 'string', NULL, '2025-12-23 04:27:06', '2025-12-23 04:27:06'),
(4, 5, 'notify_leave_monthly_summary_time', '\"\\\"00:00\\\"\"', 'string', NULL, '2025-12-23 04:27:06', '2025-12-23 04:27:06'),
(5, 5, 'notify_leave_monthly_reminder_time', '\"\\\"00:00\\\"\"', 'string', NULL, '2025-12-23 04:27:06', '2025-12-23 04:27:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `location_shifts`
--

CREATE TABLE `location_shifts` (
  `id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `shift_id` bigint UNSIGNED NOT NULL,
  `category` enum('office','non_office') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'office',
  `time_slots` json DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `location_shifts`
--

INSERT INTO `location_shifts` (`id`, `location_id`, `shift_id`, `category`, `time_slots`, `is_default`, `created_at`, `updated_at`) VALUES
(4, 2, 2, 'office', '[{\"end\": \"15:00\", \"days\": [], \"start\": \"06:00\"}, {\"end\": \"19:00\", \"days\": [], \"start\": \"12:00\"}, {\"end\": \"23:00\", \"days\": [], \"start\": \"15:00\"}, {\"end\": \"06:00\", \"days\": [], \"start\": \"22:00\"}]', 0, NULL, '2025-12-07 13:23:58'),
(10, 4, 2, 'non_office', '[{\"end\": \"14:00\", \"start\": \"06:00\"}, {\"end\": \"22:00\", \"start\": \"14:00\"}, {\"end\": \"06:00\", \"start\": \"22:00\"}]', 0, NULL, '2025-12-05 13:54:04'),
(12, 3, 1, 'office', '[{\"end\": \"17:00\", \"days\": [\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"], \"start\": \"09:00\"}, {\"end\": \"14:00\", \"days\": [\"saturday\"], \"start\": \"08:00\"}]', 0, '2025-12-07 03:30:27', '2025-12-07 03:30:27'),
(13, 1, 1, 'office', '[{\"end\": \"17:00\", \"days\": [\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"], \"start\": \"09:00\"}, {\"end\": \"14:00\", \"days\": [\"saturday\"], \"start\": \"08:00\"}]', 0, '2025-12-07 03:31:25', '2025-12-07 03:31:25'),
(14, 5, 2, 'non_office', '[{\"end\": \"14:00\", \"days\": [], \"start\": \"06:00\"}, {\"end\": \"22:00\", \"days\": [], \"start\": \"14:00\"}, {\"end\": \"06:00\", \"days\": [], \"start\": \"22:00\"}]', 0, '2025-12-08 04:17:04', '2025-12-08 04:17:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `location_work_targets`
--

CREATE TABLE `location_work_targets` (
  `id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED DEFAULT NULL,
  `year` smallint UNSIGNED NOT NULL,
  `month` tinyint UNSIGNED NOT NULL,
  `target_minutes` int UNSIGNED NOT NULL DEFAULT '0',
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `location_work_targets`
--

INSERT INTO `location_work_targets` (`id`, `location_id`, `employee_id`, `year`, `month`, `target_minutes`, `meta`, `created_at`, `updated_at`) VALUES
(1, 5, NULL, 2025, 12, 12400, NULL, '2025-12-09 09:56:35', '2025-12-09 09:56:35'),
(2, 2, NULL, 2025, 12, 12400, NULL, '2025-12-10 06:00:02', '2025-12-10 06:00:02'),
(3, 1, NULL, 2026, 3, 9600, NULL, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(4, 2, NULL, 2026, 3, 9600, NULL, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(5, 3, NULL, 2026, 3, 9600, NULL, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(6, 4, NULL, 2026, 3, 9600, NULL, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(7, 5, NULL, 2026, 3, 9600, NULL, '2026-03-05 05:21:15', '2026-03-05 05:21:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_tasks`
--

CREATE TABLE `master_tasks` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `assigned_by` bigint UNSIGNED DEFAULT NULL,
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `status` enum('pending','in_progress','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `progress` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `messages`
--

CREATE TABLE `messages` (
  `id` bigint UNSIGNED NOT NULL,
  `sender_id` bigint UNSIGNED NOT NULL,
  `receiver_id` bigint UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 13, 4, 'asdasdads', NULL, '2025-12-02 12:08:15', '2025-12-02 12:08:15'),
(2, 13, 4, 'sdfsfsdfsdf', NULL, '2025-12-02 12:09:14', '2025-12-02 12:09:14'),
(3, 13, 7, 'hai', NULL, '2025-12-02 13:49:53', '2025-12-02 13:49:53'),
(4, 14, 1, 'dfsdfsdfsdf', '2025-12-10 09:42:24', '2025-12-07 05:15:37', '2025-12-10 09:42:24'),
(5, 14, 1, 'sdfsdfsdfsd', '2025-12-10 09:42:24', '2025-12-07 05:15:46', '2025-12-10 09:42:24'),
(6, 14, 4, 'Status Izin/Cuti Anda (2025-12-07 s/d 2025-12-08, sick) berubah menjadi: approved', NULL, '2025-12-07 14:52:07', '2025-12-07 14:52:07'),
(7, 13, 4, 'Status Izin/Cuti Anda (2025-12-07 s/d 2025-12-08, sick) berubah menjadi: approved', NULL, '2025-12-07 14:56:27', '2025-12-07 14:56:27'),
(8, 13, 4, 'Status Izin/Cuti Anda (2025-12-07 s/d 2025-12-08, sick) berubah menjadi: approved', NULL, '2025-12-07 14:56:32', '2025-12-07 14:56:32'),
(9, 13, 5, 'Status Izin/Cuti Anda (2025-12-07 s/d 2025-12-11, annual) berubah menjadi: approved', NULL, '2025-12-07 14:56:39', '2025-12-07 14:56:39'),
(10, 13, 5, 'dsfsdfsdfsdfsdsdf', NULL, '2025-12-18 08:25:39', '2025-12-18 08:25:39'),
(11, 13, 4, 'sgdjhsfsdf', NULL, '2025-12-18 08:44:55', '2025-12-18 08:44:55'),
(12, 13, 7, 'hai', NULL, '2025-12-18 08:45:11', '2025-12-18 08:45:11'),
(13, 13, 1, 'Status Izin/Cuti Anda (2025-12-26 s/d 2025-12-27, annual) berubah menjadi: approved', NULL, '2025-12-23 06:59:03', '2025-12-23 06:59:03'),
(14, 13, 1, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(15, 13, 2, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(16, 13, 3, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(17, 13, 4, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(18, 13, 5, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(19, 13, 6, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(20, 13, 7, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(21, 13, 8, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(22, 13, 9, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(23, 13, 10, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(24, 13, 11, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(25, 13, 12, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(26, 13, 13, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', '2025-12-27 02:59:05', '2025-12-23 07:00:34', '2025-12-27 02:59:05'),
(27, 13, 14, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(28, 13, 15, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(29, 13, 16, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(30, 13, 17, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(31, 13, 18, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', '2025-12-27 05:27:52', '2025-12-23 07:00:34', '2025-12-27 05:27:52'),
(32, 13, 19, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(33, 13, 20, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(34, 13, 21, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(35, 13, 22, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(36, 13, 23, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(37, 13, 24, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(38, 13, 25, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(39, 13, 26, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(40, 13, 27, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(41, 13, 28, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(42, 13, 29, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(43, 13, 30, 'Pemberitahuan: Libur Nasional pada 2025-12-31 - tahun baru', NULL, '2025-12-23 07:00:34', '2025-12-23 07:00:34'),
(44, 13, 16, 'sdfsf', NULL, '2025-12-27 05:07:41', '2025-12-27 05:07:41'),
(45, 13, 18, 'Status Izin/Cuti Anda (2025-12-29 s/d 2025-12-31, annual) berubah menjadi: approved', '2025-12-29 03:17:25', '2025-12-29 03:16:51', '2025-12-29 03:17:25'),
(46, 13, 18, 'Status Izin/Cuti Anda (2025-12-29 s/d 2025-12-31, annual) berubah menjadi: approved', '2025-12-29 03:17:25', '2025-12-29 03:16:54', '2025-12-29 03:17:25'),
(47, 13, 18, 'Status Izin/Cuti Anda (2025-12-29 s/d 2025-12-31, annual) berubah menjadi: approved', '2025-12-29 03:17:25', '2025-12-29 03:16:58', '2025-12-29 03:17:25'),
(48, 13, 13, 'Status Izin/Cuti Anda (2025-12-29 s/d 2025-12-31, annual) berubah menjadi: approved', '2025-12-29 03:20:34', '2025-12-29 03:19:51', '2025-12-29 03:20:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '0001_01_01_000003_create_messages_table', 1),
(5, '0001_01_01_000004_create_tasks_table', 1),
(6, '0001_01_01_000005_create_divisions_table', 1),
(7, '0001_01_01_000006_create_employees_table', 1),
(8, '2025_10_11_140727_add_master_id_to_employees_table', 1),
(9, '2025_10_11_145047_add_employee_id_to_users_table', 1),
(10, '2025_10_11_145344_drop_user_id_from_employees_table', 1),
(11, '2025_10_11_145507_drop_master_id_from_employees_table', 1),
(12, '2025_10_12_075103_add_master_id_to_employees_table', 1),
(13, '2025_10_13_044528_create_attendances_table', 1),
(14, '2025_10_13_053417_add_late_and_approval_fields_to_attendances_table', 1),
(15, '2025_10_13_053715_add_late_and_approval_fields_to_attendances_table', 1),
(16, '2025_10_15_102033_rename_tasks_table_to_employee_tasks', 1),
(17, '2025_10_15_102418_create_master_tasks_table', 1),
(18, '2025_10_15_213415_add_file_paths_to_master_tasks_table', 1),
(19, '2025_10_15_213533_add_file_paths_to_employee_tasks_table', 1),
(20, '2025_10_15_234811_create_overtime_requests_table', 1),
(21, '2025_10_15_234842_create_overtime_approvals_table', 1),
(22, '2025_10_18_081004_add_karyawan_id_to_users_table', 1),
(23, '2025_10_18_081016_add_karyawan_id_to_users_table', 1),
(24, '2025_10_18_132017_create_locations_table', 1),
(25, '2025_10_18_132105_create_location_settings_table', 1),
(26, '2025_10_18_132139_add_location_id_to_users_table', 1),
(27, '2025_10_18_132223_add_location_id_to_attendances_table', 1),
(28, '2025_10_18_132953_create_shifts_table', 1),
(29, '2025_10_18_134008_add_shift_id_to_attendances_table', 1),
(30, '2025_10_18_210221_add_geo_fields_to_locations_table', 1),
(31, '2025_10_18_230000_add_shift_enabled_to_locations_table', 1),
(32, '2025_10_18_232038_add_location_id_and_shift_id_to_employees_table', 1),
(33, '2025_10_19_092648_drop_shift_id_from_employees_table', 1),
(34, '2025_10_19_092701_drop_shift_id_from_employees_table', 1),
(35, '2025_10_19_105622_modify_shifts_table_remove_columns_add_day', 1),
(36, '2025_10_19_111759_modify_shifts_and_locations_for_flexible_scheduling', 1),
(37, '2025_10_19_111855_create_location_shifts_table', 1),
(38, '2025_10_19_124304_modify_shifts_for_flexible_system', 1),
(39, '2025_10_19_124338_modify_shifts_for_flexible_system', 1),
(40, '2025_10_19_130803_modify_shifts_for_flexible_system', 1),
(41, '2025_10_19_130808_modify_shifts_for_flexible_system', 1),
(42, '2025_10_19_130840_modify_shifts_for_flexible_system', 1),
(43, '2025_10_19_191901_remove_schedule_fields_from_locations_table', 1),
(44, '2025_10_19_193106_drop_location_shifts_table', 1),
(45, '2025_10_19_193935_add_schedule_fields_to_employees_table', 1),
(46, '2025_10_19_194659_create_employee_shift_schedules_table', 1),
(47, '2025_10_20_000000_modify_shifts_for_master_flexible_system', 1),
(48, '2025_10_20_140239_create_permission_tables', 1),
(49, '2025_10_21_130217_create_location_change_requests_table', 1),
(50, '2025_10_21_200000_migrate_users_role_to_spatie', 1),
(51, '2025_10_21_210000_recreate_location_shifts_table', 1),
(52, '2025_10_23_000001_add_branding_fields_to_locations_table', 1),
(53, '2025_10_23_000100_create_shift_assignments_table', 1),
(54, '2025_10_23_000200_add_week5_fields_to_shifts_and_assignments', 1),
(55, '2025_10_23_010000_fix_sqlite_shifts_index_and_column', 1),
(56, '2025_10_27_000001_create_holidays_table', 2),
(57, '2025_10_27_000002_create_weekly_offs_table', 2),
(58, '2025_10_27_000003_create_employee_leaves_table', 2),
(59, '2025_10_27_120000_create_employee_absences_table', 2),
(60, '2025_10_29_000001_add_is_default_to_locations_table', 3),
(61, '2025_10_31_132539_add_two_factor_fields_to_users_table', 3),
(62, '2025_11_07_093228_add_tanggal_masuk_kerja_to_employees_table', 3),
(63, '2025_11_08_092115_add_is_permanent_to_location_change_requests_table', 4),
(64, '2025_11_07_150000_create_reports_table', 5),
(65, '2025_11_07_150010_create_report_attachments_and_approvals_table', 5),
(66, '2025_11_21_160000_add_profile_photo_to_users_table', 5),
(67, '2025_12_03_000000_add_progress_and_task_progress_updates', 6),
(68, '2025_12_06_000001_add_category_and_time_slots_to_shifts_and_location_shifts', 7),
(69, '2025_12_06_000002_add_is_default_to_location_shifts', 8),
(70, '2025_12_06_010000_add_location_refs_to_shift_assignments', 9),
(71, '2025_12_07_000000_create_weekly_rosters', 10),
(72, '2025_12_07_120000_add_shift_assignment_id_to_weekly_roster_entries', 11),
(73, '2025_12_08_000000_add_shift_assignment_id_to_attendances', 11),
(74, '2025_12_08_000050_backfill_roster_assignment_links', 11),
(75, '2025_12_09_000100_alter_task_progress_updates_approval_level', 12),
(76, '2025_12_09_144756_add_duration_minutes_to_tasks_and_create_task_slots', 13),
(77, '2025_12_09_144802_create_task_slot_attachments_table', 13),
(78, '2025_12_09_144809_create_task_slot_history_table', 13),
(79, '2025_12_09_144815_create_employee_work_recaps_table', 13),
(80, '2025_12_10_000001_create_location_work_targets_table', 14),
(81, '2025_12_10_000000_add_task_creation_approvals', 15),
(82, '2025_12_10_200000_alter_task_slot_attachments_path_to_text', 16),
(83, '2025_12_30_000002_update_task_slots_percentage_to_decimal', 17),
(84, '2025_12_15_000000_create_notifications_table', 18),
(85, '2025_12_22_000001_add_attendance_device_and_photo_fields', 19),
(86, '2025_12_22_000002_create_employee_leave_balances_table', 20),
(87, '2025_12_22_000003_add_level_to_overtime_approvals_table', 21),
(88, '2025_12_23_000001_create_audit_logs_table', 22),
(89, '2026_03_05_000001_create_jobdesks_table', 23),
(90, '2026_03_05_000002_create_task_catalogs_table', 23),
(91, '2026_03_05_000003_create_employee_jobdesk_assignments_table', 23),
(92, '2026_03_05_000004_add_task_catalog_id_to_employee_tasks_table', 23),
(93, '2026_03_05_000005_create_jobdesk_output_targets_table', 23),
(94, '2026_03_05_000006_add_min_attendance_minutes_to_jobdesks_table', 23),
(95, '2026_03_05_000007_create_approval_rules_table', 23),
(96, '2026_03_05_000008_create_employee_position_histories_table', 23),
(97, '2026_03_05_000009_create_employee_transfers_table', 23),
(98, '2026_03_05_000010_create_employee_contracts_table', 23);

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(2, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 4),
(2, 'App\\Models\\User', 5),
(2, 'App\\Models\\User', 6),
(2, 'App\\Models\\User', 7),
(2, 'App\\Models\\User', 8),
(2, 'App\\Models\\User', 9),
(2, 'App\\Models\\User', 10),
(2, 'App\\Models\\User', 11),
(2, 'App\\Models\\User', 12),
(1, 'App\\Models\\User', 13),
(1, 'App\\Models\\User', 14),
(1, 'App\\Models\\User', 15),
(3, 'App\\Models\\User', 16),
(3, 'App\\Models\\User', 17),
(2, 'App\\Models\\User', 18),
(2, 'App\\Models\\User', 19),
(2, 'App\\Models\\User', 20),
(2, 'App\\Models\\User', 21),
(2, 'App\\Models\\User', 22),
(2, 'App\\Models\\User', 23),
(2, 'App\\Models\\User', 24),
(2, 'App\\Models\\User', 25),
(2, 'App\\Models\\User', 26),
(2, 'App\\Models\\User', 27),
(2, 'App\\Models\\User', 28),
(2, 'App\\Models\\User', 29),
(2, 'App\\Models\\User', 30),
(2, 'App\\Models\\User', 31);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('02fdcea2-4a94-4c4d-b7ae-d82a14157836', 'App\\Notifications\\TaskSlotSubmittedNotification', 'App\\Models\\User', 13, '{\"type\":\"task_slot_submitted\",\"task_id\":31,\"task_title\":\"ghjghjfghfhfgh\",\"slot_id\":74,\"slot_name\":\"yuiyuiyui\",\"submitted_by_role\":\"karyawan\",\"message\":\"Bukti slot \'yuiyuiyui\' dikirim (karyawan)\"}', '2025-12-27 06:15:22', '2025-12-27 05:32:58', '2025-12-27 06:15:22'),
('064a03c4-5490-45b6-bd7d-745836a8f3c2', 'App\\Notifications\\TaskSlotApprovalNotification', 'App\\Models\\User', 26, '{\"type\":\"task_slot_approval\",\"task_id\":12,\"task_title\":\"sdfsdfs\",\"slot_id\":7,\"slot_name\":\"asda\",\"status\":\"approved\",\"reason\":null}', '2025-12-15 09:50:40', '2025-12-15 09:31:59', '2025-12-15 09:50:40'),
('06e06c29-4fcf-4ae9-90cb-cb54956206d2', 'App\\Notifications\\TaskSlotSubmittedNotification', 'App\\Models\\User', 13, '{\"type\":\"task_slot_submitted\",\"task_id\":31,\"task_title\":\"ghjghjfghfhfgh\",\"slot_id\":74,\"slot_name\":\"yuiyuiyui\",\"submitted_by_role\":\"karyawan\",\"message\":\"Bukti slot \'yuiyuiyui\' dikirim (karyawan)\"}', '2025-12-27 06:15:22', '2025-12-27 05:33:47', '2025-12-27 06:15:22'),
('0c34a96e-475c-416b-bc36-d0d0d5e047de', 'App\\Notifications\\TaskApprovalNotification', 'App\\Models\\User', 1, '{\"type\":\"task_approval\",\"task_id\":29,\"title\":\"mencari vendor untuk produksi box\",\"status\":\"approved\",\"message\":\"Tugas Anda disetujui\",\"approval_note\":null}', NULL, '2025-12-23 06:51:05', '2025-12-23 06:51:05'),
('1b047c23-507f-4a4d-97bf-542d078fa360', 'App\\Notifications\\TaskApprovalNotification', 'App\\Models\\User', 18, '{\"type\":\"task_approval\",\"task_id\":31,\"title\":\"ghjghj\",\"status\":\"rejected\",\"message\":\"Tugas Anda ditolak\",\"approval_note\":\"fghdfgdgdfgdfgdfgdfgdgdfg\"}', '2025-12-27 05:27:51', '2025-12-27 05:25:18', '2025-12-27 05:27:51'),
('235d0d9a-83dd-4f73-8002-a073855d2cb8', 'App\\Notifications\\TaskSlotSubmittedNotification', 'App\\Models\\User', 13, '{\"type\":\"task_slot_submitted\",\"task_id\":29,\"task_title\":\"mencari vendor untuk produksi box\",\"slot_id\":69,\"slot_name\":\"riset\",\"submitted_by_role\":\"karyawan\",\"message\":\"Bukti slot \'riset\' dikirim (karyawan)\"}', '2025-12-27 02:59:04', '2025-12-23 06:55:11', '2025-12-27 02:59:04'),
('2680291e-4652-4446-affc-34f526af062c', 'App\\Notifications\\TaskSlotSubmittedNotification', 'App\\Models\\User', 14, '{\"type\":\"task_slot_submitted\",\"task_id\":29,\"task_title\":\"mencari vendor untuk produksi box\",\"slot_id\":69,\"slot_name\":\"riset\",\"submitted_by_role\":\"karyawan\",\"message\":\"Bukti slot \'riset\' dikirim (karyawan)\"}', NULL, '2025-12-23 06:55:11', '2025-12-23 06:55:11'),
('2831cd8d-ec5d-45f7-910a-48da1cae410a', 'App\\Notifications\\TaskSlotSubmittedNotification', 'App\\Models\\User', 13, '{\"type\":\"task_slot_submitted\",\"task_id\":28,\"task_title\":\"interview user\",\"slot_id\":68,\"slot_name\":\"rouf\",\"submitted_by_role\":\"karyawan\",\"message\":\"Bukti slot \'rouf\' dikirim (karyawan)\"}', '2025-12-27 02:59:04', '2025-12-23 06:56:30', '2025-12-27 02:59:04'),
('4e4d1242-119c-4ab6-b2e8-93f926cb0e14', 'App\\Notifications\\TaskSlotSubmittedNotification', 'App\\Models\\User', 15, '{\"type\":\"task_slot_submitted\",\"task_id\":28,\"task_title\":\"interview user\",\"slot_id\":68,\"slot_name\":\"rouf\",\"submitted_by_role\":\"karyawan\",\"message\":\"Bukti slot \'rouf\' dikirim (karyawan)\"}', NULL, '2025-12-23 06:56:30', '2025-12-23 06:56:30'),
('63f131f1-cb3c-4ab3-a9f0-3bf8524d651d', 'App\\Notifications\\TaskApprovalNotification', 'App\\Models\\User', 26, '{\"type\":\"task_approval\",\"task_id\":30,\"title\":\"hjfhfhfgh\",\"status\":\"approved\",\"message\":\"Tugas Anda disetujui\",\"approval_note\":null}', NULL, '2025-12-27 05:16:23', '2025-12-27 05:16:23'),
('66a34b35-5213-491b-8a64-d50cae6a1f27', 'App\\Notifications\\TaskSlotApprovalNotification', 'App\\Models\\User', 18, '{\"type\":\"task_slot_approval\",\"task_id\":31,\"task_title\":\"ghjghjfghfhfgh\",\"slot_id\":74,\"slot_name\":\"yuiyuiyui\",\"status\":\"rejected\",\"reason\":\"gdgfgdfgdfg\"}', '2025-12-29 03:16:08', '2025-12-27 05:33:30', '2025-12-29 03:16:08'),
('7abbc186-b15e-48f1-9ed8-c942d49f1f58', 'App\\Notifications\\TaskApprovalNotification', 'App\\Models\\User', 1, '{\"type\":\"task_approval\",\"task_id\":28,\"title\":\"interview user\",\"status\":\"approved\",\"message\":\"Tugas Anda disetujui\",\"approval_note\":null}', NULL, '2025-12-23 06:51:11', '2025-12-23 06:51:11'),
('840b22e9-fd70-4b6b-98c5-03aafc45acc5', 'App\\Notifications\\TaskSlotSubmittedNotification', 'App\\Models\\User', 14, '{\"type\":\"task_slot_submitted\",\"task_id\":28,\"task_title\":\"interview user\",\"slot_id\":68,\"slot_name\":\"rouf\",\"submitted_by_role\":\"karyawan\",\"message\":\"Bukti slot \'rouf\' dikirim (karyawan)\"}', NULL, '2025-12-23 06:56:30', '2025-12-23 06:56:30'),
('918ad368-71c2-4be3-b32e-3c4cf9437322', 'App\\Notifications\\TaskApprovalNotification', 'App\\Models\\User', 1, '{\"type\":\"task_approval\",\"task_id\":27,\"title\":\"sdfsfsfsdf\",\"status\":\"approved\",\"message\":\"Tugas Anda disetujui\",\"approval_note\":null}', NULL, '2025-12-23 06:19:41', '2025-12-23 06:19:41'),
('a56035fb-a873-45b9-8fd3-0aa7a37a09fd', 'App\\Notifications\\TaskSlotSubmittedNotification', 'App\\Models\\User', 13, '{\"type\":\"task_slot_submitted\",\"task_id\":12,\"task_title\":\"sdfsdfs\",\"slot_id\":8,\"slot_name\":\"sasdasd\",\"submitted_by_role\":\"karyawan\",\"message\":\"Bukti slot \'sasdasd\' dikirim (karyawan)\"}', '2025-12-16 01:58:50', '2025-12-15 08:51:44', '2025-12-16 01:58:50'),
('a7534665-61e8-4094-8483-315b8d183a0a', 'App\\Notifications\\TaskSlotSubmittedNotification', 'App\\Models\\User', 14, '{\"type\":\"task_slot_submitted\",\"task_id\":31,\"task_title\":\"ghjghjfghfhfgh\",\"slot_id\":74,\"slot_name\":\"yuiyuiyui\",\"submitted_by_role\":\"karyawan\",\"message\":\"Bukti slot \'yuiyuiyui\' dikirim (karyawan)\"}', NULL, '2025-12-27 05:33:47', '2025-12-27 05:33:47'),
('b51551c4-80a7-47ad-b89c-97bef5594486', 'App\\Notifications\\TaskSlotSubmittedNotification', 'App\\Models\\User', 15, '{\"type\":\"task_slot_submitted\",\"task_id\":12,\"task_title\":\"sdfsdfs\",\"slot_id\":8,\"slot_name\":\"sasdasd\",\"submitted_by_role\":\"karyawan\",\"message\":\"Bukti slot \'sasdasd\' dikirim (karyawan)\"}', NULL, '2025-12-15 08:51:44', '2025-12-15 08:51:44'),
('c189d741-5dc2-4cb8-a78f-1e7115701d41', 'App\\Notifications\\TaskSlotSubmittedNotification', 'App\\Models\\User', 14, '{\"type\":\"task_slot_submitted\",\"task_id\":31,\"task_title\":\"ghjghjfghfhfgh\",\"slot_id\":74,\"slot_name\":\"yuiyuiyui\",\"submitted_by_role\":\"karyawan\",\"message\":\"Bukti slot \'yuiyuiyui\' dikirim (karyawan)\"}', NULL, '2025-12-27 05:32:58', '2025-12-27 05:32:58'),
('c4e346ca-7255-4020-9ddd-b144d6425233', 'App\\Notifications\\TaskSlotApprovalNotification', 'App\\Models\\User', 18, '{\"type\":\"task_slot_approval\",\"task_id\":31,\"task_title\":\"ghjghjfghfhfgh\",\"slot_id\":74,\"slot_name\":\"yuiyuiyui\",\"status\":\"approved\",\"reason\":null}', '2025-12-29 03:16:08', '2025-12-27 05:34:01', '2025-12-29 03:16:08'),
('c5507a80-c4e7-4c75-8726-fc2854e4278d', 'App\\Notifications\\TaskApprovalNotification', 'App\\Models\\User', 18, '{\"type\":\"task_approval\",\"task_id\":31,\"title\":\"ghjghjfghfhfgh\",\"status\":\"approved\",\"message\":\"Tugas Anda disetujui\",\"approval_note\":null}', '2025-12-27 05:27:51', '2025-12-27 05:26:28', '2025-12-27 05:27:51'),
('caecd128-404e-4685-a6c9-9ec341a1a954', 'App\\Notifications\\TaskSlotSubmittedNotification', 'App\\Models\\User', 14, '{\"type\":\"task_slot_submitted\",\"task_id\":12,\"task_title\":\"sdfsdfs\",\"slot_id\":8,\"slot_name\":\"sasdasd\",\"submitted_by_role\":\"karyawan\",\"message\":\"Bukti slot \'sasdasd\' dikirim (karyawan)\"}', NULL, '2025-12-15 08:51:44', '2025-12-15 08:51:44'),
('d2a6213f-4030-4ede-8c63-2371420130ea', 'App\\Notifications\\TaskSlotSubmittedNotification', 'App\\Models\\User', 15, '{\"type\":\"task_slot_submitted\",\"task_id\":31,\"task_title\":\"ghjghjfghfhfgh\",\"slot_id\":74,\"slot_name\":\"yuiyuiyui\",\"submitted_by_role\":\"karyawan\",\"message\":\"Bukti slot \'yuiyuiyui\' dikirim (karyawan)\"}', NULL, '2025-12-27 05:32:58', '2025-12-27 05:32:58'),
('e8e44fe0-ef47-4736-a7f2-457851094612', 'App\\Notifications\\TaskSlotSubmittedNotification', 'App\\Models\\User', 15, '{\"type\":\"task_slot_submitted\",\"task_id\":31,\"task_title\":\"ghjghjfghfhfgh\",\"slot_id\":74,\"slot_name\":\"yuiyuiyui\",\"submitted_by_role\":\"karyawan\",\"message\":\"Bukti slot \'yuiyuiyui\' dikirim (karyawan)\"}', NULL, '2025-12-27 05:33:47', '2025-12-27 05:33:47'),
('eb2097d2-b7a8-40f0-b780-b00cf9786816', 'App\\Notifications\\TaskSlotSubmittedNotification', 'App\\Models\\User', 15, '{\"type\":\"task_slot_submitted\",\"task_id\":29,\"task_title\":\"mencari vendor untuk produksi box\",\"slot_id\":69,\"slot_name\":\"riset\",\"submitted_by_role\":\"karyawan\",\"message\":\"Bukti slot \'riset\' dikirim (karyawan)\"}', NULL, '2025-12-23 06:55:11', '2025-12-23 06:55:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `overtime_approvals`
--

CREATE TABLE `overtime_approvals` (
  `id` bigint UNSIGNED NOT NULL,
  `overtime_request_id` bigint UNSIGNED NOT NULL,
  `master_id` bigint UNSIGNED NOT NULL,
  `level` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `overtime_approvals`
--

INSERT INTO `overtime_approvals` (`id`, `overtime_request_id`, `master_id`, `level`, `status`, `approved_at`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 13, 1, 'approved', '2025-12-03 02:31:26', 'sadfsdfsdfsf', '2025-12-03 02:29:12', '2025-12-03 02:31:26'),
(2, 1, 14, 1, 'approved', '2025-12-07 12:40:29', NULL, '2025-12-03 02:29:12', '2025-12-07 12:40:29'),
(3, 2, 13, 1, 'rejected', '2025-12-23 03:39:30', 'ghfghdfghdfghdfgh', '2025-12-23 03:36:56', '2025-12-23 03:39:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `overtime_requests`
--

CREATE TABLE `overtime_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `duration_hours` decimal(4,2) NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `selected_masters` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `overtime_requests`
--

INSERT INTO `overtime_requests` (`id`, `user_id`, `date`, `start_time`, `end_time`, `duration_hours`, `reason`, `status`, `selected_masters`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-12-03', '11:00:00', '13:00:00', 2.00, 'rdgtsdrtderfsaertergft', 'approved', '[13, 14]', '2025-12-03 02:29:12', '2025-12-07 12:40:29'),
(2, 2, '2025-12-23', '10:00:00', '11:00:00', 1.00, 'ghhfghdfghfdghdfgh', 'rejected', '[13]', '2025-12-23 03:36:56', '2025-12-23 03:38:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `reports`
--

CREATE TABLE `reports` (
  `id` bigint UNSIGNED NOT NULL,
  `ticket_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reporter_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `assigned_admin_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `finalized_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `reports`
--

INSERT INTO `reports` (`id`, `ticket_number`, `reporter_id`, `location_id`, `assigned_admin_id`, `title`, `description`, `status`, `finalized_at`, `created_at`, `updated_at`) VALUES
(1, 'RPT-20251203-RSQFN', 13, 4, NULL, 'dfghdg', 'dfgsdfsfdsdfsdfsdf', 'approved', '2025-12-03 02:26:05', '2025-12-03 02:25:44', '2025-12-03 02:26:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `report_approvals`
--

CREATE TABLE `report_approvals` (
  `id` bigint UNSIGNED NOT NULL,
  `report_id` bigint UNSIGNED NOT NULL,
  `approver_id` bigint UNSIGNED DEFAULT NULL,
  `approver_role` enum('admin_lokasi','super_admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `step_order` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `decided_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `report_approvals`
--

INSERT INTO `report_approvals` (`id`, `report_id`, `approver_id`, `approver_role`, `step_order`, `status`, `decided_at`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 13, 'super_admin', 1, 'approved', '2025-12-03 02:26:05', NULL, '2025-12-03 02:25:45', '2025-12-03 02:26:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `report_attachments`
--

CREATE TABLE `report_attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `report_id` bigint UNSIGNED NOT NULL,
  `uploaded_by` bigint UNSIGNED DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `report_attachments`
--

INSERT INTO `report_attachments` (`id`, `report_id`, `uploaded_by`, `file_path`, `original_name`, `mime_type`, `file_size`, `created_at`, `updated_at`) VALUES
(1, 1, 13, 'reports/3VEX2dVneE05Y0h3BH3ENXJMwTfbyHN0V12nPQnd.jpg', '41.jpg', 'image/jpeg', 315738, '2025-12-03 02:25:45', '2025-12-03 02:25:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'web', '2025-10-23 09:47:40', '2025-10-23 09:47:40'),
(2, 'Karyawan', 'web', '2025-10-23 09:47:40', '2025-10-23 09:47:40'),
(3, 'Admin Lokasi', 'web', '2025-10-23 09:54:45', '2025-10-23 09:54:45'),
(4, 'HR', 'web', '2026-03-05 05:21:15', '2026-03-05 05:21:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('XMtYh8xgFFEubqry40FW8JTqjYDkOlXMCC0j19cZ', 13, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiOEd3VGxNcVpOTEk1S1VKcWd1aHlKNVZNZkR6TElCaWdkQlBNWjNJOCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8va2FudG9yYXBwLnRlc3QvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjEzO30=', 1775185552);

-- --------------------------------------------------------

--
-- Struktur dari tabel `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('office','non_office') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'office',
  `day` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shift_type` enum('multiple','single') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'single',
  `time_slots` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `break_minutes` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `shifts`
--

INSERT INTO `shifts` (`id`, `name`, `code`, `category`, `day`, `shift_type`, `time_slots`, `is_active`, `description`, `break_minutes`, `created_at`, `updated_at`) VALUES
(1, 'Office Standard Shift', 'OFFICE', 'office', NULL, 'single', '{\"end\": \"17:00\", \"start\": \"09:00\"}', 1, 'Standard office hours from 9 AM to 5 PM', 59, '2025-10-23 09:54:45', '2025-12-07 03:29:18'),
(2, 'Factory Multiple Shifts', 'FACTORY', 'non_office', NULL, 'multiple', '[{\"end\": \"14:00\", \"start\": \"06:00\"}, {\"end\": \"22:00\", \"start\": \"14:00\"}, {\"end\": \"06:00\", \"start\": \"22:00\"}]', 1, 'Factory shifts: morning, afternoon, and night', NULL, '2025-10-23 09:54:45', '2025-12-05 14:24:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `shift_assignments`
--

CREATE TABLE `shift_assignments` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `shift_id` bigint UNSIGNED NOT NULL,
  `location_shift_id` bigint UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `status` enum('scheduled','cancelled','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `handover_required` tinyint(1) NOT NULL DEFAULT '0',
  `handover_note` text COLLATE utf8mb4_unicode_ci,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `shift_assignments`
--

INSERT INTO `shift_assignments` (`id`, `user_id`, `location_id`, `shift_id`, `location_shift_id`, `date`, `status`, `handover_required`, `handover_note`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, NULL, '2025-10-23', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(2, 1, 1, 2, NULL, '2025-10-24', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(4, 1, 1, 1, NULL, '2025-10-26', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(5, 1, 1, 2, NULL, '2025-10-27', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(7, 1, 1, 1, NULL, '2025-10-29', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(8, 2, 1, 1, NULL, '2025-10-23', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(9, 2, 1, 2, NULL, '2025-10-24', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(11, 2, 1, 1, NULL, '2025-10-26', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(12, 2, 1, 2, NULL, '2025-10-27', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(14, 2, 1, 1, NULL, '2025-10-29', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(15, 4, 2, 1, NULL, '2025-10-23', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(16, 4, 2, 2, 4, '2025-10-24', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(18, 4, 2, 1, NULL, '2025-10-26', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(19, 4, 2, 2, 4, '2025-10-27', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(21, 4, 2, 1, NULL, '2025-10-29', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(22, 5, 2, 1, NULL, '2025-10-23', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(23, 5, 2, 2, 4, '2025-10-24', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(25, 5, 2, 1, NULL, '2025-10-26', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(26, 5, 2, 2, 4, '2025-10-27', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(28, 5, 2, 1, NULL, '2025-10-29', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(29, 7, 3, 1, NULL, '2025-10-23', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(30, 7, 3, 2, NULL, '2025-10-24', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(32, 7, 3, 1, NULL, '2025-10-26', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(33, 7, 3, 2, NULL, '2025-10-27', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(35, 7, 3, 1, NULL, '2025-10-29', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(36, 8, 3, 1, NULL, '2025-10-23', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(37, 8, 3, 2, NULL, '2025-10-24', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(39, 8, 3, 1, NULL, '2025-10-26', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(40, 8, 3, 2, NULL, '2025-10-27', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(42, 8, 3, 1, NULL, '2025-10-29', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(43, 10, 4, 1, NULL, '2025-10-23', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(44, 10, 4, 2, 10, '2025-10-24', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(46, 10, 4, 1, NULL, '2025-10-26', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(47, 10, 4, 2, 10, '2025-10-27', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(49, 10, 4, 1, NULL, '2025-10-29', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(50, 11, 4, 1, NULL, '2025-10-23', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(51, 11, 4, 2, 10, '2025-10-24', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(53, 11, 4, 1, NULL, '2025-10-26', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(54, 11, 4, 2, 10, '2025-10-27', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(56, 11, 4, 1, NULL, '2025-10-29', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(57, 3, 1, 1, NULL, '2025-10-23', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:00:26', '2025-10-23 10:00:26'),
(59, 20, 1, 1, NULL, '2025-10-23', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:00:26', '2025-10-23 10:00:26'),
(62, 18, 1, 1, NULL, '2025-10-24', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:00:26', '2025-10-23 10:00:26'),
(64, 22, 1, 1, NULL, '2025-10-24', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:00:26', '2025-10-23 10:00:26'),
(65, 3, 1, 1, NULL, '2025-10-25', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:00:26', '2025-10-23 10:00:26'),
(67, 20, 1, 1, NULL, '2025-10-25', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:00:26', '2025-10-23 10:00:26'),
(70, 18, 1, 1, NULL, '2025-10-26', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:00:26', '2025-10-23 10:00:26'),
(72, 22, 1, 1, NULL, '2025-10-26', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:00:26', '2025-10-23 10:00:26'),
(73, 3, 1, 1, NULL, '2025-10-27', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:00:26', '2025-10-23 10:00:26'),
(75, 20, 1, 1, NULL, '2025-10-27', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:00:26', '2025-10-23 10:00:26'),
(78, 18, 1, 1, NULL, '2025-10-28', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:00:26', '2025-10-23 10:00:26'),
(80, 22, 1, 1, NULL, '2025-10-28', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:00:26', '2025-10-23 10:00:26'),
(81, 3, 1, 1, NULL, '2025-10-29', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:00:26', '2025-10-23 10:00:26'),
(83, 20, 1, 1, NULL, '2025-10-29', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:00:26', '2025-10-23 10:00:26'),
(85, 6, 2, 2, 4, '2025-10-23', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:04:56', '2025-10-23 10:04:56'),
(86, 19, 2, 2, 4, '2025-10-23', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:04:56', '2025-10-23 10:04:56'),
(87, 6, 2, 2, 4, '2025-10-24', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:04:56', '2025-10-23 10:04:56'),
(88, 19, 2, 2, 4, '2025-10-24', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:04:56', '2025-10-23 10:04:56'),
(89, 6, 2, 2, 4, '2025-10-25', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:04:56', '2025-10-23 10:04:56'),
(90, 19, 2, 2, 4, '2025-10-25', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:04:56', '2025-10-23 10:04:56'),
(91, 6, 2, 2, 4, '2025-10-26', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:04:56', '2025-10-23 10:04:56'),
(92, 19, 2, 2, 4, '2025-10-26', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:04:56', '2025-10-23 10:04:56'),
(93, 6, 2, 2, 4, '2025-10-27', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:04:56', '2025-10-23 10:04:56'),
(94, 19, 2, 2, 4, '2025-10-27', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:04:56', '2025-10-23 10:04:56'),
(95, 6, 2, 2, 4, '2025-10-28', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:04:56', '2025-10-23 10:04:56'),
(96, 19, 2, 2, 4, '2025-10-28', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:04:56', '2025-10-23 10:04:56'),
(97, 6, 2, 2, 4, '2025-10-29', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:04:56', '2025-10-23 10:04:56'),
(98, 19, 2, 2, 4, '2025-10-29', 'scheduled', 0, NULL, 'Generated rotation', '2025-10-23 10:04:56', '2025-10-23 10:04:56'),
(99, 1, 1, 1, NULL, '2025-10-30', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-24 14:15:15', '2025-10-24 14:15:15'),
(100, 2, 1, 1, NULL, '2025-10-30', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-24 14:15:15', '2025-10-24 14:15:15'),
(101, 4, 2, 2, 4, '2025-10-30', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-24 14:15:15', '2025-10-24 14:15:15'),
(102, 5, 2, 2, 4, '2025-10-30', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-24 14:15:15', '2025-10-24 14:15:15'),
(103, 7, 3, 1, NULL, '2025-10-30', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-24 14:15:15', '2025-10-24 14:15:15'),
(104, 8, 3, 1, NULL, '2025-10-30', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-24 14:15:15', '2025-10-24 14:15:15'),
(105, 10, 4, 2, 10, '2025-10-30', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-24 14:15:15', '2025-10-24 14:15:15'),
(106, 11, 4, 2, 10, '2025-10-30', 'scheduled', 0, NULL, 'Seeded assignment', '2025-10-24 14:15:15', '2025-10-24 14:15:15'),
(107, 1, 1, 1, NULL, '2025-11-09', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(109, 1, 1, 1, NULL, '2025-11-11', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(111, 1, 1, 1, NULL, '2025-11-13', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(113, 1, 1, 1, NULL, '2025-11-15', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(114, 2, 1, 1, NULL, '2025-11-09', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(116, 2, 1, 1, NULL, '2025-11-11', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(118, 2, 1, 1, NULL, '2025-11-13', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(120, 2, 1, 1, NULL, '2025-11-15', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(121, 4, 2, 2, 4, '2025-11-09', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(122, 4, 2, 2, 4, '2025-11-10', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(123, 4, 2, 2, 4, '2025-11-11', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(124, 4, 2, 2, 4, '2025-11-12', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(125, 4, 2, 2, 4, '2025-11-13', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(126, 4, 2, 2, 4, '2025-11-14', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(127, 4, 2, 2, 4, '2025-11-15', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(128, 5, 2, 2, 4, '2025-11-09', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(129, 5, 2, 2, 4, '2025-11-10', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(130, 5, 2, 2, 4, '2025-11-11', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(131, 5, 2, 2, 4, '2025-11-12', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(132, 5, 2, 2, 4, '2025-11-13', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(133, 5, 2, 2, 4, '2025-11-14', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(134, 5, 2, 2, 4, '2025-11-15', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(135, 7, 3, 1, NULL, '2025-11-09', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(137, 7, 3, 1, NULL, '2025-11-11', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(139, 7, 3, 1, NULL, '2025-11-13', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(141, 7, 3, 1, NULL, '2025-11-15', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(142, 8, 3, 1, NULL, '2025-11-09', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(144, 8, 3, 1, NULL, '2025-11-11', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(146, 8, 3, 1, NULL, '2025-11-13', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(148, 8, 3, 1, NULL, '2025-11-15', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(149, 10, 4, 2, 10, '2025-11-09', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(150, 10, 4, 2, 10, '2025-11-10', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(151, 10, 4, 2, 10, '2025-11-11', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(152, 10, 4, 2, 10, '2025-11-12', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(153, 10, 4, 2, 10, '2025-11-13', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(154, 10, 4, 2, 10, '2025-11-14', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(155, 10, 4, 2, 10, '2025-11-15', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(156, 11, 4, 2, 10, '2025-11-09', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(157, 11, 4, 2, 10, '2025-11-10', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(158, 11, 4, 2, 10, '2025-11-11', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(159, 11, 4, 2, 10, '2025-11-12', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(160, 11, 4, 2, 10, '2025-11-13', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(161, 11, 4, 2, 10, '2025-11-14', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(162, 11, 4, 2, 10, '2025-11-15', 'scheduled', 0, NULL, 'Seeded assignment', '2025-11-09 04:57:29', '2025-11-09 04:57:29'),
(164, 10, 4, 2, 10, '2025-12-07', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:06', '2025-12-07 10:04:49'),
(165, 11, 4, 2, 10, '2025-12-07', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:06', '2025-12-07 10:04:49'),
(166, 12, 4, 2, 10, '2025-12-07', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:06', '2025-12-07 10:04:49'),
(167, 21, 4, 2, 10, '2025-12-07', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(168, 23, 4, 2, 10, '2025-12-07', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(169, 24, 4, 2, 10, '2025-12-07', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(170, 25, 4, 2, 10, '2025-12-07', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(171, 10, 4, 2, 10, '2025-12-08', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:06', '2025-12-08 06:41:44'),
(172, 11, 4, 2, 10, '2025-12-08', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:06', '2025-12-08 06:41:44'),
(173, 12, 4, 2, 10, '2025-12-08', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:06', '2025-12-08 06:41:44'),
(174, 21, 4, 2, 10, '2025-12-08', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(175, 23, 4, 2, 10, '2025-12-08', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(176, 24, 4, 2, 10, '2025-12-08', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(177, 25, 4, 2, 10, '2025-12-08', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(178, 10, 4, 2, 10, '2025-12-09', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(179, 11, 4, 2, 10, '2025-12-09', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(180, 12, 4, 2, 10, '2025-12-09', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(181, 21, 4, 2, 10, '2025-12-09', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:06', '2025-12-08 06:41:45'),
(182, 23, 4, 2, 10, '2025-12-09', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:06', '2025-12-08 06:41:45'),
(183, 24, 4, 2, 10, '2025-12-09', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:06', '2025-12-08 06:41:45'),
(184, 25, 4, 2, 10, '2025-12-09', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(185, 10, 4, 2, 10, '2025-12-10', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:06', '2025-12-08 06:41:45'),
(186, 11, 4, 2, 10, '2025-12-10', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(187, 12, 4, 2, 10, '2025-12-10', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:06', '2025-12-08 06:41:45'),
(188, 21, 4, 2, 10, '2025-12-10', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(189, 23, 4, 2, 10, '2025-12-10', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(190, 24, 4, 2, 10, '2025-12-10', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(191, 25, 4, 2, 10, '2025-12-10', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:06', '2025-12-08 06:41:45'),
(192, 10, 4, 2, 10, '2025-12-11', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(193, 11, 4, 2, 10, '2025-12-11', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(194, 12, 4, 2, 10, '2025-12-11', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(195, 21, 4, 2, 10, '2025-12-11', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:06', '2025-12-07 02:11:06'),
(196, 23, 4, 2, 10, '2025-12-11', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:06', '2025-12-08 06:41:45'),
(197, 24, 4, 2, 10, '2025-12-11', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:06', '2025-12-08 06:41:45'),
(198, 25, 4, 2, 10, '2025-12-11', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:06', '2025-12-08 06:41:45'),
(199, 10, 4, 2, 10, '2025-12-12', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:06', '2025-12-08 06:41:45'),
(200, 11, 4, 2, 10, '2025-12-12', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 06:41:45'),
(201, 12, 4, 2, 10, '2025-12-12', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(202, 21, 4, 2, 10, '2025-12-12', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(203, 23, 4, 2, 10, '2025-12-12', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(204, 24, 4, 2, 10, '2025-12-12', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(205, 25, 4, 2, 10, '2025-12-12', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(206, 10, 4, 2, 10, '2025-12-13', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(207, 11, 4, 2, 10, '2025-12-13', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(208, 12, 4, 2, 10, '2025-12-13', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(209, 21, 4, 2, 10, '2025-12-13', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 06:41:45'),
(210, 23, 4, 2, 10, '2025-12-13', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(211, 24, 4, 2, 10, '2025-12-13', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 06:41:45'),
(212, 25, 4, 2, 10, '2025-12-13', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(213, 10, 4, 2, 10, '2025-12-14', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(214, 11, 4, 2, 10, '2025-12-14', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(215, 12, 4, 2, 10, '2025-12-14', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 06:41:45'),
(216, 21, 4, 2, 10, '2025-12-14', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(217, 23, 4, 2, 10, '2025-12-14', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 06:41:45'),
(218, 24, 4, 2, 10, '2025-12-14', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(219, 25, 4, 2, 10, '2025-12-14', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 06:41:45'),
(220, 10, 4, 2, 10, '2025-12-15', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 07:16:01'),
(221, 11, 4, 2, 10, '2025-12-15', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 07:16:01'),
(222, 12, 4, 2, 10, '2025-12-15', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 07:16:01'),
(223, 21, 4, 2, 10, '2025-12-15', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 07:16:01'),
(224, 23, 4, 2, 10, '2025-12-15', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 07:16:01'),
(225, 24, 4, 2, 10, '2025-12-15', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(226, 25, 4, 2, 10, '2025-12-15', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 07:16:01'),
(227, 10, 4, 2, 10, '2025-12-16', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(228, 11, 4, 2, 10, '2025-12-16', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 07:16:01'),
(229, 12, 4, 2, 10, '2025-12-16', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 07:16:01'),
(230, 21, 4, 2, 10, '2025-12-16', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 07:16:01'),
(231, 23, 4, 2, 10, '2025-12-16', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 07:16:01'),
(232, 24, 4, 2, 10, '2025-12-16', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 07:16:01'),
(233, 25, 4, 2, 10, '2025-12-16', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(234, 10, 4, 2, 10, '2025-12-17', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 07:16:02'),
(235, 11, 4, 2, 10, '2025-12-17', 'scheduled', 0, NULL, 'Generated rotation', '2025-12-07 02:11:07', '2025-12-07 02:11:07'),
(236, 12, 4, 2, 10, '2025-12-17', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 07:16:02'),
(237, 21, 4, 2, 10, '2025-12-17', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 07:16:02'),
(238, 23, 4, 2, 10, '2025-12-17', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 07:16:02'),
(239, 24, 4, 2, 10, '2025-12-17', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 07:16:02'),
(240, 25, 4, 2, 10, '2025-12-17', 'scheduled', 0, NULL, NULL, '2025-12-07 02:11:07', '2025-12-08 07:16:02'),
(241, 10, 4, 2, 10, '2025-12-01', 'scheduled', 0, NULL, NULL, '2025-12-07 06:24:07', '2025-12-07 06:24:07'),
(242, 11, 4, 2, 10, '2025-12-01', 'scheduled', 0, NULL, NULL, '2025-12-07 06:24:07', '2025-12-07 06:24:07'),
(243, 12, 4, 2, 10, '2025-12-01', 'scheduled', 0, NULL, NULL, '2025-12-07 06:24:07', '2025-12-07 06:24:07'),
(244, 21, 4, 2, 10, '2025-12-02', 'scheduled', 0, NULL, NULL, '2025-12-07 06:24:07', '2025-12-07 06:24:07'),
(245, 23, 4, 2, 10, '2025-12-02', 'scheduled', 0, NULL, NULL, '2025-12-07 06:24:07', '2025-12-07 06:24:07'),
(246, 24, 4, 2, 10, '2025-12-02', 'scheduled', 0, NULL, NULL, '2025-12-07 06:24:07', '2025-12-07 06:24:07'),
(247, 25, 4, 2, 10, '2025-12-03', 'scheduled', 0, NULL, NULL, '2025-12-07 06:24:07', '2025-12-07 06:24:07'),
(248, 10, 4, 2, 10, '2025-12-03', 'scheduled', 0, NULL, NULL, '2025-12-07 06:24:07', '2025-12-07 06:24:07'),
(249, 11, 4, 2, 10, '2025-12-03', 'scheduled', 0, NULL, NULL, '2025-12-07 06:24:07', '2025-12-07 06:24:07'),
(250, 4, 2, 2, 4, '2025-12-01', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:44', '2025-12-07 06:30:44'),
(251, 5, 2, 2, 4, '2025-12-01', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:44', '2025-12-07 06:30:44'),
(252, 6, 2, 2, 4, '2025-12-01', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:44', '2025-12-07 06:30:44'),
(253, 19, 2, 2, 4, '2025-12-01', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:44', '2025-12-07 06:30:44'),
(254, 6, 2, 2, 4, '2025-12-02', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:44', '2025-12-07 06:30:44'),
(255, 19, 2, 2, 4, '2025-12-02', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:44', '2025-12-07 06:30:44'),
(256, 5, 2, 2, 4, '2025-12-02', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:44', '2025-12-07 06:30:44'),
(257, 19, 2, 2, 4, '2025-12-03', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:44', '2025-12-07 06:30:44'),
(258, 4, 2, 2, 4, '2025-12-03', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:44', '2025-12-07 06:30:44'),
(259, 6, 2, 2, 4, '2025-12-03', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:44', '2025-12-07 06:30:44'),
(260, 4, 2, 2, 4, '2025-12-04', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:45', '2025-12-07 06:30:45'),
(261, 5, 2, 2, 4, '2025-12-04', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:45', '2025-12-07 06:30:45'),
(262, 19, 2, 2, 4, '2025-12-04', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:45', '2025-12-07 06:30:45'),
(263, 5, 2, 2, 4, '2025-12-05', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:45', '2025-12-07 06:30:45'),
(264, 6, 2, 2, 4, '2025-12-05', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:45', '2025-12-07 06:30:45'),
(265, 4, 2, 2, 4, '2025-12-05', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:45', '2025-12-07 06:30:45'),
(266, 4, 2, 2, 4, '2025-12-06', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:45', '2025-12-07 06:30:45'),
(267, 5, 2, 2, 4, '2025-12-06', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:45', '2025-12-07 06:30:45'),
(268, 6, 2, 2, 4, '2025-12-06', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:45', '2025-12-07 06:30:45'),
(269, 19, 2, 2, 4, '2025-12-06', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:45', '2025-12-07 06:30:45'),
(270, 4, 2, 2, 4, '2025-12-07', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:45', '2025-12-07 06:30:45'),
(271, 5, 2, 2, 4, '2025-12-07', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:45', '2025-12-07 06:30:45'),
(272, 6, 2, 2, 4, '2025-12-07', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:45', '2025-12-07 06:30:45'),
(273, 19, 2, 2, 4, '2025-12-07', 'scheduled', 0, NULL, NULL, '2025-12-07 06:30:45', '2025-12-07 06:30:45'),
(274, 11, 4, 2, 10, '2025-12-02', 'scheduled', 0, NULL, NULL, '2025-12-07 10:04:49', '2025-12-07 10:04:49'),
(275, 12, 4, 2, 10, '2025-12-03', 'scheduled', 0, NULL, NULL, '2025-12-07 10:04:49', '2025-12-07 10:04:49'),
(276, 21, 4, 2, 10, '2025-12-03', 'scheduled', 0, NULL, NULL, '2025-12-07 10:04:49', '2025-12-07 10:04:49'),
(277, 23, 4, 2, 10, '2025-12-04', 'scheduled', 0, NULL, NULL, '2025-12-07 10:04:49', '2025-12-07 10:04:49'),
(278, 24, 4, 2, 10, '2025-12-04', 'scheduled', 0, NULL, NULL, '2025-12-07 10:04:49', '2025-12-07 10:04:49'),
(279, 25, 4, 2, 10, '2025-12-04', 'scheduled', 0, NULL, NULL, '2025-12-07 10:04:49', '2025-12-07 10:04:49'),
(280, 10, 4, 2, 10, '2025-12-05', 'scheduled', 0, NULL, NULL, '2025-12-07 10:04:49', '2025-12-07 10:04:49'),
(281, 11, 4, 2, 10, '2025-12-05', 'scheduled', 0, NULL, NULL, '2025-12-07 10:04:49', '2025-12-07 10:04:49'),
(282, 12, 4, 2, 10, '2025-12-05', 'scheduled', 0, NULL, NULL, '2025-12-07 10:04:49', '2025-12-07 10:04:49'),
(283, 21, 4, 2, 10, '2025-12-06', 'scheduled', 0, NULL, NULL, '2025-12-07 10:04:49', '2025-12-07 10:04:49'),
(284, 24, 4, 2, 10, '2025-12-06', 'scheduled', 0, NULL, NULL, '2025-12-07 10:04:49', '2025-12-07 10:04:49'),
(285, 25, 4, 2, 10, '2025-12-06', 'scheduled', 0, NULL, NULL, '2025-12-07 10:04:49', '2025-12-07 10:04:49'),
(286, 4, 2, 2, 4, '2025-12-02', 'scheduled', 0, NULL, NULL, '2025-12-07 12:39:55', '2025-12-07 12:39:55'),
(287, 4, 2, 2, 4, '2025-12-08', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(288, 5, 2, 2, 4, '2025-12-08', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(289, 6, 2, 2, 4, '2025-12-08', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(290, 19, 2, 2, 4, '2025-12-08', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(291, 6, 2, 2, 4, '2025-12-09', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(292, 19, 2, 2, 4, '2025-12-09', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(293, 5, 2, 2, 4, '2025-12-09', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(294, 19, 2, 2, 4, '2025-12-10', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(295, 4, 2, 2, 4, '2025-12-10', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(296, 6, 2, 2, 4, '2025-12-10', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(297, 4, 2, 2, 4, '2025-12-11', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(298, 5, 2, 2, 4, '2025-12-11', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(299, 19, 2, 2, 4, '2025-12-11', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(300, 5, 2, 2, 4, '2025-12-12', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(301, 6, 2, 2, 4, '2025-12-12', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(302, 4, 2, 2, 4, '2025-12-12', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(303, 4, 2, 2, 4, '2025-12-13', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(304, 5, 2, 2, 4, '2025-12-13', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(305, 6, 2, 2, 4, '2025-12-13', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(306, 19, 2, 2, 4, '2025-12-13', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(307, 4, 2, 2, 4, '2025-12-14', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(308, 5, 2, 2, 4, '2025-12-14', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(309, 6, 2, 2, 4, '2025-12-14', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(310, 19, 2, 2, 4, '2025-12-14', 'scheduled', 0, NULL, NULL, '2025-12-07 14:58:48', '2025-12-07 14:58:48'),
(311, 26, 5, 2, 14, '2025-12-08', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:24', '2025-12-08 04:17:24'),
(312, 27, 5, 2, 14, '2025-12-08', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:24', '2025-12-08 04:17:24'),
(313, 28, 5, 2, 14, '2025-12-08', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:24', '2025-12-08 04:17:24'),
(314, 28, 5, 2, 14, '2025-12-09', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:24', '2025-12-08 04:17:24'),
(315, 27, 5, 2, 14, '2025-12-09', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:24', '2025-12-08 04:17:24'),
(316, 26, 5, 2, 14, '2025-12-10', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:24', '2025-12-08 04:17:24'),
(317, 28, 5, 2, 14, '2025-12-10', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:24', '2025-12-08 04:17:24'),
(318, 27, 5, 2, 14, '2025-12-11', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:24', '2025-12-08 04:17:24'),
(319, 26, 5, 2, 14, '2025-12-11', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:24', '2025-12-08 04:17:24'),
(320, 26, 5, 2, 14, '2025-12-12', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:24', '2025-12-08 04:17:24'),
(321, 27, 5, 2, 14, '2025-12-12', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:24', '2025-12-08 04:17:24'),
(322, 28, 5, 2, 14, '2025-12-12', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:24', '2025-12-08 04:17:24'),
(323, 26, 5, 2, 14, '2025-12-13', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:24', '2025-12-08 04:17:24'),
(324, 27, 5, 2, 14, '2025-12-13', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:24', '2025-12-08 04:17:24'),
(325, 28, 5, 2, 14, '2025-12-13', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:25', '2025-12-08 04:17:25'),
(326, 26, 5, 2, 14, '2025-12-14', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:25', '2025-12-08 04:17:25'),
(327, 27, 5, 2, 14, '2025-12-14', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:25', '2025-12-08 04:17:25'),
(328, 28, 5, 2, 14, '2025-12-14', 'scheduled', 0, NULL, NULL, '2025-12-08 04:17:25', '2025-12-08 04:17:25'),
(329, 30, 5, 2, 14, '2025-12-09', 'scheduled', 0, NULL, NULL, '2025-12-08 04:31:36', '2025-12-08 04:31:36'),
(330, 29, 5, 2, 14, '2025-12-10', 'scheduled', 0, NULL, NULL, '2025-12-08 04:31:36', '2025-12-08 04:31:36'),
(331, 30, 5, 2, 14, '2025-12-10', 'scheduled', 0, NULL, NULL, '2025-12-08 04:31:36', '2025-12-08 04:31:36'),
(332, 29, 5, 2, 14, '2025-12-11', 'scheduled', 0, NULL, NULL, '2025-12-08 04:31:36', '2025-12-08 04:31:36'),
(333, 30, 5, 2, 14, '2025-12-11', 'scheduled', 0, NULL, NULL, '2025-12-08 04:31:36', '2025-12-08 04:31:36'),
(334, 29, 5, 2, 14, '2025-12-13', 'scheduled', 0, NULL, NULL, '2025-12-08 04:31:36', '2025-12-08 04:31:36'),
(335, 29, 5, 2, 14, '2025-12-14', 'scheduled', 0, NULL, NULL, '2025-12-08 04:31:36', '2025-12-08 04:31:36'),
(336, 30, 5, 2, 14, '2025-12-14', 'scheduled', 0, NULL, NULL, '2025-12-08 04:31:36', '2025-12-08 04:31:36'),
(337, 29, 5, 2, 14, '2025-12-09', 'scheduled', 0, NULL, NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(338, 30, 5, 2, 14, '2025-12-12', 'scheduled', 0, NULL, NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(339, 4, 2, 2, 4, '2025-12-15', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(340, 5, 2, 2, 4, '2025-12-15', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(341, 6, 2, 2, 4, '2025-12-15', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(342, 19, 2, 2, 4, '2025-12-15', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(343, 6, 2, 2, 4, '2025-12-16', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(344, 19, 2, 2, 4, '2025-12-16', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(345, 5, 2, 2, 4, '2025-12-16', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(346, 4, 2, 2, 4, '2025-12-17', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(347, 19, 2, 2, 4, '2025-12-17', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(348, 6, 2, 2, 4, '2025-12-17', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(349, 4, 2, 2, 4, '2025-12-18', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(350, 5, 2, 2, 4, '2025-12-18', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(351, 19, 2, 2, 4, '2025-12-18', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(352, 5, 2, 2, 4, '2025-12-19', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(353, 6, 2, 2, 4, '2025-12-19', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(354, 4, 2, 2, 4, '2025-12-19', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(355, 4, 2, 2, 4, '2025-12-20', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(356, 5, 2, 2, 4, '2025-12-20', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(357, 6, 2, 2, 4, '2025-12-20', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(358, 19, 2, 2, 4, '2025-12-20', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(359, 4, 2, 2, 4, '2025-12-21', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(360, 5, 2, 2, 4, '2025-12-21', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(361, 6, 2, 2, 4, '2025-12-21', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(362, 19, 2, 2, 4, '2025-12-21', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(363, 26, 5, 2, 14, '2025-12-15', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(364, 27, 5, 2, 14, '2025-12-15', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(365, 28, 5, 2, 14, '2025-12-15', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(366, 29, 5, 2, 14, '2025-12-15', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(367, 30, 5, 2, 14, '2025-12-15', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(368, 30, 5, 2, 14, '2025-12-16', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(369, 27, 5, 2, 14, '2025-12-16', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(370, 28, 5, 2, 14, '2025-12-16', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(371, 29, 5, 2, 14, '2025-12-16', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(372, 26, 5, 2, 14, '2025-12-17', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(373, 29, 5, 2, 14, '2025-12-17', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(374, 30, 5, 2, 14, '2025-12-17', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(375, 28, 5, 2, 14, '2025-12-17', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(376, 27, 5, 2, 14, '2025-12-18', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(377, 26, 5, 2, 14, '2025-12-18', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(378, 29, 5, 2, 14, '2025-12-18', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(379, 30, 5, 2, 14, '2025-12-18', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(380, 26, 5, 2, 14, '2025-12-19', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(381, 27, 5, 2, 14, '2025-12-19', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(382, 28, 5, 2, 14, '2025-12-19', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(383, 30, 5, 2, 14, '2025-12-19', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(384, 29, 5, 2, 14, '2025-12-20', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(385, 26, 5, 2, 14, '2025-12-20', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(386, 27, 5, 2, 14, '2025-12-20', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(387, 28, 5, 2, 14, '2025-12-20', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(388, 29, 5, 2, 14, '2025-12-21', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(389, 30, 5, 2, 14, '2025-12-21', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(390, 26, 5, 2, 14, '2025-12-21', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(391, 27, 5, 2, 14, '2025-12-21', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(392, 28, 5, 2, 14, '2025-12-21', 'scheduled', 0, NULL, NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(393, 24, 4, 2, 10, '2025-12-18', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(394, 25, 4, 2, 10, '2025-12-18', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(395, 10, 4, 2, 10, '2025-12-18', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(396, 11, 4, 2, 10, '2025-12-18', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(397, 23, 4, 2, 10, '2025-12-18', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(398, 21, 4, 2, 10, '2025-12-18', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(399, 10, 4, 2, 10, '2025-12-19', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(400, 11, 4, 2, 10, '2025-12-19', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(401, 12, 4, 2, 10, '2025-12-19', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(402, 24, 4, 2, 10, '2025-12-19', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(403, 25, 4, 2, 10, '2025-12-19', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(404, 21, 4, 2, 10, '2025-12-20', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(405, 24, 4, 2, 10, '2025-12-20', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(406, 25, 4, 2, 10, '2025-12-20', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(407, 10, 4, 2, 10, '2025-12-20', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(408, 11, 4, 2, 10, '2025-12-20', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(409, 12, 4, 2, 10, '2025-12-20', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(410, 23, 4, 2, 10, '2025-12-21', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(411, 10, 4, 2, 10, '2025-12-21', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(412, 11, 4, 2, 10, '2025-12-21', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(413, 12, 4, 2, 10, '2025-12-21', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(414, 21, 4, 2, 10, '2025-12-21', 'scheduled', 0, NULL, NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(415, 26, 5, 2, 14, '2025-12-22', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:34', '2025-12-08 07:54:34'),
(416, 27, 5, 2, 14, '2025-12-22', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:34', '2025-12-08 07:54:34'),
(417, 28, 5, 2, 14, '2025-12-22', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:34', '2025-12-08 07:54:34'),
(418, 29, 5, 2, 14, '2025-12-22', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:34', '2025-12-08 07:54:34'),
(419, 30, 5, 2, 14, '2025-12-22', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:34', '2025-12-08 07:54:34'),
(420, 30, 5, 2, 14, '2025-12-23', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:34', '2025-12-08 07:54:34'),
(421, 27, 5, 2, 14, '2025-12-23', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:34', '2025-12-08 07:54:34'),
(422, 28, 5, 2, 14, '2025-12-23', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:34', '2025-12-08 07:54:34'),
(423, 29, 5, 2, 14, '2025-12-23', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(424, 26, 5, 2, 14, '2025-12-24', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(425, 29, 5, 2, 14, '2025-12-24', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(426, 30, 5, 2, 14, '2025-12-24', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(427, 28, 5, 2, 14, '2025-12-24', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(428, 27, 5, 2, 14, '2025-12-25', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(429, 26, 5, 2, 14, '2025-12-25', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(430, 29, 5, 2, 14, '2025-12-25', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(431, 30, 5, 2, 14, '2025-12-25', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(432, 26, 5, 2, 14, '2025-12-26', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(433, 27, 5, 2, 14, '2025-12-26', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(434, 28, 5, 2, 14, '2025-12-26', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(435, 30, 5, 2, 14, '2025-12-26', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(436, 29, 5, 2, 14, '2025-12-27', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(437, 26, 5, 2, 14, '2025-12-27', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(438, 27, 5, 2, 14, '2025-12-27', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(439, 28, 5, 2, 14, '2025-12-27', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(440, 29, 5, 2, 14, '2025-12-28', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(441, 30, 5, 2, 14, '2025-12-28', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(442, 26, 5, 2, 14, '2025-12-28', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(443, 27, 5, 2, 14, '2025-12-28', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(444, 28, 5, 2, 14, '2025-12-28', 'scheduled', 0, NULL, NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(445, 4, 2, 2, 4, '2025-12-22', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(446, 5, 2, 2, 4, '2025-12-22', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(447, 6, 2, 2, 4, '2025-12-22', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(448, 19, 2, 2, 4, '2025-12-22', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(449, 6, 2, 2, 4, '2025-12-23', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(450, 19, 2, 2, 4, '2025-12-23', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(451, 5, 2, 2, 4, '2025-12-23', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(452, 4, 2, 2, 4, '2025-12-24', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(453, 19, 2, 2, 4, '2025-12-24', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(454, 6, 2, 2, 4, '2025-12-24', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(455, 4, 2, 2, 4, '2025-12-25', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(456, 5, 2, 2, 4, '2025-12-25', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(457, 19, 2, 2, 4, '2025-12-25', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(458, 5, 2, 2, 4, '2025-12-26', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(459, 6, 2, 2, 4, '2025-12-26', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(460, 4, 2, 2, 4, '2025-12-26', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(461, 4, 2, 2, 4, '2025-12-27', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(462, 5, 2, 2, 4, '2025-12-27', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(463, 6, 2, 2, 4, '2025-12-27', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(464, 19, 2, 2, 4, '2025-12-27', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(465, 4, 2, 2, 4, '2025-12-28', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(466, 5, 2, 2, 4, '2025-12-28', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(467, 6, 2, 2, 4, '2025-12-28', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(468, 19, 2, 2, 4, '2025-12-28', 'scheduled', 0, NULL, NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(469, 4, 2, 2, 4, '2025-12-29', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(470, 5, 2, 2, 4, '2025-12-29', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(471, 6, 2, 2, 4, '2025-12-29', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(472, 19, 2, 2, 4, '2025-12-29', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(473, 6, 2, 2, 4, '2025-12-30', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(474, 19, 2, 2, 4, '2025-12-30', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(475, 5, 2, 2, 4, '2025-12-30', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(476, 4, 2, 2, 4, '2025-12-31', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(477, 19, 2, 2, 4, '2025-12-31', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(478, 6, 2, 2, 4, '2025-12-31', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(479, 4, 2, 2, 4, '2026-01-01', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(480, 5, 2, 2, 4, '2026-01-01', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(481, 19, 2, 2, 4, '2026-01-01', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(482, 5, 2, 2, 4, '2026-01-02', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(483, 6, 2, 2, 4, '2026-01-02', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(484, 4, 2, 2, 4, '2026-01-02', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(485, 4, 2, 2, 4, '2026-01-03', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(486, 5, 2, 2, 4, '2026-01-03', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(487, 6, 2, 2, 4, '2026-01-03', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(488, 19, 2, 2, 4, '2026-01-03', 'scheduled', 0, NULL, NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `task_catalogs`
--

CREATE TABLE `task_catalogs` (
  `id` bigint UNSIGNED NOT NULL,
  `jobdesk_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `unit` enum('minutes','points','weight') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'points',
  `value` int UNSIGNED NOT NULL DEFAULT '1',
  `task_type` enum('routine','project') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'routine',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `task_catalogs`
--

INSERT INTO `task_catalogs` (`id`, `jobdesk_id`, `name`, `description`, `unit`, `value`, `task_type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Administrasi Harian', 'Input & rekap harian.', 'points', 5, 'routine', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(2, 1, 'Audit Data', 'Cek data dan verifikasi.', 'points', 8, 'project', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(3, 1, 'Layanan Pelanggan', 'Menangani tiket/permintaan.', 'minutes', 120, 'routine', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(4, 2, 'Administrasi Harian', 'Input & rekap harian.', 'points', 5, 'routine', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(5, 2, 'Audit Data', 'Cek data dan verifikasi.', 'points', 8, 'project', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(6, 2, 'Layanan Pelanggan', 'Menangani tiket/permintaan.', 'minutes', 120, 'routine', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(7, 3, 'Administrasi Harian', 'Input & rekap harian.', 'points', 5, 'routine', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(8, 3, 'Audit Data', 'Cek data dan verifikasi.', 'points', 8, 'project', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(9, 3, 'Layanan Pelanggan', 'Menangani tiket/permintaan.', 'minutes', 120, 'routine', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(10, 4, 'Administrasi Harian', 'Input & rekap harian.', 'points', 5, 'routine', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(11, 4, 'Audit Data', 'Cek data dan verifikasi.', 'points', 8, 'project', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(12, 4, 'Layanan Pelanggan', 'Menangani tiket/permintaan.', 'minutes', 120, 'routine', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(13, 5, 'Administrasi Harian', 'Input & rekap harian.', 'points', 5, 'routine', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(14, 5, 'Audit Data', 'Cek data dan verifikasi.', 'points', 8, 'project', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(15, 5, 'Layanan Pelanggan', 'Menangani tiket/permintaan.', 'minutes', 120, 'routine', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(16, 6, 'Administrasi Harian', 'Input & rekap harian.', 'points', 5, 'routine', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(17, 6, 'Audit Data', 'Cek data dan verifikasi.', 'points', 8, 'project', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(18, 6, 'Layanan Pelanggan', 'Menangani tiket/permintaan.', 'minutes', 120, 'routine', 1, '2026-03-05 05:21:15', '2026-03-05 05:21:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `task_progress_updates`
--

CREATE TABLE `task_progress_updates` (
  `id` bigint UNSIGNED NOT NULL,
  `task_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `progress` tinyint UNSIGNED NOT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `approval_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approval_level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'location_admin',
  `requires_approval` tinyint(1) NOT NULL DEFAULT '1',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `task_progress_updates`
--

INSERT INTO `task_progress_updates` (`id`, `task_id`, `user_id`, `progress`, `photo_path`, `document_path`, `note`, `approval_status`, `approval_level`, `requires_approval`, `approved_by`, `approved_at`, `rejection_reason`, `created_at`, `updated_at`) VALUES
(1, 5, 13, 20, 'task-progress/photos/a2E6nPzvy6ka9OfHfUI9kkpv6OdnmDA45resOoQo.png', NULL, 'sdsdadadasd', 'approved', 'none', 0, 13, '2025-12-03 04:56:59', NULL, '2025-12-03 04:56:59', '2025-12-03 04:56:59'),
(2, 9, 7, 20, 'task-progress/photos/Db5v9Ex3bhH7VMYtnud5EhdGq3vl5r8tymFz0k5E.jpg', NULL, 'dfasdasdasdasdasd', 'approved', 'location_admin', 1, 13, '2025-12-03 05:06:11', NULL, '2025-12-03 05:05:56', '2025-12-03 05:06:11'),
(3, 9, 7, 40, 'task-progress/photos/NJV40qvLkVCZP96lsnDcO5gWNrw9y4uoXrcunUgI.jpg', NULL, NULL, 'rejected', 'location_admin', 1, 13, '2025-12-03 05:09:48', 'masih belum sesuai', '2025-12-03 05:09:24', '2025-12-03 05:09:48'),
(4, 9, 7, 30, 'task-progress/photos/H895NTiMALdXltQVj6ygbgJvfUBSrKlhZYjNBeFW.jpg', NULL, 'asdasdasdasdasd', 'rejected', 'location_admin', 1, 13, '2025-12-03 05:19:57', 'sdasdasdasdarwesfrfsezdesdasdasdasdarwesfrfsezdesdasdasdasdarwesfrfsezdesdasdasdasdarwesfrfsezdesdasdasdasdarwesfrfsezdesdasdasdasdarwesfrfsezdesdasdasdasdarwesfrfsezdesdasdasdasdarwesfrfsezdesdasdasdasdarwesfrfsezdesdasdasdasdarwesfrfsezdesdasdasdasdarwesfrfsezdesdasdasdasdarwesfrfsezde', '2025-12-03 05:10:10', '2025-12-03 05:19:57'),
(5, 9, 7, 100, 'task-progress/photos/G3QCe3JXZMEUnMbxaFPzTYUy8Y8ok9oefTk7Yk7n.jpg', NULL, 'sdfsdfasdasdasdasdasdasd', 'approved', 'location_admin', 1, 13, '2025-12-03 05:21:10', NULL, '2025-12-03 05:20:50', '2025-12-03 05:21:10'),
(6, 11, 13, 10, 'task-progress/photos/yCHHsrU9JMeGXTOtte3h7FT3iznzxyQmWKCb11GX.jpg', NULL, 'dfsdfsdfsdfsfd', 'approved', 'none', 0, 13, '2025-12-09 04:27:01', NULL, '2025-12-09 04:27:01', '2025-12-09 04:27:01'),
(7, 10, 1, 20, 'task-progress/photos/8pZ3sCivGDa5mCHI3C9lxwvtJ8acLfZgDmMxafiQ.jpg', NULL, 'gfjfgjhfghfghfh', 'approved', 'manager', 1, 13, '2025-12-09 04:39:37', NULL, '2025-12-09 04:28:38', '2025-12-09 04:39:37'),
(8, 10, 13, 10, 'task-progress/photos/IGUTvpuOpnlTKwnkRbiUcVu4gWs2DF6yP0cgF0gU.jpg', NULL, 'sdfsdfsdfsf', 'approved', 'none', 0, 13, '2025-12-09 04:50:16', NULL, '2025-12-09 04:50:16', '2025-12-09 04:50:16'),
(9, 10, 13, 50, 'task-progress/photos/p6FfybzwFH4t8mkuYQ97f28YU8L9u9pXUXTQp8ET.jpg', NULL, 'asdadadasdads', 'approved', 'none', 0, 13, '2025-12-09 04:50:49', NULL, '2025-12-09 04:50:49', '2025-12-09 04:50:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `task_slots`
--

CREATE TABLE `task_slots` (
  `id` bigint UNSIGNED NOT NULL,
  `task_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `minutes` int UNSIGNED NOT NULL,
  `order` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `task_slots`
--

INSERT INTO `task_slots` (`id`, `task_id`, `name`, `percentage`, `minutes`, `order`, `status`, `approved_by`, `approved_at`, `rejection_reason`, `created_by`, `created_at`, `updated_at`) VALUES
(7, 12, 'asda', 20.00, 60, 2, 'approved', 13, '2025-12-15 09:31:59', NULL, 13, '2025-12-09 09:01:55', '2025-12-15 09:31:59'),
(8, 12, 'sasdasd', 20.00, 60, 3, 'rejected', 13, '2025-12-15 09:35:41', 'sdfsdfsdfsdf', 13, '2025-12-09 09:01:55', '2025-12-15 09:35:41'),
(9, 12, 'sdosijdfsoidf', 20.00, 60, 4, 'rejected', 13, '2025-12-09 09:04:32', 'asdasdasdas', 13, '2025-12-09 09:01:55', '2025-12-09 09:04:32'),
(10, 12, 'sdfasdasd', 20.00, 30, 5, 'rejected', 13, '2025-12-09 09:04:36', 'asdasdasd', 13, '2025-12-09 09:01:55', '2025-12-09 09:04:36'),
(11, 1, 'Default', 100.00, 1, 0, 'approved', 13, '2025-12-09 09:38:47', NULL, NULL, '2025-12-09 09:38:47', '2025-12-09 09:38:47'),
(12, 2, 'Default', 100.00, 1, 0, 'approved', 13, '2025-12-09 09:38:47', NULL, NULL, '2025-12-09 09:38:47', '2025-12-09 09:38:47'),
(13, 3, 'Default', 100.00, 1, 0, 'approved', 13, '2025-12-09 09:38:47', NULL, NULL, '2025-12-09 09:38:47', '2025-12-09 09:38:47'),
(14, 4, 'Default', 100.00, 1, 0, 'approved', 13, '2025-12-09 09:38:47', NULL, NULL, '2025-12-09 09:38:47', '2025-12-09 09:38:47'),
(15, 5, 'Default', 100.00, 1, 0, 'approved', 13, '2025-12-09 09:38:47', NULL, NULL, '2025-12-09 09:38:47', '2025-12-09 09:38:47'),
(16, 6, 'Default', 100.00, 1, 0, 'approved', 13, '2025-12-09 09:38:47', NULL, NULL, '2025-12-09 09:38:47', '2025-12-09 09:38:47'),
(17, 7, 'Default', 100.00, 1, 0, 'approved', 13, '2025-12-09 09:38:47', NULL, NULL, '2025-12-09 09:38:47', '2025-12-09 09:38:47'),
(18, 8, 'Default', 100.00, 1, 0, 'approved', 1, '2025-12-09 09:38:47', NULL, NULL, '2025-12-09 09:38:47', '2025-12-09 09:38:47'),
(19, 9, 'Default', 100.00, 1, 0, 'approved', 7, '2025-12-09 09:38:47', NULL, NULL, '2025-12-09 09:38:47', '2025-12-09 09:38:47'),
(20, 10, 'Default', 100.00, 1, 0, 'approved', 1, '2025-12-09 09:38:47', NULL, NULL, '2025-12-09 09:38:47', '2025-12-09 09:38:47'),
(21, 11, 'Default', 100.00, 1, 0, 'approved', 1, '2025-12-09 09:38:47', NULL, NULL, '2025-12-09 09:38:47', '2025-12-09 09:38:47'),
(22, 13, 'riset bahan', 25.00, 2520, 0, 'approved', 13, '2025-12-10 06:04:46', NULL, 26, '2025-12-09 11:29:23', '2025-12-10 06:04:46'),
(23, 13, 'riset gizi', 25.00, 2520, 1, 'approved', 13, '2025-12-10 06:04:46', NULL, 26, '2025-12-09 11:29:23', '2025-12-10 06:04:46'),
(24, 13, 'riset bumbu', 25.00, 2520, 2, 'approved', 13, '2025-12-10 06:04:46', NULL, 26, '2025-12-09 11:29:23', '2025-12-10 06:04:46'),
(25, 13, 'tes rasa', 25.00, 2520, 3, 'approved', 13, '2025-12-10 06:04:46', NULL, 26, '2025-12-09 11:29:23', '2025-12-10 06:04:46'),
(26, 14, 'eqweqweqwe', 25.00, 60, 0, 'approved', 13, '2025-12-15 08:14:44', NULL, 26, '2025-12-10 03:41:09', '2025-12-15 08:14:44'),
(27, 14, 'qweqweqwe', 25.00, 60, 1, 'approved', 13, '2025-12-15 08:49:00', NULL, 26, '2025-12-10 03:41:09', '2025-12-15 08:49:00'),
(28, 14, 'qweqweqweq', 25.00, 60, 2, 'pending', NULL, NULL, NULL, 26, '2025-12-10 03:41:09', '2025-12-15 07:08:19'),
(29, 14, 'qweqweq', 25.00, 60, 3, 'pending', NULL, NULL, NULL, 26, '2025-12-10 03:41:09', '2025-12-15 07:08:19'),
(30, 15, 'riset bahan', 25.00, 60, 0, 'approved', 13, '2025-12-10 07:33:25', NULL, 26, '2025-12-10 05:01:38', '2025-12-10 07:33:25'),
(31, 15, 'riset gizi', 25.00, 60, 1, 'approved', 13, '2025-12-10 07:33:25', NULL, 26, '2025-12-10 05:01:38', '2025-12-10 07:33:25'),
(32, 15, 'riset bumbu', 25.00, 60, 2, 'approved', 13, '2025-12-10 07:33:25', NULL, 26, '2025-12-10 05:01:38', '2025-12-10 07:33:25'),
(33, 15, 'tes rasa', 25.00, 60, 3, 'approved', 13, '2025-12-10 07:33:25', NULL, 26, '2025-12-10 05:01:38', '2025-12-10 07:33:25'),
(34, 18, 'asdasdasd', 25.00, 60, 0, 'pending', NULL, NULL, NULL, 13, '2025-12-12 08:37:40', '2025-12-12 08:37:40'),
(35, 18, 'asdadad', 12.00, 29, 1, 'pending', NULL, NULL, NULL, 13, '2025-12-12 08:37:40', '2025-12-12 08:37:40'),
(36, 18, 'asdasdasd', 7.00, 17, 2, 'pending', NULL, NULL, NULL, 13, '2025-12-12 08:37:40', '2025-12-12 08:37:40'),
(37, 18, 'ssdfsdfsdf', 10.00, 24, 3, 'pending', NULL, NULL, NULL, 13, '2025-12-12 08:37:40', '2025-12-12 08:37:40'),
(38, 18, 'sfjskfjsf', 46.00, 110, 4, 'pending', NULL, NULL, NULL, 13, '2025-12-12 08:37:40', '2025-12-12 08:37:40'),
(44, 24, 'tester', 20.83, 50, 2, 'pending', NULL, NULL, NULL, 13, '2025-12-22 02:58:28', '2025-12-22 02:58:28'),
(45, 24, 'tester 2', 20.83, 50, 3, 'pending', NULL, NULL, NULL, 13, '2025-12-22 02:58:28', '2025-12-22 02:58:28'),
(46, 24, 'acc', 8.33, 20, 5, 'pending', NULL, NULL, NULL, 13, '2025-12-22 02:58:28', '2025-12-22 02:58:28'),
(47, 24, 'tester 2', 25.00, 60, 3, 'pending', NULL, NULL, NULL, 13, '2025-12-22 02:58:28', '2025-12-22 02:58:28'),
(48, 24, 'finishing', 25.00, 60, 4, 'pending', NULL, NULL, NULL, 13, '2025-12-22 02:58:28', '2025-12-22 02:58:28'),
(49, 12, 'testing 2', 20.00, 30, 4, 'pending', NULL, NULL, NULL, 13, '2025-12-22 03:13:19', '2025-12-22 03:13:19'),
(50, 20, 'cari bahan', 25.00, 60, 0, 'pending', NULL, NULL, NULL, 13, '2025-12-22 03:52:20', '2025-12-22 03:52:20'),
(51, 20, 'riset resep', 25.00, 60, 1, 'pending', NULL, NULL, NULL, 13, '2025-12-22 03:52:20', '2025-12-22 03:52:20'),
(52, 20, 'tester', 25.00, 60, 2, 'pending', NULL, NULL, NULL, 13, '2025-12-22 03:52:20', '2025-12-22 03:52:20'),
(53, 20, 'acc', 14.58, 35, 5, 'pending', NULL, NULL, NULL, 13, '2025-12-22 03:52:20', '2025-12-22 03:52:20'),
(54, 20, 'sfsdfsdf', 10.42, 25, 4, 'pending', NULL, NULL, NULL, 13, '2025-12-22 03:52:20', '2025-12-22 03:52:20'),
(55, 25, 'cari bahan', 14.17, 34, 0, 'pending', NULL, NULL, NULL, 13, '2025-12-22 03:56:35', '2025-12-22 03:56:35'),
(56, 25, 'werwerwer', 14.17, 34, 1, 'pending', NULL, NULL, NULL, 13, '2025-12-22 03:56:35', '2025-12-22 03:56:35'),
(57, 25, 'sdfsdfsdf', 25.00, 60, 2, 'pending', NULL, NULL, NULL, 13, '2025-12-22 03:56:35', '2025-12-22 03:56:35'),
(58, 25, 'ertertert', 33.33, 80, 3, 'pending', NULL, NULL, NULL, 13, '2025-12-22 03:56:35', '2025-12-22 03:56:35'),
(59, 25, 'yrtyrtyrtyr', 13.33, 32, 4, 'pending', NULL, NULL, NULL, 13, '2025-12-22 03:56:35', '2025-12-22 03:56:35'),
(60, 26, 'dfgdgdfg', 25.00, 60, 0, 'pending', NULL, NULL, NULL, 13, '2025-12-23 06:17:45', '2025-12-23 06:17:45'),
(61, 26, 'sdfsdfsdf', 25.00, 60, 1, 'pending', NULL, NULL, NULL, 13, '2025-12-23 06:17:45', '2025-12-23 06:17:45'),
(62, 26, 'sdfsdfsdfsdf', 25.00, 60, 2, 'pending', NULL, NULL, NULL, 13, '2025-12-23 06:17:45', '2025-12-23 06:17:45'),
(63, 26, 'dsfsfsdf', 25.00, 60, 3, 'pending', NULL, NULL, NULL, 13, '2025-12-23 06:17:45', '2025-12-23 06:17:45'),
(64, 27, 'sdfsfsfsf', 25.00, 60, 0, 'pending', NULL, NULL, NULL, 1, '2025-12-23 06:18:51', '2025-12-23 06:18:51'),
(65, 27, 'werwerwer', 25.00, 60, 1, 'pending', NULL, NULL, NULL, 1, '2025-12-23 06:18:51', '2025-12-23 06:18:51'),
(66, 27, 'dfgdfgdgfd', 25.00, 60, 2, 'pending', NULL, NULL, NULL, 1, '2025-12-23 06:18:51', '2025-12-23 06:18:51'),
(67, 27, 'dfgdfgdfg', 25.00, 60, 3, 'pending', NULL, NULL, NULL, 1, '2025-12-23 06:18:51', '2025-12-23 06:18:51'),
(68, 28, 'rouf', 100.00, 30, 0, 'approved', 13, '2025-12-27 05:08:09', NULL, 1, '2025-12-23 06:48:21', '2025-12-27 05:08:09'),
(69, 29, 'riset', 50.00, 30, 0, 'approved', 13, '2025-12-27 05:09:34', NULL, 1, '2025-12-23 06:50:08', '2025-12-27 05:09:34'),
(70, 29, 'menghubungi', 25.00, 15, 1, 'approved', 13, '2025-12-27 05:09:34', NULL, 1, '2025-12-23 06:50:08', '2025-12-27 05:09:34'),
(71, 29, 'di link', 25.00, 15, 2, 'approved', 13, '2025-12-27 05:09:34', NULL, 1, '2025-12-23 06:50:08', '2025-12-27 05:09:34'),
(72, 30, 'fghfghfh', 50.00, 30, 0, 'pending', NULL, NULL, NULL, 26, '2025-12-27 05:15:32', '2025-12-27 05:15:32'),
(73, 30, 'riset resep', 50.00, 30, 1, 'pending', NULL, NULL, NULL, 26, '2025-12-27 05:15:32', '2025-12-27 05:15:32'),
(74, 31, 'yuiyuiyui', 50.00, 30, 0, 'approved', 13, '2025-12-27 05:34:01', NULL, 18, '2025-12-27 05:17:46', '2025-12-27 05:34:01'),
(75, 31, 'tytyutyut', 50.00, 30, 1, 'pending', NULL, NULL, NULL, 18, '2025-12-27 05:17:46', '2025-12-27 05:25:55'),
(76, 32, 'Selesai', 100.00, 120, 0, 'approved', 13, '2026-03-05 05:21:15', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(77, 33, 'Selesai', 100.00, 120, 0, 'approved', 13, '2026-03-05 05:21:15', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(78, 34, 'Selesai', 100.00, 120, 0, 'approved', 13, '2026-03-05 05:21:15', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(79, 35, 'Selesai', 100.00, 120, 0, 'approved', 13, '2026-03-05 05:21:15', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(80, 36, 'Selesai', 100.00, 120, 0, 'approved', 13, '2026-03-05 05:21:15', NULL, 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(81, 37, 'tester', 100.00, 150, 0, 'pending', NULL, NULL, NULL, 13, '2026-03-05 06:31:17', '2026-03-05 06:31:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `task_slot_attachments`
--

CREATE TABLE `task_slot_attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `task_slot_id` bigint UNSIGNED NOT NULL,
  `type` enum('photo','document','link') COLLATE utf8mb4_unicode_ci NOT NULL,
  `path_or_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploaded_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `task_slot_attachments`
--

INSERT INTO `task_slot_attachments` (`id`, `task_slot_id`, `type`, `path_or_url`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(3, 22, 'link', 'https://www.google.com/maps/place/Jl.+Griya+Tiara+Amarta,+Kricak,+Kec.+Tegalrejo,+Kota+Yogyakarta,+Daerah+Istimewa+Yogyakarta+55242/@-7.7722971,110.3562445,3a,75y,352.29h,73.93t/data=!3m7!1e1!3m5!1sUyvaNLSRrSO-zUnx_YPTrw!2e0!6shttps:%2F%2Fstreetviewpixels-pa.googleapis.com%2Fv1%2Fthumbnail%3Fcb_client%3Dmaps_sv.tactile%26w%3D900%26h%3D600%26pitch%3D16.07075119243852%26panoid%3DUyvaNLSRrSO-zUnx_YPTrw%26yaw%3D352.29363877045756!7i16384!8i8192!4m9!1m2!2m1!1sburjo+jambon!3m5!1s0x2e7a5841eab97109:0x5ac541377382c3fb!8m2!3d-7.7720171!4d110.3561585!16s%2Fg%2F11sgj0fsx1?entry=ttu&g_ep=EgoyMDI1MTIwNy4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 26, '2025-12-10 06:03:39', '2025-12-10 06:03:39'),
(4, 23, 'link', 'https://www.google.com/maps/place/Jl.+Griya+Tiara+Amarta,+Kricak,+Kec.+Tegalrejo,+Kota+Yogyakarta,+Daerah+Istimewa+Yogyakarta+55242/@-7.7722971,110.3562445,3a,75y,352.29h,73.93t/data=!3m7!1e1!3m5!1sUyvaNLSRrSO-zUnx_YPTrw!2e0!6shttps:%2F%2Fstreetviewpixels-pa.googleapis.com%2Fv1%2Fthumbnail%3Fcb_client%3Dmaps_sv.tactile%26w%3D900%26h%3D600%26pitch%3D16.07075119243852%26panoid%3DUyvaNLSRrSO-zUnx_YPTrw%26yaw%3D352.29363877045756!7i16384!8i8192!4m9!1m2!2m1!1sburjo+jambon!3m5!1s0x2e7a5841eab97109:0x5ac541377382c3fb!8m2!3d-7.7720171!4d110.3561585!16s%2Fg%2F11sgj0fsx1?entry=ttu&g_ep=EgoyMDI1MTIwNy4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 26, '2025-12-10 06:03:54', '2025-12-10 06:03:54'),
(5, 24, 'link', 'https://docs.google.com/spreadsheets/d/1b9zaPl0FQbd04XQ3Haagv9tcO4-U3GEWwOoI7RvG7I4/edit?usp=sharing', 26, '2025-12-10 06:04:01', '2025-12-10 06:04:01'),
(6, 25, 'link', 'https://docs.google.com/spreadsheets/d/1b9zaPl0FQbd04XQ3Haagv9tcO4-U3GEWwOoI7RvG7I4/edit?usp=sharing', 26, '2025-12-10 06:04:08', '2025-12-10 06:04:08'),
(7, 7, 'link', 'https://gemini.google.com/app/73bf74b6552100d4?hl=id', 26, '2025-12-15 07:17:22', '2025-12-15 07:17:22'),
(8, 26, 'link', 'https://example.com/ecommerce', 26, '2025-12-15 08:06:12', '2025-12-15 08:06:12'),
(9, 26, 'link', 'https://gemini.google.com/app/73bf74b6552100d4?hl=id', 26, '2025-12-15 08:06:37', '2025-12-15 08:06:37'),
(10, 27, 'link', 'https://gemini.google.com/app/73bf74b6552100d4?hl=id', 26, '2025-12-15 08:32:50', '2025-12-15 08:32:50'),
(11, 27, 'link', 'https://example.com/ecommerce', 26, '2025-12-15 08:45:41', '2025-12-15 08:45:41'),
(12, 8, 'link', 'https://gemini.google.com/app/73bf74b6552100d4?hl=id', 26, '2025-12-15 08:51:44', '2025-12-15 08:51:44'),
(13, 69, 'link', 'https://docs.google.com/spreadsheets/d/1Eif8QWH9zHHDQZGIg7it-dy2D6ta5CnNXNk31CffNNU/edit?gid=0#gid=0', 1, '2025-12-23 06:55:11', '2025-12-23 06:55:11'),
(14, 68, 'link', 'https://docs.google.com/spreadsheets/d/1Eif8QWH9zHHDQZGIg7it-dy2D6ta5CnNXNk31CffNNU/edit?gid=0#gid=0', 1, '2025-12-23 06:56:30', '2025-12-23 06:56:30'),
(15, 74, 'link', 'https://gemini.google.com/app/8777388ac6dd0752?hl=id', 18, '2025-12-27 05:32:58', '2025-12-27 05:32:58'),
(16, 74, 'link', 'https://gemini.google.com/app/8777388ac6dd0752?hl=id', 18, '2025-12-27 05:33:47', '2025-12-27 05:33:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `task_slot_history`
--

CREATE TABLE `task_slot_history` (
  `id` bigint UNSIGNED NOT NULL,
  `task_slot_id` bigint UNSIGNED NOT NULL,
  `action` enum('created','updated','deleted','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_before` json DEFAULT NULL,
  `data_after` json DEFAULT NULL,
  `actor_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `task_slot_history`
--

INSERT INTO `task_slot_history` (`id`, `task_slot_id`, `action`, `data_before`, `data_after`, `actor_id`, `created_at`, `updated_at`) VALUES
(18, 7, 'created', NULL, '{\"id\": 7, \"name\": \"asda\", \"order\": 2, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": 20, \"updated_at\": \"2025-12-09T09:01:55.000000Z\"}', 13, '2025-12-09 09:01:55', '2025-12-09 09:01:55'),
(19, 8, 'created', NULL, '{\"id\": 8, \"name\": \"sasdasd\", \"order\": 3, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": 20, \"updated_at\": \"2025-12-09T09:01:55.000000Z\"}', 13, '2025-12-09 09:01:55', '2025-12-09 09:01:55'),
(20, 9, 'created', NULL, '{\"id\": 9, \"name\": \"sdosijdfsoidf\", \"order\": 4, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": 20, \"updated_at\": \"2025-12-09T09:01:55.000000Z\"}', 13, '2025-12-09 09:01:55', '2025-12-09 09:01:55'),
(21, 10, 'created', NULL, '{\"id\": 10, \"name\": \"sdfasdasd\", \"order\": 5, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": 20, \"updated_at\": \"2025-12-09T09:01:55.000000Z\"}', 13, '2025-12-09 09:01:55', '2025-12-09 09:01:55'),
(23, 7, 'rejected', '{\"id\": 7, \"name\": \"asda\", \"order\": 2, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": 20, \"updated_at\": \"2025-12-09T09:01:55.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 7, \"name\": \"asda\", \"order\": 2, \"status\": \"rejected\", \"minutes\": 60, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": 20, \"updated_at\": \"2025-12-09T09:04:24.000000Z\", \"approved_at\": \"2025-12-09T09:04:24.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"asdadas\"}', 13, '2025-12-09 09:04:24', '2025-12-09 09:04:24'),
(24, 8, 'rejected', '{\"id\": 8, \"name\": \"sasdasd\", \"order\": 3, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": 20, \"updated_at\": \"2025-12-09T09:01:55.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 8, \"name\": \"sasdasd\", \"order\": 3, \"status\": \"rejected\", \"minutes\": 60, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": 20, \"updated_at\": \"2025-12-09T09:04:28.000000Z\", \"approved_at\": \"2025-12-09T09:04:28.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"asdasdasd\"}', 13, '2025-12-09 09:04:28', '2025-12-09 09:04:28'),
(25, 9, 'rejected', '{\"id\": 9, \"name\": \"sdosijdfsoidf\", \"order\": 4, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": 20, \"updated_at\": \"2025-12-09T09:01:55.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 9, \"name\": \"sdosijdfsoidf\", \"order\": 4, \"status\": \"rejected\", \"minutes\": 60, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": 20, \"updated_at\": \"2025-12-09T09:04:32.000000Z\", \"approved_at\": \"2025-12-09T09:04:32.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"asdasdasdas\"}', 13, '2025-12-09 09:04:32', '2025-12-09 09:04:32'),
(26, 10, 'rejected', '{\"id\": 10, \"name\": \"sdfasdasd\", \"order\": 5, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": 20, \"updated_at\": \"2025-12-09T09:01:55.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 10, \"name\": \"sdfasdasd\", \"order\": 5, \"status\": \"rejected\", \"minutes\": 30, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": 20, \"updated_at\": \"2025-12-09T09:04:36.000000Z\", \"approved_at\": \"2025-12-09T09:04:36.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"asdasdasd\"}', 13, '2025-12-09 09:04:36', '2025-12-09 09:04:36'),
(29, 22, 'created', NULL, '{\"id\": 22, \"name\": \"riset bahan\", \"order\": 0, \"status\": \"pending\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-09T11:29:23.000000Z\"}', 26, '2025-12-09 11:29:23', '2025-12-09 11:29:23'),
(30, 23, 'created', NULL, '{\"id\": 23, \"name\": \"riset gizi\", \"order\": 1, \"status\": \"pending\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-09T11:29:23.000000Z\"}', 26, '2025-12-09 11:29:23', '2025-12-09 11:29:23'),
(31, 24, 'created', NULL, '{\"id\": 24, \"name\": \"riset bumbu\", \"order\": 2, \"status\": \"pending\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-09T11:29:23.000000Z\"}', 26, '2025-12-09 11:29:23', '2025-12-09 11:29:23'),
(32, 25, 'created', NULL, '{\"id\": 25, \"name\": \"tes rasa\", \"order\": 3, \"status\": \"pending\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-09T11:29:23.000000Z\"}', 26, '2025-12-09 11:29:23', '2025-12-09 11:29:23'),
(33, 26, 'created', NULL, '{\"id\": 26, \"name\": \"eqweqweqwe\", \"order\": 0, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T03:41:09.000000Z\"}', 26, '2025-12-10 03:41:09', '2025-12-10 03:41:09'),
(34, 27, 'created', NULL, '{\"id\": 27, \"name\": \"qweqweqwe\", \"order\": 1, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T03:41:09.000000Z\"}', 26, '2025-12-10 03:41:09', '2025-12-10 03:41:09'),
(35, 28, 'created', NULL, '{\"id\": 28, \"name\": \"qweqweqweq\", \"order\": 2, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T03:41:09.000000Z\"}', 26, '2025-12-10 03:41:09', '2025-12-10 03:41:09'),
(36, 29, 'created', NULL, '{\"id\": 29, \"name\": \"qweqweq\", \"order\": 3, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T03:41:09.000000Z\"}', 26, '2025-12-10 03:41:09', '2025-12-10 03:41:09'),
(37, 26, 'rejected', '{\"id\": 26, \"name\": \"eqweqweqwe\", \"order\": 0, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T03:41:09.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 26, \"name\": \"eqweqweqwe\", \"order\": 0, \"status\": \"rejected\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T03:46:51.000000Z\", \"approved_at\": \"2025-12-10T03:46:51.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"sdfsfsdf\"}', 13, '2025-12-10 03:46:51', '2025-12-10 03:46:51'),
(38, 27, 'rejected', '{\"id\": 27, \"name\": \"qweqweqwe\", \"order\": 1, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T03:41:09.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 27, \"name\": \"qweqweqwe\", \"order\": 1, \"status\": \"rejected\", \"minutes\": 30, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T03:46:51.000000Z\", \"approved_at\": \"2025-12-10T03:46:51.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"sdfsfsdf\"}', 13, '2025-12-10 03:46:51', '2025-12-10 03:46:51'),
(39, 28, 'rejected', '{\"id\": 28, \"name\": \"qweqweqweq\", \"order\": 2, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T03:41:09.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 28, \"name\": \"qweqweqweq\", \"order\": 2, \"status\": \"rejected\", \"minutes\": 30, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T03:46:51.000000Z\", \"approved_at\": \"2025-12-10T03:46:51.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"sdfsfsdf\"}', 13, '2025-12-10 03:46:51', '2025-12-10 03:46:51'),
(40, 29, 'rejected', '{\"id\": 29, \"name\": \"qweqweq\", \"order\": 3, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T03:41:09.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 29, \"name\": \"qweqweq\", \"order\": 3, \"status\": \"rejected\", \"minutes\": 30, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T03:46:51.000000Z\", \"approved_at\": \"2025-12-10T03:46:51.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"sdfsfsdf\"}', 13, '2025-12-10 03:46:51', '2025-12-10 03:46:51'),
(41, 30, 'created', NULL, '{\"id\": 30, \"name\": \"riset bahan\", \"order\": 0, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 15, \"created_at\": \"2025-12-10T05:01:38.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T05:01:38.000000Z\"}', 26, '2025-12-10 05:01:38', '2025-12-10 05:01:38'),
(42, 31, 'created', NULL, '{\"id\": 31, \"name\": \"riset gizi\", \"order\": 1, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 15, \"created_at\": \"2025-12-10T05:01:38.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T05:01:38.000000Z\"}', 26, '2025-12-10 05:01:38', '2025-12-10 05:01:38'),
(43, 32, 'created', NULL, '{\"id\": 32, \"name\": \"riset bumbu\", \"order\": 2, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 15, \"created_at\": \"2025-12-10T05:01:38.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T05:01:38.000000Z\"}', 26, '2025-12-10 05:01:38', '2025-12-10 05:01:38'),
(44, 33, 'created', NULL, '{\"id\": 33, \"name\": \"tes rasa\", \"order\": 3, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 15, \"created_at\": \"2025-12-10T05:01:38.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T05:01:38.000000Z\"}', 26, '2025-12-10 05:01:38', '2025-12-10 05:01:38'),
(45, 22, 'updated', '{\"id\": 22, \"name\": \"riset bahan\", \"task\": {\"id\": 13, \"title\": \"resep makasan\", \"status\": \"pending\", \"due_date\": \"2025-12-14T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"photo_path\": \"tasks/photos/rpy8wy9zhtqaDM0A9Gz4c27pgOX33KDtwqjbk3PZ.jpg\", \"updated_at\": \"2025-12-10T03:39:33.000000Z\", \"approved_at\": \"2025-12-10T03:39:33.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"sdfsdfsf\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 10080, \"requires_approval\": true}, \"order\": 0, \"status\": \"pending\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-09T11:29:23.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 22, \"name\": \"riset bahan\", \"task\": {\"id\": 13, \"title\": \"resep makasan\", \"status\": \"pending\", \"due_date\": \"2025-12-14T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"photo_path\": \"tasks/photos/rpy8wy9zhtqaDM0A9Gz4c27pgOX33KDtwqjbk3PZ.jpg\", \"updated_at\": \"2025-12-10T03:39:33.000000Z\", \"approved_at\": \"2025-12-10T03:39:33.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"sdfsdfsf\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 10080, \"requires_approval\": true}, \"order\": 0, \"status\": \"pending\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-09T11:29:23.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 26, '2025-12-10 06:03:39', '2025-12-10 06:03:39'),
(46, 23, 'updated', '{\"id\": 23, \"name\": \"riset gizi\", \"task\": {\"id\": 13, \"title\": \"resep makasan\", \"status\": \"pending\", \"due_date\": \"2025-12-14T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"photo_path\": \"tasks/photos/rpy8wy9zhtqaDM0A9Gz4c27pgOX33KDtwqjbk3PZ.jpg\", \"updated_at\": \"2025-12-10T03:39:33.000000Z\", \"approved_at\": \"2025-12-10T03:39:33.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"sdfsdfsf\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 10080, \"requires_approval\": true}, \"order\": 1, \"status\": \"pending\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-09T11:29:23.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 23, \"name\": \"riset gizi\", \"task\": {\"id\": 13, \"title\": \"resep makasan\", \"status\": \"pending\", \"due_date\": \"2025-12-14T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"photo_path\": \"tasks/photos/rpy8wy9zhtqaDM0A9Gz4c27pgOX33KDtwqjbk3PZ.jpg\", \"updated_at\": \"2025-12-10T03:39:33.000000Z\", \"approved_at\": \"2025-12-10T03:39:33.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"sdfsdfsf\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 10080, \"requires_approval\": true}, \"order\": 1, \"status\": \"pending\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-09T11:29:23.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 26, '2025-12-10 06:03:54', '2025-12-10 06:03:54'),
(47, 24, 'updated', '{\"id\": 24, \"name\": \"riset bumbu\", \"task\": {\"id\": 13, \"title\": \"resep makasan\", \"status\": \"pending\", \"due_date\": \"2025-12-14T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"photo_path\": \"tasks/photos/rpy8wy9zhtqaDM0A9Gz4c27pgOX33KDtwqjbk3PZ.jpg\", \"updated_at\": \"2025-12-10T03:39:33.000000Z\", \"approved_at\": \"2025-12-10T03:39:33.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"sdfsdfsf\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 10080, \"requires_approval\": true}, \"order\": 2, \"status\": \"pending\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-09T11:29:23.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 24, \"name\": \"riset bumbu\", \"task\": {\"id\": 13, \"title\": \"resep makasan\", \"status\": \"pending\", \"due_date\": \"2025-12-14T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"photo_path\": \"tasks/photos/rpy8wy9zhtqaDM0A9Gz4c27pgOX33KDtwqjbk3PZ.jpg\", \"updated_at\": \"2025-12-10T03:39:33.000000Z\", \"approved_at\": \"2025-12-10T03:39:33.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"sdfsdfsf\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 10080, \"requires_approval\": true}, \"order\": 2, \"status\": \"pending\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-09T11:29:23.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 26, '2025-12-10 06:04:01', '2025-12-10 06:04:01'),
(48, 25, 'updated', '{\"id\": 25, \"name\": \"tes rasa\", \"task\": {\"id\": 13, \"title\": \"resep makasan\", \"status\": \"pending\", \"due_date\": \"2025-12-14T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"photo_path\": \"tasks/photos/rpy8wy9zhtqaDM0A9Gz4c27pgOX33KDtwqjbk3PZ.jpg\", \"updated_at\": \"2025-12-10T03:39:33.000000Z\", \"approved_at\": \"2025-12-10T03:39:33.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"sdfsdfsf\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 10080, \"requires_approval\": true}, \"order\": 3, \"status\": \"pending\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-09T11:29:23.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 25, \"name\": \"tes rasa\", \"task\": {\"id\": 13, \"title\": \"resep makasan\", \"status\": \"pending\", \"due_date\": \"2025-12-14T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"photo_path\": \"tasks/photos/rpy8wy9zhtqaDM0A9Gz4c27pgOX33KDtwqjbk3PZ.jpg\", \"updated_at\": \"2025-12-10T03:39:33.000000Z\", \"approved_at\": \"2025-12-10T03:39:33.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"sdfsdfsf\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 10080, \"requires_approval\": true}, \"order\": 3, \"status\": \"pending\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-09T11:29:23.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 26, '2025-12-10 06:04:08', '2025-12-10 06:04:08'),
(49, 22, 'approved', '{\"id\": 22, \"name\": \"riset bahan\", \"order\": 0, \"status\": \"pending\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-09T11:29:23.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 22, \"name\": \"riset bahan\", \"order\": 0, \"status\": \"approved\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T06:04:46.000000Z\", \"approved_at\": \"2025-12-10T06:04:46.000000Z\", \"approved_by\": 13, \"rejection_reason\": null}', 13, '2025-12-10 06:04:46', '2025-12-10 06:04:46'),
(50, 23, 'approved', '{\"id\": 23, \"name\": \"riset gizi\", \"order\": 1, \"status\": \"pending\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-09T11:29:23.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 23, \"name\": \"riset gizi\", \"order\": 1, \"status\": \"approved\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T06:04:46.000000Z\", \"approved_at\": \"2025-12-10T06:04:46.000000Z\", \"approved_by\": 13, \"rejection_reason\": null}', 13, '2025-12-10 06:04:46', '2025-12-10 06:04:46'),
(51, 24, 'approved', '{\"id\": 24, \"name\": \"riset bumbu\", \"order\": 2, \"status\": \"pending\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-09T11:29:23.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 24, \"name\": \"riset bumbu\", \"order\": 2, \"status\": \"approved\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T06:04:46.000000Z\", \"approved_at\": \"2025-12-10T06:04:46.000000Z\", \"approved_by\": 13, \"rejection_reason\": null}', 13, '2025-12-10 06:04:46', '2025-12-10 06:04:46'),
(52, 25, 'approved', '{\"id\": 25, \"name\": \"tes rasa\", \"order\": 3, \"status\": \"pending\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-09T11:29:23.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 25, \"name\": \"tes rasa\", \"order\": 3, \"status\": \"approved\", \"minutes\": 2520, \"task_id\": 13, \"created_at\": \"2025-12-09T11:29:23.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T06:04:46.000000Z\", \"approved_at\": \"2025-12-10T06:04:46.000000Z\", \"approved_by\": 13, \"rejection_reason\": null}', 13, '2025-12-10 06:04:46', '2025-12-10 06:04:46'),
(53, 30, 'approved', '{\"id\": 30, \"name\": \"riset bahan\", \"order\": 0, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 15, \"created_at\": \"2025-12-10T05:01:38.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T05:01:38.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 30, \"name\": \"riset bahan\", \"order\": 0, \"status\": \"approved\", \"minutes\": 60, \"task_id\": 15, \"created_at\": \"2025-12-10T05:01:38.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T07:33:25.000000Z\", \"approved_at\": \"2025-12-10T07:33:25.000000Z\", \"approved_by\": 13, \"rejection_reason\": null}', 13, '2025-12-10 07:33:25', '2025-12-10 07:33:25'),
(54, 31, 'approved', '{\"id\": 31, \"name\": \"riset gizi\", \"order\": 1, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 15, \"created_at\": \"2025-12-10T05:01:38.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T05:01:38.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 31, \"name\": \"riset gizi\", \"order\": 1, \"status\": \"approved\", \"minutes\": 60, \"task_id\": 15, \"created_at\": \"2025-12-10T05:01:38.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T07:33:25.000000Z\", \"approved_at\": \"2025-12-10T07:33:25.000000Z\", \"approved_by\": 13, \"rejection_reason\": null}', 13, '2025-12-10 07:33:25', '2025-12-10 07:33:25'),
(55, 32, 'approved', '{\"id\": 32, \"name\": \"riset bumbu\", \"order\": 2, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 15, \"created_at\": \"2025-12-10T05:01:38.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T05:01:38.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 32, \"name\": \"riset bumbu\", \"order\": 2, \"status\": \"approved\", \"minutes\": 60, \"task_id\": 15, \"created_at\": \"2025-12-10T05:01:38.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T07:33:25.000000Z\", \"approved_at\": \"2025-12-10T07:33:25.000000Z\", \"approved_by\": 13, \"rejection_reason\": null}', 13, '2025-12-10 07:33:25', '2025-12-10 07:33:25'),
(56, 33, 'approved', '{\"id\": 33, \"name\": \"tes rasa\", \"order\": 3, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 15, \"created_at\": \"2025-12-10T05:01:38.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T05:01:38.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 33, \"name\": \"tes rasa\", \"order\": 3, \"status\": \"approved\", \"minutes\": 60, \"task_id\": 15, \"created_at\": \"2025-12-10T05:01:38.000000Z\", \"created_by\": 26, \"percentage\": 25, \"updated_at\": \"2025-12-10T07:33:25.000000Z\", \"approved_at\": \"2025-12-10T07:33:25.000000Z\", \"approved_by\": 13, \"rejection_reason\": null}', 13, '2025-12-10 07:33:25', '2025-12-10 07:33:25'),
(57, 34, 'created', NULL, '{\"id\": 34, \"name\": \"asdasdasd\", \"order\": 0, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 18, \"created_at\": \"2025-12-12T08:37:40.000000Z\", \"created_by\": 13, \"percentage\": 25, \"updated_at\": \"2025-12-12T08:37:40.000000Z\"}', 13, '2025-12-12 08:37:40', '2025-12-12 08:37:40'),
(58, 35, 'created', NULL, '{\"id\": 35, \"name\": \"asdadad\", \"order\": 1, \"status\": \"pending\", \"minutes\": 29, \"task_id\": 18, \"created_at\": \"2025-12-12T08:37:40.000000Z\", \"created_by\": 13, \"percentage\": 12, \"updated_at\": \"2025-12-12T08:37:40.000000Z\"}', 13, '2025-12-12 08:37:40', '2025-12-12 08:37:40'),
(59, 36, 'created', NULL, '{\"id\": 36, \"name\": \"asdasdasd\", \"order\": 2, \"status\": \"pending\", \"minutes\": 17, \"task_id\": 18, \"created_at\": \"2025-12-12T08:37:40.000000Z\", \"created_by\": 13, \"percentage\": 7, \"updated_at\": \"2025-12-12T08:37:40.000000Z\"}', 13, '2025-12-12 08:37:40', '2025-12-12 08:37:40'),
(60, 37, 'created', NULL, '{\"id\": 37, \"name\": \"ssdfsdfsdf\", \"order\": 3, \"status\": \"pending\", \"minutes\": 24, \"task_id\": 18, \"created_at\": \"2025-12-12T08:37:40.000000Z\", \"created_by\": 13, \"percentage\": 10, \"updated_at\": \"2025-12-12T08:37:40.000000Z\"}', 13, '2025-12-12 08:37:40', '2025-12-12 08:37:40'),
(61, 38, 'created', NULL, '{\"id\": 38, \"name\": \"sfjskfjsf\", \"order\": 4, \"status\": \"pending\", \"minutes\": 110, \"task_id\": 18, \"created_at\": \"2025-12-12T08:37:40.000000Z\", \"created_by\": 13, \"percentage\": 46, \"updated_at\": \"2025-12-12T08:37:40.000000Z\"}', 13, '2025-12-12 08:37:40', '2025-12-12 08:37:40'),
(68, 26, 'updated', '{\"id\": 26, \"name\": \"eqweqweqwe\", \"order\": 0, \"status\": \"rejected\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-10T03:46:51.000000Z\", \"approved_at\": \"2025-12-10T03:46:51.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"sdfsfsdf\"}', '{\"id\": 26, \"name\": \"eqweqweqwe\", \"order\": 0, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T07:08:19.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 26, '2025-12-15 07:08:19', '2025-12-15 07:08:19'),
(69, 27, 'updated', '{\"id\": 27, \"name\": \"qweqweqwe\", \"order\": 1, \"status\": \"rejected\", \"minutes\": 30, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-10T03:46:51.000000Z\", \"approved_at\": \"2025-12-10T03:46:51.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"sdfsfsdf\"}', '{\"id\": 27, \"name\": \"qweqweqwe\", \"order\": 1, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T07:08:19.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 26, '2025-12-15 07:08:19', '2025-12-15 07:08:19'),
(70, 28, 'updated', '{\"id\": 28, \"name\": \"qweqweqweq\", \"order\": 2, \"status\": \"rejected\", \"minutes\": 30, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-10T03:46:51.000000Z\", \"approved_at\": \"2025-12-10T03:46:51.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"sdfsfsdf\"}', '{\"id\": 28, \"name\": \"qweqweqweq\", \"order\": 2, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T07:08:19.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 26, '2025-12-15 07:08:19', '2025-12-15 07:08:19'),
(71, 29, 'updated', '{\"id\": 29, \"name\": \"qweqweq\", \"order\": 3, \"status\": \"rejected\", \"minutes\": 30, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-10T03:46:51.000000Z\", \"approved_at\": \"2025-12-10T03:46:51.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"sdfsfsdf\"}', '{\"id\": 29, \"name\": \"qweqweq\", \"order\": 3, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T07:08:19.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 26, '2025-12-15 07:08:19', '2025-12-15 07:08:19'),
(72, 7, 'updated', '{\"id\": 7, \"name\": \"asda\", \"task\": {\"id\": 12, \"title\": \"sdfsdfs\", \"status\": \"in_progress\", \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 20, \"created_at\": \"2025-12-09T08:12:23.000000Z\", \"photo_path\": \"tasks/photos/l1mgGHp9elrwy93e3wnVJwjH7TrbjiWBYH3Oj14A.jpg\", \"updated_at\": \"2025-12-09T09:22:30.000000Z\", \"approved_at\": null, \"approved_by\": null, \"assigned_by\": 13, \"assigned_to\": 26, \"description\": \"sdfsdfs\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"none\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": false}, \"order\": 2, \"status\": \"rejected\", \"minutes\": 60, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": \"20.00\", \"updated_at\": \"2025-12-09T09:04:24.000000Z\", \"approved_at\": \"2025-12-09T09:04:24.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"asdadas\"}', '{\"id\": 7, \"name\": \"asda\", \"task\": {\"id\": 12, \"title\": \"sdfsdfs\", \"status\": \"in_progress\", \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 20, \"created_at\": \"2025-12-09T08:12:23.000000Z\", \"photo_path\": \"tasks/photos/l1mgGHp9elrwy93e3wnVJwjH7TrbjiWBYH3Oj14A.jpg\", \"updated_at\": \"2025-12-09T09:22:30.000000Z\", \"approved_at\": null, \"approved_by\": null, \"assigned_by\": 13, \"assigned_to\": 26, \"description\": \"sdfsdfs\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"none\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": false}, \"order\": 2, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": \"20.00\", \"updated_at\": \"2025-12-15T07:17:22.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 26, '2025-12-15 07:17:22', '2025-12-15 07:17:22'),
(73, 26, 'rejected', '{\"id\": 26, \"name\": \"eqweqweqwe\", \"order\": 0, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T07:08:19.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 26, \"name\": \"eqweqweqwe\", \"order\": 0, \"status\": \"rejected\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T08:06:05.000000Z\", \"approved_at\": \"2025-12-15T08:06:05.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"dfsdfsdfsd\"}', 13, '2025-12-15 08:06:05', '2025-12-15 08:06:05'),
(74, 26, 'updated', '{\"id\": 26, \"name\": \"eqweqweqwe\", \"task\": {\"id\": 14, \"title\": \"asdasdasdsdfsfsdf\", \"status\": \"pending\", \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"photo_path\": \"tasks/photos/2Spy8EvraraZl9IbWCUcE3FnZX0RHz6Jp33mMLmq.jpg\", \"updated_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"asdasdassdfsdfsdf\", \"approval_note\": null, \"document_path\": \"tasks/documents/7eaH8UwW8PSSOcVHmPxXYxl8Hcoj7SJHJuLIuZ2Q.pdf\", \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": true}, \"order\": 0, \"status\": \"rejected\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T08:06:05.000000Z\", \"approved_at\": \"2025-12-15T08:06:05.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"dfsdfsdfsd\"}', '{\"id\": 26, \"name\": \"eqweqweqwe\", \"task\": {\"id\": 14, \"title\": \"asdasdasdsdfsfsdf\", \"status\": \"pending\", \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"photo_path\": \"tasks/photos/2Spy8EvraraZl9IbWCUcE3FnZX0RHz6Jp33mMLmq.jpg\", \"updated_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"asdasdassdfsdfsdf\", \"approval_note\": null, \"document_path\": \"tasks/documents/7eaH8UwW8PSSOcVHmPxXYxl8Hcoj7SJHJuLIuZ2Q.pdf\", \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": true}, \"order\": 0, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T08:06:12.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 26, '2025-12-15 08:06:12', '2025-12-15 08:06:12'),
(75, 26, 'rejected', '{\"id\": 26, \"name\": \"eqweqweqwe\", \"order\": 0, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T08:06:12.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 26, \"name\": \"eqweqweqwe\", \"order\": 0, \"status\": \"rejected\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T08:06:28.000000Z\", \"approved_at\": \"2025-12-15T08:06:28.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"sdfsdfsdfsdf\"}', 13, '2025-12-15 08:06:28', '2025-12-15 08:06:28'),
(76, 26, 'updated', '{\"id\": 26, \"name\": \"eqweqweqwe\", \"task\": {\"id\": 14, \"title\": \"asdasdasdsdfsfsdf\", \"status\": \"pending\", \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"photo_path\": \"tasks/photos/2Spy8EvraraZl9IbWCUcE3FnZX0RHz6Jp33mMLmq.jpg\", \"updated_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"asdasdassdfsdfsdf\", \"approval_note\": null, \"document_path\": \"tasks/documents/7eaH8UwW8PSSOcVHmPxXYxl8Hcoj7SJHJuLIuZ2Q.pdf\", \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": true}, \"order\": 0, \"status\": \"rejected\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T08:06:28.000000Z\", \"approved_at\": \"2025-12-15T08:06:28.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"sdfsdfsdfsdf\"}', '{\"id\": 26, \"name\": \"eqweqweqwe\", \"task\": {\"id\": 14, \"title\": \"asdasdasdsdfsfsdf\", \"status\": \"pending\", \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"photo_path\": \"tasks/photos/2Spy8EvraraZl9IbWCUcE3FnZX0RHz6Jp33mMLmq.jpg\", \"updated_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"asdasdassdfsdfsdf\", \"approval_note\": null, \"document_path\": \"tasks/documents/7eaH8UwW8PSSOcVHmPxXYxl8Hcoj7SJHJuLIuZ2Q.pdf\", \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": true}, \"order\": 0, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T08:06:37.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 26, '2025-12-15 08:06:37', '2025-12-15 08:06:37'),
(77, 26, 'approved', '{\"id\": 26, \"name\": \"eqweqweqwe\", \"task\": {\"id\": 14, \"title\": \"asdasdasdsdfsfsdf\", \"status\": \"pending\", \"assignee\": {\"id\": 26, \"name\": \"nvs_employee 1\", \"email\": \"nvs_employee1@example.com\", \"created_at\": \"2025-12-08T04:16:00.000000Z\", \"updated_at\": \"2025-12-08T04:16:00.000000Z\", \"employee_id\": 18, \"karyawan_id\": 18, \"location_id\": 5, \"phone_number\": null, \"email_verified_at\": null, \"two_factor_method\": null, \"two_factor_secret\": null, \"profile_photo_path\": null, \"two_factor_enabled\": 0, \"two_factor_backup_codes\": null, \"two_factor_confirmed_at\": null}, \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"photo_path\": \"tasks/photos/2Spy8EvraraZl9IbWCUcE3FnZX0RHz6Jp33mMLmq.jpg\", \"updated_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"asdasdassdfsdfsdf\", \"approval_note\": null, \"document_path\": \"tasks/documents/7eaH8UwW8PSSOcVHmPxXYxl8Hcoj7SJHJuLIuZ2Q.pdf\", \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": true}, \"order\": 0, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T08:06:37.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 26, \"name\": \"eqweqweqwe\", \"task\": {\"id\": 14, \"title\": \"asdasdasdsdfsfsdf\", \"status\": \"pending\", \"assignee\": {\"id\": 26, \"name\": \"nvs_employee 1\", \"email\": \"nvs_employee1@example.com\", \"created_at\": \"2025-12-08T04:16:00.000000Z\", \"updated_at\": \"2025-12-08T04:16:00.000000Z\", \"employee_id\": 18, \"karyawan_id\": 18, \"location_id\": 5, \"phone_number\": null, \"email_verified_at\": null, \"two_factor_method\": null, \"two_factor_secret\": null, \"profile_photo_path\": null, \"two_factor_enabled\": 0, \"two_factor_backup_codes\": null, \"two_factor_confirmed_at\": null}, \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"photo_path\": \"tasks/photos/2Spy8EvraraZl9IbWCUcE3FnZX0RHz6Jp33mMLmq.jpg\", \"updated_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"asdasdassdfsdfsdf\", \"approval_note\": null, \"document_path\": \"tasks/documents/7eaH8UwW8PSSOcVHmPxXYxl8Hcoj7SJHJuLIuZ2Q.pdf\", \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": true}, \"order\": 0, \"status\": \"approved\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T08:14:44.000000Z\", \"approved_at\": \"2025-12-15T08:14:44.000000Z\", \"approved_by\": 13, \"rejection_reason\": null}', 13, '2025-12-15 08:14:44', '2025-12-15 08:14:44'),
(78, 27, 'updated', '{\"id\": 27, \"name\": \"qweqweqwe\", \"task\": {\"id\": 14, \"title\": \"asdasdasdsdfsfsdf\", \"status\": \"in_progress\", \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 25, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"photo_path\": \"tasks/photos/2Spy8EvraraZl9IbWCUcE3FnZX0RHz6Jp33mMLmq.jpg\", \"updated_at\": \"2025-12-15T08:14:44.000000Z\", \"approved_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"asdasdassdfsdfsdf\", \"approval_note\": null, \"document_path\": \"tasks/documents/7eaH8UwW8PSSOcVHmPxXYxl8Hcoj7SJHJuLIuZ2Q.pdf\", \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": true}, \"order\": 1, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T07:08:19.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 27, \"name\": \"qweqweqwe\", \"task\": {\"id\": 14, \"title\": \"asdasdasdsdfsfsdf\", \"status\": \"in_progress\", \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 25, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"photo_path\": \"tasks/photos/2Spy8EvraraZl9IbWCUcE3FnZX0RHz6Jp33mMLmq.jpg\", \"updated_at\": \"2025-12-15T08:14:44.000000Z\", \"approved_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"asdasdassdfsdfsdf\", \"approval_note\": null, \"document_path\": \"tasks/documents/7eaH8UwW8PSSOcVHmPxXYxl8Hcoj7SJHJuLIuZ2Q.pdf\", \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": true}, \"order\": 1, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T07:08:19.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 26, '2025-12-15 08:32:50', '2025-12-15 08:32:50'),
(79, 27, 'rejected', '{\"id\": 27, \"name\": \"qweqweqwe\", \"order\": 1, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T07:08:19.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 27, \"name\": \"qweqweqwe\", \"order\": 1, \"status\": \"rejected\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T08:45:20.000000Z\", \"approved_at\": \"2025-12-15T08:45:20.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"fdgdfgdfgdg\"}', 13, '2025-12-15 08:45:20', '2025-12-15 08:45:20'),
(80, 27, 'updated', '{\"id\": 27, \"name\": \"qweqweqwe\", \"task\": {\"id\": 14, \"title\": \"asdasdasdsdfsfsdf\", \"status\": \"in_progress\", \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 25, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"photo_path\": \"tasks/photos/2Spy8EvraraZl9IbWCUcE3FnZX0RHz6Jp33mMLmq.jpg\", \"updated_at\": \"2025-12-15T08:14:44.000000Z\", \"approved_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"asdasdassdfsdfsdf\", \"approval_note\": null, \"document_path\": \"tasks/documents/7eaH8UwW8PSSOcVHmPxXYxl8Hcoj7SJHJuLIuZ2Q.pdf\", \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": true}, \"order\": 1, \"status\": \"rejected\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T08:45:20.000000Z\", \"approved_at\": \"2025-12-15T08:45:20.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"fdgdfgdfgdg\"}', '{\"id\": 27, \"name\": \"qweqweqwe\", \"task\": {\"id\": 14, \"title\": \"asdasdasdsdfsfsdf\", \"status\": \"in_progress\", \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 25, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"photo_path\": \"tasks/photos/2Spy8EvraraZl9IbWCUcE3FnZX0RHz6Jp33mMLmq.jpg\", \"updated_at\": \"2025-12-15T08:14:44.000000Z\", \"approved_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"asdasdassdfsdfsdf\", \"approval_note\": null, \"document_path\": \"tasks/documents/7eaH8UwW8PSSOcVHmPxXYxl8Hcoj7SJHJuLIuZ2Q.pdf\", \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": true}, \"order\": 1, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T08:45:41.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 26, '2025-12-15 08:45:41', '2025-12-15 08:45:41'),
(81, 27, 'approved', '{\"id\": 27, \"name\": \"qweqweqwe\", \"task\": {\"id\": 14, \"title\": \"asdasdasdsdfsfsdf\", \"status\": \"in_progress\", \"assignee\": {\"id\": 26, \"name\": \"nvs_employee 1\", \"email\": \"nvs_employee1@example.com\", \"created_at\": \"2025-12-08T04:16:00.000000Z\", \"updated_at\": \"2025-12-08T04:16:00.000000Z\", \"employee_id\": 18, \"karyawan_id\": 18, \"location_id\": 5, \"phone_number\": null, \"email_verified_at\": null, \"two_factor_method\": null, \"two_factor_secret\": null, \"profile_photo_path\": null, \"two_factor_enabled\": 0, \"two_factor_backup_codes\": null, \"two_factor_confirmed_at\": null}, \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 25, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"photo_path\": \"tasks/photos/2Spy8EvraraZl9IbWCUcE3FnZX0RHz6Jp33mMLmq.jpg\", \"updated_at\": \"2025-12-15T08:14:44.000000Z\", \"approved_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"asdasdassdfsdfsdf\", \"approval_note\": null, \"document_path\": \"tasks/documents/7eaH8UwW8PSSOcVHmPxXYxl8Hcoj7SJHJuLIuZ2Q.pdf\", \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": true}, \"order\": 1, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T08:45:41.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 27, \"name\": \"qweqweqwe\", \"task\": {\"id\": 14, \"title\": \"asdasdasdsdfsfsdf\", \"status\": \"in_progress\", \"assignee\": {\"id\": 26, \"name\": \"nvs_employee 1\", \"email\": \"nvs_employee1@example.com\", \"created_at\": \"2025-12-08T04:16:00.000000Z\", \"updated_at\": \"2025-12-08T04:16:00.000000Z\", \"employee_id\": 18, \"karyawan_id\": 18, \"location_id\": 5, \"phone_number\": null, \"email_verified_at\": null, \"two_factor_method\": null, \"two_factor_secret\": null, \"profile_photo_path\": null, \"two_factor_enabled\": 0, \"two_factor_backup_codes\": null, \"two_factor_confirmed_at\": null}, \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 25, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"photo_path\": \"tasks/photos/2Spy8EvraraZl9IbWCUcE3FnZX0RHz6Jp33mMLmq.jpg\", \"updated_at\": \"2025-12-15T08:14:44.000000Z\", \"approved_at\": \"2025-12-15T07:39:31.000000Z\", \"approved_by\": 13, \"assigned_by\": 26, \"assigned_to\": 26, \"description\": \"asdasdassdfsdfsdf\", \"approval_note\": null, \"document_path\": \"tasks/documents/7eaH8UwW8PSSOcVHmPxXYxl8Hcoj7SJHJuLIuZ2Q.pdf\", \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": true}, \"order\": 1, \"status\": \"approved\", \"minutes\": 60, \"task_id\": 14, \"created_at\": \"2025-12-10T03:41:09.000000Z\", \"created_by\": 26, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-15T08:49:00.000000Z\", \"approved_at\": \"2025-12-15T08:49:00.000000Z\", \"approved_by\": 13, \"rejection_reason\": null}', 13, '2025-12-15 08:49:00', '2025-12-15 08:49:00');
INSERT INTO `task_slot_history` (`id`, `task_slot_id`, `action`, `data_before`, `data_after`, `actor_id`, `created_at`, `updated_at`) VALUES
(82, 8, 'updated', '{\"id\": 8, \"name\": \"sasdasd\", \"task\": {\"id\": 12, \"title\": \"sdfsdfs\", \"status\": \"in_progress\", \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 20, \"created_at\": \"2025-12-09T08:12:23.000000Z\", \"photo_path\": \"tasks/photos/l1mgGHp9elrwy93e3wnVJwjH7TrbjiWBYH3Oj14A.jpg\", \"updated_at\": \"2025-12-09T09:22:30.000000Z\", \"approved_at\": null, \"approved_by\": null, \"assigned_by\": 13, \"assigned_to\": 26, \"description\": \"sdfsdfs\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"none\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": false}, \"order\": 3, \"status\": \"rejected\", \"minutes\": 60, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": \"20.00\", \"updated_at\": \"2025-12-09T09:04:28.000000Z\", \"approved_at\": \"2025-12-09T09:04:28.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"asdasdasd\"}', '{\"id\": 8, \"name\": \"sasdasd\", \"task\": {\"id\": 12, \"title\": \"sdfsdfs\", \"status\": \"in_progress\", \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 20, \"created_at\": \"2025-12-09T08:12:23.000000Z\", \"photo_path\": \"tasks/photos/l1mgGHp9elrwy93e3wnVJwjH7TrbjiWBYH3Oj14A.jpg\", \"updated_at\": \"2025-12-09T09:22:30.000000Z\", \"approved_at\": null, \"approved_by\": null, \"assigned_by\": 13, \"assigned_to\": 26, \"description\": \"sdfsdfs\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"none\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": false}, \"order\": 3, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": \"20.00\", \"updated_at\": \"2025-12-15T08:51:44.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 26, '2025-12-15 08:51:44', '2025-12-15 08:51:44'),
(83, 7, 'approved', '{\"id\": 7, \"name\": \"asda\", \"task\": {\"id\": 12, \"title\": \"sdfsdfs\", \"status\": \"in_progress\", \"assignee\": {\"id\": 26, \"name\": \"nvs_employee 1\", \"email\": \"nvs_employee1@example.com\", \"created_at\": \"2025-12-08T04:16:00.000000Z\", \"updated_at\": \"2025-12-08T04:16:00.000000Z\", \"employee_id\": 18, \"karyawan_id\": 18, \"location_id\": 5, \"phone_number\": null, \"email_verified_at\": null, \"two_factor_method\": null, \"two_factor_secret\": null, \"profile_photo_path\": null, \"two_factor_enabled\": 0, \"two_factor_backup_codes\": null, \"two_factor_confirmed_at\": null}, \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 20, \"created_at\": \"2025-12-09T08:12:23.000000Z\", \"photo_path\": \"tasks/photos/l1mgGHp9elrwy93e3wnVJwjH7TrbjiWBYH3Oj14A.jpg\", \"updated_at\": \"2025-12-09T09:22:30.000000Z\", \"approved_at\": null, \"approved_by\": null, \"assigned_by\": 13, \"assigned_to\": 26, \"description\": \"sdfsdfs\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"none\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": false}, \"order\": 2, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": \"20.00\", \"updated_at\": \"2025-12-15T07:17:22.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 7, \"name\": \"asda\", \"task\": {\"id\": 12, \"title\": \"sdfsdfs\", \"status\": \"in_progress\", \"assignee\": {\"id\": 26, \"name\": \"nvs_employee 1\", \"email\": \"nvs_employee1@example.com\", \"created_at\": \"2025-12-08T04:16:00.000000Z\", \"updated_at\": \"2025-12-08T04:16:00.000000Z\", \"employee_id\": 18, \"karyawan_id\": 18, \"location_id\": 5, \"phone_number\": null, \"email_verified_at\": null, \"two_factor_method\": null, \"two_factor_secret\": null, \"profile_photo_path\": null, \"two_factor_enabled\": 0, \"two_factor_backup_codes\": null, \"two_factor_confirmed_at\": null}, \"due_date\": \"2025-12-17T17:00:00.000000Z\", \"progress\": 20, \"created_at\": \"2025-12-09T08:12:23.000000Z\", \"photo_path\": \"tasks/photos/l1mgGHp9elrwy93e3wnVJwjH7TrbjiWBYH3Oj14A.jpg\", \"updated_at\": \"2025-12-09T09:22:30.000000Z\", \"approved_at\": null, \"approved_by\": null, \"assigned_by\": 13, \"assigned_to\": 26, \"description\": \"sdfsdfs\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"none\", \"approval_status\": \"approved\", \"duration_minutes\": 240, \"requires_approval\": false}, \"order\": 2, \"status\": \"approved\", \"minutes\": 60, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": \"20.00\", \"updated_at\": \"2025-12-15T09:31:59.000000Z\", \"approved_at\": \"2025-12-15T09:31:59.000000Z\", \"approved_by\": 13, \"rejection_reason\": null}', 13, '2025-12-15 09:31:59', '2025-12-15 09:31:59'),
(84, 8, 'rejected', '{\"id\": 8, \"name\": \"sasdasd\", \"order\": 3, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": \"20.00\", \"updated_at\": \"2025-12-15T08:51:44.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 8, \"name\": \"sasdasd\", \"order\": 3, \"status\": \"rejected\", \"minutes\": 60, \"task_id\": 12, \"created_at\": \"2025-12-09T09:01:55.000000Z\", \"created_by\": 13, \"percentage\": \"20.00\", \"updated_at\": \"2025-12-15T09:35:41.000000Z\", \"approved_at\": \"2025-12-15T09:35:41.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"sdfsdfsdfsdf\"}', 13, '2025-12-15 09:35:41', '2025-12-15 09:35:41'),
(90, 44, 'created', NULL, '{\"id\": 44, \"name\": \"tester\", \"order\": 2, \"status\": \"pending\", \"minutes\": 50, \"task_id\": 24, \"created_at\": \"2025-12-22T02:58:28.000000Z\", \"created_by\": 13, \"percentage\": \"20.83\", \"updated_at\": \"2025-12-22T02:58:28.000000Z\"}', 13, '2025-12-22 02:58:28', '2025-12-22 02:58:28'),
(91, 45, 'created', NULL, '{\"id\": 45, \"name\": \"tester 2\", \"order\": 3, \"status\": \"pending\", \"minutes\": 50, \"task_id\": 24, \"created_at\": \"2025-12-22T02:58:28.000000Z\", \"created_by\": 13, \"percentage\": \"20.83\", \"updated_at\": \"2025-12-22T02:58:28.000000Z\"}', 13, '2025-12-22 02:58:28', '2025-12-22 02:58:28'),
(92, 46, 'created', NULL, '{\"id\": 46, \"name\": \"acc\", \"order\": 5, \"status\": \"pending\", \"minutes\": 20, \"task_id\": 24, \"created_at\": \"2025-12-22T02:58:28.000000Z\", \"created_by\": 13, \"percentage\": \"8.33\", \"updated_at\": \"2025-12-22T02:58:28.000000Z\"}', 13, '2025-12-22 02:58:28', '2025-12-22 02:58:28'),
(93, 47, 'created', NULL, '{\"id\": 47, \"name\": \"tester 2\", \"order\": 3, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 24, \"created_at\": \"2025-12-22T02:58:28.000000Z\", \"created_by\": 13, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-22T02:58:28.000000Z\"}', 13, '2025-12-22 02:58:28', '2025-12-22 02:58:28'),
(94, 48, 'created', NULL, '{\"id\": 48, \"name\": \"finishing\", \"order\": 4, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 24, \"created_at\": \"2025-12-22T02:58:28.000000Z\", \"created_by\": 13, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-22T02:58:28.000000Z\"}', 13, '2025-12-22 02:58:28', '2025-12-22 02:58:28'),
(96, 49, 'created', NULL, '{\"id\": 49, \"name\": \"testing 2\", \"order\": 4, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 12, \"created_at\": \"2025-12-22T03:13:19.000000Z\", \"created_by\": 13, \"percentage\": \"20.00\", \"updated_at\": \"2025-12-22T03:13:19.000000Z\"}', 13, '2025-12-22 03:13:19', '2025-12-22 03:13:19'),
(97, 50, 'created', NULL, '{\"id\": 50, \"name\": \"cari bahan\", \"order\": 0, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 20, \"created_at\": \"2025-12-22T03:52:20.000000Z\", \"created_by\": 13, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-22T03:52:20.000000Z\"}', 13, '2025-12-22 03:52:20', '2025-12-22 03:52:20'),
(98, 51, 'created', NULL, '{\"id\": 51, \"name\": \"riset resep\", \"order\": 1, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 20, \"created_at\": \"2025-12-22T03:52:20.000000Z\", \"created_by\": 13, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-22T03:52:20.000000Z\"}', 13, '2025-12-22 03:52:20', '2025-12-22 03:52:20'),
(99, 52, 'created', NULL, '{\"id\": 52, \"name\": \"tester\", \"order\": 2, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 20, \"created_at\": \"2025-12-22T03:52:20.000000Z\", \"created_by\": 13, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-22T03:52:20.000000Z\"}', 13, '2025-12-22 03:52:20', '2025-12-22 03:52:20'),
(100, 53, 'created', NULL, '{\"id\": 53, \"name\": \"acc\", \"order\": 5, \"status\": \"pending\", \"minutes\": 35, \"task_id\": 20, \"created_at\": \"2025-12-22T03:52:20.000000Z\", \"created_by\": 13, \"percentage\": \"14.58\", \"updated_at\": \"2025-12-22T03:52:20.000000Z\"}', 13, '2025-12-22 03:52:20', '2025-12-22 03:52:20'),
(101, 54, 'created', NULL, '{\"id\": 54, \"name\": \"sfsdfsdf\", \"order\": 4, \"status\": \"pending\", \"minutes\": 25, \"task_id\": 20, \"created_at\": \"2025-12-22T03:52:20.000000Z\", \"created_by\": 13, \"percentage\": \"10.42\", \"updated_at\": \"2025-12-22T03:52:20.000000Z\"}', 13, '2025-12-22 03:52:20', '2025-12-22 03:52:20'),
(102, 55, 'created', NULL, '{\"id\": 55, \"name\": \"cari bahan\", \"order\": 0, \"status\": \"pending\", \"minutes\": 34, \"task_id\": 25, \"created_at\": \"2025-12-22T03:56:35.000000Z\", \"created_by\": 13, \"percentage\": \"14.17\", \"updated_at\": \"2025-12-22T03:56:35.000000Z\"}', 13, '2025-12-22 03:56:35', '2025-12-22 03:56:35'),
(103, 56, 'created', NULL, '{\"id\": 56, \"name\": \"werwerwer\", \"order\": 1, \"status\": \"pending\", \"minutes\": 34, \"task_id\": 25, \"created_at\": \"2025-12-22T03:56:35.000000Z\", \"created_by\": 13, \"percentage\": \"14.17\", \"updated_at\": \"2025-12-22T03:56:35.000000Z\"}', 13, '2025-12-22 03:56:35', '2025-12-22 03:56:35'),
(104, 57, 'created', NULL, '{\"id\": 57, \"name\": \"sdfsdfsdf\", \"order\": 2, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 25, \"created_at\": \"2025-12-22T03:56:35.000000Z\", \"created_by\": 13, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-22T03:56:35.000000Z\"}', 13, '2025-12-22 03:56:35', '2025-12-22 03:56:35'),
(105, 58, 'created', NULL, '{\"id\": 58, \"name\": \"ertertert\", \"order\": 3, \"status\": \"pending\", \"minutes\": 80, \"task_id\": 25, \"created_at\": \"2025-12-22T03:56:35.000000Z\", \"created_by\": 13, \"percentage\": \"33.33\", \"updated_at\": \"2025-12-22T03:56:35.000000Z\"}', 13, '2025-12-22 03:56:35', '2025-12-22 03:56:35'),
(106, 59, 'created', NULL, '{\"id\": 59, \"name\": \"yrtyrtyrtyr\", \"order\": 4, \"status\": \"pending\", \"minutes\": 32, \"task_id\": 25, \"created_at\": \"2025-12-22T03:56:35.000000Z\", \"created_by\": 13, \"percentage\": \"13.33\", \"updated_at\": \"2025-12-22T03:56:35.000000Z\"}', 13, '2025-12-22 03:56:35', '2025-12-22 03:56:35'),
(107, 60, 'created', NULL, '{\"id\": 60, \"name\": \"dfgdgdfg\", \"order\": 0, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 26, \"created_at\": \"2025-12-23T06:17:45.000000Z\", \"created_by\": 13, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-23T06:17:45.000000Z\"}', 13, '2025-12-23 06:17:45', '2025-12-23 06:17:45'),
(108, 61, 'created', NULL, '{\"id\": 61, \"name\": \"sdfsdfsdf\", \"order\": 1, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 26, \"created_at\": \"2025-12-23T06:17:45.000000Z\", \"created_by\": 13, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-23T06:17:45.000000Z\"}', 13, '2025-12-23 06:17:45', '2025-12-23 06:17:45'),
(109, 62, 'created', NULL, '{\"id\": 62, \"name\": \"sdfsdfsdfsdf\", \"order\": 2, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 26, \"created_at\": \"2025-12-23T06:17:45.000000Z\", \"created_by\": 13, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-23T06:17:45.000000Z\"}', 13, '2025-12-23 06:17:45', '2025-12-23 06:17:45'),
(110, 63, 'created', NULL, '{\"id\": 63, \"name\": \"dsfsfsdf\", \"order\": 3, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 26, \"created_at\": \"2025-12-23T06:17:45.000000Z\", \"created_by\": 13, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-23T06:17:45.000000Z\"}', 13, '2025-12-23 06:17:45', '2025-12-23 06:17:45'),
(111, 64, 'created', NULL, '{\"id\": 64, \"name\": \"sdfsfsfsf\", \"order\": 0, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 27, \"created_at\": \"2025-12-23T06:18:51.000000Z\", \"created_by\": 1, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-23T06:18:51.000000Z\"}', 1, '2025-12-23 06:18:51', '2025-12-23 06:18:51'),
(112, 65, 'created', NULL, '{\"id\": 65, \"name\": \"werwerwer\", \"order\": 1, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 27, \"created_at\": \"2025-12-23T06:18:51.000000Z\", \"created_by\": 1, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-23T06:18:51.000000Z\"}', 1, '2025-12-23 06:18:51', '2025-12-23 06:18:51'),
(113, 66, 'created', NULL, '{\"id\": 66, \"name\": \"dfgdfgdgfd\", \"order\": 2, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 27, \"created_at\": \"2025-12-23T06:18:51.000000Z\", \"created_by\": 1, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-23T06:18:51.000000Z\"}', 1, '2025-12-23 06:18:51', '2025-12-23 06:18:51'),
(114, 67, 'created', NULL, '{\"id\": 67, \"name\": \"dfgdfgdfg\", \"order\": 3, \"status\": \"pending\", \"minutes\": 60, \"task_id\": 27, \"created_at\": \"2025-12-23T06:18:51.000000Z\", \"created_by\": 1, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-23T06:18:51.000000Z\"}', 1, '2025-12-23 06:18:51', '2025-12-23 06:18:51'),
(115, 68, 'created', NULL, '{\"id\": 68, \"name\": \"rouf\", \"order\": 0, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 28, \"created_at\": \"2025-12-23T06:48:21.000000Z\", \"created_by\": 1, \"percentage\": \"100.00\", \"updated_at\": \"2025-12-23T06:48:21.000000Z\"}', 1, '2025-12-23 06:48:21', '2025-12-23 06:48:21'),
(116, 69, 'created', NULL, '{\"id\": 69, \"name\": \"riset\", \"order\": 0, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 29, \"created_at\": \"2025-12-23T06:50:08.000000Z\", \"created_by\": 1, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-23T06:50:08.000000Z\"}', 1, '2025-12-23 06:50:08', '2025-12-23 06:50:08'),
(117, 70, 'created', NULL, '{\"id\": 70, \"name\": \"menghubungi\", \"order\": 1, \"status\": \"pending\", \"minutes\": 15, \"task_id\": 29, \"created_at\": \"2025-12-23T06:50:08.000000Z\", \"created_by\": 1, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-23T06:50:08.000000Z\"}', 1, '2025-12-23 06:50:08', '2025-12-23 06:50:08'),
(118, 71, 'created', NULL, '{\"id\": 71, \"name\": \"di link\", \"order\": 2, \"status\": \"pending\", \"minutes\": 15, \"task_id\": 29, \"created_at\": \"2025-12-23T06:50:08.000000Z\", \"created_by\": 1, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-23T06:50:08.000000Z\"}', 1, '2025-12-23 06:50:08', '2025-12-23 06:50:08'),
(119, 69, 'updated', '{\"id\": 69, \"name\": \"riset\", \"task\": {\"id\": 29, \"title\": \"mencari vendor untuk produksi box\", \"status\": \"pending\", \"due_date\": \"2025-12-25T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-23T06:50:08.000000Z\", \"photo_path\": null, \"updated_at\": \"2025-12-23T06:51:05.000000Z\", \"approved_at\": \"2025-12-23T06:51:05.000000Z\", \"approved_by\": 13, \"assigned_by\": 1, \"assigned_to\": 1, \"description\": \"sdfsdfsdfsd\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 60, \"requires_approval\": true}, \"order\": 0, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 29, \"created_at\": \"2025-12-23T06:50:08.000000Z\", \"created_by\": 1, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-23T06:50:08.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 69, \"name\": \"riset\", \"task\": {\"id\": 29, \"title\": \"mencari vendor untuk produksi box\", \"status\": \"pending\", \"due_date\": \"2025-12-25T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-23T06:50:08.000000Z\", \"photo_path\": null, \"updated_at\": \"2025-12-23T06:51:05.000000Z\", \"approved_at\": \"2025-12-23T06:51:05.000000Z\", \"approved_by\": 13, \"assigned_by\": 1, \"assigned_to\": 1, \"description\": \"sdfsdfsdfsd\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 60, \"requires_approval\": true}, \"order\": 0, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 29, \"created_at\": \"2025-12-23T06:50:08.000000Z\", \"created_by\": 1, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-23T06:50:08.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 1, '2025-12-23 06:55:11', '2025-12-23 06:55:11'),
(120, 68, 'updated', '{\"id\": 68, \"name\": \"rouf\", \"task\": {\"id\": 28, \"title\": \"interview user\", \"status\": \"pending\", \"due_date\": \"2025-12-22T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-23T06:48:21.000000Z\", \"photo_path\": null, \"updated_at\": \"2025-12-23T06:51:11.000000Z\", \"approved_at\": \"2025-12-23T06:51:11.000000Z\", \"approved_by\": 13, \"assigned_by\": 1, \"assigned_to\": 1, \"description\": \"sdfsfs\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 30, \"requires_approval\": true}, \"order\": 0, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 28, \"created_at\": \"2025-12-23T06:48:21.000000Z\", \"created_by\": 1, \"percentage\": \"100.00\", \"updated_at\": \"2025-12-23T06:48:21.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 68, \"name\": \"rouf\", \"task\": {\"id\": 28, \"title\": \"interview user\", \"status\": \"pending\", \"due_date\": \"2025-12-22T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-23T06:48:21.000000Z\", \"photo_path\": null, \"updated_at\": \"2025-12-23T06:51:11.000000Z\", \"approved_at\": \"2025-12-23T06:51:11.000000Z\", \"approved_by\": 13, \"assigned_by\": 1, \"assigned_to\": 1, \"description\": \"sdfsfs\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 30, \"requires_approval\": true}, \"order\": 0, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 28, \"created_at\": \"2025-12-23T06:48:21.000000Z\", \"created_by\": 1, \"percentage\": \"100.00\", \"updated_at\": \"2025-12-23T06:48:21.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 1, '2025-12-23 06:56:30', '2025-12-23 06:56:30'),
(121, 68, 'approved', '{\"id\": 68, \"name\": \"rouf\", \"order\": 0, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 28, \"created_at\": \"2025-12-23T06:48:21.000000Z\", \"created_by\": 1, \"percentage\": \"100.00\", \"updated_at\": \"2025-12-23T06:48:21.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 68, \"name\": \"rouf\", \"order\": 0, \"status\": \"approved\", \"minutes\": 30, \"task_id\": 28, \"created_at\": \"2025-12-23T06:48:21.000000Z\", \"created_by\": 1, \"percentage\": \"100.00\", \"updated_at\": \"2025-12-27T05:08:09.000000Z\", \"approved_at\": \"2025-12-27T05:08:09.000000Z\", \"approved_by\": 13, \"rejection_reason\": null}', 13, '2025-12-27 05:08:09', '2025-12-27 05:08:09'),
(122, 69, 'approved', '{\"id\": 69, \"name\": \"riset\", \"order\": 0, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 29, \"created_at\": \"2025-12-23T06:50:08.000000Z\", \"created_by\": 1, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-23T06:50:08.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 69, \"name\": \"riset\", \"order\": 0, \"status\": \"approved\", \"minutes\": 30, \"task_id\": 29, \"created_at\": \"2025-12-23T06:50:08.000000Z\", \"created_by\": 1, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:09:34.000000Z\", \"approved_at\": \"2025-12-27T05:09:34.000000Z\", \"approved_by\": 13, \"rejection_reason\": null}', 13, '2025-12-27 05:09:34', '2025-12-27 05:09:34'),
(123, 70, 'approved', '{\"id\": 70, \"name\": \"menghubungi\", \"order\": 1, \"status\": \"pending\", \"minutes\": 15, \"task_id\": 29, \"created_at\": \"2025-12-23T06:50:08.000000Z\", \"created_by\": 1, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-23T06:50:08.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 70, \"name\": \"menghubungi\", \"order\": 1, \"status\": \"approved\", \"minutes\": 15, \"task_id\": 29, \"created_at\": \"2025-12-23T06:50:08.000000Z\", \"created_by\": 1, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-27T05:09:34.000000Z\", \"approved_at\": \"2025-12-27T05:09:34.000000Z\", \"approved_by\": 13, \"rejection_reason\": null}', 13, '2025-12-27 05:09:34', '2025-12-27 05:09:34'),
(124, 71, 'approved', '{\"id\": 71, \"name\": \"di link\", \"order\": 2, \"status\": \"pending\", \"minutes\": 15, \"task_id\": 29, \"created_at\": \"2025-12-23T06:50:08.000000Z\", \"created_by\": 1, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-23T06:50:08.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 71, \"name\": \"di link\", \"order\": 2, \"status\": \"approved\", \"minutes\": 15, \"task_id\": 29, \"created_at\": \"2025-12-23T06:50:08.000000Z\", \"created_by\": 1, \"percentage\": \"25.00\", \"updated_at\": \"2025-12-27T05:09:34.000000Z\", \"approved_at\": \"2025-12-27T05:09:34.000000Z\", \"approved_by\": 13, \"rejection_reason\": null}', 13, '2025-12-27 05:09:34', '2025-12-27 05:09:34'),
(125, 72, 'created', NULL, '{\"id\": 72, \"name\": \"fghfghfh\", \"order\": 0, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 30, \"created_at\": \"2025-12-27T05:15:32.000000Z\", \"created_by\": 26, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:15:32.000000Z\"}', 26, '2025-12-27 05:15:32', '2025-12-27 05:15:32'),
(126, 73, 'created', NULL, '{\"id\": 73, \"name\": \"riset resep\", \"order\": 1, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 30, \"created_at\": \"2025-12-27T05:15:32.000000Z\", \"created_by\": 26, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:15:32.000000Z\"}', 26, '2025-12-27 05:15:32', '2025-12-27 05:15:32'),
(127, 74, 'created', NULL, '{\"id\": 74, \"name\": \"yuiyuiyui\", \"order\": 0, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:17:46.000000Z\"}', 18, '2025-12-27 05:17:46', '2025-12-27 05:17:46'),
(128, 75, 'created', NULL, '{\"id\": 75, \"name\": \"tytyutyut\", \"order\": 1, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:17:46.000000Z\"}', 18, '2025-12-27 05:17:46', '2025-12-27 05:17:46'),
(129, 74, 'rejected', '{\"id\": 74, \"name\": \"yuiyuiyui\", \"order\": 0, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:17:46.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 74, \"name\": \"yuiyuiyui\", \"order\": 0, \"status\": \"rejected\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:25:18.000000Z\", \"approved_at\": \"2025-12-27T05:25:18.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"fghdfgdgdfgdfgdfgdfgdgdfg\"}', 13, '2025-12-27 05:25:18', '2025-12-27 05:25:18'),
(130, 75, 'rejected', '{\"id\": 75, \"name\": \"tytyutyut\", \"order\": 1, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:17:46.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 75, \"name\": \"tytyutyut\", \"order\": 1, \"status\": \"rejected\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:25:18.000000Z\", \"approved_at\": \"2025-12-27T05:25:18.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"fghdfgdgdfgdfgdfgdfgdgdfg\"}', 13, '2025-12-27 05:25:18', '2025-12-27 05:25:18'),
(131, 74, 'updated', '{\"id\": 74, \"name\": \"yuiyuiyui\", \"order\": 0, \"status\": \"rejected\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:25:18.000000Z\", \"approved_at\": \"2025-12-27T05:25:18.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"fghdfgdgdfgdfgdfgdfgdgdfg\"}', '{\"id\": 74, \"name\": \"yuiyuiyui\", \"order\": 0, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:25:55.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 18, '2025-12-27 05:25:55', '2025-12-27 05:25:55'),
(132, 75, 'updated', '{\"id\": 75, \"name\": \"tytyutyut\", \"order\": 1, \"status\": \"rejected\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:25:18.000000Z\", \"approved_at\": \"2025-12-27T05:25:18.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"fghdfgdgdfgdfgdfgdfgdgdfg\"}', '{\"id\": 75, \"name\": \"tytyutyut\", \"order\": 1, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:25:55.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 18, '2025-12-27 05:25:55', '2025-12-27 05:25:55'),
(133, 74, 'updated', '{\"id\": 74, \"name\": \"yuiyuiyui\", \"task\": {\"id\": 31, \"title\": \"ghjghjfghfhfgh\", \"status\": \"pending\", \"due_date\": \"2025-12-29T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"photo_path\": null, \"updated_at\": \"2025-12-27T05:26:28.000000Z\", \"approved_at\": \"2025-12-27T05:26:28.000000Z\", \"approved_by\": 13, \"assigned_by\": 18, \"assigned_to\": 18, \"description\": \"ghjghjghjfghfgh\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 60, \"requires_approval\": true}, \"order\": 0, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:25:55.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 74, \"name\": \"yuiyuiyui\", \"task\": {\"id\": 31, \"title\": \"ghjghjfghfhfgh\", \"status\": \"pending\", \"due_date\": \"2025-12-29T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"photo_path\": null, \"updated_at\": \"2025-12-27T05:26:28.000000Z\", \"approved_at\": \"2025-12-27T05:26:28.000000Z\", \"approved_by\": 13, \"assigned_by\": 18, \"assigned_to\": 18, \"description\": \"ghjghjghjfghfgh\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 60, \"requires_approval\": true}, \"order\": 0, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:25:55.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 18, '2025-12-27 05:32:58', '2025-12-27 05:32:58'),
(134, 74, 'rejected', '{\"id\": 74, \"name\": \"yuiyuiyui\", \"order\": 0, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:25:55.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 74, \"name\": \"yuiyuiyui\", \"order\": 0, \"status\": \"rejected\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:33:30.000000Z\", \"approved_at\": \"2025-12-27T05:33:30.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"gdgfgdfgdfg\"}', 13, '2025-12-27 05:33:30', '2025-12-27 05:33:30'),
(135, 74, 'updated', '{\"id\": 74, \"name\": \"yuiyuiyui\", \"task\": {\"id\": 31, \"title\": \"ghjghjfghfhfgh\", \"status\": \"pending\", \"due_date\": \"2025-12-29T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"photo_path\": null, \"updated_at\": \"2025-12-27T05:26:28.000000Z\", \"approved_at\": \"2025-12-27T05:26:28.000000Z\", \"approved_by\": 13, \"assigned_by\": 18, \"assigned_to\": 18, \"description\": \"ghjghjghjfghfgh\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 60, \"requires_approval\": true}, \"order\": 0, \"status\": \"rejected\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:33:30.000000Z\", \"approved_at\": \"2025-12-27T05:33:30.000000Z\", \"approved_by\": 13, \"rejection_reason\": \"gdgfgdfgdfg\"}', '{\"id\": 74, \"name\": \"yuiyuiyui\", \"task\": {\"id\": 31, \"title\": \"ghjghjfghfhfgh\", \"status\": \"pending\", \"due_date\": \"2025-12-29T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"photo_path\": null, \"updated_at\": \"2025-12-27T05:26:28.000000Z\", \"approved_at\": \"2025-12-27T05:26:28.000000Z\", \"approved_by\": 13, \"assigned_by\": 18, \"assigned_to\": 18, \"description\": \"ghjghjghjfghfgh\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 60, \"requires_approval\": true}, \"order\": 0, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:33:47.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', 18, '2025-12-27 05:33:47', '2025-12-27 05:33:47'),
(136, 74, 'approved', '{\"id\": 74, \"name\": \"yuiyuiyui\", \"task\": {\"id\": 31, \"title\": \"ghjghjfghfhfgh\", \"status\": \"pending\", \"assignee\": {\"id\": 18, \"name\": \"Employee 1\", \"email\": \"employee1@example.com\", \"created_at\": \"2025-10-23T09:54:50.000000Z\", \"updated_at\": \"2025-10-23T09:54:50.000000Z\", \"employee_id\": null, \"karyawan_id\": null, \"location_id\": 1, \"phone_number\": null, \"email_verified_at\": null, \"two_factor_method\": null, \"two_factor_secret\": null, \"profile_photo_path\": null, \"two_factor_enabled\": 0, \"two_factor_backup_codes\": null, \"two_factor_confirmed_at\": null}, \"due_date\": \"2025-12-29T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"photo_path\": null, \"updated_at\": \"2025-12-27T05:26:28.000000Z\", \"approved_at\": \"2025-12-27T05:26:28.000000Z\", \"approved_by\": 13, \"assigned_by\": 18, \"assigned_to\": 18, \"description\": \"ghjghjghjfghfgh\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 60, \"requires_approval\": true}, \"order\": 0, \"status\": \"pending\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:33:47.000000Z\", \"approved_at\": null, \"approved_by\": null, \"rejection_reason\": null}', '{\"id\": 74, \"name\": \"yuiyuiyui\", \"task\": {\"id\": 31, \"title\": \"ghjghjfghfhfgh\", \"status\": \"pending\", \"assignee\": {\"id\": 18, \"name\": \"Employee 1\", \"email\": \"employee1@example.com\", \"created_at\": \"2025-10-23T09:54:50.000000Z\", \"updated_at\": \"2025-10-23T09:54:50.000000Z\", \"employee_id\": null, \"karyawan_id\": null, \"location_id\": 1, \"phone_number\": null, \"email_verified_at\": null, \"two_factor_method\": null, \"two_factor_secret\": null, \"profile_photo_path\": null, \"two_factor_enabled\": 0, \"two_factor_backup_codes\": null, \"two_factor_confirmed_at\": null}, \"due_date\": \"2025-12-29T17:00:00.000000Z\", \"progress\": 0, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"photo_path\": null, \"updated_at\": \"2025-12-27T05:26:28.000000Z\", \"approved_at\": \"2025-12-27T05:26:28.000000Z\", \"approved_by\": 13, \"assigned_by\": 18, \"assigned_to\": 18, \"description\": \"ghjghjghjfghfgh\", \"approval_note\": null, \"document_path\": null, \"approval_level\": \"super_admin\", \"approval_status\": \"approved\", \"duration_minutes\": 60, \"requires_approval\": true}, \"order\": 0, \"status\": \"approved\", \"minutes\": 30, \"task_id\": 31, \"created_at\": \"2025-12-27T05:17:46.000000Z\", \"created_by\": 18, \"percentage\": \"50.00\", \"updated_at\": \"2025-12-27T05:34:01.000000Z\", \"approved_at\": \"2025-12-27T05:34:01.000000Z\", \"approved_by\": 13, \"rejection_reason\": null}', 13, '2025-12-27 05:34:01', '2025-12-27 05:34:01'),
(137, 76, 'approved', NULL, '{\"id\": 76, \"name\": \"Selesai\", \"order\": 0, \"status\": \"approved\", \"minutes\": 120, \"task_id\": 32, \"created_at\": \"2026-03-05T05:21:15.000000Z\", \"created_by\": 13, \"percentage\": \"100.00\", \"updated_at\": \"2026-03-05T05:21:15.000000Z\", \"approved_at\": \"2026-03-05T05:21:15.000000Z\", \"approved_by\": 13}', 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(138, 77, 'approved', NULL, '{\"id\": 77, \"name\": \"Selesai\", \"order\": 0, \"status\": \"approved\", \"minutes\": 120, \"task_id\": 33, \"created_at\": \"2026-03-05T05:21:15.000000Z\", \"created_by\": 13, \"percentage\": \"100.00\", \"updated_at\": \"2026-03-05T05:21:15.000000Z\", \"approved_at\": \"2026-03-05T05:21:15.000000Z\", \"approved_by\": 13}', 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(139, 78, 'approved', NULL, '{\"id\": 78, \"name\": \"Selesai\", \"order\": 0, \"status\": \"approved\", \"minutes\": 120, \"task_id\": 34, \"created_at\": \"2026-03-05T05:21:15.000000Z\", \"created_by\": 13, \"percentage\": \"100.00\", \"updated_at\": \"2026-03-05T05:21:15.000000Z\", \"approved_at\": \"2026-03-05T05:21:15.000000Z\", \"approved_by\": 13}', 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(140, 79, 'approved', NULL, '{\"id\": 79, \"name\": \"Selesai\", \"order\": 0, \"status\": \"approved\", \"minutes\": 120, \"task_id\": 35, \"created_at\": \"2026-03-05T05:21:15.000000Z\", \"created_by\": 13, \"percentage\": \"100.00\", \"updated_at\": \"2026-03-05T05:21:15.000000Z\", \"approved_at\": \"2026-03-05T05:21:15.000000Z\", \"approved_by\": 13}', 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(141, 80, 'approved', NULL, '{\"id\": 80, \"name\": \"Selesai\", \"order\": 0, \"status\": \"approved\", \"minutes\": 120, \"task_id\": 36, \"created_at\": \"2026-03-05T05:21:15.000000Z\", \"created_by\": 13, \"percentage\": \"100.00\", \"updated_at\": \"2026-03-05T05:21:15.000000Z\", \"approved_at\": \"2026-03-05T05:21:15.000000Z\", \"approved_by\": 13}', 13, '2026-03-05 05:21:15', '2026-03-05 05:21:15'),
(142, 81, 'created', NULL, '{\"id\": 81, \"name\": \"tester\", \"order\": 0, \"status\": \"pending\", \"minutes\": 150, \"task_id\": 37, \"created_at\": \"2026-03-05T06:31:17.000000Z\", \"created_by\": 13, \"percentage\": \"100.00\", \"updated_at\": \"2026-03-05T06:31:17.000000Z\"}', 13, '2026-03-05 06:31:17', '2026-03-05 06:31:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `two_factor_backup_codes` json DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `employee_id` bigint UNSIGNED DEFAULT NULL,
  `karyawan_id` bigint UNSIGNED DEFAULT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone_number`, `profile_photo_path`, `two_factor_secret`, `two_factor_method`, `two_factor_enabled`, `two_factor_backup_codes`, `two_factor_confirmed_at`, `email_verified_at`, `employee_id`, `karyawan_id`, `location_id`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'JJ_MAIN Employee 1', 'jj_main.employee1@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 3, 3, 1, '$2y$12$a1i2PM8o07dCZgsxO84yyeId6Kq/i/LJIE3bNjtUG7MG54YUOWptC', NULL, '2025-10-23 09:54:45', '2025-10-23 09:54:45'),
(2, 'JJ_MAIN Employee 2', 'jj_main.employee2@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 4, 4, 1, '$2y$12$2I0zN2vh2jN4LbfcKvA/Rel4sQIBZT9OYff5Z1kof6akyNvFddewu', NULL, '2025-10-23 09:54:45', '2025-10-23 09:54:45'),
(3, 'JJ_MAIN Employee 3', 'jj_main.employee3@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 5, 5, 1, '$2y$12$YyuZsDqp3J/ZglFBpplZt.nkTf92E59Ho.gM/J2hwX8k.BkIzPIDu', NULL, '2025-10-23 09:54:46', '2025-10-23 09:54:46'),
(4, 'FACT_BDG Employee 1', 'fact_bdg.employee1@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 6, 6, 2, '$2y$12$o67pEzmaTqO8nFPDZZsqeOLPa/gGoJx64Qk6DKnUSZIEU0OJqJtXG', NULL, '2025-10-23 09:54:46', '2025-10-23 09:54:46'),
(5, 'FACT_BDG Employee 2', 'fact_bdg.employee2@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 7, 7, 2, '$2y$12$H5xy8v5nMMVhRmsXaAH3Pu1mgn/l4aMwj7KUlrTF1CWAWkLenx6qy', NULL, '2025-10-23 09:54:46', '2025-10-23 09:54:46'),
(6, 'FACT_BDG Employee 3', 'fact_bdg.employee3@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 8, 8, 2, '$2y$12$sEq0EydRNhqSMSjsJ1oXQes9lNDYVU0/t0OPlmkm3fQv/MS6tGXcu', NULL, '2025-10-23 09:54:46', '2025-10-23 09:54:46'),
(7, 'BRANCH_SBY Employee 1', 'branch_sby.employee1@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 9, 9, 3, '$2y$12$l.OEbRR3p.l9OVx6FT2M2u0.RiqxtF.a7iB0Iz4Fmu8KOqTcC87fq', NULL, '2025-10-23 09:54:47', '2025-10-23 09:54:47'),
(8, 'BRANCH_SBY Employee 2', 'branch_sby.employee2@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 10, 10, 3, '$2y$12$yV/6PoJw.oSmrCSkVHouduOXUem7ZZnModYrQqLzLpKnFXhEaLSlq', NULL, '2025-10-23 09:54:47', '2025-10-23 09:54:47'),
(9, 'BRANCH_SBY Employee 3', 'branch_sby.employee3@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 11, 11, 3, '$2y$12$AQM7a7VG4iPJg799EyEgAuWMt8eWs5oAXHvdjYoF1bPFGEm8Rn8IW', NULL, '2025-10-23 09:54:47', '2025-10-23 09:54:47'),
(10, 'WH_SMG Employee 1', 'wh_smg.employee1@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 12, 12, 4, '$2y$12$Z8MZ3Q0cJwjDElKEIR3IJeGmbwaHpg3GjLwQB/xWqsJrlJnsnDZr6', NULL, '2025-10-23 09:54:48', '2025-10-23 09:54:48'),
(11, 'WH_SMG Employee 2', 'wh_smg.employee2@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 13, 13, 4, '$2y$12$GhgJoe9BfkwaozTGrd5n6Oy0ktLqohaaq4GlaohDQukqmhlEFkpB.', NULL, '2025-10-23 09:54:48', '2025-10-23 09:54:48'),
(12, 'WH_SMG Employee 3', 'wh_smg.employee3@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 14, 14, 4, '$2y$12$6lLpBZI1bHCtrGfQ7yPjxuZYC/wULynk2UElovpbro99wx7hwwPPW', NULL, '2025-10-23 09:54:48', '2025-10-23 09:54:48'),
(13, 'Super Admin 1', 'superadmin1@example.com', NULL, 'avatars/fMdy3zNJCgTnFnCRSGy25rd4Mrjk19D3cx3TkpzC.png', 'eyJpdiI6InY0ME1UbTVDSlh0ajVqZWEzOWlyelE9PSIsInZhbHVlIjoicElZcEhwZy9WS051TURIVFpORGRqMS9tMVoxdHZqQnpKd21ZSzdKQzVJTT0iLCJtYWMiOiIwMTVmMzE1MTFhN2M0ZmNiZjE1Mzk4YjAyYTRmNGQ0YWRkZTRjNWY1Mzg5ODZhNTVhMDFjNTU0ZDVjZjAwMDA1IiwidGFnIjoiIn0=', 'email', 0, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$0u.ZizwFyVncgoWKO8O4juR3UOoCsLpkEBswQryMiS5.1fN0iJReS', NULL, '2025-10-23 09:54:48', '2025-12-23 04:51:06'),
(14, 'Super Admin 2', 'superadmin2@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$GNBDvGWLx3F/kwXTTpKQhujqzwyODG.HhqAOSROiAhn.MH1oh0ZI2', NULL, '2025-10-23 09:54:49', '2025-10-23 09:54:49'),
(15, 'Super Admin 3', 'superadmin3@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$DJcWLtZDaUcHpB6uMsRk..4oOOjrYx2XBkbbbcCLOpaftLWb6asvu', NULL, '2025-10-23 09:54:49', '2025-10-23 09:54:49'),
(16, 'Admin Lokasi 1', 'adminlokasi1@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 3, '$2y$12$8M0VFU26bk.hs/lUXJa5O.jqEWtmQgRBIFjtPEoeJJl/YvL/hg7ca', NULL, '2025-10-23 09:54:49', '2025-10-23 09:54:49'),
(17, 'Admin Lokasi 2', 'adminlokasi2@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 2, '$2y$12$2lJnxZltrmVwPK2LtDq.huAyFnTgK.J3ifqAFTFGnkgJQJdL9u9qm', NULL, '2025-10-23 09:54:49', '2025-10-23 09:54:49'),
(18, 'Employee 1', 'employee1@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, '$2y$12$Jz994Pox6IO9P/h7q0iOJOvOerXh4UWYhatLCgfWVy5Bwr3Xd45Q2', NULL, '2025-10-23 09:54:50', '2025-10-23 09:54:50'),
(19, 'Employee 2', 'employee2@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 2, '$2y$12$83IOUD9k17aeejWMDNqwa.b4uL.sQl3U5M2za9rv1HZ.xIs9rhLaq', NULL, '2025-10-23 09:54:50', '2025-10-23 09:54:50'),
(20, 'Employee 3', 'employee3@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, '$2y$12$o47pi0JoVfYOu1iWKlcmL.C1mjsQvcOfGZaC0erkADL8aQIg5SWMG', NULL, '2025-10-23 09:54:50', '2025-10-23 09:54:50'),
(21, 'Employee 4', 'employee4@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 4, '$2y$12$ovaU.6Ho8z/tIw3IMsOPruSoCHkehsTRXy8VUYeY99tJI.WUH8FWC', NULL, '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(22, 'Employee 5', 'employee5@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, '$2y$12$wRda2EwXuKMambgH33ya1uRmcuqvTQbb0xxl1zHesDH6hVQi/iZ.q', NULL, '2025-10-23 09:54:51', '2025-10-23 09:54:51'),
(23, 'KOS Employee 1', 'kos.employee1@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 15, 15, 4, '$2y$12$dWB4z45laSpBk.4TXbJTruxnUjAvI3LpvZNbrfVnjqvytstMMfmPi', NULL, '2025-10-24 14:15:12', '2025-10-24 14:15:12'),
(24, 'KOS Employee 2', 'kos.employee2@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 16, 16, 4, '$2y$12$OoyuSJhAPt/lffXaV/7JdODuxDzMrPZCKD.F3jYp6q1i0nWD6vYzq', NULL, '2025-10-24 14:15:12', '2025-10-24 14:15:12'),
(25, 'KOS Employee 3', 'kos.employee3@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 17, 17, 4, '$2y$12$1FzNZ119VonmGh/EOv88DOKX9Y79TJb/vewI02jU1.glcAXYaSAiK', NULL, '2025-10-24 14:15:12', '2025-10-24 14:15:12'),
(26, 'nvs_employee 1', 'nvs_employee1@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 18, 18, 5, '$2y$12$FukEYD0IQf1qJtPvmREJe.DOgiwFz9twRyIhRbc9BaVW7ZKKXhCfG', NULL, '2025-12-08 04:16:00', '2025-12-08 04:16:00'),
(27, 'nvs_employee 2', 'nvs_employee2@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 19, 19, 5, '$2y$12$FPGqpSAeK1yW92xAhIrTEeqba/woZRtRIbLD.XQ5VbgAGyZWkuU4.', NULL, '2025-12-08 04:16:20', '2025-12-08 04:16:20'),
(28, 'nvs_employee 3', 'nvs_employee3@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 20, 20, 5, '$2y$12$Oudy2ga2chr8w7w3zsgmj.UwK2JmO9E8MjQrXMOShUVsoiFO1C7BC', NULL, '2025-12-08 04:16:43', '2025-12-08 04:16:43'),
(29, 'John Doe', 'john@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 1, 5, '$2y$12$CcG0SYR2YYn9gfZXsy7cXef24Z0xAw7U.PgCTbVJZJ8epxEWA56Ea', NULL, '2025-12-08 04:29:16', '2025-12-08 04:29:16'),
(30, 'Jane Smith', 'jane@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 2, 2, 5, '$2y$12$8KDpRlVm17Ge0fZ2X.iQHedddaeHRBMnsm8p3xkhXEPjSnCTMBNlq', NULL, '2025-12-08 04:29:58', '2025-12-08 04:29:58'),
(31, 'nvs_employee 4', 'nvs_employee4@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 21, 21, 5, '$2y$12$/Gs4AEKzq.nVzTOvd/dR2.fjhri5eq7cp5s6fJUSE57Cnp2mJ8ExW', NULL, '2025-12-29 06:14:16', '2025-12-29 06:14:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `weekly_offs`
--

CREATE TABLE `weekly_offs` (
  `id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `day_of_week` enum('sunday','monday','tuesday','wednesday','thursday','friday','saturday') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `weekly_offs`
--

INSERT INTO `weekly_offs` (`id`, `location_id`, `user_id`, `day_of_week`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'sunday', '2025-12-08 07:19:39', '2025-12-08 07:19:39'),
(2, 3, NULL, 'sunday', '2025-12-08 07:19:47', '2025-12-08 07:19:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `weekly_rosters`
--

CREATE TABLE `weekly_rosters` (
  `id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `location_shift_id` bigint UNSIGNED NOT NULL,
  `week_start` date NOT NULL,
  `week_end` date NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `weekly_rosters`
--

INSERT INTO `weekly_rosters` (`id`, `location_id`, `location_shift_id`, `week_start`, `week_end`, `locked`, `meta`, `created_at`, `updated_at`) VALUES
(12, 2, 4, '2025-12-08', '2025-12-14', 0, '{\"assignment_log\": [{\"date\": \"2025-12-08\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 0}, {\"date\": \"2025-12-08\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 1}, {\"date\": \"2025-12-08\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 2}, {\"date\": \"2025-12-08\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 3}, {\"date\": \"2025-12-09\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 0}, {\"date\": \"2025-12-09\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 1}, {\"date\": \"2025-12-09\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 2}, {\"date\": \"2025-12-09\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 3}, {\"date\": \"2025-12-10\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 0}, {\"date\": \"2025-12-10\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 1}, {\"date\": \"2025-12-10\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 2}, {\"date\": \"2025-12-10\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 3}, {\"date\": \"2025-12-11\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 0}, {\"date\": \"2025-12-11\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 1}, {\"date\": \"2025-12-11\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 2}, {\"date\": \"2025-12-11\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 3}, {\"date\": \"2025-12-12\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 0}, {\"date\": \"2025-12-12\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 1}, {\"date\": \"2025-12-12\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 2}, {\"date\": \"2025-12-12\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 3}, {\"date\": \"2025-12-13\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 0}, {\"date\": \"2025-12-13\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 1}, {\"date\": \"2025-12-13\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 2}, {\"date\": \"2025-12-13\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 3}, {\"date\": \"2025-12-14\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 0}, {\"date\": \"2025-12-14\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 1}, {\"date\": \"2025-12-14\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 2}, {\"date\": \"2025-12-14\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 3}], \"rotation_pointer\": 28, \"weekly_off_every\": \"6\", \"weekly_off_label\": \"OFF\", \"weekly_shift_counts\": {\"4\": 7, \"5\": 7, \"6\": 7, \"19\": 7}}', '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(13, 4, 10, '2025-12-08', '2025-12-14', 0, '{\"assignment_log\": [{\"date\": \"2025-12-08\", \"reason\": \"fair_rotation\", \"user_id\": 10, \"slot_index\": 0}, {\"date\": \"2025-12-08\", \"reason\": \"fair_rotation\", \"user_id\": 11, \"slot_index\": 1}, {\"date\": \"2025-12-08\", \"reason\": \"fair_rotation\", \"user_id\": 12, \"slot_index\": 2}, {\"date\": \"2025-12-09\", \"reason\": \"fair_rotation\", \"user_id\": 23, \"slot_index\": 0}, {\"date\": \"2025-12-09\", \"reason\": \"fair_rotation\", \"user_id\": 24, \"slot_index\": 1}, {\"date\": \"2025-12-09\", \"reason\": \"fair_rotation\", \"user_id\": 21, \"slot_index\": 2}, {\"date\": \"2025-12-10\", \"reason\": \"fair_rotation\", \"user_id\": 25, \"slot_index\": 0}, {\"date\": \"2025-12-10\", \"reason\": \"fair_rotation\", \"user_id\": 10, \"slot_index\": 1}, {\"date\": \"2025-12-10\", \"reason\": \"fair_rotation\", \"user_id\": 12, \"slot_index\": 2}, {\"date\": \"2025-12-11\", \"reason\": \"fair_rotation\", \"user_id\": 23, \"slot_index\": 0}, {\"date\": \"2025-12-11\", \"reason\": \"fair_rotation\", \"user_id\": 24, \"slot_index\": 1}, {\"date\": \"2025-12-11\", \"reason\": \"fair_rotation\", \"user_id\": 25, \"slot_index\": 2}, {\"date\": \"2025-12-12\", \"reason\": \"fair_rotation\", \"user_id\": 11, \"slot_index\": 0}, {\"date\": \"2025-12-12\", \"reason\": \"fair_rotation\", \"user_id\": 10, \"slot_index\": 1}, {\"date\": \"2025-12-12\", \"reason\": \"fair_rotation\", \"user_id\": 11, \"slot_index\": 2}, {\"date\": \"2025-12-13\", \"reason\": \"fair_rotation\", \"user_id\": 24, \"slot_index\": 0}, {\"date\": \"2025-12-13\", \"reason\": \"fair_rotation\", \"user_id\": 21, \"slot_index\": 1}, {\"date\": \"2025-12-13\", \"reason\": \"fair_rotation\", \"user_id\": 21, \"slot_index\": 2}, {\"date\": \"2025-12-14\", \"reason\": \"fair_rotation\", \"user_id\": 23, \"slot_index\": 0}, {\"date\": \"2025-12-14\", \"reason\": \"fair_rotation\", \"user_id\": 12, \"slot_index\": 1}, {\"date\": \"2025-12-14\", \"reason\": \"fair_rotation\", \"user_id\": 25, \"slot_index\": 2}], \"rotation_pointer\": 21, \"weekly_off_every\": \"6\", \"weekly_off_label\": \"OFF\", \"weekly_shift_counts\": {\"10\": 3, \"11\": 3, \"12\": 3, \"21\": 3, \"23\": 3, \"24\": 3, \"25\": 3}}', '2025-12-08 06:53:56', '2025-12-08 06:53:57'),
(14, 5, 14, '2025-12-08', '2025-12-14', 0, '{\"assignment_log\": [{\"date\": \"2025-12-08\", \"reason\": \"fair_rotation\", \"user_id\": 26, \"slot_index\": 0}, {\"date\": \"2025-12-08\", \"reason\": \"fair_rotation\", \"user_id\": 27, \"slot_index\": 1}, {\"date\": \"2025-12-08\", \"reason\": \"fair_rotation\", \"user_id\": 28, \"slot_index\": 2}, {\"date\": \"2025-12-09\", \"reason\": \"fair_rotation\", \"user_id\": 30, \"slot_index\": 0}, {\"date\": \"2025-12-09\", \"reason\": \"fair_rotation\", \"user_id\": 29, \"slot_index\": 1}, {\"date\": \"2025-12-09\", \"reason\": \"fair_rotation\", \"user_id\": 30, \"slot_index\": 2}, {\"date\": \"2025-12-10\", \"reason\": \"fair_rotation\", \"user_id\": 29, \"slot_index\": 0}, {\"date\": \"2025-12-10\", \"reason\": \"fair_rotation\", \"user_id\": 26, \"slot_index\": 1}, {\"date\": \"2025-12-10\", \"reason\": \"fair_rotation\", \"user_id\": 28, \"slot_index\": 2}, {\"date\": \"2025-12-11\", \"reason\": \"fair_rotation\", \"user_id\": 27, \"slot_index\": 0}, {\"date\": \"2025-12-11\", \"reason\": \"fair_rotation\", \"user_id\": 27, \"slot_index\": 1}, {\"date\": \"2025-12-11\", \"reason\": \"fair_rotation\", \"user_id\": 29, \"slot_index\": 2}, {\"date\": \"2025-12-12\", \"reason\": \"fair_rotation\", \"user_id\": 26, \"slot_index\": 0}, {\"date\": \"2025-12-12\", \"reason\": \"fair_rotation\", \"user_id\": 28, \"slot_index\": 1}, {\"date\": \"2025-12-12\", \"reason\": \"fair_rotation\", \"user_id\": 30, \"slot_index\": 2}, {\"date\": \"2025-12-13\", \"reason\": \"fair_rotation\", \"user_id\": 26, \"slot_index\": 0}, {\"date\": \"2025-12-13\", \"reason\": \"fair_rotation\", \"user_id\": 29, \"slot_index\": 1}, {\"date\": \"2025-12-13\", \"reason\": \"fair_rotation\", \"user_id\": 27, \"slot_index\": 2}, {\"date\": \"2025-12-14\", \"reason\": \"fair_rotation\", \"user_id\": 28, \"slot_index\": 0}, {\"date\": \"2025-12-14\", \"reason\": \"fair_rotation\", \"user_id\": 30, \"slot_index\": 1}, {\"date\": \"2025-12-14\", \"reason\": \"fair_rotation\", \"user_id\": 29, \"slot_index\": 2}], \"rotation_pointer\": 21, \"weekly_off_every\": \"6\", \"weekly_off_label\": \"OFF\", \"weekly_shift_counts\": {\"26\": 4, \"27\": 4, \"28\": 4, \"29\": 5, \"30\": 4}}', '2025-12-08 06:54:12', '2025-12-08 06:54:13'),
(15, 2, 4, '2025-12-15', '2025-12-21', 0, '{\"assignment_log\": [{\"date\": \"2025-12-15\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 0}, {\"date\": \"2025-12-15\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 1}, {\"date\": \"2025-12-15\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 2}, {\"date\": \"2025-12-15\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 3}, {\"date\": \"2025-12-16\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 0}, {\"date\": \"2025-12-16\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 1}, {\"date\": \"2025-12-16\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 2}, {\"date\": \"2025-12-17\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 0}, {\"date\": \"2025-12-17\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 1}, {\"date\": \"2025-12-17\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 2}, {\"date\": \"2025-12-18\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 0}, {\"date\": \"2025-12-18\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 1}, {\"date\": \"2025-12-18\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 2}, {\"date\": \"2025-12-19\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 0}, {\"date\": \"2025-12-19\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 1}, {\"date\": \"2025-12-19\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 2}, {\"date\": \"2025-12-20\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 0}, {\"date\": \"2025-12-20\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 1}, {\"date\": \"2025-12-20\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 2}, {\"date\": \"2025-12-20\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 3}, {\"date\": \"2025-12-21\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 0}, {\"date\": \"2025-12-21\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 1}, {\"date\": \"2025-12-21\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 2}, {\"date\": \"2025-12-21\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 3}], \"rotation_pointer\": 28, \"weekly_off_every\": \"6\", \"weekly_off_label\": \"OFF\", \"weekly_shift_counts\": {\"4\": 6, \"5\": 6, \"6\": 6, \"19\": 6}}', '2025-12-08 07:14:13', '2025-12-08 07:14:14'),
(16, 5, 14, '2025-12-15', '2025-12-21', 0, '{\"assignment_log\": [{\"date\": \"2025-12-15\", \"reason\": \"fair_rotation\", \"user_id\": 26, \"slot_index\": 0}, {\"date\": \"2025-12-15\", \"reason\": \"fair_rotation\", \"user_id\": 27, \"slot_index\": 1}, {\"date\": \"2025-12-15\", \"reason\": \"fair_rotation\", \"user_id\": 28, \"slot_index\": 2}, {\"date\": \"2025-12-15\", \"reason\": \"fair_rotation\", \"user_id\": 29, \"slot_index\": 0}, {\"date\": \"2025-12-15\", \"reason\": \"fair_rotation\", \"user_id\": 30, \"slot_index\": 1}, {\"date\": \"2025-12-16\", \"reason\": \"fair_rotation\", \"user_id\": 30, \"slot_index\": 0}, {\"date\": \"2025-12-16\", \"reason\": \"fair_rotation\", \"user_id\": 27, \"slot_index\": 1}, {\"date\": \"2025-12-16\", \"reason\": \"fair_rotation\", \"user_id\": 28, \"slot_index\": 2}, {\"date\": \"2025-12-16\", \"reason\": \"fair_rotation\", \"user_id\": 29, \"slot_index\": 0}, {\"date\": \"2025-12-17\", \"reason\": \"fair_rotation\", \"user_id\": 26, \"slot_index\": 0}, {\"date\": \"2025-12-17\", \"reason\": \"fair_rotation\", \"user_id\": 29, \"slot_index\": 1}, {\"date\": \"2025-12-17\", \"reason\": \"fair_rotation\", \"user_id\": 30, \"slot_index\": 2}, {\"date\": \"2025-12-17\", \"reason\": \"fair_rotation\", \"user_id\": 28, \"slot_index\": 1}, {\"date\": \"2025-12-18\", \"reason\": \"fair_rotation\", \"user_id\": 27, \"slot_index\": 0}, {\"date\": \"2025-12-18\", \"reason\": \"fair_rotation\", \"user_id\": 26, \"slot_index\": 1}, {\"date\": \"2025-12-18\", \"reason\": \"fair_rotation\", \"user_id\": 29, \"slot_index\": 2}, {\"date\": \"2025-12-18\", \"reason\": \"fair_rotation\", \"user_id\": 30, \"slot_index\": 1}, {\"date\": \"2025-12-19\", \"reason\": \"fair_rotation\", \"user_id\": 26, \"slot_index\": 0}, {\"date\": \"2025-12-19\", \"reason\": \"fair_rotation\", \"user_id\": 27, \"slot_index\": 1}, {\"date\": \"2025-12-19\", \"reason\": \"fair_rotation\", \"user_id\": 28, \"slot_index\": 2}, {\"date\": \"2025-12-19\", \"reason\": \"fair_rotation\", \"user_id\": 30, \"slot_index\": 0}, {\"date\": \"2025-12-20\", \"reason\": \"fair_rotation\", \"user_id\": 29, \"slot_index\": 1}, {\"date\": \"2025-12-20\", \"reason\": \"fair_rotation\", \"user_id\": 26, \"slot_index\": 2}, {\"date\": \"2025-12-20\", \"reason\": \"fair_rotation\", \"user_id\": 27, \"slot_index\": 0}, {\"date\": \"2025-12-20\", \"reason\": \"fair_rotation\", \"user_id\": 28, \"slot_index\": 1}, {\"date\": \"2025-12-21\", \"reason\": \"fair_rotation\", \"user_id\": 29, \"slot_index\": 0}, {\"date\": \"2025-12-21\", \"reason\": \"fair_rotation\", \"user_id\": 30, \"slot_index\": 1}, {\"date\": \"2025-12-21\", \"reason\": \"fair_rotation\", \"user_id\": 26, \"slot_index\": 2}, {\"date\": \"2025-12-21\", \"reason\": \"fair_rotation\", \"user_id\": 27, \"slot_index\": 0}, {\"date\": \"2025-12-21\", \"reason\": \"fair_rotation\", \"user_id\": 28, \"slot_index\": 1}], \"rotation_pointer\": 21, \"weekly_off_every\": \"6\", \"weekly_off_label\": \"OFF\", \"weekly_shift_counts\": {\"26\": 6, \"27\": 6, \"28\": 6, \"29\": 6, \"30\": 6}}', '2025-12-08 07:14:45', '2025-12-08 07:14:46'),
(17, 4, 10, '2025-12-15', '2025-12-21', 0, '{\"assignment_log\": [{\"date\": \"2025-12-15\", \"reason\": \"fair_rotation\", \"user_id\": 10, \"slot_index\": 0}, {\"date\": \"2025-12-15\", \"reason\": \"fair_rotation\", \"user_id\": 11, \"slot_index\": 1}, {\"date\": \"2025-12-15\", \"reason\": \"fair_rotation\", \"user_id\": 12, \"slot_index\": 2}, {\"date\": \"2025-12-15\", \"reason\": \"fair_rotation\", \"user_id\": 21, \"slot_index\": 0}, {\"date\": \"2025-12-15\", \"reason\": \"fair_rotation\", \"user_id\": 23, \"slot_index\": 1}, {\"date\": \"2025-12-15\", \"reason\": \"fair_rotation\", \"user_id\": 25, \"slot_index\": 2}, {\"date\": \"2025-12-16\", \"reason\": \"fair_rotation\", \"user_id\": 24, \"slot_index\": 0}, {\"date\": \"2025-12-16\", \"reason\": \"fair_rotation\", \"user_id\": 23, \"slot_index\": 1}, {\"date\": \"2025-12-16\", \"reason\": \"fair_rotation\", \"user_id\": 11, \"slot_index\": 2}, {\"date\": \"2025-12-16\", \"reason\": \"fair_rotation\", \"user_id\": 12, \"slot_index\": 1}, {\"date\": \"2025-12-16\", \"reason\": \"fair_rotation\", \"user_id\": 21, \"slot_index\": 2}, {\"date\": \"2025-12-17\", \"reason\": \"fair_rotation\", \"user_id\": 10, \"slot_index\": 0}, {\"date\": \"2025-12-17\", \"reason\": \"fair_rotation\", \"user_id\": 24, \"slot_index\": 1}, {\"date\": \"2025-12-17\", \"reason\": \"fair_rotation\", \"user_id\": 25, \"slot_index\": 2}, {\"date\": \"2025-12-17\", \"reason\": \"fair_rotation\", \"user_id\": 12, \"slot_index\": 0}, {\"date\": \"2025-12-17\", \"reason\": \"fair_rotation\", \"user_id\": 21, \"slot_index\": 1}, {\"date\": \"2025-12-17\", \"reason\": \"fair_rotation\", \"user_id\": 23, \"slot_index\": 2}, {\"date\": \"2025-12-18\", \"reason\": \"fair_rotation\", \"user_id\": 24, \"slot_index\": 0}, {\"date\": \"2025-12-18\", \"reason\": \"fair_rotation\", \"user_id\": 25, \"slot_index\": 1}, {\"date\": \"2025-12-18\", \"reason\": \"fair_rotation\", \"user_id\": 10, \"slot_index\": 2}, {\"date\": \"2025-12-18\", \"reason\": \"fair_rotation\", \"user_id\": 11, \"slot_index\": 1}, {\"date\": \"2025-12-18\", \"reason\": \"fair_rotation\", \"user_id\": 23, \"slot_index\": 2}, {\"date\": \"2025-12-18\", \"reason\": \"fair_rotation\", \"user_id\": 21, \"slot_index\": 0}, {\"date\": \"2025-12-19\", \"reason\": \"fair_rotation\", \"user_id\": 10, \"slot_index\": 1}, {\"date\": \"2025-12-19\", \"reason\": \"fair_rotation\", \"user_id\": 11, \"slot_index\": 2}, {\"date\": \"2025-12-19\", \"reason\": \"fair_rotation\", \"user_id\": 12, \"slot_index\": 0}, {\"date\": \"2025-12-19\", \"reason\": \"fair_rotation\", \"user_id\": 24, \"slot_index\": 1}, {\"date\": \"2025-12-19\", \"reason\": \"fair_rotation\", \"user_id\": 25, \"slot_index\": 2}, {\"date\": \"2025-12-20\", \"reason\": \"fair_rotation\", \"user_id\": 21, \"slot_index\": 0}, {\"date\": \"2025-12-20\", \"reason\": \"fair_rotation\", \"user_id\": 24, \"slot_index\": 1}, {\"date\": \"2025-12-20\", \"reason\": \"fair_rotation\", \"user_id\": 25, \"slot_index\": 2}, {\"date\": \"2025-12-20\", \"reason\": \"fair_rotation\", \"user_id\": 10, \"slot_index\": 0}, {\"date\": \"2025-12-20\", \"reason\": \"fair_rotation\", \"user_id\": 11, \"slot_index\": 1}, {\"date\": \"2025-12-20\", \"reason\": \"fair_rotation\", \"user_id\": 12, \"slot_index\": 2}, {\"date\": \"2025-12-21\", \"reason\": \"fair_rotation\", \"user_id\": 23, \"slot_index\": 1}, {\"date\": \"2025-12-21\", \"reason\": \"fair_rotation\", \"user_id\": 10, \"slot_index\": 2}, {\"date\": \"2025-12-21\", \"reason\": \"fair_rotation\", \"user_id\": 11, \"slot_index\": 0}, {\"date\": \"2025-12-21\", \"reason\": \"fair_rotation\", \"user_id\": 12, \"slot_index\": 1}, {\"date\": \"2025-12-21\", \"reason\": \"fair_rotation\", \"user_id\": 21, \"slot_index\": 2}], \"rotation_pointer\": 21, \"weekly_off_every\": \"6\", \"weekly_off_label\": \"OFF\", \"weekly_shift_counts\": {\"10\": 6, \"11\": 6, \"12\": 6, \"21\": 6, \"23\": 5, \"24\": 5, \"25\": 5}}', '2025-12-08 07:16:01', '2025-12-08 07:16:02'),
(18, 5, 14, '2025-12-22', '2025-12-28', 0, '{\"assignment_log\": [{\"date\": \"2025-12-22\", \"reason\": \"fair_rotation\", \"user_id\": 26, \"slot_index\": 0}, {\"date\": \"2025-12-22\", \"reason\": \"fair_rotation\", \"user_id\": 27, \"slot_index\": 1}, {\"date\": \"2025-12-22\", \"reason\": \"fair_rotation\", \"user_id\": 28, \"slot_index\": 2}, {\"date\": \"2025-12-22\", \"reason\": \"fair_rotation\", \"user_id\": 29, \"slot_index\": 0}, {\"date\": \"2025-12-22\", \"reason\": \"fair_rotation\", \"user_id\": 30, \"slot_index\": 1}, {\"date\": \"2025-12-23\", \"reason\": \"fair_rotation\", \"user_id\": 30, \"slot_index\": 0}, {\"date\": \"2025-12-23\", \"reason\": \"fair_rotation\", \"user_id\": 27, \"slot_index\": 1}, {\"date\": \"2025-12-23\", \"reason\": \"fair_rotation\", \"user_id\": 28, \"slot_index\": 2}, {\"date\": \"2025-12-23\", \"reason\": \"fair_rotation\", \"user_id\": 29, \"slot_index\": 0}, {\"date\": \"2025-12-24\", \"reason\": \"fair_rotation\", \"user_id\": 26, \"slot_index\": 0}, {\"date\": \"2025-12-24\", \"reason\": \"fair_rotation\", \"user_id\": 29, \"slot_index\": 1}, {\"date\": \"2025-12-24\", \"reason\": \"fair_rotation\", \"user_id\": 30, \"slot_index\": 2}, {\"date\": \"2025-12-24\", \"reason\": \"fair_rotation\", \"user_id\": 28, \"slot_index\": 1}, {\"date\": \"2025-12-25\", \"reason\": \"fair_rotation\", \"user_id\": 27, \"slot_index\": 0}, {\"date\": \"2025-12-25\", \"reason\": \"fair_rotation\", \"user_id\": 26, \"slot_index\": 1}, {\"date\": \"2025-12-25\", \"reason\": \"fair_rotation\", \"user_id\": 29, \"slot_index\": 2}, {\"date\": \"2025-12-25\", \"reason\": \"fair_rotation\", \"user_id\": 30, \"slot_index\": 1}, {\"date\": \"2025-12-26\", \"reason\": \"fair_rotation\", \"user_id\": 26, \"slot_index\": 0}, {\"date\": \"2025-12-26\", \"reason\": \"fair_rotation\", \"user_id\": 27, \"slot_index\": 1}, {\"date\": \"2025-12-26\", \"reason\": \"fair_rotation\", \"user_id\": 28, \"slot_index\": 2}, {\"date\": \"2025-12-26\", \"reason\": \"fair_rotation\", \"user_id\": 30, \"slot_index\": 0}, {\"date\": \"2025-12-27\", \"reason\": \"fair_rotation\", \"user_id\": 29, \"slot_index\": 1}, {\"date\": \"2025-12-27\", \"reason\": \"fair_rotation\", \"user_id\": 26, \"slot_index\": 2}, {\"date\": \"2025-12-27\", \"reason\": \"fair_rotation\", \"user_id\": 27, \"slot_index\": 0}, {\"date\": \"2025-12-27\", \"reason\": \"fair_rotation\", \"user_id\": 28, \"slot_index\": 1}, {\"date\": \"2025-12-28\", \"reason\": \"fair_rotation\", \"user_id\": 29, \"slot_index\": 0}, {\"date\": \"2025-12-28\", \"reason\": \"fair_rotation\", \"user_id\": 30, \"slot_index\": 1}, {\"date\": \"2025-12-28\", \"reason\": \"fair_rotation\", \"user_id\": 26, \"slot_index\": 2}, {\"date\": \"2025-12-28\", \"reason\": \"fair_rotation\", \"user_id\": 27, \"slot_index\": 0}, {\"date\": \"2025-12-28\", \"reason\": \"fair_rotation\", \"user_id\": 28, \"slot_index\": 1}], \"rotation_pointer\": 21, \"weekly_off_every\": \"6\", \"weekly_off_label\": \"OFF\", \"weekly_shift_counts\": {\"26\": 6, \"27\": 6, \"28\": 6, \"29\": 6, \"30\": 6}}', '2025-12-08 07:54:34', '2025-12-08 07:54:35'),
(19, 2, 4, '2025-12-22', '2025-12-28', 0, '{\"assignment_log\": [{\"date\": \"2025-12-22\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 0}, {\"date\": \"2025-12-22\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 1}, {\"date\": \"2025-12-22\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 2}, {\"date\": \"2025-12-22\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 3}, {\"date\": \"2025-12-23\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 0}, {\"date\": \"2025-12-23\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 1}, {\"date\": \"2025-12-23\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 2}, {\"date\": \"2025-12-24\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 0}, {\"date\": \"2025-12-24\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 1}, {\"date\": \"2025-12-24\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 2}, {\"date\": \"2025-12-25\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 0}, {\"date\": \"2025-12-25\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 1}, {\"date\": \"2025-12-25\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 2}, {\"date\": \"2025-12-26\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 0}, {\"date\": \"2025-12-26\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 1}, {\"date\": \"2025-12-26\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 2}, {\"date\": \"2025-12-27\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 0}, {\"date\": \"2025-12-27\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 1}, {\"date\": \"2025-12-27\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 2}, {\"date\": \"2025-12-27\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 3}, {\"date\": \"2025-12-28\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 0}, {\"date\": \"2025-12-28\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 1}, {\"date\": \"2025-12-28\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 2}, {\"date\": \"2025-12-28\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 3}], \"rotation_pointer\": 28, \"weekly_off_every\": \"6\", \"weekly_off_label\": \"OFF\", \"weekly_shift_counts\": {\"4\": 6, \"5\": 6, \"6\": 6, \"19\": 6}}', '2025-12-22 04:18:49', '2025-12-22 04:18:50'),
(20, 2, 4, '2025-12-29', '2026-01-04', 0, '{\"assignment_log\": [{\"date\": \"2025-12-29\", \"reason\": \"limit_daily\", \"user_id\": 4, \"slot_index\": 0}, {\"date\": \"2025-12-29\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 1}, {\"date\": \"2025-12-29\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 2}, {\"date\": \"2025-12-29\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 3}, {\"date\": \"2025-12-29\", \"reason\": \"limit_daily\", \"user_id\": 19, \"slot_index\": 0}, {\"date\": \"2025-12-29\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 1}, {\"date\": \"2025-12-30\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 1}, {\"date\": \"2025-12-30\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 2}, {\"date\": \"2025-12-30\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 3}, {\"date\": \"2025-12-31\", \"reason\": \"limit_daily\", \"user_id\": 4, \"slot_index\": 0}, {\"date\": \"2025-12-31\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 1}, {\"date\": \"2025-12-31\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 2}, {\"date\": \"2025-12-31\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 3}, {\"date\": \"2026-01-01\", \"reason\": \"limit_daily\", \"user_id\": 4, \"slot_index\": 0}, {\"date\": \"2026-01-01\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 1}, {\"date\": \"2026-01-01\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 2}, {\"date\": \"2026-01-01\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 3}, {\"date\": \"2026-01-02\", \"reason\": \"limit_daily\", \"user_id\": 5, \"slot_index\": 0}, {\"date\": \"2026-01-02\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 1}, {\"date\": \"2026-01-02\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 2}, {\"date\": \"2026-01-02\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 3}, {\"date\": \"2026-01-03\", \"reason\": \"fair_rotation\", \"user_id\": 4, \"slot_index\": 1}, {\"date\": \"2026-01-03\", \"reason\": \"fair_rotation\", \"user_id\": 5, \"slot_index\": 2}, {\"date\": \"2026-01-03\", \"reason\": \"fair_rotation\", \"user_id\": 6, \"slot_index\": 3}, {\"date\": \"2026-01-03\", \"reason\": \"fair_rotation\", \"user_id\": 19, \"slot_index\": 1}, {\"date\": \"2026-01-04\", \"reason\": \"limit_daily\", \"user_id\": 4, \"slot_index\": 0}, {\"date\": \"2026-01-04\", \"reason\": \"limit_weekly\", \"user_id\": 4, \"slot_index\": 1}, {\"date\": \"2026-01-04\", \"reason\": \"limit_weekly\", \"user_id\": 4, \"slot_index\": 2}, {\"date\": \"2026-01-04\", \"reason\": \"limit_weekly\", \"user_id\": 4, \"slot_index\": 3}, {\"date\": \"2026-01-04\", \"reason\": \"limit_daily\", \"user_id\": 5, \"slot_index\": 0}, {\"date\": \"2026-01-04\", \"reason\": \"limit_weekly\", \"user_id\": 5, \"slot_index\": 1}, {\"date\": \"2026-01-04\", \"reason\": \"limit_weekly\", \"user_id\": 5, \"slot_index\": 2}, {\"date\": \"2026-01-04\", \"reason\": \"limit_weekly\", \"user_id\": 5, \"slot_index\": 3}, {\"date\": \"2026-01-04\", \"reason\": \"limit_weekly\", \"user_id\": 6, \"slot_index\": 1}, {\"date\": \"2026-01-04\", \"reason\": \"limit_weekly\", \"user_id\": 6, \"slot_index\": 2}, {\"date\": \"2026-01-04\", \"reason\": \"limit_weekly\", \"user_id\": 6, \"slot_index\": 3}, {\"date\": \"2026-01-04\", \"reason\": \"limit_daily\", \"user_id\": 19, \"slot_index\": 0}, {\"date\": \"2026-01-04\", \"reason\": \"limit_weekly\", \"user_id\": 19, \"slot_index\": 1}, {\"date\": \"2026-01-04\", \"reason\": \"limit_weekly\", \"user_id\": 19, \"slot_index\": 2}, {\"date\": \"2026-01-04\", \"reason\": \"limit_weekly\", \"user_id\": 19, \"slot_index\": 3}], \"rotation_pointer\": 28, \"weekly_off_every\": \"6\", \"weekly_off_label\": \"OFF\", \"weekly_shift_counts\": {\"4\": 5, \"5\": 5, \"6\": 5, \"19\": 5}}', '2025-12-23 02:56:11', '2025-12-23 02:56:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `weekly_roster_entries`
--

CREATE TABLE `weekly_roster_entries` (
  `id` bigint UNSIGNED NOT NULL,
  `weekly_roster_id` bigint UNSIGNED NOT NULL,
  `shift_assignment_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `slot_index` int UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('scheduled','off') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `weekly_roster_entries`
--

INSERT INTO `weekly_roster_entries` (`id`, `weekly_roster_id`, `shift_assignment_id`, `user_id`, `date`, `slot_index`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(191, 9, 287, 4, '2025-12-08', 0, 'scheduled', NULL, '2025-12-08 06:41:23', '2025-12-08 06:41:23'),
(192, 9, 288, 5, '2025-12-08', 1, 'scheduled', NULL, '2025-12-08 06:41:23', '2025-12-08 06:41:23'),
(193, 9, 289, 6, '2025-12-08', 2, 'scheduled', NULL, '2025-12-08 06:41:23', '2025-12-08 06:41:23'),
(194, 9, 290, 19, '2025-12-08', 3, 'scheduled', NULL, '2025-12-08 06:41:23', '2025-12-08 06:41:23'),
(195, 9, NULL, 4, '2025-12-09', 0, 'off', 'OFF', '2025-12-08 06:41:23', '2025-12-08 06:41:23'),
(196, 9, 291, 6, '2025-12-09', 0, 'scheduled', NULL, '2025-12-08 06:41:23', '2025-12-08 06:41:23'),
(197, 9, 292, 19, '2025-12-09', 1, 'scheduled', NULL, '2025-12-08 06:41:23', '2025-12-08 06:41:23'),
(198, 9, 293, 5, '2025-12-09', 2, 'scheduled', NULL, '2025-12-08 06:41:23', '2025-12-08 06:41:23'),
(199, 9, 291, 6, '2025-12-09', 3, 'scheduled', NULL, '2025-12-08 06:41:23', '2025-12-08 06:41:23'),
(200, 9, NULL, 5, '2025-12-10', 0, 'off', 'OFF', '2025-12-08 06:41:23', '2025-12-08 06:41:23'),
(201, 9, 295, 4, '2025-12-10', 0, 'scheduled', NULL, '2025-12-08 06:41:23', '2025-12-08 06:41:23'),
(202, 9, 294, 19, '2025-12-10', 1, 'scheduled', NULL, '2025-12-08 06:41:23', '2025-12-08 06:41:23'),
(203, 9, 295, 4, '2025-12-10', 2, 'scheduled', NULL, '2025-12-08 06:41:23', '2025-12-08 06:41:23'),
(204, 9, 294, 19, '2025-12-10', 3, 'scheduled', NULL, '2025-12-08 06:41:23', '2025-12-08 06:41:23'),
(205, 9, NULL, 6, '2025-12-11', 0, 'off', 'OFF', '2025-12-08 06:41:23', '2025-12-08 06:41:23'),
(206, 9, 298, 5, '2025-12-11', 0, 'scheduled', NULL, '2025-12-08 06:41:23', '2025-12-08 06:41:23'),
(207, 9, 297, 4, '2025-12-11', 1, 'scheduled', NULL, '2025-12-08 06:41:23', '2025-12-08 06:41:23'),
(208, 9, 298, 5, '2025-12-11', 2, 'scheduled', NULL, '2025-12-08 06:41:24', '2025-12-08 06:41:24'),
(209, 9, 297, 4, '2025-12-11', 3, 'scheduled', NULL, '2025-12-08 06:41:24', '2025-12-08 06:41:24'),
(210, 9, NULL, 19, '2025-12-12', 0, 'off', 'OFF', '2025-12-08 06:41:24', '2025-12-08 06:41:24'),
(211, 9, 300, 5, '2025-12-12', 0, 'scheduled', NULL, '2025-12-08 06:41:24', '2025-12-08 06:41:24'),
(212, 9, 301, 6, '2025-12-12', 1, 'scheduled', NULL, '2025-12-08 06:41:24', '2025-12-08 06:41:24'),
(213, 9, 301, 6, '2025-12-12', 2, 'scheduled', NULL, '2025-12-08 06:41:24', '2025-12-08 06:41:24'),
(214, 9, 300, 5, '2025-12-12', 3, 'scheduled', NULL, '2025-12-08 06:41:24', '2025-12-08 06:41:24'),
(215, 9, 305, 6, '2025-12-13', 0, 'scheduled', NULL, '2025-12-08 06:41:24', '2025-12-08 06:41:24'),
(216, 9, 306, 19, '2025-12-13', 1, 'scheduled', NULL, '2025-12-08 06:41:24', '2025-12-08 06:41:24'),
(217, 9, 303, 4, '2025-12-13', 2, 'scheduled', NULL, '2025-12-08 06:41:24', '2025-12-08 06:41:24'),
(218, 9, 306, 19, '2025-12-13', 3, 'scheduled', NULL, '2025-12-08 06:41:24', '2025-12-08 06:41:24'),
(219, 9, 307, 4, '2025-12-14', 0, 'scheduled', NULL, '2025-12-08 06:41:24', '2025-12-08 06:41:24'),
(220, 9, 308, 5, '2025-12-14', 1, 'scheduled', NULL, '2025-12-08 06:41:24', '2025-12-08 06:41:24'),
(221, 9, 309, 6, '2025-12-14', 2, 'scheduled', NULL, '2025-12-08 06:41:24', '2025-12-08 06:41:24'),
(222, 9, 310, 19, '2025-12-14', 3, 'scheduled', NULL, '2025-12-08 06:41:24', '2025-12-08 06:41:24'),
(223, 10, NULL, 24, '2025-12-08', 0, 'off', 'OFF', '2025-12-08 06:41:44', '2025-12-08 06:41:44'),
(224, 10, 171, 10, '2025-12-08', 0, 'scheduled', NULL, '2025-12-08 06:41:44', '2025-12-08 06:41:44'),
(225, 10, 172, 11, '2025-12-08', 1, 'scheduled', NULL, '2025-12-08 06:41:44', '2025-12-08 06:41:44'),
(226, 10, 173, 12, '2025-12-08', 2, 'scheduled', NULL, '2025-12-08 06:41:44', '2025-12-08 06:41:44'),
(227, 10, NULL, 10, '2025-12-09', 0, 'off', 'OFF', '2025-12-08 06:41:44', '2025-12-08 06:41:44'),
(228, 10, NULL, 25, '2025-12-09', 0, 'off', 'OFF', '2025-12-08 06:41:44', '2025-12-08 06:41:44'),
(229, 10, 182, 23, '2025-12-09', 0, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(230, 10, 183, 24, '2025-12-09', 1, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(231, 10, 181, 21, '2025-12-09', 2, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(232, 10, NULL, 11, '2025-12-10', 0, 'off', 'OFF', '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(233, 10, 191, 25, '2025-12-10', 0, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(234, 10, 185, 10, '2025-12-10', 1, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(235, 10, 187, 12, '2025-12-10', 2, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(236, 10, NULL, 12, '2025-12-11', 0, 'off', 'OFF', '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(237, 10, 196, 23, '2025-12-11', 0, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(238, 10, 197, 24, '2025-12-11', 1, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(239, 10, 198, 25, '2025-12-11', 2, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(240, 10, NULL, 21, '2025-12-12', 0, 'off', 'OFF', '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(241, 10, 200, 11, '2025-12-12', 0, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(242, 10, 199, 10, '2025-12-12', 1, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(243, 10, 200, 11, '2025-12-12', 2, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(244, 10, NULL, 23, '2025-12-13', 0, 'off', 'OFF', '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(245, 10, 211, 24, '2025-12-13', 0, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(246, 10, 209, 21, '2025-12-13', 1, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(247, 10, 209, 21, '2025-12-13', 2, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(248, 10, NULL, 24, '2025-12-14', 0, 'off', 'OFF', '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(249, 10, 217, 23, '2025-12-14', 0, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(250, 10, 215, 12, '2025-12-14', 1, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(251, 10, 219, 25, '2025-12-14', 2, 'scheduled', NULL, '2025-12-08 06:41:45', '2025-12-08 06:41:45'),
(252, 11, 311, 26, '2025-12-08', 0, 'scheduled', NULL, '2025-12-08 06:42:13', '2025-12-08 06:42:13'),
(253, 11, 312, 27, '2025-12-08', 1, 'scheduled', NULL, '2025-12-08 06:42:13', '2025-12-08 06:42:13'),
(254, 11, 313, 28, '2025-12-08', 2, 'scheduled', NULL, '2025-12-08 06:42:13', '2025-12-08 06:42:13'),
(255, 11, NULL, 26, '2025-12-09', 0, 'off', 'OFF', '2025-12-08 06:42:13', '2025-12-08 06:42:13'),
(256, 11, 329, 30, '2025-12-09', 0, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(257, 11, 337, 29, '2025-12-09', 1, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(258, 11, 329, 30, '2025-12-09', 2, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(259, 11, NULL, 27, '2025-12-10', 0, 'off', 'OFF', '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(260, 11, 330, 29, '2025-12-10', 0, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(261, 11, 316, 26, '2025-12-10', 1, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(262, 11, 317, 28, '2025-12-10', 2, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(263, 11, NULL, 28, '2025-12-11', 0, 'off', 'OFF', '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(264, 11, 318, 27, '2025-12-11', 0, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(265, 11, 318, 27, '2025-12-11', 1, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(266, 11, 332, 29, '2025-12-11', 2, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(267, 11, NULL, 29, '2025-12-12', 0, 'off', 'OFF', '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(268, 11, 320, 26, '2025-12-12', 0, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(269, 11, 322, 28, '2025-12-12', 1, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(270, 11, 338, 30, '2025-12-12', 2, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(271, 11, NULL, 30, '2025-12-13', 0, 'off', 'OFF', '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(272, 11, 323, 26, '2025-12-13', 0, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(273, 11, 334, 29, '2025-12-13', 1, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(274, 11, 324, 27, '2025-12-13', 2, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(275, 11, 328, 28, '2025-12-14', 0, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(276, 11, 336, 30, '2025-12-14', 1, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(277, 11, 335, 29, '2025-12-14', 2, 'scheduled', NULL, '2025-12-08 06:42:14', '2025-12-08 06:42:14'),
(278, 12, 287, 4, '2025-12-08', 0, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(279, 12, 288, 5, '2025-12-08', 1, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(280, 12, 289, 6, '2025-12-08', 2, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(281, 12, 290, 19, '2025-12-08', 3, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(282, 12, NULL, 4, '2025-12-09', 0, 'off', 'OFF', '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(283, 12, 291, 6, '2025-12-09', 0, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(284, 12, 292, 19, '2025-12-09', 1, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(285, 12, 293, 5, '2025-12-09', 2, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(286, 12, 291, 6, '2025-12-09', 3, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(287, 12, NULL, 5, '2025-12-10', 0, 'off', 'OFF', '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(288, 12, 295, 4, '2025-12-10', 0, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(289, 12, 294, 19, '2025-12-10', 1, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(290, 12, 295, 4, '2025-12-10', 2, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(291, 12, 294, 19, '2025-12-10', 3, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(292, 12, NULL, 6, '2025-12-11', 0, 'off', 'OFF', '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(293, 12, 298, 5, '2025-12-11', 0, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(294, 12, 297, 4, '2025-12-11', 1, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(295, 12, 298, 5, '2025-12-11', 2, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(296, 12, 297, 4, '2025-12-11', 3, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(297, 12, NULL, 19, '2025-12-12', 0, 'off', 'OFF', '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(298, 12, 300, 5, '2025-12-12', 0, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(299, 12, 301, 6, '2025-12-12', 1, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(300, 12, 301, 6, '2025-12-12', 2, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(301, 12, 300, 5, '2025-12-12', 3, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(302, 12, 305, 6, '2025-12-13', 0, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(303, 12, 306, 19, '2025-12-13', 1, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(304, 12, 303, 4, '2025-12-13', 2, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(305, 12, 306, 19, '2025-12-13', 3, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(306, 12, 307, 4, '2025-12-14', 0, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(307, 12, 308, 5, '2025-12-14', 1, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(308, 12, 309, 6, '2025-12-14', 2, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(309, 12, 310, 19, '2025-12-14', 3, 'scheduled', NULL, '2025-12-08 06:53:26', '2025-12-08 06:53:26'),
(310, 13, NULL, 24, '2025-12-08', 0, 'off', 'OFF', '2025-12-08 06:53:56', '2025-12-08 06:53:56'),
(311, 13, 171, 10, '2025-12-08', 0, 'scheduled', NULL, '2025-12-08 06:53:56', '2025-12-08 06:53:56'),
(312, 13, 172, 11, '2025-12-08', 1, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(313, 13, 173, 12, '2025-12-08', 2, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(314, 13, NULL, 10, '2025-12-09', 0, 'off', 'OFF', '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(315, 13, NULL, 25, '2025-12-09', 0, 'off', 'OFF', '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(316, 13, 182, 23, '2025-12-09', 0, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(317, 13, 183, 24, '2025-12-09', 1, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(318, 13, 181, 21, '2025-12-09', 2, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(319, 13, NULL, 11, '2025-12-10', 0, 'off', 'OFF', '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(320, 13, 191, 25, '2025-12-10', 0, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(321, 13, 185, 10, '2025-12-10', 1, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(322, 13, 187, 12, '2025-12-10', 2, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(323, 13, NULL, 12, '2025-12-11', 0, 'off', 'OFF', '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(324, 13, 196, 23, '2025-12-11', 0, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(325, 13, 197, 24, '2025-12-11', 1, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(326, 13, 198, 25, '2025-12-11', 2, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(327, 13, NULL, 21, '2025-12-12', 0, 'off', 'OFF', '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(328, 13, 200, 11, '2025-12-12', 0, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(329, 13, 199, 10, '2025-12-12', 1, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(330, 13, 200, 11, '2025-12-12', 2, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(331, 13, NULL, 23, '2025-12-13', 0, 'off', 'OFF', '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(332, 13, 211, 24, '2025-12-13', 0, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(333, 13, 209, 21, '2025-12-13', 1, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(334, 13, 209, 21, '2025-12-13', 2, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(335, 13, NULL, 24, '2025-12-14', 0, 'off', 'OFF', '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(336, 13, 217, 23, '2025-12-14', 0, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(337, 13, 215, 12, '2025-12-14', 1, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(338, 13, 219, 25, '2025-12-14', 2, 'scheduled', NULL, '2025-12-08 06:53:57', '2025-12-08 06:53:57'),
(339, 14, 311, 26, '2025-12-08', 0, 'scheduled', NULL, '2025-12-08 06:54:12', '2025-12-08 06:54:12'),
(340, 14, 312, 27, '2025-12-08', 1, 'scheduled', NULL, '2025-12-08 06:54:12', '2025-12-08 06:54:12'),
(341, 14, 313, 28, '2025-12-08', 2, 'scheduled', NULL, '2025-12-08 06:54:12', '2025-12-08 06:54:12'),
(342, 14, NULL, 26, '2025-12-09', 0, 'off', 'OFF', '2025-12-08 06:54:12', '2025-12-08 06:54:12'),
(343, 14, 329, 30, '2025-12-09', 0, 'scheduled', NULL, '2025-12-08 06:54:12', '2025-12-08 06:54:12'),
(344, 14, 337, 29, '2025-12-09', 1, 'scheduled', NULL, '2025-12-08 06:54:12', '2025-12-08 06:54:12'),
(345, 14, 329, 30, '2025-12-09', 2, 'scheduled', NULL, '2025-12-08 06:54:12', '2025-12-08 06:54:12'),
(346, 14, NULL, 27, '2025-12-10', 0, 'off', 'OFF', '2025-12-08 06:54:12', '2025-12-08 06:54:12'),
(347, 14, 330, 29, '2025-12-10', 0, 'scheduled', NULL, '2025-12-08 06:54:12', '2025-12-08 06:54:12'),
(348, 14, 316, 26, '2025-12-10', 1, 'scheduled', NULL, '2025-12-08 06:54:12', '2025-12-08 06:54:12'),
(349, 14, 317, 28, '2025-12-10', 2, 'scheduled', NULL, '2025-12-08 06:54:12', '2025-12-08 06:54:12'),
(350, 14, NULL, 28, '2025-12-11', 0, 'off', 'OFF', '2025-12-08 06:54:13', '2025-12-08 06:54:13'),
(351, 14, 318, 27, '2025-12-11', 0, 'scheduled', NULL, '2025-12-08 06:54:13', '2025-12-08 06:54:13'),
(352, 14, 318, 27, '2025-12-11', 1, 'scheduled', NULL, '2025-12-08 06:54:13', '2025-12-08 06:54:13'),
(353, 14, 332, 29, '2025-12-11', 2, 'scheduled', NULL, '2025-12-08 06:54:13', '2025-12-08 06:54:13'),
(354, 14, NULL, 29, '2025-12-12', 0, 'off', 'OFF', '2025-12-08 06:54:13', '2025-12-08 06:54:13'),
(355, 14, 320, 26, '2025-12-12', 0, 'scheduled', NULL, '2025-12-08 06:54:13', '2025-12-08 06:54:13'),
(356, 14, 322, 28, '2025-12-12', 1, 'scheduled', NULL, '2025-12-08 06:54:13', '2025-12-08 06:54:13'),
(357, 14, 338, 30, '2025-12-12', 2, 'scheduled', NULL, '2025-12-08 06:54:13', '2025-12-08 06:54:13'),
(358, 14, NULL, 30, '2025-12-13', 0, 'off', 'OFF', '2025-12-08 06:54:13', '2025-12-08 06:54:13'),
(359, 14, 323, 26, '2025-12-13', 0, 'scheduled', NULL, '2025-12-08 06:54:13', '2025-12-08 06:54:13'),
(360, 14, 334, 29, '2025-12-13', 1, 'scheduled', NULL, '2025-12-08 06:54:13', '2025-12-08 06:54:13'),
(361, 14, 324, 27, '2025-12-13', 2, 'scheduled', NULL, '2025-12-08 06:54:13', '2025-12-08 06:54:13'),
(362, 14, 328, 28, '2025-12-14', 0, 'scheduled', NULL, '2025-12-08 06:54:13', '2025-12-08 06:54:13'),
(363, 14, 336, 30, '2025-12-14', 1, 'scheduled', NULL, '2025-12-08 06:54:13', '2025-12-08 06:54:13'),
(364, 14, 335, 29, '2025-12-14', 2, 'scheduled', NULL, '2025-12-08 06:54:13', '2025-12-08 06:54:13'),
(365, 15, 339, 4, '2025-12-15', 0, 'scheduled', NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(366, 15, 340, 5, '2025-12-15', 1, 'scheduled', NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(367, 15, 341, 6, '2025-12-15', 2, 'scheduled', NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(368, 15, 342, 19, '2025-12-15', 3, 'scheduled', NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(369, 15, NULL, 4, '2025-12-16', 0, 'off', 'OFF', '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(370, 15, 343, 6, '2025-12-16', 0, 'scheduled', NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(371, 15, 344, 19, '2025-12-16', 1, 'scheduled', NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(372, 15, 345, 5, '2025-12-16', 2, 'scheduled', NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(373, 15, NULL, 5, '2025-12-17', 0, 'off', 'OFF', '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(374, 15, 346, 4, '2025-12-17', 0, 'scheduled', NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(375, 15, 347, 19, '2025-12-17', 1, 'scheduled', NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(376, 15, 348, 6, '2025-12-17', 2, 'scheduled', NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(377, 15, NULL, 6, '2025-12-18', 0, 'off', 'OFF', '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(378, 15, 349, 4, '2025-12-18', 0, 'scheduled', NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(379, 15, 350, 5, '2025-12-18', 1, 'scheduled', NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(380, 15, 351, 19, '2025-12-18', 2, 'scheduled', NULL, '2025-12-08 07:14:13', '2025-12-08 07:14:13'),
(381, 15, NULL, 19, '2025-12-19', 0, 'off', 'OFF', '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(382, 15, 352, 5, '2025-12-19', 0, 'scheduled', NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(383, 15, 353, 6, '2025-12-19', 1, 'scheduled', NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(384, 15, 354, 4, '2025-12-19', 2, 'scheduled', NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(385, 15, 355, 4, '2025-12-20', 0, 'scheduled', NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(386, 15, 356, 5, '2025-12-20', 1, 'scheduled', NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(387, 15, 357, 6, '2025-12-20', 2, 'scheduled', NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(388, 15, 358, 19, '2025-12-20', 3, 'scheduled', NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(389, 15, 359, 4, '2025-12-21', 0, 'scheduled', NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(390, 15, 360, 5, '2025-12-21', 1, 'scheduled', NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(391, 15, 361, 6, '2025-12-21', 2, 'scheduled', NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(392, 15, 362, 19, '2025-12-21', 3, 'scheduled', NULL, '2025-12-08 07:14:14', '2025-12-08 07:14:14'),
(393, 16, 363, 26, '2025-12-15', 0, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(394, 16, 364, 27, '2025-12-15', 1, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(395, 16, 365, 28, '2025-12-15', 2, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(396, 16, 366, 29, '2025-12-15', 0, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(397, 16, 367, 30, '2025-12-15', 1, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(398, 16, NULL, 26, '2025-12-16', 0, 'off', 'OFF', '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(399, 16, 368, 30, '2025-12-16', 0, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(400, 16, 369, 27, '2025-12-16', 1, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(401, 16, 370, 28, '2025-12-16', 2, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(402, 16, 371, 29, '2025-12-16', 0, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(403, 16, NULL, 27, '2025-12-17', 0, 'off', 'OFF', '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(404, 16, 372, 26, '2025-12-17', 0, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(405, 16, 373, 29, '2025-12-17', 1, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(406, 16, 374, 30, '2025-12-17', 2, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(407, 16, 375, 28, '2025-12-17', 1, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(408, 16, NULL, 28, '2025-12-18', 0, 'off', 'OFF', '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(409, 16, 376, 27, '2025-12-18', 0, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(410, 16, 377, 26, '2025-12-18', 1, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(411, 16, 378, 29, '2025-12-18', 2, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(412, 16, 379, 30, '2025-12-18', 1, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(413, 16, NULL, 29, '2025-12-19', 0, 'off', 'OFF', '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(414, 16, 380, 26, '2025-12-19', 0, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(415, 16, 381, 27, '2025-12-19', 1, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(416, 16, 382, 28, '2025-12-19', 2, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(417, 16, 383, 30, '2025-12-19', 0, 'scheduled', NULL, '2025-12-08 07:14:45', '2025-12-08 07:14:45'),
(418, 16, NULL, 30, '2025-12-20', 0, 'off', 'OFF', '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(419, 16, 384, 29, '2025-12-20', 1, 'scheduled', NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(420, 16, 385, 26, '2025-12-20', 2, 'scheduled', NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(421, 16, 386, 27, '2025-12-20', 0, 'scheduled', NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(422, 16, 387, 28, '2025-12-20', 1, 'scheduled', NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(423, 16, 388, 29, '2025-12-21', 0, 'scheduled', NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(424, 16, 389, 30, '2025-12-21', 1, 'scheduled', NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(425, 16, 390, 26, '2025-12-21', 2, 'scheduled', NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(426, 16, 391, 27, '2025-12-21', 0, 'scheduled', NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(427, 16, 392, 28, '2025-12-21', 1, 'scheduled', NULL, '2025-12-08 07:14:46', '2025-12-08 07:14:46'),
(428, 17, NULL, 24, '2025-12-15', 0, 'off', 'OFF', '2025-12-08 07:16:01', '2025-12-08 07:16:01'),
(429, 17, 220, 10, '2025-12-15', 0, 'scheduled', NULL, '2025-12-08 07:16:01', '2025-12-08 07:16:01'),
(430, 17, 221, 11, '2025-12-15', 1, 'scheduled', NULL, '2025-12-08 07:16:01', '2025-12-08 07:16:01'),
(431, 17, 222, 12, '2025-12-15', 2, 'scheduled', NULL, '2025-12-08 07:16:01', '2025-12-08 07:16:01'),
(432, 17, 223, 21, '2025-12-15', 0, 'scheduled', NULL, '2025-12-08 07:16:01', '2025-12-08 07:16:01'),
(433, 17, 224, 23, '2025-12-15', 1, 'scheduled', NULL, '2025-12-08 07:16:01', '2025-12-08 07:16:01'),
(434, 17, 226, 25, '2025-12-15', 2, 'scheduled', NULL, '2025-12-08 07:16:01', '2025-12-08 07:16:01'),
(435, 17, NULL, 10, '2025-12-16', 0, 'off', 'OFF', '2025-12-08 07:16:01', '2025-12-08 07:16:01'),
(436, 17, NULL, 25, '2025-12-16', 0, 'off', 'OFF', '2025-12-08 07:16:01', '2025-12-08 07:16:01'),
(437, 17, 232, 24, '2025-12-16', 0, 'scheduled', NULL, '2025-12-08 07:16:01', '2025-12-08 07:16:01'),
(438, 17, 231, 23, '2025-12-16', 1, 'scheduled', NULL, '2025-12-08 07:16:01', '2025-12-08 07:16:01'),
(439, 17, 228, 11, '2025-12-16', 2, 'scheduled', NULL, '2025-12-08 07:16:01', '2025-12-08 07:16:01'),
(440, 17, 229, 12, '2025-12-16', 1, 'scheduled', NULL, '2025-12-08 07:16:01', '2025-12-08 07:16:01'),
(441, 17, 230, 21, '2025-12-16', 2, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(442, 17, NULL, 11, '2025-12-17', 0, 'off', 'OFF', '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(443, 17, 234, 10, '2025-12-17', 0, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(444, 17, 239, 24, '2025-12-17', 1, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(445, 17, 240, 25, '2025-12-17', 2, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(446, 17, 236, 12, '2025-12-17', 0, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(447, 17, 237, 21, '2025-12-17', 1, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(448, 17, 238, 23, '2025-12-17', 2, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(449, 17, NULL, 12, '2025-12-18', 0, 'off', 'OFF', '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(450, 17, 393, 24, '2025-12-18', 0, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(451, 17, 394, 25, '2025-12-18', 1, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(452, 17, 395, 10, '2025-12-18', 2, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(453, 17, 396, 11, '2025-12-18', 1, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(454, 17, 397, 23, '2025-12-18', 2, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(455, 17, 398, 21, '2025-12-18', 0, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(456, 17, NULL, 21, '2025-12-19', 0, 'off', 'OFF', '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(457, 17, 399, 10, '2025-12-19', 1, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(458, 17, 400, 11, '2025-12-19', 2, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(459, 17, 401, 12, '2025-12-19', 0, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(460, 17, 402, 24, '2025-12-19', 1, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(461, 17, 403, 25, '2025-12-19', 2, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(462, 17, NULL, 23, '2025-12-20', 0, 'off', 'OFF', '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(463, 17, 404, 21, '2025-12-20', 0, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(464, 17, 405, 24, '2025-12-20', 1, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(465, 17, 406, 25, '2025-12-20', 2, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(466, 17, 407, 10, '2025-12-20', 0, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(467, 17, 408, 11, '2025-12-20', 1, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(468, 17, 409, 12, '2025-12-20', 2, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(469, 17, NULL, 24, '2025-12-21', 0, 'off', 'OFF', '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(470, 17, 410, 23, '2025-12-21', 1, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(471, 17, 411, 10, '2025-12-21', 2, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(472, 17, 412, 11, '2025-12-21', 0, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(473, 17, 413, 12, '2025-12-21', 1, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(474, 17, 414, 21, '2025-12-21', 2, 'scheduled', NULL, '2025-12-08 07:16:02', '2025-12-08 07:16:02'),
(475, 18, 415, 26, '2025-12-22', 0, 'scheduled', NULL, '2025-12-08 07:54:34', '2025-12-08 07:54:34'),
(476, 18, 416, 27, '2025-12-22', 1, 'scheduled', NULL, '2025-12-08 07:54:34', '2025-12-08 07:54:34'),
(477, 18, 417, 28, '2025-12-22', 2, 'scheduled', NULL, '2025-12-08 07:54:34', '2025-12-08 07:54:34'),
(478, 18, 418, 29, '2025-12-22', 0, 'scheduled', NULL, '2025-12-08 07:54:34', '2025-12-08 07:54:34'),
(479, 18, 419, 30, '2025-12-22', 1, 'scheduled', NULL, '2025-12-08 07:54:34', '2025-12-08 07:54:34'),
(480, 18, NULL, 26, '2025-12-23', 0, 'off', 'OFF', '2025-12-08 07:54:34', '2025-12-08 07:54:34'),
(481, 18, 420, 30, '2025-12-23', 0, 'scheduled', NULL, '2025-12-08 07:54:34', '2025-12-08 07:54:34'),
(482, 18, 421, 27, '2025-12-23', 1, 'scheduled', NULL, '2025-12-08 07:54:34', '2025-12-08 07:54:34'),
(483, 18, 422, 28, '2025-12-23', 2, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(484, 18, 423, 29, '2025-12-23', 0, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(485, 18, NULL, 27, '2025-12-24', 0, 'off', 'OFF', '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(486, 18, 424, 26, '2025-12-24', 0, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(487, 18, 425, 29, '2025-12-24', 1, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(488, 18, 426, 30, '2025-12-24', 2, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(489, 18, 427, 28, '2025-12-24', 1, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(490, 18, NULL, 28, '2025-12-25', 0, 'off', 'OFF', '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(491, 18, 428, 27, '2025-12-25', 0, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(492, 18, 429, 26, '2025-12-25', 1, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(493, 18, 430, 29, '2025-12-25', 2, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(494, 18, 431, 30, '2025-12-25', 1, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(495, 18, NULL, 29, '2025-12-26', 0, 'off', 'OFF', '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(496, 18, 432, 26, '2025-12-26', 0, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(497, 18, 433, 27, '2025-12-26', 1, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(498, 18, 434, 28, '2025-12-26', 2, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(499, 18, 435, 30, '2025-12-26', 0, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(500, 18, NULL, 30, '2025-12-27', 0, 'off', 'OFF', '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(501, 18, 436, 29, '2025-12-27', 1, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(502, 18, 437, 26, '2025-12-27', 2, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(503, 18, 438, 27, '2025-12-27', 0, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(504, 18, 439, 28, '2025-12-27', 1, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(505, 18, 440, 29, '2025-12-28', 0, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(506, 18, 441, 30, '2025-12-28', 1, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(507, 18, 442, 26, '2025-12-28', 2, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(508, 18, 443, 27, '2025-12-28', 0, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(509, 18, 444, 28, '2025-12-28', 1, 'scheduled', NULL, '2025-12-08 07:54:35', '2025-12-08 07:54:35'),
(510, 19, 445, 4, '2025-12-22', 0, 'scheduled', NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(511, 19, 446, 5, '2025-12-22', 1, 'scheduled', NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(512, 19, 447, 6, '2025-12-22', 2, 'scheduled', NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(513, 19, 448, 19, '2025-12-22', 3, 'scheduled', NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(514, 19, NULL, 4, '2025-12-23', 0, 'off', 'OFF', '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(515, 19, 449, 6, '2025-12-23', 0, 'scheduled', NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(516, 19, 450, 19, '2025-12-23', 1, 'scheduled', NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(517, 19, 451, 5, '2025-12-23', 2, 'scheduled', NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(518, 19, NULL, 5, '2025-12-24', 0, 'off', 'OFF', '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(519, 19, 452, 4, '2025-12-24', 0, 'scheduled', NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(520, 19, 453, 19, '2025-12-24', 1, 'scheduled', NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(521, 19, 454, 6, '2025-12-24', 2, 'scheduled', NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(522, 19, NULL, 6, '2025-12-25', 0, 'off', 'OFF', '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(523, 19, 455, 4, '2025-12-25', 0, 'scheduled', NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(524, 19, 456, 5, '2025-12-25', 1, 'scheduled', NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(525, 19, 457, 19, '2025-12-25', 2, 'scheduled', NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(526, 19, NULL, 19, '2025-12-26', 0, 'off', 'OFF', '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(527, 19, 458, 5, '2025-12-26', 0, 'scheduled', NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(528, 19, 459, 6, '2025-12-26', 1, 'scheduled', NULL, '2025-12-22 04:18:49', '2025-12-22 04:18:49'),
(529, 19, 460, 4, '2025-12-26', 2, 'scheduled', NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(530, 19, 461, 4, '2025-12-27', 0, 'scheduled', NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(531, 19, 462, 5, '2025-12-27', 1, 'scheduled', NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(532, 19, 463, 6, '2025-12-27', 2, 'scheduled', NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(533, 19, 464, 19, '2025-12-27', 3, 'scheduled', NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(534, 19, 465, 4, '2025-12-28', 0, 'scheduled', NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(535, 19, 466, 5, '2025-12-28', 1, 'scheduled', NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(536, 19, 467, 6, '2025-12-28', 2, 'scheduled', NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(537, 19, 468, 19, '2025-12-28', 3, 'scheduled', NULL, '2025-12-22 04:18:50', '2025-12-22 04:18:50'),
(538, 20, 470, 5, '2025-12-29', 1, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 03:04:25'),
(539, 20, 469, 4, '2025-12-29', 2, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 03:04:25'),
(540, 20, 471, 6, '2025-12-29', 3, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(541, 20, 472, 19, '2025-12-29', 1, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(542, 20, NULL, 4, '2025-12-30', 0, 'off', 'OFF', '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(543, 20, 473, 6, '2025-12-30', 1, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(544, 20, 474, 19, '2025-12-30', 2, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(545, 20, 475, 5, '2025-12-30', 3, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(546, 20, NULL, 5, '2025-12-31', 0, 'off', 'OFF', '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(547, 20, 476, 4, '2025-12-31', 1, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(548, 20, 477, 19, '2025-12-31', 2, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(549, 20, 478, 6, '2025-12-31', 3, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(550, 20, NULL, 6, '2026-01-01', 0, 'off', 'OFF', '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(551, 20, 479, 4, '2026-01-01', 1, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(552, 20, 480, 5, '2026-01-01', 2, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(553, 20, 481, 19, '2026-01-01', 3, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(554, 20, NULL, 19, '2026-01-02', 0, 'off', 'OFF', '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(555, 20, 482, 5, '2026-01-02', 1, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(556, 20, 483, 6, '2026-01-02', 2, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(557, 20, 484, 4, '2026-01-02', 3, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(558, 20, 485, 4, '2026-01-03', 1, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(559, 20, 486, 5, '2026-01-03', 2, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(560, 20, 487, 6, '2026-01-03', 3, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11'),
(561, 20, 488, 19, '2026-01-03', 1, 'scheduled', NULL, '2025-12-23 02:56:11', '2025-12-23 02:56:11');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `approval_rules`
--
ALTER TABLE `approval_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_rules_scope_department_is_active_index` (`scope`,`department`,`is_active`);

--
-- Indeks untuk tabel `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendances_user_id_foreign` (`user_id`),
  ADD KEY `attendances_approved_by_foreign` (`approved_by`),
  ADD KEY `attendances_location_id_foreign` (`location_id`),
  ADD KEY `attendances_shift_id_foreign` (`shift_id`),
  ADD KEY `attendances_shift_assignment_id_foreign` (`shift_assignment_id`);

--
-- Indeks untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_actor_id_index` (`actor_id`),
  ADD KEY `audit_logs_auditable_type_index` (`auditable_type`),
  ADD KEY `audit_logs_auditable_id_index` (`auditable_id`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employees_divisi_id_foreign` (`divisi_id`),
  ADD KEY `employees_location_id_foreign` (`location_id`),
  ADD KEY `employees_default_shift_id_foreign` (`default_shift_id`);

--
-- Indeks untuk tabel `employee_absences`
--
ALTER TABLE `employee_absences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_absences_user_id_date_unique` (`user_id`,`date`),
  ADD KEY `employee_absences_location_id_foreign` (`location_id`);

--
-- Indeks untuk tabel `employee_contracts`
--
ALTER TABLE `employee_contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_contracts_employee_id_foreign` (`employee_id`),
  ADD KEY `employee_contracts_created_by_foreign` (`created_by`);

--
-- Indeks untuk tabel `employee_jobdesk_assignments`
--
ALTER TABLE `employee_jobdesk_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_jobdesk_assignments_jobdesk_id_foreign` (`jobdesk_id`),
  ADD KEY `employee_jobdesk_assignments_created_by_foreign` (`created_by`),
  ADD KEY `employee_jobdesk_assignments_employee_id_jobdesk_id_index` (`employee_id`,`jobdesk_id`);

--
-- Indeks untuk tabel `employee_leaves`
--
ALTER TABLE `employee_leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_leaves_approved_by_foreign` (`approved_by`),
  ADD KEY `employee_leaves_location_id_foreign` (`location_id`),
  ADD KEY `employee_leaves_user_id_start_date_end_date_index` (`user_id`,`start_date`,`end_date`);

--
-- Indeks untuk tabel `employee_leave_balances`
--
ALTER TABLE `employee_leave_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_leave_balances_user_id_year_unique` (`user_id`,`year`);

--
-- Indeks untuk tabel `employee_position_histories`
--
ALTER TABLE `employee_position_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_position_histories_created_by_foreign` (`created_by`),
  ADD KEY `employee_position_histories_employee_id_start_date_index` (`employee_id`,`start_date`);

--
-- Indeks untuk tabel `employee_shift_schedules`
--
ALTER TABLE `employee_shift_schedules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_shift_schedules_employee_id_day_of_week_unique` (`employee_id`,`day_of_week`),
  ADD KEY `employee_shift_schedules_shift_id_foreign` (`shift_id`);

--
-- Indeks untuk tabel `employee_tasks`
--
ALTER TABLE `employee_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_assigned_by_foreign` (`assigned_by`),
  ADD KEY `tasks_assigned_to_foreign` (`assigned_to`),
  ADD KEY `employee_tasks_approved_by_foreign` (`approved_by`),
  ADD KEY `employee_tasks_task_catalog_id_index` (`task_catalog_id`);

--
-- Indeks untuk tabel `employee_transfers`
--
ALTER TABLE `employee_transfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_transfers_employee_id_foreign` (`employee_id`),
  ADD KEY `employee_transfers_from_location_id_foreign` (`from_location_id`),
  ADD KEY `employee_transfers_to_location_id_foreign` (`to_location_id`),
  ADD KEY `employee_transfers_created_by_foreign` (`created_by`);

--
-- Indeks untuk tabel `employee_work_recaps`
--
ALTER TABLE `employee_work_recaps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `work_recaps_emp_loc_month_unique` (`employee_id`,`location_id`,`year`,`month`),
  ADD KEY `employee_work_recaps_location_id_foreign` (`location_id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `holidays_date_location_id_unique` (`date`,`location_id`),
  ADD KEY `holidays_location_id_foreign` (`location_id`);

--
-- Indeks untuk tabel `jobdesks`
--
ALTER TABLE `jobdesks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobdesks_location_id_foreign` (`location_id`),
  ADD KEY `jobdesks_created_by_foreign` (`created_by`);

--
-- Indeks untuk tabel `jobdesk_output_targets`
--
ALTER TABLE `jobdesk_output_targets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jobdesk_targets_unique` (`jobdesk_id`,`employee_id`,`year`,`month`),
  ADD KEY `jobdesk_output_targets_employee_id_foreign` (`employee_id`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `locations_code_unique` (`code`);

--
-- Indeks untuk tabel `location_change_requests`
--
ALTER TABLE `location_change_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_change_requests_user_id_foreign` (`user_id`),
  ADD KEY `location_change_requests_original_location_id_foreign` (`original_location_id`),
  ADD KEY `location_change_requests_target_location_id_foreign` (`target_location_id`),
  ADD KEY `location_change_requests_approved_by_foreign` (`approved_by`);

--
-- Indeks untuk tabel `location_settings`
--
ALTER TABLE `location_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `location_settings_location_id_key_unique` (`location_id`,`key`);

--
-- Indeks untuk tabel `location_shifts`
--
ALTER TABLE `location_shifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `location_shifts_location_id_shift_id_unique` (`location_id`,`shift_id`),
  ADD KEY `location_shifts_shift_id_foreign` (`shift_id`);

--
-- Indeks untuk tabel `location_work_targets`
--
ALTER TABLE `location_work_targets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loc_work_targets_unique` (`location_id`,`employee_id`,`year`,`month`),
  ADD KEY `location_work_targets_employee_id_foreign` (`employee_id`);

--
-- Indeks untuk tabel `master_tasks`
--
ALTER TABLE `master_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `master_tasks_assigned_by_foreign` (`assigned_by`),
  ADD KEY `master_tasks_assigned_to_foreign` (`assigned_to`);

--
-- Indeks untuk tabel `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`),
  ADD KEY `messages_receiver_id_foreign` (`receiver_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indeks untuk tabel `overtime_approvals`
--
ALTER TABLE `overtime_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `overtime_approvals_overtime_request_id_foreign` (`overtime_request_id`),
  ADD KEY `overtime_approvals_master_id_foreign` (`master_id`);

--
-- Indeks untuk tabel `overtime_requests`
--
ALTER TABLE `overtime_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `overtime_requests_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reports_ticket_number_unique` (`ticket_number`),
  ADD KEY `reports_assigned_admin_id_foreign` (`assigned_admin_id`),
  ADD KEY `reports_reporter_id_status_index` (`reporter_id`,`status`),
  ADD KEY `reports_location_id_status_index` (`location_id`,`status`);

--
-- Indeks untuk tabel `report_approvals`
--
ALTER TABLE `report_approvals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `report_approvals_report_id_approver_role_step_order_unique` (`report_id`,`approver_role`,`step_order`),
  ADD KEY `report_approvals_approver_id_foreign` (`approver_id`),
  ADD KEY `report_approvals_report_id_status_index` (`report_id`,`status`);

--
-- Indeks untuk tabel `report_attachments`
--
ALTER TABLE `report_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_attachments_uploaded_by_foreign` (`uploaded_by`),
  ADD KEY `report_attachments_report_id_index` (`report_id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shifts_code_unique` (`code`);

--
-- Indeks untuk tabel `shift_assignments`
--
ALTER TABLE `shift_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shift_assignments_user_id_date_unique` (`user_id`,`date`),
  ADD KEY `shift_assignments_shift_id_date_index` (`shift_id`,`date`),
  ADD KEY `shift_assignments_location_shift_id_foreign` (`location_shift_id`),
  ADD KEY `shift_assignments_location_date_idx` (`location_id`,`date`);

--
-- Indeks untuk tabel `task_catalogs`
--
ALTER TABLE `task_catalogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_catalogs_jobdesk_id_is_active_index` (`jobdesk_id`,`is_active`);

--
-- Indeks untuk tabel `task_progress_updates`
--
ALTER TABLE `task_progress_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_progress_updates_task_id_foreign` (`task_id`),
  ADD KEY `task_progress_updates_user_id_foreign` (`user_id`),
  ADD KEY `task_progress_updates_approved_by_foreign` (`approved_by`),
  ADD KEY `task_progress_updates_approval_status_approval_level_index` (`approval_status`,`approval_level`);

--
-- Indeks untuk tabel `task_slots`
--
ALTER TABLE `task_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_slots_task_id_foreign` (`task_id`),
  ADD KEY `task_slots_approved_by_foreign` (`approved_by`),
  ADD KEY `task_slots_created_by_foreign` (`created_by`);

--
-- Indeks untuk tabel `task_slot_attachments`
--
ALTER TABLE `task_slot_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_slot_attachments_task_slot_id_foreign` (`task_slot_id`),
  ADD KEY `task_slot_attachments_uploaded_by_foreign` (`uploaded_by`);

--
-- Indeks untuk tabel `task_slot_history`
--
ALTER TABLE `task_slot_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_slot_history_task_slot_id_foreign` (`task_slot_id`),
  ADD KEY `task_slot_history_actor_id_foreign` (`actor_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_karyawan_id_foreign` (`karyawan_id`),
  ADD KEY `users_location_id_foreign` (`location_id`);

--
-- Indeks untuk tabel `weekly_offs`
--
ALTER TABLE `weekly_offs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `weekly_offs_location_id_user_id_day_of_week_unique` (`location_id`,`user_id`,`day_of_week`),
  ADD KEY `weekly_offs_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `weekly_rosters`
--
ALTER TABLE `weekly_rosters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wr_loc_week_unique` (`location_id`,`week_start`);

--
-- Indeks untuk tabel `weekly_roster_entries`
--
ALTER TABLE `weekly_roster_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wre_roster_user_date_slot_unique` (`weekly_roster_id`,`user_id`,`date`,`slot_index`),
  ADD KEY `weekly_roster_entries_shift_assignment_id_foreign` (`shift_assignment_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `approval_rules`
--
ALTER TABLE `approval_rules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `employee_absences`
--
ALTER TABLE `employee_absences`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `employee_contracts`
--
ALTER TABLE `employee_contracts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `employee_jobdesk_assignments`
--
ALTER TABLE `employee_jobdesk_assignments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `employee_leaves`
--
ALTER TABLE `employee_leaves`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `employee_leave_balances`
--
ALTER TABLE `employee_leave_balances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `employee_position_histories`
--
ALTER TABLE `employee_position_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `employee_shift_schedules`
--
ALTER TABLE `employee_shift_schedules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `employee_tasks`
--
ALTER TABLE `employee_tasks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT untuk tabel `employee_transfers`
--
ALTER TABLE `employee_transfers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `employee_work_recaps`
--
ALTER TABLE `employee_work_recaps`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `jobdesks`
--
ALTER TABLE `jobdesks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `jobdesk_output_targets`
--
ALTER TABLE `jobdesk_output_targets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `location_change_requests`
--
ALTER TABLE `location_change_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `location_settings`
--
ALTER TABLE `location_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `location_shifts`
--
ALTER TABLE `location_shifts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `location_work_targets`
--
ALTER TABLE `location_work_targets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `master_tasks`
--
ALTER TABLE `master_tasks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT untuk tabel `overtime_approvals`
--
ALTER TABLE `overtime_approvals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `overtime_requests`
--
ALTER TABLE `overtime_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `report_approvals`
--
ALTER TABLE `report_approvals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `report_attachments`
--
ALTER TABLE `report_attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `shift_assignments`
--
ALTER TABLE `shift_assignments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=489;

--
-- AUTO_INCREMENT untuk tabel `task_catalogs`
--
ALTER TABLE `task_catalogs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `task_progress_updates`
--
ALTER TABLE `task_progress_updates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `task_slots`
--
ALTER TABLE `task_slots`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT untuk tabel `task_slot_attachments`
--
ALTER TABLE `task_slot_attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `task_slot_history`
--
ALTER TABLE `task_slot_history`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `weekly_offs`
--
ALTER TABLE `weekly_offs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `weekly_rosters`
--
ALTER TABLE `weekly_rosters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `weekly_roster_entries`
--
ALTER TABLE `weekly_roster_entries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=562;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendances_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `attendances_shift_assignment_id_foreign` FOREIGN KEY (`shift_assignment_id`) REFERENCES `shift_assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`),
  ADD CONSTRAINT `attendances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_default_shift_id_foreign` FOREIGN KEY (`default_shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_divisi_id_foreign` FOREIGN KEY (`divisi_id`) REFERENCES `divisions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employees_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`);

--
-- Ketidakleluasaan untuk tabel `employee_absences`
--
ALTER TABLE `employee_absences`
  ADD CONSTRAINT `employee_absences_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_absences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `employee_contracts`
--
ALTER TABLE `employee_contracts`
  ADD CONSTRAINT `employee_contracts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_contracts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `employee_jobdesk_assignments`
--
ALTER TABLE `employee_jobdesk_assignments`
  ADD CONSTRAINT `employee_jobdesk_assignments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_jobdesk_assignments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_jobdesk_assignments_jobdesk_id_foreign` FOREIGN KEY (`jobdesk_id`) REFERENCES `jobdesks` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `employee_leaves`
--
ALTER TABLE `employee_leaves`
  ADD CONSTRAINT `employee_leaves_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_leaves_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_leaves_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `employee_leave_balances`
--
ALTER TABLE `employee_leave_balances`
  ADD CONSTRAINT `employee_leave_balances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `employee_position_histories`
--
ALTER TABLE `employee_position_histories`
  ADD CONSTRAINT `employee_position_histories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_position_histories_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `employee_shift_schedules`
--
ALTER TABLE `employee_shift_schedules`
  ADD CONSTRAINT `employee_shift_schedules_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_shift_schedules_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `employee_tasks`
--
ALTER TABLE `employee_tasks`
  ADD CONSTRAINT `employee_tasks_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_tasks_task_catalog_id_foreign` FOREIGN KEY (`task_catalog_id`) REFERENCES `task_catalogs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `employee_transfers`
--
ALTER TABLE `employee_transfers`
  ADD CONSTRAINT `employee_transfers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_transfers_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_transfers_from_location_id_foreign` FOREIGN KEY (`from_location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_transfers_to_location_id_foreign` FOREIGN KEY (`to_location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `employee_work_recaps`
--
ALTER TABLE `employee_work_recaps`
  ADD CONSTRAINT `employee_work_recaps_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_work_recaps_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `holidays`
--
ALTER TABLE `holidays`
  ADD CONSTRAINT `holidays_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jobdesks`
--
ALTER TABLE `jobdesks`
  ADD CONSTRAINT `jobdesks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `jobdesks_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `jobdesk_output_targets`
--
ALTER TABLE `jobdesk_output_targets`
  ADD CONSTRAINT `jobdesk_output_targets_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `jobdesk_output_targets_jobdesk_id_foreign` FOREIGN KEY (`jobdesk_id`) REFERENCES `jobdesks` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `location_change_requests`
--
ALTER TABLE `location_change_requests`
  ADD CONSTRAINT `location_change_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `location_change_requests_original_location_id_foreign` FOREIGN KEY (`original_location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `location_change_requests_target_location_id_foreign` FOREIGN KEY (`target_location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `location_change_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `location_settings`
--
ALTER TABLE `location_settings`
  ADD CONSTRAINT `location_settings_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `location_shifts`
--
ALTER TABLE `location_shifts`
  ADD CONSTRAINT `location_shifts_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `location_shifts_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `location_work_targets`
--
ALTER TABLE `location_work_targets`
  ADD CONSTRAINT `location_work_targets_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `location_work_targets_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `master_tasks`
--
ALTER TABLE `master_tasks`
  ADD CONSTRAINT `master_tasks_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `master_tasks_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `overtime_approvals`
--
ALTER TABLE `overtime_approvals`
  ADD CONSTRAINT `overtime_approvals_master_id_foreign` FOREIGN KEY (`master_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `overtime_approvals_overtime_request_id_foreign` FOREIGN KEY (`overtime_request_id`) REFERENCES `overtime_requests` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `overtime_requests`
--
ALTER TABLE `overtime_requests`
  ADD CONSTRAINT `overtime_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_assigned_admin_id_foreign` FOREIGN KEY (`assigned_admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reports_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reports_reporter_id_foreign` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `report_approvals`
--
ALTER TABLE `report_approvals`
  ADD CONSTRAINT `report_approvals_approver_id_foreign` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `report_approvals_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `report_attachments`
--
ALTER TABLE `report_attachments`
  ADD CONSTRAINT `report_attachments_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `report_attachments_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `shift_assignments`
--
ALTER TABLE `shift_assignments`
  ADD CONSTRAINT `shift_assignments_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shift_assignments_location_shift_id_foreign` FOREIGN KEY (`location_shift_id`) REFERENCES `location_shifts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shift_assignments_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shift_assignments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `task_catalogs`
--
ALTER TABLE `task_catalogs`
  ADD CONSTRAINT `task_catalogs_jobdesk_id_foreign` FOREIGN KEY (`jobdesk_id`) REFERENCES `jobdesks` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `task_progress_updates`
--
ALTER TABLE `task_progress_updates`
  ADD CONSTRAINT `task_progress_updates_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `task_progress_updates_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `employee_tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_progress_updates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `task_slots`
--
ALTER TABLE `task_slots`
  ADD CONSTRAINT `task_slots_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `task_slots_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `task_slots_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `employee_tasks` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `task_slot_attachments`
--
ALTER TABLE `task_slot_attachments`
  ADD CONSTRAINT `task_slot_attachments_task_slot_id_foreign` FOREIGN KEY (`task_slot_id`) REFERENCES `task_slots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_slot_attachments_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `task_slot_history`
--
ALTER TABLE `task_slot_history`
  ADD CONSTRAINT `task_slot_history_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `task_slot_history_task_slot_id_foreign` FOREIGN KEY (`task_slot_id`) REFERENCES `task_slots` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `users_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`);

--
-- Ketidakleluasaan untuk tabel `weekly_offs`
--
ALTER TABLE `weekly_offs`
  ADD CONSTRAINT `weekly_offs_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `weekly_offs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `weekly_roster_entries`
--
ALTER TABLE `weekly_roster_entries`
  ADD CONSTRAINT `weekly_roster_entries_shift_assignment_id_foreign` FOREIGN KEY (`shift_assignment_id`) REFERENCES `shift_assignments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
