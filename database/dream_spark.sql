-- =============================================================================
--  DREAM SPARK — MySQL 8 / MariaDB 10.4+ database
--  1. In phpMyAdmin create a database named  dream_spark  (collation utf8mb4_unicode_ci)
--  2. Select it, open the Import tab, choose this file and click Import.
--  The first administrator is created afterwards with admin/setup.php (no default password).
-- =============================================================================

SET NAMES utf8mb4;
SET time_zone = '+05:30';
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS enquiries;
DROP TABLE IF EXISTS media;
DROP TABLE IF EXISTS team_members;
DROP TABLE IF EXISTS testimonials;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS brands;
DROP TABLE IF EXISTS page_sections;
DROP TABLE IF EXISTS pages;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS admins;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- Administrators (passwords are bcrypt/argon hashes from password_hash())
-- -----------------------------------------------------------------------------
CREATE TABLE admins (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name            VARCHAR(120) NOT NULL,
  email           VARCHAR(190) NOT NULL,
  password_hash   VARCHAR(255) NOT NULL,
  role            ENUM('admin','editor') NOT NULL DEFAULT 'editor',
  status          ENUM('active','disabled') NOT NULL DEFAULT 'active',
  last_login      DATETIME NULL DEFAULT NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_admins_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Failed/successful logins for rate limiting (no foreign key: attempts may use unknown emails)
CREATE TABLE login_attempts (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  email           VARCHAR(190) NOT NULL,
  ip_address      VARCHAR(45) NOT NULL,
  success         TINYINT(1) NOT NULL DEFAULT 0,
  attempted_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_login_ip_time (ip_address, attempted_at),
  KEY idx_login_email_time (email, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Pages & their sections
--   Common fields live in columns; section-specific fields (lists, extra buttons…)
--   live in the JSON "extra" column, so new section types need no schema change.
-- -----------------------------------------------------------------------------
CREATE TABLE pages (
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  page_name         VARCHAR(100) NOT NULL,
  page_slug         VARCHAR(100) NOT NULL,
  meta_title        VARCHAR(160) NOT NULL DEFAULT '',
  meta_description  VARCHAR(320) NOT NULL DEFAULT '',
  status            ENUM('published','draft') NOT NULL DEFAULT 'published',
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_pages_slug (page_slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE page_sections (
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  page_id           INT UNSIGNED NOT NULL,
  section_key       VARCHAR(60) NOT NULL,
  section_title     VARCHAR(255) NOT NULL DEFAULT '',
  section_subtitle  VARCHAR(255) NOT NULL DEFAULT '',
  section_content   TEXT NULL,
  image             VARCHAR(500) NOT NULL DEFAULT '',
  button_text       VARCHAR(100) NOT NULL DEFAULT '',
  button_url        VARCHAR(500) NOT NULL DEFAULT '',
  extra             LONGTEXT NULL CHECK (extra IS NULL OR JSON_VALID(extra)),
  display_order     INT NOT NULL DEFAULT 0,
  is_visible        TINYINT(1) NOT NULL DEFAULT 1,
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_section_page_key (page_id, section_key),
  KEY idx_section_order (page_id, display_order),
  CONSTRAINT fk_sections_page FOREIGN KEY (page_id) REFERENCES pages (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Content collections
-- -----------------------------------------------------------------------------
CREATE TABLE brands (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name            VARCHAR(150) NOT NULL,
  slug            VARCHAR(160) NOT NULL,
  category        VARCHAR(100) NOT NULL DEFAULT '',
  description     TEXT NULL,
  logo            VARCHAR(500) NOT NULL DEFAULT '',
  cover_image     VARCHAR(500) NOT NULL DEFAULT '',
  services        LONGTEXT NULL CHECK (services IS NULL OR JSON_VALID(services)),
  website_url     VARCHAR(500) NOT NULL DEFAULT '',
  social_links    LONGTEXT NULL CHECK (social_links IS NULL OR JSON_VALID(social_links)),
  is_featured     TINYINT(1) NOT NULL DEFAULT 0,
  display_order   INT NOT NULL DEFAULT 0,
  status          ENUM('published','draft') NOT NULL DEFAULT 'published',
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_brands_slug (slug),
  KEY idx_brands_public (status, is_featured, display_order),
  KEY idx_brands_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE services (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title           VARCHAR(150) NOT NULL,
  slug            VARCHAR(160) NOT NULL,
  description     TEXT NULL,
  icon            VARCHAR(80) NOT NULL DEFAULT '',
  image           VARCHAR(500) NOT NULL DEFAULT '',
  display_order   INT NOT NULL DEFAULT 0,
  status          ENUM('published','draft') NOT NULL DEFAULT 'published',
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_services_slug (slug),
  KEY idx_services_public (status, display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE testimonials (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  client_name     VARCHAR(120) NOT NULL,
  designation     VARCHAR(120) NOT NULL DEFAULT '',
  company         VARCHAR(120) NOT NULL DEFAULT '',
  testimonial     TEXT NOT NULL,
  client_image    VARCHAR(500) NOT NULL DEFAULT '',
  rating          TINYINT UNSIGNED NOT NULL DEFAULT 5,
  display_order   INT NOT NULL DEFAULT 0,
  status          ENUM('published','draft') NOT NULL DEFAULT 'published',
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_testimonials_public (status, display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE team_members (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name            VARCHAR(120) NOT NULL,
  designation     VARCHAR(120) NOT NULL DEFAULT '',
  bio             TEXT NULL,
  profile_image   VARCHAR(500) NOT NULL DEFAULT '',
  social_links    LONGTEXT NULL CHECK (social_links IS NULL OR JSON_VALID(social_links)),
  display_order   INT NOT NULL DEFAULT 0,
  status          ENUM('published','draft') NOT NULL DEFAULT 'published',
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_team_public (status, display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Media library (files are stored in assets/uploads/YYYY/MM/)
-- -----------------------------------------------------------------------------
CREATE TABLE media (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  file_name       VARCHAR(190) NOT NULL,
  original_name   VARCHAR(190) NOT NULL DEFAULT '',
  file_path       VARCHAR(500) NOT NULL,
  file_type       VARCHAR(60) NOT NULL,
  file_size       INT UNSIGNED NOT NULL DEFAULT 0,
  width           INT UNSIGNED NULL DEFAULT NULL,
  height          INT UNSIGNED NULL DEFAULT NULL,
  alt_text        VARCHAR(255) NOT NULL DEFAULT '',
  folder          VARCHAR(40) NOT NULL DEFAULT 'general',
  uploaded_by     INT UNSIGNED NULL DEFAULT NULL,
  uploaded_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_media_path (file_path(255)),
  KEY idx_media_folder (folder, uploaded_at),
  CONSTRAINT fk_media_admin FOREIGN KEY (uploaded_by) REFERENCES admins (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Contact enquiries
-- -----------------------------------------------------------------------------
CREATE TABLE enquiries (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  enquiry_id      VARCHAR(30) NOT NULL,
  name            VARCHAR(100) NOT NULL,
  email           VARCHAR(150) NOT NULL,
  phone           VARCHAR(20) NOT NULL DEFAULT '',
  company         VARCHAR(120) NOT NULL DEFAULT '',
  industry        VARCHAR(100) NOT NULL DEFAULT '',
  service         VARCHAR(150) NOT NULL DEFAULT '',
  subject         VARCHAR(150) NOT NULL DEFAULT '',
  message         TEXT NOT NULL,
  status          ENUM('new','contacted','in_progress','converted','closed') NOT NULL DEFAULT 'new',
  admin_notes     TEXT NULL,
  message_hash    CHAR(64) NOT NULL,
  ip_address      VARCHAR(45) NOT NULL DEFAULT '',
  user_agent      VARCHAR(255) NOT NULL DEFAULT '',
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_enquiries_ref (enquiry_id),
  KEY idx_enquiries_status (status, created_at),
  KEY idx_enquiries_created (created_at),
  KEY idx_enquiries_email (email),
  KEY idx_enquiries_ip (ip_address, created_at),
  KEY idx_enquiries_hash (message_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Site-wide settings (key/value)
-- -----------------------------------------------------------------------------
CREATE TABLE settings (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  setting_key     VARCHAR(100) NOT NULL,
  setting_value   TEXT NULL,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_settings_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Audit trail of admin actions
-- -----------------------------------------------------------------------------
CREATE TABLE activity_logs (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  admin_id        INT UNSIGNED NULL DEFAULT NULL,
  action          VARCHAR(100) NOT NULL,
  description     VARCHAR(500) NOT NULL DEFAULT '',
  ip_address      VARCHAR(45) NOT NULL DEFAULT '',
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_activity_created (created_at),
  CONSTRAINT fk_activity_admin FOREIGN KEY (admin_id) REFERENCES admins (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- Starter content (replace placeholders from the admin panel before launch)
-- =============================================================================

INSERT INTO settings (setting_key, setting_value, updated_at) VALUES
  ('site_name', 'Dream Spark', '2026-11-04 10:00:00'),
  ('logo', 'assets/images/brand/dream-spark-logo-horizontal.png', '2026-11-04 10:00:00'),
  ('logo_stacked', 'assets/images/brand/dream-spark-logo-web.png', '2026-11-04 10:00:00'),
  ('favicon', 'assets/images/brand/favicon-192.png', '2026-11-04 10:00:00'),
  ('nav_cta_text', 'Start a Project', '2026-11-04 10:00:00'),
  ('nav_cta_link', 'contact.php', '2026-11-04 10:00:00'),
  ('email', 'hello@yourdomain.com', '2026-11-04 10:00:00'),
  ('phone', '+91 00000 00000', '2026-11-04 10:00:00'),
  ('whatsapp', '+91 00000 00000', '2026-11-04 10:00:00'),
  ('hours', 'Mon – Sat · 10:00 AM – 7:00 PM', '2026-11-04 10:00:00'),
  ('address', 'Kolkata, West Bengal, India', '2026-11-04 10:00:00'),
  ('social_instagram', 'https://instagram.com/', '2026-11-04 10:00:00'),
  ('social_facebook', 'https://facebook.com/', '2026-11-04 10:00:00'),
  ('social_linkedin', 'https://linkedin.com/', '2026-11-04 10:00:00'),
  ('social_youtube', '', '2026-11-04 10:00:00'),
  ('social_x', '', '2026-11-04 10:00:00'),
  ('social_behance', '', '2026-11-04 10:00:00'),
  ('footer_headline', 'Let''s ignite your *next chapter.*', '2026-11-04 10:00:00'),
  ('footer_cta_text', 'Talk to us', '2026-11-04 10:00:00'),
  ('footer_cta_link', 'contact.php', '2026-11-04 10:00:00'),
  ('footer_about', 'Dream Spark is a premium digital marketing agency helping jewellers, restaurants, hotels, clinics and institutions grow with cinematic storytelling and measurable results.', '2026-11-04 10:00:00'),
  ('footer_copyright', '© {year} Dream Spark. All rights reserved.', '2026-11-04 10:00:00'),
  ('footer_tagline', 'Crafted with a spark in India.', '2026-11-04 10:00:00'),
  ('seo_title', 'Dream Spark', '2026-11-04 10:00:00'),
  ('seo_description', 'Dream Spark is a premium digital marketing agency — branding, social media, performance marketing, websites and content production.', '2026-11-04 10:00:00'),
  ('seo_keywords', 'digital marketing agency, branding, social media marketing, SEO, website design, Kolkata, West Bengal', '2026-11-04 10:00:00'),
  ('og_image', '', '2026-11-04 10:00:00');

INSERT INTO pages (id, page_name, page_slug, meta_title, meta_description, status, created_at, updated_at) VALUES
  (1, 'Home', 'home', 'Premium Digital Marketing Agency', '', 'published', '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (2, 'About', 'about', 'About Us', 'The story, mission and team behind Dream Spark.', 'published', '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (3, 'Our Brands', 'brands', 'Our Brands', 'Brands Dream Spark has built, marketed and grown.', 'published', '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (4, 'Contact', 'contact', 'Contact', 'Start a project with Dream Spark.', 'published', '2026-11-04 10:00:00', '2026-11-04 10:00:00');

INSERT INTO page_sections (page_id, section_key, section_title, section_subtitle, section_content, image, button_text, button_url, extra, display_order, is_visible, created_at, updated_at) VALUES
  (1, 'hero', 'Where Creative *Ideas* Meet Digital Growth.', 'Premium Digital Marketing Agency', 'Strategy, cinematic content and performance marketing for brands that refuse to blend in.', 'assets/images/hero.svg', 'Start a Project', 'contact.php', '{"cta2_text":"Explore Our Brands","cta2_url":"brands.php","side_text":"Dream · Create · Ignite"}', 1, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (1, 'marquee', '', '', '', '', '', '', '{"items":["Brand Strategy","Social Media","Performance Ads","Websites","Content Production","SEO","Influencer Campaigns"]}', 2, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (1, 'about', 'Where creative *spark* meets measurable growth.', 'About Dream Spark', 'Dream Spark is a full-service digital marketing studio. We combine the craft of a film crew with the discipline of a performance team — so your brand looks extraordinary and grows predictably.

From heritage jewellers to boutique hotels, we build identities, campaigns and websites that people remember and act on.', 'assets/images/about.svg', 'More About Us', 'about.php', '{"points":["Strategy-first approach","In-house content studio","Transparent monthly reporting","Dedicated brand manager"],"badge_value":"5+","badge_label":"Industries we specialise in"}', 3, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (1, 'services', 'Services that *ignite* growth', 'What we do', '', '', '', '', '{"description":"Everything your brand needs to be seen, remembered and chosen — under one roof."}', 4, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (1, 'industries', 'Specialists in *your* world', 'Industries we serve', '', '', '', '', '{"description":"Deep experience in the sectors where trust, beauty and experience sell.","items":[{"icon":"fa-solid fa-gem","title":"Jewellery","text":"Bridal campaigns, product films and festive launches that sparkle online."},{"icon":"fa-solid fa-utensils","title":"Restaurant & Cafe","text":"Food films, reservations and local search that fill tables."},{"icon":"fa-solid fa-hotel","title":"Guest House & Hotel","text":"Direct-booking websites and travel content that reduce OTA commission."},{"icon":"fa-solid fa-stethoscope","title":"Medical & Healthcare","text":"Trust-led branding, patient education and appointment campaigns."},{"icon":"fa-solid fa-graduation-cap","title":"Educational Institution","text":"Admission campaigns and student stories that build reputation."}]}', 5, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (1, 'brands', 'Brands we''ve helped *shine*', 'Portfolio', '', '', 'View All Brands', 'brands.php', '{"description":"A glimpse of the businesses we build, market and grow.","limit":3}', 6, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (1, 'why', 'Built different, *on purpose.*', 'Why Dream Spark', '', '', 'Work With Us', 'contact.php', '{"description":"A small, senior team that treats your budget like our own.","items":[{"icon":"fa-solid fa-chess-knight","title":"Strategy before spend","text":"Every campaign starts with research and a plan you can see."},{"icon":"fa-solid fa-clapperboard","title":"Cinematic content","text":"Photo, video and design produced to a premium standard."},{"icon":"fa-solid fa-chart-line","title":"Performance obsessed","text":"We track leads, sales and ROI — not just likes."},{"icon":"fa-solid fa-handshake","title":"True partnership","text":"One dedicated manager, weekly updates, no jargon."}]}', 7, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (1, 'process', 'From first call to *first results*', 'How we work', '', '', '', '', '{"description":"","steps":[{"title":"Discover","text":"We study your brand, audience and competitors to find the story only you can tell."},{"title":"Strategise","text":"A clear roadmap of channels, content, budgets and KPIs — agreed before we spend a rupee."},{"title":"Create","text":"Cinematic visuals, sharp copy and high-converting pages from our in-house studio."},{"title":"Launch & Grow","text":"We launch, measure every week and keep optimising so results compound."}]}', 8, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (1, 'stats', '', '', '', '', '', '', '{"items":[{"value":"120","suffix":"+","label":"Campaigns launched"},{"value":"60","suffix":"+","label":"Brands served"},{"value":"5","suffix":"M+","label":"Monthly reach"},{"value":"98","suffix":"%","label":"Client retention"}]}', 9, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (1, 'testimonials', 'What our clients *say*', 'Kind words', '', '', '', '', '{"description":""}', 10, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (1, 'cta', 'Have a dream? Let''s give it a *spark.*', 'Ready when you are', 'Tell us about your brand and we''ll come back within one working day with ideas.', 'assets/images/cta.svg', 'Start a Project', 'contact.php', '{"cta2_text":"View Our Brands","cta2_url":"brands.php"}', 11, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (2, 'hero', 'A studio built on *curiosity* and craft.', 'About Dream Spark', 'We are strategists, storytellers and growth marketers on a mission to make great brands impossible to ignore.', 'assets/images/page-about.svg', '', '', '{}', 1, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (2, 'intro', 'Small team. *Big* ambition.', 'Who we are', 'Dream Spark began with a simple belief: businesses with real craft deserve marketing that looks and performs as well as they do.

Today we partner with jewellers, restaurants, hotels, healthcare providers and educational institutions — bringing them the strategy, content and technology usually reserved for national brands.', 'assets/images/intro.svg', '', '', '{"signature":"Founder Name","signature_role":"Founder, Dream Spark"}', 2, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (2, 'story', 'How the *spark* began', 'Our story', 'What started as a two-person studio has grown into a creative and performance team trusted by brands across India.', 'assets/images/story.svg', '', '', '{"milestones":[{"year":"Year 1","title":"The first spark","text":"Founded with a handful of local clients and a camera."},{"year":"Year 2","title":"In-house studio","text":"Launched our content studio for photo, video and design."},{"year":"Year 3","title":"Performance team","text":"Added paid media and analytics specialists."},{"year":"Today","title":"Full-service agency","text":"Strategy, content, ads and websites under one roof."}]}', 3, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (2, 'mission_vision', 'Mission & *Vision*', 'Purpose', '', 'assets/images/mission.svg', '', '', '{"description":"","mission_title":"Our Mission","mission":"To help ambitious businesses grow by pairing beautiful storytelling with honest, measurable marketing.","vision_title":"Our Vision","vision":"To be the most trusted creative growth partner for premium brands across India."}', 4, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (2, 'values', 'What we *stand* for', 'Core values', '', '', '', '', '{"description":"","items":[{"icon":"fa-solid fa-gem","title":"Craft","text":"We sweat the details others skip."},{"icon":"fa-solid fa-scale-balanced","title":"Honesty","text":"Clear numbers, clear advice, always."},{"icon":"fa-solid fa-bolt","title":"Momentum","text":"Ship, measure, improve — every week."},{"icon":"fa-solid fa-heart","title":"Care","text":"Your brand is treated like our own."}]}', 5, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (2, 'expertise', 'Skills sharpened *every day*', 'Our expertise', 'Our team blends creative and technical disciplines so every campaign is beautiful, findable and built to convert.', '', '', '', '{"items":[{"title":"Brand Strategy & Identity","percent":95},{"title":"Social Media Marketing","percent":92},{"title":"Performance Advertising","percent":88},{"title":"Web Design & Development","percent":90},{"title":"Photo & Video Production","percent":94}]}', 6, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (2, 'why', 'Why brands choose *Dream Spark*', 'Why Dream Spark', '', '', 'Start a Project', 'contact.php', '{"description":"Premium quality, startup speed and a team that answers the phone.","items":[{"icon":"fa-solid fa-user-tie","title":"Senior people only","text":"Your work is done by experienced specialists."},{"icon":"fa-solid fa-layer-group","title":"Everything in-house","text":"Strategy, shoots, design, ads and web."},{"icon":"fa-solid fa-file-invoice","title":"Transparent pricing","text":"Fixed scopes and monthly reports."},{"icon":"fa-solid fa-industry","title":"Industry know-how","text":"Retail, hospitality, healthcare and education."}]}', 7, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (2, 'process', 'A process that *delivers*', 'Working process', '', '', '', '', '{"description":"","steps":[{"title":"Discover","text":"We study your brand, audience and competitors to find the story only you can tell."},{"title":"Strategise","text":"A clear roadmap of channels, content, budgets and KPIs — agreed before we spend a rupee."},{"title":"Create","text":"Cinematic visuals, sharp copy and high-converting pages from our in-house studio."},{"title":"Launch & Grow","text":"We launch, measure every week and keep optimising so results compound."}]}', 8, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (2, 'team', 'Meet the *sparks*', 'The team', '', '', '', '', '{"description":"The people behind your next campaign."}', 9, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (2, 'cta', 'Let''s build something *remarkable.*', 'Ready when you are', 'Book a free 30-minute discovery call with our team.', 'assets/images/cta.svg', 'Start a Project', 'contact.php', '{"cta2_text":"View Our Brands","cta2_url":"brands.php"}', 10, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (3, 'hero', 'Our *Brands*', 'Portfolio', 'Every brand here has its own story. We helped tell it — beautifully and profitably.', 'assets/images/page-brands.svg', '', '', '{}', 1, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (3, 'portfolio', 'Brands we *grow*', 'Selected work', '', '', '', '', '{"description":"Tap any brand to see what we delivered."}', 2, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (3, 'cta', 'Your brand could be *next.*', 'Ready when you are', 'Let''s talk about where you want to go.', 'assets/images/cta.svg', 'Start a Project', 'contact.php', '{"cta2_text":"View Our Brands","cta2_url":"brands.php"}', 3, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (4, 'hero', 'Let''s start a *conversation.*', 'Contact', 'Tell us about your brand, your goals and your timeline. We reply within one working day.', 'assets/images/page-contact.svg', '', '', '{}', 1, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (4, 'form', 'We''d love to *hear* from you', 'Get in touch', 'Fill in the form and a strategist will get back to you.', '', '', '', '{"description":"Call, message on WhatsApp or drop by the studio.","form_title":"Start your *project*","industries":["Jewellery","Restaurant & Cafe","Guest House & Hotel","Medical & Healthcare","Educational Institution","Other"],"services":[],"success_message":"Thank you! Your enquiry has been received — we''ll be in touch within one working day."}', 2, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (4, 'map', '', '', '', '', '', '', '{"embed_url":"https://www.google.com/maps?q=Kolkata%2C%20West%20Bengal&output=embed"}', 3, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  (4, 'cta', 'Prefer a quick *chat?*', 'Ready when you are', 'Message us on WhatsApp — we usually reply within the hour during working hours.', 'assets/images/cta.svg', 'Chat on WhatsApp', '#whatsapp', '{"cta2_text":"","cta2_url":""}', 4, 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00');

INSERT INTO services (title, slug, icon, image, description, status, display_order, created_at, updated_at) VALUES
  ('Brand Strategy & Identity', 'brand-strategy-identity', 'fa-solid fa-chess-queen', '', 'Positioning, naming, logo and visual identity systems that make premium brands instantly recognisable.', 'published', 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  ('Social Media Marketing', 'social-media-marketing', 'fa-solid fa-hashtag', '', 'Content calendars, reels, community management and influencer collaborations that build loyal audiences.', 'published', 2, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  ('Performance Advertising', 'performance-advertising', 'fa-solid fa-bullseye', '', 'Meta and Google Ads campaigns engineered for leads, bookings and sales — with full ROI tracking.', 'published', 3, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  ('Website Design & Development', 'website-design-development', 'fa-solid fa-laptop-code', '', 'Fast, beautiful, mobile-first websites and landing pages designed to convert visitors into customers.', 'published', 4, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  ('SEO & Local Search', 'seo-local-search', 'fa-solid fa-magnifying-glass-chart', '', 'Technical SEO, content and Google Business Profile optimisation so customers find you first.', 'published', 5, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  ('Photo & Video Production', 'photo-video-production', 'fa-solid fa-video', '', 'Cinematic product shoots, brand films and reels produced by our in-house creative studio.', 'published', 6, '2026-11-04 10:00:00', '2026-11-04 10:00:00');

INSERT INTO testimonials (client_name, designation, company, testimonial, client_image, rating, status, display_order, created_at, updated_at) VALUES
  ('Client Name', 'Founder', 'Your Client''s Brand', 'Replace this with a real testimonial from a client. Short, specific quotes about results work best.', '', 5, 'published', 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  ('Client Name', 'Marketing Head', 'Your Client''s Brand', 'Add, edit or hide testimonials from Admin → Testimonials. Only published ones appear here.', '', 5, 'published', 2, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  ('Client Name', 'Director', 'Your Client''s Brand', 'Real words from real clients build trust. Ask for permission before publishing their name and company.', '', 5, 'published', 3, '2026-11-04 10:00:00', '2026-11-04 10:00:00');

INSERT INTO team_members (name, designation, bio, profile_image, social_links, status, display_order, created_at, updated_at) VALUES
  ('Founder Name', 'Founder & Creative Director', 'Leads strategy and creative direction.', 'assets/images/team/member-1.svg', '{"linkedin":"","instagram":"","x":""}', 'published', 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  ('Team Member', 'Head of Performance', 'Runs paid media and analytics.', 'assets/images/team/member-2.svg', '{"linkedin":"","instagram":"","x":""}', 'published', 2, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  ('Team Member', 'Lead Designer', 'Crafts identities and campaign visuals.', 'assets/images/team/member-3.svg', '{"linkedin":"","instagram":"","x":""}', 'published', 3, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  ('Team Member', 'Content Producer', 'Directs photo and video shoots.', 'assets/images/team/member-4.svg', '{"linkedin":"","instagram":"","x":""}', 'published', 4, '2026-11-04 10:00:00', '2026-11-04 10:00:00');

INSERT INTO brands (name, slug, category, description, logo, cover_image, services, website_url, social_links, is_featured, status, display_order, created_at, updated_at) VALUES
  ('Aurelia Jewels', 'aurelia-jewels', 'Jewellery', 'A heritage jewellery house re-introduced to a younger audience with a cinematic bridal campaign, refreshed identity and an Instagram-first content strategy.', 'assets/images/brands/aurelia-logo.svg', 'assets/images/brands/aurelia-cover.svg', '["Brand Identity","Product Photography","Social Media","Meta Ads"]', '', '{"instagram":"","facebook":"","linkedin":"","youtube":"","x":""}', 1, 'published', 1, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  ('Saffron Courtyard', 'saffron-courtyard', 'Restaurant & Cafe', 'A courtyard restaurant and cafe brought to life with food films, a reservation-focused website and local search optimisation.', 'assets/images/brands/saffron-logo.svg', 'assets/images/brands/saffron-cover.svg', '["Food Videography","Website","Local SEO","Influencer Marketing"]', '', '{"instagram":"","facebook":"","linkedin":"","youtube":"","x":""}', 1, 'published', 2, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  ('The Hillside Retreat', 'the-hillside-retreat', 'Guest House & Hotel', 'A boutique guest house that moved from OTA dependence to direct bookings with a new website, Google Ads and an always-on content plan.', 'assets/images/brands/hillside-logo.svg', 'assets/images/brands/hillside-cover.svg', '["Booking Website","Google Ads","Content Production"]', '', '{"instagram":"","facebook":"","linkedin":"","youtube":"","x":""}', 1, 'published', 3, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  ('CarePoint Clinic', 'carepoint-clinic', 'Medical & Healthcare', 'A multi-speciality clinic positioned around trust and clarity, with patient-education content and appointment-focused campaigns.', 'assets/images/brands/carepoint-logo.svg', 'assets/images/brands/carepoint-cover.svg', '["Brand Strategy","Website","Lead Generation"]', '', '{"instagram":"","facebook":"","linkedin":"","youtube":"","x":""}', 0, 'published', 4, '2026-11-04 10:00:00', '2026-11-04 10:00:00'),
  ('Bright Minds Academy', 'bright-minds-academy', 'Educational Institution', 'An educational institution whose admissions season was transformed with student-story films and targeted lead campaigns.', 'assets/images/brands/brightminds-logo.svg', 'assets/images/brands/brightminds-cover.svg', '["Admissions Campaign","Video Production","Social Media"]', '', '{"instagram":"","facebook":"","linkedin":"","youtube":"","x":""}', 0, 'published', 5, '2026-11-04 10:00:00', '2026-11-04 10:00:00');

-- Done. Next: open admin/setup.php to create your administrator account.
