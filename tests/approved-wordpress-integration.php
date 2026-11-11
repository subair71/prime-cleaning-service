<?php
// Run only against a disposable WordPress test database; creates labelled test enquiries.
if(getenv('PRIME_DISPOSABLE_TEST_DB')!=='yes' || !getenv('PRIME_TEST_WP_ROOT')) { fwrite(STDERR,"Set PRIME_DISPOSABLE_TEST_DB=yes and PRIME_TEST_WP_ROOT to a disposable installation.\n"); exit(1); }
$_SERVER['HTTP_HOST']='127.0.0.1:4173';$_SERVER['REQUEST_URI']='/';define('DOING_AJAX',true);require rtrim(getenv('PRIME_TEST_WP_ROOT'),'/').'/wp-load.php';
add_filter('wp_die_ajax_handler',function(){return function(){throw new RuntimeException('ajax_done');};});
add_filter('pre_wp_mail',function(){return false;});
$count=0;function ok($label,$condition){global $count;$count++;if(!$condition){echo 'FAIL '.$label."\n";exit(1);}}
function call_flow($action,$payload){$_REQUEST=['nonce'=>wp_create_nonce('pce_v2')];$_POST=['nonce'=>$_REQUEST['nonce'],'payload'=>wp_slash(wp_json_encode($payload))];ob_start();try{PCE_Flow::$action();}catch(RuntimeException $e){}$raw=ob_get_clean();return json_decode($raw,true);}
$original=get_option(PCE_Approved::OPTION,null);$s=PCE_Approved::defaults();$s['grids']['regular']['2:1']=200;$s['grids']['lease']['2:1']=400;$s['minimum']=100;$s['suburbs']=['qa-test'=>['name'=>'QA test suburb','postcode'=>'3000','zone'=>1]];$s['email']='owner@example.invalid';$s['sender_email']='sender@example.invalid';update_option(PCE_Approved::OPTION,$s);
$p=['service'=>'regular','bedrooms'=>2,'bathrooms'=>1,'property'=>'double','frequency'=>'weekly','suburb'=>'qa-test'];$q=call_flow('quote',$p);ok('quote endpoint',$q['success'] && $q['data']['quote']['total']===195.5);
$contact=['name'=>'Isolated QA','phone'=>'0400000000','email'=>'qa@example.invalid','preferred_date'=>wp_date('Y-m-d',time()+7*DAY_IN_SECONDS),'parking'=>'discuss','consent'=>true,'quote_token'=>$q['data']['token'],'request_key'=>wp_generate_uuid4(),'additional_dates'=>'2 December','time_preference'=>'morning','pets'=>'no','condition'=>'maintained','referral'=>'QA','notes'=>'Integration fixture'];
$invalid=call_flow('submit',array_replace($contact,['preferred_date'=>'2026-02-30','consent'=>false]));ok('strict date and consent validation',!$invalid['success'] && isset($invalid['data']['errors']['preferred_date'],$invalid['data']['errors']['consent']));
$invalid=call_flow('submit',array_replace($contact,['parking'=>'']));ok('parking required',!$invalid['success']);
$s['grids']['regular']['2:1']=900;update_option(PCE_Approved::OPTION,$s);
$a=call_flow('submit',$contact);ok('save success despite mail failure',$a['success']);$b=call_flow('submit',$contact);ok('idempotent retry',$b['success'] && $a['data']['reference']===$b['data']['reference']);
global $wpdb;$table=PCE_DB::get_table_name();$id=(int)substr($a['data']['reference'],4)-1000;$row=PCE_DB::get_enquiry($id);$snapshot=json_decode($row['estimate_breakdown'],true);ok('frozen displayed quote retained after price edit',$snapshot['total']===195.5);ok('mail failure retained with record',!$row['email_sent'] && !empty($row['email_error']));ok('optional fields frozen',json_decode($row['property_details'],true)['enquiry']['referral']==='QA');
$contact['request_key']=wp_generate_uuid4();$c=call_flow('submit',$contact);ok('actual sequential references',(int)substr($c['data']['reference'],4)===(int)substr($a['data']['reference'],4)+1);
$contact['quote_token']='expired';$contact['request_key']=wp_generate_uuid4();$expired=call_flow('submit',$contact);ok('expired quote recovery',!$expired['success'] && $expired['data']['expired']);
$contact['quote_token']=$q['data']['token'];$contact['request_key']=wp_generate_uuid4();$before=(int)$wpdb->get_var("SELECT COUNT(*) FROM $table");$fail=function($sql)use($table){return str_starts_with($sql,"INSERT INTO `$table`")?str_replace("`$table`","`missing_qa_table`",$sql):$sql;};add_filter('query',$fail);$failed=call_flow('submit',$contact);remove_filter('query',$fail);ok('database failure never returns success',!$failed['success']);ok('failed save creates no row',(int)$wpdb->get_var("SELECT COUNT(*) FROM $table")===$before);
$legacy=PCE_Settings::get_all();ok('legacy scalar settings preserved',isset($legacy['base_regular'],$legacy['office_hourly_rate']));ok('legacy office calculator preserved',PCE_Calculator::calculate(['service'=>'office','postcode'=>'3145','office_hours'=>2])['service']==='office');
$beforecontent=get_post(prime_page_object('about')->ID)->post_content;prime_install_approved_pages();ok('repeat installer preserves edited content',get_post(prime_page_object('about')->ID)->post_content===$beforecontent);
// Resolve the original hierarchical service route before installing layouts.
$mapped=get_option('prime_approved_page_ids',[]);$lease=prime_page_object('lease');$old_parent=$lease->post_parent;
$parent=get_page_by_path('services');$parent_id=$parent?$parent->ID:wp_insert_post(['post_type'=>'page','post_status'=>'draft','post_title'=>'QA Services','post_name'=>'services']);
wp_update_post(['ID'=>$lease->ID,'post_parent'=>$parent_id]);$without=$mapped;unset($without['lease']);update_option('prime_approved_page_ids',$without);
ok('hierarchical service lookup preserves page ID',prime_page_object('lease')->ID===$lease->ID);
$installed=prime_install_approved_pages();ok('installer reuses hierarchical service URL',$installed['lease']===$lease->ID && get_post($lease->ID)->post_parent===$parent_id);
wp_update_post(['ID'=>$lease->ID,'post_parent'=>$old_parent]);update_option('prime_approved_page_ids',$mapped);
// The additive migration must leave the already-saved row and its snapshot intact.
$before_row=PCE_DB::get_enquiry($id);update_option('pce_schema_version','1');PCE_Flow::migrate();ok('repeat additive migration preserves saved enquiry',PCE_DB::get_enquiry($id)===$before_row);
$s['whatsapp']='61400000000';update_option(PCE_Approved::OPTION,$s);$wa=PCE_Approved::whatsapp('PDC-1234');ok('WhatsApp action contains saved reference',str_contains($wa,'61400000000') && str_contains(urldecode($wa),'PDC-1234'));
ok('legacy estimator anchor retained',str_contains(PCE_Flow::render(),'id="pce-estimator-container"'));
if($original===null)delete_option(PCE_Approved::OPTION);else update_option(PCE_Approved::OPTION,$original);
echo "$count WordPress integration checks passed. Test records retained only in isolated runtime.\n";
