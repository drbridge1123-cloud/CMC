<?php
/**
 * GET /api/provider-negotiations?case_id=X
 * List provider negotiations for a case
 */
$userId = requireAuth();
requirePermission('cases');

$caseId = (int)($_GET['case_id'] ?? 0);
if (!$caseId) errorResponse('case_id is required');

$rows = dbFetchAll(
    "SELECT pn.*,
            ml.balance AS mbr_balance,
            ml.charges AS mbr_charges
     FROM provider_negotiations pn
     LEFT JOIN mbr_lines ml ON pn.mbr_line_id = ml.id
     WHERE pn.case_id = ?
     ORDER BY pn.provider_name ASC",
    [$caseId]
);

// Cast numeric fields
foreach ($rows as &$r) {
    $r['original_balance']    = $r['original_balance'] !== null ? (float)$r['original_balance'] : null;
    $r['requested_reduction'] = $r['requested_reduction'] !== null ? (float)$r['requested_reduction'] : null;
    $r['accepted_amount']     = $r['accepted_amount'] !== null ? (float)$r['accepted_amount'] : null;
    $r['reduction_percent']   = $r['reduction_percent'] !== null ? (float)$r['reduction_percent'] : null;
    $r['mbr_balance']        = $r['mbr_balance'] !== null ? (float)$r['mbr_balance'] : null;
    $r['mbr_charges']        = $r['mbr_charges'] !== null ? (float)$r['mbr_charges'] : null;
}
unset($r);

successResponse($rows);
