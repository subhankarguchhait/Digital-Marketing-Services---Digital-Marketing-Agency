<?php defined('APP_NAME') || exit;
/** @var array $s */
$brands = public_collection('brands');
$cats = array_values(array_unique(array_filter(array_map(fn($b) => $b['category'], $brands))));
?>
<section class="section brands-page" data-section="portfolio">
  <div class="container">
    <?php sec_head($s, 'sec-head--center'); ?>
    <?php if (count($cats) > 1): ?>
      <div class="filters reveal" role="tablist" aria-label="Filter brands by category" data-brand-filters>
        <button type="button" role="tab" class="is-active" aria-selected="true" data-filter="all">All brands</button>
        <?php foreach ($cats as $c): ?><button type="button" role="tab" aria-selected="false" data-filter="<?= e($c) ?>"><?= e($c) ?></button><?php endforeach; ?>
      </div>
    <?php endif; ?>
    <div class="brand-grid" data-brand-grid>
      <?php foreach ($brands as $i => $b) echo brand_card($b, $i, false); ?>
      <?php if (!$brands): ?><p class="empty-state"><i class="fa-regular fa-folder-open"></i>No brands have been published yet.</p><?php endif; ?>
    </div>
  </div>
</section>

<div class="brand-modal" id="brandModal" role="dialog" aria-modal="true" aria-labelledby="brandModalTitle" hidden>
  <div class="brand-modal__backdrop" data-close></div>
  <article class="brand-modal__panel">
    <button class="brand-modal__close" type="button" data-close aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
    <div class="brand-modal__cover"><img alt="" data-m="cover"></div>
    <div class="brand-modal__body">
      <div class="brand-modal__head">
        <img class="brand-modal__logo" alt="" data-m="logo">
        <div><p class="brand-modal__cat" data-m="category"></p><h3 id="brandModalTitle" data-m="name"></h3></div>
      </div>
      <p class="brand-modal__desc" data-m="description"></p>
      <h4 data-m="services-title">Services delivered</h4>
      <ul class="chips" data-m="services"></ul>
      <div class="brand-modal__foot">
        <a class="btn btn--gold magnetic" data-m="website" target="_blank" rel="noopener"><span>Visit website</span><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
        <a class="btn btn--outline magnetic" href="<?= e(url('contact.php')) ?>" data-m="cta"><span>Start a similar project</span><i class="fa-solid fa-arrow-right"></i></a>
        <div class="socials socials--dark" data-m="social"></div>
      </div>
    </div>
  </article>
</div>
