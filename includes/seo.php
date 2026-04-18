<?php
/**
 * SEO Helper Functions for Mctech-hub Systems
 * Generates meta tags, Open Graph, Twitter Cards, JSON-LD structured data
 * Optimized for: developer, designers, website, app, best, and 100+ search terms
 */

// ── Site-wide SEO defaults ──
if (!defined('SITE_NAME'))    define('SITE_NAME', 'Mctech-hub Systems');
if (!defined('SITE_TAGLINE')) define('SITE_TAGLINE', 'Best Website Developer, App Developer & Designer in Uganda | Mctech-hub Systems');
if (!defined('SITE_DOMAIN'))  define('SITE_DOMAIN', $_SERVER['HTTP_HOST'] ?? 'mctech-hub.com');
if (!defined('SITE_URL'))     define('SITE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . SITE_DOMAIN);
if (!defined('SITE_PHONE'))   define('SITE_PHONE', '+256758611414');
if (!defined('SITE_EMAIL'))   define('SITE_EMAIL', 'mctechhubsystems@gmail.com');
if (!defined('SITE_LOGO'))    define('SITE_LOGO', '/mctech-hub/assets/images/logo3.png');
if (!defined('SITE_LOCALE'))  define('SITE_LOCALE', 'en_UG');
if (!defined('SITE_TWITTER')) define('SITE_TWITTER', '@mctechhub');
if (!defined('SITE_FOUNDED')) define('SITE_FOUNDED', '2023');

// ── MASSIVE KEYWORD DATABASE ──
// These are strategically organized so every page gets relevant keyword coverage
define('SEO_KEYWORDS_GLOBAL', implode(', ', [
    // ═══ "DEVELOPER" variations ═══
    'best developer in Uganda', 'developer Uganda', 'web developer Uganda',
    'app developer Uganda', 'software developer Uganda', 'best web developer Kampala',
    'best app developer Kampala', 'top developer Uganda', 'hire developer Uganda',
    'freelance developer Uganda', 'developer near me', 'best developer Africa',
    'mobile developer Uganda', 'full stack developer Uganda', 'frontend developer Uganda',
    'backend developer Uganda', 'best developer East Africa',

    // ═══ "DESIGNER" variations ═══
    'best designer in Uganda', 'designer Uganda', 'web designer Uganda',
    'UI designer Uganda', 'UX designer Uganda', 'graphic designer Uganda',
    'best web designer Kampala', 'best graphic designer Kampala',
    'logo designer Uganda', 'brand designer Uganda', 'UI/UX designer Uganda',
    'creative designer Uganda', 'top designer Africa', 'hire designer Uganda',
    'best designer East Africa', 'designer near me',

    // ═══ "WEBSITE" variations ═══
    'website Uganda', 'best website Uganda', 'website development Uganda',
    'website design Uganda', 'website builder Uganda', 'professional website Uganda',
    'business website Uganda', 'cheap website Uganda', 'affordable website Uganda',
    'corporate website Uganda', 'e-commerce website Uganda', 'responsive website Uganda',
    'custom website Uganda', 'website creation Kampala', 'website maker Uganda',
    'create website Uganda', 'build website Uganda', 'website company Uganda',
    'best website company in Uganda', 'best website development company Uganda',
    'website design Kampala', 'website developer Kampala',

    // ═══ "APP" variations ═══
    'app Uganda', 'best app development Uganda', 'app development Uganda',
    'mobile app Uganda', 'app developer Uganda', 'build app Uganda',
    'create app Uganda', 'app design Uganda', 'iOS app Uganda',
    'Android app Uganda', 'app company Uganda', 'best app company Uganda',
    'mobile app development Kampala', 'cross platform app Uganda',
    'app development company Uganda', 'best mobile app developer Uganda',
    'custom app Uganda', 'app maker Uganda',

    // ═══ "BEST" variations ═══
    'best tech company Uganda', 'best IT company Uganda', 'best digital agency Uganda',
    'best web agency Uganda', 'best software company Uganda',
    'best technology company Kampala', 'best IT services Uganda',
    'best digital solutions Uganda', 'top tech company Uganda',
    'best web development company in Kampala', 'best IT firm Uganda',
    'number one tech company Uganda',

    // ═══ GENERAL / HIGH-VOLUME KEYWORDS ═══
    'Mctech-hub Systems', 'MCTech Hub', 'mctech hub',
    'web development Uganda', 'web development Kampala', 'web design Kampala',
    'software development Uganda', 'IT services Uganda', 'IT solutions Uganda',
    'digital agency Uganda', 'digital transformation Uganda',
    'AI integration Uganda', 'artificial intelligence Uganda',
    'e-commerce development Uganda', 'online store Uganda',
    'SEO services Uganda', 'SEO Kampala', 'digital marketing Uganda',
    'branding Uganda', 'corporate branding Kampala',
    'logo design Uganda', 'brand identity Uganda',
    'hosting Uganda', 'domain registration Uganda',
    'tech startup Uganda', 'technology services Uganda',
    'web solutions Africa', 'app solutions Africa',
    'affordable web development', 'cheap web development Uganda',
    'professional web development Uganda', 'reliable developer Uganda',
    'trusted IT company Uganda', 'software house Uganda',
    'website maintenance Uganda', 'web hosting Uganda',
    'cloud services Uganda', 'database development Uganda',
    'API development Uganda', 'SaaS development Uganda',

    // ═══ LONG-TAIL / QUESTION KEYWORDS ═══
    'who is the best web developer in Uganda',
    'best website design company in Kampala',
    'how much does a website cost in Uganda',
    'best mobile app developer in Kampala',
    'affordable web design services in Uganda',
    'top rated tech company in Uganda',
    'best IT solutions provider in Uganda',
    'professional website developer near me',
    'where to build a website in Uganda',
    'best place to make an app in Uganda',
    'top software development company in East Africa'
]));

/**
 * Per-page SEO configurations
 * Each page gets its own optimized title, description, and keywords
 */
function getPageSeo($page = 'home') {
    $pages = [
        'home' => [
            'page_title'  => 'Best Website Developer, App Developer & Designer in Uganda',
            'description' => 'Mctech-hub Systems is Uganda\'s best website developer, app developer & designer. We build stunning websites, mobile apps & AI solutions for businesses. Starting from UGX 300,000. Trusted by 35+ clients. Call +256758611414.',
            'keywords'    => SEO_KEYWORDS_GLOBAL,
            'og_type'     => 'website',
            'schema_type' => 'WebPage',
        ],
        'services' => [
            'page_title'  => 'Best Web Development & App Development Services in Uganda',
            'description' => 'Affordable, professional website development, app development, UI/UX design, SEO, branding & AI integration services in Uganda. Packages starting from UGX 200,000. Best developer & designer in Kampala.',
            'keywords'    => 'best web development services Uganda, app development services Uganda, website design service Kampala, affordable website developer Uganda, best designer services Uganda, SEO services Uganda, branding services Kampala, e-commerce development Uganda, mobile app development services, best IT services Uganda, AI integration services, cloud hosting Uganda, ' . SEO_KEYWORDS_GLOBAL,
            'og_type'     => 'website',
            'schema_type' => 'CollectionPage',
        ],
        'about' => [
            'page_title'  => 'About Us — Best Tech Company & Developer Team in Uganda',
            'description' => 'Mctech-hub Systems is Uganda\'s leading tech company founded in 2023. Meet our expert team of developers, designers & AI specialists building Africa\'s digital future. 50+ projects delivered. 100% client satisfaction.',
            'keywords'    => 'about Mctech-hub Systems, best tech company Uganda, developer team Uganda, designer team Kampala, Uganda tech startup, digital agency Uganda about, software company Uganda, IT company Uganda team, web development team Africa, best developers in Uganda, ' . SEO_KEYWORDS_GLOBAL,
            'og_type'     => 'website',
            'schema_type' => 'AboutPage',
        ],
        'contact' => [
            'page_title'  => 'Contact the Best Website Developer & Designer in Uganda',
            'description' => 'Get in touch with Mctech-hub Systems — Uganda\'s best web developer & designer. Free consultation. Call +256758611414 or WhatsApp us. We respond within 24 hours. Based in Kampala, serving worldwide.',
            'keywords'    => 'contact Mctech-hub Systems, hire developer Uganda, hire designer Uganda, contact web developer Kampala, website developer phone number Uganda, WhatsApp developer Uganda, get website quote Uganda, free consultation developer Uganda, ' . SEO_KEYWORDS_GLOBAL,
            'og_type'     => 'website',
            'schema_type' => 'ContactPage',
        ],
        'portfolio' => [
            'page_title'  => 'Our Work — Best Website & App Projects Built in Uganda',
            'description' => 'Explore Mctech-hub Systems portfolio. See the best websites, web apps & AI solutions built by Uganda\'s top developer team. E-commerce, corporate sites, SaaS platforms, mobile apps & more.',
            'keywords'    => 'best website portfolio Uganda, app development projects Uganda, web developer portfolio Kampala, designer portfolio Uganda, website examples Uganda, app examples Uganda, e-commerce projects Uganda, best work developer Uganda, ' . SEO_KEYWORDS_GLOBAL,
            'og_type'     => 'website',
            'schema_type' => 'CollectionPage',
        ],
        'blog' => [
            'page_title'  => 'Tech Blog — Web Development, App & Design Tips from Uganda\'s Best',
            'description' => 'Expert insights on web development, app development, UI/UX design, AI, SEO & digital transformation from Uganda\'s best tech team. Learn, grow & stay ahead with Mctech-hub Systems blog.',
            'keywords'    => 'tech blog Uganda, web development blog, app development tips, designer blog Uganda, best developer blog Africa, IT insights Uganda, digital transformation articles, SEO tips Uganda, ' . SEO_KEYWORDS_GLOBAL,
            'og_type'     => 'blog',
            'schema_type' => 'Blog',
        ],
    ];

    return $pages[$page] ?? $pages['home'];
}

/**
 * Generate all <head> SEO meta tags
 * This is the MASTER function that outputs everything
 */
function renderSeoMeta($seo = []) {
    $title       = $seo['title']       ?? SITE_TAGLINE;
    $description = $seo['description'] ?? 'Mctech-hub Systems is the best website developer, app developer and designer in Uganda. We build professional websites, mobile apps & AI solutions for businesses in Kampala, East Africa & worldwide. Starting from UGX 300,000.';
    $keywords    = $seo['keywords']    ?? SEO_KEYWORDS_GLOBAL;
    $canonical   = $seo['canonical']   ?? currentUrl();
    $ogImage     = $seo['og_image']    ?? SITE_URL . SITE_LOGO;
    $ogType      = $seo['og_type']     ?? 'website';
    $pageTitle   = $seo['page_title']  ?? '';
    $robots      = $seo['robots']      ?? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
    $author      = $seo['author']      ?? SITE_NAME;

    // Build full title — keyword-rich
    $fullTitle = $pageTitle 
        ? htmlspecialchars($pageTitle) . ' | ' . SITE_NAME
        : SITE_NAME . ' — ' . SITE_TAGLINE;

    $desc = htmlspecialchars(substr($description, 0, 160));
    $kw   = htmlspecialchars($keywords);

    $html = <<<META
    <!-- ═══ PRIMARY SEO META ═══ -->
    <title>{$fullTitle}</title>
    <meta name="description" content="{$desc}">
    <meta name="keywords" content="{$kw}">
    <meta name="robots" content="{$robots}">
    <meta name="googlebot" content="{$robots}">
    <meta name="bingbot" content="{$robots}">
    <meta name="author" content="{$author}">
    <meta name="publisher" content="Mctech-hub Systems">
    <meta name="copyright" content="Mctech-hub Systems">
    <meta name="language" content="English">
    <meta name="revisit-after" content="3 days">
    <meta name="rating" content="general">
    <meta name="distribution" content="global">
    <meta name="coverage" content="worldwide">
    <meta name="target" content="all">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">

    <!-- ═══ GEO / LOCAL SEO ═══ -->
    <meta name="geo.region" content="UG-102">
    <meta name="geo.placename" content="Kampala, Uganda">
    <meta name="geo.position" content="0.3476;32.5825">
    <meta name="ICBM" content="0.3476, 32.5825">
    <meta name="DC.title" content="{$fullTitle}">
    <meta name="DC.creator" content="Mctech-hub Systems">
    <meta name="DC.subject" content="Web Development, App Development, Design, AI, Uganda">
    <meta name="DC.description" content="{$desc}">
    <meta name="DC.publisher" content="Mctech-hub Systems">
    <meta name="DC.language" content="en">
    <link rel="canonical" href="{$canonical}">

    <!-- ═══ OPEN GRAPH (Facebook, LinkedIn, WhatsApp) ═══ -->
    <meta property="og:type" content="{$ogType}">
    <meta property="og:title" content="{$fullTitle}">
    <meta property="og:description" content="{$desc}">
    <meta property="og:image" content="{$ogImage}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Mctech-hub Systems — Best Developer & Designer in Uganda">
    <meta property="og:url" content="{$canonical}">
    <meta property="og:site_name" content="Mctech-hub Systems">
    <meta property="og:locale" content="en_UG">
    <meta property="og:locale:alternate" content="en_US">

    <!-- ═══ TWITTER CARD ═══ -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@mctechhub">
    <meta name="twitter:creator" content="@mctechhub">
    <meta name="twitter:title" content="{$fullTitle}">
    <meta name="twitter:description" content="{$desc}">
    <meta name="twitter:image" content="{$ogImage}">
    <meta name="twitter:image:alt" content="Mctech-hub Systems — Best Developer & Designer in Uganda">

    <!-- ═══ APPLE / MOBILE ═══ -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Mctech-hub Systems">

META;

    echo $html;
}

/**
 * Get current full URL
 */
function currentUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'], '?');
}

