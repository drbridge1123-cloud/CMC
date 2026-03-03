<?php
/**
 * PUT /api/bl-cases/{id}/assign
 * Assign (or reassign) a case to a staff member
 */
$userId = requireAuth();
requireAdminOrManager();

$id = (int)$_GET['id'];
$input = getInput();

$errors = validateRequired($input, ['assigned_to']);
if (!empty($errors)) errorResponse(implode(', ', $errors));

$case = dbFetchOne("SELECT * FROM cases WHERE id = ?", [$id]);
if (!$case) errorResponse('Case not found', 404);

$assigneeId = (int)$input['assigned_to'];

$assignee = dbFetchOne("SELECT id, COALESCE(display_name, full_name) AS full_name, is_active FROM users WHERE id = ?", [$assigneeId]);
if (!$assignee) errorResponse('User not found', 404);
if (!$assignee['is_active']) errorResponse('User is not active');

dbUpdate('cases', [
    'assigned_to'                => $assigneeId,
    'assignment_status'          => 'pending',
    'assignment_assigned_by'     => $userId,
    'assignment_declined_reason' => null,
], 'id = ?', [$id]);

// If case is still in collecting, move to prelitigation
if ($case['status'] === 'collecting') {
    dbUpdate('cases', [
        'status' => 'prelitigation',
        'prelitigation_start_date' => date('Y-m-d'),
    ], 'id = ?', [$id]);
}

dbInsert('notifications', [
    'user_id' => $assigneeId,
    'type'    => 'case_assignment',
    'message' => "You have been assigned case {$case['case_number']} ({$case['client_name']}). Please accept or decline.",
    'is_read' => 0,
]);

logActivity($userId, 'case_assign', 'case', $id, [
    'assigned_to'   => $assigneeId,
    'assignee_name' => $assignee['full_name'],
]);

successResponse(null, 'Case assigned');
