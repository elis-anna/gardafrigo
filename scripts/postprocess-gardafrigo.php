<?php
/**
 * Adapt the imported Avada Energy site to the Garda Frigor structure.
 *
 * Run with:
 * docker compose run --rm wpcli wp eval-file /scripts/postprocess-gardafrigo.php
 */

if (!defined('WP_CLI') || !WP_CLI) {
    fwrite(STDERR, "This script must run through WP-CLI.\n");
    exit(1);
}

$page_map = [
    768 => ['Home', 'home', 0],
    2203 => ['Solutions', 'solutions', 0],
    2208 => ['Condizionamento', 'condizionamento', 2203],
    2207 => ['Riscaldamento', 'riscaldamento', 2203],
    2206 => ['Trattamento aria', 'trattamento-aria', 2203],
    2205 => ['Refrigerazione', 'refrigerazione', 2203],
    2204 => ['Chi siamo', 'chi-siamo', 0],
    6 => ['Servizi', 'servizi', 0],
    7 => ['Marchi', 'marchi', 0],
    60 => ['Case history', 'case-history', 0],
    9 => ['News', 'news', 0],
    607 => ['Contatti', 'contatti', 0],
];

foreach ($page_map as $id => [$title, $slug, $parent]) {
    if (get_post($id)) {
        wp_update_post([
            'ID' => $id,
            'post_title' => $title,
            'post_name' => $slug,
            'post_parent' => $parent,
            'post_status' => 'publish',
        ]);
    }
}

foreach ([4, 5, 8, 10, 2] as $legacy_id) {
    $legacy = get_post($legacy_id);
    if ($legacy) {
        wp_update_post([
            'ID' => $legacy_id,
            'post_status' => 'draft',
            'post_name' => 'legacy-' . $legacy->post_name,
        ]);
    }
}

foreach ($page_map as $id => [$title, $slug, $parent]) {
    if (get_post($id)) {
        wp_update_post([
            'ID' => $id,
            'post_name' => $slug,
            'post_parent' => $parent,
        ]);
    }
}

update_option('show_on_front', 'page');
update_option('page_on_front', 768);
update_option('blogname', 'Garda Frigor');
update_option('blogdescription', 'Impianti di condizionamento, riscaldamento, trattamento aria e refrigerazione. Progetto WordPress custom by Cawipa Elise.');

$replacements = [
    'Efficient Power Management' => 'Condizionamento e comfort su misura',
    'Power your home' => 'Comfort per casa, aziende e strutture ricettive',
    'Operations Management' => 'Riscaldamento efficiente',
    'Energy Efficiency' => 'Efficienza degli impianti',
    'Inverter Integrations' => 'Assistenza tecnica',
    'Demand Response' => 'Manutenzione programmata',
    'Solar Panels' => 'Condizionamento',
    'Hydropower Plants' => 'Riscaldamento',
    'Wind Turbines' => 'Trattamento aria',
    'Resources' => 'Refrigerazione',
    'Company' => 'Chi siamo',
    'Projects' => 'Case history',
    'Contact' => 'Contatti',
    'Get in touch' => 'Contattaci',
    'Request a quote' => 'Richiedi assistenza',
    'View all resources' => 'Scopri la refrigerazione',
    'How it works' => 'Scopri Garda Frigor',
    'About Us' => 'Chi siamo',
    'Avada Energy' => 'Garda Frigor',
    'Nam magna ex, accumsan id auctor sed, finibus a urna. Proin interdum feugiat viverra. Praesent sapien tortor, pulvinar rutrum purus at, tincidunt.' => 'Dal 1993 progettiamo, installiamo e manteniamo impianti per il comfort e la continuita operativa di aziende, negozi, uffici, hotel, ristoranti e abitazioni.',
    'Sorbi interdum blandit tellus in viverra. Pellentesque habitant morbi tristique senectus et netus et malesuada.' => 'Soluzioni personalizzate, assistenza puntuale e programmi di manutenzione costruiti sulle esigenze del cliente.',
    'Vestibulum ante ipsum primis in faucibus orci luctus ultrices posuere cubilia curae.' => 'Interventi tecnici su misura per mantenere efficienza, affidabilita e qualita degli ambienti.',
    '“Community Solar projects like these allow local citizens and businesses to benefit directly from the energy produced by these projects.”' => '“Un unico interlocutore tecnico per impianti di climatizzazione, riscaldamento, trattamento aria e refrigerazione.”',
    'Jeremy Hanegan, Vice President, Company' => 'Garda Frigor Srl, Villanuova sul Clisi',
    'http://localhost:8080/contact/' => 'http://localhost:8080/contatti/',
    'http://localhost:8080/company/' => 'http://localhost:8080/chi-siamo/',
    'http://localhost:8080/resources/' => 'http://localhost:8080/refrigerazione/',
    'http://localhost:8080/refrigerazione/' => 'http://localhost:8080/solutions/refrigerazione/',
    'http://localhost:8080/condizionamento/' => 'http://localhost:8080/solutions/condizionamento/',
    'http://localhost:8080/riscaldamento/' => 'http://localhost:8080/solutions/riscaldamento/',
    'http://localhost:8080/trattamento-aria/' => 'http://localhost:8080/solutions/trattamento-aria/',
    'http://localhost:8080/solutions/solar-panels/' => 'http://localhost:8080/solutions/condizionamento/',
    'http://localhost:8080/solutions/hydropower-plants/' => 'http://localhost:8080/solutions/riscaldamento/',
    'http://localhost:8080/solutions/wind-turbines/' => 'http://localhost:8080/solutions/trattamento-aria/',
];

