<?php
/**
 * Import the legacy Garda Frigor public pages into the local Avada site.
 *
 * Run with:
 * docker compose run --rm wpcli wp eval-file /scripts/import-gardafrigor-live-pages.php
 */

if (!defined('WP_CLI') || !WP_CLI) {
    fwrite(STDERR, "This script must run through WP-CLI.\n");
    exit(1);
}

$source_base = 'https://www.gardafrigor.it';

$urls = [
    '/',
    '/azienda.html',
    '/servizi.html',
    '/marchi.html',
    '/case-history.html',
    '/news.html',
    '/contatti.html',
    '/recupero-fiscale.html',
    '/impianti-condizionamento/',
    '/impianti-riscaldamento/',
    '/impianti-trattamento-aria/',
    '/impianti-refrigerazione/',
    '/sei-azienda.html',
    '/sei-privato.html',
    '/manutenzione.html',
    '/certificazioni.html',
    '/assistenza.html',
    '/impianti-condizionamento/casa/',
    '/impianti-trattamento-aria/casa/installazione-deumidificatori.html',
    '/ambiente.html',
    '/consulenza.html',
    '/fornitura.html',
    '/installazioni.html',
    '/ricambistica.html',
    '/news/auguri-di-buone-feste_24.html',
    '/news/nuovo-dpr146-2018_22.html',
    '/news/buon-anno_21.html',
    '/news/conto-termico-20_20.html',
    '/news/nuovi-clima-carrier_19.html',
    '/impianti-condizionamento/commerciali/',
    '/impianti-riscaldamento/casa/',
    '/impianti-riscaldamento/commerciali/',
    '/impianti-trattamento-aria/casa/',
    '/impianti-trattamento-aria/commerciali/',
    '/impianti-refrigerazione/installazione-mobili-refrigerati.html',
    '/impianti-refrigerazione/installazione-banchi-refrigerati.html',
    '/impianti-refrigerazione/installazione-banchi-pizza.html',
    '/impianti-refrigerazione/installazione-fermalievitatori.html',
    '/impianti-refrigerazione/installazione-celle-frigorifere.html',
    '/impianti-refrigerazione/installazione-centrali-frigorifere.html',
    '/impianti-refrigerazione/installazione-abbattitori.html',
    '/manutenzione/manutenzione-cond-tratt.html',
    '/manutenzione/manutenzione-riscaldamento.html',
    '/manutenzione/manutenzione-refrigerazione.html',
    '/impianti-condizionamento/casa/condizionatori-monosplit.html',
    '/impianti-condizionamento/casa/condizionatori-multisplit.html',
    '/impianti-condizionamento/casa/soluzioni-integrate.html',
    '/impianti-trattamento-aria/casa/recuperatori-calore-domestici.html',
    '/impianti-trattamento-aria/casa/posa-canalizzazioni-aria-diffusori.html',
    '/impianti-trattamento-aria/casa/sistemi-domestici-elettronica.html',
    '/news/lyexpo-verona_P2_18.html',
    '/news/lyexpo-2015_P2_17.html',
    '/news/insieme-siamo-piu-forti_P2_16.html',
    '/news/regolamento-ce-517-14_P2_15.html',
    '/news/targatura-impianti-termci_P2_14.html',
    '/news/bollino-caldaie_P3_13.html',
    '/news/proroga-nuovi-adempimenti-esercizio-impianti-termici_P3_12.html',
    '/news/regolamento-ue-n-517-2014-del-16-04-14_P3_11.html',
    '/news/libretto-d-impianto-lombardia-_P3_10.html',
    '/news/nuovi-libretti-per-la-climatizzazione-e-nuovi-rapporti-di-efficienza-energetica_P3_8.html',
    '/news/aggiornate-le-disposizioni-regionali-per-l-esercizio-il-controllo-la-manutenzione-e-l-ispezione-degli-impianti-termici_P4_4.html',
    '/impianti-condizionamento/commerciali/condizionatori-monosplit.html',
    '/impianti-condizionamento/commerciali/condizionatori-multisplit.html',
    '/impianti-condizionamento/commerciali/sistemi-freon-variabile.html',
    '/impianti-condizionamento/commerciali/sistemi-idronici.html',
    '/impianti-riscaldamento/casa/installazione-caldaie.html',
    '/impianti-riscaldamento/casa/installazione-riscaldamento-pavimento.html',
    '/impianti-riscaldamento/casa/installazione-pompe-calore.html',
    '/impianti-riscaldamento/casa/soluzioni-integrate.html',
    '/impianti-riscaldamento/casa/pannelli-solari-termici.html',
    '/impianti-riscaldamento/commerciali/installazione-caldaie.html',
    '/impianti-riscaldamento/commerciali/impianti-riscaldamento-pavimento.html',
    '/impianti-riscaldamento/commerciali/installazione-pompe-calore.html',
    '/impianti-riscaldamento/commerciali/soluzioni-integrate.html',
    '/impianti-riscaldamento/commerciali/sistemi-freon-variabile.html',
    '/impianti-riscaldamento/commerciali/sistemi-idronici.html',
    '/impianti-riscaldamento/commerciali/pannelli-solari-termici.html',
    '/impianti-trattamento-aria/commerciali/recuperatori-calore.html',
    '/impianti-trattamento-aria/commerciali/diffusori-terminali-aria.html',
    '/impianti-trattamento-aria/commerciali/sistemi-gestione-elettronica.html',
];

