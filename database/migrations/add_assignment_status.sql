-- Add case assignment workflow columns to cases table
ALTER TABLE cases
  ADD COLUMN assignment_status ENUM('unassigned','pending','accepted','declined') DEFAULT 'unassigned' AFTER assigned_to,
  ADD COLUMN assignment_assigned_by INT NULL AFTER assignment_status,
  ADD COLUMN assignment_declined_reason TEXT NULL AFTER assignment_assigned_by;
