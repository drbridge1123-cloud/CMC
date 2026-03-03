<?php
/**
 * GET /api/attorney/{id}
 * Get a single attorney case with full details
 */
$userId = requireAuth();
requirePermission('attorney_cases');
$id = (int)$_GET['id'];

$case = dbFetchOne(
    "SELECT ac.*,
            COALESCE(u.display_name, u.full_name) AS attorney_name,
            COALESCE(oa.display_name, oa.full_name) AS top_offer_assignee_name,
            COALESCE(cb.display_name, cb.full_name) AS created_by_name,
            DATEDIFF(ac.demand_deadline, CURDATE()) AS deadline_days_remaining
     FROM attorney_cases ac
     LEFT JOIN users u ON ac.attorney_user_id = u.id
     LEFT JOIN users oa ON ac.top_offer_assignee_id = oa.id
     LEFT JOIN users cb ON ac.created_by = cb.id
     WHERE ac.id = ? AND ac.deleted_at IS NULL",
    [$id]
);

if (!$case) errorResponse('Attorney case not found', 404);

// Permission already checked via requirePermission('attorney_cases')

$case['deadline_days_remaining'] = $case['deadline_days_remaining'] !== null
    ? (int)$case['deadline_days_remaining'] : null;

successResponse($case);
