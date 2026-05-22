<?php
/**
 * Garda Frigor Cawipa child theme setup.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'gardafrigo-cawipa',
        get_stylesheet_uri(),
        [],
        wp_get_theme()->get('Version')
    );
});

add_action('init', function () {
    register_post_type('solution', [
        'labels' => [
            'name' => __('Solutions', 'gardafrigo-cawipa'),
            'singular_name' => __('Solution', 'gardafrigo-cawipa'),
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-admin-tools',
        'rewrite' => ['slug' => 'solution-items'],
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
        'show_in_rest' => true,
    ]);
});

add_filter('upload_mimes', function ($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
});
