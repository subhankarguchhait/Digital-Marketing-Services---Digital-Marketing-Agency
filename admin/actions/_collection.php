<?php
/**
 * Shared save/delete handlers for brands, services, testimonials and team.
 * Every handler: admin session required · POST only · CSRF checked · server-side validation · prepared statements.
 */
declare(strict_types=1);
require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/form.php';

function unique_slug(string $table, string $text, int $exceptId = 0): string
{
    $base = slugify($text, 150);
    $slug = $base;
    for ($i = 2; db_val("SELECT id FROM $table WHERE slug = ? AND id <> ?", [$slug, $exceptId]); $i++) $slug = "$base-$i";
    return $slug;
}

function collection_save(string $key): never
{
    require_admin();
    require_post_csrf();
    $c = collection_schemas()[$key];
    $id = (int)($_POST['id'] ?? 0);
    if ($id && !db_val("SELECT id FROM {$c['table']} WHERE id = ?", [$id])) respond(false, ucfirst($c['singular']) . ' not found — it may have been deleted.', [], null, 404);

    [$clean, $errors] = validate_fields($c['fields'], $_POST['f'] ?? []);
    if ($errors) respond(false, 'Please fix the highlighted fields.', ['errors' => $errors]);

    foreach ($clean as $k => $v) if (is_array($v)) $clean[$k] = json_enc($v);
    if (isset($c['slug_from'])) $clean['slug'] = unique_slug($c['table'], $clean[$c['slug_from']], $id);
    $title = $clean[$c['title']];

    if ($id) {
        db_update($c['table'], $clean + ['updated_at' => now()], 'id = ?', [$id]);
        log_activity('Updated ' . $c['singular'], $title);
        flash('success', "“{$title}” saved — the website is updated.");
    } else {
        $order = (int)db_val("SELECT COALESCE(MAX(display_order), 0) FROM {$c['table']}") + 1;
        $id = db_insert($c['table'], $clean + ['display_order' => $order, 'created_at' => now(), 'updated_at' => now()]);
        log_activity('Added ' . $c['singular'], $title);
        flash('success', "“{$title}” added" . ($clean['status'] === 'published' ? ' and is live on the website.' : ' as hidden.'));
    }
    respond(true, 'Saved.', ['id' => $id, 'redirect' => 'admin/' . $c['admin']]);
}

function collection_delete(string $key): never
{
    require_admin();
    require_post_csrf();
    $c = collection_schemas()[$key];
    $id = (int)($_POST['id'] ?? 0);
    $row = $id ? db_one("SELECT * FROM {$c['table']} WHERE id = ?", [$id]) : null;
    if (!$row) respond(false, ucfirst($c['singular']) . ' not found.', [], null, 404);
    db_run("DELETE FROM {$c['table']} WHERE id = ?", [$id]);
    log_activity('Deleted ' . $c['singular'], $row[$c['title']]);
    respond(true, "“{$row[$c['title']]}” deleted. Its images remain in the Media Library.", ['remove' => true]);
}
