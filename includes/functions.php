<?php
/**
 * Shared helpers: escaping, URLs, settings, page/section loading, collections,
 * responses, flash messages, logging.
 */
declare(strict_types=1);
defined('APP_NAME') || exit; // no direct access

/* ------------------------------------------------------------------ output */

/** Escape for HTML text and attributes. Use for EVERY dynamic value. */
function e(mixed $v): string
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** "Grow your *brand*" → escaped HTML with <em>brand</em>; newlines → <br>. */
function rich(?string $text): string
{
    $html = e($text ?? '');
    $html = preg_replace('/\*([^*]+)\*/u', '<em>$1</em>', $html);
    return nl2br($html, false);
}

/** Paragraphs separated by blank lines → <p> elements (escaped). */
function paras(?string $text): string
{
    $parts = preg_split('/\R\s*\R/u', trim((string)$text)) ?: [];
    return implode('', array_map(fn($p) => '<p>' . nl2br(e(trim($p)), false) . '</p>', array_filter($parts, fn($p) => trim($p) !== '')));
}

/** Plain text without the *highlight* markers (for alt text, titles, meta). */
function plain(?string $text): string
{
    return trim(str_replace('*', '', (string)$text));
}

function url(string $path = ''): string
{
    return base_url() . ltrim($path, '/');
}

/** Static asset URL with cache-busting version. */
function asset(string $path): string
{
    $file = ROOT_PATH . '/' . ltrim($path, '/');
    $v = is_file($file) ? (string)filemtime($file) : '1';
    return url($path) . '?v=' . $v;
}

/** URL for a stored image path (relative "assets/…" or absolute https URL). Empty if unsafe. */
function media_url(?string $path): string
{
    $p = trim((string)$path);
    if ($p === '') return '';
    if (preg_match('#^https?://#i', $p)) return filter_var($p, FILTER_VALIDATE_URL) ? $p : '';
    if (!is_safe_media_path($p)) return '';
    return url($p);
}

function is_safe_media_path(string $p): bool
{
    return (bool)preg_match('#^assets/[A-Za-z0-9/_.\-]+\.(jpe?g|png|webp|gif|svg)$#i', $p) && !str_contains($p, '..');
}

const LINK_TOKENS = ['#whatsapp', '#email', '#phone'];

/** Is this an acceptable CMS link? page (about.php), anchor (#about), token, mailto:, tel:, http(s) URL. */
function is_valid_link(string $v): bool
{
    $v = trim($v);
    if ($v === '') return true;
    if (in_array($v, LINK_TOKENS, true)) return true;
    if (preg_match('#^https?://#i', $v)) return (bool)filter_var($v, FILTER_VALIDATE_URL);
    if (preg_match('#^mailto:[^\s@]+@[^\s@]+$#i', $v)) return true;
    if (preg_match('#^tel:[+\d\s()\-]{5,}$#i', $v)) return true;
    if (preg_match('#^[a-z][a-z0-9+.\-]*:#i', $v)) return false;           // javascript:, data:, …
    return (bool)preg_match('#^[A-Za-z0-9_\-./?=&%\#]+$#', $v) && !str_contains($v, '..') && !str_starts_with($v, '//');
}

/** Turn a CMS link into a safe href. */
function link_href(?string $v): string
{
    $v = trim((string)$v);
    if ($v === '' || !is_valid_link($v)) return '#';
    return match ($v) {
        '#whatsapp' => contact_href('whatsapp', setting('whatsapp')) ?: '#',
        '#email' => contact_href('email', setting('email')) ?: '#',
        '#phone' => contact_href('phone', setting('phone')) ?: '#',
        default => (preg_match('#^(https?:|mailto:|tel:|\#)#i', $v) ? $v : url($v)),
    };
}

/** target/rel attributes for external links. */
function link_attrs(?string $v): string
{
    return preg_match('#^https?://#i', (string)$v) ? ' target="_blank" rel="noopener"' : '';
}