/**
 * JSON-LD: Organization + LocalBusiness (site-wide)
 * Packed with keywords for search engine rich results
 */
function renderOrganizationSchema() {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => ['Organization', 'LocalBusiness', 'ProfessionalService', 'ITCompany'],
        '@id' => SITE_URL . '/#organization',
        'name' => SITE_NAME,
        'legalName' => 'Mctech-hub Systems',
        'alternateName' => ['MCTech Hub', 'Mctech Hub Systems', 'MCTECH-HUB', 'Mctech-hub'],
        'url' => SITE_URL,
        'logo' => [
            '@type' => 'ImageObject',
            'url' => SITE_URL . SITE_LOGO,
            'width' => 512,
            'height' => 512,
            'caption' => 'Mctech-hub Systems — Best Website Developer, App Developer & Designer in Uganda'
        ],
        'image' => SITE_URL . SITE_LOGO,
        'description' => 'Mctech-hub Systems is the best website developer, app developer, and designer in Uganda. We offer professional web development, mobile app development, UI/UX design, AI integration, SEO, branding and digital solutions for businesses in Kampala, East Africa and worldwide.',
        'slogan' => 'Best Developer & Designer in Uganda — Building Africa\'s Digital Future',
        'telephone' => SITE_PHONE,
        'email' => SITE_EMAIL,
        'foundingDate' => SITE_FOUNDED,
        'priceRange' => 'UGX 200,000 - UGX 5,000,000',
        'currenciesAccepted' => 'UGX, USD',
        'paymentAccepted' => 'Cash, Mobile Money, Bank Transfer, Visa, MasterCard',
        'knowsAbout' => [
            'Website Development', 'Web Design', 'App Development', 'Mobile Applications',
            'UI/UX Design', 'Graphic Design', 'AI Integration', 'Artificial Intelligence',
            'SEO', 'Search Engine Optimization', 'Digital Marketing', 'Branding',
            'E-commerce', 'Cloud Hosting', 'Database Development', 'API Development',
            'Software Development', 'Cross-platform Development', 'React', 'PHP',
            'JavaScript', 'Python', 'Flutter', 'WordPress', 'Laravel'
        ],
        'knowsLanguage' => ['English', 'Luganda', 'Swahili'],
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => 'Kampala',
            'addressRegion' => 'Central Region',
            'addressCountry' => 'UG',
            'postalCode' => '00256'
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => 0.3476,
            'longitude' => 32.5825
        ],
        'areaServed' => [
            ['@type' => 'City', 'name' => 'Kampala'],
            ['@type' => 'Country', 'name' => 'Uganda'],
            ['@type' => 'Country', 'name' => 'Kenya'],
            ['@type' => 'Country', 'name' => 'Tanzania'],
            ['@type' => 'Country', 'name' => 'Rwanda'],
            ['@type' => 'Continent', 'name' => 'Africa'],
            ['@type' => 'Place', 'name' => 'Worldwide']
        ],
        'sameAs' => [
            'https://wa.me/256758611414',
            'https://www.instagram.com/mctechhub',
            'https://www.linkedin.com/company/mctechhub',
            'https://twitter.com/mctechhub',
            'https://www.facebook.com/mctechhub'
        ],
        'contactPoint' => [
            [
                '@type' => 'ContactPoint',
                'telephone' => SITE_PHONE,
                'contactType' => 'customer service',
                'availableLanguage' => ['English', 'Luganda'],
                'areaServed' => ['UG', 'KE', 'TZ', 'RW']
            ],
            [
                '@type' => 'ContactPoint',
                'telephone' => SITE_PHONE,
                'contactType' => 'sales',
                'availableLanguage' => ['English'],
                'areaServed' => 'Worldwide'
            ]
        ],
        'openingHoursSpecification' => [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            'opens' => '08:00',
            'closes' => '18:00'
        ],
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name' => 'Digital Services — Best in Uganda',
            'itemListElement' => [
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Website Development', 'description' => 'Best website developer in Uganda. Professional responsive websites for businesses starting from UGX 300,000']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Mobile App Development', 'description' => 'Best app developer in Uganda. Cross-platform mobile apps for iOS and Android']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'UI/UX Design', 'description' => 'Best designer in Uganda. Stunning user interfaces and seamless user experiences']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'AI Integration', 'description' => 'AI chatbots, voice agents, and automation solutions for African businesses']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'SEO & Digital Marketing', 'description' => 'Rank #1 on Google. Search engine optimization and digital growth strategies']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Corporate Branding', 'description' => 'Logo design, brand identity, business cards and complete branding packages']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'E-Commerce Development', 'description' => 'Online stores with Mobile Money & Visa integration for Uganda and Africa']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Cloud Hosting & Domains', 'description' => 'Secure SSD hosting, domain registration (.co.ug, .com) with SSL certificates']]
            ]
        ],
        'aggregateRating' => [
            '@type' => 'AggregateRating',
            'ratingValue' => '5.0',
            'reviewCount' => '35',
            'bestRating' => '5',
            'worstRating' => '1'
        ],
        'review' => [
            [
                '@type' => 'Review',
                'reviewRating' => ['@type' => 'Rating', 'ratingValue' => '5', 'bestRating' => '5'],
                'author' => ['@type' => 'Person', 'name' => 'Sarah Johnson'],
                'reviewBody' => 'Best developer team in Uganda. Mctech-hub transformed our online presence with a modern, fast website.'
            ],
            [
                '@type' => 'Review',
                'reviewRating' => ['@type' => 'Rating', 'ratingValue' => '5', 'bestRating' => '5'],
                'author' => ['@type' => 'Person', 'name' => 'David Okello'],
                'reviewBody' => 'The best app developer in Kampala. Their clinic system revolutionized our operations. Efficiency improved by 40%.'
            ],
            [
                '@type' => 'Review',
                'reviewRating' => ['@type' => 'Rating', 'ratingValue' => '5', 'bestRating' => '5'],
                'author' => ['@type' => 'Person', 'name' => 'Grace Nakato'],
                'reviewBody' => 'Outstanding design work. Best designer in Uganda. They understood our requirements perfectly.'
            ]
        ]
    ];

    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

