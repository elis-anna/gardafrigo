<?php
/**
 * Create isolated reference pages with the original Avada Energy layouts.
 *
 * These pages are intentionally kept separate from the migrated Garda Frigor
 * pages, under /avada-layout-originali/, so they can be used as a clean visual
 * reference while rebuilding content.
 *
 * Run with:
 * docker compose run --rm wpcli wp eval-file /scripts/import-avada-original-layout-pages.php
 */

if (!defined('WP_CLI') || !WP_CLI) {
    fwrite(STDERR, "This script must run through WP-CLI.\n");
    exit(1);
}

$layouts = [
    [
        'title' => 'SERVIZI - Solutions / Hydropower Plants',
        'slug' => 'servizi-solutions-hydropower-plants',
        'url' => 'https://avada.website/energy/solutions/hydropower-plants/',
        'note' => 'Layout scelto per tutte le pagine servizio Garda Frigor.',
    ],
    [
        'title' => 'CHI SIAMO - Company',
        'slug' => 'chi-siamo-company',
        'url' => 'https://avada.website/energy/company/',
        'note' => 'Layout scelto per la pagina Chi siamo.',
    ],
    [
        'title' => 'CONTATTI - Contact',
        'slug' => 'contatti-contact',
        'url' => 'https://avada.website/energy/contact/',
        'note' => 'Layout scelto per la pagina Contatti.',
    ],
    [
        'title' => 'CASE HISTORY E NEWS - Case Detail',
        'slug' => 'case-history-news-case-detail',
        'url' => 'https://avada.website/energy/case/a-secure-hydropower-supply-chain/',
        'note' => 'Layout scelto per case history e news.',
    ],
];

function gf_avada_original_fetch(string $url): string
{
    $response = wp_remote_get($url, [
        'timeout' => 30,
        'redirection' => 5,
        'user-agent' => 'Garda Frigor Avada original layout importer',
    ]);

    if (is_wp_error($response)) {
        WP_CLI::warning("Fetch failed for {$url}: " . $response->get_error_message());
        return '';
    }

    return (string) wp_remote_retrieve_body($response);
}

function gf_avada_original_absolute_html(string $html, string $url): string
{
    $base = trailingslashit($url);
    $parts = wp_parse_url($url);
    $origin = ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? 'avada.website');

    $html = preg_replace_callback('/(href|src)=(["\'])(?!https?:|data:|mailto:|tel:|javascript:|#)([^"\']*)\2/i', function (array $matches) use ($base, $origin): string {
        $attr = $matches[1];
        $quote = $matches[2];
        $value = $matches[3];
        $absolute = str_starts_with($value, '/') ? $origin . $value : $base . $value;

        return $attr . '=' . $quote . esc_url_raw($absolute) . $quote;
    }, $html);

    return $html;
}

function gf_avada_original_page_content(array $layout, string $html): string
{
    $source_url = esc_url($layout['url']);
    $note = esc_html($layout['note']);

    if ($html) {
        $frame = '<iframe title="' . esc_attr($layout['title']) . '" srcdoc="' . esc_attr($html) . '" style="width:100%;min-height:2200px;border:1px solid #d8dde3;background:#fff;"></iframe>';
    } else {
        $frame = '<iframe title="' . esc_attr($layout['title']) . '" src="' . $source_url . '" style="width:100%;min-height:2200px;border:1px solid #d8dde3;background:#fff;"></iframe>';
    }

    return '<div class="gf-avada-original-layout">'
        . '<h1>' . esc_html($layout['title']) . '</h1>'
        . '<p><strong>Uso:</strong> ' . $note . '</p>'
        . '<p><strong>URL originale Avada:</strong> <a href="' . $source_url . '" target="_blank" rel="noopener">' . $source_url . '</a></p>'
        . $frame
        . '</div>';
}

function gf_avada_original_upsert_page(string $path, array $postarr): int
{
    $existing = get_page_by_path($path, OBJECT, 'page');

    if ($existing) {
        return (int) wp_update_post(['ID' => $existing->ID] + $postarr);
    }

    return (int) wp_insert_post($postarr);
}

$parent_id = gf_avada_original_upsert_page('avada-layout-originali', [
    'post_type' => 'page',
    'post_status' => 'publish',
    'post_title' => 'AVADA LAYOUT ORIGINALI',
    'post_name' => 'avada-layout-originali',
    'post_parent' => 0,
    'post_content' => '<h1>AVADA LAYOUT ORIGINALI</h1><p>Layout Avada Energy originali, separati dal sito migrato, da usare come riferimento pulito per recuperare gabbia e blocchi.</p>',
]);

$index = '';

foreach ($layouts as $layout) {
    $html = gf_avada_original_fetch($layout['url']);
    if ($html) {
        $html = gf_avada_original_absolute_html($html, $layout['url']);
    }

    $page_id = gf_avada_original_upsert_page('avada-layout-originali/' . $layout['slug'], [
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => 'AVADA ORIGINALE - ' . $layout['title'],
        'post_name' => $layout['slug'],
        'post_parent' => $parent_id,
        'post_content' => gf_avada_original_page_content($layout, $html),
    ]);

    update_post_meta($page_id, '_gardafrigor_avada_original_layout', '1');
    update_post_meta($page_id, '_gardafrigor_avada_original_url', $layout['url']);

    $index .= '<li><a href="' . esc_url(get_permalink($page_id)) . '">' . esc_html(get_the_title($page_id)) . '</a><br><small>' . esc_html($layout['url']) . '</small></li>';
    WP_CLI::log(sprintf('Imported Avada original layout %-45s -> #%d %s', $layout['title'], $page_id, get_permalink($page_id)));
}

wp_update_post([
    'ID' => $parent_id,
    'post_content' => '<h1>AVADA LAYOUT ORIGINALI</h1><p>Layout Avada Energy originali, separati dal sito migrato, da usare come riferimento pulito per recuperare gabbia e blocchi.</p><ul>' . $index . '</ul>',
]);

do_action('fusion_cache_reset_all');
flush_rewrite_rules();

WP_CLI::success('Created original Avada layout reference pages under /avada-layout-originali/.');
