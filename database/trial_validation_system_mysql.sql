CREATE DATABASE IF NOT EXISTS trial_validation_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE trial_validation_system;

DROP TABLE IF EXISTS trial_attachment_files;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS trials_review;
DROP TABLE IF EXISTS trials_weighing;
DROP TABLE IF EXISTS trials_results;
DROP TABLE IF EXISTS validation_parameters;
DROP TABLE IF EXISTS trials_header;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS master_options;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role VARCHAR(50) NOT NULL,
 department VARCHAR(50) NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 deleted_at DATETIME NULL,
 deleted_by INT NULL
);

CREATE TABLE products (
 id INT AUTO_INCREMENT PRIMARY KEY,
 product_name VARCHAR(200) NOT NULL UNIQUE,
 finish_good_code VARCHAR(100) NOT NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 deleted_at DATETIME NULL,
 deleted_by INT NULL
);

CREATE TABLE master_options (
 id INT AUTO_INCREMENT PRIMARY KEY,
 type VARCHAR(80) NOT NULL,
 name VARCHAR(200) NOT NULL,
 sort_order INT NOT NULL DEFAULT 0,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 deleted_at DATETIME NULL,
 deleted_by INT NULL,
 UNIQUE KEY uq_type_name(type,name)
);

CREATE TABLE trials_header (
 id INT AUTO_INCREMENT PRIMARY KEY,
 trial_code VARCHAR(80) NOT NULL UNIQUE,
 product_id INT NULL,
 product_name VARCHAR(200) NOT NULL,
 finish_good_code VARCHAR(100) NOT NULL,
 product_type VARCHAR(100) NOT NULL,
 validation_date DATE NOT NULL,
 validation_category VARCHAR(120) NOT NULL,
 risk_level VARCHAR(20) NOT NULL,
 validation_scope VARCHAR(200) NOT NULL,
 machine_used VARCHAR(200) NOT NULL,
 estimate_qty DECIMAL(14,2) NOT NULL,
 batch_number VARCHAR(200) NULL,
 bulk_code VARCHAR(200) NULL,
 support_team VARCHAR(200) NULL,
 initiated_person_team VARCHAR(200) NULL,
 reason TEXT NULL,
 bom TEXT NULL,
 current_step VARCHAR(80) NOT NULL DEFAULT 'Validation',
 progress_status VARCHAR(80) NOT NULL DEFAULT 'Draft',
 pending_with VARCHAR(200) NULL,
 final_decision VARCHAR(80) NULL,
 revision_no INT NOT NULL DEFAULT 0,
 approved_by VARCHAR(150) NULL,
 approved_at DATETIME NULL,
 rejected_by VARCHAR(150) NULL,
 rejected_at DATETIME NULL,
 approval_comment TEXT NULL,
 created_by VARCHAR(150) NULL,
 updated_at DATETIME NULL,
 created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 deleted_at DATETIME NULL,
 deleted_by INT NULL,
 CONSTRAINT fk_trial_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE SET NULL
);

CREATE TABLE validation_parameters (
 id INT AUTO_INCREMENT PRIMARY KEY,
 product_type VARCHAR(100) NOT NULL,
 parameter_name VARCHAR(200) NOT NULL,
 specification TEXT NULL,
 sort_order INT NOT NULL DEFAULT 0,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 deleted_at DATETIME NULL,
 deleted_by INT NULL
);

CREATE TABLE trials_results (
 trial_id INT NOT NULL,
 parameter_id INT NOT NULL,
 result_value TEXT NULL,
 decision VARCHAR(20) NULL,
 remark TEXT NULL,
 updated_at DATETIME NULL,
 PRIMARY KEY(trial_id, parameter_id),
 CONSTRAINT fk_result_trial FOREIGN KEY(trial_id) REFERENCES trials_header(id) ON DELETE CASCADE,
 CONSTRAINT fk_result_param FOREIGN KEY(parameter_id) REFERENCES validation_parameters(id) ON DELETE CASCADE
);

CREATE TABLE trials_weighing (
 id INT AUTO_INCREMENT PRIMARY KEY,
 trial_id INT NOT NULL,
 section VARCHAR(30) NOT NULL,
 item_no INT NOT NULL,
 weight_value DECIMAL(12,3) NULL,
 is_skipped TINYINT(1) NOT NULL DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_weigh(trial_id,section,item_no),
 CONSTRAINT fk_weigh_trial FOREIGN KEY(trial_id) REFERENCES trials_header(id) ON DELETE CASCADE
);

