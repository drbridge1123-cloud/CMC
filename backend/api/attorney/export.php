<?php
/**
 * GET /api/attorney/export
 * Export all attorney cases as CSV (full data for backup/import).
 */
$userId = requireAuth();
requirePermission('attorney_cases');
$user = getCurrentUser();
require_once __DIR__ . '/../../helpers/csv.php';

$where = 'ac.deleted_at IS NULL';
$params = [];

// Permission already checked via requirePermission('attorney_cases')

$phase = $_GET['phase'] ?? null;
if ($phase) {
    $where .= ' AND ac.phase = ?';
    $params[] = $phase;
}

$attorneyId = $_GET['attorney_user_id'] ?? null;
if ($attorneyId) {
    $where .= ' AND ac.attorney_user_id = ?';
    $params[] = (int)$attorneyId;
}

$rows = dbFetchAll(
    "SELECT ac.case_number, ac.client_name, ac.case_type, ac.phase, ac.status, ac.stage,
            ac.assigned_date, ac.demand_deadline, ac.demand_out_date, ac.negotiate_date,
            ac.demand_settled_date, ac.demand_duration_days,
            ac.top_offer_amount, ac.top_offer_date,
            ac.litigation_start_date, ac.litigation_settled_date, ac.litigation_duration_days,
            ac.presuit_offer, ac.resolution_type, ac.fee_rate,
            ac.uim_start_date, ac.uim_demand_out_date, ac.uim_negotiate_date,
            ac.uim_settled_date, ac.uim_duration_days, ac.is_policy_limit,
            ac.settled, ac.difference, ac.legal_fee, ac.discounted_legal_fee,
            ac.uim_settled, ac.uim_legal_fee, ac.uim_discounted_legal_fee,
            ac.commission, ac.commission_type, ac.uim_commission,
            ac.month, ac.check_received, ac.is_marketing, ac.note,
            COALESCE(u.display_name, u.full_name) AS attorney_name
     FROM attorney_cases ac
     LEFT JOIN users u ON ac.attorney_user_id = u.id
     WHERE {$where}
     ORDER BY ac.phase ASC, ac.assigned_date DESC",
    $params
);

$headers = [
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
    'month', 'check_received', 'is_marketing', 'note',
    'attorney_name'
];

$suffix = $phase ? "_{$phase}" : '';
outputCSV("attorney_cases{$suffix}_" . date('Y-m-d') . '.csv', $headers, $rows);