/**
 * JSON-LD: WebSite with SearchAction (for Google Sitelinks Search Box)
 */
function renderWebsiteSchema() {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => SITE_URL . '/#website',
        'name' => SITE_NAME,
        'alternateName' => 'MCTech Hub — Best Developer & Designer in Uganda',
        'url' => SITE_URL,
        'description' => 'Mctech-hub Systems — Best website developer, app developer & designer in Uganda. Professional web development, app development & AI solutions.',
        'publisher' => [
            '@id' => SITE_URL . '/#organization'
        ],
        'inLanguage' => 'en-UG',
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => SITE_URL . '/?s={search_term_string}'
            ],
            'query-input' => 'required name=search_term_string'
        ]
    ];

    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

/**
 * JSON-LD: WebPage
 */
function renderWebPageSchema($seo = []) {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => $seo['schema_type'] ?? 'WebPage',
        '@id' => currentUrl() . '#webpage',
        'url' => currentUrl(),
        'name' => ($seo['page_title'] ?? 'Home') . ' | ' . SITE_NAME,
        'description' => $seo['description'] ?? '',
        'isPartOf' => ['@id' => SITE_URL . '/#website'],
        'about' => ['@id' => SITE_URL . '/#organization'],
        'inLanguage' => 'en-UG',
        'dateModified' => date('c'),
        'breadcrumb' => ['@id' => currentUrl() . '#breadcrumb']
    ];

    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

