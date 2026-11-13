<?php
/** One metadata owner: Rank Math when active; compact fallback otherwise. */
defined('ABSPATH') || exit;
function prime_seo_copy(){return [
'home'=>['Home Cleaning Malvern East | Prime Domestic',"Family-run home cleaning in Malvern East and Melbourne's south-east. Instant online estimate, pay on completion, we bring everything."],
'lease'=>['End of Lease Cleaning Malvern East | Prime Domestic',"End of lease cleaning to the agent checklist across Melbourne's south-east. Clear prices, free fixes on agent reports within 72 hours."],
'regular'=>['Regular House Cleaning Malvern East | Prime Domestic','Weekly, fortnightly or one-off house cleaning by a small local team. 15% off regular visits. Get an instant estimate.'],
'carpet'=>['Carpet Steam Cleaning Malvern East | Prime Domestic','Hot-water extraction carpet cleaning, priced per room. Great for families, pets and end of lease. Get your estimate online.'],
'prices'=>['Cleaning Prices & Instant Estimate | Prime Domestic','See starting prices and get an instant cleaning estimate in about a minute. No GST, no deposits, pay on completion.'],
'about'=>['About Us | Prime Domestic and Cleaning Services','Meet the small family-run cleaning team from Malvern East. Honest prices, familiar faces, and we fix anything we miss.'],
'contact'=>['Contact & Service Areas | Prime Domestic','Call, WhatsApp or email us. Free travel within 15 km of Malvern East, flat travel fee for 15–25 km. We aim to reply within 2 hours.'],
'blog'=>['Cleaning Tips | Prime Domestic','Practical cleaning tips from a small Melbourne team: end of lease checklists, oven hacks, mould prevention and more.'],
'privacy'=>['Privacy Policy | Prime Domestic','How Prime Domestic and Cleaning Services collects, uses and protects your personal information.'],
'terms'=>['Terms of Service | Prime Domestic','Booking, payment, cancellation and service terms for Prime Domestic and Cleaning Services.'],
'office'=>['Office Cleaning — Phase 2 Draft | Prime Domestic','Unpublished Phase 2 design for review.']];}
function prime_seo_row(){if(is_singular('post'))return null;return prime_seo_copy()[prime_page_key()]??null;}
function prime_seo_plugin(){return defined('RANK_MATH_VERSION')||defined('WPSEO_VERSION')||defined('AIOSEO_VERSION');}
function prime_is_staging(){return wp_get_environment_type()!=='production'||!in_array(strtolower((string)wp_parse_url(home_url(),PHP_URL_HOST)),['primedomestic.com.au','www.primedomestic.com.au'],true)||!get_option('blog_public');}
function prime_noindex(){return prime_is_staging()||in_array(get_queried_object_id(),prime_launch_excluded_ids(),true)||is_preview()||is_search()||($p=get_queried_object()) instanceof WP_Post && $p->post_status!=='publish';}
function prime_canonical(){return is_front_page()?home_url('/'):(is_singular()?get_permalink():home_url('/'));}
add_filter('pre_get_document_title',function($title){$r=prime_seo_row();return $r?$r[0]:$title;},30);
add_filter('rank_math/frontend/title',function($v){return (prime_seo_row()[0]??$v);});
add_filter('rank_math/frontend/description',function($v){return (prime_seo_row()[1]??$v);});
add_filter('rank_math/frontend/canonical',function($v){return prime_seo_row()?prime_canonical():$v;});
add_filter('wp_robots',function($r){if(prime_noindex()){unset($r['index'],$r['follow']);$r['noindex']=true;$r['nofollow']=true;}return $r;},99);
add_filter('rank_math/frontend/robots',function($r){if(prime_noindex()){$r['index']='noindex';$r['follow']='nofollow';}return $r;});
add_filter('wp_sitemaps_enabled',function($enabled){return prime_is_staging()?false:$enabled;});
add_filter('rank_math/sitemap/entry',function($url,$type,$object){return prime_is_staging()||($object instanceof WP_Post && in_array($object->ID,prime_launch_excluded_ids(),true))?false:$url;},10,3);
add_filter('robots_txt',function($text){return prime_is_staging()?"User-agent: *\nDisallow: /\n":$text;},99);
add_action('send_headers',function(){if(prime_is_staging())header('X-Robots-Tag: noindex, nofollow, noarchive');});
function prime_schema_graph(){
 $root=home_url('/');$url=prime_canonical();$key=prime_page_key();$s=prime_business();
 $org=['@type'=>'LocalBusiness','@id'=>$root.'#business','name'=>'Prime Domestic and Cleaning Services','url'=>$root,'telephone'=>'+61491245156','email'=>'info@primedomestic.com.au','taxID'=>'16 938 734 164','areaServed'=>[['@type'=>'Place','name'=>'Malvern East'],['@type'=>'Place','name'=>"Melbourne’s south-east"]]];
 $graph=[$org];if(in_array($key,['lease','regular','carpet'],true))$graph[]=['@type'=>'Service','@id'=>$url.'#service','serviceType'=>prime_pages_map()[$key][0],'provider'=>['@id'=>$root.'#business'],'areaServed'=>'Malvern East and Melbourne’s south-east','url'=>$url];
 if($key && $key!=='home')$graph[]=['@type'=>'BreadcrumbList','@id'=>$url.'#breadcrumb','itemListElement'=>[['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>$root],['@type'=>'ListItem','position'=>2,'name'=>prime_pages_map()[$key][0],'item'=>$url]]];
 return $graph;
}
add_filter('rank_math/json_ld',function($data){if(!prime_seo_row())return $data;foreach($data as $k=>$v)if(in_array($v['@type']??'',['Organization','LocalBusiness','Service','BreadcrumbList'],true))unset($data[$k]);foreach(prime_schema_graph() as $i=>$v)$data['prime'.$i]=$v;return $data;},99);
add_action('wp',function(){if(prime_seo_row()&&!prime_seo_plugin())remove_action('wp_head','rel_canonical');});
add_action('wp_head',function(){if(prime_seo_plugin()||!($r=prime_seo_row()))return;$url=prime_canonical();$image=get_stylesheet_directory_uri().'/assets/images/prime-regular-1280.webp';
 echo '<meta name="description" content="'.esc_attr($r[1]).'">' . "\n";
 echo '<link rel="canonical" href="'.esc_url($url).'">' . "\n";
 foreach(['og:type'=>'website','og:title'=>$r[0],'og:description'=>$r[1],'og:url'=>$url,'og:locale'=>'en_AU','og:site_name'=>'Prime Domestic and Cleaning Services','og:image'=>$image,'og:image:alt'=>'Illustrative light-filled living room'] as $k=>$v)echo '<meta property="'.esc_attr($k).'" content="'.esc_attr($v).'">' . "\n";
 echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
 if(!in_array(prime_page_key(),['office','terms'],true))echo '<script type="application/ld+json">'.wp_json_encode(['@context'=>'https://schema.org','@graph'=>prime_schema_graph()],JSON_HEX_TAG|JSON_HEX_AMP).'</script>';
},5);