function contact_href(string $type, ?string $value): string
{
    $v = trim((string)$value);
    if ($v === '') return '';
    return match ($type) {
        'email' => filter_var($v, FILTER_VALIDATE_EMAIL) ? 'mailto:' . $v : '',
        'phone' => 'tel:' . preg_replace('/[^\d+]/', '', $v),
        'whatsapp' => 'https://wa.me/' . preg_replace('/\D/', '', $v),
        'address' => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($v),
        default => '',
    };
}

const SOCIAL_ICONS = [
    'instagram' => 'fa-brands fa-instagram',
    'facebook' => 'fa-brands fa-facebook-f',
    'linkedin' => 'fa-brands fa-linkedin-in',
    'youtube' => 'fa-brands fa-youtube',
    'x' => 'fa-brands fa-x-twitter',
    'behance' => 'fa-brands fa-behance',
];

function safe_icon(?string $cls, string $fallback = 'fa-solid fa-star'): string
{
    $c = trim((string)$cls);
    return preg_match('/^fa-(solid|regular|brands)( fa-[a-z0-9\-]+)+$/', $c) ? $c : $fallback;
}

function initials(string $name): string
{
    $words = preg_split('/\s+/', trim($name)) ?: [];
    $out = '';
    foreach (array_slice($words, 0, 2) as $w) $out .= mb_strtoupper(mb_substr($w, 0, 1));
    return $out ?: 'DS';
}

function slugify(string $s, int $max = 80): string
{
    $s = mb_strtolower(trim($s));
    if (function_exists('iconv')) $s = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s;
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim(substr(trim((string)$s, '-'), 0, $max), '-') ?: 'item';
}

