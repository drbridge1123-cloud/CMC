<?php
// GET /api/accounting/list
$userId = requireAuth();
requirePermission('accounting_tracker');

$search = sanitizeString($_GET['search'] ?? '');
$assignedTo = $_GET['assigned_to'] ?? '';
$filter = $_GET['filter'] ?? '';
$sortBy = $_GET['sort_by'] ?? 'sent_to_accounting_date';
$sortDir = strtoupper($_GET['sort_dir'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

$allowedSorts = ['case_number', 'client_name', 'settlement_amount', 'sent_to_accounting_date', 'assigned_name', 'days_in_accounting'];
if (!in_array($sortBy, $allowedSorts)) $sortBy = 'sent_to_accounting_date';

// ── Regular cases (from cases table) ──
$caseWhere = "c.status = 'accounting'";
$caseParams = [];

if ($search) {
    $caseWhere .= " AND (c.case_number LIKE ? OR c.client_name LIKE ?)";
    $caseParams[] = "%{$search}%";
    $caseParams[] = "%{$search}%";
}
if ($assignedTo) {
    $caseWhere .= " AND c.assigned_to = ?";
    $caseParams[] = (int)$assignedTo;
}

$caseSql = "SELECT c.id, NULL AS attorney_case_id, 'case' AS source_type,
                   c.case_number, c.client_name, c.client_dob, c.doi,
                   c.assigned_to, c.settlement_amount, c.attorney_fee_percent,
                   c.sent_to_accounting_date, c.closed_date, c.file_location,
                   c.created_at,
                   COALESCE(u.display_name, u.full_name) AS assigned_name,
                   d.disbursement_count, d.total_disbursed, d.pending_count,
                   DATEDIFF(CURDATE(), c.sent_to_accounting_date) AS days_in_accounting
            FROM cases c
            LEFT JOIN users u ON u.id = c.assigned_to
            LEFT JOIN (
                SELECT case_id,
                       COUNT(*) AS disbursement_count,
                       COALESCE(SUM(CASE WHEN status != 'void' THEN amount ELSE 0 END), 0) AS total_disbursed,
                       SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count
                FROM accounting_disbursements
                WHERE case_id IS NOT NULL
                GROUP BY case_id
            ) d ON d.case_id = c.id
            WHERE {$caseWhere}";

// ── Attorney cases (from attorney_cases table) ──
$attWhere = "ac.status = 'accounting' AND ac.deleted_at IS NULL";
$attParams = [];

if ($search) {
    $attWhere .= " AND (ac.case_number LIKE ? OR ac.client_name LIKE ?)";
    $attParams[] = "%{$search}%";
    $attParams[] = "%{$search}%";
}
if ($assignedTo) {
    $attWhere .= " AND ac.accounting_assigned_to = ?";
    $attParams[] = (int)$assignedTo;
}

$attSql = "SELECT ac.case_id AS id, ac.id AS attorney_case_id, 'attorney' AS source_type,
                  ac.case_number, ac.client_name, NULL AS client_dob, NULL AS doi,
                  ac.accounting_assigned_to AS assigned_to, ac.settled AS settlement_amount,
                  NULL AS attorney_fee_percent,
                  ac.sent_to_accounting_date, NULL AS closed_date, NULL AS file_location,
                  ac.submitted_at AS created_at,
                  COALESCE(u2.display_name, u2.full_name) AS assigned_name,
                  ad.disbursement_count, ad.total_disbursed, ad.pending_count,
                  DATEDIFF(CURDATE(), ac.sent_to_accounting_date) AS days_in_accounting
           FROM attorney_cases ac
           LEFT JOIN users u2 ON u2.id = ac.accounting_assigned_to
           LEFT JOIN (
               SELECT attorney_case_id,
                      COUNT(*) AS disbursement_count,
                      COALESCE(SUM(CASE WHEN status != 'void' THEN amount ELSE 0 END), 0) AS total_disbursed,
                      SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count
               FROM accounting_disbursements
               WHERE attorney_case_id IS NOT NULL
               GROUP BY attorney_case_id
           ) ad ON ad.attorney_case_id = ac.id
           WHERE {$attWhere}";

// Combine with UNION ALL
$unionSql = "({$caseSql}) UNION ALL ({$attSql})";
$params = array_merge($caseParams, $attParams);

// Quick filters (applied as WHERE on combined results)
$outerWhere = '1=1';
if ($filter === 'overdue') {
    $outerWhere .= " AND days_in_accounting > " . ACCOUNTING_DISBURSE_DAYS;
} elseif ($filter === 'pending') {
    $outerWhere .= " AND (pending_count > 0 OR COALESCE(disbursement_count, 0) = 0)";
}

// Sorting
$orderCol = $sortBy;
if ($sortBy === 'assigned_name') $orderCol = 'assigned_name';
elseif ($sortBy === 'settlement_amount') $orderCol = 'COALESCE(settlement_amount, 0)';

// Summary stats (across all filtered results, not just current page)
$summaryRow = dbFetchOne(
    "SELECT COUNT(*) AS total,
            SUM(CASE WHEN days_in_accounting > " . ACCOUNTING_DISBURSE_DAYS . " THEN 1 ELSE 0 END) AS overdue,
            SUM(CASE WHEN pending_count > 0 OR COALESCE(disbursement_count, 0) = 0 THEN 1 ELSE 0 END) AS pending,
            COALESCE(SUM(COALESCE(settlement_amount, 0)), 0) AS total_settlement
     FROM ({$unionSql}) AS s_combined WHERE {$outerWhere}",
    $params
);

// Paginated data query
[$page, $perPage, $offset] = getPaginationParams();
$total = (int)$summaryRow['total'];

$sql = "SELECT * FROM ({$unionSql}) AS combined WHERE {$outerWhere} ORDER BY {$orderCol} {$sortDir} LIMIT ? OFFSET ?";
$items = dbFetchAll($sql, array_merge($params, [$perPage, $offset]));

// Compute flags
foreach ($items as &$item) {
    $item['days_in_accounting'] = (int)($item['days_in_accounting'] ?? 0);
    $item['is_overdue'] = $item['days_in_accounting'] > ACCOUNTING_DISBURSE_DAYS;
    $item['disbursement_count'] = (int)($item['disbursement_count'] ?? 0);
    $item['total_disbursed'] = (float)($item['total_disbursed'] ?? 0);
    $item['pending_count'] = (int)($item['pending_count'] ?? 0);
    $item['settlement_amount'] = (float)($item['settlement_amount'] ?? 0);

    if ($item['source_type'] === 'attorney') {
        $item['attorney_fee'] = 0;
    } else {
        $item['attorney_fee'] = round($item['settlement_amount'] * (float)($item['attorney_fee_percent'] ?? 0.3333), 2);
    }
    $item['remaining'] = round($item['settlement_amount'] - $item['total_disbursed'], 2);
}
unset($item);

paginatedResponse($items, $total, $page, $perPage, [
    'summary' => [
        'total' => $total,
        'overdue' => (int)($summaryRow['overdue'] ?? 0),
        'pending' => (int)($summaryRow['pending'] ?? 0),
        'total_settlement' => round((float)$summaryRow['total_settlement'], 2),
    ]
]);
