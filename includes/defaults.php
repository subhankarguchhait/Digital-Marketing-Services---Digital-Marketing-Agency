<?php
/**
 * Built-in starter content.
 *  - database/dream_spark.sql seeds exactly this content.
 *  - The public site falls back to it when a database record is missing (e.g. before the SQL is imported).
 * After installation, edit everything from the admin panel — not here.
 * Contact details, testimonials, team names and statistics are PLACEHOLDERS: replace them before launch.
 */
declare(strict_types=1);
defined('APP_NAME') || exit; // no direct access

function default_settings(): array
{
    return [
        'site_name' => 'Dream Spark',
        'logo' => 'assets/images/brand/dream-spark-logo-horizontal.png',
        'logo_stacked' => 'assets/images/brand/dream-spark-logo-web.png',
        'favicon' => 'assets/images/brand/favicon-192.png',
        'nav_cta_text' => 'Start a Project',
        'nav_cta_link' => 'contact.php',
        'email' => 'hello@yourdomain.com',
        'phone' => '+91 00000 00000',
        'whatsapp' => '+91 00000 00000',
        'hours' => 'Mon – Sat · 10:00 AM – 7:00 PM',
        'address' => 'Kolkata, West Bengal, India',
        'social_instagram' => 'https://instagram.com/',
        'social_facebook' => 'https://facebook.com/',
        'social_linkedin' => 'https://linkedin.com/',
        'social_youtube' => '',
        'social_x' => '',
        'social_behance' => '',
        'footer_headline' => "Let's ignite your *next chapter.*",
        'footer_cta_text' => 'Talk to us',
        'footer_cta_link' => 'contact.php',
        'footer_about' => 'Dream Spark is a premium digital marketing agency helping jewellers, restaurants, hotels, clinics and institutions grow with cinematic storytelling and measurable results.',
        'footer_copyright' => '© {year} Dream Spark. All rights reserved.',
        'footer_tagline' => 'Crafted with a spark in India.',
        'seo_title' => 'Dream Spark',
        'seo_description' => 'Dream Spark is a premium digital marketing agency — branding, social media, performance marketing, websites and content production.',
        'seo_keywords' => 'digital marketing agency, branding, social media marketing, SEO, website design, Kolkata, West Bengal',
        'og_image' => '',
    ];
}

function default_steps(): array
{
    return [
        ['title' => 'Discover', 'text' => 'We study your brand, audience and competitors to find the story only you can tell.'],
        ['title' => 'Strategise', 'text' => 'A clear roadmap of channels, content, budgets and KPIs — agreed before we spend a rupee.'],
        ['title' => 'Create', 'text' => 'Cinematic visuals, sharp copy and high-converting pages from our in-house studio.'],
        ['title' => 'Launch & Grow', 'text' => 'We launch, measure every week and keep optimising so results compound.'],
    ];
}

function default_cta(string $title, string $text): array
{
    return [
        'eyebrow' => 'Ready when you are', 'title' => $title, 'content' => $text,
        'button_text' => 'Start a Project', 'button_url' => 'contact.php',
        'cta2_text' => 'View Our Brands', 'cta2_url' => 'brands.php',
        'image' => 'assets/images/cta.svg',
    ];
}

function default_industries(): array
{
    return ['Jewellery', 'Restaurant & Cafe', 'Guest House & Hotel', 'Medical & Healthcare', 'Educational Institution', 'Other'];
}

