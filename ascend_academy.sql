-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 13, 2025 at 07:11 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ascend_academy`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `schedule_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_present` tinyint(1) NOT NULL DEFAULT 0,
  `teacher_present` tinyint(1) NOT NULL DEFAULT 0,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`id`, `schedule_id`, `student_id`, `teacher_id`, `student_present`, `teacher_present`, `remark`, `created_at`, `updated_at`) VALUES
(6, 322, 4, 7, 1, 1, NULL, '2025-12-12 09:15:45', '2025-12-12 09:15:45');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `children`
--

CREATE TABLE `children` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED NOT NULL,
  `child_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `children`
--

INSERT INTO `children` (`id`, `parent_id`, `child_id`, `created_at`, `updated_at`) VALUES
(1, 2, 4, '2025-11-23 17:35:15', '2025-11-23 17:35:15'),
(2, 9, 10, '2025-12-05 09:07:20', '2025-12-05 09:07:20'),
(3, 12, 13, '2025-12-12 09:02:25', '2025-12-12 09:02:25');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `level` enum('Beginner','Intermediate','Advanced') DEFAULT NULL,
  `age_group` enum('Kids','Teens','Adults') DEFAULT NULL,
  `language` enum('English','Arabic') DEFAULT NULL,
  `is_free` tinyint(1) NOT NULL DEFAULT 0,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `level`, `age_group`, `language`, `is_free`, `photo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Eiusmod et incididun', 'Ut qui labore velit', 'Beginner', 'Adults', 'English', 0, 'courses/nTWSmWOyvHuAHxYLMGgwdeZ8zRWvBEJ5I7MXTxRl.png', '2025-11-25 18:36:27', '2025-12-11 15:54:26', '2025-12-11 15:54:26'),
(2, 'Iure amet laboris e', 'Adipisci deleniti op', 'Beginner', 'Adults', 'English', 0, NULL, '2025-11-25 18:36:44', '2025-12-11 15:54:20', '2025-12-11 15:54:20'),
(3, 'Amet magnam atque e', 'Quis at labore illum', 'Beginner', 'Adults', 'English', 0, 'courses/dFWCC6q9HdJuycMdbNzjecHSib2XSuPa5cN4XTth.png', '2025-11-25 19:09:27', '2025-12-11 15:52:10', '2025-12-11 15:52:10'),
(4, 'Ullamco rem dolor ve', 'Sit ipsum soluta qui', NULL, NULL, NULL, 0, NULL, '2025-12-05 14:01:05', '2025-12-11 15:52:02', '2025-12-11 15:52:02'),
(5, 'Qur’an Memorization', 'In this course, students focus on memorizing the Holy Book.\r\n\r\nThe timeframe is based on the abilities of each student and how many chapters they target.\r\n\r\nIt should go without mentioning that Qur’an memorization cannot stand without enhancing basic Arabic language skills in terms of reading. In this course, students will learn to perfect reading in Arabic the Holy Quran using Qaidah Al Nourania (Nour Al-bayan Textbook). The course is provided by', NULL, NULL, NULL, 0, 'courses/xaR3JMntOmiI8EBkPP0lp1Ss9PGyP9vR265XOSrP.jpg', '2025-12-11 15:56:45', '2025-12-11 15:56:45', NULL),
(6, 'Tajweed Rules', 'This course targets those who aspire to read and recite the Holy Quran as it was revealed to the Prophet Muhammad (PBUH); while staying true to the theoretical rules of recitation. By the end of this course, students will have the ability to recite Quran properly, distinguishing between all sound articulations and characteristics.\r\n\r\nFor the purpose of this course, students will study from some textbooks including, but not limited to, Tajweed Rules of the Qur\'an and Sharh Al-Jazariyyah.\r\n\r\nIn this course, students will go through the Quran taking into consideration the proper rules of Tajweed and correcting any recitation mistakes.\r\n\r\nDuring this time period, students will learn reciting using technical and theoretical rules.', NULL, NULL, NULL, 0, 'courses/dR1zlTlfGZC9FJW9WuhSB9xIoDzfEOqboekwnqam.jpg', '2025-12-11 17:02:57', '2025-12-11 17:05:15', NULL),
(7, 'Arabic language', 'This course is curated for those who need to easily comprehend Quranic language and master the reading comprehension skills of classical Arabic. For the purpose of this course, students will study from some textbooks including, but not limited to, Qaidah Al Nourania (Nour Al-bayan Textbook) and Arabic Between Your Hands (Book Series).', NULL, NULL, NULL, 0, 'courses/1WG91cfWOQ3Cvi5ZCnHP88z9hJO3QuwSob2q7r6k.jpg', '2025-12-11 17:06:33', '2025-12-11 17:06:33', NULL),
(8, 'Ijazah', 'This advanced course targets those who are eager to get a full Ijazah from Ascend Quran Academy\r\nThis advanced course targets those who are eager to get a full Ijazah from Ascend Quran Academy following a comprehensive proper recitation and memorization of the Holy Book.', NULL, NULL, NULL, 0, 'courses/aSfpPPz9V62QED8g5SHdwSqcBhqvRMTPO3Udh01n.jpg', '2025-12-11 17:10:44', '2025-12-11 17:10:44', NULL),
(9, 'Islamic Studies', 'This course introduces the student to the Islamic principles as well as the provisions and acts of worship and transactions. It also guides the student to the use of supplications uttered by the Prophet (PBUH) and the righteous early Muslims.\r\n\r\nThe textbook used for these purposes is Islamic Studies (Book Series).', NULL, NULL, NULL, 0, 'courses/PuQWl48VPfLPxlWC57M3VyxEy9OUwHTAXzirpkSN.jpg', '2025-12-11 17:15:23', '2025-12-11 17:15:23', NULL),
(10, 'In quod non placeat', 'Consequuntur fugiat', 'Beginner', 'Adults', 'English', 0, 'courses/9QaDHM2HLsFE7wLxpYfY28itzZKt1tox9ZCYbot5.jpg', '2025-12-11 17:25:19', '2025-12-11 17:25:45', '2025-12-11 17:25:45');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `days_per_week` int(11) DEFAULT NULL,
  `session_duration` enum('30','60') DEFAULT NULL,
  `start_date` date NOT NULL DEFAULT curdate(),
  `status` enum('active','completed','cancelled') NOT NULL DEFAULT 'active',
  `admin_price` decimal(10,2) DEFAULT NULL,
  `currency` enum('CAD','USD','GBP') NOT NULL DEFAULT 'CAD',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `course_id`, `days_per_week`, `session_duration`, `start_date`, `status`, `admin_price`, `currency`, `created_at`, `updated_at`) VALUES
(8, 4, 7, 1, '30', '2025-12-11', 'active', 20.00, 'USD', '2025-12-11 17:50:09', '2025-12-11 17:50:09'),
(10, 3, 8, 2, '30', '2025-12-12', 'active', 50.00, 'CAD', '2025-12-12 10:08:50', '2025-12-12 10:08:50');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_payments`
--

