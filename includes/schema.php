<?php
/**
 * Content schemas: every editable field of the CMS is described here once.
 * The admin forms, the server-side validator and the public templates all read from these definitions.
 *
 * Field keys:  k (name) · label · t (type) · required · help · placeholder · max · min · maxlen · rows
 *              options · fields (repeater/group) · singular · item_label · folder · full · default
 * Types:       text textarea email url link number toggle status select tags icon image repeater group
 *
 * Page-section fields named title / eyebrow / content / image / button_text / button_url are stored in
 * dedicated page_sections columns; all other fields go into the section's "extra" JSON column.
 */
declare(strict_types=1);
defined('APP_NAME') || exit; // no direct access

const RICH_HELP = 'Wrap a word in *asterisks* to show it in gold italics.';

function f_heading(bool $description = true): array
{
    $f = [
        ['k' => 'eyebrow', 'label' => 'Small label above the title', 'placeholder' => 'e.g. What we do', 'maxlen' => 80],
        ['k' => 'title', 'label' => 'Section title', 'required' => true, 'help' => RICH_HELP, 'full' => true, 'maxlen' => 160],
    ];
    if ($description) $f[] = ['k' => 'description', 'label' => 'Short description', 't' => 'textarea', 'rows' => 2, 'maxlen' => 300];
    return $f;
}

function f_button(string $label = 'Button'): array
{
    return [
        ['k' => 'button_text', 'label' => "$label text", 'maxlen' => 60],
        ['k' => 'button_url', 'label' => "$label link", 't' => 'link'],
    ];
}

function f_button2(): array
{
    return [
        ['k' => 'cta2_text', 'label' => 'Second button text', 'help' => 'Leave empty to hide.', 'maxlen' => 60],
        ['k' => 'cta2_url', 'label' => 'Second button link', 't' => 'link'],
    ];
}

function f_icon_items(string $singular = 'item'): array
{
    return ['k' => 'items', 'label' => 'Items', 't' => 'repeater', 'singular' => $singular, 'item_label' => 'title', 'max' => 12, 'fields' => [
        ['k' => 'icon', 'label' => 'Icon', 't' => 'icon'],
        ['k' => 'title', 'label' => 'Title', 'required' => true, 'maxlen' => 80],
        ['k' => 'text', 'label' => 'Text', 't' => 'textarea', 'rows' => 2, 'maxlen' => 240],
    ]];
}

function f_steps(): array
{
    return ['k' => 'steps', 'label' => 'Steps', 't' => 'repeater', 'singular' => 'step', 'item_label' => 'title', 'max' => 6, 'fields' => [
        ['k' => 'title', 'label' => 'Step title', 'required' => true, 'maxlen' => 60],
        ['k' => 'text', 'label' => 'Description', 't' => 'textarea', 'rows' => 2, 'maxlen' => 240],
    ]];
}

function s_page_hero(string $folder): array
{
    return ['label' => 'Hero banner', 'icon' => 'fa-solid fa-panorama', 'template' => 'page-hero', 'fields' => [
        ['k' => 'eyebrow', 'label' => 'Small label', 'maxlen' => 80],
        ['k' => 'title', 'label' => 'Heading', 'required' => true, 'help' => RICH_HELP, 'full' => true, 'maxlen' => 160],
        ['k' => 'content', 'label' => 'Subtitle', 't' => 'textarea', 'rows' => 2, 'maxlen' => 300],
        ['k' => 'image', 'label' => 'Background image', 't' => 'image', 'folder' => $folder],
    ]];
}

function s_cta(): array
{
    return ['label' => 'Call-to-action banner', 'icon' => 'fa-solid fa-bullhorn', 'template' => 'cta', 'fields' => array_merge(
        [['k' => 'eyebrow', 'label' => 'Small label', 'maxlen' => 80],
         ['k' => 'title', 'label' => 'Heading', 'required' => true, 'help' => RICH_HELP, 'full' => true, 'maxlen' => 160],
         ['k' => 'content', 'label' => 'Text', 't' => 'textarea', 'rows' => 2, 'maxlen' => 300]],
        f_button('Main button'), f_button2(),
        [['k' => 'image', 'label' => 'Background image', 't' => 'image', 'folder' => 'general']]
    )];
}

