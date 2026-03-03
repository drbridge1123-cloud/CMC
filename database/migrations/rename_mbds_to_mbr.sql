-- Migration: Rename mbds tables/columns to mbr (Medical Balance Report)
-- Run this ONCE on existing databases

-- 1. Rename tables
RENAME TABLE mbds_reports TO mbr_reports;
RENAME TABLE mbds_lines TO mbr_lines;

-- 2. Rename foreign key column in provider_negotiations
ALTER TABLE provider_negotiations DROP FOREIGN KEY provider_negotiations_ibfk_3;
ALTER TABLE provider_negotiations CHANGE mbds_line_id mbr_line_id INT NULL;
ALTER TABLE provider_negotiations ADD CONSTRAINT provider_negotiations_ibfk_3
    FOREIGN KEY (mbr_line_id) REFERENCES mbr_lines(id) ON DELETE SET NULL;

-- 3. Rename indexes
ALTER TABLE mbr_reports DROP INDEX idx_mbds_reports_case;
ALTER TABLE mbr_reports ADD INDEX idx_mbr_reports_case (case_id);
ALTER TABLE mbr_reports DROP INDEX idx_mbds_reports_status;
ALTER TABLE mbr_reports ADD INDEX idx_mbr_reports_status (status);

ALTER TABLE mbr_lines DROP INDEX idx_mbds_lines_report;
ALTER TABLE mbr_lines ADD INDEX idx_mbr_lines_report (report_id);

-- 4. Update permission references in users table (JSON permissions field)
UPDATE users SET permissions = REPLACE(permissions, '"mbds"', '"mbr"') WHERE permissions LIKE '%"mbds"%';
