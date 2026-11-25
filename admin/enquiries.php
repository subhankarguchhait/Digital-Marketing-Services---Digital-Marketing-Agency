<?php
declare(strict_types=1);
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/admin-layout.php';
require __DIR__ . '/../includes/admin-collection.php';
require_admin();

/** Build the WHERE clause from the current filters (shared with the CSV export). */
function enquiry_filters(): array
{
    $where = ['1=1'];
    $p = [];
    $f = ['q' => get_str('q', 100), 'status' => get_str('status', 20), 'industry' => get_str('industry', 100), 'service' => get_str('service', 150), 'from' => get_str('from', 10), 'to' => get_str('to', 10)];
    if ($f['q'] !== '') { $where[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ? OR company LIKE ? OR subject LIKE ? OR message LIKE ? OR enquiry_id LIKE ?)'; for ($i = 0; $i < 7; $i++) $p[] = '%' . $f['q'] . '%'; }
    if (isset(ENQUIRY_STATUSES[$f['status']])) { $where[] = 'status = ?'; $p[] = $f['status']; } else $f['status'] = '';
    if ($f['industry'] !== '') { $where[] = 'industry = ?'; $p[] = $f['industry']; }
    if ($f['service'] !== '') { $where[] = 'service = ?'; $p[] = $f['service']; }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $f['from'])) { $where[] = 'created_at >= ?'; $p[] = $f['from'] . ' 00:00:00'; } else $f['from'] = '';
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $f['to'])) { $where[] = 'created_at <= ?'; $p[] = $f['to'] . ' 23:59:59'; } else $f['to'] = '';
    return [implode(' AND ', $where), $p, $f];
}

[$whereSql, $params, $f] = enquiry_filters();
$total = (int)db_val("SELECT COUNT(*) FROM enquiries WHERE $whereSql", $params);
$pg = paginate($total, 20, (int)($_GET['p'] ?? 1));
$rows = db_all("SELECT * FROM enquiries WHERE $whereSql ORDER BY created_at DESC, id DESC LIMIT ? OFFSET ?", [...$params, $pg['per'], $pg['offset']]);
$industries = array_column(db_all("SELECT DISTINCT industry FROM enquiries WHERE industry <> '' ORDER BY industry"), 'industry');
$services = array_column(db_all("SELECT DISTINCT service FROM enquiries WHERE service <> '' ORDER BY service"), 'service');
$counts = [];
foreach (db_all('SELECT status, COUNT(*) AS n FROM enquiries GROUP BY status') as $r) $counts[$r['status']] = (int)$r['n'];
$viewId = (int)($_GET['view'] ?? 0);
$exportUrl = url('admin/actions/enquiry-export.php') . qs(['p' => null, 'view' => null]);

admin_header('Enquiries', 'enquiries');
admin_page_head('Enquiries', 'Messages from the contact form. Click a row to read it, reply and update its status.',
    '<a class="btn btn--gold" href="' . e($exportUrl) . '"><i class="fa-solid fa-file-csv"></i>Export CSV</a>');
?>
<div class="chips-filter" style="margin-bottom:16px">
  <a class="<?= $f['status'] === '' ? 'is-active' : '' ?>" href="<?= e(url('admin/enquiries.php')) ?>">All (<?= array_sum($counts) ?>)</a>
  <?php foreach (ENQUIRY_STATUSES as $k => $l): ?><a class="<?= $f['status'] === $k ? 'is-active' : '' ?>" href="<?= e(url('admin/enquiries.php?status=' . $k)) ?>"><?= e($l) ?> (<?= $counts[$k] ?? 0 ?>)</a><?php endforeach; ?>