function s_process(): array
{
    return ['label' => 'Work process', 'icon' => 'fa-solid fa-route', 'template' => 'process', 'fields' => array_merge(f_heading(), [f_steps()])];
}

function page_schemas(): array
{
    static $s = null;
    if ($s) return $s;
    return $s = [
        'home' => ['label' => 'Home', 'file' => 'index.php', 'icon' => 'fa-solid fa-house', 'sections' => [
            'hero' => ['label' => 'Hero banner', 'icon' => 'fa-solid fa-panorama', 'template' => 'home-hero', 'fields' => array_merge([
                ['k' => 'eyebrow', 'label' => 'Small label above the heading', 'maxlen' => 80],
                ['k' => 'side_text', 'label' => 'Vertical side text', 'maxlen' => 60],
                ['k' => 'title', 'label' => 'Hero heading', 'required' => true, 'help' => RICH_HELP, 'full' => true, 'maxlen' => 140],
                ['k' => 'content', 'label' => 'Subtitle', 't' => 'textarea', 'rows' => 2, 'maxlen' => 300],
            ], f_button('Main button'), f_button2(), [
                ['k' => 'image', 'label' => 'Background image', 't' => 'image', 'folder' => 'home'],
            ])],
            'marquee' => ['label' => 'Moving text strip', 'icon' => 'fa-solid fa-arrows-left-right', 'template' => 'marquee', 'fields' => [
                ['k' => 'items', 'label' => 'Phrases (one per line)', 't' => 'tags', 'required' => true],
            ]],
            'about' => ['label' => 'About Dream Spark', 'icon' => 'fa-solid fa-circle-info', 'template' => 'about-split', 'fields' => array_merge(f_heading(false), [
                ['k' => 'content', 'label' => 'Body text', 't' => 'textarea', 'rows' => 6, 'help' => 'Leave a blank line between paragraphs.', 'maxlen' => 3000],
                ['k' => 'points', 'label' => 'Highlight points (one per line)', 't' => 'tags'],
                ['k' => 'image', 'label' => 'Image', 't' => 'image', 'folder' => 'home'],
                ['k' => 'badge_value', 'label' => 'Badge number', 'placeholder' => 'e.g. 5+', 'maxlen' => 12],
                ['k' => 'badge_label', 'label' => 'Badge label', 'maxlen' => 60],
            ], f_button())],
            'services' => ['label' => 'Services overview', 'icon' => 'fa-solid fa-briefcase', 'template' => 'services', 'note' => 'Service cards are managed under Services.', 'fields' => f_heading()],
            'industries' => ['label' => 'Industries we serve', 'icon' => 'fa-solid fa-industry', 'template' => 'industries', 'fields' => array_merge(f_heading(), [f_icon_items('industry')])],
            'brands' => ['label' => 'Portfolio showcase (brands)', 'icon' => 'fa-solid fa-gem', 'template' => 'brands-preview', 'note' => 'Brands are managed under Brands. Featured brands are shown first.', 'fields' => array_merge(f_heading(), [
                ['k' => 'limit', 'label' => 'Number of brands to show', 't' => 'number', 'min' => 1, 'max' => 12, 'default' => 3],
            ], f_button())],
            'why' => ['label' => 'Why choose us', 'icon' => 'fa-solid fa-award', 'template' => 'why', 'fields' => array_merge(f_heading(), f_button(), [f_icon_items('reason')])],
            'process' => s_process(),
            'stats' => ['label' => 'Statistics counters', 'icon' => 'fa-solid fa-chart-simple', 'template' => 'stats', 'fields' => [
                ['k' => 'items', 'label' => 'Counters', 't' => 'repeater', 'singular' => 'counter', 'item_label' => 'label', 'max' => 6, 'fields' => [
                    ['k' => 'value', 'label' => 'Number', 'required' => true, 'placeholder' => '120', 'maxlen' => 12],
                    ['k' => 'suffix', 'label' => 'Suffix', 'placeholder' => '+ or % or M+', 'maxlen' => 6],
                    ['k' => 'label', 'label' => 'Label', 'required' => true, 'full' => true, 'maxlen' => 60],
                ]],
            ]],
            'testimonials' => ['label' => 'Testimonials', 'icon' => 'fa-solid fa-quote-left', 'template' => 'testimonials', 'note' => 'Testimonials are managed under Testimonials.', 'fields' => f_heading()],
            'cta' => s_cta(),
        ]],
        'about' => ['label' => 'About', 'file' => 'about.php', 'icon' => 'fa-solid fa-users', 'sections' => [
            'hero' => s_page_hero('about'),
            'intro' => ['label' => 'Company introduction', 'icon' => 'fa-solid fa-feather', 'template' => 'intro', 'fields' => array_merge(f_heading(false), [
                ['k' => 'content', 'label' => 'Body text', 't' => 'textarea', 'rows' => 6, 'help' => 'Leave a blank line between paragraphs.', 'maxlen' => 3000],
                ['k' => 'signature', 'label' => 'Signature name', 'maxlen' => 60],
                ['k' => 'signature_role', 'label' => 'Signature role', 'maxlen' => 60],
                ['k' => 'image', 'label' => 'Image', 't' => 'image', 'folder' => 'about'],
            ])],
            'story' => ['label' => 'Our story', 'icon' => 'fa-solid fa-book-open', 'template' => 'story', 'fields' => array_merge(f_heading(false), [
                ['k' => 'content', 'label' => 'Story text', 't' => 'textarea', 'rows' => 5, 'maxlen' => 3000],
                ['k' => 'image', 'label' => 'Image', 't' => 'image', 'folder' => 'about'],
                ['k' => 'milestones', 'label' => 'Timeline', 't' => 'repeater', 'singular' => 'milestone', 'item_label' => 'title', 'max' => 10, 'fields' => [
                    ['k' => 'year', 'label' => 'Year / label', 'required' => true, 'maxlen' => 20],
                    ['k' => 'title', 'label' => 'Title', 'required' => true, 'maxlen' => 80],
                    ['k' => 'text', 'label' => 'Text', 't' => 'textarea', 'rows' => 2, 'maxlen' => 240],
                ]],
            ])],
            'mission_vision' => ['label' => 'Mission & vision', 'icon' => 'fa-solid fa-bullseye', 'template' => 'mission-vision', 'fields' => array_merge(f_heading(), [
                ['k' => 'mission_title', 'label' => 'Mission title', 'maxlen' => 60],
                ['k' => 'vision_title', 'label' => 'Vision title', 'maxlen' => 60],
                ['k' => 'mission', 'label' => 'Mission', 't' => 'textarea', 'rows' => 3, 'required' => true, 'maxlen' => 600],
                ['k' => 'vision', 'label' => 'Vision', 't' => 'textarea', 'rows' => 3, 'required' => true, 'maxlen' => 600],
                ['k' => 'image', 'label' => 'Centre image', 't' => 'image', 'folder' => 'about'],
            ])],
            'values' => ['label' => 'Core values', 'icon' => 'fa-solid fa-heart', 'template' => 'values', 'fields' => array_merge(f_heading(), [f_icon_items('value')])],
            'expertise' => ['label' => 'Our expertise', 'icon' => 'fa-solid fa-chart-bar', 'template' => 'expertise', 'fields' => array_merge(f_heading(false), [
                ['k' => 'content', 'label' => 'Text', 't' => 'textarea', 'rows' => 3, 'maxlen' => 1000],
                ['k' => 'items', 'label' => 'Skill bars', 't' => 'repeater', 'singular' => 'skill', 'item_label' => 'title', 'max' => 8, 'fields' => [
                    ['k' => 'title', 'label' => 'Skill', 'required' => true, 'maxlen' => 60],
                    ['k' => 'percent', 'label' => 'Percent (0–100)', 't' => 'number', 'min' => 0, 'max' => 100, 'default' => 80],
                ]],
            ])],
            'why' => ['label' => 'Why Dream Spark', 'icon' => 'fa-solid fa-award', 'template' => 'why-dark', 'fields' => array_merge(f_heading(), f_button(), [f_icon_items('reason')])],
            'process' => s_process(),
            'team' => ['label' => 'Team', 'icon' => 'fa-solid fa-user-group', 'template' => 'team', 'note' => 'Team members are managed under Team.', 'fields' => f_heading()],
            'cta' => s_cta(),
        ]],
        'brands' => ['label' => 'Our Brands', 'file' => 'brands.php', 'icon' => 'fa-solid fa-gem', 'sections' => [
            'hero' => s_page_hero('brands'),
            'portfolio' => ['label' => 'Brand grid heading', 'icon' => 'fa-solid fa-table-cells-large', 'template' => 'brands-grid', 'note' => 'Brand cards are managed under Brands.', 'fields' => f_heading()],
            'cta' => s_cta(),
        ]],
        'contact' => ['label' => 'Contact', 'file' => 'contact.php', 'icon' => 'fa-solid fa-envelope', 'sections' => [
            'hero' => s_page_hero('general'),
            'form' => ['label' => 'Contact details & form', 'icon' => 'fa-solid fa-envelope-open-text', 'template' => 'contact-form', 'note' => 'Email, phone, WhatsApp and address come from Settings.', 'fields' => array_merge(f_heading(), [
                ['k' => 'form_title', 'label' => 'Form title', 'required' => true, 'help' => RICH_HELP, 'maxlen' => 120],
                ['k' => 'content', 'label' => 'Form intro text', 't' => 'textarea', 'rows' => 2, 'maxlen' => 300],
                ['k' => 'industries', 'label' => '“Industry” options (one per line)', 't' => 'tags'],
                ['k' => 'services', 'label' => '“Interested service” options (one per line)', 't' => 'tags', 'help' => 'Leave empty to use your published services automatically.'],
                ['k' => 'success_message', 'label' => 'Success message', 't' => 'textarea', 'rows' => 2, 'required' => true, 'maxlen' => 300],
            ])],
            'map' => ['label' => 'Google map', 'icon' => 'fa-solid fa-map-location-dot', 'template' => 'map', 'fields' => [
                ['k' => 'embed_url', 'label' => 'Google Maps embed URL', 't' => 'url', 'full' => true, 'help' => 'Google Maps → Share → Embed a map → copy only the link inside src="…" (starts with https://www.google.com/maps/embed).'],
            ]],
            'cta' => s_cta(),
        ]],
    ];
}