CREATE TABLE trials_review (
 id INT AUTO_INCREMENT PRIMARY KEY,
 trial_id INT NOT NULL,
 department VARCHAR(50) NOT NULL,
 review_round INT NOT NULL DEFAULT 1,
 status VARCHAR(30) NOT NULL DEFAULT 'Pending',
 is_required TINYINT(1) NOT NULL DEFAULT 1,
 reviewer_name VARCHAR(120) NULL,
 reviewer_email VARCHAR(150) NULL,
 comment TEXT NULL,
 reviewed_at DATETIME NULL,
 UNIQUE KEY uq_review_round(trial_id,department,review_round),
 KEY idx_review_status(trial_id,review_round,status),
 CONSTRAINT fk_review_trial FOREIGN KEY(trial_id) REFERENCES trials_header(id) ON DELETE CASCADE
);

CREATE TABLE notifications (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NULL,
 role_target VARCHAR(50) NULL,
 department_target VARCHAR(50) NULL,
 trial_id INT NULL,
 title VARCHAR(200) NOT NULL,
 message TEXT NOT NULL,
 type VARCHAR(40) NOT NULL DEFAULT 'info',
 is_read TINYINT(1) NOT NULL DEFAULT 0,
 removed_by_user TINYINT(1) NOT NULL DEFAULT 0,
 removed_at DATETIME NULL,
 read_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 KEY idx_notifications_user_read(user_id,is_read,created_at),
 KEY idx_notifications_role_dept(role_target,department_target,is_read,created_at),
 KEY idx_notifications_trial(trial_id),
 CONSTRAINT fk_notifications_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_notifications_trial FOREIGN KEY(trial_id) REFERENCES trials_header(id) ON DELETE CASCADE
);

