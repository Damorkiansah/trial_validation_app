USE trial_validation_system;

ALTER TABLE trials_header
  ADD COLUMN IF NOT EXISTS approver_user_id INT NULL AFTER approved_by;
