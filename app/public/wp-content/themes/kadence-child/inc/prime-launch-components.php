<?php
defined('ABSPATH') || exit;
function prime_has_posts(){return (bool)get_posts(['post_type'=>'post','post_status'=>'publish','numberposts'=>1,'fields'=>'ids','meta_query'=>[['key'=>'_prime_sample','compare'=>'NOT EXISTS']]]);}
function prime_launch_excluded_ids(){ $ids=[];foreach(['office','terms'] as $k){$p=prime_page_object($k);if($p && ($k==='office'||$p->post_status!=='publish'))$ids[]=$p->ID;}if(!prime_has_posts() && ($p=prime_page_object('blog')))$ids[]=$p->ID;return $ids; }
function prime_install_approved_pages(){
 $ids=get_option('prime_approved_page_ids',[]);
 foreach(prime_pages_map() as $key=>$v){
  $p=prime_page_object($key);
  if($p && get_post_meta($p->ID,'_prime_nov12_installed',true)){$ids[$key]=$p->ID;continue;}
  if($p){add_post_meta($p->ID,'_prime_pre_nov12_content',$p->post_content,true);add_post_meta($p->ID,'_prime_pre_nov12_status',$p->post_status,true);wp_save_post_revision($p->ID);}
  $status=in_array($key,['office','terms'],true)?'draft':($p?$p->post_status:'publish');
  // Never replace an explicitly client-approved legal document with a layout placeholder.
  $preserve_terms=$key==='terms' && $p && get_post_meta($p->ID,'_prime_legal_approved',true);
  $body=$preserve_terms?$p->post_content:prime_pattern($key);
  $post=['post_type'=>'page','post_title'=>$v[0],'post_content'=>$body,'post_status'=>$preserve_terms?$p->post_status:$status];
  if($p)$post['ID']=$p->ID;else $post['post_name']=$v[1][0];
  $id=wp_insert_post(wp_slash($post),true);if(is_wp_error($id))continue;
  $ids[$key]=$id;update_post_meta($id,'_prime_nov12_installed',1);update_post_meta($id,'_prime_approved_installed',1);
  if(in_array($key,['office','terms'],true))update_post_meta($id,'rank_math_robots',['noindex','nofollow']);
  if($key==='privacy')update_option('wp_page_for_privacy_policy',$id);
 }
 update_option('prime_approved_page_ids',$ids);
 if(!get_option('page_on_front') && !empty($ids['home'])){update_option('page_on_front',$ids['home']);update_option('show_on_front','page');}
 // Explicitly out of Phase 1. Keep bodies/media for later review; do not delete records.
 foreach(['services','services/deep-cleaning','deep-cleaning','bathroom-cleaning','services/bathroom-cleaning','balcony-patio-cleaning','garage-cleaning','small-office-cleaning'] as $path){$p=get_page_by_path($path);if($p && !in_array($p->ID,$ids)){add_post_meta($p->ID,'_prime_pre_nov12_status',$p->post_status,true);wp_update_post(['ID'=>$p->ID,'post_status'=>'draft']);update_post_meta($p->ID,'_prime_phase2',1);}}
 if(!get_option('prime_nov12_settings_installed')){
  $s=PCE_Approved::settings();foreach(['phone'=>'0491 245 156','whatsapp'=>'61491245156','email'=>'info@primedomestic.com.au','abn'=>'16 938 734 164','minimum'=>110,'response_notice'=>prime_response_text()] as $k=>$v)$s[$k]=$v;
  update_option(PCE_Approved::OPTION,$s);update_option('prime_nov12_settings_installed',1);
 }
 update_option('prime_nov12_content_version','2026-11-12');prime_refine_pages($ids);flush_rewrite_rules(false);wp_cache_flush();return $ids;
}
function prime_price_from($service){$s=prime_business();$minimum=PCE_Approved::money($s['minimum']);if($minimum===null)return null;
 if($service==='carpet'){ $rate=PCE_Approved::money($s['carpets']['bedroom']??null);return $rate; }
 $values=[];foreach($s['grids'][$service==='deep'?'regular':$service]??[] as $v)if(PCE_Approved::money($v)!==null)$values[]=(float)$v;
 if(!$values)return null;$base=min($values);if($service==='deep'){if(PCE_Approved::money($s['deep_multiplier'])===null)return null;$base*=$s['deep_multiplier'];}return max($base,$minimum);
}
function prime_price_text($value){return $value===null?'Custom quote':'AUD '.number_format($value,2);}
function prime_html_table($head,$rows){$out='<div class="pp-table-wrap" tabindex="0" role="region" aria-label="'.esc_attr(implode(', ',$head)).'"><table><thead><tr>';foreach($head as $h)$out.='<th scope="col">'.esc_html($h).'</th>';$out.='</tr></thead><tbody>';foreach($rows as $row){$out.='<tr>';foreach($row as $i=>$v)$out.='<'.($i?'td':'th scope="row"').'>'.esc_html($v).'</'.($i?'td':'th').'>';$out.='</tr>';}return $out.'</tbody></table></div>';}
add_action('init',function(){
 add_shortcode('prime_starting_price',function($a){$service=$a['service']??'regular';$n=prime_price_from($service);$suffix=$a['suffix']??($service==='carpet'?'per room':'');return '<p class="pp-starting">'.($n===null?'Request a custom quote':'From '.esc_html(prime_price_text($n)).($suffix?' <small>'.esc_html($suffix).'</small>':'')).'</p>';});
 add_shortcode('prime_carpet_rates',function(){$s=prime_business();$out='<div class="pp-rate-grid">';foreach(['bedroom'=>'Bedroom','living'=>'Lounge / living room','hallway'=>'Hallway','stairs'=>'Stairs'] as $k=>$name){$n=PCE_Approved::money($s['carpets'][$k]??null);$out.='<div class="pp-card"><h3>'.esc_html($name).'</h3><p class="pp-starting">'.($n===null?'Custom quote':'From '.esc_html(prime_price_text($n))).'</p></div>';}return $out.'</div>';});
 add_shortcode('prime_minimum_notice',function(){return '<p class="pp-small">Minimum booking: '.esc_html(prime_price_text(PCE_Approved::money(prime_business()['minimum']))).'. No GST charged.</p>';});
 add_shortcode('prime_starting_table',function(){$rows=[];foreach(['regular'=>'Regular clean','deep'=>'Deep / spring clean','lease'=>'End of lease clean','carpet'=>'Carpet per room'] as $k=>$name){$n=prime_price_from($k);$rows[]=[$name,$n===null?'Custom quote':'From '.prime_price_text($n)];}$s=prime_business();foreach(PCE_Approved::EXTRAS as $k=>$label)$rows[]=[$label,prime_price_text(PCE_Approved::money($s['extras'][$k]??null))];return prime_html_table(['Service / add-on','Starting price'],$rows).do_shortcode('[prime_minimum_notice]').'<p class="pp-small">Safe-reach inside windows and ordinary wall spot cleaning are included in End of Lease. Extra wall work needs a custom quote.</p>';});
 add_shortcode('prime_comparison',function(){return prime_html_table(['Task','Regular','End of lease'],[['Kitchen benchtops, sink, stovetop','Yes','Yes'],['Inside cupboards and drawers','No','Yes'],['Bathrooms, toilet, mirrors','Yes','Yes, detailed'],['Skirting boards, door frames','Light dust','Yes, wiped'],['Window sills and tracks','No','Yes'],['Wall spot cleaning','Add-on','Included where possible'],['Oven','Add-on','Add-on']]);});
 add_shortcode('prime_pricing_notice',function(){$s=prime_business();return '<p class="pp-pricing-note">Prices include equipment and products. No GST is charged. Prices are for homes in reasonable condition; homes needing extra work may be quoted separately. A flat '.esc_html(prime_price_text(PCE_Approved::money($s['travel_fee']))).' travel fee applies to approved suburbs 15–25 km from Malvern East, and further out is by arrangement. The minimum booking is '.esc_html(prime_price_text(PCE_Approved::money($s['minimum']))).'.</p>';});
 add_shortcode('prime_reviews','prime_reviews_section');
 add_shortcode('prime_latest_posts',function(){if(!prime_has_posts())return '';return '<section class="pp-section pp-mint"><div class="pp-container"><h2>Tips from the cleaning cupboard</h2>'.prime_blog_listing().'</div></section>';});
 add_shortcode('prime_service_areas',function(){ $s=prime_business();$out='<div class="pp-area-layout"><figure class="pp-zone-diagram"><img src="'.esc_url(get_stylesheet_directory_uri().'/assets/images/service-zones.svg').'" width="640" height="480" loading="lazy" alt="Schematic of two travel zones around Malvern East: inner approximately 15 km, outer approximately 25 km. No street address pin."><figcaption>Our two service zones. Free travel in the inner circle. Schematic, not a boundary map; suburb confirmation required.</figcaption></figure><div>';foreach([1=>'Within about 15 km',2=>'About 15–25 km'] as $zone=>$name){$names=[];foreach($s['suburbs'] as $r)if((int)$r['zone']===$zone)$names[]=$r['name'].' '.$r['postcode'];$out.='<section class="pp-card"><h3>Zone '.$zone.' · '.esc_html($name).'</h3><p>'.($zone===1?'No travel fee.':esc_html(prime_price_text(PCE_Approved::money($s['travel_fee']))).' per visit.').'</p><p>'.esc_html($names?implode(' · ',$names):'Suburb list awaiting confirmation. Tell us your suburb and we’ll confirm your travel zone.').'</p></section>';}$out.='</div></div>';return $out;});
},20);
add_filter('wp_nav_menu_objects',function($items){$exclude=prime_launch_excluded_ids();return array_filter($items,function($i)use($exclude){$p=get_post($i->object_id);return !in_array((int)$i->object_id,$exclude,true)&&(!$p||$p->post_type!=='page'||$p->post_status==='publish');});});
add_filter('wp_sitemaps_posts_query_args',function($args,$type){if($type==='page')$args['post__not_in']=array_unique(array_merge($args['post__not_in']??[],prime_launch_excluded_ids()));return $args;},10,2);
add_action('pre_get_posts',function($q){if(!is_admin()&&$q->is_search())$q->set('post__not_in',array_merge((array)$q->get('post__not_in'),prime_launch_excluded_ids()));});
