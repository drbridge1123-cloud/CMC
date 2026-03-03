<?php
/**
 * POST /api/provider-negotiations/auto-populate
 * Auto-populate provider negotiations from MBR lines
 */
$userId = requireAuth();
requirePermission('cases');
$input = getInput();

$errors = validateRequired($input, ['case_id']);
if (!empty($errors)) errorResponse(implode(', ', $errors));

$caseId = (int)$input['case_id'];

// Get MBR lines with balance > 0 for this case
$lines = dbFetchAll(
    "SELECT ml.id, ml.provider_name, ml.balance, ml.case_provider_id
     FROM mbr_lines ml
     JOIN mbr_reports mr ON ml.report_id = mr.id
     WHERE mr.case_id = ? AND ml.balance > 0",
    [$caseId]
);

$created = 0;

foreach ($lines as $line) {
    // Skip if negotiation already exists for this mbr_line_id
    $exists = dbFetchOne(
        "SELECT id FROM provider_negotiations WHERE case_id = ? AND mbr_line_id = ?",
        [$caseId, $line['id']]
    );
    if ($exists) continue;

    dbInsert('provider_negotiations', [
        'case_id'          => $caseId,
        'case_provider_id' => $line['case_provider_id'],
        'mbr_line_id'     => $line['id'],
        'provider_name'    => $line['provider_name'],
        'original_balance' => (float)$line['balance'],
        'status'           => 'pending',
        'created_by'       => $userId,
    ]);
    $created++;
}

logActivity($userId, 'create', 'provider_negotiation', null, [
    'case_id' => $caseId, 'auto_populated' => $created
]);

successResponse(['created' => $created], "Auto-populated {$created} provider negotiations");
