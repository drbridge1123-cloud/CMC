<?php
/**
 * POST /api/attorney/send-to-billing-final
 * Send settled attorney case to billing for final balance checkup
 */
$userId = requireAuth();
requirePermission('attorney_cases');
$input = getInput();

$errors = validateRequired($input, ['case_id', 'assigned_to']);
if (!empty($errors)) errorResponse(implode(', ', $errors));

$caseId = (int)$input['case_id'];
$assignedTo = (int)$input['assigned_to'];
$note = isset($input['note']) ? sanitizeString($input['note']) : '';

// Validate attorney case
$attCase = dbFetchOne("SELECT * FROM attorney_cases WHERE id = ? AND deleted_at IS NULL", [$caseId]);
if (!$attCase) errorResponse('Case not found', 404);
if ($attCase['phase'] !== 'settled') errorResponse('Case must be in settled phase');
if ($attCase['status'] === 'billing_review') errorResponse('Case is already in billing review');
if ($attCase['status'] === 'accounting') errorResponse('Case is already in accounting');
if ($attCase['status'] === 'closed') errorResponse('Case is already closed');

// Validate assigned user exists
$assignee = dbFetchOne("SELECT id, COALESCE(display_name, full_name) AS full_name FROM users WHERE id = ?", [$assignedTo]);
if (!$assignee) errorResponse('Assigned user not found', 404);

$today = date('Y-m-d');

dbTransaction(function() use ($caseId, $today, $assignedTo, $userId, $attCase, $note) {
    // Update attorney case
    dbUpdate('attorney_cases', [
        'status' => 'billing_review',
        'sent_to_billing_final_date' => $today,
        'billing_final_assigned_to' => $assignedTo,
    ], 'id = ?', [$caseId]);

    // Notification
    dbInsert('notifications', [
        'user_id' => $assignedTo,
        'type' => 'status_change',
        'message' => "Attorney case {$attCase['case_number']} ({$attCase['client_name']}) sent for billing final review" . ($note ? ": {$note}" : ''),
    ]);

    // Activity log
    logActivity($userId, 'send_to_billing_final', 'attorney_case', $caseId, [
        'assigned_to' => $assignedTo,
        'note' => $note,
    ]);
});

successResponse([], 'Sent to billing for final balance checkup');