$posts = get_posts([
    'post_type' => ['page', 'post', 'avada_portfolio'],
    'post_status' => ['publish', 'draft'],
    'numberposts' => -1,
]);

foreach ($posts as $post) {
    $content = strtr($post->post_content, $replacements);
    if ($content !== $post->post_content) {
        wp_update_post([
            'ID' => $post->ID,
            'post_content' => $content,
        ]);
    }
}

$solution_copy = [
    2208 => 'Installazione e manutenzione di impianti di condizionamento per aziende, strutture ricettive, spazi commerciali e abitazioni.',
    2207 => 'Soluzioni per impianti di riscaldamento efficienti, affidabili e seguiti dalla consulenza alla manutenzione.',
    2206 => 'Impianti e servizi per qualita dell aria, comfort interno, filtrazione e gestione professionale degli ambienti.',
    2205 => 'Installazione, assistenza e manutenzione di sistemi di refrigerazione per attivita commerciali e industriali.',
];

foreach ($solution_copy as $id => $excerpt) {
    wp_update_post([
        'ID' => $id,
        'post_excerpt' => $excerpt,
    ]);
}

$main_menu_pages = [768, 2204, 6, 7, 60, 9, 607];

$build_gardafrigo_menu = function (string $menu_name) use ($main_menu_pages): int {
    $menu = wp_get_nav_menu_object($menu_name);
    $menu_id = $menu ? (int) $menu->term_id : wp_create_nav_menu($menu_name);

    $items = wp_get_nav_menu_items($menu_id) ?: [];
    foreach ($items as $item) {
        wp_delete_post($item->ID, true);
    }

    foreach ($main_menu_pages as $page_id) {
        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title' => get_the_title($page_id),
            'menu-item-object' => 'page',
            'menu-item-object-id' => $page_id,
            'menu-item-type' => 'post_type',
            'menu-item-status' => 'publish',
        ]);
    }

    return $menu_id;
};

$menu_id = $build_gardafrigo_menu('Garda Frigor Main');
$build_gardafrigo_menu('Energy Main Menu');
$build_gardafrigo_menu('Energy Mobile Menu');

set_theme_mod('nav_menu_locations', [
    'main_navigation' => $menu_id,
    'mobile_navigation' => $menu_id,
    'sticky_navigation' => $menu_id,
]);

$theme_uri = get_stylesheet_directory_uri();
$logo_attachment = get_page_by_title('Garda Frigor logo', OBJECT, 'attachment');

