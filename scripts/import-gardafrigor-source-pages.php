<?php
/**
 * Create a browsable "SORGENTE" area with the original Garda Frigor pages.
 *
 * This intentionally keeps the original source layout in an iframe so the
 * migrated Avada pages can be rebuilt by copying from the real legacy pages.
 *
 * Run with:
 * docker compose run --rm wpcli wp eval-file /scripts/import-gardafrigor-source-pages.php
 */

if (!defined('WP_CLI') || !WP_CLI) {
    fwrite(STDERR, "This script must run through WP-CLI.\n");
    exit(1);
}

$base_url = 'https://www.gardafrigor.it/';
$domain = 'www.gardafrigor.it';
$max_urls = 220;

function gf_source_fetch(string $url): array
{
    $response = wp_remote_get($url, [
        'timeout' => 25,
        'redirection' => 5,
        'user-agent' => 'Garda Frigor source template importer',
    ]);

    if (is_wp_error($response)) {
        return ['ok' => false, 'error' => $response->get_error_message(), 'body' => '', 'content_type' => ''];
    }

    return [
        'ok' => true,
        'error' => '',
        'body' => (string) wp_remote_retrieve_body($response),
        'content_type' => (string) wp_remote_retrieve_header($response, 'content-type'),
    ];
}

function gf_source_normalize_url(string $url): string
{
    $parts = wp_parse_url($url);
    $path = $parts['path'] ?? '/';
    return 'https://www.gardafrigor.it' . ($path ?: '/');
}

function gf_source_is_asset(string $url): bool
{
    $path = strtolower((string) (wp_parse_url($url, PHP_URL_PATH) ?: ''));
    return (bool) preg_match('/\.(jpg|jpeg|png|gif|webp|svg|css|js|ico|zip|mp4|woff|woff2|ttf)$/', $path);
}

function gf_source_is_pdf(string $url): bool
{
    $path = strtolower((string) (wp_parse_url($url, PHP_URL_PATH) ?: ''));
    return str_ends_with($path, '.pdf');
}

function gf_source_title_from_html(string $html, string $url): string
{
    if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $html, $matches)) {
        $title = trim(html_entity_decode(wp_strip_all_tags($matches[1]), ENT_QUOTES, 'UTF-8'));
        if ($title && strtoupper($title) !== 'NEWS') {
            return $title;
        }
    }

    if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
        $title = trim(html_entity_decode(wp_strip_all_tags($matches[1]), ENT_QUOTES, 'UTF-8'));
        if ($title) {
            return $title;
        }
    }

    $path = trim((string) (wp_parse_url($url, PHP_URL_PATH) ?: '/'), '/');
    return $path ? ucwords(str_replace(['-', '/', '.html'], [' ', ' / ', ''], $path)) : 'Home';
}

function gf_source_absolute_html(string $html): string
{
    $html = preg_replace('/(href|src)=(["\'])\/([^"\']*)\2/i', '$1=$2https://www.gardafrigor.it/$3$2', $html);
    $html = preg_replace('/(href|src)=(["\'])(?!https?:|mailto:|tel:|javascript:|#)([^"\']*)\2/i', '$1=$2https://www.gardafrigor.it/$3$2', $html);

    return $html;
}

function gf_source_slug(string $url): string
{
    $path = trim((string) (wp_parse_url($url, PHP_URL_PATH) ?: '/'), '/');
    if (!$path) {
        return 'home';
    }

    $path = preg_replace('/\.html$/', '', $path);
    $path = preg_replace('/\.pdf$/', '', $path);
    return sanitize_title(str_replace('/', '-', $path));
}

function gf_source_upsert_page(string $title, string $slug, string $content, int $parent_id, string $url): int
{
    $existing = get_page_by_path('sorgente/' . $slug, OBJECT, 'page');
    $postarr = [
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => 'SORGENTE - ' . $title,
        'post_name' => $slug,
        'post_parent' => $parent_id,
        'post_content' => $content,
    ];

    if ($existing) {
        $post_id = wp_update_post(['ID' => $existing->ID] + $postarr);
    } else {
        $post_id = wp_insert_post($postarr);
    }

    update_post_meta((int) $post_id, '_gardafrigor_source_url', $url);
    update_post_meta((int) $post_id, '_gardafrigor_source_template', '1');

    return (int) $post_id;
}

$parent = get_page_by_path('sorgente', OBJECT, 'page');
if (!$parent) {
    $parent_id = wp_insert_post([
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => 'SORGENTE',
        'post_name' => 'sorgente',
        'post_content' => '<h1>SORGENTE</h1><p>Pagine originali Garda Frigor recuperate dal sito live per ricostruire le impaginazioni Avada.</p>',
    ]);
} else {
    $parent_id = (int) $parent->ID;
    wp_update_post([
        'ID' => $parent_id,
        'post_title' => 'SORGENTE',
        'post_content' => '<h1>SORGENTE</h1><p>Pagine originali Garda Frigor recuperate dal sito live per ricostruire le impaginazioni Avada.</p>',
        'post_status' => 'publish',
    ]);
}