function default_pages(): array
{
    $I = 'assets/images/';
    return [
        'home' => [
            'meta' => ['name' => 'Home', 'title' => 'Premium Digital Marketing Agency', 'description' => ''],
            'sections' => [
                'hero' => [
                    'eyebrow' => 'Premium Digital Marketing Agency',
                    'title' => 'Where Creative *Ideas* Meet Digital Growth.',
                    'content' => 'Strategy, cinematic content and performance marketing for brands that refuse to blend in.',
                    'button_text' => 'Start a Project', 'button_url' => 'contact.php',
                    'cta2_text' => 'Explore Our Brands', 'cta2_url' => 'brands.php',
                    'side_text' => 'Dream · Create · Ignite',
                    'image' => $I . 'hero.svg',
                ],
                'marquee' => ['items' => ['Brand Strategy', 'Social Media', 'Performance Ads', 'Websites', 'Content Production', 'SEO', 'Influencer Campaigns']],
                'about' => [
                    'eyebrow' => 'About Dream Spark',
                    'title' => 'Where creative *spark* meets measurable growth.',
                    'content' => "Dream Spark is a full-service digital marketing studio. We combine the craft of a film crew with the discipline of a performance team — so your brand looks extraordinary and grows predictably.\n\nFrom heritage jewellers to boutique hotels, we build identities, campaigns and websites that people remember and act on.",
                    'points' => ['Strategy-first approach', 'In-house content studio', 'Transparent monthly reporting', 'Dedicated brand manager'],
                    'image' => $I . 'about.svg',
                    'badge_value' => '5+', 'badge_label' => 'Industries we specialise in',
                    'button_text' => 'More About Us', 'button_url' => 'about.php',
                ],
                'services' => ['eyebrow' => 'What we do', 'title' => 'Services that *ignite* growth', 'description' => 'Everything your brand needs to be seen, remembered and chosen — under one roof.'],
                'industries' => [
                    'eyebrow' => 'Industries we serve', 'title' => 'Specialists in *your* world', 'description' => 'Deep experience in the sectors where trust, beauty and experience sell.',
                    'items' => [
                        ['icon' => 'fa-solid fa-gem', 'title' => 'Jewellery', 'text' => 'Bridal campaigns, product films and festive launches that sparkle online.'],
                        ['icon' => 'fa-solid fa-utensils', 'title' => 'Restaurant & Cafe', 'text' => 'Food films, reservations and local search that fill tables.'],
                        ['icon' => 'fa-solid fa-hotel', 'title' => 'Guest House & Hotel', 'text' => 'Direct-booking websites and travel content that reduce OTA commission.'],
                        ['icon' => 'fa-solid fa-stethoscope', 'title' => 'Medical & Healthcare', 'text' => 'Trust-led branding, patient education and appointment campaigns.'],
                        ['icon' => 'fa-solid fa-graduation-cap', 'title' => 'Educational Institution', 'text' => 'Admission campaigns and student stories that build reputation.'],
                    ],
                ],
                'brands' => [
                    'eyebrow' => 'Portfolio', 'title' => 'Brands we\'ve helped *shine*', 'description' => 'A glimpse of the businesses we build, market and grow.',
                    'limit' => 3, 'button_text' => 'View All Brands', 'button_url' => 'brands.php',
                ],
                'why' => [
                    'eyebrow' => 'Why Dream Spark', 'title' => 'Built different, *on purpose.*', 'description' => 'A small, senior team that treats your budget like our own.',
                    'button_text' => 'Work With Us', 'button_url' => 'contact.php',
                    'items' => [
                        ['icon' => 'fa-solid fa-chess-knight', 'title' => 'Strategy before spend', 'text' => 'Every campaign starts with research and a plan you can see.'],
                        ['icon' => 'fa-solid fa-clapperboard', 'title' => 'Cinematic content', 'text' => 'Photo, video and design produced to a premium standard.'],
                        ['icon' => 'fa-solid fa-chart-line', 'title' => 'Performance obsessed', 'text' => 'We track leads, sales and ROI — not just likes.'],
                        ['icon' => 'fa-solid fa-handshake', 'title' => 'True partnership', 'text' => 'One dedicated manager, weekly updates, no jargon.'],
                    ],
                ],
                'process' => ['eyebrow' => 'How we work', 'title' => 'From first call to *first results*', 'description' => '', 'steps' => default_steps()],
                'stats' => ['items' => [
                    ['value' => '120', 'suffix' => '+', 'label' => 'Campaigns launched'],
                    ['value' => '60', 'suffix' => '+', 'label' => 'Brands served'],
                    ['value' => '5', 'suffix' => 'M+', 'label' => 'Monthly reach'],
                    ['value' => '98', 'suffix' => '%', 'label' => 'Client retention'],
                ]],
                'testimonials' => ['eyebrow' => 'Kind words', 'title' => 'What our clients *say*', 'description' => ''],
                'cta' => default_cta('Have a dream? Let\'s give it a *spark.*', 'Tell us about your brand and we\'ll come back within one working day with ideas.'),
            ],
        ],
        'about' => [
            'meta' => ['name' => 'About', 'title' => 'About Us', 'description' => 'The story, mission and team behind Dream Spark.'],
            'sections' => [
                'hero' => ['eyebrow' => 'About Dream Spark', 'title' => 'A studio built on *curiosity* and craft.', 'content' => 'We are strategists, storytellers and growth marketers on a mission to make great brands impossible to ignore.', 'image' => $I . 'page-about.svg'],
                'intro' => [
                    'eyebrow' => 'Who we are', 'title' => 'Small team. *Big* ambition.',
                    'content' => "Dream Spark began with a simple belief: businesses with real craft deserve marketing that looks and performs as well as they do.\n\nToday we partner with jewellers, restaurants, hotels, healthcare providers and educational institutions — bringing them the strategy, content and technology usually reserved for national brands.",
                    'signature' => 'Founder Name', 'signature_role' => 'Founder, Dream Spark', 'image' => $I . 'intro.svg',
                ],
                'story' => [
                    'eyebrow' => 'Our story', 'title' => 'How the *spark* began',
                    'content' => 'What started as a two-person studio has grown into a creative and performance team trusted by brands across India.',
                    'image' => $I . 'story.svg',
                    'milestones' => [
                        ['year' => 'Year 1', 'title' => 'The first spark', 'text' => 'Founded with a handful of local clients and a camera.'],
                        ['year' => 'Year 2', 'title' => 'In-house studio', 'text' => 'Launched our content studio for photo, video and design.'],
                        ['year' => 'Year 3', 'title' => 'Performance team', 'text' => 'Added paid media and analytics specialists.'],
                        ['year' => 'Today', 'title' => 'Full-service agency', 'text' => 'Strategy, content, ads and websites under one roof.'],
                    ],
                ],
                'mission_vision' => [
                    'eyebrow' => 'Purpose', 'title' => 'Mission & *Vision*', 'description' => '',
                    'mission_title' => 'Our Mission', 'mission' => 'To help ambitious businesses grow by pairing beautiful storytelling with honest, measurable marketing.',
                    'vision_title' => 'Our Vision', 'vision' => 'To be the most trusted creative growth partner for premium brands across India.',
                    'image' => $I . 'mission.svg',
                ],
                'values' => [
                    'eyebrow' => 'Core values', 'title' => 'What we *stand* for', 'description' => '',
                    'items' => [
                        ['icon' => 'fa-solid fa-gem', 'title' => 'Craft', 'text' => 'We sweat the details others skip.'],
                        ['icon' => 'fa-solid fa-scale-balanced', 'title' => 'Honesty', 'text' => 'Clear numbers, clear advice, always.'],
                        ['icon' => 'fa-solid fa-bolt', 'title' => 'Momentum', 'text' => 'Ship, measure, improve — every week.'],
                        ['icon' => 'fa-solid fa-heart', 'title' => 'Care', 'text' => 'Your brand is treated like our own.'],
                    ],
                ],
                'expertise' => [
                    'eyebrow' => 'Our expertise', 'title' => 'Skills sharpened *every day*',
                    'content' => 'Our team blends creative and technical disciplines so every campaign is beautiful, findable and built to convert.',
                    'items' => [
                        ['title' => 'Brand Strategy & Identity', 'percent' => 95], ['title' => 'Social Media Marketing', 'percent' => 92],
                        ['title' => 'Performance Advertising', 'percent' => 88], ['title' => 'Web Design & Development', 'percent' => 90],
                        ['title' => 'Photo & Video Production', 'percent' => 94],
                    ],
                ],
                'why' => [
                    'eyebrow' => 'Why Dream Spark', 'title' => 'Why brands choose *Dream Spark*', 'description' => 'Premium quality, startup speed and a team that answers the phone.',
                    'button_text' => 'Start a Project', 'button_url' => 'contact.php',
                    'items' => [
                        ['icon' => 'fa-solid fa-user-tie', 'title' => 'Senior people only', 'text' => 'Your work is done by experienced specialists.'],
                        ['icon' => 'fa-solid fa-layer-group', 'title' => 'Everything in-house', 'text' => 'Strategy, shoots, design, ads and web.'],
                        ['icon' => 'fa-solid fa-file-invoice', 'title' => 'Transparent pricing', 'text' => 'Fixed scopes and monthly reports.'],
                        ['icon' => 'fa-solid fa-industry', 'title' => 'Industry know-how', 'text' => 'Retail, hospitality, healthcare and education.'],
                    ],
                ],
                'process' => ['eyebrow' => 'Working process', 'title' => 'A process that *delivers*', 'description' => '', 'steps' => default_steps()],
                'team' => ['eyebrow' => 'The team', 'title' => 'Meet the *sparks*', 'description' => 'The people behind your next campaign.'],
                'cta' => default_cta('Let\'s build something *remarkable.*', 'Book a free 30-minute discovery call with our team.'),
            ],
        ],
        'brands' => [
            'meta' => ['name' => 'Our Brands', 'title' => 'Our Brands', 'description' => 'Brands Dream Spark has built, marketed and grown.'],
            'sections' => [
                'hero' => ['eyebrow' => 'Portfolio', 'title' => 'Our *Brands*', 'content' => 'Every brand here has its own story. We helped tell it — beautifully and profitably.', 'image' => $I . 'page-brands.svg'],
                'portfolio' => ['eyebrow' => 'Selected work', 'title' => 'Brands we *grow*', 'description' => 'Tap any brand to see what we delivered.'],
                'cta' => default_cta('Your brand could be *next.*', 'Let\'s talk about where you want to go.'),
            ],
        ],
        'contact' => [
            'meta' => ['name' => 'Contact', 'title' => 'Contact', 'description' => 'Start a project with Dream Spark.'],
            'sections' => [
                'hero' => ['eyebrow' => 'Contact', 'title' => 'Let\'s start a *conversation.*', 'content' => 'Tell us about your brand, your goals and your timeline. We reply within one working day.', 'image' => $I . 'page-contact.svg'],
                'form' => [
                    'eyebrow' => 'Get in touch', 'title' => 'We\'d love to *hear* from you', 'description' => 'Call, message on WhatsApp or drop by the studio.',
                    'form_title' => 'Start your *project*', 'content' => 'Fill in the form and a strategist will get back to you.',
                    'industries' => default_industries(),
                    'services' => [],
                    'success_message' => 'Thank you! Your enquiry has been received — we\'ll be in touch within one working day.',
                ],
                'map' => ['embed_url' => 'https://www.google.com/maps?q=Kolkata%2C%20West%20Bengal&output=embed'],
                'cta' => array_merge(default_cta('Prefer a quick *chat?*', 'Message us on WhatsApp — we usually reply within the hour during working hours.'),
                    ['button_text' => 'Chat on WhatsApp', 'button_url' => '#whatsapp', 'cta2_text' => '', 'cta2_url' => '']),
            ],
        ],
    ];
}

