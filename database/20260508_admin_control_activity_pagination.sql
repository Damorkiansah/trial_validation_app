USE trial_validation_system;

ALTER TABLE notifications
  ADD COLUMN IF NOT EXISTS removed_by_user TINYINT(1) NOT NULL DEFAULT 0 AFTER is_read,
  ADD COLUMN IF NOT EXISTS removed_at DATETIME NULL AFTER removed_by_user;

CREATE TABLE IF NOT EXISTS notification_user_status (
 id INT AUTO_INCREMENT PRIMARY KEY,
 notification_id INT NOT NULL,
 user_id INT NOT NULL,
 is_read TINYINT(1) NOT NULL DEFAULT 0,
 read_at DATETIME NULL,
 is_removed TINYINT(1) NOT NULL DEFAULT 0,
 removed_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_notification_user(notification_id,user_id),
 KEY idx_notification_user_visible(user_id,is_removed,is_read,created_at),
 CONSTRAINT fk_notification_status_notification FOREIGN KEY(notification_id) REFERENCES notifications(id) ON DELETE CASCADE,
 CONSTRAINT fk_notification_status_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS activity_logs (
 id BIGINT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NULL,
 user_name VARCHAR(150) NULL,
 user_role VARCHAR(80) NULL,
 action VARCHAR(80) NOT NULL,
 module VARCHAR(80) NOT NULL,
 record_id VARCHAR(80) NULL,
 record_label VARCHAR(255) NULL,
 old_data LONGTEXT NULL,
 new_data LONGTEXT NULL,
 ip_address VARCHAR(64) NULL,
 user_agent VARCHAR(255) NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 KEY idx_activity_created(created_at),
 KEY idx_activity_user(user_id),
 KEY idx_activity_action(action),
 KEY idx_activity_module(module)
);

ALTER TABLE trials_header
  ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL AFTER created_at,
  ADD COLUMN IF NOT EXISTS deleted_by INT NULL AFTER deleted_at;

ALTER TABLE trial_attachment_files
  ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL AFTER created_at,
  ADD COLUMN IF NOT EXISTS deleted_by INT NULL AFTER deleted_at;

ALTER TABLE products
  ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL AFTER is_active,
  ADD COLUMN IF NOT EXISTS deleted_by INT NULL AFTER deleted_at;

ALTER TABLE validation_parameters
  ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL AFTER is_active,
  ADD COLUMN IF NOT EXISTS deleted_by INT NULL AFTER deleted_at;

ALTER TABLE master_options
  ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL AFTER is_active,
  ADD COLUMN IF NOT EXISTS deleted_by INT NULL AFTER deleted_at;

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL AFTER created_at,
  ADD COLUMN IF NOT EXISTS deleted_by INT NULL AFTER deleted_at;
