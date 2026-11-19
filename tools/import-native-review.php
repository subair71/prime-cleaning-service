<?php
/**
 * ONE-TIME OFFLINE REVIEW IMPORT. Never install in wp-content or call from HTTP.
 * wp eval-file tools/import-native-review.php
 * Requires PRIME_NATIVE_REVIEW=1, a fresh noindex local/staging WordPress,
 * Kadence, Kadence Blocks and Fluent Forms. Imports the quote estimator when Fluent Forms Pro is active.
 */
if (PHP_SAPI !== 'cli' || !defined('ABSPATH') || getenv('PRIME_NATIVE_REVIEW') !== '1') {
    throw new RuntimeException('Run via WP-CLI with PRIME_NATIVE_REVIEW=1 on a fresh review installation.');
}
if (!in_array(wp_get_environment_type(), ['local', 'staging'], true) || (int)get_option('blog_public') !== 0) {
    throw new RuntimeException('Only an isolated noindex local/staging review is supported.');
}
if (get_option('prime_native_review_imported')) throw new RuntimeException('Already imported. Existing edits will not be overwritten.');
foreach (get_posts(['post_type'=>['page','post','attachment','wp_block'], 'post_status'=>'any', 'numberposts'=>-1]) as $post) {
    if (!in_array($post->post_name, ['sample-page','privacy-policy','hello-world'], true)) {
        throw new RuntimeException('Existing content found. Use the existing-site migration guide, not this fresh-review importer.');
    }
}
if (!wp_get_theme('kadence')->exists() || !defined('KADENCE_BLOCKS_VERSION') || !defined('FLUENTFORM_VERSION')) {
    throw new RuntimeException('Install and activate the native dependencies first.');
}
if (wpFluent()->table('fluentform_submissions')->count() || wpFluent()->table('fluentform_forms')->whereNotIn('title', ['Contact Form Demo','Subscription Form'])->count()) {
    throw new RuntimeException('Existing forms or entries found; refusing to modify this installation.');
}
if (in_array('prime-cleaning-estimator/prime-cleaning-estimator.php', get_option('active_plugins', []), true)) {
    throw new RuntimeException('The custom estimator must not be active on this isolated review.');
}
if (!class_exists('DSMobileMenu\\Ajax\\MenuItemsAjax')) throw new RuntimeException('Activate DS Mobile Menu before importing this review.');
$root=dirname(__DIR__).'/docs/native-migration';
$read=fn($file)=>json_decode(file_get_contents($root.'/exports/'.$file), true, 512, JSON_THROW_ON_ERROR);
$pages=$read('page-manifest.json');$media=$read('media-manifest.json');$source='http://127.0.0.1:4173';
require_once ABSPATH.'wp-admin/includes/file.php';require_once ABSPATH.'wp-admin/includes/media.php';require_once ABSPATH.'wp-admin/includes/image.php';
$replace=[$source=>untrailingslashit(home_url())];$image_ids=[];$site_logo=0;
foreach ($media as $key=>$m) {
    $tmp=wp_tempnam($m['file']);copy($root.'/exports/media/'.$m['file'],$tmp);
    $id=media_handle_sideload(['name'=>$m['file'],'tmp_name'=>$tmp],0,'Prime '.$key.' interior');
    if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
    if(isset($m['url']))$replace[$m['url']]=wp_get_attachment_url($id);
    if(isset($m['id']))$image_ids[$m['id']]=$id;
    if(($m['role']??'')==='site-logo')$site_logo=$id;
    update_post_meta($id,'_wp_attachment_image_alt',$m['alt']??'');
}
// Import through the plugin's own supported export/import service, not custom field handling.
$import_form = function (string $filename) use ($read, &$replace): int {
    $forms = $read($filename);
    // Keep the business recipients in the portable export, and mute them in disposable reviews.
    foreach ($forms[0]['metas'] as &$meta) {
        if ($meta['meta_key'] === 'notifications') {
            $settings = json_decode($meta['value'], true, 512, JSON_THROW_ON_ERROR);
            if ($settings) {
                $settings['enabled'] = false;
                $meta['value'] = wp_json_encode($settings);
            }
        }
    }
    unset($meta);
    $tmp = wp_tempnam($filename);
    try {
        $payload = strtr(wp_json_encode($forms, JSON_UNESCAPED_SLASHES), $replace);
        if (file_put_contents($tmp, $payload) === false) {
            throw new RuntimeException("Could not prepare $filename");
        }
        $file = new FluentForm\Framework\Http\Request\File($tmp, $filename, 'application/json');
        $result = FluentForm\App\Services\Transfer\TransferService::importForms($file);
        $form_id = (int) array_key_first($result['inserted_forms']);
        if (!$form_id) {
            throw new RuntimeException("Could not import $filename");
        }
        return $form_id;
    } finally {
        wp_delete_file($tmp);
    }
};
$form_id = $import_form('fluentforms-contact.json');
$source_contact_id = $read('fluentforms-contact.json')[0]['id'];
$replace['[fluentform id="' . $source_contact_id . '"]'] = '[fluentform id="' . $form_id . '"]';
$quote_form_id = null;
if (defined('FLUENTFORMPRO_VERSION')) {
    $quote_form_id = $import_form('fluentforms-quote.json');
} else {
    // The existing call and WhatsApp buttons below this block still work in a Free-only review.
    $replace["<!-- wp:shortcode -->\n" . '[fluentform title="Prime | Request a Quote"]' . "\n<!-- /wp:shortcode -->"] =
        '<!-- wp:paragraph --><p>Please call or message us for a personalised quote.</p><!-- /wp:paragraph -->';
}
$rewrite=function($content)use($replace,$image_ids){
    $content=strtr($content,$replace);
    $content=preg_replace_callback('/"id":(\d+)/',fn($m)=>'"id":'.($image_ids[(int)$m[1]]??$m[1]),$content);
    return preg_replace_callback('/wp-image-(\d+)/',fn($m)=>'wp-image-'.($image_ids[(int)$m[1]]??$m[1]),$content);
};
$ids=[];
foreach($pages as $page){
    $old=get_page_by_path($page['slug']);
    $id=wp_insert_post(wp_slash(['ID'=>$old?$old->ID:0,'post_type'=>'page','post_name'=>$page['slug'],'post_title'=>$page['title'],'post_status'=>$page['status'],'post_content'=>$rewrite(file_get_contents($root.'/'.$page['file']))]),true);
    if(is_wp_error($id))throw new RuntimeException($id->get_error_message());$ids[$page['key']]=$id;
}
foreach(['sample-page','hello-world','privacy-policy'] as $slug){$post=get_page_by_path($slug,OBJECT,['post','page']);if($post)wp_update_post(['ID'=>$post->ID,'post_status'=>'draft']);}
switch_theme('kadence');
foreach($read('theme-mods-kadence.json') as $key=>$value){if(in_array($key,['custom_css_post_id','nav_menu_locations','sidebars_widgets'],true))continue;set_theme_mod($key,json_decode(strtr(wp_json_encode($value,JSON_UNESCAPED_SLASHES),$replace),true));}
if($site_logo)set_theme_mod('custom_logo',$site_logo);
update_option('kadence_global_palette',file_get_contents($root.'/exports/kadence-global-palette.json'));
// One-time application of the plugin's native export through its own sanitizers.
$bar=json_decode(strtr(wp_json_encode($read('mobile-actions.json'),JSON_UNESCAPED_SLASHES),$replace),true);
$bar_sanitize=new DSMobileMenu\Ajax\MenuItemsAjax();
update_option('dsmm_active_layout',$bar['dsmm_active_layout']);
update_option('dsmm_menu_items',$bar_sanitize->sanitize_items($bar['dsmm_menu_items']));
update_option('dsmm_visibility_rules',$bar_sanitize->sanitize_visibility_rules($bar['dsmm_visibility_rules']));
update_option('dsmm_general_settings',$bar_sanitize->sanitize_general_settings($bar['dsmm_general_settings']));
$style_sanitize=new DSMobileMenu\Ajax\StyleAjax(new DSMobileMenu\Core\StyleGenerator());
update_option('dsmm_style_settings',$style_sanitize->sanitize($bar['dsmm_style_settings']));
update_option('dsmm_wc_settings',$bar['dsmm_wc_settings']);
(new DSMobileMenu\Core\StyleGenerator())->generate();
$widgets=$read('widget-block.json');foreach($widgets as &$widget)if(is_array($widget)&&isset($widget['content']))$widget['content']=$rewrite($widget['content']);unset($widget);
update_option('widget_block',$widgets);wp_set_sidebars_widgets($read('sidebars-widgets.json'));
$menu=wp_create_nav_menu('Prime main navigation');if(is_wp_error($menu))throw new RuntimeException($menu->get_error_message());
$add=function($key,$label,$parent=0)use($menu,$ids){return wp_update_nav_menu_item($menu,0,['menu-item-title'=>$label,'menu-item-object'=>'page','menu-item-object-id'=>$ids[$key],'menu-item-type'=>'post_type','menu-item-status'=>'publish','menu-item-parent-id'=>$parent]);};
$add('home','Home');$parent=wp_update_nav_menu_item($menu,0,['menu-item-title'=>'Services','menu-item-type'=>'custom','menu-item-url'=>'#','menu-item-status'=>'publish']);
foreach(['regular'=>'Regular & Deep Cleaning','lease'=>'End of Lease Cleaning','carpet'=>'Carpet Steam Cleaning'] as $key=>$label)$add($key,$label,$parent);
foreach(['prices'=>'Prices','about'=>'About','contact'=>'Contact'] as $key=>$label)$add($key,$label);
set_theme_mod('nav_menu_locations',['primary'=>$menu,'mobile'=>$menu]);
foreach(['blogname'=>'Prime Domestic','blogdescription'=>'& Cleaning Services','show_on_front'=>'page','page_on_front'=>$ids['home'],'wp_page_for_privacy_policy'=>$ids['privacy'],'timezone_string'=>'Australia/Melbourne','permalink_structure'=>'/%postname%/','_fluentform_global_form_settings'=>['misc'=>['honeypotStatus'=>'yes']]] as $key=>$value)update_option($key,$value);
update_option('prime_native_review_imported',['pages'=>$ids,'contact_form'=>$form_id,'quote_form'=>$quote_form_id,'date'=>gmdate('c')],false);flush_rewrite_rules(false);
echo 'Imported ' . count($ids) . ' pages and contact form ' . $form_id . '. ';
echo $quote_form_id ? 'Quote estimator ' . $quote_form_id . ' imported. ' : 'Quote page uses the phone and WhatsApp fallback. ';
echo 'Review notifications are disabled.' . PHP_EOL;
