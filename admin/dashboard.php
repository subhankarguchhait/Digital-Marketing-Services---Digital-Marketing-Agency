<?php
declare(strict_types=1);
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/admin-layout.php';
$admin = require_admin();

$stats = [
    'total' => (int)db_val('SELECT COUNT(*) FROM enquiries'),
    'new' => (int)db_val("SELECT COUNT(*) FROM enquiries WHERE status = 'new'"),
    'brands' => (int)db_val('SELECT COUNT(*) FROM brands'),
    'published' => (int)db_val("SELECT COUNT(*) FROM brands WHERE status = 'published'"),
    'services' => (int)db_val('SELECT COUNT(*) FROM services'),
    'converted' => (int)db_val("SELECT COUNT(*) FROM enquiries WHERE status = 'converted'"),
];

// Monthly enquiries — last 12 months (bucketed in PHP so the query is portable)
$start = new DateTimeImmutable('first day of this month 00:00');
$start = $start->modify('-11 months');
$months = [];
for ($i = 0; $i < 12; $i++) { $m = $start->modify("+$i months"); $months[$m->format('Y-m')] = ['label' => $m->format('M'), 'year' => $m->format('Y'), 'n' => 0]; }
foreach (db_all('SELECT created_at FROM enquiries WHERE created_at >= ?', [$start->format('Y-m-d H:i:s')]) as $r) {
    $k = substr($r['created_at'], 0, 7);
    if (isset($months[$k])) $months[$k]['n']++;
}
$max = max(1, ...array_column($months, 'n'));
$recent = db_all('SELECT * FROM enquiries ORDER BY created_at DESC LIMIT 6');
$activity = db_all('SELECT l.*, a.name AS admin_name FROM activity_logs l LEFT JOIN admins a ON a.id = l.admin_id ORDER BY l.created_at DESC LIMIT 8');
$hour = (int)date('G');
$greet = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

admin_header('Dashboard', 'dashboard');
admin_page_head("$greet, " . explode(' ', $admin['name'])[0], "Here's what's happening on your website.",
    '<a class="btn btn--ghost" href="' . e(url()) . '" target="_blank" rel="noopener"><i class="fa-solid fa-globe"></i>View website</a>'
    . '<a class="btn btn--gold" href="' . e(url('admin/pages.php?page=home')) . '"><i class="fa-solid fa-pen"></i>Edit home page</a>');

$cards = [
    ['fa-solid fa-inbox', 'Total enquiries', $stats['total'], '', 'enquiries.php', 'View all'],
    ['fa-solid fa-bell', 'New enquiries', $stats['new'], 'stat-card--spark', 'enquiries.php?status=new', 'Reply now'],
    ['fa-solid fa-gem', 'Total brands', $stats['brands'], 'stat-card--gold', 'brands.php', 'Manage'],
    ['fa-solid fa-eye', 'Published brands', $stats['published'], 'stat-card--ok', 'brands.php?status=published', 'Manage'],
    ['fa-solid fa-briefcase', 'Total services', $stats['services'], '', 'services.php', 'Manage'],
    ['fa-solid fa-handshake', 'Converted enquiries', $stats['converted'], 'stat-card--ok', 'enquiries.php?status=converted', 'View'],
];
?>
<div class="grid grid--3" style="margin-bottom:20px">
  <?php foreach ($cards as [$icon, $label, $n, $tone, $href, $link]): ?>
    <div class="card stat-card <?= $tone ?>">
      <span class="stat-card__icon"><i class="<?= $icon ?>"></i></span>
      <div><small><?= e($label) ?></small><strong><?= number_format($n) ?></strong><a href="<?= e(url('admin/' . $href)) ?>"><?= e($link) ?> →</a></div>
    </div>
  <?php endforeach; ?>
</div>

