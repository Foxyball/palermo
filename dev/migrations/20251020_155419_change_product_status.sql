-- Migration: 20251020_155419_change_product_status.sql
-- Created: 2025-10-20 15:54:19
-- Description: change-product-status
START TRANSACTION;

-- write SQL below this line
ALTER TABLE `products`
    CHANGE COLUMN `status` `active` ENUM('0','1') NOT NULL DEFAULT '1';

COMMIT;
