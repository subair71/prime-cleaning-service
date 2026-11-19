<?php
/** Native migration checks. A disposable WordPress database is required. */
if(getenv('PRIME_DISPOSABLE_TEST_DB')!=='yes'||empty($argv[1]))throw new RuntimeException('Use PRIME_DISPOSABLE_TEST_DB=yes php tests/native-wordpress.php /path/to/wp-load.php');
$_SERVER['HTTP_HOST']='native-roundtrip.invalid';$_SERVER['REQUEST_URI']='/';require $argv[1];
wp_set_current_user(1);
if(wp_get_environment_type()!=='local'||get_option('blog_public'))throw new RuntimeException('Disposable local noindex site required.');
$checks=0;$fail=[];$check=function($label,$pass)use(&$checks,&$fail){$checks++;if(!$pass)$fail[]=$label;};
$check('parent Kadence active',get_option('stylesheet')==='kadence'&&get_option('template')==='kadence');
$check('custom classes absent',!class_exists('PCE_Approved')&&!class_exists('PCE_Fluent_Forms')&&!function_exists('prime_pattern'));
$check('only native plugins active',!array_diff(get_option('active_plugins'),['fluentform/fluentform.php','fluentformpro/fluentformpro.php','kadence-blocks/kadence-blocks.php','devstack-mobile-menu/devstack-mobile-menu.php']));
$check('no custom theme CSS',!wp_get_custom_css());
$check('supplied logo imported',has_custom_logo()&&get_post_meta(get_theme_mod('custom_logo'),'_wp_attachment_image_alt',true)==='Prime Domestic and Cleaning Services');
$check('desktop native sticky header',get_theme_mod('header_sticky')==='main');
$check('mobile header scrolls away',get_theme_mod('mobile_header_sticky')==='no');
$manifest=json_decode(file_get_contents(dirname(__DIR__).'/docs/native-migration/exports/page-manifest.json'),true);$contents=[];
foreach($manifest as $page){
 $p=get_page_by_path($page['slug']);$check($page['key'].' exists',(bool)$p);if(!$p)continue;
 $check($page['key'].' correct status',$p->post_status===$page['status']);
 $check($page['key'].' has no custom markup',!preg_match('/\[prime_|pp-|kadence-child|prime-cleaning-estimator|<!-- wp:html/',$p->post_content));
 $check($page['key'].' no old page template',!get_post_meta($p->ID,'_wp_page_template',true)||get_post_meta($p->ID,'_wp_page_template',true)==='default');
 $native=true;$walk=function($blocks)use(&$walk,&$native){foreach($blocks as $b){if($b['blockName']!==null&&!str_starts_with($b['blockName'],'core/')&&!in_array($b['blockName'],['kadence/rowlayout','kadence/column','kadence/advancedheading','kadence/icon','kadence/single-icon','kadence/advancedbtn','kadence/singlebtn'],true))$native=false;if($b['blockName']&&!WP_Block_Type_Registry::get_instance()->is_registered($b['blockName']))$native=false;$walk($b['innerBlocks']);}};$walk(parse_blocks($p->post_content));$check($page['key'].' registered native blocks',$native);
 $check($page['key'].' no stale source domain',home_url()==='http://127.0.0.1:4173'||!str_contains($p->post_content,'http://127.0.0.1:4173'));
 $contents[$page['key']]=$p->post_content;
}
$hero=serialize_block(parse_blocks($contents['home'])[0]);
$check('signed hero has no photograph',!str_contains($hero,'<img')&&str_contains($hero,'#f2f7fd'));
$bar=get_option('dsmm_menu_items');
$check('three labelled mobile actions',array_column($bar,'label')===['Call','WhatsApp','Estimate']);
$check('mobile action destinations',array_column($bar,'url')===['tel:+61491245156','https://wa.me/61491245156',home_url('/prices/#estimate')]);
$check('native safe area enabled',get_option('dsmm_style_settings')['safe_area_support']===true);
$check('bar limited to smaller screens',get_option('dsmm_visibility_rules')['hide_on_desktop']===true);
$check('approved service minimum displayed', str_contains($contents['prices'], 'End of Lease has no minimum'));
$check('unresolved bed making removed',!str_contains($contents['regular'],'beds made'));
$form=wpFluent()->table('fluentform_forms')->where('title','Prime — Native contact')->first();$check('native contact exists',(bool)$form);
$definition=json_decode($form->form_fields,true);$names=array_column(array_column($definition['fields'],'attributes'),'name');
$check('only native enquiry fields',$names===['name','phone','email','suburb','notes','consent']);
$check('no custom calculator or hidden bridge fields',!preg_match('/quote_token|request_key|prime_estimate|prime-ff/',$form->form_fields));
$metas=wpFluent()->table('fluentform_form_meta')->where('form_id',$form->id)->get();
$custom=false;$notifications=false;foreach($metas as $m){if(in_array($m->meta_key,['_custom_form_css','_custom_form_js'],true)&&trim($m->value))$custom=true;if($m->meta_key==='notifications'&&json_decode($m->value,true))$notifications=true;}
$check('no custom form code',!$custom);$check('notifications disabled in QA',!$notifications);
$check('native honeypot configured',get_option('_fluentform_global_form_settings')['misc']['honeypotStatus']==='yes');
$menu=wp_get_nav_menu_items(get_theme_mod('nav_menu_locations')['primary']);$check('native navigation has eight items',count($menu)===8);
$draft_ids=array_map(fn($slug)=>get_page_by_path($slug)->ID,['office-cleaning','terms']);$check('drafts absent from menu',!array_intersect($draft_ids,array_column($menu,'object_id')));
$price = $contents['prices'];
$check('homepage quote link', str_contains($contents['home'], 'id="quote"') && str_contains($contents['home'], '#estimator-section') && str_contains($contents['home'], '"anchor":"estimator-section"'));
$check('prices quote anchors', str_contains($price, '"anchor":"estimate"') && str_contains($price, 'id="quote"'));
$quote = wpFluent()->table('fluentform_forms')->where('title', 'Prime | Request a Quote')->first();
if (defined('FLUENTFORMPRO_VERSION')) {
    $check('native quote form imported', (bool) $quote);
    $check('prices embeds estimator', str_contains($price, '[fluentform title="Prime | Request a Quote"]'));
    if ($quote) {
        $feeds = wpFluent()->table('fluentform_form_meta')->where('form_id', $quote->id)->where('meta_key', 'notifications')->get();
        $check('two review quote notifications configured', count($feeds) === 2);
        foreach ($feeds as $feed) {
            $check('review quote notification muted', json_decode($feed->value, true)['enabled'] === false);
        }
    }
} else {
    $check('free-only review offers a custom quote', str_contains($price, 'Please call or message us for a personalised quote.'));
    $check('free-only review omits Pro estimator', !$quote && !str_contains($price, '[fluentform'));
}
$office=get_page_by_path('office-cleaning');$original=$office->post_content;
try{$test=$original."\n<!-- wp:paragraph --><p>Native save roundtrip QA</p><!-- /wp:paragraph -->";wp_update_post(wp_slash(['ID'=>$office->ID,'post_content'=>$test]));clean_post_cache($office->ID);$check('native block edit persists',get_post($office->ID)->post_content===$test);}finally{wp_update_post(wp_slash(['ID'=>$office->ID,'post_content'=>$original]));}
$check('test edit restored',get_post($office->ID)->post_content===$original);
$check('import guard protects subsequent edits',(bool)get_option('prime_native_review_imported'));
if(!empty($argv[2]))file_put_contents($argv[2],wp_json_encode($contents));
echo json_encode(['checks'=>$checks,'failed'=>$fail,'wordpress'=>$wp_version,'kadence'=>wp_get_theme()->get('Version'),'kadence_blocks'=>KADENCE_BLOCKS_VERSION,'fluent_forms'=>FLUENTFORM_VERSION,'pro_available'=>defined('FLUENTFORMPRO_VERSION'),'ui_editing_tested'=>false],JSON_PRETTY_PRINT).PHP_EOL;
exit($fail?1:0);