CREATE TABLE `enrollment_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `enrollment_id` bigint(20) UNSIGNED NOT NULL,
  `month` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` enum('CAD','USD','GBP') NOT NULL DEFAULT 'CAD',
  `payment_status` enum('paid','unpaid','partial') NOT NULL DEFAULT 'unpaid',
  `paid_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollment_payments`
--

INSERT INTO `enrollment_payments` (`id`, `enrollment_id`, `month`, `amount`, `currency`, `payment_status`, `paid_at`, `notes`, `created_at`, `updated_at`) VALUES
(70, 8, '2025-12-01', 20.00, 'USD', 'unpaid', NULL, NULL, '2025-12-11 17:50:09', '2025-12-11 17:50:09'),
(71, 8, '2026-01-01', 20.00, 'USD', 'unpaid', NULL, NULL, '2025-12-11 17:52:55', '2025-12-11 17:52:55'),
(72, 8, '2026-02-01', 20.00, 'USD', 'unpaid', NULL, NULL, '2025-12-11 17:52:55', '2025-12-11 17:52:55'),
(73, 8, '2026-03-01', 20.00, 'USD', 'unpaid', NULL, NULL, '2025-12-11 17:52:55', '2025-12-11 17:52:55'),
(74, 8, '2026-04-01', 20.00, 'USD', 'unpaid', NULL, NULL, '2025-12-11 17:52:55', '2025-12-11 17:52:55'),
(75, 8, '2026-05-01', 20.00, 'USD', 'unpaid', NULL, NULL, '2025-12-11 17:52:55', '2025-12-11 17:52:55'),
(76, 10, '2025-12-01', 50.00, 'CAD', 'unpaid', NULL, NULL, '2025-12-12 10:08:50', '2025-12-12 10:08:50'),
(77, 10, '2026-01-01', 50.00, 'CAD', 'unpaid', NULL, NULL, '2025-12-12 10:09:00', '2025-12-12 10:09:00'),
(78, 10, '2026-02-01', 50.00, 'CAD', 'unpaid', NULL, NULL, '2025-12-12 10:09:00', '2025-12-12 10:09:00'),
(79, 10, '2026-03-01', 50.00, 'CAD', 'unpaid', NULL, NULL, '2025-12-12 10:09:00', '2025-12-12 10:09:00'),
(80, 10, '2026-04-01', 50.00, 'CAD', 'unpaid', NULL, NULL, '2025-12-12 10:09:00', '2025-12-12 10:09:00'),
(81, 10, '2026-05-01', 50.00, 'CAD', 'unpaid', NULL, NULL, '2025-12-12 10:09:00', '2025-12-12 10:09:00');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('trial','contact','registration') NOT NULL DEFAULT 'trial',
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `child_name` varchar(255) DEFAULT NULL,
  `child_age` varchar(255) DEFAULT NULL,
  `child_gender` enum('male','female') DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `preferred_course` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','contacted','converted','cancelled') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `type`, `full_name`, `email`, `phone`, `child_name`, `child_age`, `child_gender`, `country`, `city`, `preferred_course`, `message`, `status`, `admin_notes`, `created_at`, `updated_at`) VALUES