CREATE TABLE notification_user_status (
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

CREATE TABLE activity_logs (
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

CREATE TABLE audit_logs (
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

CREATE TABLE trial_attachment_files (
 id INT AUTO_INCREMENT PRIMARY KEY,
 trial_id INT NOT NULL,
 category VARCHAR(120) NOT NULL,
 file_name VARCHAR(255) NOT NULL,
 file_path VARCHAR(255) NOT NULL,
 uploaded_by VARCHAR(150) NULL,
 created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 deleted_at DATETIME NULL,
 deleted_by INT NULL,
 CONSTRAINT fk_attach_trial FOREIGN KEY(trial_id) REFERENCES trials_header(id) ON DELETE CASCADE
);

INSERT INTO users(name,email,password_hash,role,department,is_active) VALUES
('Administrator','admin@local.test','$2y$12$QXyAdZWdXDfQYayyz0QuFezwMpWERajpG3TjAy8jxOF79wfx4JqZS','Admin','Admin',1),
('Staff Trial','staff@local.test','$2y$12$MPeeEpBmeyT6Zz6vzbzmWeMGwmgieT5WcTsqALpjbCo1kdEhk5ov6','Staff','Staff',1),
('Reviewer PRD','prd@local.test','$2y$12$cIX/pQdFvjaRweo2D7bvw.jT/tElEDpPKG58SODoXLNQt5omlVtOW','PRD','PRD',1),
('Reviewer RNI','rni@local.test','$2y$12$Rt0SddSMcd860oBVYa8xjux1XgEntFWAvaTbp6zBw1axRfH6JkY1.','RNI','RNI',1),
('Reviewer QAC','qac@local.test','$2y$12$yKi55zChMgu4Jvl5iuINE.xVrVAUc4.KjUavg16KiBaIrVuY2bGi2','QAC','QAC',1),
('Reviewer PRNI','prni@local.test','$2y$12$rH3nbuhoxxCmWULchYQgEeEk9y3rB9yp8y60emdLnq4ocuKSHPrde','PRNI','PRNI',1),
('Reviewer PI','pi@local.test','$2y$12$O/FvAjC0TmrZNrGvvZbXKefgO3Xw5b72t9JyYJ/o3eqa3M3BI.kGu','PI','PI',1),
('Manager QAC','manager.qac@local.test','$2y$12$GmHDNV7ulyhEQMqi0uBXQenrMyDK.qLyewMCv1ngY2mBXzdLBNzpm','Manager QAC','QAC',1);

INSERT INTO products(product_name,finish_good_code) VALUES
('Sample Tube Product','FG-TUBE-001'),
('Sample Hotpouring Product','FG-HOT-001'),
('Sample Sachet Product','FG-SCT-001');

INSERT INTO master_options(type,name,sort_order,is_active) VALUES
('validation_category','New Product',1,1),('validation_category','Change Process',2,1),('validation_category','Re-validation',3,1),
('validation_scope','Appearance',1,1),('validation_scope','Filling',2,1),('validation_scope','Packaging',3,1),('validation_scope','Full Validation',4,1),
('product_type','Mixing',1,1),('product_type','Tube',2,1),('product_type','Bottle + Screw Cap',3,1),('product_type','Bottle + Screw Pump Cap',4,1),('product_type','Bottle + Airless Pump Cap',5,1),('product_type','Jar + Screw Cap',6,1),('product_type','Jar + Airless Pump Cap',7,1),('product_type','Cushion',8,1),('product_type','Press Powder',9,1),('product_type','Loose Powder',10,1),('product_type','Lipstick',11,1),('product_type','Hotpouring',12,1),('product_type','Sachet',13,1),('product_type','Mask Sheet',14,1),('product_type','Lip Concealer',15,1),('product_type','Mascara',16,1),
('machine_used','Fill JAR',1,1),('machine_used','Fill SINGLE HEAD',2,1),('machine_used','Fill FOUR HEAD',3,1),('machine_used','Fill CUSHION AUTO',4,1),('machine_used','Fill CUSHION MANUAL',5,1),('machine_used','Fill SEMI AUTO',6,1),('machine_used','Fill JIREH 1',7,1),('machine_used','Fill JIREH 2',8,1),('machine_used','Fill TUBE SC 10',9,1),('machine_used','Fill TUBE SC 11',10,1),('machine_used','Fill TUBE SC 12',11,1),('machine_used','Fill POUCH/SACHET 1',12,1),('machine_used','Fill POUCH/SACHET 2',13,1),('machine_used','Fill MASK SHEET 1',14,1),('machine_used','Fill MASK SHEET 2',15,1),('machine_used','Fill SEMI AUTO CONCEALER',16,1),('machine_used','Fill PISTON KOREA 1',17,1),('machine_used','Fill PISTON KOREA 2',18,1),('machine_used','Fill MANUAL WIRPACK',19,1),('machine_used','Fill MANUAL AGA',20,1),('machine_used','Fill MANUAL TAMARU',21,1),('machine_used','Shrink STEAM',22,1),('machine_used','Shrink TUNEL',23,1),('machine_used','Shrink HOTAIR',24,1),('machine_used','Coding INK JET PRINTER 40',25,1),('machine_used','Coding INK JET PRINTER 65',26,1),('machine_used','Coding LASER JET',27,1),('machine_used','Label PACK LEADER',28,1),('machine_used','Label TAMARU',29,1),('machine_used','Label AGA',30,1),('machine_used','Vision System',31,1),('machine_used','Cutter AUTOMATIC-SHRINK',32,1),
('attachment_category','Vacuum Test',1,1),('attachment_category','Press Test',2,1),('attachment_category','Air Pressure Test',3,1),('attachment_category','Filling Process',4,1),('attachment_category','Line Configuration',5,1),('attachment_category','Final Product',6,1),('attachment_category','Machine Setting',7,1);

INSERT INTO validation_parameters(product_type,parameter_name,specification,sort_order,is_active) VALUES
('Tube','Appearance','Clean, no scratch, no skew. Shoulder skew <= 3 mm',1,1),
('Tube','Coding result','Clear, readable, resistance',2,1),
('Tube','Shrink result','Appearance same with existing, easy to assembly',3,1),
('Tube','Filling Volume','Fill volume/weight meets target specification',4,1),
('Hotpouring','Appearance','Clean, no scratch, surface acceptable',1,1),
('Hotpouring','Filling Volume','Fill volume/weight meets target specification',2,1),
('Hotpouring','Cooling Result','No deformation after cooling',3,1),
('Sachet','Appearance','No leak, seal clean, coding readable',1,1),
('Sachet','Filling Volume','Fill volume/weight meets target specification',2,1),
('Mixing','Appearance','Bulk condition acceptable',1,1),
('Mixing','Homogeneity','Mixing result homogeneous',2,1);
