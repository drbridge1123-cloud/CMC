<?php
/**
 * GET /api/bl-cases/pending-assignments
 * Returns cases with assignment_status='pending' for the current user
 */
$userId = requireAuth();

$rows = dbFetchAll(
    "SELECT c.id, c.case_number, c.client_name, c.client_dob, c.doi,
            c.status, c.assignment_status, c.created_at, c.attorney_name,
            COALESCE(ab.display_name, ab.full_name) AS assigned_by_name
     FROM cases c
     LEFT JOIN users ab ON ab.id = c.assignment_assigned_by
     WHERE c.assigned_to = ? AND c.assignment_status = 'pending'
     ORDER BY c.created_at DESC",
    [$userId]
);

successResponse($rows);