(1, 'trial', 'Aretha Garcia', 'xuhisomone@mailinator.com', '+1 (666) 598-2865', 'Lucian Burton', '18+ Adult', NULL, NULL, NULL, 'Quran Memorization', 'Quo ut dolor aut und', 'converted', 'Converted to parent account on 2025-11-23 19:18:19', '2025-11-23 17:18:08', '2025-11-23 17:18:19'),
(2, 'trial', 'Nicole Cabrera', 'puqyto@mailinator.com', '+1 (179) 987-7691', 'Quinn Coleman', '10-13 years', NULL, NULL, NULL, 'Arabic Language', 'Eligendi ad nesciunt', 'converted', 'Converted to parent account on 2025-12-05 11:06:31', '2025-12-05 09:05:44', '2025-12-05 09:06:31'),
(3, 'trial', 'Zeph Mills', 'tubylary@mailinator.com', '+1 (484) 577-5701', NULL, '6-9 years', NULL, NULL, NULL, 'Quran Memorization', NULL, 'converted', 'Converted to parent account on 2025-12-12 11:00:08', '2025-12-12 08:58:54', '2025-12-12 09:00:08');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(34, '0001_01_01_000000_create_users_table', 1),
(35, '0001_01_01_000001_create_cache_table', 1),
(36, '0001_01_01_000002_create_jobs_table', 1),
(37, '2025_11_15_190351_create_childrens_table', 1),
(38, '2025_11_15_191024_create_courses_table', 1),
(39, '2025_11_15_191043_create_enrollments_table', 1),
(40, '2025_11_15_191106_create_schedules_table', 1),
(41, '2025_11_15_191132_create_attendances_table', 1),
(42, '2025_11_15_191156_create_resources_table', 1),
(43, '2025_11_15_191210_create_reports_table', 1),
(44, '2025_11_15_191239_create_teacher_hours_table', 1),
(45, '2025_11_22_114800_create_inquiries_table', 1),
(46, '2025_11_24_190014_create_teacher_applications_table', 2),
(47, '2025_11_25_204331_add_photo_to_courses_table', 3),
(48, '2025_11_26_161939_add_filter_fields_to_courses_table', 4),
(49, '2025_11_27_221740_add_enrollment_id_to_schedules_table', 5),
(50, '2025_11_28_121307_add_hourly_rate_to_users_table', 6),
(51, '2025_11_28_121953_remove_hourly_rate_from_teacher_hours_table', 7),
(52, '2025_11_28_122000_remove_hourly_rate_from_teacher_hours_table', 7),
(53, '2025_12_05_145450_add_flexible_scheduling_to_enrollments_table', 8),
(54, '2025_12_05_150800_create_pricing_tiers_table', 9),
(55, '2025_12_05_152951_remove_price_and_duration_from_courses_table', 10),
(56, '2025_12_05_202324_create_enrollment_payments_table', 11),
(57, '2025_12_05_202326_remove_date_fields_from_enrollments_table', 12),
(58, '2025_12_05_211227_remove_payment_fields_from_enrollments_table', 13);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pricing_tiers`
--

CREATE TABLE `pricing_tiers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `days_per_week` int(11) NOT NULL,
  `session_duration` enum('30','60') NOT NULL,
  `price_cad` decimal(10,2) NOT NULL,
  `price_usd` decimal(10,2) NOT NULL,
  `price_gbp` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pricing_tiers`
--

INSERT INTO `pricing_tiers` (`id`, `days_per_week`, `session_duration`, `price_cad`, `price_usd`, `price_gbp`, `is_active`, `notes`, `created_at`, `updated_at`) VALUES
(3, 1, '30', 25.00, 20.00, 15.00, 1, NULL, '2025-12-11 15:46:45', '2025-12-11 15:46:45'),
(4, 2, '30', 50.00, 40.00, 30.00, 1, NULL, '2025-12-11 15:47:14', '2025-12-11 15:47:14'),
(5, 3, '30', 70.00, 55.00, 42.00, 1, NULL, '2025-12-11 15:47:38', '2025-12-11 15:47:38'),
(6, 4, '30', 90.00, 70.00, 53.00, 1, NULL, '2025-12-11 15:48:14', '2025-12-11 15:48:14'),
(7, 5, '30', 110.00, 88.00, 65.00, 1, NULL, '2025-12-11 15:48:45', '2025-12-11 15:48:45');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `level` varchar(255) DEFAULT NULL,
  `mastery_score` int(11) DEFAULT NULL,
  `strengths` text DEFAULT NULL,
  `weaknesses` text DEFAULT NULL,
  `behavior` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `type` enum('pdf','image','video','audio','link','other') NOT NULL DEFAULT 'other',
  `file_path` varchar(255) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `external_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `enrollment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime DEFAULT NULL,
  `zoom_link` varchar(255) DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `enrollment_id`, `course_id`, `teacher_id`, `student_id`, `starts_at`, `ends_at`, `zoom_link`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(322, 8, 7, 7, 4, '2025-12-15 17:00:00', '2025-12-15 18:00:00', NULL, 'completed', NULL, '2025-12-11 17:50:38', '2025-12-12 09:15:45'),
(323, 8, 7, 7, 4, '2025-12-17 17:00:00', '2025-12-17 18:00:00', NULL, 'scheduled', NULL, '2025-12-11 17:50:38', '2025-12-11 17:50:38'),
(324, 8, 7, 7, 4, '2025-12-22 17:00:00', '2025-12-22 18:00:00', NULL, 'scheduled', NULL, '2025-12-11 17:50:38', '2025-12-11 17:50:38'),
(325, 8, 7, 7, 4, '2025-12-24 17:00:00', '2025-12-24 18:00:00', NULL, 'scheduled', NULL, '2025-12-11 17:50:38', '2025-12-11 17:50:38'),
(326, 8, 7, 7, 4, '2025-12-29 17:00:00', '2025-12-29 18:00:00', NULL, 'scheduled', NULL, '2025-12-11 17:50:38', '2025-12-11 17:50:38'),
(327, 8, 7, 7, 4, '2025-12-31 17:00:00', '2025-12-31 18:00:00', NULL, 'scheduled', NULL, '2025-12-11 17:50:38', '2025-12-11 17:50:38');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('ErnGm6XL5m3EeKbYJqnmO74PeAkSnAa35N9fGU51', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNjNadVU0Vmw1TTYxTXIyd21LdTJ3cHNSQWNTTVBKcVgwNjFhWExjRSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo3OToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3BhcmVudC9zY2hlZHVsZS93ZWVrbHk/Y2hpbGRfaWQ9YWxsJndlZWtfc3RhcnQ9MjAyNS0xMi0xNSI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1765552732),
('Rf1rFpdkcXd2o3LDNvyEuzapuiFGnZQguaQ2rKOf', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUFJGZVpWQ0dpVDFaT3ltTVJObEw1aG44QzJDblJjakhyamswRUE4MSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozOToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3N0dWRlbnQvZGFzaGJvYXJkIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1765552732);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_applications`
--

