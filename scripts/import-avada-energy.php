<?php
/**
 * Import the Avada Energy prebuilt website through WP-CLI.
 *
 * Run with:
 * docker compose run --rm wpcli wp eval-file /scripts/import-avada-energy.php
 */

if (!defined('WP_CLI') || !WP_CLI) {
    fwrite(STDERR, "This script must run through WP-CLI.\n");
    exit(1);
}

$demo_type = 'energy';
$import_stages = avada_get_demo_import_stages();
$demos = Avada_Importer_Data::get_data();

if (empty($demos[$demo_type])) {
    WP_CLI::error("Avada demo not found: {$demo_type}");
}

$demo_details = $demos[$demo_type];
$demo_details['plugin_dependencies'] = $demo_details['plugin_dependencies'] ?? [];
$demo_details['plugin_dependencies']['fusion-core'] = true;
$demo_details['plugin_dependencies']['fusion-builder'] = true;

$demo_import_stages = ['download'];
$demo_content_types = [];

foreach ($import_stages as $import_stage) {
    if (!empty($import_stage['plugin_dependency']) && empty($demo_details['plugin_dependencies'][$import_stage['plugin_dependency']])) {
        continue;
    }

    if (!empty($import_stage['feature_dependency']) && !in_array($import_stage['feature_dependency'], $demo_details['features'], true)) {
        continue;
    }

    if (isset($import_stage['data']) && 'content' === $import_stage['data']) {
        $demo_content_types[] = $import_stage['value'];
        if (!in_array('content', $demo_import_stages, true)) {
            $demo_import_stages[] = 'content';
        }
        continue;
    }

    $demo_import_stages[] = $import_stage['value'];
}

$demo_import_stages[] = 'general_data';

$args = [
    'importStages' => $demo_import_stages,
    'demoType' => $demo_type,
    'fetchAttachments' => true,
    'contentTypes' => $demo_content_types,
    'allImport' => true,
];

if (!class_exists('Avada_Demo_Import')) {
    include Avada::$template_dir_path . '/includes/importer/importer.php';
}

$avada_import = new Avada_Demo_Import();

foreach ($demo_import_stages as $import_stage) {
    WP_CLI::log('Processing: ' . $import_stage);
    $avada_import->import_demo_stage($args);
    array_shift($args['importStages']);
}

WP_CLI::success('Avada Energy imported.');
