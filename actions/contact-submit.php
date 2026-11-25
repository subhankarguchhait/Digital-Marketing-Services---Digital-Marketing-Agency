<?php
/**
 * Public contact form endpoint (AJAX, falls back to normal POST).
 * Defences: POST only · CSRF token · honeypot · signed minimum-time check · per-IP hourly limit ·
 * duplicate detection · strict server-side validation · prepared statements.
 */
declare(strict_types=1);
require __DIR__ . '/../includes/bootstrap.php';

function contact_reply(bool $ok, string $message, array $errors = [], int $status = 0): never
{
    if (is_ajax()) json_response(['ok' => $ok, 'message' => $message, 'errors' => (object)$errors], $status ?: ($ok ? 200 : 422));
    flash($ok ? 'success' : 'error', $message);
    redirect('contact.php' . ($ok ? '?sent=1' : '') . '#contactForm');
}

if (!is_post()) contact_reply(false, 'Please use the contact form.', [], 405);
if (!csrf_valid()) contact_reply(false, 'Your session expired. Please refresh the page and send your enquiry again.', [], 419);

// 1. Honeypot: real visitors never see this field. Pretend success so bots learn nothing.
if (post_str('website') !== '') contact_reply(true, 'Thank you! Your enquiry has been received.');

// 2. Timing: the signed stamp proves when the form was rendered.
$age = form_stamp_age(post_str('form_stamp', 100));
if ($age === null || $age > 86400) contact_reply(false, 'This form has expired. Please refresh the page and try again.', [], 419);
if ($age < ENQUIRY_MIN_SECONDS) contact_reply(false, 'That was very quick! Please take a moment to check your details and submit again.');

// 3. Validation
$d = [
    'name' => post_str('name', 100), 'email' => mb_strtolower(post_str('email', 150)), 'phone' => post_str('phone', 20),
    'company' => post_str('company', 120), 'industry' => post_str('industry', 100), 'service' => post_str('service', 150),
    'subject' => post_str('subject', 150), 'message' => clean_text((string)($_POST['message'] ?? ''), 3000, true),
];
$e = [];
if (mb_strlen($d['name']) < 2) $e['name'] = 'Please enter your full name.';
elseif (!preg_match("/^[\p{L}\p{M} .'\-]+$/u", $d['name'])) $e['name'] = 'Please use letters only in your name.';
if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) $e['email'] = 'Please enter a valid email address.';
$digits = preg_replace('/\D/', '', $d['phone']);
if (!preg_match('/^[+()\d\s\-]{7,20}$/', $d['phone']) || strlen($digits) < 7 || strlen($digits) > 15) $e['phone'] = 'Please enter a valid phone number.';
if ($d['industry'] === '') $e['industry'] = 'Please choose your industry.';
if ($d['service'] === '') $e['service'] = 'Please choose a service.';
if (mb_strlen($d['subject']) < 3) $e['subject'] = 'Please add a short subject.';
if (mb_strlen($d['message']) < 10) $e['message'] = 'Please tell us a little more (at least 10 characters).';
if (($_POST['consent'] ?? '') !== '1') $e['consent'] = 'Please confirm we may contact you.';
if (preg_match_all('#https?://#i', $d['message']) > 3) $e['message'] = 'Please include no more than 3 links.';

// Only accept options that the site actually offers
$form = get_page('contact')['sections']['form'] ?? [];
$industries = sl($form, 'industries') ?: default_industries();
if ($d['industry'] !== '' && !in_array($d['industry'], $industries, true)) $e['industry'] = 'Please choose an industry from the list.';
$services = sl($form, 'services');
if (!$services) { $services = array_map(fn($x) => $x['title'], public_collection('services')); $services[] = 'Other'; }
if ($d['service'] !== '' && !in_array($d['service'], $services, true)) $e['service'] = 'Please choose a service from the list.';

if ($e) contact_reply(false, 'Please correct the highlighted fields.', $e);

try {
    // 4. Rate limit per IP
    $recent = (int)db_val('SELECT COUNT(*) FROM enquiries WHERE ip_address = ? AND created_at >= ?', [client_ip(), date('Y-m-d H:i:s', time() - 3600)]);
    if ($recent >= ENQUIRY_MAX_PER_HOUR) contact_reply(false, 'You have sent several enquiries recently. Please try again later or call us directly.', [], 429);

    // 5. Duplicate submission (double-click, resubmits) within 24 hours
    $hash = hash('sha256', $d['email'] . '|' . mb_strtolower(preg_replace('/\s+/', ' ', $d['message'])));
    if (db_val('SELECT id FROM enquiries WHERE message_hash = ? AND created_at >= ?', [$hash, date('Y-m-d H:i:s', time() - 86400)])) {
        contact_reply(true, 'We already have this enquiry — thank you! Our team will be in touch soon.');
    }

    $ref = 'DS-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    db_insert('enquiries', $d + [
        'enquiry_id' => $ref, 'status' => 'new', 'message_hash' => $hash, 'ip_address' => client_ip(),
        'user_agent' => mb_substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255), 'created_at' => now(), 'updated_at' => now(),
    ]);
} catch (DatabaseUnavailable $ex) {
    contact_reply(false, 'Sorry, we could not save your message right now. Please email or call us directly.', [], 503);
} catch (Throwable $ex) {
    log_error('Enquiry save failed: ' . $ex->getMessage());
    contact_reply(false, 'Sorry, something went wrong while sending. Please try again or contact us directly.', [], 500);
}

$success = sv($form, 'success_message', 'Thank you! Your enquiry has been received.');
contact_reply(true, $success . " Your reference: $ref");
