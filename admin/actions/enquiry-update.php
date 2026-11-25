<?php
/** Change an enquiry's status and/or internal notes (POST + CSRF). */
declare(strict_types=1);
require __DIR__ . '/../../includes/bootstrap.php';
require_admin();
require_post_csrf();

$id = (int)($_POST['id'] ?? 0);
$row = $id ? db_one('SELECT id, name, status FROM enquiries WHERE id = ?', [$id]) : null;
if (!$row) respond(false, 'Enquiry not found.', [], null, 404);
$status = post_str('status', 20);
if (!isset(ENQUIRY_STATUSES[$status])) respond(false, 'Choose a valid status.');
$data = ['status' => $status, 'updated_at' => now()];
if (array_key_exists('admin_notes', $_POST)) $data['admin_notes'] = mb_substr(trim((string)$_POST['admin_notes']), 0, 2000);
db_update('enquiries', $data, 'id = ?', [$id]);
if ($status !== $row['status']) log_activity('Changed enquiry status', $row['name'] . ' → ' . ENQUIRY_STATUSES[$status]);
respond(true, isset($data['admin_notes']) ? 'Enquiry updated.' : 'Marked as ' . ENQUIRY_STATUSES[$status] . '.', ['status' => $status, 'label' => ENQUIRY_STATUSES[$status]]);
