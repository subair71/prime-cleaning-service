<?php
/** Approved UI and server-authoritative enquiry workflow. */
defined('ABSPATH') || exit;
class PCE_Flow {
    static function init() {
        foreach(['quote','submit','contact'] as $action) foreach(['wp_ajax_','wp_ajax_nopriv_'] as $prefix) add_action($prefix.'pce_v2_'.$action,[__CLASS__,$action]);
        add_shortcode('prime_contact_form',[__CLASS__,'contact_form']);
        add_action('init',[__CLASS__,'migrate']);
    }
    static function migrate() {
        if(get_option('pce_schema_version')==='2') return;
        PCE_DB::create_tables();
        global $wpdb; $table=PCE_DB::get_table_name();
        $column=$wpdb->get_var("SHOW COLUMNS FROM $table LIKE 'request_key'");
        if(!$column) $wpdb->query("ALTER TABLE $table ADD request_key varchar(64) DEFAULT NULL, ADD UNIQUE KEY request_key (request_key)");
        if($wpdb->get_var("SHOW COLUMNS FROM $table LIKE 'request_key'")) update_option('pce_schema_version','2');
    }
    static function assets() {
        wp_enqueue_style('pce-approved',plugins_url('assets/css/approved.css',PCE_PLUGIN_FILE),[],PCE_VERSION);
        wp_enqueue_script('pce-approved',plugins_url('assets/js/approved.js',PCE_PLUGIN_FILE),[],PCE_VERSION,true);
        wp_localize_script('pce-approved','primeEstimate',['url'=>admin_url('admin-ajax.php'),'nonce'=>wp_create_nonce('pce_v2'),'today'=>wp_date('Y-m-d'),'suburbs'=>PCE_Approved::settings()['suburbs'],'whatsapp'=>PCE_Approved::whatsapp()]);
    }
    static function service_icon($service) {
        $paths=[
            'lease'=>'<path d="m3 11 9-8 9 8M5 9v12h5v-7h4v7h5V9"/>',
            'regular'=>'<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4m10-4v4M3 10h18M7 14h2m3 0h2M7 17h2"/>',
            'deep'=>'<path d="m14 13 7-9a1.5 1.5 0 0 0-2-2l-8 9M5 12l6-1 4 4-1 6c-4 1-9-1-12-4l3-5Zm1 3-2 4m5-3-1 5m4-4v4"/>',
            'carpet'=>'<rect x="3" y="5" width="18" height="14" rx="1"/><path d="M6 9h12M6 12h12M6 15h12M5 3v2m4-2v2m6-2v2m4-2v2M5 19v2m4-2v2m6-2v2m4-2v2"/>',
        ];
        return '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($paths[$service]??'').'</svg>';
    }
    static function render() { self::assets();ob_start(); include PCE_PLUGIN_DIR.'templates/approved-estimator.php'; return ob_get_clean(); }
    static function contact_form() { self::assets();ob_start(); ?><form class="pe-contact pe-form" novalidate><div class="pe-errors" role="alert" hidden></div><?php self::contact_fields(true); ?><button class="pe-primary" type="submit">Send message</button><p class="pe-small">An enquiry is not a confirmed booking.</p><div class="pe-contact-success" role="status" hidden></div></form><?php return ob_get_clean(); }
    static function contact_fields($simple=false) {
        $fields=['name'=>['Full name','text',true,'name'],'phone'=>['Mobile number','tel',true,'tel'],'email'=>['Email address','email',true,'email']];
        if($simple) $fields['suburb_name']=['Suburb','text',true,'address-level2'];
        else { $fields['preferred_date']=['Preferred date','date',true,'off'];$fields['additional_dates']=['Additional preferred dates (optional)','text',false,'off']; }
        echo '<div class="pe-fields">';
        foreach($fields as $key=>$f) { echo '<label>'.$f[0].($f[2]?' <span aria-hidden="true">*</span>':'').'<input name="'.esc_attr($key).'" type="'.esc_attr($f[1]).'" autocomplete="'.esc_attr($f[3]).'" '.($f[2]?'required ':'').'maxlength="200"'.($f[1]==='date'?' min="'.esc_attr(wp_date('Y-m-d')).'"':'').'><span class="pe-field-error" data-error="'.esc_attr($key).'"></span></label>'; }
        if(!$simple) {
            echo '<label>Suburb (selected above)<input name="review_suburb" readonly aria-describedby="pe-suburb-review"><small id="pe-suburb-review">Use Back to change the suburb before sending.</small></label>';
            self::select('time_preference','Time preference (optional)',[''=>'No preference','morning'=>'Morning','afternoon'=>'Afternoon']);
            self::select('parking','Parking / access',[''=>'Please select','available'=>'Parking available','limited'=>'Limited parking','discuss'=>'Please discuss access'],true);
            self::select('condition','Property condition (optional)',[''=>'Please select','maintained'=>'Regularly maintained','heavy'=>'Needs extra attention','discuss'=>'Please discuss']);
            self::select('pets','Pets (optional)',[''=>'Please select','yes'=>'Yes','no'=>'No']);
            echo '<label>How did you hear about us? (optional)<input name="referral" maxlength="200"></label>';
        }
        echo '</div><label>'.($simple?'Your enquiry':'Notes (optional)').'<textarea name="notes" rows="4" maxlength="3000" '.($simple?'required':'').'></textarea><span class="pe-field-error" data-error="notes"></span></label>';
        echo '<div class="pe-honeypot" aria-hidden="true"><label>Leave empty<input name="website" tabindex="-1" autocomplete="off"></label></div><label class="pe-check"><input type="checkbox" name="consent" required> <span>I agree to Prime Domestic contacting me about this enquiry. See our <a href="'.esc_url(get_privacy_policy_url()?:home_url('/privacy-policy/')).'">Privacy Policy</a>.*</span></label><span class="pe-field-error" data-error="consent"></span>';
        if(!$simple) echo '<p class="pe-note">This is a request, not a confirmed booking. We’ll confirm by phone or message.</p>';
    }
    static function select($name,$label,$options,$required=false) { echo '<label>'.esc_html($label).($required?' *':'').'<select name="'.esc_attr($name).'" '.($required?'required':'').'>'; foreach($options as $v=>$text) echo '<option value="'.esc_attr($v).'">'.esc_html($text).'</option>';echo '</select><span class="pe-field-error" data-error="'.esc_attr($name).'"></span></label>'; }
    static function request() {
        if(!check_ajax_referer('pce_v2','nonce',false)) wp_send_json_error(['message'=>'This form has expired. Refresh the page and try again.'],403);
        $p=json_decode(wp_unslash($_POST['payload']??''),true);
        if(!is_array($p)) wp_send_json_error(['message'=>'Please check the form and try again.'],400);
        if(!empty($p['website'])) wp_send_json_error(['message'=>'Unable to send this enquiry.'],400);
        foreach($p as $key=>$value) if(!in_array($key,['extras','carpets'],true) && !is_scalar($value) && $value!==null) $p[$key]='';
        return $p;
    }
    static function quote() {
        $p=self::request();$r=PCE_Approved::calculate($p);
        if($r['state']==='invalid') wp_send_json_error(['message'=>'Please complete the highlighted fields.','errors'=>$r['errors']],422);
        $token=wp_generate_uuid4();set_transient('pce_quote_'.$token,$r,30*MINUTE_IN_SECONDS);
        wp_send_json_success(['quote'=>$r,'token'=>$token]);
    }
    static function validate_contact($p,$simple=false) {
        $e=[];
        if(trim($p['name']??'')==='') $e['name']='Enter your full name.';
        if(!is_email($p['email']??'')) $e['email']='Enter a valid email address.';
        if(!preg_match('/^[+()\d\s-]{8,25}$/',$p['phone']??'') || strlen(preg_replace('/\D/','',$p['phone']??''))<8 || strlen(preg_replace('/\D/','',$p['phone']??''))>15) $e['phone']='Enter a valid mobile number.';
        if(empty($p['consent'])) $e['consent']='Please agree before sending your enquiry.';
        if($simple) { if(empty(trim($p['suburb_name']??''))) $e['suburb_name']='Enter your suburb.';if(empty(trim($p['notes']??''))) $e['notes']='Tell us how we can help.'; }
        else {
            $date=$p['preferred_date']??'';$dt=DateTimeImmutable::createFromFormat('!Y-m-d',$date,wp_timezone());
            if(!$dt || $dt->format('Y-m-d')!==$date || $date<wp_date('Y-m-d')) $e['preferred_date']='Choose a valid date today or later.';
            if(!in_array($p['parking']??'',['available','limited','discuss'],true)) $e['parking']='Tell us about parking / access.';
        }
        return $e;
    }
    static function submit() { self::save(false); }
    static function contact() { self::save(true); }
    static function save($simple) {
        $p=self::request();$errors=self::validate_contact($p,$simple);
        if($errors) wp_send_json_error(['message'=>'Please complete the highlighted fields.','errors'=>$errors],422);
        $key=$p['request_key']??'';
        if(!preg_match('/^[a-zA-Z0-9-]{24,80}$/',$key)) wp_send_json_error(['message'=>'Refresh the page to start a new enquiry.'],422);
        $key=hash('sha256',$key);global $wpdb;$table=PCE_DB::get_table_name();
        $existing=$wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE request_key=%s",$key));
        if($existing) self::success($existing);
        $quote=$simple?['version'=>2,'state'=>'custom','service_name'=>'General enquiry','reasons'=>['enquiry'],'input'=>['service'=>'contact'],'lines'=>[],'total'=>null,'low'=>null,'high'=>null]:get_transient('pce_quote_'.sanitize_text_field($p['quote_token']??''));
        if(!$quote) wp_send_json_error(['message'=>'Your estimate has expired. Go back to review your estimate; your contact details will stay in this form.','expired'=>true],409);
        $input=$quote['input'];$s=PCE_Approved::settings();$suburb=$quote['suburb_details']??null;
        if(!$simple && !$suburb && trim($p['suburb_name']??'')==='') wp_send_json_error(['message'=>'Enter your suburb.','errors'=>['suburb_name'=>'Enter your suburb for a custom quote.']],422);
        $extras=[]; foreach(['additional_dates','time_preference','parking','condition','pets','referral'] as $k) $extras[$k]=sanitize_text_field($p[$k]??'');
        $extras['consent']=true;$extras['consent_at']=gmdate('c');$extras['privacy_url']=get_privacy_policy_url();
        $record=['created_at'=>current_time('mysql'),'status'=>'new','customer_name'=>sanitize_text_field($p['name']),'customer_email'=>sanitize_email($p['email']),'customer_phone'=>sanitize_text_field($p['phone']),'suburb'=>$suburb?$suburb['name']:sanitize_text_field($p['suburb_name']??''),'postcode'=>$suburb?$suburb['postcode']:'','service_type'=>$input['service'],'is_in_area'=>$suburb?1:0,'property_details'=>wp_json_encode(array_merge($input,['enquiry'=>$extras])),'estimate_breakdown'=>wp_json_encode($quote),'total_amount'=>$quote['total']??0,'preferred_date'=>$simple?null:$p['preferred_date'],'notes'=>sanitize_textarea_field($p['notes']??''),'request_key'=>$key,'email_sent'=>0];
        $ok=$wpdb->insert($table,$record);$id=$wpdb->insert_id;
        if(!$ok) { $id=$wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE request_key=%s",$key));if($id) self::success($id);wp_send_json_error(['message'=>'Your enquiry could not be saved. Your details remain below. Please try again.'],500); }
        // A saved record is success even when transport is not configured. Never claim inbox delivery.
        $reference=PCE_Approved::reference($id);$mail_error='Email transport / approved sender not configured.';$sent=0;
        if(is_email($s['sender_email']) && is_email($s['email'])) {
            $headers=['Content-Type: text/plain; charset=UTF-8','From: Prime Domestic & Cleaning Services <'.$s['sender_email'].'>'];
            $range=$quote['state']==='estimate'?'AUD '.$quote['low'].' to AUD '.$quote['high']:'Custom quote required';
            $first=preg_split('/\s+/',trim($record['customer_name']))[0];
            $subject='New enquiry '.$reference.' – '.$quote['service_name'].' – '.$record['suburb'].' – '.$range;
            $body=$reference."\n".$record['customer_name']."\n".$record['customer_email']."\n".$record['customer_phone']."\n".$record['suburb']."\n".$quote['service_name']."\n".$range."\nPreferred date request: ".($record['preferred_date']??'Not supplied')."\n".$record['notes']."\n\nDetails: ".wp_json_encode($extras)."\nSelections: ".wp_json_encode($quote['input']);
            if($quote['state']==='estimate')$body.="\nFrozen breakdown: ".wp_json_encode($quote);
            else $body.="\nCustom quote: ".implode(', ',$quote['reasons']??[]).". No numerical estimate has been issued.";
            $recipients=array_values(array_unique(array_filter([$s['email'],$s['owner_email']],'is_email')));
            $owner=wp_mail($recipients,$subject,$body,array_merge($headers,['Reply-To: '.$record['customer_email']]));
            $ackbody='Hi '.$first.', thanks for reaching out to Prime Domestic and Cleaning Services. We’ve received your enquiry (reference '.$reference.') for '.$quote['service_name'].' in '.$record['suburb'];
            if($quote['state']==='estimate')$ackbody.=', with an estimate of '.$range.'. We aim to reply within 2 hours during the day, and always within 24 hours, to confirm the details and a time that suits you. If you’d like to send photos, just reply to this email or message us on WhatsApp at 0491 245 156.';
            else $ackbody.='. This one needs a proper look, so we’ll come back to you with a quote. We aim to reply within 2 hours during the day, and always within 24 hours. Photos help a lot: just reply to this email or WhatsApp us at 0491 245 156.';
            $ackbody.="\n\nYour preferred date is a request, not a confirmed booking.\n\nTalk soon, Derick and the Prime Domestic team.";
            $ack=wp_mail($record['customer_email'],'Got it! Your enquiry '.$reference,$ackbody,array_merge($headers,['Reply-To: '.$s['email']]));
            $sent=$owner&&$ack?1:0;$mail_error=$sent?'':'One or more email handoffs failed. Check the saved enquiry and mail configuration.';
        }
        $wpdb->update($table,['email_sent'=>$sent,'email_error'=>$mail_error],['id'=>$id]);self::success($id);
    }
    static function success($id) { $row=PCE_DB::get_enquiry($id);$quote=json_decode($row['estimate_breakdown'],true);$first=preg_split('/\s+/',trim($row['customer_name']))[0];wp_send_json_success(['reference'=>PCE_Approved::reference($id),'whatsapp'=>PCE_Approved::whatsapp(PCE_Approved::reference($id)),'first_name'=>$first,'state'=>$quote['state'],'range'=>$quote['state']==='estimate'?['low'=>$quote['low'],'high'=>$quote['high']]:null,'message'=>'Thanks '.$first.'! Your reference is '.PCE_Approved::reference($id).'. We aim to reply within 2 hours during the day, and always within 24 hours. Want to speed things up? Send us a few photos on WhatsApp.']); }
}
