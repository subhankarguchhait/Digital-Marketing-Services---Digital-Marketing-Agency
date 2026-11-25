<?php defined('APP_NAME') || exit;
/** @var array $s */
$industries = sl($s, 'industries') ?: default_industries();
$serviceOptions = sl($s, 'services');
if (!$serviceOptions) { $serviceOptions = array_map(fn($x) => $x['title'], public_collection('services')); $serviceOptions[] = 'Other'; }
$wanted = get_str('service', 150);
if ($wanted !== '' && !in_array($wanted, $serviceOptions, true)) $wanted = '';
$info = [
    'email' => ['fa-solid fa-envelope', 'Email'], 'phone' => ['fa-solid fa-phone', 'Phone'],
    'whatsapp' => ['fa-brands fa-whatsapp', 'WhatsApp'], 'address' => ['fa-solid fa-location-dot', 'Studio'],
];
?>
<section class="section contact" data-section="form">
  <div class="container contact__grid">
    <aside class="contact__info">
      <?php sec_head($s); ?>
      <ul class="info-list reveal">
        <?php foreach ($info as $type => [$icon, $label]): $val = setting($type); if ($val === '') continue; ?>
          <li><span class="info-list__icon"><i class="<?= $icon ?>" aria-hidden="true"></i></span><div><small><?= $label ?></small><a href="<?= e(contact_href($type, $val)) ?>"<?= in_array($type, ['whatsapp', 'address'], true) ? ' target="_blank" rel="noopener"' : '' ?>><?= e($val) ?></a></div></li>
        <?php endforeach; ?>
        <?php if (setting('hours') !== ''): ?><li><span class="info-list__icon"><i class="fa-regular fa-clock" aria-hidden="true"></i></span><div><small>Hours</small><span><?= e(setting('hours')) ?></span></div></li><?php endif; ?>
      </ul>
      <div class="contact__social reveal"><small>Follow the spark</small><?php social_links(site_socials(), 'socials socials--dark'); ?></div>
    </aside>

    <div class="contact__form-wrap reveal">
      <h2 class="contact__form-title"><?= rich(sv($s, 'form_title', 'Start your *project*')) ?></h2>
      <?php if (sv($s, 'content') !== ''): ?><p class="contact__form-text"><?= e(sv($s, 'content')) ?></p><?php endif; ?>
      <form class="form" id="contactForm" action="<?= e(url('actions/contact-submit.php')) ?>" method="post" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="form_stamp" value="<?= e(form_stamp()) ?>">
        <div class="row g-3">
          <div class="col-md-6"><div class="field"><input id="cf-name" name="name" type="text" placeholder=" " autocomplete="name" maxlength="100" required><label for="cf-name">Full name *</label><small class="field__err"></small></div></div>
          <div class="col-md-6"><div class="field"><input id="cf-email" name="email" type="email" placeholder=" " autocomplete="email" maxlength="150" required><label for="cf-email">Email address *</label><small class="field__err"></small></div></div>
          <div class="col-md-6"><div class="field"><input id="cf-phone" name="phone" type="tel" placeholder=" " autocomplete="tel" maxlength="20" required><label for="cf-phone">Phone number *</label><small class="field__err"></small></div></div>
          <div class="col-md-6"><div class="field"><input id="cf-company" name="company" type="text" placeholder=" " autocomplete="organization" maxlength="120"><label for="cf-company">Company / Brand</label><small class="field__err"></small></div></div>
          <div class="col-md-6"><div class="field field--select"><select id="cf-industry" name="industry" required><option value="" selected disabled>Select your industry</option><?php foreach ($industries as $o): ?><option value="<?= e($o) ?>"><?= e($o) ?></option><?php endforeach; ?></select><label for="cf-industry">Industry *</label><small class="field__err"></small></div></div>
          <div class="col-md-6"><div class="field field--select"><select id="cf-service" name="service" required><option value="" <?= $wanted === '' ? 'selected ' : '' ?>disabled>Select a service</option><?php foreach ($serviceOptions as $o): ?><option value="<?= e($o) ?>"<?= $o === $wanted ? ' selected' : '' ?>><?= e($o) ?></option><?php endforeach; ?></select><label for="cf-service">Interested service *</label><small class="field__err"></small></div></div>
          <div class="col-12"><div class="field"><input id="cf-subject" name="subject" type="text" placeholder=" " maxlength="150" required><label for="cf-subject">Subject *</label><small class="field__err"></small></div></div>
          <div class="col-12"><div class="field"><textarea id="cf-message" name="message" rows="5" placeholder=" " maxlength="3000" required></textarea><label for="cf-message">Tell us about your project *</label><small class="field__err"></small></div></div>
        </div>
        <div class="hp" aria-hidden="true"><label for="cf-website">Leave this field empty</label><input id="cf-website" name="website" type="text" tabindex="-1" autocomplete="off"></div>
        <label class="consent"><input type="checkbox" name="consent" value="1" required><span>I agree to be contacted by <?= e(setting('site_name')) ?> about this enquiry.</span></label>
        <small class="field__err" data-err="consent"></small>
        <button class="btn btn--spark btn--lg btn--block magnetic" type="submit"><span class="btn__label">Send enquiry</span><i class="fa-solid fa-paper-plane" aria-hidden="true"></i></button>
        <div class="form__status" role="status" aria-live="polite"></div>
      </form>
    </div>
  </div>
</section>
