<?php
if(getenv('PRIME_DISPOSABLE_TEST_DB')!=='yes'||!getenv('PRIME_TEST_WP_ROOT'))exit('Disposable WordPress required.');
$_SERVER['HTTP_HOST']='127.0.0.1:4173';$_SERVER['REQUEST_URI']='/';define('DOING_AJAX',true);require getenv('PRIME_TEST_WP_ROOT').'/wp-load.php';
add_filter('wp_die_ajax_handler',function(){return function(){throw new RuntimeException('ajax_done');};});
$checks=0;function check_launch($label,$result){global $checks;$checks++;if(!$result){fwrite(STDERR,"FAIL $label\n");exit(1);}}
function launch_call($action,$p){$_REQUEST=['nonce'=>wp_create_nonce('pce_v2')];$_POST=['nonce'=>$_REQUEST['nonce'],'payload'=>wp_slash(wp_json_encode($p))];ob_start();try{PCE_Flow::$action();}catch(RuntimeException $e){}return json_decode(ob_get_clean(),true);}
$original=get_option(PCE_Approved::OPTION);$s=PCE_Approved::defaults();$s['sender_email']='sender@example.invalid';$s['email']='owner@example.invalid';$s['owner_email']='second@example.invalid';$s['grids']['regular']['2:1']=200;$s['suburbs']=['qa'=>['name'=>'QA fixture suburb','postcode'=>'3000','zone'=>1]];update_option(PCE_Approved::OPTION,$s);
$mail=[];add_filter('pre_wp_mail',function($pre,$args)use(&$mail){$mail[]=$args;return false;},100,2);
$input=['service'=>'regular','bedrooms'=>'2','bathrooms'=>'1','property'=>'single','suburb'=>'qa'];
$person=['name'=>'Review Tester','phone'=>'0400000000','email'=>'review@example.invalid','preferred_date'=>wp_date('Y-m-d',time()+7*DAY_IN_SECONDS),'parking'=>'discuss','consent'=>true,'notes'=>'Isolated November 12 QA'];
$q=launch_call('quote',$input);$a=launch_call('submit',array_merge($person,['quote_token'=>$q['data']['token'],'request_key'=>wp_generate_uuid4()]));
check_launch('normal confirmation shows saved range',$a['data']['range']===['low'=>180,'high'=>220]);
check_launch('normal email approved subject',str_starts_with($mail[1]['subject'],'Got it! Your enquiry PDC-'));
check_launch('normal email saved range',str_contains($mail[1]['message'],'AUD 180 to AUD 220'));
check_launch('owner recipients preserved',count($mail[0]['to'])===2);
$mail=[];$q=launch_call('quote',array_replace($input,['bedrooms'=>'6']));$a=launch_call('submit',array_merge($person,['quote_token'=>$q['data']['token'],'request_key'=>wp_generate_uuid4()]));
check_launch('custom confirmation null range',$a['data']['state']==='custom'&&$a['data']['range']===null);
check_launch('custom email has no AUD estimate',!str_contains($mail[1]['message'],'AUD')&&str_contains($mail[1]['message'],'proper look'));
check_launch('custom owner no zero or stale estimate',!preg_match('/AUD|"total"|"low"|"high"/',$mail[0]['message']));
check_launch('photo link exact reference message',str_contains(urldecode($a['data']['whatsapp']),'Hi Prime Domestic, my reference is '.$a['data']['reference'].'. Here are some photos of the place.'));
foreach(prime_pages_map() as $key=>$v){$content=prime_pattern($key);check_launch($key.' one H1',substr_count($content,'<h1 ')===1);if($key!=='office')check_launch($key.' no Lorem Ipsum',stripos($content,'lorem ipsum')===false);check_launch($key.' parseable native blocks',!empty(parse_blocks($content)));}
check_launch('office unpublished',prime_page_object('office')->post_status==='draft');check_launch('terms unpublished',prime_page_object('terms')->post_status==='draft');
check_launch('staging sitemap disabled',apply_filters('wp_sitemaps_enabled',true)===false);
check_launch('staging robots blocked',str_contains(apply_filters('robots_txt',''),'Disallow: /'));
check_launch('privacy 16 sections',count(json_decode(file_get_contents(get_stylesheet_directory().'/inc/prime-privacy.json'),true))===16);
check_launch('deep dynamic from',prime_price_from('deep')===300.0);
check_launch('missing carpet from remains custom',prime_price_from('carpet')===null);
$before=get_post(prime_page_object('about')->ID)->post_content;prime_install_approved_pages();check_launch('installer repeat preserves content',get_post(prime_page_object('about')->ID)->post_content===$before);check_launch('installer repeat preserves settings',get_option(PCE_Approved::OPTION)===$s);
update_option(PCE_Approved::OPTION,$original);echo "$checks November 12 WordPress checks passed. Mail intercepted; no delivery attempted.\n";
