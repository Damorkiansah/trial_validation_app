-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2026 at 10:12 AM
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
-- Database: `trial_validation_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(150) DEFAULT NULL,
  `user_role` varchar(80) DEFAULT NULL,
  `action` varchar(80) NOT NULL,
  `module` varchar(80) NOT NULL,
  `record_id` varchar(80) DEFAULT NULL,
  `record_label` varchar(255) DEFAULT NULL,
  `old_data` longtext DEFAULT NULL,
  `new_data` longtext DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `user_role`, `action`, `module`, `record_id`, `record_label`, `old_data`, `new_data`, `ip_address`, `user_agent`, `created_at`) VALUES
(216, 9, 'damor', 'Admin', 'LOGOUT', 'AUTH', '9', 'damor@cosmax.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-20 19:31:04'),
(217, 2, 'Staff Trial', 'Staff', 'LOGIN', 'AUTH', '2', 'staff@local.test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-20 19:31:27'),
(218, 2, 'Staff Trial', 'Staff', 'LOGOUT', 'AUTH', '2', 'staff@local.test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:46:12'),
(219, 2, 'Staff Trial', 'Staff', 'LOGIN', 'AUTH', '2', 'staff@local.test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:46:24'),
(220, 2, 'Staff Trial', 'Staff', 'CREATE', 'TRIAL', '12', 'trial_created', '[]', '{\n    \"trial_code\": \"TRIAL-20260521-034844\",\n    \"product\": \"Sample Tube Product\"\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:48:44'),
(221, 2, 'Staff Trial', 'Staff', 'CREATE', 'TRIAL', '12', 'TRIAL-20260521-034844', NULL, '{\n    \"trial_code\": \"TRIAL-20260521-034844\",\n    \"product\": \"Sample Tube Product\"\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:48:44'),
(222, 2, 'Staff Trial', 'Staff', 'CREATE', 'TRIAL', '13', 'trial_created', '[]', '{\n    \"trial_code\": \"TRIAL-20260521-034909\",\n    \"product\": \"Sample Tube Product\"\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:49:09'),
(223, 2, 'Staff Trial', 'Staff', 'CREATE', 'TRIAL', '13', 'TRIAL-20260521-034909', NULL, '{\n    \"trial_code\": \"TRIAL-20260521-034909\",\n    \"product\": \"Sample Tube Product\"\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:49:09'),
(224, 2, 'Staff Trial', 'Staff', 'UPDATE', 'PARAMETER', '13', 'validation_saved', '[]', '{\n    \"parameters\": 4\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:49:57'),
(225, 2, 'Staff Trial', 'Staff', 'UPDATE', 'PARAMETER', '13', 'validation_saved', '[]', '{\n    \"parameters\": 4\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:50:01'),
(226, 2, 'Staff Trial', 'Staff', 'UPDATE', 'PARAMETER', '13', 'validation_saved', '[]', '{\n    \"parameters\": 4\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:50:15'),
(227, 2, 'Staff Trial', 'Staff', 'UPDATE', 'TRIAL', '13', 'weighing_saved', '[]', '{\n    \"section\": \"Packaging\",\n    \"skipped\": false\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:51:00'),
(228, 2, 'Staff Trial', 'Staff', 'UPDATE', 'TRIAL', '13', 'weighing_saved', '[]', '{\n    \"section\": \"Filling\",\n    \"skipped\": false\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:51:27'),
(229, 2, 'Staff Trial', 'Staff', 'CREATE', 'ATTACHMENT', '13', 'attachments_uploaded', '[]', '{\n    \"category\": \"Vacuum Test\",\n    \"count\": 1\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:51:35'),
(230, 2, 'Staff Trial', 'Staff', 'CREATE', 'ATTACHMENT', '13', 'TRIAL-20260521-034909', NULL, '{\n    \"category\": \"Vacuum Test\",\n    \"count\": 1\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:51:35'),
(231, 2, 'Staff Trial', 'Staff', 'CREATE', 'ATTACHMENT', '13', 'attachments_uploaded', '[]', '{\n    \"category\": \"Press Test\",\n    \"count\": 1\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:51:47'),
(232, 2, 'Staff Trial', 'Staff', 'CREATE', 'ATTACHMENT', '13', 'TRIAL-20260521-034909', NULL, '{\n    \"category\": \"Press Test\",\n    \"count\": 1\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:51:47'),
(233, 2, 'Staff Trial', 'Staff', 'CREATE', 'ATTACHMENT', '13', 'attachments_uploaded', '[]', '{\n    \"category\": \"Filling Process\",\n    \"count\": 1\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:51:56'),
(234, 2, 'Staff Trial', 'Staff', 'CREATE', 'ATTACHMENT', '13', 'TRIAL-20260521-034909', NULL, '{\n    \"category\": \"Filling Process\",\n    \"count\": 1\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:51:56'),
(235, 2, 'Staff Trial', 'Staff', 'CREATE', 'ATTACHMENT', '13', 'attachments_uploaded', '[]', '{\n    \"category\": \"Final Product\",\n    \"count\": 1\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:52:07'),
(236, 2, 'Staff Trial', 'Staff', 'CREATE', 'ATTACHMENT', '13', 'TRIAL-20260521-034909', NULL, '{\n    \"category\": \"Final Product\",\n    \"count\": 1\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:52:07'),
(237, 2, 'Staff Trial', 'Staff', 'CREATE', 'ATTACHMENT', '13', 'attachments_uploaded', '[]', '{\n    \"category\": \"Machine Setting\",\n    \"count\": 1\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:52:14'),
(238, 2, 'Staff Trial', 'Staff', 'CREATE', 'ATTACHMENT', '13', 'TRIAL-20260521-034909', NULL, '{\n    \"category\": \"Machine Setting\",\n    \"count\": 1\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:52:14'),
(239, 2, 'Staff Trial', 'Staff', 'SUBMIT_REVIEW', 'REVIEW', '13', 'submitted_for_review', '[]', '{\n    \"round\": 1,\n    \"departments\": [\n        \"PRD\",\n        \"QAC\",\n        \"PRNI\",\n        \"PI\"\n    ]\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:53:17'),
(240, 2, 'Staff Trial', 'Staff', 'SUBMIT_REVIEW', 'TRIAL', '13', 'TRIAL-20260521-034909', NULL, '{\n    \"round\": 1,\n    \"departments\": [\n        \"PRD\",\n        \"QAC\",\n        \"PRNI\",\n        \"PI\"\n    ]\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:53:17'),
(241, 2, 'Staff Trial', 'Staff', 'SELECT_REVIEW_DEPARTMENT', 'REVIEW', '13', 'TRIAL-20260521-034909', NULL, '{\n    \"departments\": [\n        \"PRD\",\n        \"QAC\",\n        \"PRNI\",\n        \"PI\"\n    ]\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:53:17'),
(242, 2, 'Staff Trial', 'Staff', 'LOGOUT', 'AUTH', '2', 'staff@local.test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 09:03:55'),
(243, 8, 'Manager QAC', 'Manager QAC', 'LOGIN', 'AUTH', '8', 'manager.qac@local.test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 09:04:03'),
(244, 8, 'Manager QAC', 'Manager QAC', 'MARK_NOTIFICATION_READ', 'NOTIFICATION', '54', 'New Trial Waiting for Review', NULL, '{\n    \"id\": 54,\n    \"user_id\": 8,\n    \"role_target\": \"Reviewer\",\n    \"department_target\": \"QAC\",\n    \"trial_id\": 13,\n    \"title\": \"New Trial Waiting for Review\",\n    \"message\": \"Trial TRIAL-20260521-034909 - Sample Tube Product membutuhkan review department Anda.\",\n    \"type\": \"review\",\n    \"is_read\": 0,\n    \"removed_by_user\": 0,\n    \"removed_at\": null,\n    \"read_at\": null,\n    \"created_at\": \"2026-05-21 08:53:17\",\n    \"progress_status\": \"In Review\",\n    \"current_step\": \"Review\"\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 09:04:12'),
(245, 8, 'Manager QAC', 'Manager QAC', 'MARK_NOTIFICATION_READ', 'NOTIFICATION', '47', 'New Trial Waiting for Review', NULL, '{\n    \"id\": 47,\n    \"user_id\": 8,\n    \"role_target\": \"Reviewer\",\n    \"department_target\": \"QAC\",\n    \"trial_id\": 11,\n    \"title\": \"New Trial Waiting for Review\",\n    \"message\": \"Trial TRIAL-20260520-142100 - Sample Tube Product membutuhkan review department Anda.\",\n    \"type\": \"review\",\n    \"is_read\": 0,\n    \"removed_by_user\": 0,\n    \"removed_at\": null,\n    \"read_at\": null,\n    \"created_at\": \"2026-05-20 19:25:17\",\n    \"progress_status\": \"In Review\",\n    \"current_step\": \"Review\"\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 09:04:39'),
(246, 8, 'Manager QAC', 'Manager QAC', 'UPDATE', 'REVIEW', '13', 'department_reviewed', '[]', '{\n    \"department\": \"QAC\",\n    \"round\": 1,\n    \"reviewer_name\": \"Manager QAC\",\n    \"reviewer_email\": \"manager.qac@local.test\"\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 09:06:23'),
(247, 8, 'Manager QAC', 'Manager QAC', 'SUBMIT_REVIEW', 'REVIEW', '36', 'TRIAL-20260521-034909 QAC', NULL, '{\n    \"department\": \"QAC\",\n    \"round\": 1,\n    \"comment\": \"Ok sudah bagus\"\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 09:06:23'),
(248, 8, 'Manager QAC', 'Manager QAC', 'UPDATE', 'REVIEW', '11', 'department_reviewed', '[]', '{\n    \"department\": \"QAC\",\n    \"round\": 1,\n    \"reviewer_name\": \"Manager QAC\",\n    \"reviewer_email\": \"manager.qac@local.test\"\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 09:06:31'),
(249, 8, 'Manager QAC', 'Manager QAC', 'SUBMIT_REVIEW', 'REVIEW', '33', 'TRIAL-20260520-142100 QAC', NULL, '{\n    \"department\": \"QAC\",\n    \"round\": 1,\n    \"comment\": \"ok sudah bagus\"\n}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 09:06:31'),
(250, 8, 'Manager QAC', 'Manager QAC', 'LOGOUT', 'AUTH', '8', 'manager.qac@local.test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 15:10:43'),
(251, 9, 'damor', 'Admin', 'LOGIN', 'AUTH', '9', 'damor@cosmax.com', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 15:10:49');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) NOT NULL,
  `trial_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_email` varchar(150) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `old_data` longtext DEFAULT NULL,
  `new_data` longtext DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `trial_id`, `user_id`, `user_email`, `action`, `old_data`, `new_data`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 2, 9, 'damor@cosmax.com', 'trial_created', '[]', '{\"trial_code\":\"TRIAL-20260506-054921\",\"product\":\"Sample Sachet Product\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 10:49:21'),
(2, 2, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 10:49:35'),
(3, 2, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 10:49:41'),
(4, 2, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 10:49:46'),
(5, 2, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 10:50:23'),
(6, 2, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 10:51:04'),
(7, 2, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 10:51:06'),
(8, 2, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Filling Process\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 10:51:26'),
(9, 2, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Air Pressure Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 10:51:36'),
(10, 1, 9, 'damor@cosmax.com', 'trial_header_updated', '{\"id\":1,\"trial_code\":\"TRIAL-20260506-044614\",\"product_id\":2,\"product_name\":\"Sample Hotpouring Product\",\"finish_good_code\":\"FG-HOT-001\",\"product_type\":\"Tube\",\"validation_date\":\"2026-05-06\",\"validation_category\":\"New Product\",\"risk_level\":\"Medium\",\"validation_scope\":\"Appearance\",\"machine_used\":\"Fill JAR\",\"estimate_qty\":\"1.00\",\"batch_number\":null,\"bulk_code\":null,\"support_team\":null,\"initiated_person_team\":null,\"reason\":null,\"bom\":null,\"current_step\":\"Attachment\",\"progress_status\":\"Draft\",\"pending_with\":null,\"final_decision\":null,\"revision_no\":0,\"approved_by\":null,\"approved_at\":null,\"rejected_by\":null,\"rejected_at\":null,\"approval_comment\":null,\"created_by\":\"admin@local.test\",\"updated_at\":\"2026-05-06 10:42:54\",\"created_at\":\"2026-05-06 09:46:14\"}', '{\"product_id\":2,\"product_type\":\"Tube\",\"risk_level\":\"Medium\",\"validation_scope\":\"Appearance\",\"batch_number\":\"3\",\"bulk_code\":\"2434sdsdf \",\"support_team\":\"3432ssdfs\",\"initiated_person_team\":\"s23423sdfsd\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 11:09:08'),
(11, 1, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 11:09:14'),
(12, 1, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 11:09:15'),
(13, 1, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 11:09:16'),
(14, 1, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Final Product\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 11:10:10'),
(15, 1, 9, 'damor@cosmax.com', 'submitted_for_review', '[]', '{\"round\":1,\"departments\":[\"PRD\",\"RNI\",\"QAC\",\"PRNI\",\"PI\"]}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 11:10:24'),
(16, NULL, 9, 'damor@cosmax.com', 'admin_user_saved', '[]', '{\"email\":\"dian.andrian@cosmax.com\",\"role\":\"PRD\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 11:13:25'),
(17, 1, 3, 'prd@local.test', 'department_reviewed', '[]', '{\"department\":\"PRD\",\"round\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 11:15:58'),
(18, 2, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 21:35:03'),
(19, 2, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 21:35:04'),
(20, 2, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 21:35:05'),
(21, 1, 7, 'pi@local.test', 'department_reviewed', '[]', '{\"department\":\"PI\",\"round\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 22:09:09'),
(22, 1, 5, 'qac@local.test', 'department_reviewed', '[]', '{\"department\":\"QAC\",\"round\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 22:09:51'),
(23, 1, 6, 'prni@local.test', 'department_reviewed', '[]', '{\"department\":\"PRNI\",\"round\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 22:10:20'),
(24, 1, 4, 'rni@local.test', 'department_reviewed', '[]', '{\"department\":\"RNI\",\"round\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 22:10:43'),
(25, 1, 8, 'manager.qac@local.test', 'manager_approval', '[]', '{\"decision\":\"Approved\",\"comment\":\"iya sudaah oke\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 22:12:08'),
(26, 3, 9, 'damor@cosmax.com', 'trial_created', '[]', '{\"trial_code\":\"TRIAL-20260506-174415\",\"product\":\"Sample Tube Product\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 22:44:15'),
(27, 3, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 22:44:20'),
(28, 3, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 22:44:24'),
(29, 3, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 22:44:26'),
(30, 3, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 22:44:31'),
(31, 3, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 22:44:57'),
(32, 3, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Final Product\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 22:45:07'),
(33, 3, 9, 'damor@cosmax.com', 'submitted_for_review', '[]', '{\"round\":1,\"departments\":[\"PRD\",\"RNI\",\"QAC\",\"PRNI\",\"PI\"]}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 22:45:25'),
(34, 3, 10, 'dian.andrian@cosmax.com', 'department_reviewed', '[]', '{\"department\":\"PRD\",\"round\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 22:48:04'),
(35, 4, 9, 'damor@cosmax.com', 'trial_created', '[]', '{\"trial_code\":\"TRIAL-20260507-045044\",\"product\":\"Sample Hotpouring Product\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 09:50:44'),
(36, 5, 9, 'damor@cosmax.com', 'trial_created', '[]', '{\"trial_code\":\"TRIAL-20260507-045125\",\"product\":\"Sample Hotpouring Product\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 09:51:25'),
(37, 5, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 09:51:30'),
(38, 5, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 09:51:54'),
(39, 5, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 09:52:25'),
(40, 5, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 09:52:59'),
(41, 5, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Final Product\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 09:53:11'),
(42, 5, 9, 'damor@cosmax.com', 'trial_header_updated', '{\"id\":5,\"trial_code\":\"TRIAL-20260507-045125\",\"product_id\":2,\"product_name\":\"Sample Hotpouring Product\",\"finish_good_code\":\"FG-HOT-001\",\"product_type\":\"Hotpouring\",\"validation_date\":\"2026-05-19\",\"validation_category\":\"New Product\",\"risk_level\":\"Low\",\"validation_scope\":\"Appearance\",\"machine_used\":\"Fill SINGLE HEAD\",\"estimate_qty\":\"1334.00\",\"batch_number\":\"3\",\"bulk_code\":\"dfsd\",\"support_team\":\"sdfsd\",\"initiated_person_team\":\"dsfsdfs\",\"reason\":\"sdfsdfd\",\"bom\":\"sdfsdf 344355\",\"current_step\":\"Attachment\",\"progress_status\":\"Draft\",\"pending_with\":null,\"final_decision\":null,\"revision_no\":0,\"approved_by\":null,\"approved_at\":null,\"rejected_by\":null,\"rejected_at\":null,\"approval_comment\":null,\"created_by\":\"damor@cosmax.com\",\"updated_at\":\"2026-05-07 09:53:11\",\"created_at\":\"2026-05-07 09:51:25\"}', '{\"product_id\":2,\"product_type\":\"Hotpouring\",\"risk_level\":\"Low\",\"validation_scope\":\"Appearance\",\"batch_number\":\"3\",\"bulk_code\":\"dfsd\",\"support_team\":\"sdfsd\",\"initiated_person_team\":\"dsfsdfs\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 09:53:25'),
(43, 5, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 09:53:27'),
(44, 5, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 09:53:28'),
(45, 5, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 09:53:29'),
(46, NULL, 9, 'damor@cosmax.com', 'admin_user_saved', '[]', '{\"email\":\"pinto@cosmax.com\",\"role\":\"Staff\",\"department\":\"RNI\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 09:55:12'),
(47, 6, 9, 'damor@cosmax.com', 'trial_created', '[]', '{\"trial_code\":\"TRIAL-20260507-090448\",\"product\":\"Sample Hotpouring Product\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:04:48'),
(48, 6, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:05:47'),
(49, 6, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:06:29'),
(50, 6, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:07:31'),
(51, 6, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:07:58'),
(52, 6, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:08:10'),
(53, 6, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Press Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:08:17'),
(54, 6, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Machine Setting\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:08:27'),
(55, 6, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:08:55'),
(56, 6, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Line Configuration\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:10:04'),
(57, 6, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:10:23'),
(58, 6, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Filling Process\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:11:12'),
(59, 6, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Final Product\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:11:30'),
(60, 6, 9, 'damor@cosmax.com', 'submitted_for_review', '[]', '{\"round\":1,\"departments\":[\"PRD\",\"RNI\",\"QAC\",\"PRNI\",\"PI\"]}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:13:11'),
(61, 6, 10, 'dian.andrian@cosmax.com', 'department_reviewed', '[]', '{\"department\":\"PRD\",\"round\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:16:18'),
(62, 7, 9, 'damor@cosmax.com', 'trial_created', '[]', '{\"trial_code\":\"TRIAL-20260507-095357\",\"product\":\"Sample Tube Product\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:53:57'),
(63, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:53:59'),
(64, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:54:02'),
(65, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:54:04'),
(66, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:56:38'),
(67, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:56:39'),
(68, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:56:39'),
(69, 7, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:56:51'),
(70, 7, 9, 'damor@cosmax.com', 'attachment_deleted', '{\"id\":21,\"trial_id\":7,\"category\":\"Vacuum Test\",\"file_name\":\"97ecaf2e4b4009d3bbef003ece216f6b.png\",\"file_path\":\"/uploads/7/97ecaf2e4b4009d3bbef003ece216f6b.png\",\"uploaded_by\":\"damor@cosmax.com\",\"created_at\":\"2026-05-07 14:56:51\"}', '[]', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:57:01'),
(71, 7, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Air Pressure Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:57:10'),
(72, 7, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:57:58'),
(73, 7, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Final Product\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:58:09'),
(74, 7, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:58:20'),
(75, 7, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:58:33'),
(76, 7, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:58:44'),
(77, 7, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:58:59'),
(78, 7, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 14:59:24'),
(79, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:05:05'),
(80, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:05:12'),
(81, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:05:16'),
(82, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:05:17'),
(83, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:05:18'),
(84, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:13:40'),
(85, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:13:41'),
(86, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:13:41'),
(87, 7, 9, 'damor@cosmax.com', 'trial_header_updated', '{\"id\":7,\"trial_code\":\"TRIAL-20260507-095357\",\"product_id\":1,\"product_name\":\"Sample Tube Product\",\"finish_good_code\":\"FG-TUBE-001\",\"product_type\":\"Tube\",\"validation_date\":\"2026-05-07\",\"validation_category\":\"New Product\",\"risk_level\":\"Low\",\"validation_scope\":\"Filling\",\"machine_used\":\"Fill SINGLE HEAD\",\"estimate_qty\":\"12.00\",\"batch_number\":\"asd\",\"bulk_code\":\"sdfsd\",\"support_team\":\"Damor\",\"initiated_person_team\":\"Fauzi\",\"reason\":\"23423sdfs\",\"bom\":\"sda 324\",\"current_step\":\"Attachment\",\"progress_status\":\"Draft\",\"pending_with\":null,\"final_decision\":null,\"revision_no\":0,\"approved_by\":null,\"approved_at\":null,\"rejected_by\":null,\"rejected_at\":null,\"approval_comment\":null,\"created_by\":\"damor@cosmax.com\",\"updated_at\":\"2026-05-07 15:13:41\",\"created_at\":\"2026-05-07 14:53:57\"}', '{\"product_id\":1,\"product_type\":\"Tube\",\"risk_level\":\"Low\",\"validation_scope\":\"Filling\",\"batch_number\":\"asd\",\"bulk_code\":\"sdfsd\",\"support_team\":\"Damor\",\"initiated_person_team\":\"Fauzi\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:17:08'),
(88, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:17:10'),
(89, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:17:31'),
(90, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:17:33'),
(91, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:17:34'),
(92, 7, 9, 'damor@cosmax.com', 'trial_header_updated', '{\"id\":7,\"trial_code\":\"TRIAL-20260507-095357\",\"product_id\":1,\"product_name\":\"Sample Tube Product\",\"finish_good_code\":\"FG-TUBE-001\",\"product_type\":\"Tube\",\"validation_date\":\"2026-05-07\",\"validation_category\":\"New Product\",\"risk_level\":\"Low\",\"validation_scope\":\"Filling\",\"machine_used\":\"Fill SINGLE HEAD\",\"estimate_qty\":\"12.00\",\"batch_number\":\"asd\",\"bulk_code\":\"sdfsd\",\"support_team\":\"Damor\",\"initiated_person_team\":\"Fauzi\",\"reason\":\"23423sdfs\",\"bom\":\"6007395\\tSURCOSIL E-9(EVE VEGAN)(Z1)\\r\\n6028951\\tKF-6017(EVE VEGAN)\\r\\n6018464\\tUVINUL MC 80 (IDN)\\r\\n6010869\\t(SUNSCREEN AGENT)PARSOL EHS(EVE VEGAN)\\r\\n6011377\\tSF5600Z(EVE VEGAN)\\r\\n6000335\\tCEH(WAGLINOL 242)(RSPO)(EVE VEGAN)\\r\\n6018650\\tXIAMETER PMX-0345 (IDN)\\r\\n6017695\\tTRIMELA GEL SSA(EVE VEGAN)(Z1)\\r\\n6010021\\tCSMX 803(EVE VEGAN)(Z2)\\r\\n6009589\\tGELBASE B38(RSPO)(EVE VEGAN)\\r\\n4600559\\tGUE GUELE Bare Cushion-Ambr\\u00e9 #PB\\r\\n6007204\\tSILICA L-51(EVE VEGAN)\\r\\n6001221\\tPURIFIED WATER(50031700)(EVE VEGAN)\\r\\n6000369\\tEDTA-2NA(Z3)(ZA)(EVE VEGAN)\\r\\n6001743\\tMG SULFATE.7H2O(EVE VEGAN)(Z2)(NO MORE USE)\\r\\n6026981\\tMASCEROL REFINED GLYCERINE 99.7% USP RSPO MB(RSPO)(COSMOS)\\r\\n6012764\\tMINACARE PENTIOL (HYDROLITE-5)(EVE VEGAN)(ZC)\\r\\n6010464\\tEUXYL PE9010(50056645)(EVE VEGAN)\\r\\n6018217\\tAMINO MOISTURE - C\\r\\n6024950\\tFASHIONABLE ROSE 2 E_1709212 (HALAL)(IDN)(DP)\\r\\n6007395\\tSURCOSIL E-9(EVE VEGAN)(Z1)\\r\\n6024785\\tDMC-10(Z2)(EVE VEGAN)\\r\\n6018464\\tUVINUL MC 80 (IDN)\\r\\n6010869\\t(SUNSCREEN AGENT)PARSOL EHS(EVE VEGAN)\\r\\n\",\"current_step\":\"Attachment\",\"progress_status\":\"Draft\",\"pending_with\":null,\"final_decision\":null,\"revision_no\":0,\"approved_by\":null,\"approved_at\":null,\"rejected_by\":null,\"rejected_at\":null,\"approval_comment\":null,\"created_by\":\"damor@cosmax.com\",\"updated_at\":\"2026-05-07 15:17:34\",\"created_at\":\"2026-05-07 14:53:57\"}', '{\"product_id\":1,\"product_type\":\"Tube\",\"risk_level\":\"Low\",\"validation_scope\":\"Filling\",\"batch_number\":\"asd\",\"bulk_code\":\"sdfsd\",\"support_team\":\"Damor\",\"initiated_person_team\":\"Fauzi\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:23:48'),
(93, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:23:49'),
(94, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:23:51'),
(95, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:23:51'),
(96, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:35:00'),
(97, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:35:05'),
(98, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:41:20'),
(99, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:41:22'),
(100, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:41:25'),
(101, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:41:26'),
(102, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:41:27'),
(103, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:42:37'),
(104, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:51:54'),
(105, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:51:58'),
(106, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:52:11'),
(107, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:52:39'),
(108, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:52:44'),
(109, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:54:01'),
(110, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:54:08'),
(111, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 15:55:26'),
(112, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:01:32'),
(113, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:01:38'),
(114, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:01:42'),
(115, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:28:59'),
(116, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:29:00'),
(117, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:29:01'),
(118, 8, 9, 'damor@cosmax.com', 'trial_created', '[]', '{\"trial_code\":\"TRIAL-20260507-113541\",\"product\":\"Sample Sachet Product\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:35:41'),
(119, 8, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":2}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:35:44'),
(120, 8, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:35:47'),
(121, 8, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":2}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:36:04'),
(122, 8, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:36:07'),
(123, 8, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:36:11'),
(124, 8, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:36:28'),
(125, 8, 9, 'damor@cosmax.com', 'submitted_for_review', '[]', '{\"round\":1,\"departments\":[\"PRD\",\"RNI\",\"QAC\",\"PRNI\",\"PI\"]}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:37:03'),
(126, 8, 7, 'pi@local.test', 'department_reviewed', '[]', '{\"department\":\"PI\",\"round\":1,\"reviewer_name\":\"Reviewer PI\",\"reviewer_email\":\"pi@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:38:49'),
(127, 6, 7, 'pi@local.test', 'department_reviewed', '[]', '{\"department\":\"PI\",\"round\":1,\"reviewer_name\":\"Reviewer PI\",\"reviewer_email\":\"pi@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:39:00'),
(128, 3, 7, 'pi@local.test', 'department_reviewed', '[]', '{\"department\":\"PI\",\"round\":1,\"reviewer_name\":\"Reviewer PI\",\"reviewer_email\":\"pi@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:39:06'),
(129, 7, 9, 'damor@cosmax.com', 'trial_header_updated', '{\"id\":7,\"trial_code\":\"TRIAL-20260507-095357\",\"product_id\":1,\"product_name\":\"Sample Tube Product\",\"finish_good_code\":\"FG-TUBE-001\",\"product_type\":\"Tube\",\"validation_date\":\"2026-05-07\",\"validation_category\":\"New Product\",\"risk_level\":\"Low\",\"validation_scope\":\"Filling\",\"machine_used\":\"Fill SINGLE HEAD\",\"estimate_qty\":\"12.00\",\"batch_number\":\"asd\",\"bulk_code\":\"sdfsd\",\"support_team\":\"Damor\",\"initiated_person_team\":\"Fauzi\",\"reason\":\"23423sdfs\",\"bom\":\"6007395\\tSURCOSIL E-9(EVE VEGAN)(Z1)\\r\\n6028951\\tKF-6017(EVE VEGAN)\\r\\n6018464\\tUVINUL MC 80 (IDN)\\r\\n6010869\\t(SUNSCREEN AGENT)PARSOL EHS(EVE VEGAN)\\r\\n6011377\\tSF5600Z(EVE VEGAN)\\r\\n6000335\\tCEH(WAGLINOL 242)(RSPO)(EVE VEGAN)\\r\\n6018650\\tXIAMETER PMX-0345 (IDN)\\r\\n6017695\\tTRIMELA GEL SSA(EVE VEGAN)(Z1)\\r\\n6010021\\tCSMX 803(EVE VEGAN)(Z2)\\r\\n6009589\\tGELBASE B38(RSPO)(EVE VEGAN)\\r\\n4600559\\tGUE GUELE Bare Cushion-Ambr\\u00e9 #PB\\r\\n6007204\\tSILICA L-51(EVE VEGAN)\\r\\n6001221\\tPURIFIED WATER(50031700)(EVE VEGAN)\\r\\n6000369\\tEDTA-2NA(Z3)(ZA)(EVE VEGAN)\\r\\n6001743\\tMG SULFATE.7H2O(EVE VEGAN)(Z2)(NO MORE USE)\\r\\n6026981\\tMASCEROL REFINED GLYCERINE 99.7% USP RSPO MB(RSPO)(COSMOS)\\r\\n6012764\\tMINACARE PENTIOL (HYDROLITE-5)(EVE VEGAN)(ZC)\\r\\n6010464\\tEUXYL PE9010(50056645)(EVE VEGAN)\\r\\n6018217\\tAMINO MOISTURE - C\\r\\n6024950\\tFASHIONABLE ROSE 2 E_1709212 (HALAL)(IDN)(DP)\\r\\n6007395\\tSURCOSIL E-9(EVE VEGAN)(Z1)\\r\\n6024785\\tDMC-10(Z2)(EVE VEGAN)\\r\\n6018464\\tUVINUL MC 80 (IDN)\\r\\n6010869\\t(SUNSCREEN AGENT)PARSOL EHS(EVE VEGAN)\\r\\n\",\"current_step\":\"Attachment\",\"progress_status\":\"Draft\",\"pending_with\":null,\"final_decision\":null,\"revision_no\":0,\"approved_by\":null,\"approved_at\":null,\"rejected_by\":null,\"rejected_at\":null,\"approval_comment\":null,\"created_by\":\"damor@cosmax.com\",\"updated_at\":\"2026-05-07 16:29:01\",\"created_at\":\"2026-05-07 14:53:57\"}', '{\"product_id\":1,\"product_type\":\"Tube\",\"risk_level\":\"Low\",\"validation_scope\":\"Filling\",\"batch_number\":\"asd\",\"bulk_code\":\"sdfsd\",\"support_team\":\"Damor\",\"initiated_person_team\":\"Fauzi\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:41:27'),
(130, 7, 9, 'damor@cosmax.com', 'trial_header_updated', '{\"id\":7,\"trial_code\":\"TRIAL-20260507-095357\",\"product_id\":1,\"product_name\":\"Sample Tube Product\",\"finish_good_code\":\"FG-TUBE-001\",\"product_type\":\"Tube\",\"validation_date\":\"2026-05-07\",\"validation_category\":\"New Product\",\"risk_level\":\"Low\",\"validation_scope\":\"Filling\",\"machine_used\":\"Fill SINGLE HEAD\",\"estimate_qty\":\"12.00\",\"batch_number\":\"asd\",\"bulk_code\":\"sdfsd\",\"support_team\":\"Damor\",\"initiated_person_team\":\"Fauzi\",\"reason\":\"23423sdfs\",\"bom\":\"6007395\\tSURCOSIL E-9(EVE VEGAN)(Z1)\\r\\n6028951\\tKF-6017(EVE VEGAN)\\r\\n6018464\\tUVINUL MC 80 (IDN)\\r\\n6010869\\t(SUNSCREEN AGENT)PARSOL EHS(EVE VEGAN)\\r\\n6011377\\tSF5600Z(EVE VEGAN)\\r\\n6000335\\tCEH(WAGLINOL 242)(RSPO)(EVE VEGAN)\\r\\n6018650\\tXIAMETER PMX-0345 (IDN)\\r\\n6017695\\tTRIMELA GEL SSA(EVE VEGAN)(Z1)\\r\\n6010021\\tCSMX 803(EVE VEGAN)(Z2)\\r\\n6009589\\tGELBASE B38(RSPO)(EVE VEGAN)\\r\\n4600559\\tGUE GUELE Bare Cushion-Ambr\\u00e9 #PB\\r\\n6007204\\tSILICA L-51(EVE VEGAN)\\r\\n6001221\\tPURIFIED WATER(50031700)(EVE VEGAN)\\r\\n6000369\\tEDTA-2NA(Z3)(ZA)(EVE VEGAN)\\r\\n6001743\\tMG SULFATE.7H2O(EVE VEGAN)(Z2)(NO MORE USE)\\r\\n6026981\\tMASCEROL REFINED GLYCERINE 99.7% USP RSPO MB(RSPO)(COSMOS)\\r\\n6012764\\tMINACARE PENTIOL (HYDROLITE-5)(EVE VEGAN)(ZC)\\r\\n6010464\\tEUXYL PE9010(50056645)(EVE VEGAN)\\r\\n6018217\\tAMINO MOISTURE - C\\r\\n6024950\\tFASHIONABLE ROSE 2 E_1709212 (HALAL)(IDN)(DP)\\r\\n6007395\\tSURCOSIL E-9(EVE VEGAN)(Z1)\\r\\n6024785\\tDMC-10(Z2)(EVE VEGAN)\\r\\n6018464\\tUVINUL MC 80 (IDN)\\r\\n6010869\\t(SUNSCREEN AGENT)PARSOL EHS(EVE VEGAN)\\r\\n\",\"current_step\":\"Attachment\",\"progress_status\":\"Draft\",\"pending_with\":null,\"final_decision\":null,\"revision_no\":0,\"approved_by\":null,\"approved_at\":null,\"rejected_by\":null,\"rejected_at\":null,\"approval_comment\":null,\"created_by\":\"damor@cosmax.com\",\"updated_at\":\"2026-05-07 16:41:27\",\"created_at\":\"2026-05-07 14:53:57\"}', '{\"product_id\":1,\"product_type\":\"Tube\",\"risk_level\":\"Low\",\"validation_scope\":\"Filling\",\"batch_number\":\"asd\",\"bulk_code\":\"sdfsd\",\"support_team\":\"Damor\",\"initiated_person_team\":\"Fauzi\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:53:59'),
(131, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:54:01'),
(132, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:54:14'),
(133, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:54:17'),
(134, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:54:21'),
(135, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-07 16:54:22'),
(136, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 09:14:25'),
(137, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 09:14:26'),
(138, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 09:14:27'),
(139, 7, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 10:04:43'),
(140, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 10:04:45'),
(141, 7, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 10:04:46'),
(142, 7, 9, 'damor@cosmax.com', 'submitted_for_review', '[]', '{\"round\":1,\"departments\":[\"PRD\",\"QAC\",\"PRNI\"]}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 10:06:41'),
(143, 5, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 10:52:20'),
(144, 5, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 10:52:21'),
(145, 5, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 10:52:22'),
(146, 5, 9, 'damor@cosmax.com', 'submitted_for_review', '[]', '{\"round\":1,\"departments\":[\"RNI\",\"QAC\",\"PRNI\"]}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 10:52:33'),
(147, 8, 9, 'damor@cosmax.com', 'report_printed', '[]', '{\"report_type\":\"Report Summary\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 14:50:31'),
(148, NULL, 9, 'damor@cosmax.com', 'admin_user_saved', '[]', '{\"email\":\"lomoniroha@cosma.com\",\"role\":\"Staff\",\"department\":\"IT\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 14:57:24'),
(149, NULL, 9, 'damor@cosmax.com', 'admin_user_saved', '[]', '{\"email\":\"lomoniroha@cosmax.com\",\"role\":\"Staff\",\"department\":\"IT\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 14:58:34'),
(150, 5, 9, 'damor@cosmax.com', 'report_printed', '[]', '{\"report_type\":\"Report Summary\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 15:17:12'),
(151, 9, 13, 'lomoniroha@cosmax.com', 'trial_created', '[]', '{\"trial_code\":\"TRIAL-20260508-110631\",\"product\":\"Sample Hotpouring Product\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:06:31'),
(152, 9, 13, 'lomoniroha@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:06:33'),
(153, 9, 13, 'lomoniroha@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:07:10'),
(154, 9, 13, 'lomoniroha@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:07:45'),
(155, 9, 13, 'lomoniroha@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:07:52'),
(156, 9, 13, 'lomoniroha@cosmax.com', 'submitted_for_review', '[]', '{\"round\":1,\"departments\":[\"PRD\",\"RNI\",\"QAC\",\"PRNI\",\"PI\"]}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:08:35'),
(157, 9, 7, 'pi@local.test', 'report_printed', '[]', '{\"report_type\":\"Report Summary\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:10:05');
INSERT INTO `audit_logs` (`id`, `trial_id`, `user_id`, `user_email`, `action`, `old_data`, `new_data`, `ip_address`, `user_agent`, `created_at`) VALUES
(158, 4, 9, 'damor@cosmax.com', 'trial_header_updated', '{\"id\":4,\"trial_code\":\"TRIAL-20260507-045044\",\"product_id\":2,\"product_name\":\"Sample Hotpouring Product\",\"finish_good_code\":\"FG-HOT-001\",\"product_type\":\"Cushion\",\"validation_date\":\"2026-05-07\",\"validation_category\":\"New Product\",\"risk_level\":\"Low\",\"validation_scope\":\"Appearance\",\"machine_used\":\"Fill SINGLE HEAD\",\"estimate_qty\":\"11.00\",\"batch_number\":\"2sdf\",\"bulk_code\":\"sdfsd\",\"support_team\":\"sdfdsfs\",\"initiated_person_team\":\"fdgdfg\",\"reason\":\"dfgdf\",\"bom\":\"dfgdfg 23432\",\"current_step\":\"Validation\",\"progress_status\":\"Draft\",\"pending_with\":null,\"final_decision\":null,\"revision_no\":0,\"approved_by\":null,\"approved_at\":null,\"rejected_by\":null,\"rejected_at\":null,\"approval_comment\":null,\"created_by\":\"damor@cosmax.com\",\"updated_at\":\"2026-05-07 04:50:44\",\"created_at\":\"2026-05-07 09:50:44\",\"deleted_at\":null,\"deleted_by\":null}', '{\"product_id\":2,\"product_type\":\"Cushion\",\"risk_level\":\"Low\",\"validation_scope\":\"Appearance\",\"batch_number\":\"2sdf\",\"bulk_code\":\"sdfsd\",\"support_team\":\"sdfdsfs\",\"initiated_person_team\":\"fdgdfg\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:16:59'),
(159, 3, 4, 'rni@local.test', 'department_reviewed', '[]', '{\"department\":\"RNI\",\"round\":1,\"reviewer_name\":\"Reviewer RNI\",\"reviewer_email\":\"rni@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:18:50'),
(160, 8, 4, 'rni@local.test', 'department_reviewed', '[]', '{\"department\":\"RNI\",\"round\":1,\"reviewer_name\":\"Reviewer RNI\",\"reviewer_email\":\"rni@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:18:53'),
(161, 6, 4, 'rni@local.test', 'department_reviewed', '[]', '{\"department\":\"RNI\",\"round\":1,\"reviewer_name\":\"Reviewer RNI\",\"reviewer_email\":\"rni@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:18:55'),
(162, 5, 4, 'rni@local.test', 'department_reviewed', '[]', '{\"department\":\"RNI\",\"round\":1,\"reviewer_name\":\"Reviewer RNI\",\"reviewer_email\":\"rni@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:19:01'),
(163, 9, 4, 'rni@local.test', 'department_reviewed', '[]', '{\"department\":\"RNI\",\"round\":1,\"reviewer_name\":\"Reviewer RNI\",\"reviewer_email\":\"rni@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:19:06'),
(164, 9, 5, 'qac@local.test', 'department_reviewed', '[]', '{\"department\":\"QAC\",\"round\":1,\"reviewer_name\":\"Reviewer QAC\",\"reviewer_email\":\"qac@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:19:51'),
(165, 5, 5, 'qac@local.test', 'department_reviewed', '[]', '{\"department\":\"QAC\",\"round\":1,\"reviewer_name\":\"Reviewer QAC\",\"reviewer_email\":\"qac@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:19:57'),
(166, 7, 5, 'qac@local.test', 'department_reviewed', '[]', '{\"department\":\"QAC\",\"round\":1,\"reviewer_name\":\"Reviewer QAC\",\"reviewer_email\":\"qac@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:20:01'),
(167, 6, 5, 'qac@local.test', 'department_reviewed', '[]', '{\"department\":\"QAC\",\"round\":1,\"reviewer_name\":\"Reviewer QAC\",\"reviewer_email\":\"qac@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:20:04'),
(168, 3, 5, 'qac@local.test', 'department_reviewed', '[]', '{\"department\":\"QAC\",\"round\":1,\"reviewer_name\":\"Reviewer QAC\",\"reviewer_email\":\"qac@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:20:08'),
(169, 8, 5, 'qac@local.test', 'department_reviewed', '[]', '{\"department\":\"QAC\",\"round\":1,\"reviewer_name\":\"Reviewer QAC\",\"reviewer_email\":\"qac@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:20:12'),
(170, 9, 6, 'prni@local.test', 'department_reviewed', '[]', '{\"department\":\"PRNI\",\"round\":1,\"reviewer_name\":\"Reviewer PRNI\",\"reviewer_email\":\"prni@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:20:53'),
(171, 7, 6, 'prni@local.test', 'department_reviewed', '[]', '{\"department\":\"PRNI\",\"round\":1,\"reviewer_name\":\"Reviewer PRNI\",\"reviewer_email\":\"prni@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:20:56'),
(172, 5, 6, 'prni@local.test', 'department_reviewed', '[]', '{\"department\":\"PRNI\",\"round\":1,\"reviewer_name\":\"Reviewer PRNI\",\"reviewer_email\":\"prni@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:21:00'),
(173, 8, 6, 'prni@local.test', 'department_reviewed', '[]', '{\"department\":\"PRNI\",\"round\":1,\"reviewer_name\":\"Reviewer PRNI\",\"reviewer_email\":\"prni@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:21:02'),
(174, 6, 6, 'prni@local.test', 'department_reviewed', '[]', '{\"department\":\"PRNI\",\"round\":1,\"reviewer_name\":\"Reviewer PRNI\",\"reviewer_email\":\"prni@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:21:06'),
(175, 3, 6, 'prni@local.test', 'department_reviewed', '[]', '{\"department\":\"PRNI\",\"round\":1,\"reviewer_name\":\"Reviewer PRNI\",\"reviewer_email\":\"prni@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:21:09'),
(176, 3, 8, 'manager.qac@local.test', 'manager_approval', '[]', '{\"decision\":\"Approved\",\"comment\":\"sudah oke\",\"manager_name\":\"Manager QAC\",\"manager_email\":\"manager.qac@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:21:55'),
(177, 6, 8, 'manager.qac@local.test', 'manager_approval', '[]', '{\"decision\":\"Approved\",\"comment\":\"oke bagus\",\"manager_name\":\"Manager QAC\",\"manager_email\":\"manager.qac@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:22:04'),
(178, 5, 8, 'manager.qac@local.test', 'manager_approval', '[]', '{\"decision\":\"Rejected\",\"comment\":\"perlu di revis\",\"manager_name\":\"Manager QAC\",\"manager_email\":\"manager.qac@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:22:14'),
(179, NULL, 9, 'damor@cosmax.com', 'admin_user_saved', '[]', '{\"email\":\"fauzi@cosmax.com\",\"role\":\"Staff\",\"department\":\"QA\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:38:09'),
(180, NULL, 9, 'damor@cosmax.com', 'admin_user_saved', '[]', '{\"email\":\"hanbi@cosmax.com\",\"role\":\"Staff\",\"department\":\"QA\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:38:32'),
(181, 10, 14, 'fauzi@cosmax.com', 'trial_created', '[]', '{\"trial_code\":\"TRIAL-20260508-114120\",\"product\":\"Sample Hotpouring Product\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:41:20'),
(182, 10, 14, 'fauzi@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:42:19'),
(183, 10, 14, 'fauzi@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:43:01'),
(184, 10, 14, 'fauzi@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:45:21'),
(185, 10, 14, 'fauzi@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:45:33'),
(186, 10, 14, 'fauzi@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Press Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:45:43'),
(187, 10, 14, 'fauzi@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Filling Process\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:45:54'),
(188, 10, 14, 'fauzi@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:50:50'),
(189, 10, 14, 'fauzi@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:50:52'),
(190, 10, 14, 'fauzi@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:50:52'),
(191, 10, 14, 'fauzi@cosmax.com', 'trial_header_updated', '{\"id\":10,\"trial_code\":\"TRIAL-20260508-114120\",\"product_id\":2,\"product_name\":\"Sample Hotpouring Product\",\"finish_good_code\":\"FG-HOT-001\",\"product_type\":\"Tube\",\"validation_date\":\"2026-05-08\",\"validation_category\":\"New Product\",\"risk_level\":\"Medium\",\"validation_scope\":\"Filling\",\"machine_used\":\"Fill TUBE SC 10\",\"estimate_qty\":\"50.00\",\"batch_number\":\"abc\",\"bulk_code\":\"123456\",\"support_team\":\"Damor\",\"initiated_person_team\":\"heri\",\"reason\":\"unik pacvkaging\",\"bom\":\"6007204\\tSILICA L-51(EVE VEGAN)\\r\\n6001221\\tPURIFIED WATER(50031700)(EVE VEGAN)\\r\\n6000369\\tEDTA-2NA(Z3)(ZA)(EVE VEGAN)\\r\\n6001743\\tMG SULFATE.7H2O(EVE VEGAN)(Z2)(NO MORE USE)\\r\\n6026981\\tMASCEROL REFINED GLYCERINE 99.7% USP RSPO MB(RSPO)(COSMOS)\\r\\n6012764\\tMINACARE PENTIOL (HYDROLITE-5)(EVE VEGAN)(ZC)\\r\\n6010464\\tEUXYL PE9010(50056645)(EVE VEGAN)\\r\\n6018217\\tAMINO MOISTURE - C\\r\\n6024950\\tFASHIONABLE ROSE 2 E_1709212 (HALAL)(IDN)(DP)\\r\\n6007395\\tSURCOSIL E-9(EVE VEGAN)(Z1)\\r\\n6028951\\tKF-6017(EVE VEGAN)\\r\\n6018464\\tUVINUL MC 80 (IDN)\\r\\n6010869\\t(SUNSCREEN AGENT)PARSOL EHS(EVE VEGAN)\\r\\n6011377\\tSF5600Z(EVE VEGAN)\\r\\n6000335\\tCEH(WAGLINOL 242)(RSPO)(EVE VEGAN)\\r\\n6018650\\tXIAMETER PMX-0345 (IDN)\\r\\n6009589\\tGELBASE B38(RSPO)(EVE VEGAN)\\r\\n6010021\\tCSMX 803(EVE VEGAN)(Z2)\\r\\n6017695\\tTRIMELA GEL SSA(EVE VEGAN)(Z1)\\r\\n4600558\\tGUE GUELE Bare Cushion-Sandr\\u00e9 #PB\\r\\n6007204\\tSILICA L-51(EVE VEGAN)\\r\\n6001221\\tPURIFIED WATER(50031700)(EVE VEGAN)\\r\\n6000369\\tEDTA-2NA(Z3)(ZA)(EVE VEGAN)\\r\\n6001743\\tMG SULFATE.7H2O(EVE VEGAN)(Z2)(NO MORE USE)\\r\\n6026981\\tMASCEROL REFINED GLYCERINE 99.7% USP RSPO MB(RSPO)(COSMOS)\\r\\n6012764\\tMINACARE PENTIOL (HYDROLITE-5)(EVE VEGAN)(ZC)\\r\\n6010464\\tEUXYL PE9010(50056645)(EVE VEGAN)\\r\\n\",\"current_step\":\"Attachment\",\"progress_status\":\"Draft\",\"pending_with\":null,\"final_decision\":null,\"revision_no\":0,\"approved_by\":null,\"approved_at\":null,\"rejected_by\":null,\"rejected_at\":null,\"approval_comment\":null,\"created_by\":\"fauzi@cosmax.com\",\"updated_at\":\"2026-05-08 16:50:52\",\"created_at\":\"2026-05-08 16:41:20\",\"deleted_at\":null,\"deleted_by\":null}', '{\"product_id\":2,\"product_type\":\"Tube\",\"risk_level\":\"Medium\",\"validation_scope\":\"Filling\",\"batch_number\":\"abc\",\"bulk_code\":\"123456\",\"support_team\":\"Damor\",\"initiated_person_team\":\"heri\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:51:07'),
(192, 10, 14, 'fauzi@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:51:08'),
(193, 10, 14, 'fauzi@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:51:09'),
(194, 10, 14, 'fauzi@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:51:10'),
(195, 10, 14, 'fauzi@cosmax.com', 'report_printed', '[]', '{\"report_type\":\"Report Summary\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:51:47'),
(196, 6, 14, 'fauzi@cosmax.com', 'report_printed', '[]', '{\"report_type\":\"Report Summary\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:53:01'),
(197, 5, 14, 'fauzi@cosmax.com', 'trial_header_updated', '{\"id\":5,\"trial_code\":\"TRIAL-20260507-045125\",\"product_id\":2,\"product_name\":\"Sample Hotpouring Product\",\"finish_good_code\":\"FG-HOT-001\",\"product_type\":\"Hotpouring\",\"validation_date\":\"2026-05-19\",\"validation_category\":\"New Product\",\"risk_level\":\"Low\",\"validation_scope\":\"Appearance\",\"machine_used\":\"Fill SINGLE HEAD\",\"estimate_qty\":\"1334.00\",\"batch_number\":\"3\",\"bulk_code\":\"dfsd\",\"support_team\":\"sdfsd\",\"initiated_person_team\":\"dsfsdfs\",\"reason\":\"sdfsdfd\",\"bom\":\"sdfsdf 344355\",\"current_step\":\"Revision\",\"progress_status\":\"Need Revision\",\"pending_with\":\"Staff\",\"final_decision\":\"Need Revision\",\"revision_no\":1,\"approved_by\":null,\"approved_at\":null,\"rejected_by\":\"Manager QAC\",\"rejected_at\":\"2026-05-08 16:22:14\",\"approval_comment\":\"perlu di revis\",\"created_by\":\"damor@cosmax.com\",\"updated_at\":\"2026-05-08 16:22:14\",\"created_at\":\"2026-05-07 09:51:25\",\"deleted_at\":null,\"deleted_by\":null}', '{\"product_id\":2,\"product_type\":\"Hotpouring\",\"risk_level\":\"Low\",\"validation_scope\":\"Appearance\",\"batch_number\":\"3\",\"bulk_code\":\"dfsd\",\"support_team\":\"sdfsd\",\"initiated_person_team\":\"dsfsdfs\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:56:30'),
(198, 5, 14, 'fauzi@cosmax.com', 'validation_saved', '[]', '{\"parameters\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:56:33'),
(199, 5, 14, 'fauzi@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:56:35'),
(200, 5, 14, 'fauzi@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:56:36'),
(201, 5, 14, 'fauzi@cosmax.com', 'trial_header_updated', '{\"id\":5,\"trial_code\":\"TRIAL-20260507-045125\",\"product_id\":2,\"product_name\":\"Sample Hotpouring Product\",\"finish_good_code\":\"FG-HOT-001\",\"product_type\":\"Hotpouring\",\"validation_date\":\"2026-05-19\",\"validation_category\":\"New Product\",\"risk_level\":\"Low\",\"validation_scope\":\"Appearance\",\"machine_used\":\"Fill SINGLE HEAD\",\"estimate_qty\":\"1334.00\",\"batch_number\":\"3\",\"bulk_code\":\"dfsd\",\"support_team\":\"sdfsd\",\"initiated_person_team\":\"dsfsdfs\",\"reason\":\"sdfsdfd\",\"bom\":\"sdfsdf 344355\",\"current_step\":\"Attachment\",\"progress_status\":\"Need Revision\",\"pending_with\":\"Staff\",\"final_decision\":\"Need Revision\",\"revision_no\":1,\"approved_by\":null,\"approved_at\":null,\"rejected_by\":\"Manager QAC\",\"rejected_at\":\"2026-05-08 16:22:14\",\"approval_comment\":\"perlu di revis\",\"created_by\":\"damor@cosmax.com\",\"updated_at\":\"2026-05-08 16:56:36\",\"created_at\":\"2026-05-07 09:51:25\",\"deleted_at\":null,\"deleted_by\":null}', '{\"product_id\":2,\"product_type\":\"Tube\",\"risk_level\":\"Low\",\"validation_scope\":\"Appearance\",\"batch_number\":\"3\",\"bulk_code\":\"dfsd\",\"support_team\":\"sdfsd\",\"initiated_person_team\":\"dsfsdfs\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:56:59'),
(202, 5, 14, 'fauzi@cosmax.com', 'trial_header_updated', '{\"id\":5,\"trial_code\":\"TRIAL-20260507-045125\",\"product_id\":2,\"product_name\":\"Sample Hotpouring Product\",\"finish_good_code\":\"FG-HOT-001\",\"product_type\":\"Tube\",\"validation_date\":\"2026-05-19\",\"validation_category\":\"New Product\",\"risk_level\":\"Low\",\"validation_scope\":\"Appearance\",\"machine_used\":\"Fill SINGLE HEAD\",\"estimate_qty\":\"1334.00\",\"batch_number\":\"3\",\"bulk_code\":\"dfsd\",\"support_team\":\"sdfsd\",\"initiated_person_team\":\"dsfsdfs\",\"reason\":\"sdfsdfd\",\"bom\":\"sdfsdf 344355\",\"current_step\":\"Attachment\",\"progress_status\":\"Need Revision\",\"pending_with\":\"Staff\",\"final_decision\":\"Need Revision\",\"revision_no\":1,\"approved_by\":null,\"approved_at\":null,\"rejected_by\":\"Manager QAC\",\"rejected_at\":\"2026-05-08 16:22:14\",\"approval_comment\":\"perlu di revis\",\"created_by\":\"damor@cosmax.com\",\"updated_at\":\"2026-05-08 16:56:59\",\"created_at\":\"2026-05-07 09:51:25\",\"deleted_at\":null,\"deleted_by\":null}', '{\"product_id\":2,\"product_type\":\"Mixing\",\"risk_level\":\"Low\",\"validation_scope\":\"Appearance\",\"batch_number\":\"3\",\"bulk_code\":\"dfsd\",\"support_team\":\"sdfsd\",\"initiated_person_team\":\"dsfsdfs\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:57:04'),
(203, 5, 14, 'fauzi@cosmax.com', 'trial_header_updated', '{\"id\":5,\"trial_code\":\"TRIAL-20260507-045125\",\"product_id\":2,\"product_name\":\"Sample Hotpouring Product\",\"finish_good_code\":\"FG-HOT-001\",\"product_type\":\"Mixing\",\"validation_date\":\"2026-05-19\",\"validation_category\":\"New Product\",\"risk_level\":\"Low\",\"validation_scope\":\"Appearance\",\"machine_used\":\"Fill SINGLE HEAD\",\"estimate_qty\":\"1334.00\",\"batch_number\":\"3\",\"bulk_code\":\"dfsd\",\"support_team\":\"sdfsd\",\"initiated_person_team\":\"dsfsdfs\",\"reason\":\"sdfsdfd\",\"bom\":\"sdfsdf 344355\",\"current_step\":\"Attachment\",\"progress_status\":\"Need Revision\",\"pending_with\":\"Staff\",\"final_decision\":\"Need Revision\",\"revision_no\":1,\"approved_by\":null,\"approved_at\":null,\"rejected_by\":\"Manager QAC\",\"rejected_at\":\"2026-05-08 16:22:14\",\"approval_comment\":\"perlu di revis\",\"created_by\":\"damor@cosmax.com\",\"updated_at\":\"2026-05-08 16:57:04\",\"created_at\":\"2026-05-07 09:51:25\",\"deleted_at\":null,\"deleted_by\":null}', '{\"product_id\":2,\"product_type\":\"Bottle + Screw Cap\",\"risk_level\":\"Low\",\"validation_scope\":\"Appearance\",\"batch_number\":\"3\",\"bulk_code\":\"dfsd\",\"support_team\":\"sdfsd\",\"initiated_person_team\":\"dsfsdfs\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 16:57:11'),
(204, 6, 9, 'damor@cosmax.com', 'report_printed', '[]', '{\"report_type\":\"Report Summary\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 17:14:25'),
(205, 6, 9, 'damor@cosmax.com', 'report_printed', '[]', '{\"report_type\":\"Report Summary\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-08 23:53:05'),
(206, 5, 9, 'damor@cosmax.com', 'trial_header_updated', '{\"id\":5,\"trial_code\":\"TRIAL-20260507-045125\",\"product_id\":2,\"product_name\":\"Sample Hotpouring Product\",\"finish_good_code\":\"FG-HOT-001\",\"product_type\":\"Bottle + Screw Cap\",\"validation_date\":\"2026-05-19\",\"validation_category\":\"New Product\",\"risk_level\":\"Low\",\"validation_scope\":\"Appearance\",\"machine_used\":\"Fill SINGLE HEAD\",\"estimate_qty\":\"1334.00\",\"batch_number\":\"3\",\"bulk_code\":\"dfsd\",\"support_team\":\"sdfsd\",\"initiated_person_team\":\"dsfsdfs\",\"reason\":\"sdfsdfd\",\"bom\":\"sdfsdf 344355\",\"current_step\":\"Attachment\",\"progress_status\":\"Need Revision\",\"pending_with\":\"Staff\",\"final_decision\":\"Need Revision\",\"revision_no\":1,\"approved_by\":null,\"approved_at\":null,\"rejected_by\":\"Manager QAC\",\"rejected_at\":\"2026-05-08 16:22:14\",\"approval_comment\":\"perlu di revis\",\"created_by\":\"damor@cosmax.com\",\"updated_at\":\"2026-05-08 16:57:11\",\"created_at\":\"2026-05-07 09:51:25\",\"deleted_at\":null,\"deleted_by\":null}', '{\"product_id\":2,\"product_type\":\"Bottle + Screw Cap\",\"risk_level\":\"Low\",\"validation_scope\":\"Appearance\",\"batch_number\":\"3\",\"bulk_code\":\"dfsd\",\"support_team\":\"sdfsd\",\"initiated_person_team\":\"dsfsdfs\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-19 09:26:32'),
(207, 11, 9, 'damor@cosmax.com', 'trial_created', '[]', '{\"trial_code\":\"TRIAL-20260520-142100\",\"product\":\"Sample Tube Product\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-20 19:21:00'),
(208, 11, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-20 19:21:27'),
(209, 11, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-20 19:22:06'),
(210, 11, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-20 19:22:12'),
(211, 11, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Press Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-20 19:22:22'),
(212, 11, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-20 19:22:31'),
(213, 11, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-20 19:22:38'),
(214, 11, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-20 19:22:52'),
(215, 11, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Press Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-20 19:23:06'),
(216, 11, 9, 'damor@cosmax.com', 'attachments_uploaded', '[]', '{\"category\":\"Press Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-20 19:23:40'),
(217, 11, 9, 'damor@cosmax.com', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-20 19:24:53'),
(218, 11, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-20 19:24:54'),
(219, 11, 9, 'damor@cosmax.com', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-20 19:24:56'),
(220, 11, 9, 'damor@cosmax.com', 'submitted_for_review', '[]', '{\"round\":1,\"departments\":[\"PRD\",\"QAC\",\"PRNI\"]}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-20 19:25:17'),
(221, 12, 2, 'staff@local.test', 'trial_created', '[]', '{\"trial_code\":\"TRIAL-20260521-034844\",\"product\":\"Sample Tube Product\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:48:44'),
(222, 13, 2, 'staff@local.test', 'trial_created', '[]', '{\"trial_code\":\"TRIAL-20260521-034909\",\"product\":\"Sample Tube Product\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:49:09'),
(223, 13, 2, 'staff@local.test', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:49:57'),
(224, 13, 2, 'staff@local.test', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:50:01'),
(225, 13, 2, 'staff@local.test', 'validation_saved', '[]', '{\"parameters\":4}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:50:15'),
(226, 13, 2, 'staff@local.test', 'weighing_saved', '[]', '{\"section\":\"Packaging\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:51:00'),
(227, 13, 2, 'staff@local.test', 'weighing_saved', '[]', '{\"section\":\"Filling\",\"skipped\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:51:27'),
(228, 13, 2, 'staff@local.test', 'attachments_uploaded', '[]', '{\"category\":\"Vacuum Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:51:35'),
(229, 13, 2, 'staff@local.test', 'attachments_uploaded', '[]', '{\"category\":\"Press Test\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:51:47'),
(230, 13, 2, 'staff@local.test', 'attachments_uploaded', '[]', '{\"category\":\"Filling Process\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:51:56'),
(231, 13, 2, 'staff@local.test', 'attachments_uploaded', '[]', '{\"category\":\"Final Product\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:52:07'),
(232, 13, 2, 'staff@local.test', 'attachments_uploaded', '[]', '{\"category\":\"Machine Setting\",\"count\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:52:14'),
(233, 13, 2, 'staff@local.test', 'submitted_for_review', '[]', '{\"round\":1,\"departments\":[\"PRD\",\"QAC\",\"PRNI\",\"PI\"]}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 08:53:17'),
(234, 13, 8, 'manager.qac@local.test', 'department_reviewed', '[]', '{\"department\":\"QAC\",\"round\":1,\"reviewer_name\":\"Manager QAC\",\"reviewer_email\":\"manager.qac@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 09:06:23'),
(235, 11, 8, 'manager.qac@local.test', 'department_reviewed', '[]', '{\"department\":\"QAC\",\"round\":1,\"reviewer_name\":\"Manager QAC\",\"reviewer_email\":\"manager.qac@local.test\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-21 09:06:31');

-- --------------------------------------------------------

--
-- Table structure for table `master_options`
--

CREATE TABLE `master_options` (
  `id` int(11) NOT NULL,
  `type` varchar(80) NOT NULL,
  `name` varchar(200) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `master_options`
--

INSERT INTO `master_options` (`id`, `type`, `name`, `sort_order`, `is_active`, `deleted_at`, `deleted_by`) VALUES
(1, 'validation_category', 'New Product', 1, 1, NULL, NULL),
(2, 'validation_category', 'Change Process', 2, 1, NULL, NULL),
(3, 'validation_category', 'Re-validation', 3, 1, NULL, NULL),
(4, 'validation_scope', 'Appearance', 1, 1, NULL, NULL),
(5, 'validation_scope', 'Filling', 2, 1, NULL, NULL),
(6, 'validation_scope', 'Packaging', 3, 1, NULL, NULL),
(7, 'validation_scope', 'Full Validation', 4, 1, NULL, NULL),
(8, 'product_type', 'Mixing', 1, 1, NULL, NULL),
(9, 'product_type', 'Tube', 2, 1, NULL, NULL),
(10, 'product_type', 'Bottle + Screw Cap', 3, 1, NULL, NULL),
(11, 'product_type', 'Bottle + Screw Pump Cap', 4, 1, NULL, NULL),
(12, 'product_type', 'Bottle + Airless Pump Cap', 5, 1, NULL, NULL),
(13, 'product_type', 'Jar + Screw Cap', 6, 1, NULL, NULL),
(14, 'product_type', 'Jar + Airless Pump Cap', 7, 1, NULL, NULL),
(15, 'product_type', 'Cushion', 8, 1, NULL, NULL),
(16, 'product_type', 'Press Powder', 9, 1, NULL, NULL),
(17, 'product_type', 'Loose Powder', 10, 1, NULL, NULL),
(18, 'product_type', 'Lipstick', 11, 1, NULL, NULL),
(19, 'product_type', 'Hotpouring', 12, 1, NULL, NULL),
(20, 'product_type', 'Sachet', 13, 1, NULL, NULL),
(21, 'product_type', 'Mask Sheet', 14, 1, NULL, NULL),
(22, 'product_type', 'Lip Concealer', 15, 1, NULL, NULL),
(23, 'product_type', 'Mascara', 16, 1, NULL, NULL),
(24, 'machine_used', 'Fill JAR', 1, 1, NULL, NULL),
(25, 'machine_used', 'Fill SINGLE HEAD', 2, 1, NULL, NULL),
(26, 'machine_used', 'Fill FOUR HEAD', 3, 1, NULL, NULL),
(27, 'machine_used', 'Fill CUSHION AUTO', 4, 1, NULL, NULL),
(28, 'machine_used', 'Fill CUSHION MANUAL', 5, 1, NULL, NULL),
(29, 'machine_used', 'Fill SEMI AUTO', 6, 1, NULL, NULL),
(30, 'machine_used', 'Fill JIREH 1', 7, 1, NULL, NULL),
(31, 'machine_used', 'Fill JIREH 2', 8, 1, NULL, NULL),
(32, 'machine_used', 'Fill TUBE SC 10', 9, 1, NULL, NULL),
(33, 'machine_used', 'Fill TUBE SC 11', 10, 1, NULL, NULL),
(34, 'machine_used', 'Fill TUBE SC 12', 11, 1, NULL, NULL),
(35, 'machine_used', 'Fill POUCH/SACHET 1', 12, 1, NULL, NULL),
(36, 'machine_used', 'Fill POUCH/SACHET 2', 13, 1, NULL, NULL),
(37, 'machine_used', 'Fill MASK SHEET 1', 14, 1, NULL, NULL),
(38, 'machine_used', 'Fill MASK SHEET 2', 15, 1, NULL, NULL),
(39, 'machine_used', 'Fill SEMI AUTO CONCEALER', 16, 1, NULL, NULL),
(40, 'machine_used', 'Fill PISTON KOREA 1', 17, 1, NULL, NULL),
(41, 'machine_used', 'Fill PISTON KOREA 2', 18, 1, NULL, NULL),
(42, 'machine_used', 'Fill MANUAL WIRPACK', 19, 1, NULL, NULL),
(43, 'machine_used', 'Fill MANUAL AGA', 20, 1, NULL, NULL),
(44, 'machine_used', 'Fill MANUAL TAMARU', 21, 1, NULL, NULL),
(45, 'machine_used', 'Shrink STEAM', 22, 1, NULL, NULL),
(46, 'machine_used', 'Shrink TUNEL', 23, 1, NULL, NULL),
(47, 'machine_used', 'Shrink HOTAIR', 24, 1, NULL, NULL),
(48, 'machine_used', 'Coding INK JET PRINTER 40', 25, 1, NULL, NULL),
(49, 'machine_used', 'Coding INK JET PRINTER 65', 26, 1, NULL, NULL),
(50, 'machine_used', 'Coding LASER JET', 27, 1, NULL, NULL),
(51, 'machine_used', 'Label PACK LEADER', 28, 1, NULL, NULL),
(52, 'machine_used', 'Label TAMARU', 29, 1, NULL, NULL),
(53, 'machine_used', 'Label AGA', 30, 1, NULL, NULL),
(54, 'machine_used', 'Vision System', 31, 1, NULL, NULL),
(55, 'machine_used', 'Cutter AUTOMATIC-SHRINK', 32, 1, NULL, NULL),
(56, 'attachment_category', 'Vacuum Test', 1, 1, NULL, NULL),
(57, 'attachment_category', 'Press Test', 2, 1, NULL, NULL),
(58, 'attachment_category', 'Air Pressure Test', 3, 1, NULL, NULL),
(59, 'attachment_category', 'Filling Process', 4, 1, NULL, NULL),
(60, 'attachment_category', 'Line Configuration', 5, 1, NULL, NULL),
(61, 'attachment_category', 'Final Product', 6, 1, NULL, NULL),
(62, 'attachment_category', 'Machine Setting', 7, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `role_target` varchar(50) DEFAULT NULL,
  `department_target` varchar(50) DEFAULT NULL,
  `trial_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(40) NOT NULL DEFAULT 'info',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `removed_by_user` tinyint(1) NOT NULL DEFAULT 0,
  `removed_at` datetime DEFAULT NULL,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `role_target`, `department_target`, `trial_id`, `title`, `message`, `type`, `is_read`, `removed_by_user`, `removed_at`, `read_at`, `created_at`) VALUES
