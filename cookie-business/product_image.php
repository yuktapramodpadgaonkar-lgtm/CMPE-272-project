<?php
require_once __DIR__ . '/includes/products_catalog.php';

function cookie_svg_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

function cookie_product_theme(string $slug): array
{
    $themes = [
        'classic-chocolate-chip' => ['#f7dba7', '#e8a96a', '#7a4a2a', '#4f2f1c', 'chips'],
        'oatmeal-raisin-deluxe' => ['#f1dfb4', '#c88d52', '#87552d', '#5d3720', 'oatmeal'],
        'decorated-sugar-cookies' => ['#ffe8f1', '#f6a4c8', '#ffffff', '#e36fa5', 'decorated'],
        'snickerdoodle' => ['#f5d2a2', '#d39152', '#8a5228', '#6f3e1d', 'snickerdoodle'],
        'double-chocolate-fudge' => ['#55332b', '#2d1b18', '#8f5a49', '#f6ddc7', 'fudge'],
        'lemon-glazed-shortbread' => ['#fff1a8', '#ffd85a', '#fff9df', '#d7a400', 'lemon'],
        'peanut-butter-blossom' => ['#e9c085', '#c88742', '#6f3d22', '#4e2614', 'blossom'],
        'custom-catering-tray' => ['#f4dfc7', '#d6a87b', '#9a6035', '#6b3d20', 'tray'],
        'gift-box-subscription' => ['#efe5ff', '#b995ff', '#ffffff', '#7a4de0', 'gift'],
        'gluten-friendly-almond-cookie' => ['#f3dfbf', '#d5af7a', '#8b5c34', '#6d4424', 'almond'],
    ];

    return $themes[$slug] ?? ['#f7dba7', '#e8a96a', '#7a4a2a', '#4f2f1c', 'chips'];
}

