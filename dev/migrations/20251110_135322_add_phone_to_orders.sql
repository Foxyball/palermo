-- Migration: 20251110_135322_add_phone_to_orders.sql
-- Created: 2025-11-10 13:53:22
-- Description: add phone to orders
START TRANSACTION;

ALTER TABLE orders ADD COLUMN order_phone VARCHAR(20) NOT NULL AFTER order_address;
UPDATE orders SET order_phone = "08848800809" WHERE order_phone = "";

COMMIT;
