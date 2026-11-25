<?php
/** Download the currently filtered enquiries as CSV (opens in Excel). Read-only, admin session required. */
declare(strict_types=1);
require __DIR__ . '/../../includes/bootstrap.php';
require_admin();

// Reuse the exact filter logic of the Enquiries page
$where = ['1=1']; $p = [];
$q = get_str('q', 100); $status = get_str('status', 20); $industry = get_str('industry', 100); $service = get_str('service', 150); $from = get_str('from', 10); $to = get_str('to', 10);
if ($q !== '') { $where[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ? OR company LIKE ? OR subject LIKE ? OR message LIKE ? OR enquiry_id LIKE ?)'; for ($i = 0; $i < 7; $i++) $p[] = "%$q%"; }
if (isset(ENQUIRY_STATUSES[$status])) { $where[] = 'status = ?'; $p[] = $status; }
if ($industry !== '') { $where[] = 'industry = ?'; $p[] = $industry; }
if ($service !== '') { $where[] = 'service = ?'; $p[] = $service; }
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) { $where[] = 'created_at >= ?'; $p[] = "$from 00:00:00"; }
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) { $where[] = 'created_at <= ?'; $p[] = "$to 23:59:59"; }

$rows = db_all('SELECT * FROM enquiries WHERE ' . implode(' AND ', $where) . ' ORDER BY created_at DESC', $p);
log_activity('Exported enquiries CSV', count($rows) . ' rows');

/** Prevent spreadsheet formula injection. */
function csv_safe(?string $v): string
{
    $v = (string)$v;
    return preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v;
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="dream-spark-enquiries-' . date('Y-m-d') . '.csv"');
header('Cache-Control: no-store');
$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows ₹, é, etc.
fputcsv($out, ['Reference', 'Received', 'Status', 'Name', 'Email', 'Phone', 'Company', 'Industry', 'Service', 'Subject', 'Message', 'Internal notes']);
foreach ($rows as $r) {
    fputcsv($out, array_map('csv_safe', [$r['enquiry_id'], $r['created_at'], ENQUIRY_STATUSES[$r['status']] ?? $r['status'], $r['name'], $r['email'], $r['phone'],
        $r['company'], $r['industry'], $r['service'], $r['subject'], $r['message'], $r['admin_notes'] ?? '']));
}
fclose($out);