$special_pages = [
    '/' => ['id' => 768, 'title' => 'Home', 'slug' => 'home', 'layout' => 'home'],
    '/azienda.html' => ['id' => 2204, 'title' => 'Chi siamo', 'slug' => 'chi-siamo', 'layout' => 'company'],
    '/servizi.html' => ['id' => 6, 'title' => 'Servizi', 'slug' => 'servizi', 'layout' => 'services_index'],
    '/marchi.html' => ['id' => 7, 'title' => 'Marchi', 'slug' => 'marchi', 'layout' => 'service'],
    '/case-history.html' => ['id' => 60, 'title' => 'Case history', 'slug' => 'case-history', 'layout' => 'case'],
    '/news.html' => ['id' => 9, 'title' => 'News', 'slug' => 'news', 'layout' => 'news_index'],
    '/contatti.html' => ['id' => 607, 'title' => 'Contatti', 'slug' => 'contatti', 'layout' => 'contact'],
];

$service_cards = [
    '/impianti-condizionamento/' => 'Condizionamento',
    '/impianti-riscaldamento/' => 'Riscaldamento',
    '/impianti-trattamento-aria/' => 'Trattamento aria',
    '/impianti-refrigerazione/' => 'Refrigerazione',
];

function gardafrigor_fetch_html(string $url): string
{
    $response = wp_remote_get($url, [
        'timeout' => 20,
        'redirection' => 5,
        'user-agent' => 'Garda Frigor WordPress local importer',
    ]);

    if (is_wp_error($response)) {
        WP_CLI::warning("Fetch failed for {$url}: " . $response->get_error_message());
        return '';
    }

    return (string) wp_remote_retrieve_body($response);
}

function gardafrigor_first_match(string $pattern, string $html): string
{
    if (preg_match($pattern, $html, $matches)) {
        return trim(html_entity_decode(wp_strip_all_tags($matches[1]), ENT_QUOTES, 'UTF-8'));
    }

    return '';
}

function gardafrigor_inner_html(DOMNode $node): string
{
    $html = '';
    foreach ($node->childNodes as $child) {
        $html .= $node->ownerDocument->saveHTML($child);
    }

    return $html;
}

