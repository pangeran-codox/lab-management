-- PostgreSQL Dump (converted from MySQL)
-- Converted for lab-management project
-- Run this on PostgreSQL database

SET client_encoding = 'UTF8';
BEGIN;

--
-- Basis data: "lab-management"
--

-- --------------------------------------------------------

--
-- Struktur dari tabel activity_logs
--

CREATE TABLE activity_logs (
  id BIGSERIAL PRIMARY KEY,
  user_id BIGINT DEFAULT NULL,
  user_name varchar(255) DEFAULT NULL,
  action varchar(100) NOT NULL,
  entity_type varchar(50) DEFAULT NULL,
  entity_id BIGINT DEFAULT NULL,
  table_name varchar(50) DEFAULT NULL,
  record_id BIGINT DEFAULT NULL,
  description text,
  changes TEXT,
  ip_address varchar(45) DEFAULT NULL,
  user_agent text,
  metadata TEXT,
  created_at timestamp NULL DEFAULT NOW()
) ;

--
-- Dumping data untuk tabel activity_logs
--

INSERT INTO activity_logs (id, user_id, user_name, action, entity_type, entity_id, table_name, record_id, description, changes, ip_address, user_agent, metadata, created_at) VALUES
(1, NULL, 'System', 'CREATE', 'class', 1, NULL, NULL, 'Kelas baru ditambahkan: 8-A - Kelas 8 A', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:48:19'),
(2, NULL, 'System', 'CREATE', 'class', 2, NULL, NULL, 'Kelas baru ditambahkan: 8-B - Kelas 8 B', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:48:44'),
(3, NULL, 'System', 'CREATE', 'class', 3, NULL, NULL, 'Kelas baru ditambahkan: 8-C - Kelas 8 C', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:49:00'),
(4, NULL, 'System', 'CREATE', 'class', 4, NULL, NULL, 'Kelas baru ditambahkan: 8-D - Kelas 8 D', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:50:20'),
(5, NULL, 'System', 'CREATE', 'class', 5, NULL, NULL, 'Kelas baru ditambahkan: 8-E - Kelas 8 E', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:50:33'),
(6, NULL, 'System', 'CREATE', 'class', 6, NULL, NULL, 'Kelas baru ditambahkan: 8-F - Kelas 8 F', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:50:55'),
(7, NULL, 'System', 'CREATE', 'class', 7, NULL, NULL, 'Kelas baru ditambahkan: 8-G - Kelas 8 G', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:52:36'),
(8, NULL, 'System', 'CREATE', 'class', 8, NULL, NULL, 'Kelas baru ditambahkan: 8-H - Kelas 8 H', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:53:31'),
(9, NULL, 'System', 'CREATE', 'class', 9, NULL, NULL, 'Kelas baru ditambahkan: 8-I - Kelas 8 I', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:53:48'),
(10, NULL, 'System', 'CREATE', 'class', 10, NULL, NULL, 'Kelas baru ditambahkan: 9-A - Kelas 9 A', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:54:08'),
(11, NULL, 'System', 'CREATE', 'class', 11, NULL, NULL, 'Kelas baru ditambahkan: 9-B - Kelas 9 B', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:54:27'),
(12, NULL, 'System', 'CREATE', 'class', 12, NULL, NULL, 'Kelas baru ditambahkan: 9-C - Kelas 9 C', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:54:49'),
(13, NULL, 'System', 'CREATE', 'class', 13, NULL, NULL, 'Kelas baru ditambahkan: 9-D - Kelas 9 D', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:55:08'),
(14, NULL, 'System', 'CREATE', 'class', 14, NULL, NULL, 'Kelas baru ditambahkan: 9-E - Kelas 9 E', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:55:26'),
(15, NULL, 'System', 'CREATE', 'class', 15, NULL, NULL, 'Kelas baru ditambahkan: 9-F - Kelas 9 F', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:55:37'),
(16, NULL, 'System', 'CREATE', 'class', 16, NULL, NULL, 'Kelas baru ditambahkan: 9-G - Kelas 9 G', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:56:01'),
(17, NULL, 'System', 'CREATE', 'class', 17, NULL, NULL, 'Kelas baru ditambahkan: 9-H - Kelas 9 H', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:56:28'),
(18, NULL, 'System', 'CREATE', 'class', 18, NULL, NULL, 'Kelas baru ditambahkan: 9-I - Kelas 9 I', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 03:56:41'),
(19, NULL, 'System', 'CREATE', 'class', 19, NULL, NULL, 'Kelas baru ditambahkan: XII-IPA-1 - Kelas XII IPA 1', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 04:00:07'),
(20, NULL, 'System', 'CREATE', 'class', 20, NULL, NULL, 'Kelas baru ditambahkan: XII-IPA-2 - Kelas XII IPA 2', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 04:00:36'),
(21, NULL, 'System', 'CREATE', 'class', 21, NULL, NULL, 'Kelas baru ditambahkan: XII-IPA-3 - Kelas XII IPA 3', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 04:01:00'),
(22, NULL, 'System', 'CREATE', 'class', 22, NULL, NULL, 'Kelas baru ditambahkan: XII-IPS-1 - Kelas XII IPS 1', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 04:01:27'),
(23, NULL, 'System', 'CREATE', 'class', 23, NULL, NULL, 'Kelas baru ditambahkan: XII-IPS-2 - Kelas XII IPS 2', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 04:01:52'),
(24, NULL, 'System', 'create', 'schedule', 1, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.0.6', NULL, NULL, '2026-01-13 04:32:11'),
(25, NULL, 'System', 'create', 'schedule', 2, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 13:56:47'),
(26, NULL, 'System', 'create', 'schedule', 3, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:01:09'),
(27, NULL, 'System', 'create', 'schedule', 4, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:01:36'),
(28, NULL, 'System', 'create', 'schedule', 5, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:02:06'),
(29, NULL, 'System', 'create', 'schedule', 6, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:02:26'),
(30, NULL, 'System', 'CREATE', 'class', 24, NULL, NULL, 'Kelas baru ditambahkan: EXC - Kelas EXC', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:10:11'),
(31, NULL, 'System', 'create', 'schedule', 7, NULL, NULL, 'Jadwal baru ditambahkan: Tentor - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:11:14'),
(32, NULL, 'System', 'create', 'schedule', 8, NULL, NULL, 'Jadwal baru ditambahkan: Tentor - KIR', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:12:51'),
(33, NULL, 'System', 'create', 'schedule', 9, NULL, NULL, 'Jadwal baru ditambahkan: Tentor - KIR', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:13:35'),
(34, NULL, 'System', 'create', 'schedule', 10, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:17:11'),
(35, NULL, 'System', 'create', 'schedule', 11, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:17:32'),
(36, NULL, 'System', 'create', 'schedule', 12, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:19:01'),
(37, NULL, 'System', 'create', 'schedule', 13, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:19:19'),
(38, NULL, 'System', 'create', 'schedule', 14, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:20:15'),
(39, NULL, 'System', 'create', 'schedule', 15, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:20:47'),
(40, NULL, 'System', 'create', 'schedule', 16, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:22:03'),
(41, NULL, 'System', 'create', 'schedule', 17, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:22:27'),
(42, NULL, 'System', 'create', 'schedule', 18, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:23:25'),
(43, NULL, 'System', 'create', 'schedule', 19, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:23:44'),
(44, NULL, 'System', 'create', 'schedule', 20, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:24:19'),
(45, NULL, 'System', 'create', 'schedule', 21, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:25:06'),
(46, NULL, 'System', 'create', 'schedule', 22, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:28:21'),
(47, NULL, 'System', 'create', 'schedule', 23, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:28:50'),
(48, NULL, 'System', 'create', 'schedule', 24, NULL, NULL, 'Jadwal baru ditambahkan: Tentor - KIR', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:32:19'),
(49, NULL, 'System', 'create', 'schedule', 25, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - KIR', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:32:42'),
(50, NULL, 'System', 'create', 'schedule', 26, NULL, NULL, 'Jadwal baru ditambahkan: Tentor - KIR', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:34:32'),
(51, NULL, 'System', 'create', 'schedule', 27, NULL, NULL, 'Jadwal baru ditambahkan: Tentor - KIR', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:34:53'),
(52, NULL, 'System', 'create', 'schedule', 28, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:35:38'),
(53, NULL, 'System', 'create', 'schedule', 29, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:35:57'),
(54, NULL, 'System', 'create', 'schedule', 30, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:37:15'),
(55, NULL, 'System', 'create', 'schedule', 31, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:37:37'),
(56, NULL, 'System', 'create', 'schedule', 32, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:37:52'),
(57, NULL, 'System', 'create', 'schedule', 33, NULL, NULL, 'Jadwal baru ditambahkan: Bu Husnul - TIK', NULL, '170.1.1.1', NULL, NULL, '2026-01-15 14:38:18'),
(58, NULL, 'dasd', 'CREATE', 'booking', 1, NULL, NULL, 'Booking baru dibuat: BKG-20260202-0001 - asa', NULL, NULL, NULL, NULL, '2026-02-02 13:56:40'),
(59, NULL, 'Admin', 'APPROVE', 'booking', 1, NULL, NULL, 'Booking disetujui: asa', NULL, NULL, NULL, NULL, '2026-02-03 13:19:48'),
(60, NULL, 'dasdad', 'CREATE', 'booking', 2, NULL, NULL, 'Booking baru dibuat: BKG-20260203-0002 - adas', NULL, NULL, NULL, NULL, '2026-02-03 13:24:39'),
(61, NULL, 'dasdad', 'CREATE', 'booking', 3, NULL, NULL, 'Booking baru dibuat: BKG-20260203-0003 - adas', NULL, NULL, NULL, NULL, '2026-02-03 13:24:39'),
(62, NULL, 'Admin', 'APPROVE', 'booking', 2, NULL, NULL, 'Booking disetujui: adas', NULL, NULL, NULL, NULL, '2026-02-03 13:24:57'),
(63, NULL, 'Admin', 'APPROVE', 'booking', 3, NULL, NULL, 'Booking disetujui: adas', NULL, NULL, NULL, NULL, '2026-02-03 13:25:00'),
(64, NULL, 'System', 'CREATE', 'resource', 3, NULL, NULL, 'Lab baru ditambahkan: Lab Komputer 1', NULL, '103.164.212.121', NULL, NULL, '2026-02-05 04:59:15'),
(65, NULL, 'System', 'CREATE', 'resource', 4, NULL, NULL, 'Lab baru ditambahkan: Lab Komputer 2', NULL, '103.164.212.121', NULL, NULL, '2026-02-05 05:00:42'),
(66, NULL, 'System', 'CREATE', 'resource', 5, NULL, NULL, 'Lab baru ditambahkan: Lab Komputer SMP', NULL, '103.164.212.121', NULL, NULL, '2026-02-05 05:04:29'),
(67, NULL, 'System', 'CREATE', 'resource', 6, NULL, NULL, 'Lab baru ditambahkan: Lab Komputer 3', NULL, '103.164.212.121', NULL, NULL, '2026-02-06 03:23:27'),
(68, NULL, 'System', 'CREATE', 'resource', 7, NULL, NULL, 'Lab baru ditambahkan: Lab Komputer 4', NULL, '103.164.212.121', NULL, NULL, '2026-02-06 03:25:17'),
(69, NULL, 'System', 'CREATE', 'resource', 8, NULL, NULL, 'Lab baru ditambahkan: Lab Fiber Optic', NULL, '103.164.212.121', NULL, NULL, '2026-02-06 03:26:32'),
(70, NULL, 'System', 'CREATE', 'role', NULL, NULL, NULL, 'Role admin dan teknisi ditambahkan via setup script', NULL, NULL, NULL, NULL, '2026-02-20 13:59:27'),
(71, NULL, 'System', 'CREATE', 'user', NULL, NULL, NULL, 'User teknisi1 ditambahkan sebagai PIC Lab Komputer 7 & 8', NULL, NULL, NULL, NULL, '2026-02-20 13:59:27'),
(72, NULL, 'System', 'CREATE', 'role', NULL, NULL, NULL, 'Role admin & teknisi ditambahkan', NULL, NULL, NULL, NULL, '2026-02-20 14:08:02'),
(73, NULL, 'System', 'CREATE', 'user', NULL, NULL, NULL, 'User teknisi1 dibuat, assigned PIC Lab 7 & Lab 8', NULL, NULL, NULL, NULL, '2026-02-20 14:08:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel assignments
--

CREATE TABLE assignments (
  id BIGSERIAL PRIMARY KEY,
  teacher_id BIGINT NOT NULL,
  organization_id BIGINT DEFAULT NULL,
  title varchar(255) NOT NULL,
  description text,
  subject_name varchar(255) NOT NULL,
  class_name varchar(255) NOT NULL,
  deadline TIMESTAMP NOT NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  attachment_path varchar(255) DEFAULT NULL,
  attachment_name varchar(255) DEFAULT NULL,
  attachment_size varchar(255) DEFAULT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL
);

--
-- Dumping data untuk tabel assignments
--

INSERT INTO assignments (id, teacher_id, organization_id, title, description, subject_name, class_name, deadline, is_active, attachment_path, attachment_name, attachment_size, created_at, updated_at) VALUES
(1, 1, NULL, 'sad', 'asdasd', 'TIK', 'X ipa 1', '2026-02-28 21:14:00', TRUE, 'attachments/XKDazzd0Wgih9nHXKckzIw7DUXVPb36KYff2cOqv.xlsx', 'Inventaris_Semua_Lab_2026-02-07_184029.xlsx', '19.3 KB', '2026-02-27 21:14:44', '2026-02-27 21:14:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel assignment_submissions
--

CREATE TABLE assignment_submissions (
  id BIGSERIAL PRIMARY KEY,
  assignment_id BIGINT NOT NULL,
  student_name varchar(255) NOT NULL,
  student_class varchar(255) NOT NULL,
  file_path varchar(255) NOT NULL,
  file_name varchar(255) NOT NULL,
  file_size varchar(255) DEFAULT NULL,
  file_ext varchar(255) DEFAULT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'submitted',
  grade decimal(5,2) DEFAULT NULL,
  feedback text,
  submitted_at timestamp NOT NULL DEFAULT NOW(),
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL
);

--
-- Dumping data untuk tabel assignment_submissions
--

INSERT INTO assignment_submissions (id, assignment_id, student_name, student_class, file_path, file_name, file_size, file_ext, status, grade, feedback, submitted_at, created_at, updated_at) VALUES
(1, 1, 'bang ucup', 'xii ipa', 'submissions/1/4mgS3noIUF0klrNtHqkb6icCMu9ozx6fO4eKlnjL.xlsx', 'Inventaris_Semua_Lab_2026-02-07_184029 (1).xlsx', '19.3 KB', 'xlsx', 'graded', 11.00, NULL, '2026-02-27 21:27:23', '2026-02-27 21:27:23', '2026-02-28 12:39:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel bookings
--

CREATE TABLE bookings (
  id BIGSERIAL PRIMARY KEY,
  session_id UUID DEFAULT NULL,
  teacher_id BIGINT DEFAULT NULL,
  resource_id BIGINT NOT NULL,
  time_slot_id BIGINT NOT NULL,
  user_id BIGINT DEFAULT NULL,
  organization_id BIGINT NOT NULL,
  booking_date date NOT NULL,
  teacher_name varchar(255) NOT NULL,
  teacher_phone varchar(50) NOT NULL,
  class_name varchar(255) DEFAULT NULL,
  subject_name varchar(255) DEFAULT NULL,
  title varchar(500) NOT NULL,
  description text,
  participant_count int DEFAULT '0',
  status varchar(50) DEFAULT 'pending',
  approved_by BIGINT DEFAULT NULL,
  approved_at timestamp NULL DEFAULT NULL,
  notes text,
  created_at timestamp NULL DEFAULT NOW(),
  updated_at timestamp NULL DEFAULT NOW()
);

--
-- Dumping data untuk tabel bookings
--

INSERT INTO bookings (id, session_id, teacher_id, resource_id, time_slot_id, user_id, organization_id, booking_date, teacher_name, teacher_phone, class_name, subject_name, title, description, participant_count, status, approved_by, approved_at, notes, created_at, updated_at) VALUES
(1, '24d5d847-20df-44b2-9f64-f1909e97a581', NULL, 1, 5, NULL, 1, '2026-02-03', 'dasd', '083265485687', 'X ipa 1', 'TIK', 'asa', 'saa', 10, 'approved', 1, '2026-02-03 13:19:48', NULL, '2026-02-02 13:56:40', '2026-05-16 06:51:43'),
(2, '1388a485-3b23-4bab-9095-18059ad5e7fe', NULL, 2, 1, NULL, 5, '2026-02-04', 'dasdad', '083265485687', '12', 'TIK', 'adas', 'sad', 7, 'approved', 1, '2026-02-03 13:24:57', NULL, '2026-02-03 13:24:39', '2026-05-16 06:51:43'),
(3, '1388a485-3b23-4bab-9095-18059ad5e7fe', NULL, 2, 2, NULL, 5, '2026-02-04', 'dasdad', '083265485687', '12', 'TIK', 'adas', 'sad', 7, 'approved', 1, '2026-02-03 13:25:00', NULL, '2026-02-03 13:24:39', '2026-05-16 06:51:43'),
(4, '8d082824-bea5-4c64-9f7a-1d5e33953286', NULL, 1, 1, NULL, 1, '2026-02-23', 'bang ucup', '081217574441', 'Kelas XII IPA 1', 'tik', 'grafik', NULL, 10, 'approved', 5, '2026-02-21 12:53:49', NULL, '2026-02-21 12:52:57', '2026-05-16 06:51:43'),
(5, '8d082824-bea5-4c64-9f7a-1d5e33953286', NULL, 1, 2, NULL, 1, '2026-02-23', 'bang ucup', '081217574441', 'Kelas XII IPA 1', 'tik', 'grafik', NULL, 10, 'approved', 5, '2026-02-21 12:53:46', NULL, '2026-02-21 12:52:57', '2026-05-16 06:51:43'),
(6, '543bd2d1-98c2-470e-93cd-6dd0f5a96197', NULL, 1, 3, NULL, 2, '2026-02-23', 'pak guru', '083265485687', 'Kelas 9 G', 'TIK', 'ada', NULL, 2, 'approved', 5, '2026-02-21 13:18:04', NULL, '2026-02-21 13:17:42', '2026-05-16 06:51:43'),
(7, 'd8825c01-ab38-42f5-9a63-4177e7c98ff2', NULL, 2, 3, NULL, 2, '2026-02-26', 'bang ucup', '081217574441', 'Kelas 9 E', 'tik', 'tes', NULL, 10, 'approved', 1, '2026-02-26 00:51:39', NULL, '2026-02-26 00:51:09', '2026-05-16 06:51:43'),
(8, 'd8825c01-ab38-42f5-9a63-4177e7c98ff2', NULL, 2, 4, NULL, 2, '2026-02-26', 'bang ucup', '081217574441', 'Kelas 9 E', 'tik', 'tes', NULL, 10, 'approved', 1, '2026-02-26 00:51:37', NULL, '2026-02-26 00:51:09', '2026-05-16 06:51:43'),
(9, '547e0a43-046c-401f-8fb1-72bc071786ae', NULL, 1, 4, NULL, 1, '2026-02-27', 'Bang Ucup', '6281217574441', 'Kelas XII IPA 2', 'inggris', 'praktik', NULL, 12, 'approved', 1, '2026-02-27 01:54:35', NULL, '2026-02-27 01:54:00', '2026-05-16 06:51:43'),
(10, '7da70b5d-fbe5-435f-beb6-1372eb042ba6', NULL, 1, 5, NULL, 1, '2026-02-27', 'Bang Ucup', '6281217574441', 'Kelas XII IPA 1', 'inggris', 'asd', NULL, 3, 'approved', 1, '2026-02-27 01:57:12', NULL, '2026-02-27 01:56:55', '2026-05-16 06:51:43'),
(11, 'db9914e9-a85b-4ded-88ae-0728dba3044d', NULL, 2, 8, NULL, 2, '2026-02-27', 'Bang Ucup', '6281217574441', 'Kelas 9 H', 'tik', 'asaddf', NULL, 2, 'approved', 1, '2026-02-27 02:17:43', NULL, '2026-02-27 02:16:50', '2026-05-16 06:51:43'),
(12, '1dca1c5f-5c90-445b-984d-001b2e9ad00d', NULL, 3, 8, NULL, 2, '2026-02-27', 'Alip', '6283112088830', 'Kelas 9 G', 'tik', 'vdgfd', NULL, 4, 'approved', 1, '2026-02-27 03:44:33', NULL, '2026-02-27 03:43:48', '2026-05-16 06:51:43'),
(13, '1dca1c5f-5c90-445b-984d-001b2e9ad00d', NULL, 3, 9, NULL, 2, '2026-02-27', 'Alip', '6283112088830', 'Kelas 9 G', 'tik', 'vdgfd', NULL, 4, 'approved', 1, '2026-02-27 03:44:36', NULL, '2026-02-27 03:43:48', '2026-05-16 06:51:43'),
(14, '1b544aaa-72e0-44e3-82fc-bfe0908f57e4', NULL, 4, 1, NULL, 1, '2026-03-07', 'Bu Husnul', '6281217574441', 'Kelas XII IPA 2', 'aswaja', 'ffd', NULL, 2, 'approved', 1, '2026-02-28 12:35:06', NULL, '2026-02-28 12:33:51', '2026-05-16 06:51:43'),
(15, '1b544aaa-72e0-44e3-82fc-bfe0908f57e4', NULL, 4, 2, NULL, 1, '2026-03-07', 'Bu Husnul', '6281217574441', 'Kelas XII IPA 2', 'aswaja', 'ffd', NULL, 2, 'approved', 1, '2026-02-28 12:35:11', NULL, '2026-02-28 12:33:51', '2026-05-16 06:51:43'),
(16, '1b544aaa-72e0-44e3-82fc-bfe0908f57e4', NULL, 4, 3, NULL, 1, '2026-03-07', 'Bu Husnul', '6281217574441', 'Kelas XII IPA 2', 'aswaja', 'ffd', NULL, 2, 'approved', 1, '2026-02-28 12:35:15', NULL, '2026-02-28 12:33:51', '2026-05-16 06:51:43'),
(17, '1b544aaa-72e0-44e3-82fc-bfe0908f57e4', NULL, 4, 4, NULL, 1, '2026-03-07', 'Bu Husnul', '6281217574441', 'Kelas XII IPA 2', 'aswaja', 'ffd', NULL, 2, 'approved', 1, '2026-02-28 12:35:19', NULL, '2026-02-28 12:33:51', '2026-05-16 06:51:43'),
(18, '1b544aaa-72e0-44e3-82fc-bfe0908f57e4', NULL, 4, 5, NULL, 1, '2026-03-07', 'Bu Husnul', '6281217574441', 'Kelas XII IPA 2', 'aswaja', 'ffd', NULL, 2, 'approved', 1, '2026-02-28 12:35:34', NULL, '2026-02-28 12:33:51', '2026-05-16 06:51:43'),
(19, '1b544aaa-72e0-44e3-82fc-bfe0908f57e4', NULL, 4, 7, NULL, 1, '2026-03-07', 'Bu Husnul', '6281217574441', 'Kelas XII IPA 2', 'aswaja', 'ffd', NULL, 2, 'approved', 1, '2026-02-28 12:35:44', NULL, '2026-02-28 12:33:51', '2026-05-16 06:51:43'),
(20, '1b544aaa-72e0-44e3-82fc-bfe0908f57e4', NULL, 4, 8, NULL, 1, '2026-03-07', 'Bu Husnul', '6281217574441', 'Kelas XII IPA 2', 'aswaja', 'ffd', NULL, 2, 'approved', 1, '2026-02-28 12:35:47', NULL, '2026-02-28 12:33:51', '2026-05-16 06:51:43'),
(21, '1b544aaa-72e0-44e3-82fc-bfe0908f57e4', NULL, 4, 9, NULL, 1, '2026-03-07', 'Bu Husnul', '6281217574441', 'Kelas XII IPA 2', 'aswaja', 'ffd', NULL, 2, 'approved', 1, '2026-02-28 12:35:49', NULL, '2026-02-28 12:33:51', '2026-05-16 06:51:43'),
(22, '1b544aaa-72e0-44e3-82fc-bfe0908f57e4', NULL, 4, 10, NULL, 1, '2026-03-07', 'Bu Husnul', '6281217574441', 'Kelas XII IPA 2', 'aswaja', 'ffd', NULL, 2, 'approved', 1, '2026-02-28 12:35:52', NULL, '2026-02-28 12:33:51', '2026-05-16 06:51:43'),
(23, 'baa65b30-a806-470a-aba6-3bed3cf59891', NULL, 4, 1, NULL, 2, '2026-03-06', 'Bu Husnul', '6281217574441', 'Kelas 9 G', 'tik', 'ada saja', NULL, 2, 'approved', 1, '2026-02-28 18:59:22', NULL, '2026-02-28 18:58:48', '2026-05-16 06:51:43'),
(24, 'baa65b30-a806-470a-aba6-3bed3cf59891', NULL, 4, 2, NULL, 2, '2026-03-06', 'Bu Husnul', '6281217574441', 'Kelas 9 G', 'tik', 'ada saja', NULL, 2, 'approved', 1, '2026-02-28 18:59:22', NULL, '2026-02-28 18:58:48', '2026-05-16 06:51:43'),
(25, 'baa65b30-a806-470a-aba6-3bed3cf59891', NULL, 4, 3, NULL, 2, '2026-03-06', 'Bu Husnul', '6281217574441', 'Kelas 9 G', 'tik', 'ada saja', NULL, 2, 'approved', 1, '2026-02-28 18:59:22', NULL, '2026-02-28 18:58:48', '2026-05-16 06:51:43'),
(26, 'baa65b30-a806-470a-aba6-3bed3cf59891', NULL, 4, 4, NULL, 2, '2026-03-06', 'Bu Husnul', '6281217574441', 'Kelas 9 G', 'tik', 'ada saja', NULL, 2, 'approved', 1, '2026-02-28 18:59:22', NULL, '2026-02-28 18:58:48', '2026-05-16 06:51:43'),
(27, 'baa65b30-a806-470a-aba6-3bed3cf59891', NULL, 4, 5, NULL, 2, '2026-03-06', 'Bu Husnul', '6281217574441', 'Kelas 9 G', 'tik', 'ada saja', NULL, 2, 'approved', 1, '2026-02-28 18:59:22', NULL, '2026-02-28 18:58:48', '2026-05-16 06:51:43'),
(28, 'baa65b30-a806-470a-aba6-3bed3cf59891', NULL, 4, 7, NULL, 2, '2026-03-06', 'Bu Husnul', '6281217574441', 'Kelas 9 G', 'tik', 'ada saja', NULL, 2, 'approved', 1, '2026-02-28 18:59:22', NULL, '2026-02-28 18:58:48', '2026-05-16 06:51:43'),
(29, 'baa65b30-a806-470a-aba6-3bed3cf59891', NULL, 4, 8, NULL, 2, '2026-03-06', 'Bu Husnul', '6281217574441', 'Kelas 9 G', 'tik', 'ada saja', NULL, 2, 'approved', 1, '2026-02-28 18:59:22', NULL, '2026-02-28 18:58:48', '2026-05-16 06:51:43'),
(30, 'baa65b30-a806-470a-aba6-3bed3cf59891', NULL, 4, 9, NULL, 2, '2026-03-06', 'Bu Husnul', '6281217574441', 'Kelas 9 G', 'tik', 'ada saja', NULL, 2, 'approved', 1, '2026-02-28 18:59:22', NULL, '2026-02-28 18:58:48', '2026-05-16 06:51:43'),
(31, 'baa65b30-a806-470a-aba6-3bed3cf59891', NULL, 4, 10, NULL, 2, '2026-03-06', 'Bu Husnul', '6281217574441', 'Kelas 9 G', 'tik', 'ada saja', NULL, 2, 'approved', 1, '2026-02-28 18:59:22', NULL, '2026-02-28 18:58:48', '2026-05-16 06:51:43'),
(32, 'a016a684-232f-4b9b-a037-833780c46e12', NULL, 3, 1, NULL, 2, '2026-03-02', 'Bu Husnul', '6281217574441', 'Kelas 8 H', 'tik', 'tes', NULL, 3, 'approved', 1, '2026-02-28 19:20:59', NULL, '2026-02-28 19:20:35', '2026-05-16 06:51:43'),
(33, 'a016a684-232f-4b9b-a037-833780c46e12', NULL, 3, 2, NULL, 2, '2026-03-02', 'Bu Husnul', '6281217574441', 'Kelas 8 H', 'tik', 'tes', NULL, 3, 'approved', 1, '2026-02-28 19:20:59', NULL, '2026-02-28 19:20:35', '2026-05-16 06:51:43'),
(34, 'a016a684-232f-4b9b-a037-833780c46e12', NULL, 3, 3, NULL, 2, '2026-03-02', 'Bu Husnul', '6281217574441', 'Kelas 8 H', 'tik', 'tes', NULL, 3, 'approved', 1, '2026-02-28 19:20:59', NULL, '2026-02-28 19:20:35', '2026-05-16 06:51:43'),
(35, 'a016a684-232f-4b9b-a037-833780c46e12', NULL, 3, 4, NULL, 2, '2026-03-02', 'Bu Husnul', '6281217574441', 'Kelas 8 H', 'tik', 'tes', NULL, 3, 'approved', 1, '2026-02-28 19:20:59', NULL, '2026-02-28 19:20:35', '2026-05-16 06:51:43'),
(36, 'a016a684-232f-4b9b-a037-833780c46e12', NULL, 3, 5, NULL, 2, '2026-03-02', 'Bu Husnul', '6281217574441', 'Kelas 8 H', 'tik', 'tes', NULL, 3, 'approved', 1, '2026-02-28 19:20:59', NULL, '2026-02-28 19:20:35', '2026-05-16 06:51:43'),
(37, 'a016a684-232f-4b9b-a037-833780c46e12', NULL, 3, 7, NULL, 2, '2026-03-02', 'Bu Husnul', '6281217574441', 'Kelas 8 H', 'tik', 'tes', NULL, 3, 'approved', 1, '2026-02-28 19:20:59', NULL, '2026-02-28 19:20:35', '2026-05-16 06:51:43'),
(38, 'a016a684-232f-4b9b-a037-833780c46e12', NULL, 3, 8, NULL, 2, '2026-03-02', 'Bu Husnul', '6281217574441', 'Kelas 8 H', 'tik', 'tes', NULL, 3, 'approved', 1, '2026-02-28 19:20:59', NULL, '2026-02-28 19:20:35', '2026-05-16 06:51:43'),
(39, 'a016a684-232f-4b9b-a037-833780c46e12', NULL, 3, 9, NULL, 2, '2026-03-02', 'Bu Husnul', '6281217574441', 'Kelas 8 H', 'tik', 'tes', NULL, 3, 'approved', 1, '2026-02-28 19:20:59', NULL, '2026-02-28 19:20:35', '2026-05-16 06:51:43'),
(40, 'a016a684-232f-4b9b-a037-833780c46e12', NULL, 3, 10, NULL, 2, '2026-03-02', 'Bu Husnul', '6281217574441', 'Kelas 8 H', 'tik', 'tes', NULL, 3, 'approved', 1, '2026-02-28 19:20:59', NULL, '2026-02-28 19:20:36', '2026-05-16 06:51:43'),
(41, 'df24ad54-52a0-481c-a45f-7bc7bc174e1b', NULL, 2, 4, NULL, 2, '2026-05-01', 'Bang Ucup', '6281217574441', 'Kelas 9 H', 'TIK', 'sasdas', NULL, 2, 'rejected', NULL, NULL, 'asdfas', '2026-04-30 13:33:32', '2026-05-16 06:51:43'),
(42, 'b3e4e237-1c14-4534-a78c-664bbb116d29', NULL, 2, 3, NULL, 2, '2026-05-01', 'Bang Ucup', '6281217574441', 'Kelas 9 G', 'TIK', 'ada', NULL, 3, 'rejected', NULL, NULL, 'sdfasd', '2026-05-01 00:05:35', '2026-05-16 06:51:43'),
(43, '7e34fea1-513d-4cde-9f30-a482b086a9d1', NULL, 2, 5, NULL, 2, '2026-05-02', 'Bang Ucup', '6281217574441', 'Kelas 8 H', 'TIK', 'adasd', NULL, 3, 'rejected', NULL, NULL, 'dasda', '2026-05-01 00:10:22', '2026-05-16 06:51:43'),
(44, 'ba66ea7b-b0d4-4086-8e51-421c04f8de18', NULL, 1, 2, NULL, 2, '2026-05-01', 'Bang Ucup', '6281217574441', 'Kelas 9 H', 'TIK', 'dasdasdf', NULL, 2, 'rejected', NULL, NULL, 'asdas', '2026-05-01 00:16:48', '2026-05-16 06:51:43'),
(45, 'ba66ea7b-b0d4-4086-8e51-421c04f8de18', NULL, 1, 3, NULL, 2, '2026-05-01', 'Bang Ucup', '6281217574441', 'Kelas 9 H', 'TIK', 'dasdasdf', NULL, 2, 'rejected', NULL, NULL, 'sdasd', '2026-05-01 00:16:48', '2026-05-16 06:51:43'),
(46, 'b4b152b9-cc5a-44d2-9cf8-dcd64b8f7c5b', NULL, 3, 2, NULL, 2, '2026-05-15', 'Bang Ucup', '6281217574441', 'Kelas 9 H', 'bahasa inggris', 'baca buku', NULL, 12, 'rejected', NULL, NULL, 'ada aja', '2026-05-13 13:30:48', '2026-05-16 12:52:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel classes
--

CREATE TABLE classes (
  id BIGSERIAL PRIMARY KEY,
  name varchar(255) NOT NULL,
  pin char(6) DEFAULT NULL,
  organization_id BIGINT NOT NULL,
  grade_level varchar(50) DEFAULT NULL,
  major varchar(100) DEFAULT NULL,
  student_count int DEFAULT '0',
  academic_year varchar(20) DEFAULT NULL,
  semester varchar(10) DEFAULT NULL,
  metadata TEXT,
  is_active BOOLEAN DEFAULT TRUE,
  created_at timestamp NULL DEFAULT NOW(),
  updated_at timestamp NULL DEFAULT NOW(),
  deleted_at timestamp NULL DEFAULT NULL
) ;

--
-- Dumping data untuk tabel classes
--

INSERT INTO classes (id, name, pin, organization_id, grade_level, major, student_count, academic_year, semester, metadata, is_active, created_at, updated_at, deleted_at) VALUES
(1, 'Kelas 8 A', '668268', 2, 'VIII', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:48:19', '2026-05-18 07:01:25', NULL),
(2, 'Kelas 8 B', '545151', 2, 'VIII', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:48:44', '2026-05-18 07:01:25', NULL),
(3, 'Kelas 8 C', '121357', 2, 'VIII', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:49:00', '2026-05-18 07:01:25', NULL),
(4, 'Kelas 8 D', '617570', 2, 'VIII', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:50:20', '2026-05-18 07:01:25', NULL),
(5, 'Kelas 8 E', '187472', 2, 'VIII', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:50:33', '2026-05-18 07:01:25', NULL),
(6, 'Kelas 8 F', '616552', 2, 'VIII', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:50:55', '2026-05-18 07:01:25', NULL),
(7, 'Kelas 8 G', '792691', 2, 'VIII', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:52:36', '2026-05-18 07:01:25', NULL),
(8, 'Kelas 8 H', '014757', 2, 'VIII', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:53:31', '2026-05-18 07:01:25', NULL),
(9, 'Kelas 8 I', '065917', 2, 'VIII', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:53:48', '2026-05-18 07:01:25', NULL),
(10, 'Kelas 9 A', '942042', 2, 'IX', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:54:08', '2026-05-18 07:01:25', NULL),
(11, 'Kelas 9 B', '419269', 2, 'IX', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:54:27', '2026-05-18 07:01:25', NULL),
(12, 'Kelas 9 C', '225511', 2, 'IX', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:54:49', '2026-05-18 07:01:25', NULL),
(13, 'Kelas 9 D', '065476', 2, 'IX', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:55:08', '2026-05-18 07:01:25', NULL),
(14, 'Kelas 9 E', '112656', 2, 'IX', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:55:26', '2026-05-18 07:01:25', NULL),
(15, 'Kelas 9 F', '919582', 2, 'IX', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:55:37', '2026-05-18 07:01:25', NULL),
(16, 'Kelas 9 G', '698955', 2, 'IX', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:56:01', '2026-05-18 07:01:25', NULL),
(17, 'Kelas 9 H', '443523', 2, 'IX', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:56:28', '2026-05-18 07:01:25', NULL),
(18, 'Kelas 9 I', '007426', 2, 'IX', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 03:56:41', '2026-05-18 07:01:26', NULL),
(19, 'Kelas XII IPA 1', '838475', 1, 'XII', 'IPA', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 04:00:07', '2026-05-18 07:01:26', NULL),
(20, 'Kelas XII IPA 2', '051685', 1, 'XII', 'IPA', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 04:00:36', '2026-05-18 07:01:26', NULL),
(21, 'Kelas XII IPA 3', '003888', 1, 'XII', 'IPA', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 04:01:00', '2026-05-18 07:01:26', NULL),
(22, 'Kelas XII IPS 1', '461550', 1, 'XII', 'IPS', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 04:01:27', '2026-05-18 07:01:26', NULL),
(23, 'Kelas XII IPS 2', '252591', 1, 'XII', 'IPS', 0, '2026/2027', '', NULL, TRUE, '2026-01-13 04:01:52', '2026-05-18 07:01:26', NULL),
(24, 'Kelas EXC', '174046', 5, '', '', 0, '2026/2027', '', NULL, TRUE, '2026-01-15 14:10:11', '2026-05-18 07:01:26', NULL),
(25, 'X PK 1', '285078', 3, 'X', 'PK', 0, '2026/2027', NULL, NULL, TRUE, '2026-05-12 14:32:25', '2026-05-18 07:01:26', NULL),
(26, 'X - 1', '134887', 1, 'X', NULL, 0, '2026/2027', NULL, NULL, TRUE, '2026-05-18 22:45:06', '2026-05-18 22:45:06', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel holidays
--

CREATE TABLE holidays (
  id BIGSERIAL PRIMARY KEY,
  date date NOT NULL,
  name varchar(255) NOT NULL,
  type varchar(50) DEFAULT NULL,
  description text,
  is_active BOOLEAN DEFAULT TRUE,
  created_at timestamp NULL DEFAULT NOW(),
  updated_at timestamp NULL DEFAULT NOW()
);

-- --------------------------------------------------------

--
-- Struktur dari tabel lab_inventory
--

CREATE TABLE lab_inventory (
  id BIGSERIAL PRIMARY KEY,
  resource_id BIGINT NOT NULL,
  item_name varchar(255) NOT NULL,
  category VARCHAR(50) NOT NULL DEFAULT 'other',
  brand varchar(100) DEFAULT NULL,
  model varchar(100) DEFAULT NULL,
  serial_number varchar(100) DEFAULT NULL,
  specifications text,
  condition VARCHAR(50) NOT NULL DEFAULT 'good',
  status VARCHAR(50) NOT NULL DEFAULT 'active',
  quantity int NOT NULL DEFAULT '1',
  quantity_good int NOT NULL DEFAULT '0',
  quantity_broken int NOT NULL DEFAULT '0',
  quantity_backup int NOT NULL DEFAULT '0',
  notes text,
  created_by BIGINT DEFAULT NULL,
  updated_by BIGINT DEFAULT NULL,
  created_at timestamp NULL DEFAULT NOW(),
  updated_at timestamp NULL DEFAULT NOW(),
  deleted_at timestamp NULL DEFAULT NULL
);

--
-- Dumping data untuk tabel lab_inventory
--

INSERT INTO lab_inventory (id, resource_id, item_name, category, brand, model, serial_number, specifications, condition, status, quantity, quantity_good, quantity_broken, quantity_backup, notes, created_by, updated_by, created_at, updated_at, deleted_at) VALUES
(1, 1, 'Smartboard', 'computer', 'HISENSE', 'android', NULL, NULL, 'good', 'active', 1, 1, 0, 0, NULL, NULL, NULL, '2026-01-28 14:10:59', '2026-02-04 04:48:14', NULL),
(2, 2, 'mikrotik x86', 'network', 'mikrotik', 'x86', NULL, 'SSD 64 GB', 'good', 'active', 1, 1, 0, 0, NULL, NULL, NULL, '2026-02-01 14:49:08', '2026-02-02 12:48:36', NULL),
(3, 1, 'switch hub', 'network', 'D link', NULL, NULL, '24 port', 'good', 'active', 2, 2, 0, 0, NULL, NULL, NULL, '2026-02-02 02:30:37', '2026-02-02 12:48:36', NULL),
(4, 2, 'switch hub', 'network', 'D link', NULL, NULL, '24 port', 'good', 'active', 2, 2, 0, 0, NULL, NULL, NULL, '2026-02-02 02:56:06', '2026-02-02 12:48:36', NULL),
(5, 1, 'air conditioner', 'furniture', 'crystal', NULL, NULL, '1 pk', 'good', 'active', 1, 1, 0, 0, NULL, NULL, NULL, '2026-02-02 05:04:38', '2026-02-04 04:46:31', NULL),
(6, 2, 'air conditioner', 'furniture', 'midea', NULL, NULL, '1,5 pk', 'good', 'active', 1, 1, 0, 0, NULL, NULL, NULL, '2026-02-02 05:08:21', '2026-02-04 04:45:47', NULL),
(7, 1, 'PC', 'computer', 'venom', NULL, NULL, NULL, 'good', 'active', 21, 20, 1, 0, NULL, NULL, NULL, '2026-02-02 13:12:35', '2026-02-02 13:12:35', NULL),
(8, 2, 'PC', 'computer', 'venom', NULL, NULL, NULL, 'good', 'active', 28, 22, 6, 0, NULL, NULL, NULL, '2026-02-02 13:13:48', '2026-02-04 06:31:30', NULL),
(9, 1, 'Lemari Etalase', 'furniture', NULL, NULL, NULL, NULL, 'good', 'active', 1, 1, 0, 0, NULL, NULL, NULL, '2026-02-02 13:14:49', '2026-02-02 13:14:49', NULL),
(10, 2, 'Lemari Etalase', 'furniture', NULL, NULL, NULL, NULL, 'good', 'active', 1, 1, 0, 0, NULL, NULL, NULL, '2026-02-02 13:15:16', '2026-02-02 13:15:16', NULL),
(11, 1, 'Monitor', 'peripheral', 'Pixel', NULL, NULL, NULL, 'good', 'active', 23, 20, 3, 0, NULL, NULL, NULL, '2026-02-02 13:20:15', '2026-02-02 13:20:15', NULL),
(12, 2, 'Monitor', 'peripheral', '', NULL, NULL, NULL, 'good', 'active', 31, 26, 5, 0, NULL, NULL, NULL, '2026-02-02 13:21:01', '2026-02-04 06:33:47', NULL),
(13, 1, 'air conditioner', 'furniture', 'midea', NULL, NULL, NULL, 'good', 'active', 1, 1, 0, 0, NULL, NULL, NULL, '2026-02-04 04:53:34', '2026-02-04 04:53:34', NULL),
(14, 1, 'monitor', 'peripheral', NULL, NULL, NULL, NULL, 'good', 'active', 26, 21, 5, 0, NULL, NULL, NULL, '2026-02-04 06:00:41', '2026-02-04 06:00:41', NULL),
(15, 1, 'keyboard', 'peripheral', NULL, NULL, NULL, NULL, 'good', 'active', 30, 20, 10, 0, NULL, NULL, NULL, '2026-02-04 06:03:23', '2026-02-04 06:03:23', NULL),
(16, 1, 'mouse', 'peripheral', NULL, NULL, NULL, NULL, 'good', 'active', 30, 19, 11, 0, NULL, NULL, NULL, '2026-02-04 06:05:09', '2026-02-04 06:05:09', NULL),
(17, 1, 'stopkontak', 'peripheral', NULL, NULL, NULL, NULL, 'good', 'active', 30, 29, 1, 0, NULL, NULL, NULL, '2026-02-04 06:07:16', '2026-02-04 06:07:16', NULL),
(18, 1, 'meja komputer', 'furniture', NULL, NULL, NULL, NULL, 'good', 'active', 30, 25, 5, 0, NULL, NULL, NULL, '2026-02-04 06:10:02', '2026-02-04 06:10:02', NULL),
(19, 1, 'meja guru', 'furniture', NULL, NULL, NULL, NULL, 'good', 'active', 2, 2, 0, 0, NULL, NULL, NULL, '2026-02-04 06:11:42', '2026-02-04 06:11:42', NULL),
(20, 1, 'kursi kayu', 'furniture', NULL, NULL, NULL, NULL, 'good', 'active', 7, 7, 0, 0, NULL, NULL, NULL, '2026-02-04 06:12:44', '2026-02-04 06:12:44', NULL),
(21, 1, 'kursi plastik', 'furniture', NULL, NULL, NULL, NULL, 'good', 'active', 21, 19, 2, 0, NULL, NULL, NULL, '2026-02-04 06:14:15', '2026-02-04 06:14:15', NULL),
(22, 1, 'kabel LAN', 'network', NULL, NULL, NULL, NULL, 'good', 'active', 30, 29, 1, 0, NULL, NULL, NULL, '2026-02-04 06:16:18', '2026-02-04 06:16:18', NULL),
(23, 1, 'lampu', 'furniture', NULL, NULL, NULL, NULL, 'good', 'active', 6, 4, 2, 0, NULL, NULL, NULL, '2026-02-04 06:18:27', '2026-02-04 06:18:27', NULL),
(24, 1, 'papan tulis', 'furniture', NULL, NULL, NULL, NULL, 'good', 'active', 1, 1, 0, 0, NULL, NULL, NULL, '2026-02-04 06:19:16', '2026-02-04 06:19:16', NULL),
(25, 2, 'air conditioner', 'furniture', 'sharp', NULL, NULL, NULL, 'good', 'active', 1, 1, 0, 0, NULL, NULL, NULL, '2026-02-04 06:29:40', '2026-02-04 06:29:40', NULL),
(26, 2, 'keyboard', 'peripheral', NULL, NULL, NULL, NULL, 'good', 'active', 27, 27, 0, 0, NULL, NULL, NULL, '2026-02-04 06:35:53', '2026-02-04 06:35:53', NULL),
(27, 2, 'mouse', 'peripheral', NULL, NULL, NULL, NULL, 'good', 'active', 27, 22, 5, 0, NULL, NULL, NULL, '2026-02-04 06:37:32', '2026-02-04 06:37:32', NULL),
(28, 2, 'stopkontak', 'peripheral', NULL, NULL, NULL, NULL, 'good', 'active', 30, 30, 0, 0, NULL, NULL, NULL, '2026-02-04 06:39:51', '2026-02-04 06:39:51', NULL),
(29, 2, 'kabel LAN', 'network', NULL, NULL, NULL, NULL, 'good', 'active', 33, 33, 0, 0, NULL, NULL, NULL, '2026-02-04 06:40:29', '2026-02-04 06:40:29', NULL),
(30, 2, 'meja komputer', 'furniture', NULL, NULL, NULL, NULL, 'good', 'active', 30, 30, 0, 0, NULL, NULL, NULL, '2026-02-04 06:41:34', '2026-02-04 06:41:34', NULL),
(31, 2, 'meja guru', 'furniture', NULL, NULL, NULL, NULL, 'good', 'active', 2, 2, 0, 0, NULL, NULL, NULL, '2026-02-04 06:42:16', '2026-02-04 06:42:16', NULL),
(32, 2, 'kursi kayu', 'furniture', NULL, NULL, NULL, NULL, 'good', 'active', 20, 20, 0, 0, NULL, NULL, NULL, '2026-02-04 06:43:13', '2026-02-04 06:43:13', NULL),
(33, 2, 'kursi plastik', 'furniture', NULL, NULL, NULL, NULL, 'good', 'active', 12, 11, 1, 0, NULL, NULL, NULL, '2026-02-04 06:44:06', '2026-02-04 06:44:06', NULL),
(34, 2, 'papan tulis', 'furniture', NULL, NULL, NULL, NULL, 'good', 'active', 1, 1, 0, 0, NULL, NULL, NULL, '2026-02-04 06:44:30', '2026-02-04 06:44:30', NULL),
(35, 2, 'lampu', 'furniture', NULL, NULL, NULL, NULL, 'good', 'active', 6, 5, 1, 0, NULL, NULL, NULL, '2026-02-04 06:45:37', '2026-02-04 06:45:37', NULL),
(36, 2, 'modem wifi', 'network', NULL, NULL, NULL, NULL, 'good', 'active', 1, 1, 0, 0, NULL, NULL, NULL, '2026-02-04 06:46:15', '2026-02-04 06:46:15', NULL),
(37, 2, 'pc server', 'computer', NULL, NULL, NULL, NULL, 'good', 'active', 1, 1, 0, 0, NULL, NULL, NULL, '2026-02-05 01:00:21', '2026-02-05 01:00:21', NULL),
(38, 6, 'Komputer', 'computer', NULL, NULL, NULL, NULL, 'good', 'active', 18, 8, 10, 0, NULL, NULL, NULL, '2026-02-06 04:16:28', '2026-02-06 04:16:28', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel lab_inventory_history
--

CREATE TABLE lab_inventory_history (
  id BIGSERIAL PRIMARY KEY,
  inventory_id BIGINT NOT NULL,
  action_type VARCHAR(50) NOT NULL,
  old_condition VARCHAR(50) DEFAULT NULL,
  new_condition VARCHAR(50) DEFAULT NULL,
  old_status VARCHAR(50) DEFAULT NULL,
  new_status VARCHAR(50) DEFAULT NULL,
  old_quantity int DEFAULT NULL,
  new_quantity int DEFAULT NULL,
  description text,
  cost decimal(15,2) DEFAULT NULL,
  performed_by BIGINT DEFAULT NULL,
  performed_at timestamp NULL DEFAULT NOW()
);

--
-- Dumping data untuk tabel lab_inventory_history
--

INSERT INTO lab_inventory_history (id, inventory_id, action_type, old_condition, new_condition, old_status, new_status, old_quantity, new_quantity, description, cost, performed_by, performed_at) VALUES
(1, 1, 'add', NULL, 'good', NULL, 'active', NULL, NULL, 'Item inventaris baru ditambahkan: Smartboard', NULL, NULL, '2026-01-28 14:10:59'),
(2, 2, 'add', NULL, 'good', NULL, 'active', NULL, NULL, 'Item inventaris baru ditambahkan: mikrotik x86', NULL, NULL, '2026-02-01 14:49:08'),
(3, 3, 'add', NULL, 'good', NULL, 'active', NULL, NULL, 'Item inventaris baru ditambahkan: switch hub 24 port', NULL, NULL, '2026-02-02 02:30:37'),
(4, 4, 'add', NULL, 'good', NULL, 'active', NULL, 2, 'Item inventaris baru ditambahkan: switch hub 24 port (Jumlah: 2 unit)', NULL, NULL, '2026-02-02 02:56:06'),
(5, 5, 'add', NULL, 'good', NULL, 'active', NULL, 2, 'Item inventaris baru ditambahkan: ac 1,5 pk (Jumlah: 2 unit)', NULL, NULL, '2026-02-02 05:04:38'),
(6, 6, 'add', NULL, 'good', NULL, 'active', NULL, 1, 'Item inventaris baru ditambahkan: air conditioner (Jumlah: 1 unit)', NULL, NULL, '2026-02-02 05:08:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel lab_sessions
--

CREATE TABLE lab_sessions (
  id BIGSERIAL PRIMARY KEY,
  token varchar(12) NOT NULL,
  lab_key varchar(20) NOT NULL,
  resource_id BIGINT NOT NULL,
  source_type VARCHAR(50) NOT NULL,
  source_id BIGINT DEFAULT NULL,
  teacher_name varchar(255) NOT NULL,
  teacher_phone varchar(255) DEFAULT NULL,
  session_start TIMESTAMP NOT NULL,
  session_end TIMESTAMP NOT NULL,
  used_at TIMESTAMP DEFAULT NULL,
  invalidated_at TIMESTAMP DEFAULT NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  invalidated_reason varchar(255) DEFAULT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL
);

--
-- Dumping data untuk tabel lab_sessions
--

INSERT INTO lab_sessions (id, token, lab_key, resource_id, source_type, source_id, teacher_name, teacher_phone, session_start, session_end, used_at, invalidated_at, is_active, invalidated_reason, created_at, updated_at) VALUES
(1, 'FPJY-VUGH', 'lab7', 1, 'manual', NULL, 'Test Guru', '08123456789', '2026-02-25 22:57:38', '2026-02-26 00:57:38', '2026-02-25 22:58:43', '2026-02-26 01:00:01', FALSE, 'expired', '2026-02-25 22:57:38', '2026-02-26 01:00:01'),
(2, 'UNLF-ZKFJ', 'lab8', 2, 'booking', 8, 'bang ucup', NULL, '2026-02-26 08:45:00', '2026-02-26 09:20:00', NULL, '2026-02-26 09:20:01', FALSE, 'expired', '2026-02-26 00:51:37', '2026-02-26 09:20:01'),
(3, 'RFKE-50FY', 'lab8', 2, 'booking', 7, 'bang ucup', NULL, '2026-02-26 08:10:00', '2026-02-26 08:45:00', NULL, '2026-02-26 08:45:01', FALSE, 'expired', '2026-02-26 00:51:39', '2026-02-26 08:45:01'),
(4, 'XDTE-HGSA', 'lab7', 1, 'manual', NULL, 'Test Guru', '08123456789', '2026-02-26 03:31:30', '2026-02-26 05:31:30', '2026-02-26 03:32:06', '2026-02-26 05:35:01', FALSE, 'expired', '2026-02-26 03:31:30', '2026-02-26 05:35:01'),
(5, 'PFFJ-43WA', 'lab7', 1, 'manual', NULL, 'Test Guru', '08123456789', '2026-02-26 04:24:34', '2026-02-26 06:24:34', '2026-02-26 04:25:16', '2026-02-26 06:25:01', FALSE, 'expired', '2026-02-26 04:24:34', '2026-02-26 06:25:01'),
(6, 'LRL2-BUZZ', 'lab7', 1, 'manual', NULL, 'Test Guru', NULL, '2026-02-26 12:13:46', '2026-02-26 14:13:46', '2026-02-26 12:14:50', '2026-02-26 14:15:01', FALSE, 'expired', '2026-02-26 12:13:46', '2026-02-26 14:15:01'),
(7, 'ZQEL-A7AT', 'lab7', 1, 'schedule', 1, 'Bu Husnul', '6281217574441', '2026-02-26 11:35:00', '2026-02-26 12:10:00', NULL, '2026-02-26 13:50:02', FALSE, 'expired', '2026-02-26 13:48:07', '2026-02-26 13:50:02'),
(8, '5CDE-SUQA', 'lab7', 1, 'schedule', 1, 'Bu Husnul', '6281217574441', '2026-02-26 11:35:00', '2026-02-26 12:10:00', NULL, '2026-02-26 13:55:02', FALSE, 'expired', '2026-02-26 13:51:53', '2026-02-26 13:55:02'),
(9, 'DBER-K2E4', 'lab7', 1, 'booking', 9, 'Bang Ucup', NULL, '2026-02-27 08:45:00', '2026-02-27 09:20:00', NULL, '2026-02-27 09:20:01', FALSE, 'expired', '2026-02-27 01:54:35', '2026-02-27 09:20:01'),
(10, 'F4DY-WYUT', 'lab7', 1, 'booking', 10, 'Bang Ucup', NULL, '2026-02-27 09:20:00', '2026-02-27 09:55:00', NULL, '2026-02-27 09:55:01', FALSE, 'expired', '2026-02-27 01:57:12', '2026-02-27 09:55:01'),
(11, 'KR8M-9VUC', 'lab8', 2, 'booking', 11, 'Bang Ucup', '6281217574441', '2026-02-27 11:00:00', '2026-02-27 11:35:00', NULL, '2026-02-27 11:35:01', FALSE, 'expired', '2026-02-27 02:17:43', '2026-02-27 11:35:01'),
(12, 'XTQO-7CPT', 'lab1', 3, 'booking', 12, 'Alip', '6283112088830', '2026-02-27 11:00:00', '2026-02-27 11:35:00', NULL, '2026-02-27 11:35:01', FALSE, 'expired', '2026-02-27 06:26:27', '2026-02-27 11:35:01'),
(13, 'UQFP-BPDU', 'lab1', 3, 'manual', NULL, 'Test Guru', '6281217574441', '2026-02-27 06:40:38', '2026-02-27 07:40:38', '2026-02-27 06:40:58', '2026-02-27 07:45:01', FALSE, 'expired', '2026-02-27 06:40:38', '2026-02-27 07:45:01'),
(14, 'RG4S-WSBB', 'lab7', 1, 'manual', NULL, 'Test Guru', '6281217574441', '2026-02-27 06:50:38', '2026-02-27 07:50:38', '2026-02-27 06:50:50', '2026-02-27 07:55:02', FALSE, 'expired', '2026-02-27 06:50:38', '2026-02-27 07:55:02'),
(15, 'MFLP-GDSD', 'lab8', 2, 'schedule', 28, 'Bu Husnul', '6281217574441', '2026-02-27 07:00:00', '2026-02-27 07:35:00', NULL, '2026-02-27 07:35:02', FALSE, 'expired', '2026-02-27 06:53:01', '2026-02-27 07:35:02'),
(16, 'RZH7-ZBPX', 'lab1', 3, 'manual', NULL, 'Test Guru', '6281217574441', '2026-02-27 07:15:16', '2026-02-27 08:15:16', '2026-02-27 07:15:28', '2026-02-27 08:20:02', FALSE, 'expired', '2026-02-27 07:15:16', '2026-02-27 08:20:02'),
(17, 'NBDV-XNUX', 'lab7', 1, 'manual', NULL, 'Test Guru', '6281217574441', '2026-02-27 07:16:03', '2026-02-27 08:16:03', '2026-02-27 07:16:14', '2026-02-27 08:20:02', FALSE, 'expired', '2026-02-27 07:16:03', '2026-02-27 08:20:02'),
(18, 'BYIA-WZFG', 'lab7', 1, 'manual', NULL, 'Test Guru', '6281217574441', '2026-02-27 07:24:04', '2026-02-27 08:24:04', '2026-02-27 07:24:21', '2026-02-27 08:25:01', FALSE, 'expired', '2026-02-27 07:24:04', '2026-02-27 08:25:01'),
(19, 'K0QB-OKP7', 'lab8', 2, 'schedule', 29, 'Bu Husnul', '6281217574441', '2026-02-27 07:35:00', '2026-02-27 08:10:00', '2026-02-27 07:30:18', '2026-02-27 08:10:01', FALSE, 'expired', '2026-02-27 07:28:01', '2026-02-27 08:10:01'),
(20, 'MZAN-2ZBE', 'lab8', 2, 'schedule', 42, 'Pak Adhi', NULL, '2026-02-27 09:20:00', '2026-02-27 09:55:00', NULL, '2026-02-27 09:55:01', FALSE, 'expired', '2026-02-27 09:13:01', '2026-02-27 09:55:01'),
(21, 'XP43-JVY1', 'lab8', 2, 'schedule', 43, 'Pak Adhi', NULL, '2026-02-27 10:25:00', '2026-02-27 11:00:00', NULL, '2026-02-27 11:00:01', FALSE, 'expired', '2026-02-27 10:18:02', '2026-02-27 11:00:01'),
(22, 'RWTE-EHJD', 'lab7', 1, 'manual', NULL, 'Test Guru', '6281217574441', '2026-02-27 12:02:23', '2026-02-27 13:02:23', '2026-02-27 12:02:30', '2026-02-27 19:10:01', FALSE, 'expired', '2026-02-27 12:02:23', '2026-02-27 19:10:01'),
(23, 'Z2RM-UMSZ', 'lab7', 1, 'manual', NULL, 'Test Guru', '6281217574441', '2026-02-27 19:26:23', '2026-02-27 20:26:23', '2026-02-27 19:26:31', '2026-02-27 20:30:01', FALSE, 'expired', '2026-02-27 19:26:23', '2026-02-27 20:30:01'),
(24, 'DYIA-XK31', 'lab7', 1, 'schedule', 7, 'Tentor', NULL, '2026-02-28 07:00:00', '2026-02-28 07:35:00', NULL, '2026-02-28 07:35:01', FALSE, 'expired', '2026-02-28 06:53:01', '2026-02-28 07:35:01'),
(25, 'E66F-OGOA', 'lab8', 2, 'schedule', 24, 'Tentor', NULL, '2026-02-28 07:00:00', '2026-02-28 07:35:00', NULL, '2026-02-28 07:35:01', FALSE, 'expired', '2026-02-28 06:53:01', '2026-02-28 07:35:01'),
(26, 'GTNF-Q9IB', 'lab7', 1, 'schedule', 8, 'Tentor', NULL, '2026-02-28 07:35:00', '2026-02-28 08:10:00', NULL, '2026-02-28 08:10:01', FALSE, 'expired', '2026-02-28 07:28:01', '2026-02-28 08:10:01'),
(27, 'T5MT-2SMU', 'lab8', 2, 'schedule', 25, 'Bu Husnul', '6281217574441', '2026-02-28 07:35:00', '2026-02-28 08:10:00', '2026-02-28 07:57:28', '2026-02-28 08:10:01', FALSE, 'expired', '2026-02-28 07:28:01', '2026-02-28 08:10:01'),
(28, 'PIGY-9KMZ', 'lab7', 1, 'schedule', 9, 'Tentor', '6281217574441', '2026-02-28 08:10:00', '2026-02-28 08:45:00', NULL, '2026-02-28 08:45:02', FALSE, 'expired', '2026-02-28 08:03:02', '2026-02-28 08:45:02'),
(29, 'PCBU-W46Z', 'lab8', 2, 'schedule', 27, 'Tentor', '6281217574441', '2026-02-28 08:10:00', '2026-02-28 08:45:00', NULL, '2026-02-28 08:45:02', FALSE, 'expired', '2026-02-28 08:03:02', '2026-02-28 08:45:02'),
(30, 'TR0T-Y6TS', 'lab8', 2, 'schedule', 26, 'Tentor', '6281217574441', '2026-02-28 08:45:00', '2026-02-28 09:20:00', NULL, '2026-02-28 09:20:02', FALSE, 'expired', '2026-02-28 08:38:01', '2026-02-28 09:20:02'),
(31, 'CEGL-KFDH', 'lab8', 2, 'schedule', 30, 'Bu Husnul', '6281217574441', '2026-02-28 10:25:00', '2026-02-28 11:00:00', '2026-02-28 10:50:55', '2026-02-28 11:00:01', FALSE, 'expired', '2026-02-28 10:18:01', '2026-02-28 11:00:01'),
(32, 'JZYE-L4SY', 'lab8', 2, 'schedule', 31, 'Bu Husnul', '6281217574441', '2026-02-28 11:00:00', '2026-02-28 11:35:00', '2026-02-28 10:57:57', '2026-02-28 11:35:01', FALSE, 'expired', '2026-02-28 10:53:01', '2026-02-28 11:35:01'),
(33, 'BNRY-K9ML', 'lab8', 2, 'schedule', 32, 'Bu Husnul', '6281217574441', '2026-02-28 11:35:00', '2026-02-28 12:10:00', '2026-02-28 11:38:05', '2026-02-28 12:10:01', FALSE, 'expired', '2026-02-28 11:28:01', '2026-02-28 12:10:01'),
(34, 'OCHQ-E4CB', 'lab8', 2, 'schedule', 33, 'Bu Husnul', '6281217574441', '2026-02-28 12:10:00', '2026-02-28 12:45:00', NULL, '2026-02-28 12:45:01', FALSE, 'expired', '2026-02-28 12:03:01', '2026-02-28 12:45:01'),
(35, 'IDOV-8ZCO', 'lab2', 4, 'booking', 14, 'Bu Husnul', '6281217574441', '2026-03-07 07:00:00', '2026-03-07 12:45:00', NULL, NULL, TRUE, NULL, '2026-02-28 12:35:06', '2026-02-28 12:35:52'),
(36, 'EKKF-YS4E', 'lab2', 4, 'booking', 23, 'Bu Husnul', '6281217574441', '2026-03-06 07:00:00', '2026-03-06 12:45:00', NULL, NULL, TRUE, NULL, '2026-02-28 18:59:22', '2026-02-28 18:59:23'),
(37, '7QPU-JLEI', 'lab1', 3, 'booking', 32, 'Bu Husnul', '6281217574441', '2026-03-02 07:00:00', '2026-03-02 12:45:00', NULL, NULL, TRUE, NULL, '2026-02-28 19:20:59', '2026-02-28 19:20:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel maintenance_records
--

CREATE TABLE maintenance_records (
  id BIGSERIAL PRIMARY KEY,
  resource_id BIGINT NOT NULL,
  type varchar(50) DEFAULT NULL,
  title varchar(500) NOT NULL,
  description text,
  scheduled_date date NOT NULL,
  completed_date date DEFAULT NULL,
  technician varchar(255) DEFAULT NULL,
  technician_phone varchar(50) DEFAULT NULL,
  cost decimal(15,2) DEFAULT '0.00',
  status varchar(50) DEFAULT 'scheduled',
  notes text,
  created_by BIGINT DEFAULT NULL,
  metadata TEXT,
  created_at timestamp NULL DEFAULT NOW(),
  updated_at timestamp NULL DEFAULT NOW()
) ;

-- --------------------------------------------------------

--
-- Struktur dari tabel migrations
--

CREATE TABLE migrations (
  id INTEGER NOT NULL,
  migration varchar(255) NOT NULL,
  batch int NOT NULL
);

--
-- Dumping data untuk tabel migrations
--

INSERT INTO migrations (id, migration, batch) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2026_02_18_225406_create_permission_tables', 1),
(3, '2026_02_25_000001_add_target_phones_to_wa_settings', 2),
(4, '2026_02_25_062107_create_teachers_table', 3),
(5, '2026_02_25_062137_add_teacher_id_to_schedules_table', 3),
(6, '2026_02_25_062147_add_teacher_id_to_bookings_table', 3),
(7, '2026_02_25_062157_create_assignments_table', 3),
(8, '2026_02_25_062208_create_assignment_submissions_table', 3),
(9, '2026_02_25_160622_create_lab_sessions_table', 4),
(10, '2026_02_27_204126_add_attachment_to_assignments_table', 5),
(11, '2026_02_27_212100_add_organization_id_to_assignments_table', 6),
(12, '2026_05_01_075148_create_sunday_bookings_table', 7),
(13, '2026_05_16_000001_add_session_id_to_bookings_table', 8),
(14, '2026_05_16_000001_add_composite_indexes', 9),
(15, '2026_05_18_000001_add_pin_to_classes_table', 10),
(16, '2026_05_18_203616_add_remember_token_to_users_table', 11),
(17, '2026_05_23_222531_fix_teachers_phone_unique_and_booking_indexes', 12);

-- --------------------------------------------------------

--
-- Struktur dari tabel model_has_permissions
--

CREATE TABLE model_has_permissions (
  permission_id BIGINT NOT NULL,
  model_type varchar(255) NOT NULL,
  model_id BIGINT NOT NULL
);

-- --------------------------------------------------------

--
-- Struktur dari tabel model_has_roles
--

CREATE TABLE model_has_roles (
  role_id BIGINT NOT NULL,
  model_type varchar(255) NOT NULL,
  model_id BIGINT NOT NULL
);

--
-- Dumping data untuk tabel model_has_roles
--

INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel notifications
--

CREATE TABLE notifications (
  id BIGSERIAL PRIMARY KEY,
  user_id BIGINT DEFAULT NULL,
  type varchar(50) NOT NULL,
  title varchar(500) NOT NULL,
  message text,
  action_url varchar(500) DEFAULT NULL,
  reference_type varchar(50) DEFAULT NULL,
  reference_id BIGINT DEFAULT NULL,
  priority varchar(50) DEFAULT 'normal',
  is_read BOOLEAN DEFAULT FALSE,
  read_at timestamp NULL DEFAULT NULL,
  metadata TEXT,
  created_at timestamp NULL DEFAULT NOW()
) ;

-- --------------------------------------------------------

--
-- Struktur dari tabel organizations
--

CREATE TABLE organizations (
  id BIGSERIAL PRIMARY KEY,
  name varchar(255) NOT NULL,
  type varchar(50) DEFAULT NULL,
  parent_id BIGINT DEFAULT NULL,
  address text,
  phone varchar(50) DEFAULT NULL,
  email varchar(255) DEFAULT NULL,
  metadata TEXT,
  is_active BOOLEAN DEFAULT TRUE,
  created_at timestamp NULL DEFAULT NOW(),
  updated_at timestamp NULL DEFAULT NOW(),
  deleted_at timestamp NULL DEFAULT NULL
) ;

--
-- Dumping data untuk tabel organizations
--

INSERT INTO organizations (id, name, type, parent_id, address, phone, email, metadata, is_active, created_at, updated_at, deleted_at) VALUES
(1, 'SMA NURIS JEMBER', 'SMA', NULL, NULL, NULL, NULL, NULL, TRUE, '2026-01-13 02:19:51', '2026-01-13 02:19:51', NULL),
(2, 'MTS UNGGULAN NURIS', 'MTS', NULL, NULL, NULL, NULL, NULL, TRUE, '2026-01-13 02:19:51', '2026-01-13 02:19:51', NULL),
(3, 'MA UNGGULAN NURIS', 'MA', NULL, NULL, NULL, NULL, NULL, TRUE, '2026-01-13 02:19:51', '2026-01-13 02:19:51', NULL),
(4, 'SMP NURIS', 'SMP', NULL, NULL, NULL, NULL, NULL, TRUE, '2026-01-13 02:19:51', '2026-01-13 02:19:51', NULL),
(5, 'Ekstrakurikuler', 'EXCUL', NULL, NULL, NULL, NULL, NULL, TRUE, '2026-01-13 02:19:51', '2026-01-13 02:19:51', NULL),
(6, 'SMK NURIS JEMBER', 'SMK', NULL, NULL, NULL, NULL, NULL, TRUE, '2026-04-23 13:21:34', '2026-04-23 13:21:34', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel permissions
--

CREATE TABLE permissions (
  id BIGSERIAL PRIMARY KEY,
  name varchar(255) NOT NULL,
  guard_name varchar(255) NOT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL
);

--
-- Dumping data untuk tabel permissions
--

INSERT INTO permissions (id, name, guard_name, created_at, updated_at) VALUES
(1, 'booking.viewAny', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(2, 'booking.view', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(3, 'booking.create', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(4, 'booking.edit', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(5, 'booking.approve', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(6, 'booking.reject', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(7, 'booking.delete', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(8, 'schedule.viewAny', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(9, 'schedule.view', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(10, 'schedule.create', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(11, 'schedule.edit', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(12, 'schedule.delete', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(13, 'inventory.viewAny', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(14, 'inventory.view', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(15, 'inventory.create', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(16, 'inventory.edit', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(17, 'inventory.delete', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(18, 'maintenance.viewAny', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(19, 'maintenance.view', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(20, 'maintenance.create', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(21, 'maintenance.edit', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(22, 'maintenance.delete', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(23, 'procurement.viewAny', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(24, 'procurement.view', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(25, 'procurement.create', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(26, 'procurement.edit', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(27, 'procurement.approve', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(28, 'procurement.reject', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(29, 'procurement.delete', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(30, 'resource.viewAny', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(31, 'resource.view', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(32, 'resource.create', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(33, 'resource.edit', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(34, 'resource.delete', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(35, 'report.viewAny', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(36, 'report.view', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(37, 'report.export', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(38, 'user.viewAny', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(39, 'user.create', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(40, 'user.edit', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(41, 'user.delete', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(42, 'class.viewAny', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(43, 'class.create', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(44, 'class.edit', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(45, 'class.delete', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(46, 'system.settings', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(47, 'system.logs', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel procurement_requests
--

CREATE TABLE procurement_requests (
  id BIGSERIAL PRIMARY KEY,
  lab_id BIGINT NOT NULL,
  item_name varchar(255) NOT NULL,
  category VARCHAR(50) NOT NULL,
  quantity int NOT NULL,
  estimated_price decimal(15,2) NOT NULL,
  priority VARCHAR(50) NOT NULL DEFAULT 'medium',
  justification text NOT NULL,
  specifications text,
  preferred_brand varchar(100) DEFAULT NULL,
  notes text,
  status VARCHAR(50) NOT NULL DEFAULT 'pending',
  requested_by BIGINT NOT NULL,
  requested_at TIMESTAMP NOT NULL,
  reviewed_by BIGINT DEFAULT NULL,
  reviewed_at TIMESTAMP DEFAULT NULL,
  review_notes text,
  completed_at TIMESTAMP DEFAULT NULL,
  procurement_notes text,
  created_at timestamp NULL DEFAULT NOW(),
  updated_at timestamp NULL DEFAULT NOW()
);

--
-- Dumping data untuk tabel procurement_requests
--

INSERT INTO procurement_requests (id, lab_id, item_name, category, quantity, estimated_price, priority, justification, specifications, preferred_brand, notes, status, requested_by, requested_at, reviewed_by, reviewed_at, review_notes, completed_at, procurement_notes, created_at, updated_at) VALUES
(1, 1, 'Komputer PC Desktop', 'computer', 10, 8000000.00, 'high', 'Komputer yang ada sudah tidak mendukung software terbaru dan sering hang. Diperlukan upgrade untuk mendukung kegiatan pembelajaran yang lebih efektif.', 'Processor: Intel Core i5 Gen 11 atau AMD Ryzen 5\nRAM: 16GB DDR4\nStorage: 512GB NVMe SSD\nMonitor: 24 inch Full HD\nGraphics: Integrated\nOS: Windows 11 Pro', 'Dell Optiplex atau HP ProDesk', NULL, 'pending', 1, '2026-02-01 15:07:28', NULL, NULL, NULL, NULL, NULL, '2026-02-01 15:07:28', '2026-02-01 15:07:28'),
(2, 1, 'Mouse Wireless', 'peripheral', 20, 150000.00, 'low', 'Beberapa mouse sudah rusak dan perlu diganti. Mouse wireless akan lebih rapi dan mengurangi kabel yang berantakan.', 'Mouse wireless dengan baterai rechargeable\nDPI adjustable\nErgonomic design', 'Logitech', NULL, 'approved', 1, '2026-01-29 15:07:28', 1, '2026-01-30 15:07:28', 'Disetujui. Segera lakukan proses procurement.', NULL, NULL, '2026-01-29 15:07:28', '2026-02-01 15:07:28'),
(3, 2, 'Switch Network 24 Port', 'network', 2, 3500000.00, 'medium', 'Untuk meningkatkan koneksi jaringan di Lab Komputer 8. Switch yang ada sudah penuh dan butuh ekspansi.', 'Gigabit Ethernet\nManaged Switch\n24 Port RJ45\nRack mountable', 'TP-Link atau Cisco', NULL, 'pending', 1, '2026-01-31 15:07:28', NULL, NULL, NULL, NULL, NULL, '2026-01-31 15:07:28', '2026-01-31 15:07:28'),
(4, 1, 'Kursi Komputer Ergonomis', 'furniture', 15, 1200000.00, 'medium', 'Kursi yang ada sudah banyak yang rusak dan tidak ergonomis, menyebabkan siswa tidak nyaman saat praktikum.', 'Kursi ergonomis dengan sandaran punggung\nTinggi adjustable\nRoda berkualitas\nMaterial breathable', 'IKEA atau Informa', NULL, 'rejected', 2, '2026-01-27 15:07:28', 1, '2026-01-28 15:07:28', 'Ditolak karena anggaran tahun ini sudah habis. Bisa diajukan lagi tahun depan.', NULL, NULL, '2026-01-27 15:07:28', '2026-02-01 15:07:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel resources
--

CREATE TABLE resources (
  id BIGSERIAL PRIMARY KEY,
  name varchar(255) NOT NULL,
  type varchar(50) NOT NULL,
  parent_id BIGINT DEFAULT NULL,
  organization_id BIGINT DEFAULT NULL,
  building varchar(100) DEFAULT NULL,
  floor int DEFAULT NULL,
  room_number varchar(50) DEFAULT NULL,
  capacity int DEFAULT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'active',
  metadata TEXT,
  created_at timestamp NULL DEFAULT NOW(),
  updated_at timestamp NULL DEFAULT NOW(),
  deleted_at timestamp NULL DEFAULT NULL
) ;

--
-- Dumping data untuk tabel resources
--

INSERT INTO resources (id, name, type, parent_id, organization_id, building, floor, room_number, capacity, status, metadata, created_at, updated_at, deleted_at) VALUES
(1, 'Lab Komputer 7', 'lab', NULL, NULL, 'Gedung SMA', NULL, NULL, 30, 'active', '{\"floor\": 2, \"room_number\": \"201\", \"pic_role\": \"teknisi\", \"pic_username\": \"teknisi1\", \"pic_name\": \"Teknisi Lab\", \"pic_phone\": \"08129876543\"}', '2026-01-13 02:19:51', '2026-02-20 13:59:27', NULL),
(2, 'Lab Komputer 8', 'lab', NULL, NULL, 'Gedung SMA', NULL, NULL, 35, 'active', '{\"floor\": 2, \"room_number\": \"202\", \"pic_role\": \"teknisi\", \"pic_username\": \"teknisi1\", \"pic_name\": \"Teknisi Lab\", \"pic_phone\": \"08129876543\"}', '2026-01-13 02:19:51', '2026-02-20 13:59:27', NULL),
(3, 'Lab Komputer 1', 'lab', NULL, NULL, 'gedung lab', NULL, '', 25, 'active', NULL, '2026-02-05 04:59:15', '2026-02-05 04:59:15', NULL),
(4, 'Lab Komputer 2', 'lab', NULL, NULL, 'gedung lab', NULL, '', 25, 'active', NULL, '2026-02-05 05:00:42', '2026-02-05 05:00:42', NULL),
(5, 'Lab Komputer SMP', 'lab', NULL, NULL, 'Nuris 3', 2, '', 14, 'active', '{\"floor\":2}', '2026-02-05 05:04:29', '2026-02-05 05:04:29', NULL),
(6, 'Lab Komputer 3', 'lab', NULL, NULL, 'gedung lab SMK', NULL, '', 30, 'active', NULL, '2026-02-06 03:23:27', '2026-02-06 03:23:27', NULL),
(7, 'Lab Komputer 4', 'lab', NULL, NULL, 'gedung lab SMK', NULL, '', 30, 'active', NULL, '2026-02-06 03:25:17', '2026-02-06 03:25:17', NULL),
(8, 'Lab Fiber Optic', 'lab', NULL, NULL, 'gedung lab SMK', NULL, '', NULL, 'nonactive', NULL, '2026-02-06 03:26:32', '2026-02-28 05:43:28', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel roles
--

CREATE TABLE roles (
  id BIGSERIAL PRIMARY KEY,
  name varchar(255) NOT NULL,
  guard_name varchar(255) NOT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL
);

--
-- Dumping data untuk tabel roles
--

INSERT INTO roles (id, name, guard_name, created_at, updated_at) VALUES
(1, 'admin', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02'),
(2, 'teknisi', 'web', '2026-02-20 13:59:27', '2026-02-20 14:08:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel role_has_permissions
--

CREATE TABLE role_has_permissions (
  permission_id BIGINT NOT NULL,
  role_id BIGINT NOT NULL
);

--
-- Dumping data untuk tabel role_has_permissions
--

INSERT INTO role_has_permissions (permission_id, role_id) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 2),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(25, 2),
(26, 2),
(27, 2),
(28, 2),
(29, 2),
(30, 2),
(31, 2),
(32, 2),
(33, 2),
(34, 2),
(35, 2),
(36, 2),
(37, 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel schedules
--

CREATE TABLE schedules (
  id BIGSERIAL PRIMARY KEY,
  teacher_id BIGINT DEFAULT NULL,
  resource_id BIGINT NOT NULL,
  time_slot_id BIGINT NOT NULL,
  class_id BIGINT NOT NULL,
  user_id BIGINT DEFAULT NULL,
  day_of_week varchar(20) NOT NULL,
  teacher_name varchar(255) NOT NULL,
  subject_name varchar(255) DEFAULT NULL,
  notes text,
  academic_year varchar(20) DEFAULT NULL,
  semester varchar(10) DEFAULT NULL,
  start_date date DEFAULT NULL,
  end_date date DEFAULT NULL,
  status varchar(50) DEFAULT 'active',
  metadata TEXT,
  created_at timestamp NULL DEFAULT NOW(),
  updated_at timestamp NULL DEFAULT NOW(),
  deleted_at timestamp NULL DEFAULT NULL
) ;

--
-- Dumping data untuk tabel schedules
--

INSERT INTO schedules (id, teacher_id, resource_id, time_slot_id, class_id, user_id, day_of_week, teacher_name, subject_name, notes, academic_year, semester, start_date, end_date, status, metadata, created_at, updated_at, deleted_at) VALUES
(1, 1, 1, 9, 2, NULL, 'Monday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-13 04:32:11', '2026-02-25 06:37:00', NULL),
(2, 1, 1, 10, 2, NULL, 'Monday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 13:56:47', '2026-02-25 06:37:00', NULL),
(3, 1, 1, 1, 14, NULL, 'Wednesday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:01:09', '2026-04-29 12:09:48', '2026-04-29 12:09:48'),
(4, 1, 1, 2, 14, NULL, 'Wednesday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:01:36', '2026-02-25 06:37:00', NULL),
(5, 1, 1, 1, 5, NULL, 'Thursday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:02:06', '2026-02-25 06:37:00', NULL),
(6, 1, 1, 2, 5, NULL, 'Thursday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:02:26', '2026-02-25 06:37:00', NULL),
(7, 2, 1, 1, 24, NULL, 'Saturday', 'Tentor', 'KIR', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:11:14', '2026-02-25 06:37:00', NULL),
(8, 2, 1, 2, 24, NULL, 'Saturday', 'Tentor', 'KIR', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:12:51', '2026-02-25 06:37:00', NULL),
(9, 2, 1, 3, 24, NULL, 'Saturday', 'Tentor', 'KIR', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:13:35', '2026-02-25 06:37:00', NULL),
(10, 1, 2, 1, 17, NULL, 'Monday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:17:11', '2026-02-25 06:37:00', NULL),
(11, 1, 2, 2, 17, NULL, 'Monday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:17:32', '2026-02-25 06:37:00', NULL),
(12, 1, 2, 4, 16, NULL, 'Monday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:19:01', '2026-02-25 06:37:00', NULL),
(13, 1, 2, 5, 16, NULL, 'Monday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:19:19', '2026-02-25 06:37:00', NULL),
(14, 1, 2, 7, 4, NULL, 'Monday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:20:15', '2026-02-25 06:37:00', NULL),
(15, 1, 2, 8, 4, NULL, 'Monday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:20:47', '2026-02-25 06:37:00', NULL),
(16, 1, 2, 1, 7, NULL, 'Tuesday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:22:03', '2026-02-25 06:37:00', NULL),
(17, 1, 2, 2, 7, NULL, 'Tuesday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:22:27', '2026-02-25 06:37:00', NULL),
(18, 1, 2, 7, 8, NULL, 'Tuesday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:23:25', '2026-02-25 06:37:00', NULL),
(19, 1, 2, 8, 8, NULL, 'Tuesday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:23:44', '2026-02-25 06:37:00', NULL),
(20, 1, 2, 9, 11, NULL, 'Tuesday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:24:19', '2026-02-25 06:37:00', NULL),
(21, 1, 2, 10, 11, NULL, 'Tuesday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:25:06', '2026-02-25 06:37:00', NULL),
(22, 1, 2, 9, 3, NULL, 'Thursday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:28:21', '2026-02-25 06:37:00', NULL),
(23, 1, 2, 10, 3, NULL, 'Thursday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:28:50', '2026-02-25 06:37:00', NULL),
(24, 2, 2, 1, 24, NULL, 'Saturday', 'Tentor', 'KIR', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:32:19', '2026-02-25 06:37:00', NULL),
(25, 1, 2, 2, 24, NULL, 'Saturday', 'Bu Husnul', 'KIR', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:32:42', '2026-02-25 06:37:00', NULL),
(26, 2, 2, 4, 24, NULL, 'Saturday', 'Tentor', 'KIR', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:34:32', '2026-02-25 06:37:00', NULL),
(27, 2, 2, 3, 24, NULL, 'Saturday', 'Tentor', 'KIR', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:34:53', '2026-02-25 06:37:00', NULL),
(28, 1, 2, 1, 6, NULL, 'Friday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:35:38', '2026-02-25 06:37:00', NULL),
(29, 1, 2, 2, 6, NULL, 'Friday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:35:57', '2026-02-25 06:37:00', NULL),
(30, 1, 2, 7, 13, NULL, 'Saturday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:37:15', '2026-02-25 06:37:00', NULL),
(31, 1, 2, 8, 13, NULL, 'Saturday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:37:37', '2026-02-25 06:37:00', NULL),
(32, 1, 2, 9, 18, NULL, 'Saturday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:37:52', '2026-02-25 06:37:00', NULL),
(33, 1, 2, 10, 18, NULL, 'Saturday', 'Bu Husnul', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-15 14:38:18', '2026-02-25 06:37:00', NULL),
(34, 3, 2, 9, 23, NULL, 'Monday', 'Pak Adhi', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-21 13:21:41', '2026-04-29 12:39:27', '2026-04-29 12:39:27'),
(36, 3, 2, 7, 22, NULL, 'Wednesday', 'Pak Adhi', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-21 13:23:10', '2026-02-25 06:37:00', NULL),
(37, 3, 2, 8, 22, NULL, 'Wednesday', 'Pak Adhi', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-21 13:24:03', '2026-02-25 06:37:00', NULL),
(38, 3, 2, 9, 19, NULL, 'Wednesday', 'Pak Adhi', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-21 13:24:25', '2026-02-25 06:37:00', NULL),
(39, 3, 2, 10, 19, NULL, 'Wednesday', 'Pak Adhi', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-21 13:24:49', '2026-02-25 06:37:00', NULL),
(40, 3, 2, 1, 21, NULL, 'Thursday', 'Pak Adhi', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-21 13:25:28', '2026-02-25 06:37:00', NULL),
(41, 3, 2, 2, 21, NULL, 'Thursday', 'Pak Adhi', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-21 13:26:04', '2026-02-25 06:37:00', NULL),
(42, 3, 2, 5, 20, NULL, 'Friday', 'Pak Adhi', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-21 13:26:34', '2026-02-25 06:37:00', NULL),
(43, 3, 2, 7, 20, NULL, 'Friday', 'Pak Adhi', 'TIK', '', NULL, NULL, NULL, NULL, 'active', NULL, '2026-01-21 13:27:28', '2026-02-25 06:37:00', NULL),
(44, NULL, 2, 10, 1, 1, 'Monday', 'tes', 'TIK', NULL, NULL, NULL, NULL, NULL, 'active', NULL, '2026-04-29 11:39:34', '2026-04-29 12:37:50', '2026-04-29 12:37:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel sunday_bookings
--

CREATE TABLE sunday_bookings (
  id BIGSERIAL PRIMARY KEY,
  teacher_id BIGINT DEFAULT NULL,
  resource_id BIGINT NOT NULL,
  organization_id BIGINT NOT NULL,
  approved_by BIGINT DEFAULT NULL,
  booking_date date NOT NULL,
  teacher_name varchar(255) NOT NULL,
  teacher_phone varchar(50) NOT NULL,
  class_name varchar(255) DEFAULT NULL,
  subject_name varchar(255) DEFAULT NULL,
  title varchar(500) NOT NULL,
  description text,
  participant_count int NOT NULL DEFAULT '0',
  status varchar(50) NOT NULL DEFAULT 'pending',
  approved_at timestamp NULL DEFAULT NULL,
  notes text,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL
);

--
-- Dumping data untuk tabel sunday_bookings
--

INSERT INTO sunday_bookings (id, teacher_id, resource_id, organization_id, approved_by, booking_date, teacher_name, teacher_phone, class_name, subject_name, title, description, participant_count, status, approved_at, notes, created_at, updated_at) VALUES
(1, 1, 2, 2, NULL, '2026-05-03', 'Bang Ucup', '6281217574441', 'Kelas 9 A', 'TIK', 'adas', NULL, 2, 'rejected', NULL, 'asfsaf', '2026-05-01 01:27:42', '2026-05-02 12:07:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel teachers
--

CREATE TABLE teachers (
  id BIGSERIAL PRIMARY KEY,
  name varchar(255) NOT NULL,
  phone varchar(255) DEFAULT NULL,
  token varchar(10) NOT NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL
);

--
-- Dumping data untuk tabel teachers
--

INSERT INTO teachers (id, name, phone, token, is_active, created_at, updated_at) VALUES
(1, 'Bu Husnul', '6281217574441', 'GRU-001', TRUE, '2026-02-25 06:37:00', '2026-02-26 13:25:34'),
(2, 'Tentor', '628883147162', 'GRU-002', TRUE, '2026-02-25 06:37:00', '2026-05-12 03:07:16'),
(3, 'Pak Adhi', '6281217574123', 'GRU-003', TRUE, '2026-02-25 06:37:00', '2026-02-28 00:58:58'),
(9, 'Alip', '6283112088830', 'GRU-005', TRUE, '2026-02-27 03:43:48', '2026-02-26 20:43:48'),
(10, 'Husnul', '6285832553627', 'GRU-006', TRUE, '2026-04-27 01:16:25', '2026-04-27 01:16:25'),
(11, 'Bu Annisa', '6282302454519', 'GRU-007', TRUE, '2026-05-05 01:09:26', '2026-05-05 01:09:26'),
(12, 'Nabila Fatmawati', '6281235706327', 'GRU-008', TRUE, '2026-05-05 03:43:53', '2026-05-05 03:43:53'),
(15, 'Fiki Dwi Sembilan', '6281357118518', 'GRU-011', TRUE, '2026-05-07 02:15:21', '2026-05-07 02:15:21'),
(16, 'Laili Bk', '6285230779470', 'GRU-012', TRUE, '2026-05-07 09:22:59', '2026-05-07 09:22:59'),
(17, 'Kartika', '6289526636020', 'GRU-013', TRUE, '2026-05-09 00:51:44', '2026-05-09 00:51:44'),
(19, 'Fitria Saqinah Damayanti', '6281615400260', 'GRU-015', TRUE, '2026-05-12 01:15:21', '2026-05-12 01:15:21'),
(20, 'Atik Robbana', '6289606805479', 'GRU-016', TRUE, '2026-05-15 00:48:08', '2026-05-15 00:48:08'),
(21, 'Nurma', '6282329441631', 'GRU-017', TRUE, '2026-05-18 01:31:47', '2026-05-18 01:31:47'),
(23, 'Devita Wulansari', '6284782576484', 'GRU-019', TRUE, '2026-05-18 01:37:54', '2026-05-18 01:37:54'),
(24, 'Dimas Abdur Rozaq', '6282228299594', 'GRU-020', TRUE, '2026-05-18 01:54:28', '2026-05-18 01:54:28'),
(25, 'Iin', '6285259384695', 'GRU-021', TRUE, '2026-05-19 01:57:03', '2026-05-19 01:57:03'),
(27, 'Rina Yuastri', '6289607148707', 'GRU-023', TRUE, '2026-05-20 04:01:27', '2026-05-20 04:01:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel time_slots
--

CREATE TABLE time_slots (
  id BIGSERIAL PRIMARY KEY,
  name varchar(255) NOT NULL,
  start_time time NOT NULL,
  end_time time NOT NULL,
  day_of_week SMALLINT DEFAULT NULL,
  slot_order int DEFAULT NULL,
  is_break BOOLEAN DEFAULT FALSE,
  metadata TEXT,
  is_active BOOLEAN DEFAULT TRUE,
  created_at timestamp NULL DEFAULT NOW(),
  updated_at timestamp NULL DEFAULT NOW()
) ;

--
-- Dumping data untuk tabel time_slots
--

INSERT INTO time_slots (id, name, start_time, end_time, day_of_week, slot_order, is_break, metadata, is_active, created_at, updated_at) VALUES
(1, 'Slot 1', '07:00:00', '07:35:00', NULL, 1, FALSE, NULL, TRUE, '2026-01-13 02:19:51', '2026-01-13 02:19:51'),
(2, 'Slot 2', '07:35:00', '08:10:00', NULL, 2, FALSE, NULL, TRUE, '2026-01-13 02:19:51', '2026-01-13 02:19:51'),
(3, 'Slot 3', '08:10:00', '08:45:00', NULL, 3, FALSE, NULL, TRUE, '2026-01-13 02:19:51', '2026-01-13 02:19:51'),
(4, 'Slot 4', '08:45:00', '09:20:00', NULL, 4, FALSE, NULL, TRUE, '2026-01-13 02:19:51', '2026-01-13 02:19:51'),
(5, 'Slot 5', '09:20:00', '09:55:00', NULL, 5, FALSE, NULL, TRUE, '2026-01-13 02:19:51', '2026-01-13 02:19:51'),
(6, 'Istirahat', '09:55:00', '10:25:00', NULL, 6, TRUE, NULL, TRUE, '2026-01-13 02:19:51', '2026-01-13 02:19:51'),
(7, 'Slot 6', '10:25:00', '11:00:00', NULL, 7, FALSE, NULL, TRUE, '2026-01-13 02:19:51', '2026-01-13 02:19:51'),
(8, 'Slot 7', '11:00:00', '11:35:00', NULL, 8, FALSE, NULL, TRUE, '2026-01-13 02:19:51', '2026-01-13 02:19:51'),
(9, 'Slot 8', '11:35:00', '12:10:00', NULL, 9, FALSE, NULL, TRUE, '2026-01-13 02:19:51', '2026-01-13 02:19:51'),
(10, 'Slot 9', '12:10:00', '12:45:00', NULL, 10, FALSE, NULL, TRUE, '2026-01-13 02:19:51', '2026-01-13 02:19:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel users
--

CREATE TABLE users (
  id BIGSERIAL PRIMARY KEY,
  username varchar(100) NOT NULL,
  email varchar(255) NOT NULL,
  password_hash varchar(255) NOT NULL,
  full_name varchar(255) NOT NULL,
  phone varchar(50) DEFAULT NULL,
  role varchar(50) DEFAULT 'user',
  organization_id BIGINT DEFAULT NULL,
  metadata TEXT,
  is_active BOOLEAN DEFAULT TRUE,
  remember_token varchar(100) DEFAULT NULL,
  email_verified_at timestamp NULL DEFAULT NULL,
  last_login_at timestamp NULL DEFAULT NULL,
  last_login_ip varchar(45) DEFAULT NULL,
  failed_login_attempts int DEFAULT '0',
  locked_until timestamp NULL DEFAULT NULL,
  created_at timestamp NULL DEFAULT NOW(),
  updated_at timestamp NULL DEFAULT NOW(),
  deleted_at timestamp NULL DEFAULT NULL
) ;

--
-- Dumping data untuk tabel users
--

INSERT INTO users (id, username, email, password_hash, full_name, phone, role, organization_id, metadata, is_active, remember_token, email_verified_at, last_login_at, last_login_ip, failed_login_attempts, locked_until, created_at, updated_at, deleted_at) VALUES
(1, 'admin', 'admin@labsystem.com', '$2y$12$/PF5hhL/rbnYK.okF9A1wO0aGnv/rmRolOlDnvwX2AZEEPqIpdH/y', 'Administrator', '08123456789', 'admin', NULL, NULL, TRUE, NULL, NULL, NULL, NULL, 0, NULL, '2026-01-13 02:19:51', '2026-02-21 03:54:05', NULL),
(2, 'operator1', 'operator1@labsystem.com', '$2y$12$l7Oz/OC0e0Z29XmRyV8BM.g4I42EusOS7JoBQ1I2kYFI64AegOubK', 'Operator Lab', '08123456790', 'operator', NULL, NULL, TRUE, NULL, NULL, NULL, NULL, 0, NULL, '2026-01-13 02:19:51', '2026-02-21 03:54:06', NULL),
(3, 'teknisi1', 'teknisi1@labsystem.com', '$2y$12$ICiywQpi8K/aiJqMBkqesOzE.FYS0JYXy8fPqmd8nVYz/aH1bi3pG', 'Teknisi Lab', '08129876543', 'teknisi', NULL, '{\"allowed_resources\":[3,4]}', TRUE, NULL, NULL, NULL, NULL, 0, NULL, '2026-02-20 13:59:27', '2026-02-21 11:23:11', NULL),
(5, 'teknisi2', 'teknisi2@labsystem.com', '$2y$12$ICiywQpi8K/aiJqMBkqesOzE.FYS0JYXy8fPqmd8nVYz/aH1bi3pG', 'Teknisi Lab 2', '08129876544', 'teknisi', NULL, '{\"allowed_resources\":[1,2]}', TRUE, NULL, NULL, NULL, NULL, 0, NULL, '2026-02-21 12:33:46', '2026-02-21 12:33:46', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel user_sessions
--

CREATE TABLE user_sessions (
  id BIGSERIAL PRIMARY KEY,
  user_id BIGINT NOT NULL,
  token varchar(255) NOT NULL,
  ip_address varchar(45) DEFAULT NULL,
  user_agent text,
  metadata TEXT,
  expires_at timestamp NOT NULL DEFAULT NOW(),
  created_at timestamp NULL DEFAULT NOW()
) ;

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel activity_logs
--

--
-- Indeks untuk tabel assignments
--

--
-- Indeks untuk tabel assignment_submissions
--

--
-- Indeks untuk tabel bookings
--

--
-- Indeks untuk tabel classes
--

--
-- Indeks untuk tabel holidays
--

--
-- Indeks untuk tabel lab_inventory
--

--
-- Indeks untuk tabel lab_inventory_history
--

--
-- Indeks untuk tabel lab_sessions
--

--
-- Indeks untuk tabel maintenance_records
--

--
-- Indeks untuk tabel migrations
--

--
-- Indeks untuk tabel model_has_permissions
--

--
-- Indeks untuk tabel model_has_roles
--

--
-- Indeks untuk tabel notifications
--

--
-- Indeks untuk tabel organizations
--

--
-- Indeks untuk tabel permissions
--

--
-- Indeks untuk tabel procurement_requests
--

--
-- Indeks untuk tabel resources
--

--
-- Indeks untuk tabel roles
--

--
-- Indeks untuk tabel role_has_permissions
--

--
-- Indeks untuk tabel schedules
--

--
-- Indeks untuk tabel sunday_bookings
--

--
-- Indeks untuk tabel teachers
--

--
-- Indeks untuk tabel time_slots
--

--
-- Indeks untuk tabel users
--

--
-- Indeks untuk tabel user_sessions
--

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel activity_logs
--
ALTER TABLE activity_logs
  ADD CONSTRAINT fk_activity_logs_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel assignments
--
ALTER TABLE assignments
  ADD CONSTRAINT assignments_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES teachers (id) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel assignment_submissions
--
ALTER TABLE assignment_submissions
  ADD CONSTRAINT assignment_submissions_assignment_id_foreign FOREIGN KEY (assignment_id) REFERENCES assignments (id) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel bookings
--
ALTER TABLE bookings
  ADD CONSTRAINT bookings_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES teachers (id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_bookings_approved_by FOREIGN KEY (approved_by) REFERENCES users (id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_bookings_organization FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE,
  ADD CONSTRAINT fk_bookings_resource FOREIGN KEY (resource_id) REFERENCES resources (id) ON DELETE CASCADE,
  ADD CONSTRAINT fk_bookings_time_slot FOREIGN KEY (time_slot_id) REFERENCES time_slots (id),
  ADD CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel classes
--
ALTER TABLE classes
  ADD CONSTRAINT fk_classes_organization FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel lab_inventory
--
ALTER TABLE lab_inventory
  ADD CONSTRAINT fk_lab_inventory_created_by FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_lab_inventory_resource FOREIGN KEY (resource_id) REFERENCES resources (id) ON DELETE CASCADE,
  ADD CONSTRAINT fk_lab_inventory_updated_by FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel lab_inventory_history
--
ALTER TABLE lab_inventory_history
  ADD CONSTRAINT fk_inventory_history_inventory FOREIGN KEY (inventory_id) REFERENCES lab_inventory (id) ON DELETE CASCADE,
  ADD CONSTRAINT fk_inventory_history_user FOREIGN KEY (performed_by) REFERENCES users (id) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel lab_sessions
--
ALTER TABLE lab_sessions
  ADD CONSTRAINT lab_sessions_resource_id_foreign FOREIGN KEY (resource_id) REFERENCES resources (id) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel maintenance_records
--
ALTER TABLE maintenance_records
  ADD CONSTRAINT fk_maintenance_created_by FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_maintenance_resource FOREIGN KEY (resource_id) REFERENCES resources (id) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel model_has_permissions
--
ALTER TABLE model_has_permissions
  ADD CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel model_has_roles
--
ALTER TABLE model_has_roles
  ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel notifications
--
ALTER TABLE notifications
  ADD CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel organizations
--
ALTER TABLE organizations
  ADD CONSTRAINT fk_organizations_parent FOREIGN KEY (parent_id) REFERENCES organizations (id) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel resources
--
ALTER TABLE resources
  ADD CONSTRAINT fk_resources_organization FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_resources_parent FOREIGN KEY (parent_id) REFERENCES resources (id) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel role_has_permissions
--
ALTER TABLE role_has_permissions
  ADD CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE,
  ADD CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel schedules
--
ALTER TABLE schedules
  ADD CONSTRAINT fk_schedules_class FOREIGN KEY (class_id) REFERENCES classes (id) ON DELETE CASCADE,
  ADD CONSTRAINT fk_schedules_resource FOREIGN KEY (resource_id) REFERENCES resources (id) ON DELETE CASCADE,
  ADD CONSTRAINT fk_schedules_time_slot FOREIGN KEY (time_slot_id) REFERENCES time_slots (id),
  ADD CONSTRAINT fk_schedules_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
  ADD CONSTRAINT schedules_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES teachers (id) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel sunday_bookings
--
ALTER TABLE sunday_bookings
  ADD CONSTRAINT sunday_bookings_approved_by_foreign FOREIGN KEY (approved_by) REFERENCES users (id) ON DELETE SET NULL,
  ADD CONSTRAINT sunday_bookings_organization_id_foreign FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE,
  ADD CONSTRAINT sunday_bookings_resource_id_foreign FOREIGN KEY (resource_id) REFERENCES resources (id) ON DELETE CASCADE,
  ADD CONSTRAINT sunday_bookings_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES teachers (id) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel users
--
ALTER TABLE users
  ADD CONSTRAINT fk_users_organization FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel user_sessions
--
ALTER TABLE user_sessions
  ADD CONSTRAINT fk_user_sessions_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;

-- Reset sequences to match current max IDs
SELECT setval('activity_logs_id_seq', (SELECT MAX(id) FROM activity_logs));
SELECT setval('assignments_id_seq', (SELECT MAX(id) FROM assignments));
SELECT setval('assignment_submissions_id_seq', (SELECT MAX(id) FROM assignment_submissions));
SELECT setval('bookings_id_seq', (SELECT MAX(id) FROM bookings));
SELECT setval('classes_id_seq', (SELECT MAX(id) FROM classes));
SELECT setval('lab_inventory_id_seq', (SELECT MAX(id) FROM lab_inventory));
SELECT setval('lab_inventory_history_id_seq', (SELECT MAX(id) FROM lab_inventory_history));
SELECT setval('lab_sessions_id_seq', (SELECT MAX(id) FROM lab_sessions));
SELECT setval('maintenance_records_id_seq', (SELECT MAX(id) FROM maintenance_records));
SELECT setval('notifications_id_seq', (SELECT MAX(id) FROM notifications));
SELECT setval('organizations_id_seq', (SELECT MAX(id) FROM organizations));
SELECT setval('permissions_id_seq', (SELECT MAX(id) FROM permissions));
SELECT setval('procurement_requests_id_seq', (SELECT MAX(id) FROM procurement_requests));
SELECT setval('resources_id_seq', (SELECT MAX(id) FROM resources));
SELECT setval('roles_id_seq', (SELECT MAX(id) FROM roles));
SELECT setval('schedules_id_seq', (SELECT MAX(id) FROM schedules));
SELECT setval('sunday_bookings_id_seq', (SELECT MAX(id) FROM sunday_bookings));
SELECT setval('teachers_id_seq', (SELECT MAX(id) FROM teachers));
SELECT setval('time_slots_id_seq', (SELECT MAX(id) FROM time_slots));
SELECT setval('users_id_seq', (SELECT MAX(id) FROM users));
SELECT setval('user_sessions_id_seq', (SELECT MAX(id) FROM user_sessions));

COMMIT;