-- DemoFlow AI Database Schema
-- Import this file via phpMyAdmin or MySQL CLI

CREATE TABLE IF NOT EXISTS `demos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT 'Untitled Demo',
    `data` longtext NOT NULL,
    `status` enum('draft','published') NOT NULL DEFAULT 'draft',
    `views` int(11) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_status` (`status`),
    KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `analytics_views` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `demo_id` int(11) NOT NULL,
    `ip` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_demo_id` (`demo_id`),
    KEY `idx_created` (`created_at`),
    CONSTRAINT `fk_views_demo` FOREIGN KEY (`demo_id`) REFERENCES `demos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `analytics_clicks` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `demo_id` int(11) NOT NULL,
    `step_index` int(11) NOT NULL DEFAULT 0,
    `pin_index` int(11) NOT NULL DEFAULT 0,
    `ip` varchar(45) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_demo_id` (`demo_id`),
    KEY `idx_created` (`created_at`),
    CONSTRAINT `fk_clicks_demo` FOREIGN KEY (`demo_id`) REFERENCES `demos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `uploads` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `filename` varchar(255) NOT NULL,
    `filepath` varchar(500) NOT NULL,
    `filesize` int(11) NOT NULL DEFAULT 0,
    `mime_type` varchar(100) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
