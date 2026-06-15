USE trial_validation_system;

ALTER TABLE products
  ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER finish_good_code;

UPDATE products SET is_active=1 WHERE is_active IS NULL;