CREATE TABLE `teacher_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `city` varchar(255) DEFAULT NULL,
  `gender` enum('male','female') NOT NULL,
  `birth_date` date DEFAULT NULL,
  `education_level` varchar(255) NOT NULL,
  `certifications` text DEFAULT NULL,
  `years_of_experience` int(11) NOT NULL,
  `teaching_experience` text NOT NULL,
  `subjects` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`subjects`)),
  `age_groups` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`age_groups`)),
  `teaching_methodology` text DEFAULT NULL,
  `availability` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`availability`)),
  `has_stable_internet` tinyint(1) NOT NULL DEFAULT 1,
  `has_quiet_space` tinyint(1) NOT NULL DEFAULT 1,
  `why_join` text DEFAULT NULL,
  `cv_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','reviewed','approved','rejected','converted') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teacher_applications`
--

INSERT INTO `teacher_applications` (`id`, `full_name`, `email`, `phone`, `country`, `city`, `gender`, `birth_date`, `education_level`, `certifications`, `years_of_experience`, `teaching_experience`, `subjects`, `age_groups`, `teaching_methodology`, `availability`, `has_stable_internet`, `has_quiet_space`, `why_join`, `cv_path`, `status`, `admin_notes`, `created_at`, `updated_at`) VALUES
(1, 'Karleigh Lawrence', 'vakiluby@mailinator.com', '+1 (557) 497-7388', 'Sed optio similique', 'Eu quis porro nesciu', 'female', '2025-06-20', 'Master\'s', 'Pariatur Eum distin', 3, 'Tempore sunt sint', '[\"Islamic Studies\"]', '[\"teens\"]', 'Officia minus esse', '[\"Sunday\",\"Monday\",\"Friday\"]', 1, 1, 'Vel vel quas porro i', 'teacher-cvs/YAopyX5kTyztwfYX2QtxBGIRvTJunI65SjZaVGJJ.pdf', 'converted', 'Converted to teacher account on 2025-11-25 20:10:05', '2025-11-25 18:01:03', '2025-11-25 18:10:05'),
(2, 'Dillon Fisher', 'qisonabof@mailinator.com', '+1 (866) 496-3413', 'Ab voluptas incididu', 'Culpa ea rem rerum', 'female', '1988-06-13', 'Bachelor\'s', 'Eos cum dolores quos', 2, 'Modi tempore soluta', '[\"Quran Memorization\",\"Tajweed\",\"Islamic Studies\"]', '[\"kids\"]', 'Natus consequatur o', '[\"Sunday\",\"Monday\",\"Wednesday\",\"Friday\"]', 1, 1, 'Voluptatibus velit', 'teacher-cvs/IlTKlKuQVNfBun5sORifwCryL09gUBSKZXtcTkRc.pdf', 'converted', 'Converted to teacher account on 2025-11-25 20:19:56', '2025-11-25 18:18:04', '2025-11-25 18:19:56'),
(3, 'Lacy Burke', 'lazer@mailinator.com', '+1 (917) 135-5435', 'Et possimus consequ', 'Proident explicabo', 'female', '2012-02-14', 'Bachelor\'s', 'In aperiam laudantiu', 4, 'Iusto ea nemo beatae', '[\"Quran Memorization\",\"Tajweed\"]', '[\"teens\"]', 'Est explicabo Illo', '[\"Sunday\",\"Tuesday\"]', 1, 1, 'Sit qui perspiciatis', 'teacher-cvs/8FVsjgKatzUPbE9tHHF39WdqlSoQMRwmh7okp9Sv.pdf', 'converted', 'Converted to teacher account on 2025-12-05 11:13:38', '2025-12-05 09:12:37', '2025-12-05 09:13:38');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_hours`
--

CREATE TABLE `teacher_hours` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `year` year(4) NOT NULL,
  `month` tinyint(4) NOT NULL,
  `total_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `total_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `paid_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teacher_hours`
--

INSERT INTO `teacher_hours` (`id`, `teacher_id`, `year`, `month`, `total_hours`, `total_salary`, `notes`, `is_paid`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, 7, '2025', 11, 0.00, 0.00, NULL, 0, NULL, '2025-11-28 10:03:33', '2025-11-28 10:23:55'),
(2, 6, '2025', 11, 0.00, 0.00, NULL, 0, NULL, '2025-11-28 10:03:33', '2025-11-28 10:03:33'),
(3, 5, '2025', 11, 0.00, 0.00, NULL, 0, NULL, '2025-11-28 10:03:33', '2025-11-28 10:03:33'),
(4, 6, '2025', 12, 0.00, 0.00, NULL, 0, NULL, '2025-12-01 16:26:59', '2025-12-01 16:26:59'),
(5, 5, '2025', 12, 0.00, 0.00, NULL, 0, NULL, '2025-12-01 16:26:59', '2025-12-01 16:26:59'),
(6, 7, '2025', 12, 0.00, 0.00, NULL, 0, NULL, '2025-12-01 16:27:00', '2025-12-06 17:17:52'),
(7, 11, '2025', 12, 0.00, 0.00, NULL, 0, NULL, '2025-12-05 09:27:31', '2025-12-05 09:27:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'Parent',
  `avatar` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `birth_date` varchar(255) DEFAULT NULL,
  `active` varchar(255) NOT NULL DEFAULT '0',
  `hourly_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `avatar`, `gender`, `phone`, `birth_date`, `active`, `hourly_rate`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', NULL, '$2y$12$2idMG2LzZ8Ao7aG2UgOJ0OXOVnwmG2snXevynkXnwP1RlezHXEGGe', 'Admin', NULL, NULL, NULL, NULL, '0', 0.00, NULL, '2025-11-23 17:12:41', '2025-11-23 17:12:41'),
