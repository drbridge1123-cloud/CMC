<?php
/**
 * PUT /api/mbr/{id}/complete
 * Mark MBR report as completed
 */
$userId = requireAuth();
requirePermission('mbr');

$id = (int)$_GET['id'];

$report = dbFetchOne("SELECT id, status FROM mbr_reports WHERE id = ?", [$id]);
if (!$report) errorResponse('MBR report not found', 404);

if ($report['status'] !== 'draft') {
    errorResponse('Only draft reports can be marked as completed');
}

dbUpdate('mbr_reports', [
    'status'       => 'completed',
    'completed_by' => $userId,
    'completed_at' => date('Y-m-d H:i:s'),
], 'id = ?', [$id]);

logActivity($userId, 'complete', 'mbr_report', $id);

successResponse(null, 'MBR report marked as completed');