$queue = [$base_url];
$seen = [];
$html_pages = [];
$pdfs = [];

while ($queue && count($seen) < $max_urls) {
    $url = array_shift($queue);
    $url = gf_source_normalize_url($url);

    if (isset($seen[$url]) || gf_source_is_asset($url)) {
        continue;
    }

    $seen[$url] = true;

    if (gf_source_is_pdf($url)) {
        $pdfs[$url] = true;
        continue;
    }

    $fetched = gf_source_fetch($url);
    if (!$fetched['ok']) {
        WP_CLI::warning("Fetch failed for {$url}: {$fetched['error']}");
        continue;
    }

    $html = $fetched['body'];
    if (!str_contains(strtolower(substr($html, 0, 3000)), '<html')) {
        continue;
    }

    $html_pages[$url] = $html;

    if (preg_match_all('/<a\s+[^>]*href=(["\'])(.*?)\1/i', $html, $matches)) {
        foreach ($matches[2] as $href) {
            $href = trim(html_entity_decode($href, ENT_QUOTES, 'UTF-8'));
            if (!$href || str_starts_with($href, '#') || str_starts_with($href, 'mailto:') || str_starts_with($href, 'tel:') || str_starts_with($href, 'javascript:')) {
                continue;
            }

            $absolute = wp_http_validate_url($href) ? $href : WP_Http::make_absolute_url($href, $url);
            $parts = wp_parse_url($absolute);

            if (($parts['host'] ?? '') !== 'www.gardafrigor.it' && ($parts['host'] ?? '') !== 'gardafrigor.it') {
                continue;
            }

            $clean = gf_source_normalize_url($absolute);
            if (!isset($seen[$clean])) {
                $queue[] = $clean;
            }
        }
    }
}

ksort($html_pages);
ksort($pdfs);

$imported = 0;
foreach ($html_pages as $url => $html) {
    $source_html = gf_source_absolute_html($html);
    $title = gf_source_title_from_html($source_html, $url);
    $srcdoc = esc_attr($source_html);
    $source_link = esc_url($url);

    $content = '<div class="gf-source-template">'
        . '<p><strong>URL sorgente:</strong> <a href="' . $source_link . '" target="_blank" rel="noopener">' . esc_html($url) . '</a></p>'
        . '<iframe title="' . esc_attr($title) . '" srcdoc="' . $srcdoc . '" style="width:100%;min-height:1600px;border:1px solid #d8dde3;background:#fff;"></iframe>'
        . '</div>';

    gf_source_upsert_page($title, gf_source_slug($url), $content, $parent_id, $url);
    $imported++;
}

foreach (array_keys($pdfs) as $url) {
    $path = basename((string) wp_parse_url($url, PHP_URL_PATH));
    $title = $path ?: 'PDF';
    $source_link = esc_url($url);
    $content = '<div class="gf-source-template">'
        . '<p><strong>PDF sorgente:</strong> <a href="' . $source_link . '" target="_blank" rel="noopener">' . esc_html($url) . '</a></p>'
        . '<iframe title="' . esc_attr($title) . '" src="' . $source_link . '" style="width:100%;min-height:1200px;border:1px solid #d8dde3;background:#fff;"></iframe>'
        . '</div>';

    gf_source_upsert_page($title, gf_source_slug($url), $content, $parent_id, $url);
    $imported++;
}

$children = get_posts([
    'post_type' => 'page',
    'post_status' => 'publish',
    'post_parent' => $parent_id,
    'meta_key' => '_gardafrigor_source_template',
    'numberposts' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
]);

$index_items = '';
foreach ($children as $child) {
    $source_url = get_post_meta($child->ID, '_gardafrigor_source_url', true);
    $index_items .= '<li><a href="' . esc_url(get_permalink($child)) . '">' . esc_html(get_the_title($child)) . '</a>';
    if ($source_url) {
        $index_items .= ' <small>' . esc_html($source_url) . '</small>';
    }
    $index_items .= '</li>';
}

wp_update_post([
    'ID' => $parent_id,
    'post_content' => '<h1>SORGENTE</h1><p>Pagine originali Garda Frigor recuperate dal sito live per ricostruire le impaginazioni Avada.</p><ul>' . $index_items . '</ul>',
]);

do_action('fusion_cache_reset_all');
flush_rewrite_rules();

WP_CLI::success("Created {$imported} SORGENTE pages under /sorgente/.");