<div class="grid grid--2-1" style="margin-bottom:20px">
  <div class="card">
    <div class="card__head"><h2><i class="fa-solid fa-chart-column"></i>Monthly enquiries</h2><span class="f__help"><?= array_sum(array_column($months, 'n')) ?> in the last 12 months</span></div>
    <div class="card__body">
      <div class="chart" role="img" aria-label="Bar chart of enquiries per month">
        <?php foreach ($months as $k => $m): ?>
          <div class="chart__col" title="<?= e($m['label'] . ' ' . $m['year'] . ': ' . $m['n']) ?>">
            <div class="chart__bar" style="height:<?= round($m['n'] / $max * 85, 1) ?>%"><?php if ($m['n']): ?><span><?= $m['n'] ?></span><?php endif; ?></div>
            <small><?= e($m['label']) ?></small>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card__head"><h2><i class="fa-solid fa-clock-rotate-left"></i>Recent activity</h2></div>
    <div class="card__body" style="padding-top:6px;padding-bottom:6px">
      <ul class="activity">
        <?php foreach ($activity as $l): ?>
          <li><i class="fa-solid fa-pen-nib"></i><div><span><?= e($l['action']) ?><?php if ($l['description']): ?> · <strong><?= e($l['description']) ?></strong><?php endif; ?></span><small><?= e($l['admin_name'] ?? 'System') ?> · <?= e(format_date($l['created_at'], true)) ?></small></div></li>
        <?php endforeach; ?>
        <?php if (!$activity): ?><li><i class="fa-regular fa-clock"></i><div>No activity yet. Your edits will be logged here.</div></li><?php endif; ?>
      </ul>
    </div>
  </div>
</div>

<div class="card" style="margin-bottom:20px">
  <div class="card__head"><h2><i class="fa-solid fa-envelope-open-text"></i>Recent enquiries</h2><a class="btn btn--ghost btn--sm" href="<?= e(url('admin/enquiries.php')) ?>">Open inbox</a></div>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>Name</th><th>Industry</th><th>Service</th><th>Status</th><th>Received</th></tr></thead>
    <tbody>
      <?php foreach ($recent as $r): ?>
        <tr class="<?= $r['status'] === 'new' ? 'is-unread' : '' ?>">
          <td><a href="<?= e(url('admin/enquiries.php?view=' . (int)$r['id'])) ?>"><strong><?= e($r['name']) ?></strong></a><div class="muted"><?= e($r['email']) ?></div></td>
          <td><?= e($r['industry'] ?: '—') ?></td>
          <td><?= e($r['service'] ?: '—') ?></td>
          <td><span class="badge badge--<?= e($r['status']) ?>"><?= e(ENQUIRY_STATUSES[$r['status']] ?? $r['status']) ?></span></td>
          <td class="muted" title="<?= e(format_date($r['created_at'], true)) ?>"><?= e(time_ago($r['created_at'])) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$recent): ?><tr><td colspan="5"><div class="empty"><i class="fa-regular fa-envelope"></i><h3>No enquiries yet</h3>Contact-form messages will appear here.</div></td></tr><?php endif; ?>
    </tbody>
  </table></div>
</div>

<div class="grid grid--4">
  <?php foreach ([['pages.php?page=home', 'fa-solid fa-house', 'Home page', 'Hero, sections, stats'], ['pages.php?page=about', 'fa-solid fa-users', 'About page', 'Story, mission, values'], ['media.php', 'fa-solid fa-photo-film', 'Media library', 'Upload & organise images'], ['profile.php', 'fa-solid fa-user-gear', 'My profile', 'Name & password']] as [$h, $i, $t, $d]): ?>
    <a class="card stat-card" href="<?= e(url('admin/' . $h)) ?>"><span class="stat-card__icon"><i class="<?= $i ?>"></i></span><div><strong style="font-size:22px"><?= e($t) ?></strong><small><?= e($d) ?></small></div></a>
  <?php endforeach; ?>
</div>
<?php admin_footer();
