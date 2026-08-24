USE trial_validation_system;

CREATE TABLE IF NOT EXISTS sso_tickets (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  token CHAR(64) NOT NULL,
  user_id INT NOT NULL,
  direction VARCHAR(20) NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_sso_tickets_token (token),
  KEY idx_sso_tickets_user (user_id, direction),
  CONSTRAINT fk_sso_tickets_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
