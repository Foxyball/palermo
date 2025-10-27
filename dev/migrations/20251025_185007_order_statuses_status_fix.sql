-- Migration: 20251025_185007_order_statuses_status_fix.sql
-- Created: 2025-10-25 18:50:07
-- Description: order statuses-status-fix
START TRANSACTION;

ALTER TABLE `order_statuses` 
MODIFY COLUMN `active` ENUM('0', '1') NOT NULL DEFAULT '1';

COMMIT;
