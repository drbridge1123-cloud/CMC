<?php
/**
 * PUT /api/bl-cases/{id}/respond-assignment
 * Accept or decline a case assignment
 */
$userId = requireAuth();

$caseId = (int)($_GET['id'] ?? 0);
if (!$caseId) errorResponse('Case ID is required', 400);

$input = getInput();
$action = $input['action'] ?? '';
if (!in_array($action, ['accept', 'decline'])) {
    errorResponse('Action must be accept or decline', 400);
}

$case = dbFetchOne("SELECT * FROM cases WHERE id = ?", [$caseId]);
if (!$case) errorResponse('Case not found', 404);

if ($case['assignment_status'] !== 'pending') {
    errorResponse('This assignment is not pending', 400);
}

if ((int)$case['assigned_to'] !== $userId) {
    errorResponse('You are not assigned to this case', 403);
}

$currentUser = dbFetchOne("SELECT COALESCE(display_name, full_name) AS full_name FROM users WHERE id = ?", [$userId]);

if ($action === 'accept') {
    dbUpdate('cases', [
        'assignment_status' => 'accepted',
    ], 'id = ?', [$caseId]);

    // Notify the assigner
    if ($case['assignment_assigned_by']) {
        dbInsert('notifications', [
            'user_id' => (int)$case['assignment_assigned_by'],
            'type'    => 'case_assignment_accepted',
            'message' => "{$currentUser['full_name']} accepted case {$case['case_number']} ({$case['client_name']})",
            'is_read' => 0,
        ]);
    }

    logActivity($userId, 'case_assignment_accepted', 'case', $caseId, [
        'case_number' => $case['case_number'],
    ]);

    successResponse(['status' => 'accepted'], 'Assignment accepted');

} else {
    $reason = trim($input['reason'] ?? '');
    if (!$reason) {
        errorResponse('Decline reason is required', 400);
    }

    dbUpdate('cases', [
        'assignment_status'          => 'declined',
        'assigned_to'                => null,
        'assignment_declined_reason' => $reason,
    ], 'id = ?', [$caseId]);

    // Notify the assigner
    if ($case['assignment_assigned_by']) {
        dbInsert('notifications', [
            'user_id' => (int)$case['assignment_assigned_by'],
            'type'    => 'case_assignment_declined',
            'message' => "{$currentUser['full_name']} declined case {$case['case_number']} ({$case['client_name']}). Reason: {$reason}",
            'is_read' => 0,
        ]);
    }

    logActivity($userId, 'case_assignment_declined', 'case', $caseId, [
        'case_number' => $case['case_number'],
        'reason'      => $reason,
    ]);

    successResponse(['status' => 'declined'], 'Assignment declined');
}
