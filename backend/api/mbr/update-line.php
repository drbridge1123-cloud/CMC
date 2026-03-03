<?php
/**
 * PUT /api/mbr/{id}/update-line
 * Update an MBR line item with auto-balance calculation
 */
$userId = requireAuth();
requirePermission('mbr');

$input = getInput();

$lineId = (int)($input['line_id'] ?? $_GET['line_id'] ?? 0);
if (!$lineId) errorResponse('line_id is required');

$line = dbFetchOne("SELECT * FROM mbr_lines WHERE id = ?", [$lineId]);
if (!$line) errorResponse('Line not found', 404);

// Verify parent report exists
$report = dbFetchOne("SELECT id FROM mbr_reports WHERE id = ?", [$line['report_id']]);
if (!$report) errorResponse('MBR report not found', 404);

$allowedFields = [
    'provider_name', 'charges', 'pip1_amount', 'pip2_amount',
    'health1_amount', 'health2_amount', 'health3_amount', 'discount',
    'office_paid', 'client_paid', 'treatment_dates', 'visits',
    'note', 'record_types_needed', 'ini_status', 'sort_order', 'line_type'
];

$data = [];
$financialFields = ['charges', 'pip1_amount', 'pip2_amount', 'health1_amount',
    'health2_amount', 'health3_amount', 'discount', 'office_paid', 'client_paid'];

foreach ($allowedFields as $field) {
    if (!array_key_exists($field, $input)) continue;
    if (in_array($field, $financialFields)) {
        $data[$field] = (float)$input[$field];
    } elseif ($field === 'sort_order') {
        $data[$field] = (int)$input[$field];
    } elseif ($field === 'ini_status') {
        $data[$field] = $input[$field] === 'complete' ? 'complete' : 'pending';
    } else {
        $data[$field] = sanitizeString($input[$field]);
    }
}

if (empty($data)) errorResponse('No fields to update');

// Auto-calculate balance from merged values
$merged = array_merge($line, $data);
$data['balance'] = (float)$merged['charges']
    - (float)$merged['pip1_amount'] - (float)$merged['pip2_amount']
    - (float)$merged['health1_amount'] - (float)$merged['health2_amount']
    - (float)$merged['health3_amount'] - (float)$merged['discount']
    - (float)$merged['office_paid'] - (float)$merged['client_paid'];

dbUpdate('mbr_lines', $data, 'id = ?', [$lineId]);

$updated = dbFetchOne("SELECT * FROM mbr_lines WHERE id = ?", [$lineId]);

successResponse($updated, 'Line updated successfully');