function gardafrigor_extract_content(string $html, string $url): array
{
    $title = gardafrigor_first_match('/<title[^>]*>(.*?)<\/title>/is', $html);
    $description = '';

    if (preg_match('/<meta\s+name=["\']description["\']\s+content=["\']([^"\']*)["\']/i', $html, $matches)) {
        $description = trim(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8'));
    }

    $body = '';
    if (class_exists('DOMDocument')) {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="UTF-8">' . $html);
        $xpath = new DOMXPath($doc);
        $nodes = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' contenuto-interno ')]");

        if ($nodes && $nodes->length) {
            $node = $nodes->item(0);
            foreach ($xpath->query('.//script|.//style|.//iframe|.//form', $node) as $remove) {
                $remove->parentNode->removeChild($remove);
            }
            foreach ($xpath->query('.//img', $node) as $img) {
                $src = $img->getAttribute('src');
                if ($src && str_starts_with($src, '/')) {
                    $img->setAttribute('src', 'https://www.gardafrigor.it' . $src);
                }
                if ($src && !str_starts_with($src, 'http')) {
                    $img->setAttribute('src', 'https://www.gardafrigor.it/' . ltrim($src, '/'));
                }
            }
            $body = gardafrigor_inner_html($node);
        }
    }

    if (!$body && preg_match('/<div\s+class=["\']contenuto-interno["\'][^>]*>(.*?)(?:<div\s+style=["\']height:5px;background-color:#efefef|<div\s+class=["\']sfondo-footer-bottom)/is', $html, $matches)) {
        $body = $matches[1];
    }

    if (!$body) {
        $body = '<p>' . esc_html($description ?: $title) . '</p>';
    }

    $body = preg_replace('/<h1\b[^>]*>(.*?)<\/h1>/is', '', $body, 1);
    $body = preg_replace('/\s+/', ' ', $body);
    $body = gardafrigor_rewrite_import_links($body);
    $body = wp_kses_post($body);

    $heading = gardafrigor_first_match('/<h1[^>]*>(.*?)<\/h1>/is', $html);
    if (!$heading || strtoupper($heading) === 'NEWS') {
        $heading = $title;
    }

    return [
        'title' => $title,
        'heading' => $heading,
        'description' => $description,
        'body' => $body,
    ];
}

function gardafrigor_rewrite_import_links(string $html): string
{
    return preg_replace_callback('/href=(["\'])(https?:\/\/(?:www\.)?gardafrigor\.it)?([^"\']*)\1/i', function (array $matches): string {
        $quote = $matches[1];
        $path = $matches[3] ?: '/';

        if (str_starts_with($path, 'mailto:') || str_starts_with($path, 'tel:') || str_starts_with($path, '#')) {
            return $matches[0];
        }

        $parsed = wp_parse_url($path);
        $clean_path = $parsed['path'] ?? '/';

        if (preg_match('/\.(pdf|jpg|jpeg|png|gif|webp|zip)$/i', $clean_path)) {
            return 'href=' . $quote . 'https://www.gardafrigor.it' . $clean_path . $quote;
        }

        return 'href=' . $quote . home_url(gardafrigor_clean_path($clean_path)) . $quote;
    }, $html);
}