(2, 'parent', 'parent@gmail.com', NULL, '$2y$12$osQPxo3DzEeP1MAvkKrknOxksFsrnadVjrpiZ82ivfKQ/Cd33yia6', 'Parent', NULL, NULL, '+1 (666) 598-2865', NULL, '1', 0.00, NULL, '2025-11-23 17:18:19', '2025-12-03 16:58:57'),
(3, 'Hedwig Rocha', 'kylumadi@mailinator.com', NULL, '$2y$12$g/npi88QLy/vyiwmQkXu3uK.eC2cHb5sp0qo5TIzasdvgQ8XGoG42', 'Student', NULL, 'male', NULL, '2010-07-25', '1', 0.00, NULL, '2025-11-23 17:18:58', '2025-11-23 17:18:58'),
(4, 'student', 'student@gmail.com', NULL, '$2y$12$/8.XMpXaJtMCaqH4cR7LL..F3aJ.4rTHJ3n1g2RzbJZWSkN7GPRku', 'Student', NULL, 'female', '01148016161', '2017-07-26', '1', 0.00, NULL, '2025-11-23 17:35:15', '2025-12-01 16:43:56'),
(5, 'Karleigh Lawrence', 'vakiluby@mailinator.com', NULL, '$2y$12$5Ts3NgMJ6LpjaUpIcrlnxuh2GXMkX9Tweu0ukfUBeiSxBHtMo3ewG', 'Teacher', NULL, 'female', '+1 (557) 497-7388', '2025-06-20', '1', 0.00, NULL, '2025-11-25 18:10:05', '2025-11-25 19:10:53'),
(6, 'Dillon Fisher', 'qisonabof@mailinator.com', NULL, '$2y$12$uh2N1TNtqtgcMbXnF1K48eQzs0knOIlMJs0rvaIwb5Ak7Jmz72qN.', 'Teacher', 'avatars/FKYxHAm5CxT05xhxQ7Z7cn3du0T3WWbrQUv3tWGM.png', 'female', '+1 (866) 496-3413', '2003-02-20', '1', 0.00, NULL, '2025-11-25 18:19:56', '2025-11-26 15:39:05'),
(7, 'teacher', 'teacher@test.com', NULL, '$2y$12$5Ts3NgMJ6LpjaUpIcrlnxuh2GXMkX9Tweu0ukfUBeiSxBHtMo3ewG', 'Teacher', 'avatars/qmZdYCGIzjKciWep9HpdIzinftZcuhKvbuitd0k2.jpg', 'male', '1382-9441', '2004-02-11', '1', 20.00, NULL, '2025-11-26 15:28:26', '2025-11-29 17:26:50'),
(8, 'Admin User', 'admin@ascend.com', NULL, '$2y$12$k.n61WBUucifXeWBg3tiOOczgA8HXff/4NLNKcLQzT5n8xXfo1txe', 'Admin', NULL, NULL, NULL, NULL, '1', 0.00, NULL, '2025-11-27 18:25:17', '2025-11-27 18:25:17'),
(9, 'Nicole Cabrera', 'puqyto@mailinator.com', NULL, '$2y$12$8GRBDrYumuKXgGRkHI/U.e0fGgnuVnl9pcvxarOwKPbRR.dM99AQG', 'Parent', NULL, NULL, '+1 (179) 987-7691', NULL, '1', 0.00, NULL, '2025-12-05 09:06:31', '2025-12-05 09:06:31'),
(10, 'Veronica Obrien', 'qyxowucugu@mailinator.com', NULL, '$2y$12$mZ0GT1hVxF1L9Hm5PA26LuOdRaQiVqJK4H7mffp0Eslws/sPJ7dk2', 'Student', NULL, 'female', NULL, '1988-06-13 00:00:00', '1', 0.00, NULL, '2025-12-05 09:07:20', '2025-12-05 09:07:20'),
(11, 'Lacy Burke', 'lazer@mailinator.com', NULL, '$2y$12$Pe/ssxbjNkkNkWeUM8ZgFeMNckXpZv.WdSH0cJYWuXtWTteHMZ.IG', 'Teacher', NULL, 'female', '+1 (917) 135-5435', '2012-02-14 00:00:00', '1', 0.00, NULL, '2025-12-05 09:13:38', '2025-12-05 09:13:38'),
(12, 'Zeph Mills', 'tubylary@mailinator.com', NULL, '$2y$12$zfSkjX5anS0pdiXclGlh1.COJnNwRh8jRD650WgaW14id0u5guPta', 'Parent', NULL, NULL, '+1 (484) 577-5701', NULL, '1', 0.00, NULL, '2025-12-12 09:00:08', '2025-12-12 09:00:08'),
(13, 'Lucian Mcknight', 'qygyvi@mailinator.com', NULL, '$2y$12$nak9fPEHeSBQ5CLd8VPYRecmCdtuIXVx3cLUfTNgEbtZjCzgtQQNW', 'Student', NULL, 'female', NULL, '2010-09-27 00:00:00', '1', 0.00, NULL, '2025-12-12 09:02:25', '2025-12-12 09:02:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendances_schedule_id_student_id_unique` (`schedule_id`,`student_id`),
  ADD KEY `attendances_student_id_foreign` (`student_id`),
  ADD KEY `attendances_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `children`
--
ALTER TABLE `children`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `children_parent_id_child_id_unique` (`parent_id`,`child_id`),
  ADD KEY `children_child_id_foreign` (`child_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enrollments_student_id_course_id_unique` (`student_id`,`course_id`),
  ADD KEY `enrollments_course_id_foreign` (`course_id`);

--
-- Indexes for table `enrollment_payments`
--
ALTER TABLE `enrollment_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enrollment_payments_enrollment_id_month_unique` (`enrollment_id`,`month`),
  ADD KEY `enrollment_payments_month_index` (`month`),
  ADD KEY `enrollment_payments_payment_status_index` (`payment_status`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inquiries_type_status_index` (`type`,`status`),
  ADD KEY `inquiries_email_index` (`email`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pricing_tiers`
--
ALTER TABLE `pricing_tiers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pricing_tiers_days_per_week_session_duration_unique` (`days_per_week`,`session_duration`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_teacher_id_foreign` (`teacher_id`),
  ADD KEY `reports_student_id_foreign` (`student_id`),
  ADD KEY `reports_course_id_foreign` (`course_id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resources_teacher_id_foreign` (`teacher_id`),
  ADD KEY `resources_student_id_foreign` (`student_id`),
  ADD KEY `resources_course_id_foreign` (`course_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedules_course_id_foreign` (`course_id`),
  ADD KEY `schedules_teacher_id_foreign` (`teacher_id`),
  ADD KEY `schedules_student_id_foreign` (`student_id`),
  ADD KEY `schedules_enrollment_id_foreign` (`enrollment_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `teacher_applications`
--
ALTER TABLE `teacher_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_applications_email_unique` (`email`),
  ADD KEY `teacher_applications_status_email_index` (`status`,`email`);

--
-- Indexes for table `teacher_hours`
--
ALTER TABLE `teacher_hours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_hours_teacher_id_year_month_unique` (`teacher_id`,`year`,`month`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `children`
--
ALTER TABLE `children`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `enrollment_payments`
--
ALTER TABLE `enrollment_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `pricing_tiers`
--
ALTER TABLE `pricing_tiers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=333;

--
-- AUTO_INCREMENT for table `teacher_applications`
--
ALTER TABLE `teacher_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `teacher_hours`
--
ALTER TABLE `teacher_hours`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `children`
--
ALTER TABLE `children`
  ADD CONSTRAINT `children_child_id_foreign` FOREIGN KEY (`child_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `children_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollment_payments`
--
ALTER TABLE `enrollment_payments`
  ADD CONSTRAINT `enrollment_payments_enrollment_id_foreign` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reports_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `resources_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `resources_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `resources_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `schedules_enrollment_id_foreign` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `schedules_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `teacher_hours`
--
ALTER TABLE `teacher_hours`
  ADD CONSTRAINT `teacher_hours_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
