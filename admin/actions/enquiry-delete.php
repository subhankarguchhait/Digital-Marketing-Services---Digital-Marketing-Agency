<?php
/** Delete an enquiry (POST + CSRF). */
declare(strict_types=1);
require __DIR__ . '/../../includes/bootstrap.php';
require_admin();
require_post_csrf();

$id = (int)($_POST['id'] ?? 0);
$row = $id ? db_one('SELECT id, name, enquiry_id FROM enquiries WHERE id = ?', [$id]) : null;
if (!$row) respond(false, 'Enquiry not found.', [], null, 404);
db_run('DELETE FROM enquiries WHERE id = ?', [$id]);
log_activity('Deleted enquiry', $row['name'] . ' (' . $row['enquiry_id'] . ')');
respond(true, 'Enquiry deleted.', ['remove' => true]);