function gardafrigor_builder_content(array $data, string $layout, array $children = []): string
{
    $accent = [
        'company' => 'Chi siamo',
        'contact' => 'Contatti',
        'case' => 'Case history',
        'news' => 'News',
        'news_index' => 'News',
        'services_index' => 'Servizi',
        'home' => 'Garda Frigor',
        'service' => 'Solutions',
    ][$layout] ?? 'Solutions';

    $description = $data['description'] ?: 'Soluzioni Garda Frigor per climatizzazione, riscaldamento, trattamento aria e refrigerazione.';
    $body = $data['body'];

    $links = '';
    foreach ($children as $url => $label) {
        $links .= sprintf(
            '<li><a href="%s">%s</a></li>',
            esc_url(home_url(gardafrigor_clean_path($url))),
            esc_html($label)
        );
    }

    if ($links) {
        $body .= '<h3>Approfondimenti</h3><ul>' . $links . '</ul>';
    }

    return '[fusion_builder_container type="flex" hundred_percent="no" hundred_percent_height="no" min_height_medium="" min_height_small="" min_height="" hundred_percent_height_scroll="no" align_content="stretch" flex_align_items="center" flex_justify_content="center" flex_column_spacing="" hundred_percent_height_center_content="yes" equal_height_columns="no" container_tag="div" menu_anchor="" hide_on_mobile="small-visibility,medium-visibility,large-visibility" status="published" publish_date="" class="" id="" spacing_medium="" margin_top_medium="" margin_bottom_medium="" spacing_small="" margin_top_small="" margin_bottom_small="" margin_top="0px" margin_bottom="0px" padding_top="90px" padding_right="30px" padding_bottom="70px" padding_left="30px" link_color="" link_hover_color="" border_sizes="" border_color="" border_style="solid" border_radius_top_left="" border_radius_top_right="" border_radius_bottom_right="" border_radius_bottom_left="" box_shadow="no" box_shadow_vertical="" box_shadow_horizontal="" box_shadow_blur="0" box_shadow_spread="0" box_shadow_color="" box_shadow_style="" z_index="" overflow="" gradient_start_color="#eef9fd" gradient_end_color="#ffffff" gradient_start_position="0" gradient_end_position="100" gradient_type="linear" radial_direction="center center" linear_angle="180" background_color="#eef9fd" background_image="" skip_lazy_load="" background_position="center center" background_repeat="no-repeat" fade="no" background_parallax="none" enable_mobile="no" parallax_speed="0.3" background_blend_mode="none" video_mp4="" video_webm="" video_ogv="" video_url="" video_aspect_ratio="16:9" video_loop="yes" video_mute="yes" video_preview_image="" render_logics="" absolute="off" absolute_devices="small,medium,large" sticky="off" sticky_devices="small-visibility,medium-visibility,large-visibility" sticky_transition_offset="0" scroll_offset="0" animation_type="" animation_direction="left" animation_color="" animation_speed="0.3" animation_delay="0" filter_hue="0" filter_saturation="100" filter_brightness="100" filter_contrast="100" filter_invert="0" filter_sepia="0" filter_opacity="100" filter_blur="0" filter_hue_hover="0" filter_saturation_hover="100" filter_brightness_hover="100" filter_contrast_hover="100" filter_invert_hover="0" filter_sepia_hover="0" filter_opacity_hover="100" filter_blur_hover="0"]'
        . '[fusion_builder_row][fusion_builder_column type="1_1" layout="1_1" align_self="auto" content_layout="column" align_content="flex-start" valign_content="flex-start" content_wrap="wrap" spacing="" center_content="no" column_tag="div" link="" target="_self" link_description="" min_height="" hide_on_mobile="small-visibility,medium-visibility,large-visibility" sticky_display="normal,sticky" class="" id="" type_medium="" type_small="" order_medium="0" order_small="0" dimension_spacing_medium="" dimension_spacing_small="" dimension_spacing="" dimension_margin_medium="" dimension_margin_small="" margin_top="" margin_bottom="" padding_medium="" padding_small="" padding_top="" padding_right="" padding_bottom="" padding_left="" hover_type="none" border_sizes="" border_color="" border_style="solid" border_radius="" box_shadow="no" box_shadow_vertical="" box_shadow_horizontal="" box_shadow_blur="0" box_shadow_spread="0" box_shadow_color="" box_shadow_style="" overflow="" background_type="single" gradient_start_color="" gradient_end_color="" gradient_start_position="0" gradient_end_position="100" gradient_type="linear" radial_direction="center center" linear_angle="180" background_color="" background_image="" background_image_id="" background_position="left top" background_repeat="no-repeat" background_blend_mode="none" render_logics="" sticky="off" sticky_devices="small-visibility,medium-visibility,large-visibility" sticky_offset="" absolute="off" absolute_props="" filter_type="regular" filter_hue="0" filter_saturation="100" filter_brightness="100" filter_contrast="100" filter_invert="0" filter_sepia="0" filter_opacity="100" filter_blur="0" filter_hue_hover="0" filter_saturation_hover="100" filter_brightness_hover="100" filter_contrast_hover="100" filter_invert_hover="0" filter_sepia_hover="0" filter_opacity_hover="100" filter_blur_hover="0" transform_type="regular" animation_type="" animation_direction="left" animation_color="" animation_speed="0.3" animation_delay="0" last="true" border_position="all" first="true"]'
        . '[fusion_text columns="" column_min_width="" column_spacing="" rule_style="" rule_size="" rule_color="" hue="" saturation="" lightness="" alpha="" user_select="" content_alignment="" hide_on_mobile="small-visibility,medium-visibility,large-visibility" sticky_display="normal,sticky" class="gf-eyebrow" id="" margin_top="" margin_right="" margin_bottom="16px" margin_left="" fusion_font_family_text_font="" fusion_font_variant_text_font="" font_size="16px" line_height="1.5" letter_spacing="" text_color="#f58220" animation_type="" animation_direction="left" animation_color="" animation_speed="0.3" animation_delay="0"]' . esc_html($accent) . '[/fusion_text]'
        . '[fusion_title title_type="text" rotation_effect="bounceIn" display_time="1200" highlight_effect="circle" loop_animation="off" highlight_width="9" highlight_top_margin="0" before_text="" rotation_text="" highlight_text="" after_text="" title_link="off" link_url="" link_target="_self" hide_on_mobile="small-visibility,medium-visibility,large-visibility" sticky_display="normal,sticky" class="" id="" content_align="left" size="1" animated_font_size="" fusion_font_family_title_font="" fusion_font_variant_title_font="" font_size="48px" line_height="1.1" letter_spacing="0" text_color="#1f4f92" margin_top="" margin_bottom="22px" margin_top_mobile="" margin_bottom_mobile="" text_shadow="no" text_shadow_vertical="" text_shadow_horizontal="" text_shadow_blur="0" text_shadow_color="" style_type="default" sep_color="" link_color="" link_hover_color="" animation_type="" animation_direction="left" animation_color="" animation_speed="0.3" animation_delay="0"]' . esc_html($data['heading']) . '[/fusion_title]'
        . '[fusion_text columns="" column_min_width="" column_spacing="" rule_style="" rule_size="" rule_color="" hue="" saturation="" lightness="" alpha="" user_select="" content_alignment="" hide_on_mobile="small-visibility,medium-visibility,large-visibility" sticky_display="normal,sticky" class="" id="" margin_top="" margin_right="" margin_bottom="" margin_left="" fusion_font_family_text_font="" fusion_font_variant_text_font="" font_size="20px" line_height="1.55" letter_spacing="0" text_color="#17212b" animation_type="" animation_direction="left" animation_color="" animation_speed="0.3" animation_delay="0"]' . esc_html($description) . '[/fusion_text]'
        . '[/fusion_builder_column][/fusion_builder_row][/fusion_builder_container]'
        . '[fusion_builder_container type="flex" hundred_percent="no" hundred_percent_height="no" min_height="" align_content="stretch" flex_align_items="flex-start" flex_justify_content="center" flex_column_spacing="" equal_height_columns="no" container_tag="div" hide_on_mobile="small-visibility,medium-visibility,large-visibility" status="published" margin_top="0px" margin_bottom="0px" padding_top="70px" padding_right="30px" padding_bottom="90px" padding_left="30px" border_style="solid" box_shadow="no" box_shadow_blur="0" box_shadow_spread="0" gradient_start_position="0" gradient_end_position="100" gradient_type="linear" radial_direction="center center" linear_angle="180" background_color="#ffffff" background_position="center center" background_repeat="no-repeat" background_parallax="none" enable_mobile="no" parallax_speed="0.3" background_blend_mode="none" video_aspect_ratio="16:9" video_loop="yes" video_mute="yes" absolute="off" absolute_devices="small,medium,large" sticky="off" sticky_devices="small-visibility,medium-visibility,large-visibility" sticky_transition_offset="0" scroll_offset="0" animation_direction="left" animation_speed="0.3" animation_delay="0" filter_hue="0" filter_saturation="100" filter_brightness="100" filter_contrast="100" filter_invert="0" filter_sepia="0" filter_opacity="100" filter_blur="0" filter_hue_hover="0" filter_saturation_hover="100" filter_brightness_hover="100" filter_contrast_hover="100" filter_invert_hover="0" filter_sepia_hover="0" filter_opacity_hover="100" filter_blur_hover="0"]'
        . '[fusion_builder_row][fusion_builder_column type="1_1" layout="1_1" align_self="auto" content_layout="column" align_content="flex-start" valign_content="flex-start" content_wrap="wrap" center_content="no" column_tag="div" target="_self" min_height="" hide_on_mobile="small-visibility,medium-visibility,large-visibility" sticky_display="normal,sticky" order_medium="0" order_small="0" border_style="solid" box_shadow="no" box_shadow_blur="0" box_shadow_spread="0" hover_type="none" background_type="single" gradient_start_position="0" gradient_end_position="100" gradient_type="linear" radial_direction="center center" linear_angle="180" background_position="left top" background_repeat="no-repeat" background_blend_mode="none" sticky="off" sticky_devices="small-visibility,medium-visibility,large-visibility" absolute="off" filter_type="regular" filter_hue="0" filter_saturation="100" filter_brightness="100" filter_contrast="100" filter_invert="0" filter_sepia="0" filter_opacity="100" filter_blur="0" filter_hue_hover="0" filter_saturation_hover="100" filter_brightness_hover="100" filter_contrast_hover="100" filter_invert_hover="0" filter_sepia_hover="0" filter_opacity_hover="100" filter_blur_hover="0" transform_type="regular" animation_direction="left" animation_speed="0.3" animation_delay="0" last="true" border_position="all" first="true"]'
        . '[fusion_text columns="" column_min_width="" column_spacing="" rule_style="" rule_size="" rule_color="" hue="" saturation="" lightness="" alpha="" user_select="" content_alignment="" hide_on_mobile="small-visibility,medium-visibility,large-visibility" sticky_display="normal,sticky" class="gf-imported-content" id="" margin_top="" margin_right="" margin_bottom="" margin_left="" fusion_font_family_text_font="" fusion_font_variant_text_font="" font_size="18px" line_height="1.75" letter_spacing="0" text_color="#17212b" animation_type="" animation_direction="left" animation_color="" animation_speed="0.3" animation_delay="0"]' . $body . '[/fusion_text]'
        . '[/fusion_builder_column][/fusion_builder_row][/fusion_builder_container]';
}