(1, 3, 'Reviewer', 'PRD', 7, 'New Trial Waiting for Review', 'Trial TRIAL-20260507-095357 - Sample Tube Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-08 10:06:41'),
(2, 10, 'Reviewer', 'PRD', 7, 'New Trial Waiting for Review', 'Trial TRIAL-20260507-095357 - Sample Tube Product membutuhkan review department Anda.', 'review', 1, 0, NULL, '2026-05-08 10:53:04', '2026-05-08 10:06:41'),
(3, 5, 'Reviewer', 'QAC', 7, 'New Trial Waiting for Review', 'Trial TRIAL-20260507-095357 - Sample Tube Product membutuhkan review department Anda.', 'review', 1, 0, NULL, '2026-05-08 11:18:03', '2026-05-08 10:06:41'),
(4, 8, 'Reviewer', 'QAC', 7, 'New Trial Waiting for Review', 'Trial TRIAL-20260507-095357 - Sample Tube Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-08 10:06:41'),
(5, 6, 'Reviewer', 'PRNI', 7, 'New Trial Waiting for Review', 'Trial TRIAL-20260507-095357 - Sample Tube Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-08 10:06:41'),
(6, 1, 'Admin', NULL, 7, 'Trial Submitted for Review', 'Trial TRIAL-20260507-095357 - Sample Tube Product dikirim ke review department: PRD, QAC, PRNI.', 'info', 0, 0, NULL, NULL, '2026-05-08 10:06:41'),
(7, 9, 'Admin', NULL, 7, 'Trial Submitted for Review', 'Trial TRIAL-20260507-095357 - Sample Tube Product dikirim ke review department: PRD, QAC, PRNI.', 'info', 1, 0, NULL, '2026-05-08 10:51:45', '2026-05-08 10:06:41'),
(8, 4, 'Reviewer', 'RNI', 5, 'New Trial Waiting for Review', 'Trial TRIAL-20260507-045125 - Sample Hotpouring Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-08 10:52:33'),
(9, 11, 'Reviewer', 'RNI', 5, 'New Trial Waiting for Review', 'Trial TRIAL-20260507-045125 - Sample Hotpouring Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-08 10:52:33'),
(10, 5, 'Reviewer', 'QAC', 5, 'New Trial Waiting for Review', 'Trial TRIAL-20260507-045125 - Sample Hotpouring Product membutuhkan review department Anda.', 'review', 1, 0, NULL, '2026-05-08 11:18:26', '2026-05-08 10:52:33'),
(11, 8, 'Reviewer', 'QAC', 5, 'New Trial Waiting for Review', 'Trial TRIAL-20260507-045125 - Sample Hotpouring Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-08 10:52:33'),
(12, 6, 'Reviewer', 'PRNI', 5, 'New Trial Waiting for Review', 'Trial TRIAL-20260507-045125 - Sample Hotpouring Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-08 10:52:33'),
(13, 1, 'Admin', NULL, 5, 'Trial Submitted for Review', 'Trial TRIAL-20260507-045125 - Sample Hotpouring Product dikirim ke review department: RNI, QAC, PRNI.', 'info', 0, 0, NULL, NULL, '2026-05-08 10:52:33'),
(14, 9, 'Admin', NULL, 5, 'Trial Submitted for Review', 'Trial TRIAL-20260507-045125 - Sample Hotpouring Product dikirim ke review department: RNI, QAC, PRNI.', 'info', 1, 0, NULL, '2026-05-08 11:16:48', '2026-05-08 10:52:33'),
(15, 3, 'Reviewer', 'PRD', 9, 'New Trial Waiting for Review', 'Trial TRIAL-20260508-110631 - Sample Hotpouring Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-08 16:08:35'),
(16, 10, 'Reviewer', 'PRD', 9, 'New Trial Waiting for Review', 'Trial TRIAL-20260508-110631 - Sample Hotpouring Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-08 16:08:35'),
(17, 4, 'Reviewer', 'RNI', 9, 'New Trial Waiting for Review', 'Trial TRIAL-20260508-110631 - Sample Hotpouring Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-08 16:08:35'),
(18, 11, 'Reviewer', 'RNI', 9, 'New Trial Waiting for Review', 'Trial TRIAL-20260508-110631 - Sample Hotpouring Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-08 16:08:35'),
(19, 5, 'Reviewer', 'QAC', 9, 'New Trial Waiting for Review', 'Trial TRIAL-20260508-110631 - Sample Hotpouring Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-08 16:08:35'),
(20, 8, 'Reviewer', 'QAC', 9, 'New Trial Waiting for Review', 'Trial TRIAL-20260508-110631 - Sample Hotpouring Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-08 16:08:35'),
(21, 6, 'Reviewer', 'PRNI', 9, 'New Trial Waiting for Review', 'Trial TRIAL-20260508-110631 - Sample Hotpouring Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-08 16:08:35'),
(22, 7, 'Reviewer', 'PI', 9, 'New Trial Waiting for Review', 'Trial TRIAL-20260508-110631 - Sample Hotpouring Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-08 16:08:35'),
(23, 1, 'Admin', NULL, 9, 'Trial Submitted for Review', 'Trial TRIAL-20260508-110631 - Sample Hotpouring Product dikirim ke review department: PRD, RNI, QAC, PRNI, PI.', 'info', 0, 0, NULL, NULL, '2026-05-08 16:08:35'),
(24, 9, 'Admin', NULL, 9, 'Trial Submitted for Review', 'Trial TRIAL-20260508-110631 - Sample Hotpouring Product dikirim ke review department: PRD, RNI, QAC, PRNI, PI.', 'info', 0, 0, NULL, NULL, '2026-05-08 16:08:35'),
(25, 8, 'Manager QAC', NULL, 5, 'Trial Waiting Final Approval', 'Trial TRIAL-20260507-045125 - Sample Hotpouring Product sudah selesai direview dan menunggu final approval.', 'approval', 0, 0, NULL, NULL, '2026-05-08 16:21:00'),
(26, 1, 'Admin', NULL, 5, 'Trial Waiting Final Approval', 'Trial TRIAL-20260507-045125 - Sample Hotpouring Product sudah selesai direview dan menunggu final approval.', 'approval', 0, 0, NULL, NULL, '2026-05-08 16:21:00'),
(27, 9, 'Admin', NULL, 5, 'Trial Waiting Final Approval', 'Trial TRIAL-20260507-045125 - Sample Hotpouring Product sudah selesai direview dan menunggu final approval.', 'approval', 0, 0, NULL, NULL, '2026-05-08 16:21:00'),
(28, 8, 'Manager QAC', NULL, 6, 'Trial Waiting Final Approval', 'Trial TRIAL-20260507-090448 - Sample Hotpouring Product sudah selesai direview dan menunggu final approval.', 'approval', 0, 0, NULL, NULL, '2026-05-08 16:21:06'),
(29, 1, 'Admin', NULL, 6, 'Trial Waiting Final Approval', 'Trial TRIAL-20260507-090448 - Sample Hotpouring Product sudah selesai direview dan menunggu final approval.', 'approval', 0, 0, NULL, NULL, '2026-05-08 16:21:06'),
(30, 9, 'Admin', NULL, 6, 'Trial Waiting Final Approval', 'Trial TRIAL-20260507-090448 - Sample Hotpouring Product sudah selesai direview dan menunggu final approval.', 'approval', 0, 0, NULL, NULL, '2026-05-08 16:21:06'),
(31, 8, 'Manager QAC', NULL, 3, 'Trial Waiting Final Approval', 'Trial TRIAL-20260506-174415 - Sample Tube Product sudah selesai direview dan menunggu final approval.', 'approval', 0, 0, NULL, NULL, '2026-05-08 16:21:09'),
(32, 1, 'Admin', NULL, 3, 'Trial Waiting Final Approval', 'Trial TRIAL-20260506-174415 - Sample Tube Product sudah selesai direview dan menunggu final approval.', 'approval', 0, 0, NULL, NULL, '2026-05-08 16:21:09'),
(33, 9, 'Admin', NULL, 3, 'Trial Waiting Final Approval', 'Trial TRIAL-20260506-174415 - Sample Tube Product sudah selesai direview dan menunggu final approval.', 'approval', 0, 0, NULL, NULL, '2026-05-08 16:21:09'),
(34, 9, 'Staff', NULL, 3, 'Trial Approved', 'Trial TRIAL-20260506-174415 - Sample Tube Product sudah approved oleh Manager QAC.', 'approved', 0, 0, NULL, NULL, '2026-05-08 16:21:55'),
(35, 1, 'Admin', NULL, 3, 'Trial Approved', 'Trial TRIAL-20260506-174415 - Sample Tube Product sudah approved oleh Manager QAC.', 'approved', 0, 0, NULL, NULL, '2026-05-08 16:21:55'),
(36, 9, 'Admin', NULL, 3, 'Trial Approved', 'Trial TRIAL-20260506-174415 - Sample Tube Product sudah approved oleh Manager QAC.', 'approved', 0, 0, NULL, NULL, '2026-05-08 16:21:55'),
(37, 9, 'Staff', NULL, 6, 'Trial Approved', 'Trial TRIAL-20260507-090448 - Sample Hotpouring Product sudah approved oleh Manager QAC.', 'approved', 0, 0, NULL, NULL, '2026-05-08 16:22:04'),
(38, 1, 'Admin', NULL, 6, 'Trial Approved', 'Trial TRIAL-20260507-090448 - Sample Hotpouring Product sudah approved oleh Manager QAC.', 'approved', 0, 0, NULL, NULL, '2026-05-08 16:22:04'),
(39, 9, 'Admin', NULL, 6, 'Trial Approved', 'Trial TRIAL-20260507-090448 - Sample Hotpouring Product sudah approved oleh Manager QAC.', 'approved', 0, 0, NULL, NULL, '2026-05-08 16:22:04'),
(40, 9, 'Staff', NULL, 5, 'Trial Rejected / Need Revision', 'Trial TRIAL-20260507-045125 - Sample Hotpouring Product ditolak dan perlu revisi.', 'rejected', 0, 0, NULL, NULL, '2026-05-08 16:22:14'),
(41, 9, 'Staff', NULL, 5, 'Trial Need Revision', 'Trial TRIAL-20260507-045125 membutuhkan revisi.', 'revision', 0, 0, NULL, NULL, '2026-05-08 16:22:14'),
(42, 1, 'Admin', NULL, 5, 'Trial Rejected / Need Revision', 'Trial TRIAL-20260507-045125 - Sample Hotpouring Product ditolak dan perlu revisi.', 'rejected', 0, 0, NULL, NULL, '2026-05-08 16:22:14'),
(43, 9, 'Admin', NULL, 5, 'Trial Rejected / Need Revision', 'Trial TRIAL-20260507-045125 - Sample Hotpouring Product ditolak dan perlu revisi.', 'rejected', 0, 0, NULL, NULL, '2026-05-08 16:22:14'),
(44, 3, 'Reviewer', 'PRD', 11, 'New Trial Waiting for Review', 'Trial TRIAL-20260520-142100 - Sample Tube Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-20 19:25:17'),
(45, 10, 'Reviewer', 'PRD', 11, 'New Trial Waiting for Review', 'Trial TRIAL-20260520-142100 - Sample Tube Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-20 19:25:17'),
(46, 5, 'Reviewer', 'QAC', 11, 'New Trial Waiting for Review', 'Trial TRIAL-20260520-142100 - Sample Tube Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-20 19:25:17'),
(47, 8, 'Reviewer', 'QAC', 11, 'New Trial Waiting for Review', 'Trial TRIAL-20260520-142100 - Sample Tube Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-20 19:25:17'),
(48, 6, 'Reviewer', 'PRNI', 11, 'New Trial Waiting for Review', 'Trial TRIAL-20260520-142100 - Sample Tube Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-20 19:25:17'),
(49, 1, 'Admin', NULL, 11, 'Trial Submitted for Review', 'Trial TRIAL-20260520-142100 - Sample Tube Product dikirim ke review department: PRD, QAC, PRNI.', 'info', 0, 0, NULL, NULL, '2026-05-20 19:25:17'),
(50, 9, 'Admin', NULL, 11, 'Trial Submitted for Review', 'Trial TRIAL-20260520-142100 - Sample Tube Product dikirim ke review department: PRD, QAC, PRNI.', 'info', 0, 0, NULL, NULL, '2026-05-20 19:25:17'),
(51, 3, 'Reviewer', 'PRD', 13, 'New Trial Waiting for Review', 'Trial TRIAL-20260521-034909 - Sample Tube Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-21 08:53:17'),
(52, 10, 'Reviewer', 'PRD', 13, 'New Trial Waiting for Review', 'Trial TRIAL-20260521-034909 - Sample Tube Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-21 08:53:17'),
(53, 5, 'Reviewer', 'QAC', 13, 'New Trial Waiting for Review', 'Trial TRIAL-20260521-034909 - Sample Tube Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-21 08:53:17'),
(54, 8, 'Reviewer', 'QAC', 13, 'New Trial Waiting for Review', 'Trial TRIAL-20260521-034909 - Sample Tube Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-21 08:53:17'),
(55, 6, 'Reviewer', 'PRNI', 13, 'New Trial Waiting for Review', 'Trial TRIAL-20260521-034909 - Sample Tube Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-21 08:53:17'),
(56, 7, 'Reviewer', 'PI', 13, 'New Trial Waiting for Review', 'Trial TRIAL-20260521-034909 - Sample Tube Product membutuhkan review department Anda.', 'review', 0, 0, NULL, NULL, '2026-05-21 08:53:17'),
(57, 1, 'Admin', NULL, 13, 'Trial Submitted for Review', 'Trial TRIAL-20260521-034909 - Sample Tube Product dikirim ke review department: PRD, QAC, PRNI, PI.', 'info', 0, 0, NULL, NULL, '2026-05-21 08:53:17'),
(58, 9, 'Admin', NULL, 13, 'Trial Submitted for Review', 'Trial TRIAL-20260521-034909 - Sample Tube Product dikirim ke review department: PRD, QAC, PRNI, PI.', 'info', 0, 0, NULL, NULL, '2026-05-21 08:53:17');

