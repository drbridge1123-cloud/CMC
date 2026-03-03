<?php
/**
 * POST /api/mbr
 * Create MBR report for a case, auto-populating provider lines
 */
$userId = requireAuth();
requirePermission('mbr');

$input = getInput();

$caseId = (int)($_GET['id'] ?? $input['case_id'] ?? 0);
if (!$caseId) errorResponse('case_id is required');

// Verify case exists
$case = dbFetchOne("SELECT id FROM cases WHERE id = ?", [$caseId]);
if (!$case) errorResponse('Case not found', 404);

// Check for existing report (case_id is UNIQUE)
$existing = dbFetchOne("SELECT id FROM mbr_reports WHERE case_id = ?", [$caseId]);
if ($existing) successResponse(['id' => $existing['id']], 'MBR report already exists');

$reportId = dbInsert('mbr_reports', [
    'case_id'                 => $caseId,
    'pip1_name'               => sanitizeString($input['pip1_name'] ?? ''),
    'pip2_name'               => sanitizeString($input['pip2_name'] ?? ''),
    'health1_name'            => sanitizeString($input['health1_name'] ?? ''),
    'health2_name'            => sanitizeString($input['health2_name'] ?? ''),
    'health3_name'            => sanitizeString($input['health3_name'] ?? ''),
    'has_wage_loss'           => 0,
    'has_essential_service'   => 0,
    'has_health_subrogation'  => 0,
    'has_health_subrogation2' => 0,
    'status'                  => 'draft',
    'notes'                   => sanitizeString($input['notes'] ?? ''),
]);

// Auto-populate provider lines from case_providers
$providers = dbFetchAll(
    "SELECT cp.id AS cp_id, p.name AS provider_name
     FROM case_providers cp
     JOIN providers p ON cp.provider_id = p.id
     WHERE cp.case_id = ?
     ORDER BY cp.id ASC",
    [$caseId]
);

foreach ($providers as $idx => $prov) {
    dbInsert('mbr_lines', [
        'report_id'        => $reportId,
        'line_type'        => 'provider',
        'provider_name'    => $prov['provider_name'],
        'case_provider_id' => $prov['cp_id'],
        'sort_order'       => $idx + 1,
    ]);
}

logActivity($userId, 'create', 'mbr_report', $reportId, [
    'case_id'        => $caseId,
    'provider_lines' => count($providers),
]);

successResponse(['id' => $reportId], 'MBR report created successfully');
