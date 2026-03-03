<?php
/**
 * GET /api/attorney/transfer-history?case_id=123
 * Get transfer history for an attorney case
 */
$userId = requireAuth();
requirePermission('attorney_cases');

$caseId = (int)($_GET['case_id'] ?? 0);
if (!$caseId) errorResponse('case_id is required');

$transfers = dbFetchAll("
    SELECT t.*,
           fu.full_name AS from_full_name, fu.display_name AS from_display_name,
           tu.full_name AS to_full_name, tu.display_name AS to_display_name,
           bu.full_name AS by_full_name, bu.display_name AS by_display_name
    FROM attorney_case_transfers t
    LEFT JOIN users fu ON t.from_attorney_id = fu.id
    LEFT JOIN users tu ON t.to_attorney_id = tu.id
    LEFT JOIN users bu ON t.transferred_by = bu.id
    WHERE t.attorney_case_id = ?
    ORDER BY t.transferred_at DESC
", [$caseId]);

// Format names to use display_name
foreach ($transfers as &$t) {
    $t['from_name'] = $t['from_display_name'] ?: $t['from_full_name'];
    $t['to_name'] = $t['to_display_name'] ?: $t['to_full_name'];
    $t['by_name'] = $t['by_display_name'] ?: $t['by_full_name'];
}
unset($t);

successResponse($transfers);