function gardafrigor_clean_path(string $path): string
{
    if ($path === '/') {
        return '/';
    }

    $path = trim($path, '/');
    $path = preg_replace('/\.html$/', '', $path);
    return '/' . $path . '/';
}

function gardafrigor_find_page_by_path(string $clean_path): ?WP_Post
{
    $page = get_page_by_path(trim($clean_path, '/'), OBJECT, 'page');
    return $page instanceof WP_Post ? $page : null;
}

function gardafrigor_ensure_parent_path(string $clean_path): int
{
    $parts = array_values(array_filter(explode('/', trim($clean_path, '/'))));
    array_pop($parts);

    $parent_id = 0;
    $current = '';

    foreach ($parts as $part) {
        $current .= '/' . $part;
        $page = gardafrigor_find_page_by_path($current . '/');
        if (!$page) {
            $parent_id = wp_insert_post([
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_title' => ucwords(str_replace('-', ' ', $part)),
                'post_name' => sanitize_title($part),
                'post_parent' => $parent_id,
                'post_content' => gardafrigor_builder_content([
                    'heading' => ucwords(str_replace('-', ' ', $part)),
                    'description' => 'Pagina raccoglitore importata dal sito Garda Frigor.',
                    'body' => '<p>Pagina raccoglitore importata dal sito Garda Frigor.</p>',
                ], 'service'),
            ]);
        } else {
            $parent_id = (int) $page->ID;
        }
    }

    return $parent_id;
}

