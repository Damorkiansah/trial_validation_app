USE trial_validation_system;

ALTER TABLE trials_header
  ADD COLUMN IF NOT EXISTS batch_number VARCHAR(200) NULL AFTER estimate_qty,
  ADD COLUMN IF NOT EXISTS bulk_code VARCHAR(200) NULL AFTER batch_number,
  ADD COLUMN IF NOT EXISTS support_team VARCHAR(200) NULL AFTER bulk_code,
  ADD COLUMN IF NOT EXISTS initiated_person_team VARCHAR(200) NULL AFTER support_team,
  ADD COLUMN IF NOT EXISTS reason TEXT NULL AFTER initiated_person_team,
  ADD COLUMN IF NOT EXISTS bom TEXT NULL AFTER reason;
