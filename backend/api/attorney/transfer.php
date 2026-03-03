<?php
/**
 * POST /api/attorney/transfer
 * Transfer an attorney case to a different attorney
 */
$userId = requireAuth();
requirePermission('attorney_cases');
$input = getInput();

$errors = validateRequired($input, ['case_id', 'to_attorney_id', 'note']);
if (!empty($errors)) errorResponse(implode(', ', $errors));

$caseId = (int)$input['case_id'];
$toAttorneyId = (int)$input['to_attorney_id'];
$note = sanitizeString($input['note']);

// Validate attorney case
$attCase = dbFetchOne("SELECT * FROM attorney_cases WHERE id = ? AND deleted_at IS NULL", [$caseId]);
if (!$attCase) errorResponse('Case not found', 404);
if ($attCase['status'] === 'closed') errorResponse('Cannot transfer a closed case');

$fromAttorneyId = (int)$attCase['attorney_user_id'];
if ($fromAttorneyId === $toAttorneyId) errorResponse('Case is already assigned to this attorney');

// Validate target attorney
$toAttorney = dbFetchOne("SELECT id, full_name, display_name FROM users WHERE id = ? AND is_active = 1", [$toAttorneyId]);
if (!$toAttorney) errorResponse('Target attorney not found', 404);

$fromAttorney = dbFetchOne("SELECT id, full_name, display_name FROM users WHERE id = ?", [$fromAttorneyId]);

// Determine from_start_date (when the current attorney started working on it)
$lastTransfer = dbFetchOne(
    "SELECT transferred_at FROM attorney_case_transfers WHERE attorney_case_id = ? ORDER BY transferred_at DESC LIMIT 1",
    [$caseId]
);
$fromStartDate = $lastTransfer
    ? date('Y-m-d', strtotime($lastTransfer['transferred_at']))
    : ($attCase['assigned_date'] ?? date('Y-m-d', strtotime($attCase['submitted_at'])));

// Record transfer history
dbInsert('attorney_case_transfers', [
    'attorney_case_id' => $caseId,
    'from_attorney_id' => $fromAttorneyId,
    'to_attorney_id' => $toAttorneyId,
    'note' => $note,
    'transferred_by' => $userId,
    'from_start_date' => $fromStartDate,
]);

// Update attorney assignment
dbUpdate('attorney_cases', [
    'attorney_user_id' => $toAttorneyId,
], 'id = ?', [$caseId]);

$fromName = $fromAttorney['display_name'] ?: $fromAttorney['full_name'];
$toName = $toAttorney['display_name'] ?: $toAttorney['full_name'];

// Notify both attorneys
dbInsert('notifications', [
    'user_id' => $toAttorneyId,
    'type' => 'assignment',
    'message' => "Case {$attCase['case_number']} ({$attCase['client_name']}) transferred to you from {$fromName}: {$note}",
]);
if ($fromAttorneyId !== $userId) {
    dbInsert('notifications', [
        'user_id' => $fromAttorneyId,
        'type' => 'status_change',
        'message' => "Case {$attCase['case_number']} ({$attCase['client_name']}) transferred to {$toName}: {$note}",
    ]);
}

// Activity log
logActivity($userId, 'transfer', 'attorney_case', $caseId, [
    'from_attorney_id' => $fromAttorneyId,
    'from_attorney_name' => $fromName,
    'to_attorney_id' => $toAttorneyId,
    'to_attorney_name' => $toName,
    'note' => $note,
]);

successResponse([
    'from' => $fromName,
    'to' => $toName,
], 'Case transferred successfully');
