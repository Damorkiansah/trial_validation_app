USE trial_validation_system;

ALTER TABLE trials_review
  ADD COLUMN IF NOT EXISTS is_required TINYINT(1) NOT NULL DEFAULT 1 AFTER status;

CREATE TABLE IF NOT EXISTS notifications (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NULL,
 role_target VARCHAR(50) NULL,
 department_target VARCHAR(50) NULL,
 trial_id INT NULL,
 title VARCHAR(200) NOT NULL,
 message TEXT NOT NULL,
 type VARCHAR(40) NOT NULL DEFAULT 'info',
 is_read TINYINT(1) NOT NULL DEFAULT 0,
 read_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 KEY idx_notifications_user_read(user_id,is_read,created_at),
 KEY idx_notifications_role_dept(role_target,department_target,is_read,created_at),
 KEY idx_notifications_trial(trial_id),
 CONSTRAINT fk_notifications_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_notifications_trial FOREIGN KEY(trial_id) REFERENCES trials_header(id) ON DELETE CASCADE
);