/**
 * JSON-LD: BreadcrumbList
 */
function renderBreadcrumbSchema($items = []) {
    if (empty($items)) return;

    $listItems = [];
    foreach ($items as $i => $item) {
        $listItems[] = [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $item['name'],
            'item' => $item['url'] ?? ''
        ];
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        '@id' => currentUrl() . '#breadcrumb',
        'itemListElement' => $listItems
    ];

    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

/**
 * JSON-LD: Service listing
 */
function renderServiceSchema($name, $desc, $price, $currency = 'UGX') {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'serviceType' => $name,
        'provider' => ['@id' => SITE_URL . '/#organization'],
        'name' => $name,
        'description' => $desc,
        'areaServed' => [
            ['@type' => 'Country', 'name' => 'Uganda'],
            ['@type' => 'Continent', 'name' => 'Africa'],
            ['@type' => 'Place', 'name' => 'Worldwide']
        ],
        'offers' => [
            '@type' => 'Offer',
            'price' => $price,
            'priceCurrency' => $currency,
            'availability' => 'https://schema.org/InStock'
        ]
    ];

    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

/**
 * JSON-LD: BlogPosting
 */
function renderBlogPostSchema($post) {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $post['title'],
        'description' => $post['excerpt'],
        'image' => SITE_URL . '/mctech-hub/assets/images/blog/' . $post['featured_image'],
        'author' => [
            '@type' => 'Organization',
            'name' => $post['author'] ?? SITE_NAME
        ],
        'publisher' => [
            '@id' => SITE_URL . '/#organization'
        ],
        'datePublished' => $post['published_at'],
        'dateModified' => $post['published_at'],
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => currentUrl()
        ],
        'inLanguage' => 'en-UG'
    ];

    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

