USE trial_validation_system;

UPDATE users SET department='PRD' WHERE role='PRD';
UPDATE users SET department='RNI' WHERE role='RNI';
UPDATE users SET department='QAC' WHERE role='QAC';
UPDATE users SET department='PRNI' WHERE role='PRNI';
UPDATE users SET department='PI' WHERE role='PI';
