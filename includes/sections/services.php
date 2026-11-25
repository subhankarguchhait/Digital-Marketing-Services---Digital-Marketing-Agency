<?php defined('APP_NAME') || exit;
/** @var array $s */ $services = public_collection('services'); ?>
<section class="section services" data-section="services">
  <div class="container">
    <?php sec_head($s, 'sec-head--center'); ?>
    <div class="services__grid">
      <?php foreach ($services as $i => $svc): ?>
        <article class="service-card reveal-card tilt" style="--d:<?= ($i % 3) * 0.12 ?>s">
          <span class="service-card__num"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
          <?php if (media_url($svc['image'] ?? '')): ?>
            <div class="service-card__img"><?= img_tag($svc['image'], $svc['title']) ?></div>
          <?php else: ?>
            <div class="service-card__icon"><i class="<?= e(safe_icon($svc['icon'] ?? '')) ?>" aria-hidden="true"></i></div>
          <?php endif; ?>
          <h3><?= e($svc['title']) ?></h3>
          <p><?= e($svc['description']) ?></p>
          <a class="service-card__more" href="<?= e(url('contact.php?service=' . rawurlencode($svc['title']))) ?>">Enquire now <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </article>
      <?php endforeach; ?>
      <?php if (!$services): ?><p class="empty-state"><i class="fa-regular fa-folder-open"></i>Services will appear here once published.</p><?php endif; ?>
    </div>
  </div>
</section>