function json_enc(mixed $v): string
{
    return json_encode($v, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
}

function json_dec(?string $s, mixed $default = []): mixed
{
    if ($s === null || $s === '') return $default;
    $v = json_decode($s, true);
    return $v === null ? $default : $v;
}

function format_date(?string $dt, bool $time = false): string
{
    if (!$dt) return '—';
    $ts = strtotime($dt);
    return $ts ? date($time ? 'd M Y, h:i A' : 'd M Y', $ts) : '—';
}

function time_ago(?string $dt): string
{
    $ts = $dt ? strtotime($dt) : false;
    if (!$ts) return '';
    $s = time() - $ts;
    if ($s < 60) return 'just now';
    if ($s < 3600) return floor($s / 60) . ' min ago';
    if ($s < 86400) return floor($s / 3600) . ' h ago';
    if ($s < 30 * 86400) return floor($s / 86400) . ' d ago';
    return format_date($dt);
}

/* --------------------------------------------------------------- logging */

function log_error(string $message): void
{
    $line = sprintf("[%s] %s%s", date('Y-m-d H:i:s'), $message, PHP_EOL);
    @file_put_contents(LOG_PATH . '/app.log', $line, FILE_APPEND | LOCK_EX);
}

function client_ip(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

function log_activity(string $action, string $description = ''): void
{
    try {
        db_insert('activity_logs', [
            'admin_id' => current_admin()['id'] ?? null,
            'action' => mb_substr($action, 0, 100),
            'description' => mb_substr($description, 0, 500),
            'ip_address' => client_ip(),
            'created_at' => now(),
        ]);
    } catch (Throwable $e) {
        log_error('Activity log failed: ' . $e->getMessage());
    }
}

/* -------------------------------------------------------------- settings */

/** All settings (database values over defaults). */
function settings(bool $refresh = false): array
{
    static $cache = null;
    if ($cache !== null && !$refresh) return $cache;
    $cache = default_settings();
    try {
        foreach (db_all('SELECT setting_key, setting_value FROM settings') as $r) {
            $cache[$r['setting_key']] = (string)$r['setting_value'];
        }
    } catch (Throwable $e) {
        log_error('Settings fallback: ' . $e->getMessage());
    }
    return $cache;
}

function setting(string $key, string $default = ''): string
{
    $s = settings();
    $v = $s[$key] ?? $default;
    return str_replace('{year}', date('Y'), (string)$v);
}

/* ------------------------------------------------------- pages & sections */

const SECTION_COLUMNS = [
    'title' => 'section_title',
    'eyebrow' => 'section_subtitle',
    'content' => 'section_content',
    'image' => 'image',
    'button_text' => 'button_text',
    'button_url' => 'button_url',
];

/** Convert a page_sections row into a flat field array used by templates and editors. */
function section_from_row(array $row): array
{
    $s = json_dec($row['extra'] ?? null, []);
    foreach (SECTION_COLUMNS as $field => $col) $s[$field] = $row[$col] ?? '';
    $s['_id'] = (int)$row['id'];
    $s['_visible'] = (bool)$row['is_visible'];
    $s['_order'] = (int)$row['display_order'];
    return $s;
}

/** Split a field array back into column values + extra JSON for saving. */
function section_to_row(array $values): array
{
    $row = [];
    foreach (SECTION_COLUMNS as $field => $col) {
        $row[$col] = isset($values[$field]) ? (string)$values[$field] : '';
        unset($values[$field]);
    }
    $row['extra'] = json_enc((object)$values);
    return $row;
}

/**
 * Load a page for the public site: meta + ordered, visible sections.
 * Falls back to the built-in default content for any section missing from the database
 * (or for the whole page if the database is not reachable yet).
 */
function get_page(string $slug): array
{
    $defaults = default_pages()[$slug] ?? ['meta' => [], 'sections' => []];
    $schema = page_schemas()[$slug]['sections'] ?? [];
    $meta = $defaults['meta'];
    $sections = [];
    $position = 0;
    foreach ($defaults['sections'] as $key => $s) {
        $sections[$key] = $s + ['_visible' => true, '_order' => ++$position, '_id' => 0];
    }
    try {
        $page = db_one('SELECT * FROM pages WHERE page_slug = ?', [$slug]);
        if ($page) {
            $meta = ['title' => $page['meta_title'], 'description' => $page['meta_description'], 'name' => $page['page_name']];
            foreach (db_all('SELECT * FROM page_sections WHERE page_id = ? ORDER BY display_order, id', [(int)$page['id']]) as $row) {
                if (isset($schema[$row['section_key']])) $sections[$row['section_key']] = section_from_row($row);
            }
        }
    } catch (Throwable $e) {
        log_error("Page '$slug' fallback to defaults: " . $e->getMessage());
    }
    uasort($sections, fn($a, $b) => $a['_order'] <=> $b['_order']);
    return ['slug' => $slug, 'meta' => $meta, 'sections' => $sections];
}

/** Render the visible sections of a page using includes/sections/{template}.php */
function render_sections(array $page): void
{
    $schema = page_schemas()[$page['slug']]['sections'];
    foreach ($page['sections'] as $key => $s) {
        if (empty($s['_visible']) && empty($schema[$key]['always'])) continue;
        $template = $schema[$key]['template'] ?? null;
        if (!$template) continue;
        render_section($template, $s, $key, $page);
    }
}

function render_section(string $template, array $s, string $key = '', array $page = []): void
{
    $file = ROOT_PATH . '/includes/sections/' . basename($template) . '.php';
    if (is_file($file)) (static function () use ($file, $s, $key, $page) { include $file; })();
}

/** Value helper for templates: field from a section with default ''. */
function sv(array $s, string $k, string $default = ''): string
{
    $v = $s[$k] ?? $default;
    return is_scalar($v) ? (string)$v : $default;
}

function sl(array $s, string $k): array
{
    $v = $s[$k] ?? [];
    return is_array($v) ? $v : [];
}

/* ------------------------------------------------------------ collections */

/** Published items of a collection for the public site (ordered). */
function public_collection(string $key, int $limit = 0): array
{
    $c = collection_schemas()[$key];
    try {
        $sql = "SELECT * FROM {$c['table']} WHERE status = 'published' ORDER BY " . ($key === 'brands' ? 'is_featured DESC, ' : '') . 'display_order, id';
        if ($limit > 0) $sql .= ' LIMIT ' . (int)$limit;
        $rows = db_all($sql);
        if (!$rows && !db_val("SELECT COUNT(*) FROM {$c['table']}")) throw new RuntimeException('empty table');
        return array_map(fn($r) => decode_item($key, $r), $rows);
    } catch (Throwable $e) {
        // Database not installed yet → show starter content
        if (!($e instanceof RuntimeException && $e->getMessage() === 'empty table')) log_error("Collection '$key' fallback: " . $e->getMessage());
        $items = array_values(array_filter(default_collections()[$key] ?? [], fn($i) => ($i['status'] ?? 'published') === 'published'));
        return $limit > 0 ? array_slice($items, 0, $limit) : $items;
    }
}

/** Decode JSON columns of a collection row. */
function decode_item(string $key, array $row): array
{
    foreach (collection_schemas()[$key]['fields'] as $f) {
        if (in_array($f['t'] ?? 'text', ['tags', 'group', 'repeater'], true) && isset($row[$f['k']]) && is_string($row[$f['k']])) {
            $row[$f['k']] = json_dec($row[$f['k']], []);
        }
    }
    return $row;
}

/* ----------------------------------------------------- requests/responses */

function is_post(): bool { return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'; }

function is_ajax(): bool
{
    return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
        || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
}

function post_str(string $key, int $max = 255): string
{
    $v = $_POST[$key] ?? '';
    if (!is_string($v)) return '';
    $v = trim(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $v) ?? '');
    return mb_substr($v, 0, $max);
}

function get_str(string $key, int $max = 200): string
{
    $v = $_GET[$key] ?? '';
    return is_string($v) ? mb_substr(trim($v), 0, $max) : '';
}

function json_response(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function redirect(string $to): never
{
    header('Location: ' . (preg_match('#^https?://#', $to) ? $to : url($to)));
    exit;
}

/**
 * Reply to a form action: JSON for fetch() requests, flash message + redirect otherwise.
 */
function respond(bool $ok, string $message, array $extra = [], ?string $back = null, int $status = 0): never
{
    if (is_ajax()) json_response(['ok' => $ok, 'message' => $message] + $extra, $status ?: ($ok ? 200 : 422));
    flash($ok ? 'success' : 'error', $message);
    $target = $extra['redirect'] ?? $back ?? ($_SERVER['HTTP_REFERER'] ?? 'admin/dashboard.php');
    header('Location: ' . (preg_match('#^https?://#', $target) ? $target : url($target)));
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function take_flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

/** Simple pagination numbers. */
function paginate(int $total, int $perPage, int $page): array
{
    $pages = max(1, (int)ceil($total / $perPage));
    $page = min(max(1, $page), $pages);
    return ['page' => $page, 'pages' => $pages, 'offset' => ($page - 1) * $perPage, 'per' => $perPage, 'total' => $total];
}

/** Build a query string from the current GET params with overrides. */
function qs(array $override = []): string
{
    $q = array_merge($_GET, $override);
    $q = array_filter($q, fn($v) => $v !== '' && $v !== null);
    return $q ? '?' . http_build_query($q) : '';
}

/* ------------------------------------------------------- template helpers */

/** Section heading block: eyebrow, title (*rich*), description. */
function sec_head(array $s, string $mods = ''): void
{
    $eyebrow = sv($s, 'eyebrow');
    $desc = sv($s, 'description');
    echo '<div class="sec-head ' . e($mods) . '">';
    if ($eyebrow !== '') echo '<p class="eyebrow reveal"><span class="eyebrow__line"></span><span>' . e($eyebrow) . '</span></p>';
    echo '<h2 class="sec-title reveal">' . rich(sv($s, 'title')) . '</h2>';
    if ($desc !== '') echo '<p class="sec-desc reveal">' . e($desc) . '</p>';
    echo '</div>';
}

/** A CMS-driven button; prints nothing when the text is empty. */
function cms_button(string $text, string $link, string $class = 'btn btn--gold', bool $arrow = true): void
{
    if (trim($text) === '') return;
    echo '<a class="' . e($class) . ' magnetic" href="' . e(link_href($link)) . '"' . link_attrs($link) . '><span>' . e($text) . '</span>'
        . ($arrow ? '<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>' : '') . '</a>';
}

/** Social icon links from a key=>url map. */
function social_links(array $links, string $class = 'socials'): void
{
    $out = '';
    foreach ($links as $k => $u) {
        if (!$u || !isset(SOCIAL_ICONS[$k]) || !filter_var($u, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $u)) continue;
        $out .= '<a href="' . e($u) . '" target="_blank" rel="noopener" aria-label="' . e(ucfirst($k)) . '"><i class="' . SOCIAL_ICONS[$k] . '"></i></a>';
    }
    if ($out !== '') echo '<div class="' . e($class) . '">' . $out . '</div>';
}

function site_socials(): array
{
    $out = [];
    foreach (array_keys(SOCIAL_ICONS) as $k) $out[$k] = setting('social_' . $k);
    return $out;
}

/** Image tag for a stored path (nothing when empty). */
function img_tag(string $path, string $alt = '', string $attrs = 'loading="lazy"'): string
{
    $src = media_url($path);
    return $src === '' ? '' : '<img src="' . e($src) . '" alt="' . e($alt) . '" ' . $attrs . '>';
}

/** Site name with the last word in gold italics. */
function site_name_html(): string
{
    $words = preg_split('/\s+/', trim(setting('site_name', APP_NAME))) ?: [APP_NAME];
    $last = count($words) > 1 ? array_pop($words) : '';
    return e(implode(' ', $words)) . ($last !== '' ? ' <em>' . e($last) . '</em>' : '');
}

/** Brand card markup (preview cards link to the Brands page; grid cards open the detail modal). */
function brand_card(array $b, int $i, bool $asLink): string
{
    $social = is_array($b['social_links'] ?? null) ? $b['social_links'] : [];
    $data = [
        'slug' => $b['slug'] ?? '', 'name' => $b['name'] ?? '', 'category' => $b['category'] ?? '', 'description' => $b['description'] ?? '',
        'logo' => media_url($b['logo'] ?? ''), 'cover' => media_url($b['cover_image'] ?? ''),
        'services' => array_values(array_filter((array)($b['services'] ?? []), 'is_string')),
        'website' => preg_match('#^https?://#i', (string)($b['website_url'] ?? '')) ? $b['website_url'] : '',
        'social' => array_filter($social, fn($u, $k) => isset(SOCIAL_ICONS[$k]) && preg_match('#^https?://#i', (string)$u), ARRAY_FILTER_USE_BOTH),
    ];
    $tag = $asLink ? 'a' : 'button';
    $attrs = $asLink
        ? 'href="' . e(url('brands.php?brand=' . rawurlencode($data['slug']))) . '"'
        : 'type="button" aria-haspopup="dialog" data-brand="' . e(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) . '"';
    $html = '<' . $tag . ' class="brand-card reveal-card" data-category="' . e($data['category']) . '" style="--d:' . (($i % 3) * 0.1) . 's" ' . $attrs . '>';
    $html .= '<div class="brand-card__cover">' . ($data['cover'] ? '<img src="' . e($data['cover']) . '" alt="" loading="lazy">' : '') . '</div>';
    if ($data['logo']) $html .= '<div class="brand-card__logo"><img src="' . e($data['logo']) . '" alt="' . e($data['name']) . ' logo" loading="lazy"></div>';
    if ($data['category']) $html .= '<span class="brand-card__cat">' . e($data['category']) . '</span>';
    $html .= '<div class="brand-card__body"><h3>' . e($data['name']) . '</h3><p>' . e($data['description']) . '</p>'
        . '<div class="brand-card__reveal"><span>View case</span><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></div></div>';
    return $html . '</' . $tag . '>';
}

/** Trim, strip control characters (keeping newlines in multi-line text) and cap length. */
function clean_text(string $v, int $max = 255, bool $multiline = false): string
{
    $v = str_replace("\r\n", "\n", $v);
    $v = preg_replace($multiline ? '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u' : '/[\x00-\x1F\x7F]/u', '', $v) ?? '';
    return mb_substr(trim($v), 0, $max);
}
