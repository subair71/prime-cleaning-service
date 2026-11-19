<?php
if (!defined('ABSPATH')) {
    exit;
}

use FluentForm\App\Models\Form;

function get_fluentform_export($formIds) {
    $result = Form::with(['formMeta'])
        ->whereIn('id', $formIds)
        ->get();
    $forms = [];
    foreach ($result as $item) {
        $form = json_decode($item);
        $formMetaFiltered = array_filter($form->form_meta, function ($m) {
            return ($m->meta_key !== '_total_views');
        });
        $form->metas = array_values($formMetaFiltered);
        $form->form_fields = json_decode($form->form_fields);
        $forms[] = $form;
    }
    return $forms;
}

$export_dir = dirname(__DIR__) . '/docs/native-migration/exports';

// 1. Export Form 3 (Contact)
$f3 = get_fluentform_export([3]);
if (!empty($f3)) {
    $f3_path = $export_dir . '/fluentforms-contact.json';
    file_put_contents($f3_path, json_encode($f3, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "SUCCESS: Exported Form 3 to $f3_path\n";
}

// 2. Export Form 4 (Quote Enquiry)
$f4 = get_fluentform_export([4]);
if (!empty($f4)) {
    $f4_path = $export_dir . '/fluentforms-quote-enquiry.json';
    file_put_contents($f4_path, json_encode($f4, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "SUCCESS: Exported Form 4 to $f4_path\n";
}

// 3. Export all active forms (3 & 4)
$all = get_fluentform_export([3, 4]);
if (!empty($all)) {
    $all_path = $export_dir . '/fluentforms-forms.json';
    file_put_contents($all_path, json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "SUCCESS: Exported Forms 3 & 4 to $all_path\n";
}

// 4. Update prices.html to match database post 12 content
$prices_page = get_post(12);
if ($prices_page) {
    $prices_html_file = dirname(__DIR__) . '/docs/native-migration/pages/prices.html';
    file_put_contents($prices_html_file, $prices_page->post_content);
    echo "SUCCESS: Updated docs/native-migration/pages/prices.html with latest post 12 content.\n";
}