if (!$logo_attachment) {
    $logo_file = get_stylesheet_directory() . '/assets/garda-frigor-logo.jpg';
    $upload = wp_upload_bits('garda-frigor-logo.jpg', null, file_get_contents($logo_file));

    if (!empty($upload['error'])) {
        WP_CLI::error('Logo upload failed: ' . $upload['error']);
    }

    $logo_id = wp_insert_attachment([
        'post_mime_type' => 'image/jpeg',
        'post_title' => 'Garda Frigor logo',
        'post_content' => '',
        'post_status' => 'inherit',
    ], $upload['file']);

    require_once ABSPATH . 'wp-admin/includes/image.php';
    wp_update_attachment_metadata($logo_id, wp_generate_attachment_metadata($logo_id, $upload['file']));
    update_post_meta($logo_id, '_wp_attachment_image_alt', 'Garda Frigor');
} else {
    $logo_id = (int) $logo_attachment->ID;
}

$logo_url = wp_get_attachment_url($logo_id);
$options = get_option('fusion_options', []);
$logo = [
    'url' => $logo_url,
    'id' => (string) $logo_id,
    'height' => '82',
    'width' => '273',
    'thumbnail' => $logo_url,
];

foreach (['logo', 'logo_retina', 'sticky_header_logo', 'sticky_header_logo_retina', 'mobile_logo', 'mobile_logo_retina'] as $logo_key) {
    $options[$logo_key] = $logo;
}

$header_logo_replacements = [
    'http://localhost:8080/wp-content/uploads/2022/05/avada-energy-logo-dark@2x.png' => $logo_url,
    'http://localhost:8080/wp-content/uploads/2022/05/avada-energy-mobile-logo.svg' => $logo_url,
    'https://avada.website/energy/wp-content/uploads/sites/164/2022/05/avada-energy-logo-light.png' => $logo_url,
    'https://avada.website/energy/wp-content/uploads/sites/164/2022/05/avada-energy-logo-light@2x.png' => $logo_url,
    'alt="Avada Energy"' => 'alt="Garda Frigor"',
    'alt="Avada ECO"' => 'alt="Garda Frigor"',
    'image_id="731|full"' => 'image_id="' . $logo_id . '|full"',
    'image_id="732|full"' => 'image_id="' . $logo_id . '|full"',
    'image_id="733|full"' => 'image_id="' . $logo_id . '|full"',
    'image_id="860|full"' => 'image_id="' . $logo_id . '|full"',
    'image_id=""' => 'image_id="' . $logo_id . '|full"',
    'max_width="166px"' => 'max_width="273px"',
    'max_width="32px"' => 'max_width="136px"',
];

$header_sections = get_posts([
    'post_type' => 'fusion_tb_section',
    'post_status' => ['publish', 'draft'],
    'numberposts' => -1,
    's' => 'Global Header',
]);

foreach ($header_sections as $section) {
    $content = strtr($section->post_content, $header_logo_replacements);
    if ($content !== $section->post_content) {
        wp_update_post([
            'ID' => $section->ID,
            'post_content' => $content,
        ]);
    }
}

$options['header_number'] = '0365 522645';
$options['header_email'] = 'info@gardafrigor.it';
$options['header_tagline'] = 'Cawipa Elise per Garda Frigor';
$options['footer_text'] = 'Copyright ' . gmdate('Y') . ' Garda Frigor Srl | Progetto WordPress custom by Cawipa Elise';
$options['primary_color'] = '#3f87c8';
$options['header_top_bg_color'] = '#7cc9df';
$options['header_bg_color'] = '#ffffff';
$options['header_sticky_bg_color'] = '#ffffff';
$options['menu_hover_first_color'] = '#f58220';
$options['menu_highlight_background'] = '#f58220';
$options['button_gradient_top_color'] = '#3f87c8';
$options['button_gradient_bottom_color'] = '#1f4f92';
$options['button_gradient_top_color_hover'] = '#f58220';
$options['button_gradient_bottom_color_hover'] = '#f58220';
$options['button_accent_color'] = '#ffffff';
$options['button_accent_hover_color'] = '#ffffff';
$options['color_palette']['color2']['color'] = '#eef9fd';
$options['color_palette']['color3']['color'] = '#f6d51f';
$options['color_palette']['color4']['color'] = '#7cc9df';
$options['color_palette']['color5']['color'] = '#3f87c8';
$options['color_palette']['color6']['color'] = '#1f4f92';
$options['color_palette']['color7']['color'] = '#17212b';
$options['color_palette']['color8']['color'] = '#111820';

update_option('fusion_options', $options);

do_action('fusion_cache_reset_all');
flush_rewrite_rules();

WP_CLI::success('Garda Frigor post-process complete.');
