-- Migration: 20251027_120000_add_order_address_column.sql
-- Created: 2025-10-27 12:00:00
-- Description: Add order_address column to orders table for delivery address information
START TRANSACTION;

-- Add the order_address column to orders table
ALTER TABLE `orders` 
ADD COLUMN `order_address` TEXT NULL AFTER `message`;

COMMIT;