function default_collections(): array
{
    $B = 'assets/images/brands/';
    $T = 'assets/images/team/';
    $noSocial = ['instagram' => '', 'facebook' => '', 'linkedin' => '', 'youtube' => '', 'x' => ''];
    return [
        'services' => [
            ['title' => 'Brand Strategy & Identity', 'slug' => 'brand-strategy-identity', 'icon' => 'fa-solid fa-chess-queen', 'image' => '', 'description' => 'Positioning, naming, logo and visual identity systems that make premium brands instantly recognisable.', 'status' => 'published'],
            ['title' => 'Social Media Marketing', 'slug' => 'social-media-marketing', 'icon' => 'fa-solid fa-hashtag', 'image' => '', 'description' => 'Content calendars, reels, community management and influencer collaborations that build loyal audiences.', 'status' => 'published'],
            ['title' => 'Performance Advertising', 'slug' => 'performance-advertising', 'icon' => 'fa-solid fa-bullseye', 'image' => '', 'description' => 'Meta and Google Ads campaigns engineered for leads, bookings and sales — with full ROI tracking.', 'status' => 'published'],
            ['title' => 'Website Design & Development', 'slug' => 'website-design-development', 'icon' => 'fa-solid fa-laptop-code', 'image' => '', 'description' => 'Fast, beautiful, mobile-first websites and landing pages designed to convert visitors into customers.', 'status' => 'published'],
            ['title' => 'SEO & Local Search', 'slug' => 'seo-local-search', 'icon' => 'fa-solid fa-magnifying-glass-chart', 'image' => '', 'description' => 'Technical SEO, content and Google Business Profile optimisation so customers find you first.', 'status' => 'published'],
            ['title' => 'Photo & Video Production', 'slug' => 'photo-video-production', 'icon' => 'fa-solid fa-video', 'image' => '', 'description' => 'Cinematic product shoots, brand films and reels produced by our in-house creative studio.', 'status' => 'published'],
        ],
        'testimonials' => [
            ['client_name' => 'Client Name', 'designation' => 'Founder', 'company' => 'Your Client\'s Brand', 'testimonial' => 'Replace this with a real testimonial from a client. Short, specific quotes about results work best.', 'client_image' => '', 'rating' => 5, 'status' => 'published'],
            ['client_name' => 'Client Name', 'designation' => 'Marketing Head', 'company' => 'Your Client\'s Brand', 'testimonial' => 'Add, edit or hide testimonials from Admin → Testimonials. Only published ones appear here.', 'client_image' => '', 'rating' => 5, 'status' => 'published'],
            ['client_name' => 'Client Name', 'designation' => 'Director', 'company' => 'Your Client\'s Brand', 'testimonial' => 'Real words from real clients build trust. Ask for permission before publishing their name and company.', 'client_image' => '', 'rating' => 5, 'status' => 'published'],
        ],
        'team' => [
            ['name' => 'Founder Name', 'designation' => 'Founder & Creative Director', 'bio' => 'Leads strategy and creative direction.', 'profile_image' => $T . 'member-1.svg', 'social_links' => ['linkedin' => '', 'instagram' => '', 'x' => ''], 'status' => 'published'],
            ['name' => 'Team Member', 'designation' => 'Head of Performance', 'bio' => 'Runs paid media and analytics.', 'profile_image' => $T . 'member-2.svg', 'social_links' => ['linkedin' => '', 'instagram' => '', 'x' => ''], 'status' => 'published'],
            ['name' => 'Team Member', 'designation' => 'Lead Designer', 'bio' => 'Crafts identities and campaign visuals.', 'profile_image' => $T . 'member-3.svg', 'social_links' => ['linkedin' => '', 'instagram' => '', 'x' => ''], 'status' => 'published'],
            ['name' => 'Team Member', 'designation' => 'Content Producer', 'bio' => 'Directs photo and video shoots.', 'profile_image' => $T . 'member-4.svg', 'social_links' => ['linkedin' => '', 'instagram' => '', 'x' => ''], 'status' => 'published'],
        ],
        'brands' => [
            ['name' => 'Aurelia Jewels', 'slug' => 'aurelia-jewels', 'category' => 'Jewellery', 'description' => 'A heritage jewellery house re-introduced to a younger audience with a cinematic bridal campaign, refreshed identity and an Instagram-first content strategy.', 'logo' => $B . 'aurelia-logo.svg', 'cover_image' => $B . 'aurelia-cover.svg', 'services' => ['Brand Identity', 'Product Photography', 'Social Media', 'Meta Ads'], 'website_url' => '', 'social_links' => $noSocial, 'is_featured' => 1, 'status' => 'published'],
            ['name' => 'Saffron Courtyard', 'slug' => 'saffron-courtyard', 'category' => 'Restaurant & Cafe', 'description' => 'A courtyard restaurant and cafe brought to life with food films, a reservation-focused website and local search optimisation.', 'logo' => $B . 'saffron-logo.svg', 'cover_image' => $B . 'saffron-cover.svg', 'services' => ['Food Videography', 'Website', 'Local SEO', 'Influencer Marketing'], 'website_url' => '', 'social_links' => $noSocial, 'is_featured' => 1, 'status' => 'published'],
            ['name' => 'The Hillside Retreat', 'slug' => 'the-hillside-retreat', 'category' => 'Guest House & Hotel', 'description' => 'A boutique guest house that moved from OTA dependence to direct bookings with a new website, Google Ads and an always-on content plan.', 'logo' => $B . 'hillside-logo.svg', 'cover_image' => $B . 'hillside-cover.svg', 'services' => ['Booking Website', 'Google Ads', 'Content Production'], 'website_url' => '', 'social_links' => $noSocial, 'is_featured' => 1, 'status' => 'published'],
            ['name' => 'CarePoint Clinic', 'slug' => 'carepoint-clinic', 'category' => 'Medical & Healthcare', 'description' => 'A multi-speciality clinic positioned around trust and clarity, with patient-education content and appointment-focused campaigns.', 'logo' => $B . 'carepoint-logo.svg', 'cover_image' => $B . 'carepoint-cover.svg', 'services' => ['Brand Strategy', 'Website', 'Lead Generation'], 'website_url' => '', 'social_links' => $noSocial, 'is_featured' => 0, 'status' => 'published'],
            ['name' => 'Bright Minds Academy', 'slug' => 'bright-minds-academy', 'category' => 'Educational Institution', 'description' => 'An educational institution whose admissions season was transformed with student-story films and targeted lead campaigns.', 'logo' => $B . 'brightminds-logo.svg', 'cover_image' => $B . 'brightminds-cover.svg', 'services' => ['Admissions Campaign', 'Video Production', 'Social Media'], 'website_url' => '', 'social_links' => $noSocial, 'is_featured' => 0, 'status' => 'published'],
        ],
    ];
}