/* ---------------------------------------------------------------- collections */

function f_status(): array { return ['k' => 'status', 'label' => 'Published (visible on the website)', 't' => 'status', 'default' => 'published']; }

function collection_schemas(): array
{
    static $c = null;
    if ($c) return $c;
    return $c = [
        'brands' => [
            'table' => 'brands', 'label' => 'Brands', 'singular' => 'brand', 'icon' => 'fa-solid fa-gem', 'folder' => 'brands', 'admin' => 'brands.php', 'public' => 'brands.php',
            'title' => 'name', 'subtitle' => 'category', 'thumb' => 'logo', 'slug_from' => 'name', 'search' => ['name', 'category', 'description'],
            'fields' => [
                ['k' => 'name', 'label' => 'Brand name', 'required' => true, 'maxlen' => 150],
                ['k' => 'category', 'label' => 'Category / industry', 'required' => true, 'maxlen' => 100, 'list' => 'industry-list', 'help' => 'Used for the filter buttons on the Our Brands page.'],
                ['k' => 'description', 'label' => 'Description', 't' => 'textarea', 'rows' => 4, 'required' => true, 'maxlen' => 2000],
                ['k' => 'logo', 'label' => 'Logo (square)', 't' => 'image', 'folder' => 'brands'],
                ['k' => 'cover_image', 'label' => 'Cover image (portrait 4:5 works best)', 't' => 'image', 'folder' => 'brands'],
                ['k' => 'services', 'label' => 'Services delivered (one per line)', 't' => 'tags'],
                ['k' => 'website_url', 'label' => 'Website URL', 't' => 'url'],
                ['k' => 'social_links', 'label' => 'Social media', 't' => 'group', 'fields' => [
                    ['k' => 'instagram', 'label' => 'Instagram', 't' => 'url'], ['k' => 'facebook', 'label' => 'Facebook', 't' => 'url'],
                    ['k' => 'linkedin', 'label' => 'LinkedIn', 't' => 'url'], ['k' => 'youtube', 'label' => 'YouTube', 't' => 'url'], ['k' => 'x', 'label' => 'X (Twitter)', 't' => 'url'],
                ]],
                ['k' => 'is_featured', 'label' => 'Featured (shown first on the Home page)', 't' => 'toggle', 'default' => false],
                f_status(),
            ],
        ],
        'services' => [
            'table' => 'services', 'label' => 'Services', 'singular' => 'service', 'icon' => 'fa-solid fa-briefcase', 'folder' => 'services', 'admin' => 'services.php', 'public' => 'index.php',
            'title' => 'title', 'subtitle' => 'description', 'thumb' => 'image', 'icon_field' => 'icon', 'slug_from' => 'title', 'search' => ['title', 'description'],
            'fields' => [
                ['k' => 'title', 'label' => 'Service name', 'required' => true, 'maxlen' => 150],
                ['k' => 'icon', 'label' => 'Icon', 't' => 'icon'],
                ['k' => 'description', 'label' => 'Description', 't' => 'textarea', 'rows' => 3, 'required' => true, 'maxlen' => 600],
                ['k' => 'image', 'label' => 'Image (optional — replaces the icon on the card)', 't' => 'image', 'folder' => 'services'],
                f_status(),
            ],
        ],
        'testimonials' => [
            'table' => 'testimonials', 'label' => 'Testimonials', 'singular' => 'testimonial', 'icon' => 'fa-solid fa-quote-left', 'folder' => 'testimonials', 'admin' => 'testimonials.php', 'public' => 'index.php',
            'title' => 'client_name', 'subtitle' => 'testimonial', 'thumb' => 'client_image', 'search' => ['client_name', 'company', 'testimonial'],
            'fields' => [
                ['k' => 'client_name', 'label' => 'Client name', 'required' => true, 'maxlen' => 120],
                ['k' => 'designation', 'label' => 'Designation', 'maxlen' => 120],
                ['k' => 'company', 'label' => 'Company', 'maxlen' => 120],
                ['k' => 'rating', 'label' => 'Star rating (0–5)', 't' => 'number', 'min' => 0, 'max' => 5, 'default' => 5],
                ['k' => 'testimonial', 'label' => 'Testimonial', 't' => 'textarea', 'rows' => 4, 'required' => true, 'maxlen' => 800],
                ['k' => 'client_image', 'label' => 'Client photo (optional)', 't' => 'image', 'folder' => 'testimonials'],
                f_status(),
            ],
        ],
        'team' => [
            'table' => 'team_members', 'label' => 'Team', 'singular' => 'team member', 'icon' => 'fa-solid fa-user-group', 'folder' => 'team', 'admin' => 'team.php', 'public' => 'about.php',
            'title' => 'name', 'subtitle' => 'designation', 'thumb' => 'profile_image', 'search' => ['name', 'designation', 'bio'],
            'fields' => [
                ['k' => 'name', 'label' => 'Name', 'required' => true, 'maxlen' => 120],
                ['k' => 'designation', 'label' => 'Designation', 'required' => true, 'maxlen' => 120],
                ['k' => 'bio', 'label' => 'Short bio', 't' => 'textarea', 'rows' => 2, 'maxlen' => 300],
                ['k' => 'profile_image', 'label' => 'Profile photo (portrait works best)', 't' => 'image', 'folder' => 'team'],
                ['k' => 'social_links', 'label' => 'Social links', 't' => 'group', 'fields' => [
                    ['k' => 'linkedin', 'label' => 'LinkedIn', 't' => 'url'], ['k' => 'instagram', 'label' => 'Instagram', 't' => 'url'], ['k' => 'x', 'label' => 'X (Twitter)', 't' => 'url'],
                ]],
                f_status(),
            ],
        ],
    ];
}

