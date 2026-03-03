<?php
/**
 * DELETE /api/users/{id}
 * Delete a user (admin only)
 */
requireAdmin();
$id = (int)$_GET['id'];

// Prevent self-deletion
if ($id === (int)$_SESSION['user_id']) {
    errorResponse('You cannot delete your own account');
}

$user = dbFetchOne("SELECT id, username, full_name FROM users WHERE id = ?", [$id]);
if (!$user) errorResponse('User not found', 404);

dbDelete('users', 'id = ?', [$id]);

logActivity($_SESSION['user_id'], 'delete', 'user', $id, [
    'username' => $user['username'],
    'full_name' => $user['full_name']
]);

successResponse(null, 'User deleted successfully');