function cookie_product_art(string $art, string $cookieFill, string $accent, string $highlight): string
{
    switch ($art) {
        case 'oatmeal':
            return <<<SVG
<g transform="translate(785 110)">
  <circle cx="175" cy="190" r="140" fill="{$cookieFill}" />
  <ellipse cx="135" cy="145" rx="20" ry="10" fill="{$accent}" opacity="0.88" />
  <ellipse cx="215" cy="128" rx="18" ry="9" fill="{$accent}" opacity="0.85" />
  <ellipse cx="250" cy="205" rx="20" ry="10" fill="{$accent}" opacity="0.88" />
  <ellipse cx="108" cy="232" rx="17" ry="9" fill="{$accent}" opacity="0.84" />
  <ellipse cx="195" cy="250" rx="18" ry="10" fill="{$accent}" opacity="0.86" />
  <ellipse cx="158" cy="102" rx="22" ry="7" fill="{$highlight}" />
  <ellipse cx="245" cy="162" rx="24" ry="7" fill="{$highlight}" />
  <ellipse cx="95" cy="180" rx="23" ry="7" fill="{$highlight}" />
  <ellipse cx="210" cy="215" rx="21" ry="7" fill="{$highlight}" />
</g>
SVG;

        case 'decorated':
            return <<<SVG
<g transform="translate(790 105)">
  <path d="M116 136c0-46 37-83 83-83 20 0 39 7 54 20 15-13 34-20 54-20 46 0 83 37 83 83 0 35-19 61-47 88l-90 87-90-87c-28-27-47-53-47-88z" fill="{$cookieFill}" />
  <path d="M139 136c0-33 27-60 60-60 17 0 33 7 44 18 11-11 27-18 44-18 33 0 60 27 60 60 0 24-12 44-33 64l-71 69-71-69c-21-20-33-40-33-64z" fill="{$highlight}" />
  <circle cx="184" cy="126" r="10" fill="{$accent}" />
  <circle cx="310" cy="152" r="10" fill="{$accent}" />
  <circle cx="228" cy="244" r="10" fill="{$accent}" />
</g>
SVG;

        case 'snickerdoodle':
            return <<<SVG
<g transform="translate(790 110)">
  <circle cx="170" cy="190" r="138" fill="{$cookieFill}" />
  <path d="M92 146c42-28 90-29 132 0 30 20 63 24 99 11" stroke="{$accent}" stroke-width="16" fill="none" stroke-linecap="round" opacity="0.8" />
  <path d="M80 203c44-28 96-28 140 2 26 17 59 18 91 9" stroke="{$accent}" stroke-width="16" fill="none" stroke-linecap="round" opacity="0.72" />
  <path d="M102 258c36-20 77-21 114 0 22 12 46 14 71 7" stroke="{$accent}" stroke-width="14" fill="none" stroke-linecap="round" opacity="0.75" />
</g>
SVG;

        case 'fudge':
            return <<<SVG
<g transform="translate(790 112)">
  <circle cx="170" cy="188" r="138" fill="{$cookieFill}" />
  <rect x="122" y="120" width="34" height="24" rx="6" fill="{$accent}" />
  <rect x="210" y="145" width="38" height="26" rx="6" fill="{$accent}" />
  <rect x="250" y="220" width="32" height="24" rx="6" fill="{$accent}" />
  <rect x="152" y="245" width="37" height="25" rx="6" fill="{$accent}" />
  <rect x="92" y="194" width="34" height="24" rx="6" fill="{$accent}" />
  <rect x="190" y="96" width="32" height="22" rx="6" fill="{$highlight}" opacity="0.8" />
</g>
SVG;

        case 'lemon':
            return <<<SVG
<g transform="translate(782 122)">
  <rect x="55" y="92" width="230" height="168" rx="28" fill="{$cookieFill}" />
  <rect x="55" y="92" width="230" height="168" rx="28" fill="none" stroke="{$accent}" stroke-width="8" opacity="0.35" />
  <circle cx="305" cy="124" r="62" fill="{$highlight}" />
  <circle cx="305" cy="124" r="46" fill="none" stroke="{$accent}" stroke-width="10" />
  <path d="M305 78v92M259 124h92M274 93l61 61M274 155l61-61" stroke="{$accent}" stroke-width="8" stroke-linecap="round" opacity="0.7" />
  <path d="M92 126c40-18 82-18 124 0M92 172c40-18 82-18 124 0M92 218c40-18 82-18 124 0" stroke="{$highlight}" stroke-width="11" stroke-linecap="round" opacity="0.65" />
</g>
SVG;

        case 'blossom':
            return <<<SVG
<g transform="translate(792 112)">
  <circle cx="170" cy="190" r="138" fill="{$cookieFill}" />
  <circle cx="170" cy="190" r="52" fill="{$accent}" />
  <circle cx="170" cy="190" r="36" fill="{$highlight}" opacity="0.18" />
  <circle cx="116" cy="125" r="13" fill="{$highlight}" opacity="0.6" />
  <circle cx="242" cy="136" r="11" fill="{$highlight}" opacity="0.5" />
  <circle cx="226" cy="248" r="12" fill="{$highlight}" opacity="0.45" />
</g>
SVG;

        case 'tray':
            return <<<SVG
<g transform="translate(760 118)">
  <rect x="30" y="70" width="310" height="210" rx="28" fill="{$accent}" />
  <rect x="46" y="86" width="278" height="178" rx="22" fill="{$highlight}" />
  <circle cx="108" cy="145" r="36" fill="{$cookieFill}" />
  <circle cx="188" cy="145" r="36" fill="{$cookieFill}" />
  <circle cx="268" cy="145" r="36" fill="{$cookieFill}" />
  <circle cx="148" cy="214" r="36" fill="{$cookieFill}" />
  <circle cx="228" cy="214" r="36" fill="{$cookieFill}" />
  <circle cx="108" cy="145" r="8" fill="{$accent}" />
  <circle cx="188" cy="150" r="8" fill="{$accent}" />
  <circle cx="262" cy="139" r="8" fill="{$accent}" />
  <circle cx="145" cy="205" r="8" fill="{$accent}" />
  <circle cx="233" cy="220" r="8" fill="{$accent}" />
</g>
SVG;

        case 'gift':
            return <<<SVG
<g transform="translate(785 112)">
  <rect x="82" y="136" width="186" height="142" rx="18" fill="{$highlight}" />
  <rect x="74" y="122" width="202" height="36" rx="18" fill="{$accent}" />
  <rect x="162" y="122" width="22" height="156" rx="11" fill="{$accent}" />
  <path d="M171 92c-22-26-61-24-69 2-5 16 7 30 28 30h41V92z" fill="{$accent}" />
  <path d="M171 92c22-26 61-24 69 2 5 16-7 30-28 30h-41V92z" fill="{$accent}" />
  <circle cx="118" cy="230" r="22" fill="{$cookieFill}" />
  <circle cx="225" cy="230" r="22" fill="{$cookieFill}" />
</g>
SVG;

        case 'almond':
            return <<<SVG
<g transform="translate(790 110)">
  <circle cx="170" cy="190" r="138" fill="{$cookieFill}" />
  <path d="M115 162c0-28 23-51 51-51 20 0 37 11 46 28-10 18-28 31-48 39-15-5-49-25-49-16z" fill="{$accent}" opacity="0.9" />
  <path d="M188 198c0-28 23-51 51-51 20 0 37 11 46 28-10 18-28 31-48 39-15-5-49-25-49-16z" fill="{$accent}" opacity="0.78" />
  <circle cx="115" cy="230" r="10" fill="{$highlight}" />
  <circle cx="260" cy="118" r="10" fill="{$highlight}" />
</g>
SVG;

        case 'chips':
        default:
            return <<<SVG
<g transform="translate(790 110)">
  <circle cx="170" cy="190" r="138" fill="{$cookieFill}" />
  <circle cx="120" cy="142" r="14" fill="{$accent}" />
  <circle cx="232" cy="132" r="13" fill="{$accent}" />
  <circle cx="252" cy="218" r="15" fill="{$accent}" />
  <circle cx="104" cy="228" r="12" fill="{$accent}" />
  <circle cx="176" cy="250" r="13" fill="{$accent}" />
  <circle cx="183" cy="112" r="10" fill="{$highlight}" opacity="0.45" />
</g>
SVG;
    }
}

