<?php
/** Direct native submission-service checks; no browser or email-delivery claim. */
if(getenv('PRIME_DISPOSABLE_TEST_DB')!=='yes'||empty($argv[1]))throw new RuntimeException('Disposable local database required.');
$_SERVER['HTTP_HOST']='127.0.0.1:4173';$_SERVER['REQUEST_URI']='/contact/';$_SERVER['REMOTE_ADDR']='127.0.0.1';$_SERVER['HTTP_USER_AGENT']='Prime Local QA';require $argv[1];
if(wp_get_environment_type()!=='local'||get_option('blog_public'))throw new RuntimeException('Only noindex local QA.');
$f=wpFluent()->table('fluentform_forms')->where('title','Prime — Native contact')->first();
$notifications=wpFluent()->table('fluentform_form_meta')->where('form_id',$f->id)->where('meta_key','notifications')->first();
if(!$notifications||json_decode($notifications->value,true))throw new RuntimeException('Notifications must be disabled.');
$honeypot=(new FluentForm\App\Modules\Form\HoneyPot(FluentForm\Framework\Foundation\App::getInstance()))->getConversationalHoneypotInput($f->id);
$blank=array_fill_keys(['name','phone','email','suburb','notes','consent'],'')+$honeypot;
$checks=[];$before=wpFluent()->table('fluentform_submissions')->where('form_id',$f->id)->count();
try{(new FluentForm\App\Services\Form\SubmissionHandlerService())->handleSubmission($blank,$f->id);$checks['blank_rejected']=false;}catch(FluentForm\Framework\Validator\ValidationException $e){$checks['blank_rejected']=true;}
$checks['blank_not_saved']=wpFluent()->table('fluentform_submissions')->where('form_id',$f->id)->count()===$before;
$data=['name'=>'Synthetic QA enquiry','phone'=>'0400000000','email'=>'qa@example.invalid','suburb'=>'Malvern East','notes'=>'Synthetic local validation only. Do not contact.','consent'=>'on']+$honeypot;
$result=(new FluentForm\App\Services\Form\SubmissionHandlerService())->handleSubmission($data,$f->id);
$checks['one_entry_saved']=wpFluent()->table('fluentform_submissions')->where('form_id',$f->id)->count()===$before+1;
$entry=wpFluent()->table('fluentform_submissions')->where('form_id',$f->id)->orderBy('id','desc')->first();$stored=json_decode($entry->response,true);
$checks['submitted_fields_preserved']=$stored['email']===$data['email']&&$stored['notes']===$data['notes'];
$checks['confirmation_has_no_review_request']=!preg_match('/review us|g\.page|google\.com\/.*review/i',wp_json_encode($result));
echo wp_json_encode(['type'=>'direct native PHP submission service; not browser/SMTP','checks'=>$checks,'external_notifications_enabled'=>false],JSON_PRETTY_PRINT).PHP_EOL;
exit(in_array(false,$checks,true)?1:0);