function gardafrigor_upsert_page(string $source_path, array $data, string $layout, array $special = [], array $children = []): int
{
    $clean_path = $special['clean_path'] ?? gardafrigor_clean_path($source_path);
    $title = $special['title'] ?? $data['heading'];
    $slug = $special['slug'] ?? basename(trim($clean_path, '/'));
    $parent_id = isset($special['id']) ? 0 : gardafrigor_ensure_parent_path($clean_path);
    $content = gardafrigor_builder_content($data, $layout, $children);

    if (isset($special['id']) && get_post((int) $special['id'])) {
        $post_id = (int) $special['id'];
        wp_update_post([
            'ID' => $post_id,
            'post_title' => $title,
            'post_name' => $slug,
            'post_parent' => $parent_id,
            'post_status' => 'publish',
            'post_content' => $content,
            'post_excerpt' => $data['description'],
        ]);
    } else {
        $page = gardafrigor_find_page_by_path($clean_path);
        $postarr = [
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => $title,
            'post_name' => sanitize_title($slug),
            'post_parent' => $parent_id,
            'post_content' => $content,
            'post_excerpt' => $data['description'],
        ];
        $post_id = $page ? wp_update_post(['ID' => $page->ID] + $postarr) : wp_insert_post($postarr);
    }

    update_post_meta($post_id, '_gardafrigor_source_url', 'https://www.gardafrigor.it' . $source_path);
    update_post_meta($post_id, '_gardafrigor_import_layout', $layout);

    return (int) $post_id;
}