$slug = trim((string) ($_GET['slug'] ?? 'classic-chocolate-chip'));
$product = get_product_by_slug($slug);
if ($product === null) {
    $slug = 'classic-chocolate-chip';
    $product = get_product_by_slug($slug);
}

[$bgStart, $bgEnd, $cookieFill, $accent, $art] = cookie_product_theme($slug);
$title = cookie_svg_escape((string) ($product['name'] ?? 'Sweet Crumb Homemade Cookies'));
$subtitle = cookie_svg_escape((string) ($product['short'] ?? 'Freshly baked small-batch treats.'));
$artMarkup = cookie_product_art($art, $cookieFill, $accent, $bgStart);

header('Content-Type: image/svg+xml; charset=UTF-8');
header('Cache-Control: public, max-age=86400');

echo <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630" role="img" aria-labelledby="title desc">
  <title id="title">{$title}</title>
  <desc id="desc">Sweet Crumb Homemade Cookies product artwork for {$title}.</desc>
  <defs>
    <linearGradient id="bg" x1="0" x2="1" y1="0" y2="1">
      <stop offset="0%" stop-color="{$bgStart}" />
      <stop offset="100%" stop-color="{$bgEnd}" />
    </linearGradient>
  </defs>
  <rect width="1200" height="630" rx="36" fill="url(#bg)" />
  <circle cx="942" cy="315" r="208" fill="#ffffff" opacity="0.16" />
  <rect x="74" y="80" width="560" height="470" rx="28" fill="#ffffff" opacity="0.18" />
  <text x="110" y="150" font-family="Arial, Helvetica, sans-serif" font-size="34" font-weight="700" fill="#4c2f1d">Sweet Crumb Homemade Cookies</text>
  <text x="110" y="255" font-family="Arial, Helvetica, sans-serif" font-size="66" font-weight="800" fill="#2d1b18">{$title}</text>
  <text x="110" y="322" font-family="Arial, Helvetica, sans-serif" font-size="30" fill="#5f4332">{$subtitle}</text>
  <text x="110" y="500" font-family="Arial, Helvetica, sans-serif" font-size="28" fill="#5f4332">Freshly baked for the marketplace catalog</text>
  {$artMarkup}
</svg>
SVG;
