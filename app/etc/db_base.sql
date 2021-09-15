
--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `messages`
--

CREATE TABLE `email_notifications` (
  `id` int(11) NOT NULL,
  `from_addr` varchar(320) DEFAULT NULL,
  `from_name` varchar(80) DEFAULT NULL,
  `to_addr` varchar(320) NOT NULL,
  `to_name` varchar(80) DEFAULT NULL,
  `cc_addr` varchar(320) DEFAULT NULL,
  `cc_name` varchar(80) DEFAULT NULL,
  `bcc_addr` varchar(320) DEFAULT NULL,
  `bcc_name` varchar(80) DEFAULT NULL,
  `replyto_addr` varchar(320) DEFAULT NULL,
  `subject` varchar(80) NOT NULL,
  `body` text,
  `sent_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `webhooks`
--

CREATE TABLE `webhooks` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `entity` varchar(50) NOT NULL,
  `op` varchar(10) NOT NULL,
  `conditions` varchar(1024) DEFAULT NULL,
  `callback` varchar(255) NOT NULL,
  `belongs_to` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16;


--
-- Table structure for table `sp_permissions`
--

CREATE TABLE `sp_permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sp_permissions`
--

INSERT INTO `sp_permissions` (`id`, `name`) VALUES
(10, 'fill_all'),
(11, 'grant'),
(9, 'impersonate'),
(7, 'lock'),
(1, 'read_all'),
(3, 'read_all_folders'),
(5, 'read_all_trashcan'),
(8, 'transfer'),
(2, 'write_all'),
(12, 'write_all_collections'),
(4, 'write_all_folders'),
(6, 'write_all_trashcan');


ALTER TABLE `sp_permissions` ADD PRIMARY KEY(`id`);

--
-- AUTO_INCREMENT for table `sp_permissions`
--
ALTER TABLE `sp_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Indexes for table `email_notifications`
--
ALTER TABLE `email_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);


--
-- Indexes for table `webhooks`
--
ALTER TABLE `webhooks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


--
-- AUTO_INCREMENT for table `webhooks`
--
ALTER TABLE `webhooks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


--
-- AUTO_INCREMENT for table `email_notifications`
--
ALTER TABLE `email_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