/**
 * JSON-LD: FAQ Page (great for Google rich snippets)
 */
function renderFaqSchema($faqs = []) {
    if (empty($faqs)) return;

    $items = [];
    foreach ($faqs as $faq) {
        $items[] = [
            '@type' => 'Question',
            'name' => $faq['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq['a']
            ]
        ];
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $items
    ];

    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

/**
 * JSON-LD: ProfessionalService (extra local SEO boost)
 */
function renderProfessionalServiceSchema() {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'ProfessionalService',
        'name' => 'Mctech-hub Systems — Best Developer & Designer in Uganda',
        'image' => SITE_URL . SITE_LOGO,
        'url' => SITE_URL,
        'telephone' => SITE_PHONE,
        'priceRange' => 'UGX 200,000 - UGX 5,000,000',
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => 'Kampala',
            'addressCountry' => 'UG'
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => 0.3476,
            'longitude' => 32.5825
        ],
        'aggregateRating' => [
            '@type' => 'AggregateRating',
            'ratingValue' => '5.0',
            'reviewCount' => '35'
        ]
    ];

    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

/**
 * Render ALL schema for a page (call this in footer)
 */
function renderAllSchemas($seo = []) {
    renderOrganizationSchema();
    renderWebsiteSchema();
    renderWebPageSchema($seo);
    renderProfessionalServiceSchema();
}
?>
