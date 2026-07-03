USE trial_validation_system;

CREATE TABLE IF NOT EXISTS trial_edit_permissions (
 id BIGINT AUTO_INCREMENT PRIMARY KEY,
 trial_id INT NOT NULL,
 user_id INT NOT NULL,
 can_edit TINYINT(1) NOT NULL DEFAULT 1,
 granted_by INT NULL,
 granted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 revoked_by INT NULL,
 revoked_at DATETIME NULL,
 UNIQUE KEY uq_trial_user_active(trial_id, user_id),
 KEY idx_trial_edit_permissions_user(user_id, can_edit, revoked_at),
 CONSTRAINT fk_trial_edit_permission_trial FOREIGN KEY(trial_id) REFERENCES trials_header(id) ON DELETE CASCADE,
 CONSTRAINT fk_trial_edit_permission_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);
