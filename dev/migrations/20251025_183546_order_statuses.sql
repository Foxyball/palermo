-- Migration: 20251025_183546_order_statuses.sql
-- Created: 2025-10-25 18:35:46
-- Description: order statuses
START TRANSACTION;

-- Create order_statuses table
CREATE TABLE `order_statuses` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL,
    `active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default order statuses
INSERT INTO `order_statuses` (`name`, `active`) VALUES
('Pending', 1),
('Confirmed', 1),
('Preparing', 1),
('Ready', 1),
('Out for Delivery', 1),
('Delivered', 1),
('Cancelled', 1);

COMMIT;