-- Migration: Add remember_token columns to users table
-- Date: 2025-11-06
-- Description: Add columns to support "Remember Me" functionality

ALTER TABLE `users` 
ADD COLUMN `remember_token` VARCHAR(64) NULL DEFAULT NULL AFTER `password`,
ADD COLUMN `remember_expires` DATETIME NULL DEFAULT NULL AFTER `remember_token`,
ADD INDEX `idx_remember_token` (`remember_token`);
