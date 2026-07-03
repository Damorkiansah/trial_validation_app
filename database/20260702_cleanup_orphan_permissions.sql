USE trial_validation_system;

-- Cleanup orphan permissions (should be handled by CASCADE but just in case)
DELETE tp FROM trial_edit_permissions tp
LEFT JOIN trials_header h ON h.id = tp.trial_id
WHERE h.id IS NULL;