/* ----------------------------------------------------------------- settings */

function settings_groups(): array
{
    return [
        'branding' => ['label' => 'Branding', 'icon' => 'fa-solid fa-palette', 'fields' => [
            ['k' => 'site_name', 'label' => 'Website name', 'required' => true, 'maxlen' => 80, 'help' => 'The last word is shown in gold when no logo image is set.'],
            ['k' => 'logo', 'label' => 'Navbar logo (horizontal)', 't' => 'image', 'folder' => 'branding', 'help' => 'Transparent PNG for a dark background, wide format (about 4:1). Leave empty to use the built-in wordmark.'],
            ['k' => 'logo_stacked', 'label' => 'Stacked logo (footer & loading screen)', 't' => 'image', 'folder' => 'branding', 'help' => 'Transparent PNG with the mark above the name. Leave empty to reuse the navbar logo.'],
            ['k' => 'favicon', 'label' => 'Favicon (browser tab icon)', 't' => 'image', 'folder' => 'branding'],
            ['k' => 'nav_cta_text', 'label' => 'Header button text', 'maxlen' => 40],
            ['k' => 'nav_cta_link', 'label' => 'Header button link', 't' => 'link'],
        ]],
        'contact' => ['label' => 'Contact details', 'icon' => 'fa-solid fa-address-book', 'fields' => [
            ['k' => 'email', 'label' => 'Email', 't' => 'email', 'required' => true],
            ['k' => 'phone', 'label' => 'Phone', 'maxlen' => 30, 'pattern' => 'phone'],
            ['k' => 'whatsapp', 'label' => 'WhatsApp number (with country code)', 'maxlen' => 30, 'pattern' => 'phone', 'help' => 'Powers the floating WhatsApp button.'],
            ['k' => 'hours', 'label' => 'Opening hours', 'maxlen' => 80],
            ['k' => 'address', 'label' => 'Address', 't' => 'textarea', 'rows' => 2, 'maxlen' => 300],
        ]],
        'social' => ['label' => 'Social links', 'icon' => 'fa-solid fa-share-nodes', 'fields' => [
            ['k' => 'social_instagram', 'label' => 'Instagram', 't' => 'url'], ['k' => 'social_facebook', 'label' => 'Facebook', 't' => 'url'],
            ['k' => 'social_linkedin', 'label' => 'LinkedIn', 't' => 'url'], ['k' => 'social_youtube', 'label' => 'YouTube', 't' => 'url'],
            ['k' => 'social_x', 'label' => 'X (Twitter)', 't' => 'url'], ['k' => 'social_behance', 'label' => 'Behance', 't' => 'url'],
        ]],
        'footer' => ['label' => 'Footer', 'icon' => 'fa-solid fa-shoe-prints', 'fields' => [
            ['k' => 'footer_headline', 'label' => 'Footer headline', 'help' => RICH_HELP, 'full' => true, 'maxlen' => 120],
            ['k' => 'footer_cta_text', 'label' => 'Footer button text', 'maxlen' => 40],
            ['k' => 'footer_cta_link', 'label' => 'Footer button link', 't' => 'link'],
            ['k' => 'footer_about', 'label' => 'About text', 't' => 'textarea', 'rows' => 3, 'maxlen' => 400],
            ['k' => 'footer_copyright', 'label' => 'Copyright line', 'help' => '{year} becomes the current year.', 'maxlen' => 120],
            ['k' => 'footer_tagline', 'label' => 'Small tagline', 'maxlen' => 120],
        ]],
        'seo' => ['label' => 'SEO defaults', 'icon' => 'fa-solid fa-magnifying-glass', 'fields' => [
            ['k' => 'seo_title', 'label' => 'SEO title (site name in Google)', 'required' => true, 'maxlen' => 70],
            ['k' => 'seo_description', 'label' => 'Default SEO description', 't' => 'textarea', 'rows' => 2, 'maxlen' => 170],
            ['k' => 'seo_keywords', 'label' => 'Keywords', 't' => 'textarea', 'rows' => 2, 'maxlen' => 300],
            ['k' => 'og_image', 'label' => 'Social share image (1200×630)', 't' => 'image', 'folder' => 'branding'],
        ]],
    ];
}

function page_seo_fields(): array
{
    return [
        ['k' => 'meta_title', 'label' => 'Page title (browser tab & Google)', 'maxlen' => 70, 'help' => 'Shown as “Title | Site name”. Keep under 60 characters.'],
        ['k' => 'meta_description', 'label' => 'Meta description', 't' => 'textarea', 'rows' => 2, 'maxlen' => 170, 'help' => '140–160 characters is ideal.'],
    ];
}

const ENQUIRY_STATUSES = ['new' => 'New', 'contacted' => 'Contacted', 'in_progress' => 'In Progress', 'converted' => 'Converted', 'closed' => 'Closed'];
const MEDIA_FOLDERS = ['general', 'home', 'about', 'brands', 'services', 'team', 'testimonials', 'branding'];
