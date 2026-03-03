<?php
/**
 * POST /api/attorney/import
 * Import attorney cases from CSV file.
 * Matches by case_number: existing → UPDATE, new → INSERT.
 */
$userId = requireAuth();
requireAdmin();
require_once __DIR__ . '/../../helpers/csv.php';

if (empty($_FILES['file'])) {
    errorResponse('No file uploaded', 400);
}

$file = $_FILES['file'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    errorResponse('File upload error', 400);
}

$rows = parseCSV($file['tmp_name']);
if (empty($rows)) {
    errorResponse('No data found in CSV', 400);
}

// Build attorney name → id lookup
$users = dbFetchAll("SELECT id, full_name, display_name FROM users WHERE is_active = 1");
$attorneyMap = [];
foreach ($users as $u) {
    $name = strtolower(trim($u['display_name'] ?: $u['full_name']));
    $attorneyMap[$name] = (int)$u['id'];
    // Also map full_name
    if ($u['full_name']) {
        $attorneyMap[strtolower(trim($u['full_name']))] = (int)$u['id'];
    }
}

// Importable columns (must match export headers, excluding computed fields)
$allowedColumns = [
    'case_number', 'client_name', 'case_type', 'phase', 'status', 'stage',
    'assigned_date', 'demand_deadline', 'demand_out_date', 'negotiate_date',
    'demand_settled_date', 'demand_duration_days',
    'top_offer_amount', 'top_offer_date',
    'litigation_start_date', 'litigation_settled_date', 'litigation_duration_days',
    'presuit_offer', 'resolution_type', 'fee_rate',
    'uim_start_date', 'uim_demand_out_date', 'uim_negotiate_date',
    'uim_settled_date', 'uim_duration_days', 'is_policy_limit',
    'settled', 'difference', 'legal_fee', 'discounted_legal_fee',
    'uim_settled', 'uim_legal_fee', 'uim_discounted_legal_fee',
    'commission', 'commission_type', 'uim_commission',
    'month', 'check_received', 'is_marketing', 'note'
];

$dateColumns = [
    'assigned_date', 'demand_deadline', 'demand_out_date', 'negotiate_date',
    'demand_settled_date', 'top_offer_date',
    'litigation_start_date', 'litigation_settled_date',
    'uim_start_date', 'uim_demand_out_date', 'uim_negotiate_date', 'uim_settled_date'
];

$numericColumns = [
    'demand_duration_days', 'litigation_duration_days', 'uim_duration_days',
    'top_offer_amount', 'presuit_offer', 'fee_rate',
    'settled', 'difference', 'legal_fee', 'discounted_legal_fee',
    'uim_settled', 'uim_legal_fee', 'uim_discounted_legal_fee',
    'commission', 'uim_commission',
    'check_received', 'is_policy_limit', 'is_marketing'
];

$inserted = 0;
$updated = 0;
$skipped = 0;
$errors = [];

foreach ($rows as $i => $row) {
    $lineNum = $i + 2; // +2 because header is line 1, data starts line 2

    $caseNumber = trim($row['case_number'] ?? '');
    if (!$caseNumber) {
        $skipped++;
        continue;
    }

    $clientName = trim($row['client_name'] ?? '');
    if (!$clientName) {
        $errors[] = "Line {$lineNum}: Missing client_name for case {$caseNumber}";
        $skipped++;
        continue;
    }

    // Resolve attorney
    $attorneyName = strtolower(trim($row['attorney_name'] ?? ''));
    $attorneyId = $attorneyMap[$attorneyName] ?? null;
    if (!$attorneyId) {
        // Try partial match
        foreach ($attorneyMap as $name => $id) {
            if (str_contains($name, $attorneyName) || str_contains($attorneyName, $name)) {
                $attorneyId = $id;
                break;
            }
        }
    }
    if (!$attorneyId) {
        $errors[] = "Line {$lineNum}: Unknown attorney '{$row['attorney_name']}' for case {$caseNumber}";
        $skipped++;
        continue;
    }

    // Build data array from allowed columns
    $data = [];
    foreach ($allowedColumns as $col) {
        if (!array_key_exists($col, $row)) continue;
        $val = trim($row[$col]);

        if ($val === '' || $val === '—') {
            // Date and numeric columns → NULL, others → empty string
            if (in_array($col, $dateColumns) || in_array($col, $numericColumns)) {
                $data[$col] = null;
            } else {
                $data[$col] = ($col === 'note') ? null : '';
            }
        } elseif (in_array($col, $numericColumns)) {
            $data[$col] = is_numeric($val) ? $val : null;
        } else {
            $data[$col] = $val;
        }
    }

    // Normalize case_type
    if (!empty($data['case_type'])) {
        $typeMap = ['pi' => 'Auto', 'personal injury' => 'Auto', 'auto' => 'Auto',
                    'pedestrian' => 'Pedestrian', 'motorcycle' => 'Motorcycle',
                    'slip & fall' => 'Slip & Fall', 'slip and fall' => 'Slip & Fall',
                    'dog bite' => 'Dog Bite', 'other' => 'Other'];
        $normalized = $typeMap[strtolower(trim($data['case_type']))] ?? $data['case_type'];
        $data['case_type'] = $normalized;
    }

    $data['attorney_user_id'] = $attorneyId;

    // Check existing
    $existing = dbFetchOne(
        "SELECT id FROM attorney_cases WHERE case_number = ? AND deleted_at IS NULL",
        [$caseNumber]
    );

    try {
        if ($existing) {
            // UPDATE — exclude case_number from SET
            $updateData = $data;
            unset($updateData['case_number']);
            dbUpdate('attorney_cases', $updateData, 'id = ?', [$existing['id']]);
            $updated++;
        } else {
            // INSERT
            $data['created_by'] = $userId;
            dbInsert('attorney_cases', $data);
            $inserted++;
        }
    } catch (Exception $e) {
        $errors[] = "Line {$lineNum}: " . $e->getMessage();
        $skipped++;
    }
}

logActivity($userId, 'import', 'attorney_cases', null, "Imported attorney cases: {$inserted} inserted, {$updated} updated, {$skipped} skipped");

successResponse([
    'inserted' => $inserted,
    'updated'  => $updated,
    'skipped'  => $skipped,
    'errors'   => array_slice($errors, 0, 10) // limit error messages
], "Import complete: {$inserted} inserted, {$updated} updated, {$skipped} skipped");