$imported = [];

foreach ($urls as $path) {
    $source_url = $source_base . $path;
    $html = gardafrigor_fetch_html($source_url);
    if (!$html) {
        continue;
    }

    $data = gardafrigor_extract_content($html, $source_url);
    $layout = 'service';
    $children = [];
    $special = $special_pages[$path] ?? [];

    if ($special) {
        $layout = $special['layout'];
        if ($layout === 'services_index') {
            $children = $service_cards;
        }
    } elseif (str_starts_with($path, '/news/')) {
        $layout = 'news';
    } elseif ($path === '/contatti.html') {
        $layout = 'contact';
    } elseif ($path === '/azienda.html') {
        $layout = 'company';
    } elseif ($path === '/case-history.html') {
        $layout = 'case';
    }

    $post_id = gardafrigor_upsert_page($path, $data, $layout, $special, $children);
    $imported[$path] = $post_id;
    WP_CLI::log(sprintf('Imported %-90s -> #%d %s', $path, $post_id, get_permalink($post_id)));
}

$menu = wp_get_nav_menu_object('Garda Frigor Main');
if ($menu) {
    $items = wp_get_nav_menu_items($menu->term_id) ?: [];
    $servizi_item_id = 0;
    $existing_object_ids = [];

    foreach ($items as $item) {
        $existing_object_ids[(int) $item->object_id] = true;
        if ((int) $item->object_id === 6) {
            $servizi_item_id = (int) $item->ID;
        }
    }

    foreach ($service_cards as $path => $label) {
        $page = gardafrigor_find_page_by_path(gardafrigor_clean_path($path));
        if (!$page || isset($existing_object_ids[(int) $page->ID])) {
            continue;
        }

        wp_update_nav_menu_item($menu->term_id, 0, [
            'menu-item-title' => $label,
            'menu-item-object' => 'page',
            'menu-item-object-id' => $page->ID,
            'menu-item-type' => 'post_type',
            'menu-item-status' => 'publish',
            'menu-item-parent-id' => $servizi_item_id,
        ]);
    }
}

do_action('fusion_cache_reset_all');
flush_rewrite_rules();

WP_CLI::success('Imported ' . count($imported) . ' Garda Frigor pages.');
