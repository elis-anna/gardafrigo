<?php
/**
 * Project bootstrap helpers for the Garda Frigor local build.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', function () {
    register_post_type('solution', [
        'labels' => [
            'name' => __('Solutions', 'gardafrigo-cawipa'),
            'singular_name' => __('Solution', 'gardafrigo-cawipa'),
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-admin-tools',
        'rewrite' => ['slug' => 'solutions'],
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
        'show_in_rest' => true,
    ]);
});

add_action('gardafrigo_seed_content', 'gardafrigo_project_seed_content');

function gardafrigo_project_seed_content(): void
{
    if (get_option('gardafrigo_seeded_v1')) {
        return;
    }

    $pages = [
        'Home' => gardafrigo_project_home_content(),
        'Chi siamo' => '<p>Dal 1993 Garda Frigor lavora nell installazione e manutenzione di impianti di condizionamento, riscaldamento, trattamento aria e refrigerazione per clienti industriali, commerciali e residenziali.</p>',
        'Servizi' => '<p>Consulenza, installazioni personalizzate, assistenza e programmi di manutenzione su misura.</p>',
        'Marchi' => '<p>Fornitori autorizzati dei migliori brand del settore climatizzazione, riscaldamento, trattamento aria e refrigerazione.</p>',
        'Case history' => '<p>Area pronta per importare i progetti realizzati e le referenze dal sito esistente.</p>',
        'News' => '<p>Area pronta per migrare comunicazioni, aggiornamenti e archivio news.</p>',
        'Contatti' => '<p>Garda Frigor Srl, Via Fibbia 7, 25089 Villanuova sul Clisi (Brescia). Tel. 0365 522645. Email info@gardafrigor.it.</p>',
    ];

    $page_ids = [];
    foreach ($pages as $title => $content) {
        $existing = get_page_by_title($title);
        if ($existing) {
            $page_ids[$title] = (int) $existing->ID;
            continue;
        }

        $page_ids[$title] = wp_insert_post([
            'post_type' => 'page',
            'post_title' => $title,
            'post_name' => sanitize_title($title),
            'post_status' => 'publish',
            'post_content' => $content,
        ]);
    }

    if (!empty($page_ids['Home'])) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $page_ids['Home']);
    }

    gardafrigo_project_seed_solutions();
    gardafrigo_project_seed_menu($page_ids);
    update_option('gardafrigo_seeded_v1', time());
}

function gardafrigo_project_seed_solutions(): void
{
    $solutions = [
        'Condizionamento' => 'Installazione e manutenzione di impianti di condizionamento per aziende, negozi, uffici e abitazioni.',
        'Riscaldamento' => 'Soluzioni per impianti di riscaldamento efficienti, seguite dalla consulenza alla manutenzione.',
        'Trattamento aria' => 'Impianti e servizi per qualita dell aria, comfort interno e gestione professionale degli ambienti.',
        'Refrigerazione' => 'Installazione, assistenza e manutenzione di sistemi di refrigerazione per attivita commerciali e industriali.',
    ];

    foreach ($solutions as $title => $excerpt) {
        if (get_page_by_title($title, OBJECT, 'solution')) {
            continue;
        }

        wp_insert_post([
            'post_type' => 'solution',
            'post_title' => $title,
            'post_status' => 'publish',
            'post_excerpt' => $excerpt,
            'post_content' => '<p>' . esc_html($excerpt) . '</p><p>Contenuto pronto per essere impaginato nel layout Avada Energy.</p>',
        ]);
    }
}

function gardafrigo_project_seed_menu(array $page_ids): void
{
    $menu_name = 'Navigazione principale';
    $menu = wp_get_nav_menu_object($menu_name);
    $menu_id = $menu ? (int) $menu->term_id : wp_create_nav_menu($menu_name);

    foreach (['Home', 'Chi siamo', 'Servizi', 'Marchi', 'Case history', 'News', 'Contatti'] as $label) {
        if (empty($page_ids[$label])) {
            continue;
        }

        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title' => $label,
            'menu-item-object' => 'page',
            'menu-item-object-id' => $page_ids[$label],
            'menu-item-type' => 'post_type',
            'menu-item-status' => 'publish',
        ]);
    }

    $locations = get_theme_mod('nav_menu_locations', []);
    $locations['main_navigation'] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);
}

function gardafrigo_project_home_content(): string
{
    return <<<HTML
<!-- wp:group {"className":"gf-hero"} -->
<div class="wp-block-group gf-hero">
<p class="gf-kicker">Cawipa Elise per Garda Frigor</p>
<h1>Impianti di climatizzazione, riscaldamento e trattamento aria</h1>
<p>Dal 1993 Garda Frigor e' il partner ideale nella gestione degli impianti per aziende, attivita commerciali e famiglie nelle province di Brescia, Mantova, Bergamo, Verona e Trento.</p>
</div>
<!-- /wp:group -->

<!-- wp:heading -->
<h2>Solutions</h2>
<!-- /wp:heading -->

<!-- wp:html -->
<div class="gf-solution-grid">
  <article class="gf-solution-card"><h3>Condizionamento</h3><p>Installazione e manutenzione impianti di condizionamento.</p></article>
  <article class="gf-solution-card"><h3>Riscaldamento</h3><p>Installazione e manutenzione impianti di riscaldamento.</p></article>
  <article class="gf-solution-card"><h3>Trattamento aria</h3><p>Installazione e manutenzione impianti di trattamento aria.</p></article>
  <article class="gf-solution-card"><h3>Refrigerazione</h3><p>Installazione e manutenzione impianti di refrigerazione.</p></article>
</div>
<!-- /wp:html -->

<!-- wp:group {"className":"gf-cta-band"} -->
<div class="wp-block-group gf-cta-band">
<h2>Assistenza e manutenzione</h2>
<p>Programmi su misura per mantenere il migliore grado di efficienza degli impianti. Tel. 0365 522645.</p>
</div>
<!-- /wp:group -->
HTML;
}
