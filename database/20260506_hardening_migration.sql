USE trial_validation_system;

ALTER TABLE trials_header
  ADD COLUMN IF NOT EXISTS revision_no INT NOT NULL DEFAULT 0 AFTER final_decision,
  ADD COLUMN IF NOT EXISTS approved_by VARCHAR(150) NULL AFTER revision_no,
  ADD COLUMN IF NOT EXISTS approved_at DATETIME NULL AFTER approved_by,
  ADD COLUMN IF NOT EXISTS rejected_by VARCHAR(150) NULL AFTER approved_at,
  ADD COLUMN IF NOT EXISTS rejected_at DATETIME NULL AFTER rejected_by,
  ADD COLUMN IF NOT EXISTS approval_comment TEXT NULL AFTER rejected_at;

ALTER TABLE trials_review
  ADD COLUMN IF NOT EXISTS review_round INT NOT NULL DEFAULT 1 AFTER department;

ALTER TABLE trials_review ADD UNIQUE KEY IF NOT EXISTS uq_review_round(trial_id,department,review_round);
ALTER TABLE trials_review ADD INDEX IF NOT EXISTS idx_review_status(trial_id,review_round,status);
ALTER TABLE trials_review DROP INDEX IF EXISTS uq_review;

CREATE TABLE IF NOT EXISTS audit_logs (
 id BIGINT AUTO_INCREMENT PRIMARY KEY,
 trial_id INT NULL,
 user_id INT NULL,
 user_email VARCHAR(150) NULL,
 action VARCHAR(100) NOT NULL,
 old_data LONGTEXT NULL,
 new_data LONGTEXT NULL,
 ip_address VARCHAR(64) NULL,
 user_agent VARCHAR(255) NULL,
 created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 KEY idx_audit_trial(trial_id),
 KEY idx_audit_action(action),
 CONSTRAINT fk_audit_trial FOREIGN KEY(trial_id) REFERENCES trials_header(id) ON DELETE SET NULL
);