</div>
<div class="card">
  <form class="toolbar" method="get">
    <div class="search"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="q" value="<?= e($f['q']) ?>" placeholder="Search name, email, phone, company, subject, message or reference…" aria-label="Search enquiries"></div>
    <select name="status" aria-label="Status"><option value="">All statuses</option><?php foreach (ENQUIRY_STATUSES as $k => $l): ?><option value="<?= $k ?>"<?= $f['status'] === $k ? ' selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select>
    <select name="industry" aria-label="Industry"><option value="">All industries</option><?php foreach ($industries as $o): ?><option<?= $f['industry'] === $o ? ' selected' : '' ?>><?= e($o) ?></option><?php endforeach; ?></select>
    <select name="service" aria-label="Service"><option value="">All services</option><?php foreach ($services as $o): ?><option<?= $f['service'] === $o ? ' selected' : '' ?>><?= e($o) ?></option><?php endforeach; ?></select>
    <input type="date" name="from" value="<?= e($f['from']) ?>" aria-label="From date">
    <input type="date" name="to" value="<?= e($f['to']) ?>" aria-label="To date">
    <button class="btn btn--forest btn--sm" type="submit"><i class="fa-solid fa-filter"></i>Filter</button>
    <a class="btn btn--ghost btn--sm" href="<?= e(url('admin/enquiries.php')) ?>">Reset</a>
  </form>
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>From</th><th>Industry / Service</th><th>Subject</th><th>Status</th><th>Received</th><th style="text-align:right">Actions</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r):
          $data = array_intersect_key($r, array_flip(['id', 'enquiry_id', 'name', 'email', 'phone', 'company', 'industry', 'service', 'subject', 'message', 'status', 'admin_notes']));
          $data['received'] = format_date($r['created_at'], true);
          $data['whatsapp'] = contact_href('whatsapp', $r['phone']);
          $data['tel'] = contact_href('phone', $r['phone']); ?>
          <tr class="<?= $r['status'] === 'new' ? 'is-unread' : '' ?>" data-enquiry="<?= e(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) ?>"<?= $viewId === (int)$r['id'] ? ' data-autoopen' : '' ?> style="cursor:pointer">
            <td><strong><?= e($r['name']) ?></strong><div class="muted"><?= e($r['email']) ?></div><?php if ($r['company']): ?><div class="muted"><?= e($r['company']) ?></div><?php endif; ?></td>
            <td><?= e($r['industry'] ?: '—') ?><div class="muted"><?= e($r['service'] ?: '—') ?></div></td>
            <td style="max-width:300px"><span class="clamp-2"><strong><?= e($r['subject']) ?></strong> — <span class="muted"><?= e($r['message']) ?></span></span></td>
            <td>
              <form method="post" action="<?= e(url('admin/actions/enquiry-update.php')) ?>" data-ajax data-autosubmit>
                <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <select name="status" aria-label="Status for <?= e($r['name']) ?>" class="status-select status-select--<?= e($r['status']) ?>"><?php foreach (ENQUIRY_STATUSES as $k => $l): ?><option value="<?= $k ?>"<?= $r['status'] === $k ? ' selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select>
              </form>
            </td>
            <td class="muted" title="<?= e(format_date($r['created_at'], true)) ?>"><?= e(format_date($r['created_at'], true)) ?><div><?= e($r['enquiry_id']) ?></div></td>
            <td><div class="actions">
              <button type="button" class="btn btn--ghost btn--sm" data-enquiry-open><i class="fa-regular fa-eye"></i>View</button>
              <form method="post" action="<?= e(url('admin/actions/enquiry-delete.php')) ?>" data-ajax data-confirm="Delete the enquiry from <?= e($r['name']) ?>? This cannot be undone — export to CSV first if you need a record." data-danger>
                <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn--danger btn--icon btn--sm" type="submit" aria-label="Delete enquiry from <?= e($r['name']) ?>"><i class="fa-solid fa-trash-can"></i></button>
              </form>
            </div></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="6"><div class="empty"><i class="fa-regular fa-envelope-open"></i><h3><?= array_sum($counts) ? 'No enquiries match these filters' : 'No enquiries yet' ?></h3>New messages from the contact form will appear here.</div></td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="toolbar" style="border-top:1px solid var(--line);border-bottom:0;justify-content:space-between">
    <span class="muted">Showing <?= count($rows) ?> of <?= $total ?> enquiries</span>
    <?php render_pagination($pg); ?>
  </div>
</div>

<template id="enquiryTemplate">
  <div class="enq-detail">
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap"><span class="badge" data-e="status-badge"></span><span class="muted" data-e="received"></span><span class="muted" data-e="enquiry_id"></span></div>
    <div class="enq-meta">
      <div><small>Email</small><a data-e="email"></a></div>
      <div><small>Phone</small><a data-e="phone"></a></div>
      <div><small>Company</small><span data-e="company"></span></div>
      <div><small>Industry</small><span data-e="industry"></span></div>
      <div><small>Interested service</small><span data-e="service"></span></div>
      <div><small>Subject</small><span data-e="subject"></span></div>
    </div>
    <div><small class="f__label" style="display:block;margin-bottom:6px">Message</small><div class="enq-detail__msg" data-e="message"></div></div>
    <form method="post" action="<?= e(url('admin/actions/enquiry-update.php')) ?>" data-ajax class="grid" style="gap:12px">
      <?= csrf_field() ?><input type="hidden" name="id" data-e="id-input">
      <div class="f"><label>Status</label><select name="status" data-e="status-select"><?php foreach (ENQUIRY_STATUSES as $k => $l): ?><option value="<?= $k ?>"><?= e($l) ?></option><?php endforeach; ?></select></div>
      <div class="f"><label>Internal notes (only visible to admins)</label><textarea name="admin_notes" rows="3" maxlength="2000" data-e="notes"></textarea></div>
      <div><button class="btn btn--forest btn--sm" type="submit"><i class="fa-solid fa-floppy-disk"></i>Save status & notes</button></div>
    </form>
  </div>
</template>
<?php admin_footer();
