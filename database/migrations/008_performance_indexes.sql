-- ============================================================
-- Migration 008: Performance Indexes
-- Composite indexes for common query patterns
-- Safe to run multiple times (IF NOT EXISTS via CREATE INDEX IF NOT EXISTS)
-- ============================================================

-- case_providers: covers 4 correlated subqueries in bl-cases/list.php
ALTER TABLE case_providers ADD INDEX idx_cp_case_status (case_id, overall_status);
ALTER TABLE case_providers ADD INDEX idx_cp_case_deadline (case_id, deadline, overall_status);
ALTER TABLE case_providers ADD INDEX idx_cp_assigned_status (assigned_to, assignment_status);

-- record_requests: covers MAX(id) subquery + followup date filtering
ALTER TABLE record_requests ADD INDEX idx_rr_cp_id (case_provider_id, id);
ALTER TABLE record_requests ADD INDEX idx_rr_cp_followup (case_provider_id, next_followup_date, id);

-- cases: composite for filtered list queries
ALTER TABLE cases ADD INDEX idx_cases_status_assigned (status, assigned_to);

-- accounting_disbursements: GROUP BY aggregation in accounting/list.php
ALTER TABLE accounting_disbursements ADD INDEX idx_acct_disb_case (case_id, status);
ALTER TABLE accounting_disbursements ADD INDEX idx_acct_disb_attorney (attorney_case_id, status);

-- attorney_cases: phase+status filtering
ALTER TABLE attorney_cases ADD INDEX idx_atc_phase_status (phase, status, deleted_at);

-- provider_contacts: batch fetch optimization
ALTER TABLE provider_contacts ADD INDEX idx_pc_provider_primary (provider_id, is_primary);
