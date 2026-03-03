<?php
/**
 * PUT /api/mbr/{id}/approve
 * Approve a completed MBR report
 */
$userId = requireAuth();
requirePermission('mbr');

$id = (int)$_GET['id'];

$report = dbFetchOne("SELECT id, status FROM mbr_reports WHERE id = ?", [$id]);
if (!$report) errorResponse('MBR report not found', 404);

if ($report['status'] !== 'completed') {
    errorResponse('Only completed reports can be approved');
}

dbUpdate('mbr_reports', [
    'status'      => 'approved',
    'approved_by' => $userId,
    'approved_at' => date('Y-m-d H:i:s'),
], 'id = ?', [$id]);

logActivity($userId, 'approve', 'mbr_report', $id);

successResponse(null, 'MBR report approved');
