-- Migration: 20251027_000000_orders_use_status_id.sql
-- Created: 2025-10-27 00:00:00
-- Description: Update orders table to use status_id referencing order_statuses table
START TRANSACTION;

-- Add new status_id column after user_id
ALTER TABLE `orders` ADD COLUMN `status_id` INT NULL AFTER `user_id`;

-- Update existing orders to use status_id based on current status enum value
UPDATE `orders` o 
SET `status_id` = (
    SELECT os.id FROM `order_statuses` os 
    WHERE LOWER(os.name) = CASE 
        WHEN o.status = 'pending' THEN 'pending'
        WHEN o.status = 'processing' THEN 'confirmed' 
        WHEN o.status = 'completed' THEN 'delivered'
        WHEN o.status = 'canceled' THEN 'cancelled'
        ELSE 'pending'
    END
    LIMIT 1
);

-- Set default status_id to 'Pending' for any NULL values
UPDATE `orders` 
SET `status_id` = (SELECT id FROM `order_statuses` WHERE LOWER(name) = 'pending' LIMIT 1)
WHERE `status_id` IS NULL;

-- Drop the old status enum column
ALTER TABLE `orders` DROP COLUMN `status`;

-- Rename status_id to status
ALTER TABLE `orders` CHANGE COLUMN `status_id` `status_id` INT NOT NULL;

-- Add foreign key constraint
ALTER TABLE `orders` ADD CONSTRAINT `fk_orders_status` 
FOREIGN KEY (`status_id`) REFERENCES `order_statuses`(`id`);

COMMIT;