-- --------------------------------------------------------

--
-- Table structure for table `notification_user_status`
--

CREATE TABLE `notification_user_status` (
  `id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `is_removed` tinyint(1) NOT NULL DEFAULT 0,
  `removed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_user_status`
--

INSERT INTO `notification_user_status` (`id`, `notification_id`, `user_id`, `is_read`, `read_at`, `is_removed`, `removed_at`, `created_at`) VALUES
(1, 10, 5, 1, '2026-05-08 09:45:45', 0, NULL, '2026-05-08 14:45:45'),
(2, 14, 9, 1, '2026-05-08 10:17:08', 0, NULL, '2026-05-08 15:17:08'),
(3, 22, 7, 1, '2026-05-08 11:09:55', 0, NULL, '2026-05-08 16:09:55'),
(5, 24, 9, 1, '2026-05-08 11:15:05', 0, NULL, '2026-05-08 16:15:05'),
(7, 7, 9, 1, '2026-05-08 11:15:10', 0, NULL, '2026-05-08 16:15:10'),
(9, 17, 4, 1, '2026-05-08 11:18:45', 0, NULL, '2026-05-08 16:18:45'),
(10, 8, 4, 1, '2026-05-08 11:19:09', 0, NULL, '2026-05-08 16:19:09'),
(11, 19, 5, 1, '2026-05-08 11:20:14', 0, NULL, '2026-05-08 16:20:14'),
(12, 3, 5, 1, '2026-05-08 11:20:26', 1, '2026-05-08 11:20:26', '2026-05-08 16:20:26'),
(13, 21, 6, 1, '2026-05-08 11:21:15', 0, NULL, '2026-05-08 16:21:15'),
(14, 12, 6, 1, '2026-05-08 11:21:23', 0, NULL, '2026-05-08 16:21:23'),
(15, 5, 6, 1, '2026-05-08 11:21:24', 0, NULL, '2026-05-08 16:21:24'),
(16, 31, 8, 1, '2026-05-08 11:21:42', 0, NULL, '2026-05-08 16:21:42'),
(17, 28, 8, 1, '2026-05-08 11:22:19', 0, NULL, '2026-05-08 16:22:19'),
(18, 25, 8, 1, '2026-05-08 11:22:23', 0, NULL, '2026-05-08 16:22:23'),
(24, 20, 8, 1, '2026-05-08 11:22:48', 0, NULL, '2026-05-08 16:22:48'),
(25, 4, 8, 1, '2026-05-08 11:22:51', 0, NULL, '2026-05-08 16:22:51'),
(27, 11, 8, 1, '2026-05-08 11:23:21', 0, NULL, '2026-05-08 16:23:21'),
(29, 27, 9, 1, '2026-05-08 11:38:38', 0, NULL, '2026-05-08 16:38:38'),
(30, 30, 9, 1, '2026-05-08 11:38:38', 0, NULL, '2026-05-08 16:38:38'),
(31, 33, 9, 1, '2026-05-08 11:38:38', 0, NULL, '2026-05-08 16:38:38'),
(32, 34, 9, 1, '2026-05-08 11:38:38', 0, NULL, '2026-05-08 16:38:38'),
(33, 36, 9, 1, '2026-05-08 11:38:38', 0, NULL, '2026-05-08 16:38:38'),
(34, 37, 9, 1, '2026-05-08 11:38:38', 0, NULL, '2026-05-08 16:38:38'),
(35, 39, 9, 1, '2026-05-08 11:38:38', 0, NULL, '2026-05-08 16:38:38'),
(36, 40, 9, 1, '2026-05-08 11:38:38', 0, NULL, '2026-05-08 16:38:38'),
(37, 41, 9, 1, '2026-05-08 11:38:38', 0, NULL, '2026-05-08 16:38:38'),
(38, 43, 9, 1, '2026-05-08 11:38:38', 0, NULL, '2026-05-08 16:38:38'),
(39, 50, 9, 1, '2026-05-20 14:25:31', 0, NULL, '2026-05-20 19:25:31'),
(49, 54, 8, 1, '2026-05-21 04:04:12', 0, NULL, '2026-05-21 09:04:12'),
(50, 47, 8, 1, '2026-05-21 04:04:39', 0, NULL, '2026-05-21 09:04:39');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `finish_good_code` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `finish_good_code`, `is_active`, `deleted_at`, `deleted_by`) VALUES
(1, 'Sample Tube Product', 'FG-TUBE-001', 1, NULL, NULL),
(2, 'Sample Hotpouring Product', 'FG-HOT-001', 1, NULL, NULL),
(3, 'Sample Sachet Product', 'FG-SCT-001', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `trials_header`
--

CREATE TABLE `trials_header` (
  `id` int(11) NOT NULL,
  `trial_code` varchar(80) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(200) NOT NULL,
  `finish_good_code` varchar(100) NOT NULL,
  `product_type` varchar(100) NOT NULL,
  `validation_date` date NOT NULL,
  `validation_category` varchar(120) NOT NULL,
  `risk_level` varchar(20) NOT NULL,
  `validation_scope` varchar(200) NOT NULL,
  `machine_used` varchar(200) NOT NULL,
  `estimate_qty` decimal(14,2) NOT NULL,
  `batch_number` varchar(200) DEFAULT NULL,
  `bulk_code` varchar(200) DEFAULT NULL,
  `support_team` varchar(200) DEFAULT NULL,
  `initiated_person_team` varchar(200) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `bom` text DEFAULT NULL,
  `current_step` varchar(80) NOT NULL DEFAULT 'Validation',
  `progress_status` varchar(80) NOT NULL DEFAULT 'Draft',
  `pending_with` varchar(200) DEFAULT NULL,
  `final_decision` varchar(80) DEFAULT NULL,
  `revision_no` int(11) NOT NULL DEFAULT 0,
  `approved_by` varchar(150) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `rejected_by` varchar(150) DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `approval_comment` text DEFAULT NULL,
  `created_by` varchar(150) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trials_header`
--

INSERT INTO `trials_header` (`id`, `trial_code`, `product_id`, `product_name`, `finish_good_code`, `product_type`, `validation_date`, `validation_category`, `risk_level`, `validation_scope`, `machine_used`, `estimate_qty`, `batch_number`, `bulk_code`, `support_team`, `initiated_person_team`, `reason`, `bom`, `current_step`, `progress_status`, `pending_with`, `final_decision`, `revision_no`, `approved_by`, `approved_at`, `rejected_by`, `rejected_at`, `approval_comment`, `created_by`, `updated_at`, `created_at`, `deleted_at`, `deleted_by`) VALUES
(1, 'TRIAL-20260506-044614', 2, 'Sample Hotpouring Product', 'FG-HOT-001', 'Tube', '2026-05-06', 'New Product', 'Medium', 'Appearance', 'Fill JAR', 1.00, '3', '2434sdsdf ', '3432ssdfs', 's23423sdfsd', '23423sdfs', '23423 sdfsdf', 'Approval', 'Approved', '', 'Approved', 0, 'manager.qac@local.test', '2026-05-06 22:12:08', NULL, NULL, 'iya sudaah oke', 'admin@local.test', '2026-05-06 22:12:08', '2026-05-06 09:46:14', NULL, NULL),
(2, 'TRIAL-20260506-054921', 3, 'Sample Sachet Product', 'FG-SCT-001', 'Tube', '2026-05-06', 'New Product', 'Medium', 'Full Validation', 'Fill SINGLE HEAD', 11.00, NULL, NULL, NULL, NULL, NULL, NULL, 'Attachment', 'Draft', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'damor@cosmax.com', '2026-05-08 16:17:27', '2026-05-06 10:49:21', '2026-05-08 16:17:27', 9),
(3, 'TRIAL-20260506-174415', 1, 'Sample Tube Product', 'FG-TUBE-001', 'Tube', '2026-05-06', 'New Product', 'Low', 'Appearance', 'Fill JAR', 1211.00, '3sds', 'wdd123', 'sdas', 'wdsdfsd', 'sdfsdfd', 'sdfsdfs 2334 \r\ndfgdfdfgdf345 345345', 'Approval', 'Approved', '', 'Approved', 0, 'Manager QAC', '2026-05-08 16:21:55', NULL, NULL, 'sudah oke', 'damor@cosmax.com', '2026-05-08 16:21:55', '2026-05-06 22:44:15', NULL, NULL),
(4, 'TRIAL-20260507-045044', 2, 'Sample Hotpouring Product', 'FG-HOT-001', 'Cushion', '2026-05-07', 'New Product', 'Low', 'Appearance', 'Fill SINGLE HEAD', 11.00, '2sdf', 'sdfsd', 'sdfdsfs', 'fdgdfg', 'dfgdf', 'dfgdfg 23432', 'Validation', 'Draft', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'damor@cosmax.com', '2026-05-08 16:18:06', '2026-05-07 09:50:44', '2026-05-08 16:18:06', 9),
(5, 'TRIAL-20260507-045125', 2, 'Sample Hotpouring Product', 'FG-HOT-001', 'Bottle + Screw Cap', '2026-05-19', 'New Product', 'Low', 'Appearance', 'Fill SINGLE HEAD', 1334.00, '3', 'dfsd', 'sdfsd', 'dsfsdfs', 'sdfsdfd', 'sdfsdf 344355', 'Attachment', 'Need Revision', 'Staff', 'Need Revision', 1, NULL, NULL, 'Manager QAC', '2026-05-08 16:22:14', 'perlu di revis', 'damor@cosmax.com', '2026-05-20 19:26:54', '2026-05-07 09:51:25', '2026-05-20 19:26:54', 9),
(6, 'TRIAL-20260507-090448', 2, 'Sample Hotpouring Product', 'FG-HOT-001', 'Hotpouring', '2026-05-07', 'New Product', 'Low', 'Full Validation', 'Fill JIREH 1', 50.00, 'ABC123', '123ABCD', 'Hanbi', 'Fauzi', 'trial pertama ', '12312 kjsdfjsd\r\n21637 sdfhsdgs', 'Approval', 'Approved', '', 'Approved', 0, 'Manager QAC', '2026-05-08 16:22:04', NULL, NULL, 'oke bagus', 'damor@cosmax.com', '2026-05-08 16:22:04', '2026-05-07 14:04:48', NULL, NULL),
(7, 'TRIAL-20260507-095357', 1, 'Sample Tube Product', 'FG-TUBE-001', 'Tube', '2026-05-07', 'New Product', 'Low', 'Filling', 'Fill SINGLE HEAD', 12.00, 'asd', 'sdfsd', 'Damor', 'Fauzi', '23423sdfs', '6007395	SURCOSIL E-9(EVE VEGAN)(Z1)\r\n6028951	KF-6017(EVE VEGAN)\r\n6018464	UVINUL MC 80 (IDN)\r\n6010869	(SUNSCREEN AGENT)PARSOL EHS(EVE VEGAN)\r\n6011377	SF5600Z(EVE VEGAN)\r\n6000335	CEH(WAGLINOL 242)(RSPO)(EVE VEGAN)\r\n6018650	XIAMETER PMX-0345 (IDN)\r\n6017695	TRIMELA GEL SSA(EVE VEGAN)(Z1)\r\n6010021	CSMX 803(EVE VEGAN)(Z2)\r\n6009589	GELBASE B38(RSPO)(EVE VEGAN)\r\n4600559	GUE GUELE Bare Cushion-Ambré #PB\r\n6007204	SILICA L-51(EVE VEGAN)\r\n6001221	PURIFIED WATER(50031700)(EVE VEGAN)\r\n6000369	EDTA-2NA(Z3)(ZA)(EVE VEGAN)\r\n6001743	MG SULFATE.7H2O(EVE VEGAN)(Z2)(NO MORE USE)\r\n6026981	MASCEROL REFINED GLYCERINE 99.7% USP RSPO MB(RSPO)(COSMOS)\r\n6012764	MINACARE PENTIOL (HYDROLITE-5)(EVE VEGAN)(ZC)\r\n6010464	EUXYL PE9010(50056645)(EVE VEGAN)\r\n6018217	AMINO MOISTURE - C\r\n6024950	FASHIONABLE ROSE 2 E_1709212 (HALAL)(IDN)(DP)\r\n6007395	SURCOSIL E-9(EVE VEGAN)(Z1)\r\n6024785	DMC-10(Z2)(EVE VEGAN)\r\n6018464	UVINUL MC 80 (IDN)\r\n6010869	(SUNSCREEN AGENT)PARSOL EHS(EVE VEGAN)\r\n', 'Review', 'In Review', 'PRD', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'damor@cosmax.com', '2026-05-08 16:20:56', '2026-05-07 14:53:57', NULL, NULL),
(8, 'TRIAL-20260507-113541', 3, 'Sample Sachet Product', 'FG-SCT-001', 'Sachet', '2026-05-07', 'New Product', 'Low', 'Appearance', 'Fill SINGLE HEAD', 22.00, 'asd', 'sdfsd1223', 'Damor', 'Fauzi', 'sdfsdfd', '6011377	SF5600Z(EVE VEGAN)\r\n6000335	CEH(WAGLINOL 242)(RSPO)(EVE VEGAN)\r\n6018650	XIAMETER PMX-0345 (IDN)\r\n6036779	TRIMELA GEL SSA(EVE VEGAN)\r\n6010021	CSMX 803(EVE VEGAN)(Z2)\r\n6009589	GELBASE B38(RSPO)(EVE VEGAN)\r\n4600861	GUE GUELE Bare Cushion-Lumbre #PB\r\n6007204	SILICA L-51(EVE VEGAN)\r\n6001221	PURIFIED WATER(50031700)(EVE VEGAN)\r\n6018137	DISSOLVINENA2-S(Z1)(EVE VEGAN)\r\n6001743	MG SULFATE.7H2O(EVE VEGAN)(Z2)(NO MORE USE)\r\n6026981	MASCEROL REFINED GLYCERINE 99.7% USP RSPO MB(RSPO)(COSMOS)\r\n6012764	MINACARE PENTIOL (HYDROLITE-5)(EVE VEGAN)(ZC)\r\n6010464	EUXYL PE9010(50056645)(EVE VEGAN)\r\n6018217	AMINO MOISTURE - C\r\n6024950	FASHIONABLE ROSE 2 E_1709212 (HALAL)(IDN)(DP)\r\n6007395	SURCOSIL E-9(EVE VEGAN)(Z1)\r\n6028951	KF-6017(EVE VEGAN)\r\n6018464	UVINUL MC 80 (IDN)\r\n6010869	(SUNSCREEN AGENT)PARSOL EHS(EVE VEGAN)\r\n6011377	SF5600Z(EVE VEGAN)\r\n6000335	CEH(WAGLINOL 242)(RSPO)(EVE VEGAN)\r\n6018650	XIAMETER PMX-0345 (IDN)\r\n6009589	GELBASE B38(RSPO)(EVE VEGAN)\r\n6010021	CSMX 803(EVE VEGAN)(Z2)\r\n6017695	TRIMELA GEL SSA(EVE VEGAN)(Z1)\r\n4600557	GUE GUELE Bare Cushion-Maré #PB\r\n', 'Review', 'In Review', 'PRD', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'damor@cosmax.com', '2026-05-08 16:21:02', '2026-05-07 16:35:41', '2026-05-08 15:16:49', 9),
(9, 'TRIAL-20260508-110631', 2, 'Sample Hotpouring Product', 'FG-HOT-001', 'Tube', '2026-05-08', 'New Product', 'Low', 'Appearance', 'Fill JAR', 222.00, '2sdf', 'asd123', 'Damor', 'Fauzi', 'sdfsdfd', '6007204	SILICA L-51(EVE VEGAN)\r\n6001221	PURIFIED WATER(50031700)(EVE VEGAN)\r\n6000369	EDTA-2NA(Z3)(ZA)(EVE VEGAN)\r\n6001743	MG SULFATE.7H2O(EVE VEGAN)(Z2)(NO MORE USE)\r\n6026981	MASCEROL REFINED GLYCERINE 99.7% USP RSPO MB(RSPO)(COSMOS)\r\n6012764	MINACARE PENTIOL (HYDROLITE-5)(EVE VEGAN)(ZC)\r\n6010464	EUXYL PE9010(50056645)(EVE VEGAN)\r\n6018217	AMINO MOISTURE - C\r\n6024950	FASHIONABLE ROSE 2 E_1709212 (HALAL)(IDN)(DP)\r\n6007395	SURCOSIL E-9(EVE VEGAN)(Z1)\r\n6028951	KF-6017(EVE VEGAN)\r\n6018464	UVINUL MC 80 (IDN)\r\n6010869	(SUNSCREEN AGENT)PARSOL EHS(EVE VEGAN)\r\n6011377	SF5600Z(EVE VEGAN)\r\n6000335	CEH(WAGLINOL 242)(RSPO)(EVE VEGAN)\r\n6018650	XIAMETER PMX-0345 (IDN)\r\n6009589	GELBASE B38(RSPO)(EVE VEGAN)\r\n6010021	CSMX 803(EVE VEGAN)(Z2)\r\n6017695	TRIMELA GEL SSA(EVE VEGAN)(Z1)\r\n4600558	GUE GUELE Bare Cushion-Sandré #PB\r\n6007204	SILICA L-51(EVE VEGAN)\r\n6001221	PURIFIED WATER(50031700)(EVE VEGAN)\r\n6000369	EDTA-2NA(Z3)(ZA)(EVE VEGAN)\r\n6001743	MG SULFATE.7H2O(EVE VEGAN)(Z2)(NO MORE USE)\r\n6026981	MASCEROL REFINED GLYCERINE 99.7% USP RSPO MB(RSPO)(COSMOS)\r\n6012764	MINACARE PENTIOL (HYDROLITE-5)(EVE VEGAN)(ZC)\r\n6010464	EUXYL PE9010(50056645)(EVE VEGAN)\r\n', 'Review', 'In Review', 'PI,PRD', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'lomoniroha@cosmax.com', '2026-05-08 16:20:53', '2026-05-08 16:06:31', NULL, NULL),
(10, 'TRIAL-20260508-114120', 2, 'Sample Hotpouring Product', 'FG-HOT-001', 'Tube', '2026-05-08', 'New Product', 'Medium', 'Filling', 'Fill TUBE SC 10', 50.00, 'abc', '123456', 'Damor', 'heri', 'unik pacvkaging', '6007204	SILICA L-51(EVE VEGAN)\r\n6001221	PURIFIED WATER(50031700)(EVE VEGAN)\r\n6000369	EDTA-2NA(Z3)(ZA)(EVE VEGAN)\r\n6001743	MG SULFATE.7H2O(EVE VEGAN)(Z2)(NO MORE USE)\r\n6026981	MASCEROL REFINED GLYCERINE 99.7% USP RSPO MB(RSPO)(COSMOS)\r\n6012764	MINACARE PENTIOL (HYDROLITE-5)(EVE VEGAN)(ZC)\r\n6010464	EUXYL PE9010(50056645)(EVE VEGAN)\r\n6018217	AMINO MOISTURE - C\r\n6024950	FASHIONABLE ROSE 2 E_1709212 (HALAL)(IDN)(DP)\r\n6007395	SURCOSIL E-9(EVE VEGAN)(Z1)\r\n6028951	KF-6017(EVE VEGAN)\r\n6018464	UVINUL MC 80 (IDN)\r\n6010869	(SUNSCREEN AGENT)PARSOL EHS(EVE VEGAN)\r\n6011377	SF5600Z(EVE VEGAN)\r\n6000335	CEH(WAGLINOL 242)(RSPO)(EVE VEGAN)\r\n6018650	XIAMETER PMX-0345 (IDN)\r\n6009589	GELBASE B38(RSPO)(EVE VEGAN)\r\n6010021	CSMX 803(EVE VEGAN)(Z2)\r\n6017695	TRIMELA GEL SSA(EVE VEGAN)(Z1)\r\n4600558	GUE GUELE Bare Cushion-Sandré #PB\r\n6007204	SILICA L-51(EVE VEGAN)\r\n6001221	PURIFIED WATER(50031700)(EVE VEGAN)\r\n6000369	EDTA-2NA(Z3)(ZA)(EVE VEGAN)\r\n6001743	MG SULFATE.7H2O(EVE VEGAN)(Z2)(NO MORE USE)\r\n6026981	MASCEROL REFINED GLYCERINE 99.7% USP RSPO MB(RSPO)(COSMOS)\r\n6012764	MINACARE PENTIOL (HYDROLITE-5)(EVE VEGAN)(ZC)\r\n6010464	EUXYL PE9010(50056645)(EVE VEGAN)\r\n', 'Attachment', 'Draft', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'fauzi@cosmax.com', '2026-05-08 16:51:10', '2026-05-08 16:41:20', NULL, NULL),
(11, 'TRIAL-20260520-142100', 1, 'Sample Tube Product', 'FG-TUBE-001', 'Tube', '2026-05-20', 'New Product', 'Low', '[\"Filling\",\"Packaging\"]', '[\"Fill JAR\",\"Fill SINGLE HEAD\"]', 22.00, '3ABCV', 'BVDF123', 'TESTER', 'DAMOR', 'TESS ', '234324 FDGDFGD\r\n322323 DFGDFGF', 'Review', 'In Review', 'PRD,PRNI', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'damor@cosmax.com', '2026-05-21 09:06:31', '2026-05-20 19:21:00', NULL, NULL),
(12, 'TRIAL-20260521-034844', 1, 'Sample Tube Product', 'FG-TUBE-001', 'Tube', '2026-05-21', 'New Product', 'Medium', '[\"Filling\",\"Packaging\"]', '[\"Fill TUBE SC 11\",\"Shrink STEAM\",\"Coding INK JET PRINTER 65\"]', 50.00, 'na', '1123', 'heri', 'fauzi', 'npd', '12 daugfdvas\r\n11 asdfas\r\n13 srfsr\r\n14 sfdrfws', 'Validation', 'Draft', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'staff@local.test', '2026-05-21 03:48:44', '2026-05-21 08:48:44', NULL, NULL),
(13, 'TRIAL-20260521-034909', 1, 'Sample Tube Product', 'FG-TUBE-001', 'Tube', '2026-05-21', 'New Product', 'Medium', '[\"Filling\",\"Packaging\"]', '[\"Fill TUBE SC 11\",\"Shrink STEAM\",\"Coding INK JET PRINTER 65\"]', 50.00, 'na', '1123', 'heri', 'fauzi', 'npd', '12 daugfdvas\r\n11 asdfas\r\n13 srfsr\r\n14 sfdrfws', 'Review', 'In Review', 'PI,PRD,PRNI', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'staff@local.test', '2026-05-21 09:06:23', '2026-05-21 08:49:09', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `trials_results`
--

CREATE TABLE `trials_results` (
  `trial_id` int(11) NOT NULL,
  `parameter_id` int(11) NOT NULL,
  `result_value` text DEFAULT NULL,
  `decision` varchar(20) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trials_results`
--

INSERT INTO `trials_results` (`trial_id`, `parameter_id`, `result_value`, `decision`, `remark`, `updated_at`) VALUES
(1, 1, 'Conform', 'NOT OK', 'sdfsd', '2026-05-06 11:09:14'),
(1, 2, 'Conform', 'OK', 'sdfsd', '2026-05-06 11:09:14'),
(1, 3, 'N/A', 'N/A', 'sdf', '2026-05-06 11:09:14'),
(1, 4, 'Conform', 'OK', 'sdf', '2026-05-06 11:09:14'),
(2, 1, 'Conform', 'OK', '', '2026-05-06 21:35:03'),
(2, 2, 'Conform', 'OK', '', '2026-05-06 21:35:03'),
(2, 3, 'Conform', 'NOT OK', 'berak', '2026-05-06 21:35:03'),
(2, 4, 'Conform', 'OK', '', '2026-05-06 21:35:03'),
(3, 1, 'Conform', 'OK', '', '2026-05-06 22:44:20'),
(3, 2, 'Conform', 'OK', '', '2026-05-06 22:44:20'),
(3, 3, 'Conform', 'OK', '', '2026-05-06 22:44:20'),
(3, 4, 'Conform', 'OK', '', '2026-05-06 22:44:20'),
(5, 5, 'Conform', 'OK', 'dsfsdf', '2026-05-08 16:56:33'),
(5, 6, 'Conform', 'OK', 'sdfsd', '2026-05-08 16:56:33'),
(5, 7, 'Conform', 'OK', 'sdfsd', '2026-05-08 16:56:33'),
(6, 5, 'Conform', 'OK', 'oke', '2026-05-07 14:05:47'),
(6, 6, 'Over  Flow', 'NOT OK', 'Not Oke harus di turunkan volume', '2026-05-07 14:05:47'),
(6, 7, 'Conform', 'OK', '', '2026-05-07 14:05:47'),
(7, 1, 'Conform', 'OK', '', '2026-05-08 10:04:43'),
(7, 2, 'Conform', 'OK', '', '2026-05-08 10:04:43'),
(7, 3, 'Conform', 'OK', '', '2026-05-08 10:04:43'),
(7, 4, 'Conform', 'OK', '', '2026-05-08 10:04:43'),
(8, 8, 'Conform', 'OK', '', '2026-05-07 16:36:04'),
(8, 9, 'Conform', 'OK', '', '2026-05-07 16:36:04'),
(9, 1, 'Conform', 'OK', '', '2026-05-08 16:06:33'),
(9, 2, 'Conform', 'OK', '', '2026-05-08 16:06:33'),
(9, 3, 'Conform', 'OK', '', '2026-05-08 16:06:33'),
(9, 4, 'Conform', 'OK', '', '2026-05-08 16:06:33'),
(10, 1, 'Conform', 'OK', 'ok', '2026-05-08 16:51:08'),
(10, 2, 'erase', 'NOT OK', 'rer-trial', '2026-05-08 16:51:08'),
(10, 3, 'Conform', 'OK', 'ok', '2026-05-08 16:51:08'),
(10, 4, 'Conform', 'OK', '100-101g', '2026-05-08 16:51:08'),
(11, 1, 'Conform', 'OK', 'OK', '2026-05-20 19:24:53'),
(11, 2, 'Conform', 'OK', 'OK', '2026-05-20 19:24:53'),
(11, 3, 'Conform', 'OK', 'OK', '2026-05-20 19:24:53'),
(11, 4, 'Conform', 'OK', 'FILL 40-50GR', '2026-05-20 19:24:53'),
(13, 1, 'Conform', 'OK', '', '2026-05-21 08:50:15'),
(13, 2, 'terhapus', 'NOT OK', 'shrink terhapus', '2026-05-21 08:50:15'),
(13, 3, 'kerut', 'NOT OK', 'harus pakai steam shrink', '2026-05-21 08:50:15'),
(13, 4, 'Conform', 'OK', '', '2026-05-21 08:50:15');

-- --------------------------------------------------------

--
-- Table structure for table `trials_review`
--

CREATE TABLE `trials_review` (
  `id` int(11) NOT NULL,
  `trial_id` int(11) NOT NULL,
  `department` varchar(50) NOT NULL,
  `review_round` int(11) NOT NULL DEFAULT 1,
  `status` varchar(30) NOT NULL DEFAULT 'Pending',
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `reviewer_name` varchar(120) DEFAULT NULL,
  `reviewer_email` varchar(150) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trials_review`
--

INSERT INTO `trials_review` (`id`, `trial_id`, `department`, `review_round`, `status`, `is_required`, `reviewer_name`, `reviewer_email`, `comment`, `reviewed_at`) VALUES
(1, 1, 'PRD', 1, 'Reviewed', 1, 'Reviewer PRD', 'prd@local.test', 'Oke sudah bagus', '2026-05-06 11:15:58'),
(2, 1, 'RNI', 1, 'Reviewed', 1, 'Reviewer RNI', 'rni@local.test', 'oke', '2026-05-06 22:10:43'),
(3, 1, 'QAC', 1, 'Reviewed', 1, 'Reviewer QAC', 'qac@local.test', 'siplah', '2026-05-06 22:09:51'),
(4, 1, 'PRNI', 1, 'Reviewed', 1, 'Reviewer PRNI', 'prni@local.test', 'bagus', '2026-05-06 22:10:20'),
(5, 1, 'PI', 1, 'Reviewed', 1, 'Reviewer PI', 'pi@local.test', 'ok ya', '2026-05-06 22:09:09'),
(6, 3, 'PRD', 1, 'Reviewed', 1, 'Dian', 'dian.andrian@cosmax.com', 'sip lah', '2026-05-06 22:48:04'),
(7, 3, 'RNI', 1, 'Reviewed', 1, 'Reviewer RNI', 'rni@local.test', 'yes', '2026-05-08 16:18:50'),
(8, 3, 'QAC', 1, 'Reviewed', 1, 'Reviewer QAC', 'qac@local.test', 'ok banget', '2026-05-08 16:20:08'),
(9, 3, 'PRNI', 1, 'Reviewed', 1, 'Reviewer PRNI', 'prni@local.test', 'kelazz', '2026-05-08 16:21:09'),
(10, 3, 'PI', 1, 'Reviewed', 1, 'Reviewer PI', 'pi@local.test', 'mantap', '2026-05-07 16:39:06'),
(11, 6, 'PRD', 1, 'Reviewed', 1, 'Dian', 'dian.andrian@cosmax.com', 'oke sudah bagus', '2026-05-07 14:16:18'),
(12, 6, 'RNI', 1, 'Reviewed', 1, 'Reviewer RNI', 'rni@local.test', 'yes', '2026-05-08 16:18:55'),
(13, 6, 'QAC', 1, 'Reviewed', 1, 'Reviewer QAC', 'qac@local.test', 'oke', '2026-05-08 16:20:04'),
(14, 6, 'PRNI', 1, 'Reviewed', 1, 'Reviewer PRNI', 'prni@local.test', 'mantap', '2026-05-08 16:21:06'),
(15, 6, 'PI', 1, 'Reviewed', 1, 'Reviewer PI', 'pi@local.test', 'siap', '2026-05-07 16:39:00'),
(16, 8, 'PRD', 1, 'Pending', 1, NULL, NULL, NULL, NULL),
(17, 8, 'RNI', 1, 'Reviewed', 1, 'Reviewer RNI', 'rni@local.test', 'yes', '2026-05-08 16:18:53'),
(18, 8, 'QAC', 1, 'Reviewed', 1, 'Reviewer QAC', 'qac@local.test', 'bagus', '2026-05-08 16:20:12'),
(19, 8, 'PRNI', 1, 'Reviewed', 1, 'Reviewer PRNI', 'prni@local.test', 'good', '2026-05-08 16:21:02'),
(20, 8, 'PI', 1, 'Reviewed', 1, 'Reviewer PI', 'pi@local.test', 'oke sih', '2026-05-07 16:38:49'),
(21, 7, 'PRD', 1, 'Pending', 1, NULL, NULL, NULL, NULL),
(22, 7, 'QAC', 1, 'Reviewed', 1, 'Reviewer QAC', 'qac@local.test', 'oke', '2026-05-08 16:20:01'),
(23, 7, 'PRNI', 1, 'Reviewed', 1, 'Reviewer PRNI', 'prni@local.test', 'ok', '2026-05-08 16:20:56'),
(24, 5, 'RNI', 1, 'Reviewed', 1, 'Reviewer RNI', 'rni@local.test', 'yes bagus', '2026-05-08 16:19:01'),
(25, 5, 'QAC', 1, 'Reviewed', 1, 'Reviewer QAC', 'qac@local.test', 'oke', '2026-05-08 16:19:57'),
(26, 5, 'PRNI', 1, 'Reviewed', 1, 'Reviewer PRNI', 'prni@local.test', 'bagus', '2026-05-08 16:21:00'),
(27, 9, 'PRD', 1, 'Pending', 1, NULL, NULL, NULL, NULL),
(28, 9, 'RNI', 1, 'Reviewed', 1, 'Reviewer RNI', 'rni@local.test', 'sudah oke', '2026-05-08 16:19:06'),
(29, 9, 'QAC', 1, 'Reviewed', 1, 'Reviewer QAC', 'qac@local.test', 'ok', '2026-05-08 16:19:51'),
(30, 9, 'PRNI', 1, 'Reviewed', 1, 'Reviewer PRNI', 'prni@local.test', 'oke', '2026-05-08 16:20:53'),
(31, 9, 'PI', 1, 'Pending', 1, NULL, NULL, NULL, NULL),
(32, 11, 'PRD', 1, 'Pending', 1, NULL, NULL, NULL, NULL),
(33, 11, 'QAC', 1, 'Reviewed', 1, 'Manager QAC', 'manager.qac@local.test', 'ok sudah bagus', '2026-05-21 09:06:31'),
(34, 11, 'PRNI', 1, 'Pending', 1, NULL, NULL, NULL, NULL),
(35, 13, 'PRD', 1, 'Pending', 1, NULL, NULL, NULL, NULL),
(36, 13, 'QAC', 1, 'Reviewed', 1, 'Manager QAC', 'manager.qac@local.test', 'Ok sudah bagus', '2026-05-21 09:06:23'),
(37, 13, 'PRNI', 1, 'Pending', 1, NULL, NULL, NULL, NULL),
(38, 13, 'PI', 1, 'Pending', 1, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `trials_weighing`
--

CREATE TABLE `trials_weighing` (
  `id` int(11) NOT NULL,
  `trial_id` int(11) NOT NULL,
  `section` varchar(30) NOT NULL,
  `item_no` int(11) NOT NULL,
  `weight_value` decimal(12,3) DEFAULT NULL,
  `is_skipped` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trials_weighing`
--

INSERT INTO `trials_weighing` (`id`, `trial_id`, `section`, `item_no`, `weight_value`, `is_skipped`, `created_at`) VALUES
(301, 1, 'Packaging', 1, 1.000, 0, '2026-05-06 04:09:15'),
(302, 1, 'Packaging', 2, 2.000, 0, '2026-05-06 04:09:15'),
(303, 1, 'Packaging', 3, 2.000, 0, '2026-05-06 04:09:15'),
(304, 1, 'Packaging', 4, 34.000, 0, '2026-05-06 04:09:15'),
(305, 1, 'Packaging', 5, 5.000, 0, '2026-05-06 04:09:15'),
(306, 1, 'Packaging', 6, 4.000, 0, '2026-05-06 04:09:15'),
(307, 1, 'Packaging', 7, 3.000, 0, '2026-05-06 04:09:15'),
(308, 1, 'Packaging', 8, 4.000, 0, '2026-05-06 04:09:15'),
(309, 1, 'Packaging', 9, 3.000, 0, '2026-05-06 04:09:15'),
(310, 1, 'Packaging', 10, 2.000, 0, '2026-05-06 04:09:15'),
(311, 1, 'Packaging', 11, 1.000, 0, '2026-05-06 04:09:15'),
(312, 1, 'Packaging', 12, 2.000, 0, '2026-05-06 04:09:15'),
(313, 1, 'Packaging', 13, 3.000, 0, '2026-05-06 04:09:15'),
(314, 1, 'Packaging', 14, 4.000, 0, '2026-05-06 04:09:15'),
(315, 1, 'Packaging', 15, 5.000, 0, '2026-05-06 04:09:15'),
(316, 1, 'Packaging', 16, 5.000, 0, '2026-05-06 04:09:15'),
(317, 1, 'Packaging', 17, 5.000, 0, '2026-05-06 04:09:15'),
(318, 1, 'Packaging', 18, 6.000, 0, '2026-05-06 04:09:15'),
(319, 1, 'Packaging', 19, 5.000, 0, '2026-05-06 04:09:15'),
(320, 1, 'Packaging', 20, 443.000, 0, '2026-05-06 04:09:15'),
(321, 1, 'Packaging', 21, 33.000, 0, '2026-05-06 04:09:15'),
(322, 1, 'Packaging', 22, 44.000, 0, '2026-05-06 04:09:15'),
(323, 1, 'Packaging', 23, 33.000, 0, '2026-05-06 04:09:15'),
(324, 1, 'Packaging', 24, 33.000, 0, '2026-05-06 04:09:15'),
(325, 1, 'Packaging', 25, 334.000, 0, '2026-05-06 04:09:15'),
(326, 1, 'Packaging', 26, 44.000, 0, '2026-05-06 04:09:15'),
(327, 1, 'Packaging', 27, 22.000, 0, '2026-05-06 04:09:15'),
(328, 1, 'Packaging', 28, 11.000, 0, '2026-05-06 04:09:15'),
(329, 1, 'Packaging', 29, 11.000, 0, '2026-05-06 04:09:15'),
(330, 1, 'Packaging', 30, 11.000, 0, '2026-05-06 04:09:15'),
(331, 1, 'Filling', 1, 23.000, 0, '2026-05-06 04:09:16'),
(332, 1, 'Filling', 2, 43.000, 0, '2026-05-06 04:09:16'),
(333, 1, 'Filling', 3, 43.000, 0, '2026-05-06 04:09:16'),
(334, 1, 'Filling', 4, 4.000, 0, '2026-05-06 04:09:16'),
(335, 1, 'Filling', 5, 44.000, 0, '2026-05-06 04:09:16'),
(336, 1, 'Filling', 6, 45.000, 0, '2026-05-06 04:09:16'),
(337, 1, 'Filling', 7, 4543.000, 0, '2026-05-06 04:09:16'),
(338, 1, 'Filling', 8, 544.000, 0, '2026-05-06 04:09:16'),
(339, 1, 'Filling', 9, 334.000, 0, '2026-05-06 04:09:16'),
(340, 1, 'Filling', 10, 45.000, 0, '2026-05-06 04:09:16'),
(341, 1, 'Filling', 11, 34.000, 0, '2026-05-06 04:09:16'),
(342, 1, 'Filling', 12, 345.000, 0, '2026-05-06 04:09:16'),
(343, 1, 'Filling', 13, 345.000, 0, '2026-05-06 04:09:16'),
(344, 1, 'Filling', 14, 45.000, 0, '2026-05-06 04:09:16'),
(345, 1, 'Filling', 15, 445.000, 0, '2026-05-06 04:09:16'),
(346, 1, 'Filling', 16, 454.000, 0, '2026-05-06 04:09:16'),
(347, 1, 'Filling', 17, 454.000, 0, '2026-05-06 04:09:16'),
(348, 1, 'Filling', 18, 454.000, 0, '2026-05-06 04:09:16'),
(349, 1, 'Filling', 19, 5444.000, 0, '2026-05-06 04:09:16'),
(350, 1, 'Filling', 20, 454543.000, 0, '2026-05-06 04:09:16'),
(351, 1, 'Filling', 21, 343.000, 0, '2026-05-06 04:09:16'),
(352, 1, 'Filling', 22, 343.000, 0, '2026-05-06 04:09:16'),
(353, 1, 'Filling', 23, 3434.000, 0, '2026-05-06 04:09:16'),
(354, 1, 'Filling', 24, 343.000, 0, '2026-05-06 04:09:16'),
(355, 1, 'Filling', 25, 343.000, 0, '2026-05-06 04:09:16'),
(356, 1, 'Filling', 26, 343.000, 0, '2026-05-06 04:09:16'),
(357, 1, 'Filling', 27, 343.000, 0, '2026-05-06 04:09:16'),
(358, 1, 'Filling', 28, 343.000, 0, '2026-05-06 04:09:16'),
(359, 1, 'Filling', 29, 343.000, 0, '2026-05-06 04:09:16'),
(360, 1, 'Filling', 30, 3434.000, 0, '2026-05-06 04:09:16'),
(361, 2, 'Packaging', 1, 0.006, 0, '2026-05-06 14:35:04'),
(362, 2, 'Packaging', 2, 0.006, 0, '2026-05-06 14:35:04'),
(363, 2, 'Packaging', 3, 0.006, 0, '2026-05-06 14:35:04'),
(364, 2, 'Packaging', 4, 0.013, 0, '2026-05-06 14:35:04'),
(365, 2, 'Packaging', 5, 12113.000, 0, '2026-05-06 14:35:04'),
(366, 2, 'Packaging', 6, 12113.000, 0, '2026-05-06 14:35:04'),
(367, 2, 'Packaging', 7, 12113.000, 0, '2026-05-06 14:35:04'),
(368, 2, 'Packaging', 8, 12113.000, 0, '2026-05-06 14:35:04'),
(369, 2, 'Packaging', 9, 12113.000, 0, '2026-05-06 14:35:04'),
(370, 2, 'Packaging', 10, 12113.000, 0, '2026-05-06 14:35:04'),
(371, 2, 'Packaging', 11, 12113.000, 0, '2026-05-06 14:35:04'),
(372, 2, 'Packaging', 12, 12113.000, 0, '2026-05-06 14:35:04'),
(373, 2, 'Packaging', 13, 12113.000, 0, '2026-05-06 14:35:04'),
(374, 2, 'Packaging', 14, 12113.000, 0, '2026-05-06 14:35:04'),
(375, 2, 'Packaging', 15, 12113.000, 0, '2026-05-06 14:35:04'),
(376, 2, 'Packaging', 16, 12113.000, 0, '2026-05-06 14:35:04'),
(377, 2, 'Packaging', 17, 12113.000, 0, '2026-05-06 14:35:04'),
(378, 2, 'Packaging', 18, 12113.000, 0, '2026-05-06 14:35:04'),
(379, 2, 'Packaging', 19, 12113.000, 0, '2026-05-06 14:35:04'),
(380, 2, 'Packaging', 20, 12113.000, 0, '2026-05-06 14:35:04'),
(381, 2, 'Packaging', 21, 0.000, 0, '2026-05-06 14:35:04'),
(382, 2, 'Packaging', 22, 0.000, 0, '2026-05-06 14:35:04'),
(383, 2, 'Packaging', 23, 0.000, 0, '2026-05-06 14:35:04'),
(384, 2, 'Packaging', 24, 0.000, 0, '2026-05-06 14:35:04'),
(385, 2, 'Packaging', 25, 7.000, 0, '2026-05-06 14:35:04'),
(386, 2, 'Packaging', 26, 12113.000, 0, '2026-05-06 14:35:04'),
(387, 2, 'Packaging', 27, 12113.000, 0, '2026-05-06 14:35:04'),
(388, 2, 'Packaging', 28, 12113.000, 0, '2026-05-06 14:35:04'),
(389, 2, 'Packaging', 29, 12113.000, 0, '2026-05-06 14:35:04'),
(390, 2, 'Packaging', 30, 12113.000, 0, '2026-05-06 14:35:04'),
(391, 2, 'Filling', 1, NULL, 1, '2026-05-06 14:35:05'),
(392, 2, 'Filling', 2, NULL, 1, '2026-05-06 14:35:05'),
(393, 2, 'Filling', 3, NULL, 1, '2026-05-06 14:35:05'),
(394, 2, 'Filling', 4, NULL, 1, '2026-05-06 14:35:05'),
(395, 2, 'Filling', 5, NULL, 1, '2026-05-06 14:35:05'),
(396, 2, 'Filling', 6, NULL, 1, '2026-05-06 14:35:05'),
(397, 2, 'Filling', 7, NULL, 1, '2026-05-06 14:35:05'),
(398, 2, 'Filling', 8, NULL, 1, '2026-05-06 14:35:05'),
(399, 2, 'Filling', 9, NULL, 1, '2026-05-06 14:35:05'),
(400, 2, 'Filling', 10, NULL, 1, '2026-05-06 14:35:05'),
(401, 2, 'Filling', 11, NULL, 1, '2026-05-06 14:35:05'),
(402, 2, 'Filling', 12, NULL, 1, '2026-05-06 14:35:05'),
(403, 2, 'Filling', 13, NULL, 1, '2026-05-06 14:35:05'),
(404, 2, 'Filling', 14, NULL, 1, '2026-05-06 14:35:05'),
(405, 2, 'Filling', 15, NULL, 1, '2026-05-06 14:35:05'),
(406, 2, 'Filling', 16, NULL, 1, '2026-05-06 14:35:05'),
(407, 2, 'Filling', 17, NULL, 1, '2026-05-06 14:35:05'),
(408, 2, 'Filling', 18, NULL, 1, '2026-05-06 14:35:05'),
(409, 2, 'Filling', 19, NULL, 1, '2026-05-06 14:35:05'),
(410, 2, 'Filling', 20, NULL, 1, '2026-05-06 14:35:05'),
(411, 2, 'Filling', 21, NULL, 1, '2026-05-06 14:35:05'),
(412, 2, 'Filling', 22, NULL, 1, '2026-05-06 14:35:05'),
(413, 2, 'Filling', 23, NULL, 1, '2026-05-06 14:35:05'),
(414, 2, 'Filling', 24, NULL, 1, '2026-05-06 14:35:05'),
(415, 2, 'Filling', 25, NULL, 1, '2026-05-06 14:35:05'),
(416, 2, 'Filling', 26, NULL, 1, '2026-05-06 14:35:05'),
(417, 2, 'Filling', 27, NULL, 1, '2026-05-06 14:35:05'),
(418, 2, 'Filling', 28, NULL, 1, '2026-05-06 14:35:05'),
(419, 2, 'Filling', 29, NULL, 1, '2026-05-06 14:35:05'),
(420, 2, 'Filling', 30, NULL, 1, '2026-05-06 14:35:05'),
(421, 3, 'Packaging', 1, NULL, 1, '2026-05-06 15:44:24'),
(422, 3, 'Packaging', 2, NULL, 1, '2026-05-06 15:44:24'),
(423, 3, 'Packaging', 3, NULL, 1, '2026-05-06 15:44:24'),
(424, 3, 'Packaging', 4, NULL, 1, '2026-05-06 15:44:24'),
(425, 3, 'Packaging', 5, NULL, 1, '2026-05-06 15:44:24'),
(426, 3, 'Packaging', 6, NULL, 1, '2026-05-06 15:44:24'),
(427, 3, 'Packaging', 7, NULL, 1, '2026-05-06 15:44:24'),
(428, 3, 'Packaging', 8, NULL, 1, '2026-05-06 15:44:24'),
(429, 3, 'Packaging', 9, NULL, 1, '2026-05-06 15:44:24'),
(430, 3, 'Packaging', 10, NULL, 1, '2026-05-06 15:44:24'),
(431, 3, 'Packaging', 11, NULL, 1, '2026-05-06 15:44:24'),
(432, 3, 'Packaging', 12, NULL, 1, '2026-05-06 15:44:24'),
(433, 3, 'Packaging', 13, NULL, 1, '2026-05-06 15:44:24'),
(434, 3, 'Packaging', 14, NULL, 1, '2026-05-06 15:44:24'),
(435, 3, 'Packaging', 15, NULL, 1, '2026-05-06 15:44:24'),
(436, 3, 'Packaging', 16, NULL, 1, '2026-05-06 15:44:24'),
(437, 3, 'Packaging', 17, NULL, 1, '2026-05-06 15:44:24'),
(438, 3, 'Packaging', 18, NULL, 1, '2026-05-06 15:44:24'),
(439, 3, 'Packaging', 19, NULL, 1, '2026-05-06 15:44:24'),
(440, 3, 'Packaging', 20, NULL, 1, '2026-05-06 15:44:24'),
(441, 3, 'Packaging', 21, NULL, 1, '2026-05-06 15:44:24'),
(442, 3, 'Packaging', 22, NULL, 1, '2026-05-06 15:44:24'),
(443, 3, 'Packaging', 23, NULL, 1, '2026-05-06 15:44:24'),
(444, 3, 'Packaging', 24, NULL, 1, '2026-05-06 15:44:24'),
(445, 3, 'Packaging', 25, NULL, 1, '2026-05-06 15:44:24'),
(446, 3, 'Packaging', 26, NULL, 1, '2026-05-06 15:44:24'),
(447, 3, 'Packaging', 27, NULL, 1, '2026-05-06 15:44:24'),
(448, 3, 'Packaging', 28, NULL, 1, '2026-05-06 15:44:24'),
(449, 3, 'Packaging', 29, NULL, 1, '2026-05-06 15:44:24'),
(450, 3, 'Packaging', 30, NULL, 1, '2026-05-06 15:44:24'),
(451, 3, 'Filling', 1, NULL, 1, '2026-05-06 15:44:26'),
(452, 3, 'Filling', 2, NULL, 1, '2026-05-06 15:44:26'),
(453, 3, 'Filling', 3, NULL, 1, '2026-05-06 15:44:26'),
(454, 3, 'Filling', 4, NULL, 1, '2026-05-06 15:44:26'),
(455, 3, 'Filling', 5, NULL, 1, '2026-05-06 15:44:26'),
(456, 3, 'Filling', 6, NULL, 1, '2026-05-06 15:44:26'),
(457, 3, 'Filling', 7, NULL, 1, '2026-05-06 15:44:26'),
(458, 3, 'Filling', 8, NULL, 1, '2026-05-06 15:44:26'),
(459, 3, 'Filling', 9, NULL, 1, '2026-05-06 15:44:26'),
(460, 3, 'Filling', 10, NULL, 1, '2026-05-06 15:44:26'),
(461, 3, 'Filling', 11, NULL, 1, '2026-05-06 15:44:26'),
(462, 3, 'Filling', 12, NULL, 1, '2026-05-06 15:44:26'),
(463, 3, 'Filling', 13, NULL, 1, '2026-05-06 15:44:26'),
(464, 3, 'Filling', 14, NULL, 1, '2026-05-06 15:44:26'),
(465, 3, 'Filling', 15, NULL, 1, '2026-05-06 15:44:26'),
(466, 3, 'Filling', 16, NULL, 1, '2026-05-06 15:44:26'),
(467, 3, 'Filling', 17, NULL, 1, '2026-05-06 15:44:26'),
(468, 3, 'Filling', 18, NULL, 1, '2026-05-06 15:44:26'),
(469, 3, 'Filling', 19, NULL, 1, '2026-05-06 15:44:26'),
(470, 3, 'Filling', 20, NULL, 1, '2026-05-06 15:44:26'),
(471, 3, 'Filling', 21, NULL, 1, '2026-05-06 15:44:26'),
(472, 3, 'Filling', 22, NULL, 1, '2026-05-06 15:44:26'),
(473, 3, 'Filling', 23, NULL, 1, '2026-05-06 15:44:26'),
(474, 3, 'Filling', 24, NULL, 1, '2026-05-06 15:44:26'),
(475, 3, 'Filling', 25, NULL, 1, '2026-05-06 15:44:26'),
(476, 3, 'Filling', 26, NULL, 1, '2026-05-06 15:44:26'),
(477, 3, 'Filling', 27, NULL, 1, '2026-05-06 15:44:26'),
(478, 3, 'Filling', 28, NULL, 1, '2026-05-06 15:44:26'),
(479, 3, 'Filling', 29, NULL, 1, '2026-05-06 15:44:26'),
(480, 3, 'Filling', 30, NULL, 1, '2026-05-06 15:44:26'),
(631, 6, 'Packaging', 1, 1.000, 0, '2026-05-07 07:06:29'),
(632, 6, 'Packaging', 2, 2.000, 0, '2026-05-07 07:06:29'),
(633, 6, 'Packaging', 3, 3.000, 0, '2026-05-07 07:06:29'),
(634, 6, 'Packaging', 4, 4.000, 0, '2026-05-07 07:06:29'),
(635, 6, 'Packaging', 5, 5.000, 0, '2026-05-07 07:06:29'),
(636, 6, 'Packaging', 6, 3.000, 0, '2026-05-07 07:06:29'),
(637, 6, 'Packaging', 7, 0.034, 0, '2026-05-07 07:06:29'),
(638, 6, 'Packaging', 8, 23.000, 0, '2026-05-07 07:06:29'),
(639, 6, 'Packaging', 9, 32.000, 0, '2026-05-07 07:06:29'),
(640, 6, 'Packaging', 10, 23.000, 0, '2026-05-07 07:06:29'),
(641, 6, 'Packaging', 11, 23.000, 0, '2026-05-07 07:06:29'),
(642, 6, 'Packaging', 12, 34.000, 0, '2026-05-07 07:06:29'),
(643, 6, 'Packaging', 13, 43.000, 0, '2026-05-07 07:06:29'),
(644, 6, 'Packaging', 14, 33.000, 0, '2026-05-07 07:06:29'),
(645, 6, 'Packaging', 15, 33.000, 0, '2026-05-07 07:06:29'),
(646, 6, 'Packaging', 16, 31.000, 0, '2026-05-07 07:06:29'),
(647, 6, 'Packaging', 17, 2.000, 0, '2026-05-07 07:06:29'),
(648, 6, 'Packaging', 18, 12.000, 0, '2026-05-07 07:06:29'),
(649, 6, 'Packaging', 19, 12.000, 0, '2026-05-07 07:06:29'),
(650, 6, 'Packaging', 20, 21.000, 0, '2026-05-07 07:06:29'),
(651, 6, 'Packaging', 21, 11.000, 0, '2026-05-07 07:06:29'),
(652, 6, 'Packaging', 22, 22.000, 0, '2026-05-07 07:06:29'),
(653, 6, 'Packaging', 23, 11.000, 0, '2026-05-07 07:06:29'),
(654, 6, 'Packaging', 24, 22.000, 0, '2026-05-07 07:06:29'),
(655, 6, 'Packaging', 25, 13.000, 0, '2026-05-07 07:06:29'),
(656, 6, 'Packaging', 26, 42.000, 0, '2026-05-07 07:06:29'),
(657, 6, 'Packaging', 27, 34.000, 0, '2026-05-07 07:06:29'),
(658, 6, 'Packaging', 28, 21.000, 0, '2026-05-07 07:06:29'),
(659, 6, 'Packaging', 29, 23.000, 0, '2026-05-07 07:06:29'),
(660, 6, 'Packaging', 30, 11.000, 0, '2026-05-07 07:06:29'),
(661, 6, 'Filling', 1, 65.000, 0, '2026-05-07 07:07:31'),
(662, 6, 'Filling', 2, 12.000, 0, '2026-05-07 07:07:31'),
(663, 6, 'Filling', 3, 12.000, 0, '2026-05-07 07:07:31'),
(664, 6, 'Filling', 4, 23.000, 0, '2026-05-07 07:07:31'),
(665, 6, 'Filling', 5, 31.000, 0, '2026-05-07 07:07:31'),
(666, 6, 'Filling', 6, 23.000, 0, '2026-05-07 07:07:31'),
(667, 6, 'Filling', 7, 65.000, 0, '2026-05-07 07:07:31'),
(668, 6, 'Filling', 8, 3.000, 0, '2026-05-07 07:07:31'),
(669, 6, 'Filling', 9, 73.000, 0, '2026-05-07 07:07:31'),
(670, 6, 'Filling', 10, 23.000, 0, '2026-05-07 07:07:31'),
(671, 6, 'Filling', 11, 23.000, 0, '2026-05-07 07:07:31'),
(672, 6, 'Filling', 12, 12.000, 0, '2026-05-07 07:07:31'),
(673, 6, 'Filling', 13, 12.000, 0, '2026-05-07 07:07:31'),
(674, 6, 'Filling', 14, 21.000, 0, '2026-05-07 07:07:31'),
(675, 6, 'Filling', 15, 23.000, 0, '2026-05-07 07:07:31'),
(676, 6, 'Filling', 16, 11.000, 0, '2026-05-07 07:07:31'),
(677, 6, 'Filling', 17, 22.000, 0, '2026-05-07 07:07:31'),
(678, 6, 'Filling', 18, 44.000, 0, '2026-05-07 07:07:31'),
(679, 6, 'Filling', 19, 55.000, 0, '2026-05-07 07:07:31'),
(680, 6, 'Filling', 20, 66.000, 0, '2026-05-07 07:07:31'),
(681, 6, 'Filling', 21, 67.000, 0, '2026-05-07 07:07:31'),
(682, 6, 'Filling', 22, 11.000, 0, '2026-05-07 07:07:31'),
(683, 6, 'Filling', 23, 11.000, 0, '2026-05-07 07:07:31'),
(684, 6, 'Filling', 24, 11.000, 0, '2026-05-07 07:07:31'),
(685, 6, 'Filling', 25, 12.000, 0, '2026-05-07 07:07:31'),
(686, 6, 'Filling', 26, 12.000, 0, '2026-05-07 07:07:31'),
(687, 6, 'Filling', 27, 1.000, 0, '2026-05-07 07:07:31'),
(688, 6, 'Filling', 28, 21.000, 0, '2026-05-07 07:07:31'),
(689, 6, 'Filling', 29, 11.000, 0, '2026-05-07 07:07:31'),
(690, 6, 'Filling', 30, 12.000, 0, '2026-05-07 07:07:31'),
(1561, 8, 'Packaging', 1, NULL, 1, '2026-05-07 09:36:07'),
(1562, 8, 'Packaging', 2, NULL, 1, '2026-05-07 09:36:07'),
(1563, 8, 'Packaging', 3, NULL, 1, '2026-05-07 09:36:07'),
(1564, 8, 'Packaging', 4, NULL, 1, '2026-05-07 09:36:07'),
(1565, 8, 'Packaging', 5, NULL, 1, '2026-05-07 09:36:07'),
(1566, 8, 'Packaging', 6, NULL, 1, '2026-05-07 09:36:07'),
(1567, 8, 'Packaging', 7, NULL, 1, '2026-05-07 09:36:07'),
(1568, 8, 'Packaging', 8, NULL, 1, '2026-05-07 09:36:07'),
(1569, 8, 'Packaging', 9, NULL, 1, '2026-05-07 09:36:07'),
(1570, 8, 'Packaging', 10, NULL, 1, '2026-05-07 09:36:07'),
(1571, 8, 'Packaging', 11, NULL, 1, '2026-05-07 09:36:07'),
(1572, 8, 'Packaging', 12, NULL, 1, '2026-05-07 09:36:07'),
(1573, 8, 'Packaging', 13, NULL, 1, '2026-05-07 09:36:07'),
(1574, 8, 'Packaging', 14, NULL, 1, '2026-05-07 09:36:07'),
(1575, 8, 'Packaging', 15, NULL, 1, '2026-05-07 09:36:07'),
(1576, 8, 'Packaging', 16, NULL, 1, '2026-05-07 09:36:07'),
(1577, 8, 'Packaging', 17, NULL, 1, '2026-05-07 09:36:07'),
(1578, 8, 'Packaging', 18, NULL, 1, '2026-05-07 09:36:07'),
(1579, 8, 'Packaging', 19, NULL, 1, '2026-05-07 09:36:07'),
(1580, 8, 'Packaging', 20, NULL, 1, '2026-05-07 09:36:07'),
(1581, 8, 'Packaging', 21, NULL, 1, '2026-05-07 09:36:07'),
(1582, 8, 'Packaging', 22, NULL, 1, '2026-05-07 09:36:07'),
(1583, 8, 'Packaging', 23, NULL, 1, '2026-05-07 09:36:07'),
(1584, 8, 'Packaging', 24, NULL, 1, '2026-05-07 09:36:07'),
(1585, 8, 'Packaging', 25, NULL, 1, '2026-05-07 09:36:07'),
(1586, 8, 'Packaging', 26, NULL, 1, '2026-05-07 09:36:07'),
(1587, 8, 'Packaging', 27, NULL, 1, '2026-05-07 09:36:07'),
(1588, 8, 'Packaging', 28, NULL, 1, '2026-05-07 09:36:07'),
(1589, 8, 'Packaging', 29, NULL, 1, '2026-05-07 09:36:07'),
(1590, 8, 'Packaging', 30, NULL, 1, '2026-05-07 09:36:07'),
(1591, 8, 'Filling', 1, NULL, 1, '2026-05-07 09:36:11'),
(1592, 8, 'Filling', 2, NULL, 1, '2026-05-07 09:36:11'),
(1593, 8, 'Filling', 3, NULL, 1, '2026-05-07 09:36:11'),
(1594, 8, 'Filling', 4, NULL, 1, '2026-05-07 09:36:11'),
(1595, 8, 'Filling', 5, NULL, 1, '2026-05-07 09:36:11'),
(1596, 8, 'Filling', 6, NULL, 1, '2026-05-07 09:36:11'),
(1597, 8, 'Filling', 7, NULL, 1, '2026-05-07 09:36:11'),
(1598, 8, 'Filling', 8, NULL, 1, '2026-05-07 09:36:11'),
(1599, 8, 'Filling', 9, NULL, 1, '2026-05-07 09:36:11'),
(1600, 8, 'Filling', 10, NULL, 1, '2026-05-07 09:36:11'),
(1601, 8, 'Filling', 11, NULL, 1, '2026-05-07 09:36:11'),
(1602, 8, 'Filling', 12, NULL, 1, '2026-05-07 09:36:11'),
(1603, 8, 'Filling', 13, NULL, 1, '2026-05-07 09:36:11'),
(1604, 8, 'Filling', 14, NULL, 1, '2026-05-07 09:36:11'),
(1605, 8, 'Filling', 15, NULL, 1, '2026-05-07 09:36:11'),
(1606, 8, 'Filling', 16, NULL, 1, '2026-05-07 09:36:11'),
(1607, 8, 'Filling', 17, NULL, 1, '2026-05-07 09:36:11'),
(1608, 8, 'Filling', 18, NULL, 1, '2026-05-07 09:36:11'),
(1609, 8, 'Filling', 19, NULL, 1, '2026-05-07 09:36:11'),
(1610, 8, 'Filling', 20, NULL, 1, '2026-05-07 09:36:11'),
(1611, 8, 'Filling', 21, NULL, 1, '2026-05-07 09:36:11'),
(1612, 8, 'Filling', 22, NULL, 1, '2026-05-07 09:36:11'),
(1613, 8, 'Filling', 23, NULL, 1, '2026-05-07 09:36:11'),
(1614, 8, 'Filling', 24, NULL, 1, '2026-05-07 09:36:11'),
(1615, 8, 'Filling', 25, NULL, 1, '2026-05-07 09:36:11'),
(1616, 8, 'Filling', 26, NULL, 1, '2026-05-07 09:36:11'),
(1617, 8, 'Filling', 27, NULL, 1, '2026-05-07 09:36:11'),
(1618, 8, 'Filling', 28, NULL, 1, '2026-05-07 09:36:11'),
(1619, 8, 'Filling', 29, NULL, 1, '2026-05-07 09:36:11'),
(1620, 8, 'Filling', 30, NULL, 1, '2026-05-07 09:36:11'),
(1801, 7, 'Packaging', 1, NULL, 1, '2026-05-08 03:04:45'),
(1802, 7, 'Packaging', 2, NULL, 1, '2026-05-08 03:04:45'),
(1803, 7, 'Packaging', 3, NULL, 1, '2026-05-08 03:04:45'),
(1804, 7, 'Packaging', 4, NULL, 1, '2026-05-08 03:04:45'),
(1805, 7, 'Packaging', 5, NULL, 1, '2026-05-08 03:04:45'),
(1806, 7, 'Packaging', 6, NULL, 1, '2026-05-08 03:04:45'),
(1807, 7, 'Packaging', 7, NULL, 1, '2026-05-08 03:04:45'),
(1808, 7, 'Packaging', 8, NULL, 1, '2026-05-08 03:04:45'),
(1809, 7, 'Packaging', 9, NULL, 1, '2026-05-08 03:04:45'),
(1810, 7, 'Packaging', 10, NULL, 1, '2026-05-08 03:04:45'),
(1811, 7, 'Packaging', 11, NULL, 1, '2026-05-08 03:04:45'),
(1812, 7, 'Packaging', 12, NULL, 1, '2026-05-08 03:04:45'),
(1813, 7, 'Packaging', 13, NULL, 1, '2026-05-08 03:04:45'),
(1814, 7, 'Packaging', 14, NULL, 1, '2026-05-08 03:04:45'),
(1815, 7, 'Packaging', 15, NULL, 1, '2026-05-08 03:04:45'),
(1816, 7, 'Packaging', 16, NULL, 1, '2026-05-08 03:04:45'),
(1817, 7, 'Packaging', 17, NULL, 1, '2026-05-08 03:04:45'),
(1818, 7, 'Packaging', 18, NULL, 1, '2026-05-08 03:04:45'),
(1819, 7, 'Packaging', 19, NULL, 1, '2026-05-08 03:04:45'),
(1820, 7, 'Packaging', 20, NULL, 1, '2026-05-08 03:04:45'),
(1821, 7, 'Packaging', 21, NULL, 1, '2026-05-08 03:04:45'),
(1822, 7, 'Packaging', 22, NULL, 1, '2026-05-08 03:04:45'),
(1823, 7, 'Packaging', 23, NULL, 1, '2026-05-08 03:04:45'),
(1824, 7, 'Packaging', 24, NULL, 1, '2026-05-08 03:04:45'),
(1825, 7, 'Packaging', 25, NULL, 1, '2026-05-08 03:04:45'),
(1826, 7, 'Packaging', 26, NULL, 1, '2026-05-08 03:04:45'),
(1827, 7, 'Packaging', 27, NULL, 1, '2026-05-08 03:04:45'),
(1828, 7, 'Packaging', 28, NULL, 1, '2026-05-08 03:04:45'),
(1829, 7, 'Packaging', 29, NULL, 1, '2026-05-08 03:04:45'),
(1830, 7, 'Packaging', 30, NULL, 1, '2026-05-08 03:04:45'),
(1831, 7, 'Filling', 1, NULL, 1, '2026-05-08 03:04:46'),
(1832, 7, 'Filling', 2, NULL, 1, '2026-05-08 03:04:46'),
(1833, 7, 'Filling', 3, NULL, 1, '2026-05-08 03:04:46'),
(1834, 7, 'Filling', 4, NULL, 1, '2026-05-08 03:04:46'),
(1835, 7, 'Filling', 5, NULL, 1, '2026-05-08 03:04:46'),
(1836, 7, 'Filling', 6, NULL, 1, '2026-05-08 03:04:46'),
(1837, 7, 'Filling', 7, NULL, 1, '2026-05-08 03:04:46'),
(1838, 7, 'Filling', 8, NULL, 1, '2026-05-08 03:04:46'),
(1839, 7, 'Filling', 9, NULL, 1, '2026-05-08 03:04:46'),
(1840, 7, 'Filling', 10, NULL, 1, '2026-05-08 03:04:46'),
(1841, 7, 'Filling', 11, NULL, 1, '2026-05-08 03:04:46'),
(1842, 7, 'Filling', 12, NULL, 1, '2026-05-08 03:04:46'),
(1843, 7, 'Filling', 13, NULL, 1, '2026-05-08 03:04:46'),
(1844, 7, 'Filling', 14, NULL, 1, '2026-05-08 03:04:46'),
(1845, 7, 'Filling', 15, NULL, 1, '2026-05-08 03:04:46'),
(1846, 7, 'Filling', 16, NULL, 1, '2026-05-08 03:04:46'),
(1847, 7, 'Filling', 17, NULL, 1, '2026-05-08 03:04:46'),
(1848, 7, 'Filling', 18, NULL, 1, '2026-05-08 03:04:46'),
(1849, 7, 'Filling', 19, NULL, 1, '2026-05-08 03:04:46'),
(1850, 7, 'Filling', 20, NULL, 1, '2026-05-08 03:04:46'),
(1851, 7, 'Filling', 21, NULL, 1, '2026-05-08 03:04:46'),
(1852, 7, 'Filling', 22, NULL, 1, '2026-05-08 03:04:46'),
(1853, 7, 'Filling', 23, NULL, 1, '2026-05-08 03:04:46'),
(1854, 7, 'Filling', 24, NULL, 1, '2026-05-08 03:04:46'),
(1855, 7, 'Filling', 25, NULL, 1, '2026-05-08 03:04:46'),
(1856, 7, 'Filling', 26, NULL, 1, '2026-05-08 03:04:46'),
(1857, 7, 'Filling', 27, NULL, 1, '2026-05-08 03:04:46'),
(1858, 7, 'Filling', 28, NULL, 1, '2026-05-08 03:04:46'),
(1859, 7, 'Filling', 29, NULL, 1, '2026-05-08 03:04:46'),
(1860, 7, 'Filling', 30, NULL, 1, '2026-05-08 03:04:46'),
(1921, 9, 'Packaging', 1, 1.000, 0, '2026-05-08 09:07:10'),
(1922, 9, 'Packaging', 2, 1.000, 0, '2026-05-08 09:07:10'),
(1923, 9, 'Packaging', 3, 2.000, 0, '2026-05-08 09:07:10'),
(1924, 9, 'Packaging', 4, 1.000, 0, '2026-05-08 09:07:10'),
(1925, 9, 'Packaging', 5, 2.000, 0, '2026-05-08 09:07:10'),
(1926, 9, 'Packaging', 6, 2.000, 0, '2026-05-08 09:07:10'),
(1927, 9, 'Packaging', 7, 12.000, 0, '2026-05-08 09:07:10'),
(1928, 9, 'Packaging', 8, 12.000, 0, '2026-05-08 09:07:10'),
(1929, 9, 'Packaging', 9, 13.000, 0, '2026-05-08 09:07:10'),
(1930, 9, 'Packaging', 10, 12.000, 0, '2026-05-08 09:07:10'),
(1931, 9, 'Packaging', 11, 12.000, 0, '2026-05-08 09:07:10'),
(1932, 9, 'Packaging', 12, 12.000, 0, '2026-05-08 09:07:10'),
(1933, 9, 'Packaging', 13, 12.000, 0, '2026-05-08 09:07:10'),
(1934, 9, 'Packaging', 14, 12.000, 0, '2026-05-08 09:07:10'),
(1935, 9, 'Packaging', 15, 22.000, 0, '2026-05-08 09:07:10'),
(1936, 9, 'Packaging', 16, 11.000, 0, '2026-05-08 09:07:10'),
(1937, 9, 'Packaging', 17, 112.000, 0, '2026-05-08 09:07:10'),
(1938, 9, 'Packaging', 18, 55.000, 0, '2026-05-08 09:07:10'),
(1939, 9, 'Packaging', 19, 34.000, 0, '2026-05-08 09:07:10'),
(1940, 9, 'Packaging', 20, 33.000, 0, '2026-05-08 09:07:10'),
(1941, 9, 'Packaging', 21, 44.000, 0, '2026-05-08 09:07:10'),
(1942, 9, 'Packaging', 22, 44.000, 0, '2026-05-08 09:07:10'),
(1943, 9, 'Packaging', 23, 55.000, 0, '2026-05-08 09:07:10'),
(1944, 9, 'Packaging', 24, 33.000, 0, '2026-05-08 09:07:10'),
(1945, 9, 'Packaging', 25, 43.000, 0, '2026-05-08 09:07:10'),
(1946, 9, 'Packaging', 26, 32.000, 0, '2026-05-08 09:07:10'),
(1947, 9, 'Packaging', 27, 24.000, 0, '2026-05-08 09:07:10'),
(1948, 9, 'Packaging', 28, 23.000, 0, '2026-05-08 09:07:10'),
(1949, 9, 'Packaging', 29, 23.000, 0, '2026-05-08 09:07:10'),
(1950, 9, 'Packaging', 30, 21.000, 0, '2026-05-08 09:07:10'),
(1951, 9, 'Filling', 1, 1.000, 0, '2026-05-08 09:07:45'),
(1952, 9, 'Filling', 2, 2.000, 0, '2026-05-08 09:07:45'),
(1953, 9, 'Filling', 3, 12.000, 0, '2026-05-08 09:07:45'),
(1954, 9, 'Filling', 4, 12.000, 0, '2026-05-08 09:07:45'),
(1955, 9, 'Filling', 5, 12.000, 0, '2026-05-08 09:07:45'),
(1956, 9, 'Filling', 6, 12.000, 0, '2026-05-08 09:07:45'),
(1957, 9, 'Filling', 7, 12.000, 0, '2026-05-08 09:07:45'),
(1958, 9, 'Filling', 8, 12.000, 0, '2026-05-08 09:07:45'),
(1959, 9, 'Filling', 9, 1.000, 0, '2026-05-08 09:07:45'),
(1960, 9, 'Filling', 10, 21.000, 0, '2026-05-08 09:07:45'),
(1961, 9, 'Filling', 11, 12.000, 0, '2026-05-08 09:07:45'),
(1962, 9, 'Filling', 12, 12.000, 0, '2026-05-08 09:07:45'),
(1963, 9, 'Filling', 13, 12.000, 0, '2026-05-08 09:07:45'),
(1964, 9, 'Filling', 14, 12.000, 0, '2026-05-08 09:07:45'),
(1965, 9, 'Filling', 15, 12.000, 0, '2026-05-08 09:07:45'),
(1966, 9, 'Filling', 16, 21.000, 0, '2026-05-08 09:07:45'),
(1967, 9, 'Filling', 17, 12.000, 0, '2026-05-08 09:07:45'),
(1968, 9, 'Filling', 18, 12.000, 0, '2026-05-08 09:07:45'),
(1969, 9, 'Filling', 19, 2.000, 0, '2026-05-08 09:07:45'),
(1970, 9, 'Filling', 20, 12.000, 0, '2026-05-08 09:07:45'),
(1971, 9, 'Filling', 21, 23.000, 0, '2026-05-08 09:07:45'),
(1972, 9, 'Filling', 22, 22.000, 0, '2026-05-08 09:07:45'),
(1973, 9, 'Filling', 23, 22.000, 0, '2026-05-08 09:07:45'),
(1974, 9, 'Filling', 24, 45.000, 0, '2026-05-08 09:07:45'),
(1975, 9, 'Filling', 25, 23.000, 0, '2026-05-08 09:07:45'),
(1976, 9, 'Filling', 26, 12.000, 0, '2026-05-08 09:07:45'),
(1977, 9, 'Filling', 27, 12.000, 0, '2026-05-08 09:07:45'),
(1978, 9, 'Filling', 28, 23.000, 0, '2026-05-08 09:07:45'),
(1979, 9, 'Filling', 29, 34.000, 0, '2026-05-08 09:07:45'),
(1980, 9, 'Filling', 30, 23.000, 0, '2026-05-08 09:07:45'),
(2101, 10, 'Packaging', 1, 1.000, 0, '2026-05-08 09:51:09'),
(2102, 10, 'Packaging', 2, 1.000, 0, '2026-05-08 09:51:09'),
(2103, 10, 'Packaging', 3, 2.000, 0, '2026-05-08 09:51:09'),
(2104, 10, 'Packaging', 4, 2.000, 0, '2026-05-08 09:51:09'),
(2105, 10, 'Packaging', 5, 2.000, 0, '2026-05-08 09:51:09'),
(2106, 10, 'Packaging', 6, 3.000, 0, '2026-05-08 09:51:09'),
(2107, 10, 'Packaging', 7, 3.000, 0, '2026-05-08 09:51:09'),
(2108, 10, 'Packaging', 8, 4.000, 0, '2026-05-08 09:51:09'),
(2109, 10, 'Packaging', 9, 4.000, 0, '2026-05-08 09:51:09'),
(2110, 10, 'Packaging', 10, 5.000, 0, '2026-05-08 09:51:09'),
(2111, 10, 'Packaging', 11, 5.000, 0, '2026-05-08 09:51:09'),
(2112, 10, 'Packaging', 12, 8.000, 0, '2026-05-08 09:51:09'),
(2113, 10, 'Packaging', 13, 8.000, 0, '2026-05-08 09:51:09'),
(2114, 10, 'Packaging', 14, 4.000, 0, '2026-05-08 09:51:09'),
(2115, 10, 'Packaging', 15, 4.000, 0, '2026-05-08 09:51:09'),
(2116, 10, 'Packaging', 16, 4.000, 0, '2026-05-08 09:51:09'),
(2117, 10, 'Packaging', 17, 5.000, 0, '2026-05-08 09:51:09'),
(2118, 10, 'Packaging', 18, 4.000, 0, '2026-05-08 09:51:09'),
(2119, 10, 'Packaging', 19, 4.000, 0, '2026-05-08 09:51:09'),
(2120, 10, 'Packaging', 20, 4.000, 0, '2026-05-08 09:51:09'),
(2121, 10, 'Packaging', 21, 4.000, 0, '2026-05-08 09:51:09'),
(2122, 10, 'Packaging', 22, 5.000, 0, '2026-05-08 09:51:09'),
(2123, 10, 'Packaging', 23, 5.000, 0, '2026-05-08 09:51:09'),
(2124, 10, 'Packaging', 24, 5.000, 0, '2026-05-08 09:51:09'),
(2125, 10, 'Packaging', 25, 5.000, 0, '2026-05-08 09:51:09'),
(2126, 10, 'Packaging', 26, 5.000, 0, '2026-05-08 09:51:09'),
(2127, 10, 'Packaging', 27, 5.000, 0, '2026-05-08 09:51:09'),
(2128, 10, 'Packaging', 28, 5.000, 0, '2026-05-08 09:51:09'),
(2129, 10, 'Packaging', 29, 5.000, 0, '2026-05-08 09:51:09'),
(2130, 10, 'Packaging', 30, 5.000, 0, '2026-05-08 09:51:09'),
(2131, 10, 'Filling', 1, 100.000, 0, '2026-05-08 09:51:10'),
(2132, 10, 'Filling', 2, 100.000, 0, '2026-05-08 09:51:10'),
(2133, 10, 'Filling', 3, 100.000, 0, '2026-05-08 09:51:10'),
(2134, 10, 'Filling', 4, 100.000, 0, '2026-05-08 09:51:10'),
(2135, 10, 'Filling', 5, 100.000, 0, '2026-05-08 09:51:10'),
(2136, 10, 'Filling', 6, 100.000, 0, '2026-05-08 09:51:10'),
(2137, 10, 'Filling', 7, 100.000, 0, '2026-05-08 09:51:10'),
(2138, 10, 'Filling', 8, 100.000, 0, '2026-05-08 09:51:10'),
(2139, 10, 'Filling', 9, 100.000, 0, '2026-05-08 09:51:10'),
(2140, 10, 'Filling', 10, 100.000, 0, '2026-05-08 09:51:10'),
(2141, 10, 'Filling', 11, 100.000, 0, '2026-05-08 09:51:10'),
(2142, 10, 'Filling', 12, 100.000, 0, '2026-05-08 09:51:10'),
(2143, 10, 'Filling', 13, 100.000, 0, '2026-05-08 09:51:10'),
(2144, 10, 'Filling', 14, 100.000, 0, '2026-05-08 09:51:10'),
(2145, 10, 'Filling', 15, 100.000, 0, '2026-05-08 09:51:10'),
(2146, 10, 'Filling', 16, 100.000, 0, '2026-05-08 09:51:10'),
(2147, 10, 'Filling', 17, 100.000, 0, '2026-05-08 09:51:10'),
(2148, 10, 'Filling', 18, 100.000, 0, '2026-05-08 09:51:10'),
(2149, 10, 'Filling', 19, 100.000, 0, '2026-05-08 09:51:10'),
(2150, 10, 'Filling', 20, 100.000, 0, '2026-05-08 09:51:10'),
(2151, 10, 'Filling', 21, 100.000, 0, '2026-05-08 09:51:10'),
(2152, 10, 'Filling', 22, 100.000, 0, '2026-05-08 09:51:10'),
(2153, 10, 'Filling', 23, 100.000, 0, '2026-05-08 09:51:10'),
(2154, 10, 'Filling', 24, 100.000, 0, '2026-05-08 09:51:10'),
(2155, 10, 'Filling', 25, 100.000, 0, '2026-05-08 09:51:10'),
(2156, 10, 'Filling', 26, 100.000, 0, '2026-05-08 09:51:10'),
(2157, 10, 'Filling', 27, 100.000, 0, '2026-05-08 09:51:10'),
(2158, 10, 'Filling', 28, 100.000, 0, '2026-05-08 09:51:10'),
(2159, 10, 'Filling', 29, 100.000, 0, '2026-05-08 09:51:10'),
(2160, 10, 'Filling', 30, 100.000, 0, '2026-05-08 09:51:10'),
(2161, 5, 'Packaging', 1, 2.000, 0, '2026-05-08 09:56:35'),
(2162, 5, 'Packaging', 2, 2.000, 0, '2026-05-08 09:56:35'),
(2163, 5, 'Packaging', 3, 5.000, 0, '2026-05-08 09:56:35'),
(2164, 5, 'Packaging', 4, 5.000, 0, '2026-05-08 09:56:35'),
(2165, 5, 'Packaging', 5, 5.000, 0, '2026-05-08 09:56:35'),
(2166, 5, 'Packaging', 6, 5.000, 0, '2026-05-08 09:56:35'),
(2167, 5, 'Packaging', 7, 45.000, 0, '2026-05-08 09:56:35'),
(2168, 5, 'Packaging', 8, 54.000, 0, '2026-05-08 09:56:35'),
(2169, 5, 'Packaging', 9, 54.000, 0, '2026-05-08 09:56:35'),
(2170, 5, 'Packaging', 10, 54.000, 0, '2026-05-08 09:56:35'),
(2171, 5, 'Packaging', 11, 4.000, 0, '2026-05-08 09:56:35'),
(2172, 5, 'Packaging', 12, 3.000, 0, '2026-05-08 09:56:35'),
(2173, 5, 'Packaging', 13, 5.000, 0, '2026-05-08 09:56:35'),
(2174, 5, 'Packaging', 14, 5.000, 0, '2026-05-08 09:56:35'),
(2175, 5, 'Packaging', 15, 5.000, 0, '2026-05-08 09:56:35'),
(2176, 5, 'Packaging', 16, 5.000, 0, '2026-05-08 09:56:35'),
(2177, 5, 'Packaging', 17, 545.000, 0, '2026-05-08 09:56:35'),
(2178, 5, 'Packaging', 18, 54.000, 0, '2026-05-08 09:56:35'),
(2179, 5, 'Packaging', 19, 54.000, 0, '2026-05-08 09:56:35'),
(2180, 5, 'Packaging', 20, 545.000, 0, '2026-05-08 09:56:35'),
(2181, 5, 'Packaging', 21, 5.000, 0, '2026-05-08 09:56:35'),
(2182, 5, 'Packaging', 22, 45.000, 0, '2026-05-08 09:56:35'),
(2183, 5, 'Packaging', 23, 5.000, 0, '2026-05-08 09:56:35'),
(2184, 5, 'Packaging', 24, 34.000, 0, '2026-05-08 09:56:35'),
(2185, 5, 'Packaging', 25, 43.000, 0, '2026-05-08 09:56:35'),
(2186, 5, 'Packaging', 26, 54.000, 0, '2026-05-08 09:56:35'),
(2187, 5, 'Packaging', 27, 54.000, 0, '2026-05-08 09:56:35'),
(2188, 5, 'Packaging', 28, 54.000, 0, '2026-05-08 09:56:35'),
(2189, 5, 'Packaging', 29, 54.000, 0, '2026-05-08 09:56:35'),
(2190, 5, 'Packaging', 30, 45.000, 0, '2026-05-08 09:56:35'),
(2191, 5, 'Filling', 1, 2.000, 0, '2026-05-08 09:56:36'),
(2192, 5, 'Filling', 2, 2.000, 0, '2026-05-08 09:56:36'),
(2193, 5, 'Filling', 3, 22.001, 0, '2026-05-08 09:56:36'),
(2194, 5, 'Filling', 4, 2.001, 0, '2026-05-08 09:56:36'),
(2195, 5, 'Filling', 5, 222.000, 0, '2026-05-08 09:56:36'),
(2196, 5, 'Filling', 6, 33.000, 0, '2026-05-08 09:56:36'),
(2197, 5, 'Filling', 7, 0.002, 0, '2026-05-08 09:56:36'),
(2198, 5, 'Filling', 8, 33.000, 0, '2026-05-08 09:56:36'),
(2199, 5, 'Filling', 9, 0.002, 0, '2026-05-08 09:56:36'),
(2200, 5, 'Filling', 10, 0.004, 0, '2026-05-08 09:56:36'),
(2201, 5, 'Filling', 11, 0.003, 0, '2026-05-08 09:56:36'),
(2202, 5, 'Filling', 12, 0.005, 0, '2026-05-08 09:56:36'),
(2203, 5, 'Filling', 13, 22.000, 0, '2026-05-08 09:56:36'),
(2204, 5, 'Filling', 14, 2.000, 0, '2026-05-08 09:56:36'),
(2205, 5, 'Filling', 15, 1.000, 0, '2026-05-08 09:56:36'),
(2206, 5, 'Filling', 16, 11.000, 0, '2026-05-08 09:56:36'),
(2207, 5, 'Filling', 17, 1.000, 0, '2026-05-08 09:56:36'),
(2208, 5, 'Filling', 18, 11.000, 0, '2026-05-08 09:56:36'),
(2209, 5, 'Filling', 19, 11.000, 0, '2026-05-08 09:56:36'),
(2210, 5, 'Filling', 20, 11.000, 0, '2026-05-08 09:56:36'),
(2211, 5, 'Filling', 21, 1.000, 0, '2026-05-08 09:56:36'),
(2212, 5, 'Filling', 22, 1.000, 0, '2026-05-08 09:56:36'),
(2213, 5, 'Filling', 23, 1.000, 0, '2026-05-08 09:56:36'),
(2214, 5, 'Filling', 24, 1.000, 0, '2026-05-08 09:56:36'),
(2215, 5, 'Filling', 25, 1.000, 0, '2026-05-08 09:56:36'),
(2216, 5, 'Filling', 26, 1.000, 0, '2026-05-08 09:56:36'),
(2217, 5, 'Filling', 27, 1.000, 0, '2026-05-08 09:56:36'),
(2218, 5, 'Filling', 28, 1.000, 0, '2026-05-08 09:56:36'),
(2219, 5, 'Filling', 29, 1.000, 0, '2026-05-08 09:56:36'),
(2220, 5, 'Filling', 30, 11.000, 0, '2026-05-08 09:56:36'),
(2246, 11, 'Packaging', 1, 1.000, 0, '2026-05-20 12:24:54'),
(2247, 11, 'Packaging', 2, 2.000, 0, '2026-05-20 12:24:54'),
(2248, 11, 'Packaging', 3, 1.000, 0, '2026-05-20 12:24:54'),
(2249, 11, 'Packaging', 4, 1.000, 0, '2026-05-20 12:24:54'),
(2250, 11, 'Packaging', 5, 1.000, 0, '2026-05-20 12:24:54'),
(2251, 11, 'Packaging', 6, 11.000, 0, '2026-05-20 12:24:54'),
(2252, 11, 'Packaging', 11, 1.000, 0, '2026-05-20 12:24:54'),
(2253, 11, 'Packaging', 12, 11.000, 0, '2026-05-20 12:24:54'),
(2254, 11, 'Packaging', 13, 1.000, 0, '2026-05-20 12:24:54'),
(2255, 11, 'Packaging', 14, 1.000, 0, '2026-05-20 12:24:54'),
(2256, 11, 'Packaging', 15, 11.000, 0, '2026-05-20 12:24:54'),
(2257, 11, 'Packaging', 16, 1.000, 0, '2026-05-20 12:24:54'),
(2258, 11, 'Packaging', 21, 1.000, 0, '2026-05-20 12:24:54'),
(2259, 11, 'Packaging', 22, 1.000, 0, '2026-05-20 12:24:54'),
(2260, 11, 'Packaging', 23, 1.000, 0, '2026-05-20 12:24:54'),
(2261, 11, 'Packaging', 24, 1.000, 0, '2026-05-20 12:24:54'),
(2262, 11, 'Packaging', 25, 23.000, 0, '2026-05-20 12:24:54'),
(2263, 11, 'Packaging', 26, 3.000, 0, '2026-05-20 12:24:54'),
(2264, 11, 'Packaging', 31, 1.000, 0, '2026-05-20 12:24:54'),
(2265, 11, 'Filling', 1, 3.000, 0, '2026-05-20 12:24:56'),
(2266, 11, 'Filling', 2, 4.000, 0, '2026-05-20 12:24:56'),
(2267, 11, 'Filling', 11, 3.000, 0, '2026-05-20 12:24:56'),
(2268, 11, 'Filling', 12, 4.000, 0, '2026-05-20 12:24:56'),
(2269, 11, 'Filling', 21, 4.000, 0, '2026-05-20 12:24:56'),
(2270, 11, 'Filling', 22, 3.000, 0, '2026-05-20 12:24:56'),
(2271, 13, 'Packaging', 1, 10.000, 0, '2026-05-21 01:51:00'),
(2272, 13, 'Packaging', 2, 10.000, 0, '2026-05-21 01:51:00'),
(2273, 13, 'Packaging', 3, 10.000, 0, '2026-05-21 01:51:00'),
(2274, 13, 'Packaging', 4, 11.000, 0, '2026-05-21 01:51:00'),
(2275, 13, 'Packaging', 5, 11.000, 0, '2026-05-21 01:51:00'),
(2276, 13, 'Packaging', 6, 12.000, 0, '2026-05-21 01:51:00'),
(2277, 13, 'Packaging', 7, 12.000, 0, '2026-05-21 01:51:00'),
(2278, 13, 'Packaging', 8, 15.000, 0, '2026-05-21 01:51:00'),
(2279, 13, 'Packaging', 9, 13.000, 0, '2026-05-21 01:51:00'),
(2280, 13, 'Packaging', 10, 11.000, 0, '2026-05-21 01:51:00'),
(2281, 13, 'Filling', 1, 24.000, 0, '2026-05-21 01:51:27'),
(2282, 13, 'Filling', 2, 25.000, 0, '2026-05-21 01:51:27'),
(2283, 13, 'Filling', 3, 21.000, 0, '2026-05-21 01:51:27'),
(2284, 13, 'Filling', 4, 22.000, 0, '2026-05-21 01:51:27'),
(2285, 13, 'Filling', 5, 22.000, 0, '2026-05-21 01:51:27'),
(2286, 13, 'Filling', 6, 25.000, 0, '2026-05-21 01:51:27'),
(2287, 13, 'Filling', 7, 24.000, 0, '2026-05-21 01:51:27'),
(2288, 13, 'Filling', 8, 25.000, 0, '2026-05-21 01:51:27'),
(2289, 13, 'Filling', 9, 25.000, 0, '2026-05-21 01:51:27'),
(2290, 13, 'Filling', 10, 24.000, 0, '2026-05-21 01:51:27');

-- --------------------------------------------------------

--
-- Table structure for table `trial_attachment_files`
--

CREATE TABLE `trial_attachment_files` (
  `id` int(11) NOT NULL,
  `trial_id` int(11) NOT NULL,
  `category` varchar(120) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_by` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trial_attachment_files`
--

INSERT INTO `trial_attachment_files` (`id`, `trial_id`, `category`, `file_name`, `file_path`, `uploaded_by`, `created_at`, `deleted_at`, `deleted_by`) VALUES
(1, 1, 'Vacuum Test', '1778035680_0_Screenshot_1.png', '/uploads/1/1778035680_0_Screenshot_1.png', 'admin@local.test', '2026-05-06 09:48:00', NULL, NULL),
(2, 1, 'Press Test', '1778035687_0_Screenshot_2.png', '/uploads/1/1778035687_0_Screenshot_2.png', 'admin@local.test', '2026-05-06 09:48:07', NULL, NULL),
(3, 1, 'Vacuum Test', '1778035699_0_thumbnail_9b35ae76-4197-41ee-8c32-e979a495b410.png', '/uploads/1/1778035699_0_thumbnail_9b35ae76-4197-41ee-8c32-e979a495b410.png', 'admin@local.test', '2026-05-06 09:48:19', NULL, NULL),
(4, 1, 'Vacuum Test', '1778035708_0_Screenshot_6.png', '/uploads/1/1778035708_0_Screenshot_6.png', 'admin@local.test', '2026-05-06 09:48:28', NULL, NULL),
(5, 2, 'Filling Process', 'f85080f411a50978ba6e77763b06a238.png', '/uploads/2/f85080f411a50978ba6e77763b06a238.png', 'damor@cosmax.com', '2026-05-06 10:51:26', NULL, NULL),
(6, 2, 'Air Pressure Test', '766a1d153135c3f742bbe9c496f83fd0.png', '/uploads/2/766a1d153135c3f742bbe9c496f83fd0.png', 'damor@cosmax.com', '2026-05-06 10:51:36', NULL, NULL),
(7, 1, 'Final Product', '1dec6fe4c51d0c00df327c64981cfe8f.png', '/uploads/1/1dec6fe4c51d0c00df327c64981cfe8f.png', 'damor@cosmax.com', '2026-05-06 11:10:10', NULL, NULL),
(8, 3, 'Vacuum Test', 'd3e90c17e93e6fc981d9ff0e02cdd11a.png', '/uploads/3/d3e90c17e93e6fc981d9ff0e02cdd11a.png', 'damor@cosmax.com', '2026-05-06 22:44:31', NULL, NULL),
(9, 3, 'Vacuum Test', 'd060bf66723f69ad34115aa221c596af.png', '/uploads/3/d060bf66723f69ad34115aa221c596af.png', 'damor@cosmax.com', '2026-05-06 22:44:57', NULL, NULL),
(10, 3, 'Final Product', 'afff4a1f20ebdc545381627aef08e9ce.png', '/uploads/3/afff4a1f20ebdc545381627aef08e9ce.png', 'damor@cosmax.com', '2026-05-06 22:45:07', NULL, NULL),
(11, 5, 'Final Product', '1d5a0704fb8fe3eb7aa3f9fdf1df6ff5.png', '/uploads/5/1d5a0704fb8fe3eb7aa3f9fdf1df6ff5.png', 'damor@cosmax.com', '2026-05-07 09:53:11', NULL, NULL),
(12, 6, 'Vacuum Test', '49454d030f5665062610292dcfe00ae0.png', '/uploads/6/49454d030f5665062610292dcfe00ae0.png', 'damor@cosmax.com', '2026-05-07 14:07:58', NULL, NULL),
(13, 6, 'Vacuum Test', 'ab140acfc817bc69820cb0ae4f858990.png', '/uploads/6/ab140acfc817bc69820cb0ae4f858990.png', 'damor@cosmax.com', '2026-05-07 14:08:10', NULL, NULL),
(14, 6, 'Press Test', '3a24c70720428158502b0c0d1ee280be.png', '/uploads/6/3a24c70720428158502b0c0d1ee280be.png', 'damor@cosmax.com', '2026-05-07 14:08:17', NULL, NULL),
(15, 6, 'Machine Setting', 'eea723bf9567c303930c33c94b75be35.png', '/uploads/6/eea723bf9567c303930c33c94b75be35.png', 'damor@cosmax.com', '2026-05-07 14:08:27', NULL, NULL),
(16, 6, 'Vacuum Test', '4918d33e8b54303414b236a267f2fca8.png', '/uploads/6/4918d33e8b54303414b236a267f2fca8.png', 'damor@cosmax.com', '2026-05-07 14:08:55', NULL, NULL),
(17, 6, 'Line Configuration', 'e58d77a6521b8ef5215b639a4045d5f3.png', '/uploads/6/e58d77a6521b8ef5215b639a4045d5f3.png', 'damor@cosmax.com', '2026-05-07 14:10:04', NULL, NULL),
(18, 6, 'Vacuum Test', 'cf9805095d96e327cb9d4d33e3703cd6.png', '/uploads/6/cf9805095d96e327cb9d4d33e3703cd6.png', 'damor@cosmax.com', '2026-05-07 14:10:23', NULL, NULL),
(19, 6, 'Filling Process', '0426774b9b3a0cd92dc0976e460a45c9.png', '/uploads/6/0426774b9b3a0cd92dc0976e460a45c9.png', 'damor@cosmax.com', '2026-05-07 14:11:12', NULL, NULL),
(20, 6, 'Final Product', '526c784095a7b4b7c728664a76a401c3.png', '/uploads/6/526c784095a7b4b7c728664a76a401c3.png', 'damor@cosmax.com', '2026-05-07 14:11:30', NULL, NULL),
(22, 7, 'Air Pressure Test', 'ef0a9585fd945a60396e0cb4004b963c.png', '/uploads/7/ef0a9585fd945a60396e0cb4004b963c.png', 'damor@cosmax.com', '2026-05-07 14:57:10', NULL, NULL),
(23, 7, 'Vacuum Test', '9a20a1d9a14a14e842dd766e39a805ee.png', '/uploads/7/9a20a1d9a14a14e842dd766e39a805ee.png', 'damor@cosmax.com', '2026-05-07 14:57:58', NULL, NULL),
(24, 7, 'Final Product', '0506b5e7249f3384a23b8c077bc58f93.png', '/uploads/7/0506b5e7249f3384a23b8c077bc58f93.png', 'damor@cosmax.com', '2026-05-07 14:58:09', NULL, NULL),
(25, 7, 'Vacuum Test', '4ac5c545d218784010993f26d666e1fa.png', '/uploads/7/4ac5c545d218784010993f26d666e1fa.png', 'damor@cosmax.com', '2026-05-07 14:58:20', NULL, NULL),
(26, 7, 'Vacuum Test', '6f7f8b56861ba427b8884d078ad88c65.png', '/uploads/7/6f7f8b56861ba427b8884d078ad88c65.png', 'damor@cosmax.com', '2026-05-07 14:58:33', NULL, NULL),
(27, 7, 'Vacuum Test', 'dc6b4454035f5b96ec729879710368af.png', '/uploads/7/dc6b4454035f5b96ec729879710368af.png', 'damor@cosmax.com', '2026-05-07 14:58:44', NULL, NULL),
(28, 7, 'Vacuum Test', '0863a7bbf4a9c2d47c44bf0cfcedcfff.png', '/uploads/7/0863a7bbf4a9c2d47c44bf0cfcedcfff.png', 'damor@cosmax.com', '2026-05-07 14:58:59', NULL, NULL),
(29, 7, 'Vacuum Test', 'd782f17b4d9a4654835c4dfac45d38a9.png', '/uploads/7/d782f17b4d9a4654835c4dfac45d38a9.png', 'damor@cosmax.com', '2026-05-07 14:59:24', NULL, NULL),
(30, 8, 'Vacuum Test', '934eaebab3c61619e149d4192d21d372.png', '/uploads/8/934eaebab3c61619e149d4192d21d372.png', 'damor@cosmax.com', '2026-05-07 16:36:28', NULL, NULL),
(31, 9, 'Vacuum Test', 'bbd2b06239ceccac4728effd83ac621f.jpg', '/uploads/9/bbd2b06239ceccac4728effd83ac621f.jpg', 'lomoniroha@cosmax.com', '2026-05-08 16:07:52', NULL, NULL),
(32, 10, 'Vacuum Test', 'cdbb03504f4c8c1c7deb790658344c5b.jpg', '/uploads/10/cdbb03504f4c8c1c7deb790658344c5b.jpg', 'fauzi@cosmax.com', '2026-05-08 16:45:33', NULL, NULL),
(33, 10, 'Press Test', '75d1a091ed9c35185db2af8f49fbb78d.jpg', '/uploads/10/75d1a091ed9c35185db2af8f49fbb78d.jpg', 'fauzi@cosmax.com', '2026-05-08 16:45:43', NULL, NULL),
(34, 10, 'Filling Process', '8db2349aba95e4fc7ab3544fcbc213be.png', '/uploads/10/8db2349aba95e4fc7ab3544fcbc213be.png', 'fauzi@cosmax.com', '2026-05-08 16:45:54', NULL, NULL),
(35, 11, 'Press Test', '73f82dfcebb1ff013d2ae8a1941a212a.png', '/uploads/11/73f82dfcebb1ff013d2ae8a1941a212a.png', 'damor@cosmax.com', '2026-05-20 19:22:22', NULL, NULL),
(36, 11, 'Vacuum Test', '5522a6e043b8041f8bb9391007ac780c.jpg', '/uploads/11/5522a6e043b8041f8bb9391007ac780c.jpg', 'damor@cosmax.com', '2026-05-20 19:22:31', NULL, NULL),
(37, 11, 'Vacuum Test', '0dcf748583e590dcf23bc2f50110582e.jpg', '/uploads/11/0dcf748583e590dcf23bc2f50110582e.jpg', 'damor@cosmax.com', '2026-05-20 19:22:38', NULL, NULL),
(38, 11, 'Vacuum Test', 'da62f132fd23ed1d5942e9e6a3b09c47.png', '/uploads/11/da62f132fd23ed1d5942e9e6a3b09c47.png', 'damor@cosmax.com', '2026-05-20 19:22:52', NULL, NULL),
(39, 11, 'Press Test', 'f1f5d8dcbdf8aabbb9de97660892f3ba.png', '/uploads/11/f1f5d8dcbdf8aabbb9de97660892f3ba.png', 'damor@cosmax.com', '2026-05-20 19:23:06', NULL, NULL),
(40, 11, 'Press Test', '30417e9a0abc07120ef0f6d2dc34b4e8.png', '/uploads/11/30417e9a0abc07120ef0f6d2dc34b4e8.png', 'damor@cosmax.com', '2026-05-20 19:23:40', NULL, NULL),
(41, 13, 'Vacuum Test', 'e8f72446103bf96ca168bb1f6d166e8f.png', '/uploads/13/e8f72446103bf96ca168bb1f6d166e8f.png', 'staff@local.test', '2026-05-21 08:51:35', NULL, NULL),
(42, 13, 'Press Test', 'c5408a94b633820b48d11b63f6b18e51.png', '/uploads/13/c5408a94b633820b48d11b63f6b18e51.png', 'staff@local.test', '2026-05-21 08:51:47', NULL, NULL),
(43, 13, 'Filling Process', 'ca90aa215ae482ca3d578997f0543e15.png', '/uploads/13/ca90aa215ae482ca3d578997f0543e15.png', 'staff@local.test', '2026-05-21 08:51:56', NULL, NULL),
(44, 13, 'Final Product', '9ca60496dfb723570d3cb960c8b54a57.png', '/uploads/13/9ca60496dfb723570d3cb960c8b54a57.png', 'staff@local.test', '2026-05-21 08:52:07', NULL, NULL),
(45, 13, 'Machine Setting', '4d15b5f818d416d2d5cabab5bcb915b3.png', '/uploads/13/4d15b5f818d416d2d5cabab5bcb915b3.png', 'staff@local.test', '2026-05-21 08:52:14', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `department` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `department`, `is_active`, `created_at`, `deleted_at`, `deleted_by`) VALUES
(1, 'Administrator', 'admin@local.test', '$2y$12$QXyAdZWdXDfQYayyz0QuFezwMpWERajpG3TjAy8jxOF79wfx4JqZS', 'Admin', 'Admin', 1, '2026-05-06 02:37:02', NULL, NULL),
(2, 'Staff Trial', 'staff@local.test', '$2y$12$MPeeEpBmeyT6Zz6vzbzmWeMGwmgieT5WcTsqALpjbCo1kdEhk5ov6', 'Staff', 'Staff', 1, '2026-05-06 02:37:02', NULL, NULL),
(3, 'Reviewer PRD', 'prd@local.test', '$2y$12$cIX/pQdFvjaRweo2D7bvw.jT/tElEDpPKG58SODoXLNQt5omlVtOW', 'PRD', 'PRD', 1, '2026-05-06 02:37:02', NULL, NULL),
(4, 'Reviewer RNI', 'rni@local.test', '$2y$12$Rt0SddSMcd860oBVYa8xjux1XgEntFWAvaTbp6zBw1axRfH6JkY1.', 'RNI', 'RNI', 1, '2026-05-06 02:37:02', NULL, NULL),
(5, 'Reviewer QAC', 'qac@local.test', '$2y$12$yKi55zChMgu4Jvl5iuINE.xVrVAUc4.KjUavg16KiBaIrVuY2bGi2', 'QAC', 'QAC', 1, '2026-05-06 02:37:02', NULL, NULL),
(6, 'Reviewer PRNI', 'prni@local.test', '$2y$12$rH3nbuhoxxCmWULchYQgEeEk9y3rB9yp8y60emdLnq4ocuKSHPrde', 'PRNI', 'PRNI', 1, '2026-05-06 02:37:02', NULL, NULL),
(7, 'Reviewer PI', 'pi@local.test', '$2y$12$O/FvAjC0TmrZNrGvvZbXKefgO3Xw5b72t9JyYJ/o3eqa3M3BI.kGu', 'PI', 'PI', 1, '2026-05-06 02:37:02', NULL, NULL),
(8, 'Manager QAC', 'manager.qac@local.test', '$2y$12$GmHDNV7ulyhEQMqi0uBXQenrMyDK.qLyewMCv1ngY2mBXzdLBNzpm', 'Manager QAC', 'QAC', 1, '2026-05-06 02:37:02', NULL, NULL),
(9, 'damor', 'damor@cosmax.com', '$2y$10$.neRNsRG1aBr/kuyunrrTO4U7XVo0okxQ6Ne37gZGE5fKFPcYHkSS', 'Admin', 'IT', 1, '2026-05-06 02:45:24', NULL, NULL),
(10, 'Dian', 'dian.andrian@cosmax.com', '$2y$10$XqeIaD9e.8yqAZKirSsyze/HBkDBguA8xdul8w2CFflU29uWVISSe', 'PRD', 'PRD', 1, '2026-05-06 04:13:25', NULL, NULL),
(11, 'Pinto', 'pinto@cosmax.com', '$2y$10$kmxPOOT5S1583wFRBB7KReboclxbe7hXLc0DPfQsWCg13TC5f2xNy', 'Staff', 'RNI', 1, '2026-05-07 02:55:12', NULL, NULL),
(12, 'Lomo', 'lomoniroha@cosma.com', '$2y$10$640hine4z1QLTkvUNwfK6OTybh9lIrd7Nb0ceUwbdLTVZpGCy4TCO', 'Staff', 'IT', 0, '2026-05-08 07:57:24', '2026-05-08 14:58:19', 9),
(13, 'Lomo', 'lomoniroha@cosmax.com', '$2y$10$Ij58cucicCuHUs7VWyu2FeOfSo.xHjb4BG7SGGE5j5dFsluFgxl1.', 'Staff', 'IT', 1, '2026-05-08 07:58:34', NULL, NULL),
(14, 'Fauzi', 'fauzi@cosmax.com', '$2y$10$v2NcbrMYVZLeGLoRl8n8Cuk3Y4TNmD2XEaLtf/iMvZdGn9SyMhbPe', 'Staff', 'QA', 1, '2026-05-08 09:38:09', NULL, NULL),
(15, 'Hanbi', 'hanbi@cosmax.com', '$2y$10$ZuLGZ7HFZRcA4tKuqgJ1BeQkg/F8EQMO55is0FHKkH3NmlfZUmB4e', 'Staff', 'QA', 1, '2026-05-08 09:38:32', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `validation_parameters`
--

CREATE TABLE `validation_parameters` (
  `id` int(11) NOT NULL,
  `product_type` varchar(100) NOT NULL,
  `parameter_name` varchar(200) NOT NULL,
  `specification` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `validation_parameters`
--

INSERT INTO `validation_parameters` (`id`, `product_type`, `parameter_name`, `specification`, `sort_order`, `is_active`, `deleted_at`, `deleted_by`) VALUES
(1, 'Tube', 'Appearance', 'Clean, no scratch, no skew. Shoulder skew <= 3 mm', 1, 1, NULL, NULL),
(2, 'Tube', 'Coding result', 'Clear, readable, resistance', 2, 1, NULL, NULL),
(3, 'Tube', 'Shrink result', 'Appearance same with existing, easy to assembly', 3, 1, NULL, NULL),
(4, 'Tube', 'Filling Volume', 'Fill volume/weight meets target specification', 4, 1, NULL, NULL),
(5, 'Hotpouring', 'Appearance', 'Clean, no scratch, surface acceptable', 1, 1, NULL, NULL),
(6, 'Hotpouring', 'Filling Volume', 'Fill volume/weight meets target specification', 2, 1, NULL, NULL),
(7, 'Hotpouring', 'Cooling Result', 'No deformation after cooling', 3, 1, NULL, NULL),
(8, 'Sachet', 'Appearance', 'No leak, seal clean, coding readable', 1, 1, NULL, NULL),
(9, 'Sachet', 'Filling Volume', 'Fill volume/weight meets target specification', 2, 1, NULL, NULL),
(10, 'Mixing', 'Appearance', 'Bulk condition acceptable', 1, 1, NULL, NULL),
(11, 'Mixing', 'Homogeneity', 'Mixing result homogeneous', 2, 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_created` (`created_at`),
  ADD KEY `idx_activity_user` (`user_id`),
  ADD KEY `idx_activity_action` (`action`),
  ADD KEY `idx_activity_module` (`module`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_trial` (`trial_id`),
  ADD KEY `idx_audit_action` (`action`);

--
-- Indexes for table `master_options`
--
ALTER TABLE `master_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_type_name` (`type`,`name`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user_read` (`user_id`,`is_read`,`created_at`),
  ADD KEY `idx_notifications_role_dept` (`role_target`,`department_target`,`is_read`,`created_at`),
  ADD KEY `idx_notifications_trial` (`trial_id`);

--
-- Indexes for table `notification_user_status`
--
ALTER TABLE `notification_user_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_notification_user` (`notification_id`,`user_id`),
  ADD KEY `idx_notification_user_visible` (`user_id`,`is_removed`,`is_read`,`created_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_name` (`product_name`);

--
-- Indexes for table `trials_header`
--
ALTER TABLE `trials_header`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trial_code` (`trial_code`),
  ADD KEY `fk_trial_product` (`product_id`);

--
-- Indexes for table `trials_results`
--
ALTER TABLE `trials_results`
  ADD PRIMARY KEY (`trial_id`,`parameter_id`),
  ADD KEY `fk_result_param` (`parameter_id`);

--
-- Indexes for table `trials_review`
--
ALTER TABLE `trials_review`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_review_round` (`trial_id`,`department`,`review_round`),
  ADD KEY `idx_review_status` (`trial_id`,`review_round`,`status`);

--
-- Indexes for table `trials_weighing`
--
ALTER TABLE `trials_weighing`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_weigh` (`trial_id`,`section`,`item_no`);

--
-- Indexes for table `trial_attachment_files`
--
ALTER TABLE `trial_attachment_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_attach_trial` (`trial_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `validation_parameters`
--
ALTER TABLE `validation_parameters`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=252;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

--
-- AUTO_INCREMENT for table `master_options`
--
ALTER TABLE `master_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `notification_user_status`
--
ALTER TABLE `notification_user_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `trials_header`
--
ALTER TABLE `trials_header`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `trials_review`
--
ALTER TABLE `trials_review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `trials_weighing`
--
ALTER TABLE `trials_weighing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2291;

--
-- AUTO_INCREMENT for table `trial_attachment_files`
--
ALTER TABLE `trial_attachment_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `validation_parameters`
--
ALTER TABLE `validation_parameters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_trial` FOREIGN KEY (`trial_id`) REFERENCES `trials_header` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_trial` FOREIGN KEY (`trial_id`) REFERENCES `trials_header` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_user_status`
--
ALTER TABLE `notification_user_status`
  ADD CONSTRAINT `fk_notification_status_notification` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_notification_status_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trials_header`
--
ALTER TABLE `trials_header`
  ADD CONSTRAINT `fk_trial_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `trials_results`
--
ALTER TABLE `trials_results`
  ADD CONSTRAINT `fk_result_param` FOREIGN KEY (`parameter_id`) REFERENCES `validation_parameters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_result_trial` FOREIGN KEY (`trial_id`) REFERENCES `trials_header` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trials_review`
--
ALTER TABLE `trials_review`
  ADD CONSTRAINT `fk_review_trial` FOREIGN KEY (`trial_id`) REFERENCES `trials_header` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trials_weighing`
--
ALTER TABLE `trials_weighing`
  ADD CONSTRAINT `fk_weigh_trial` FOREIGN KEY (`trial_id`) REFERENCES `trials_header` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trial_attachment_files`
--
ALTER TABLE `trial_attachment_files`
  ADD CONSTRAINT `fk_attach_trial` FOREIGN KEY (`trial_id`) REFERENCES `trials_header` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
