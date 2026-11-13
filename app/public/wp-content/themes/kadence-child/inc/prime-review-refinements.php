<?php
/** Review-ready presentation; no prices, enquiries or approved legal text are replaced. */
defined('ABSPATH') || exit;

add_action('init', function () {
    register_post_type('prime_review', [
        'labels' => ['name'=>'Customer reviews', 'singular_name'=>'Customer review', 'add_new_item'=>'Add genuine Google review'],
        'public'=>false, 'show_ui'=>true, 'show_in_rest'=>true, 'show_in_nav_menus'=>false,
        'exclude_from_search'=>true, 'rewrite'=>false, 'supports'=>['title','editor','revisions'],
        'menu_icon'=>'dashicons-format-quote',
    ]);
});
add_action('add_meta_boxes_prime_review', function () {
    add_meta_box('prime-review-source', 'Google review source and approval', function ($post) {
        wp_nonce_field('prime_review_source', 'prime_review_nonce');
        echo '<p>Copy a genuine review exactly into the block editor. Use the customer’s first name and initial as the title. Do not add ratings, dates or invented names.</p>';
        echo '<p><label>Public Google review URL<br><input type="url" style="width:100%" name="prime_review_source" value="'.esc_attr(get_post_meta($post->ID,'_prime_review_source',true)).'"></label></p>';
        echo '<label><input type="checkbox" name="prime_review_approved" value="1" '.checked(get_post_meta($post->ID,'_prime_review_approved',true),1,false).'> I have checked this wording and author against the genuine Google review.</label>';
        echo '<p>Only published, approved reviews with a Google source appear. Drafts and Lorem Ipsum never appear in production.</p>';
    });
});
function prime_review_source_valid($url) {
    $host = strtolower((string)wp_parse_url($url, PHP_URL_HOST));
    return wp_parse_url($url, PHP_URL_SCHEME)==='https' && (in_array($host,['google.com','www.google.com','maps.google.com','google.com.au','www.google.com.au','maps.app.goo.gl','g.page'],true));
}
add_action('save_post_prime_review', function ($id) {
    if (wp_is_post_revision($id) || !current_user_can('edit_post',$id) || !wp_verify_nonce($_POST['prime_review_nonce']??'', 'prime_review_source')) return;
    $source=esc_url_raw(wp_unslash($_POST['prime_review_source']??''));
    update_post_meta($id,'_prime_review_source',$source);
    update_post_meta($id,'_prime_review_approved',!empty($_POST['prime_review_approved']) && prime_review_source_valid($source)?1:0);
});
function prime_review_preview_enabled() {
    return defined('PRIME_REVIEW_PREVIEW') && PRIME_REVIEW_PREVIEW && wp_get_environment_type()!=='production' && !get_option('blog_public');
}
function prime_reviews_section($atts=[]) {
    $cards='';
    $reviews=get_posts(['post_type'=>'prime_review','post_status'=>'publish','numberposts'=>6,'meta_key'=>'_prime_review_approved','meta_value'=>1]);
    foreach($reviews as $review) {
        $source=get_post_meta($review->ID,'_prime_review_source',true);
        if(!prime_review_source_valid($source) || !$review->post_title || !trim(wp_strip_all_tags($review->post_content)) || preg_match('/lorem\s+ipsum/i',$review->post_content)) continue;
        $cards.='<figure class="pp-review-card"><blockquote>'.wp_kses_post(do_blocks($review->post_content)).'</blockquote><figcaption>'.esc_html($review->post_title).' · <a href="'.esc_url($source).'">Read on Google</a></figcaption></figure>';
    }
    // Existing trusted integrations can supply their rendered widget without being replaced.
    $integration=apply_filters('prime_reviews_integration_html','');
    $preview=!$cards && !$integration && prime_review_preview_enabled();
    $url=prime_business()['reviews_url']??'';
    if(!$cards && !$integration && !$preview && !$url) return '';
    if($preview) {
        for($i=0;$i<3;$i++) $cards.='<figure class="pp-review-card"><blockquote><p>'.esc_html(prime_lorem()).'</p></blockquote></figure>';
    }
    $heading=$atts['heading']??'What our neighbours say';
    $out='<section class="pp-section pp-reviews"><div class="pp-container"><div class="pp-reviews-heading"><h2>'.esc_html($heading).'</h2><p>'.($preview?'Review layout preview — placeholder text, not customer reviews.':'Real reviews from real neighbours on Google.').'</p></div>';
    $out.=$integration?:($cards?'<div class="pp-review-grid">'.$cards.'</div>':'');
    if($url && !$preview) $out.='<p class="pp-review-link"><a href="'.esc_url($url).'">Read all our Google reviews →</a></p>';
    return $out.'</div></section>';
}

function prime_refine_block($block,$in_cta=false) {
    // Shorten only existing estimator destinations; query selections remain intact.
    $prices=prime_page_link('prices');
    $rewrite=function($html)use($prices){return preg_replace_callback('/href="([^"#]+)"/',function($m)use($prices){
        $href=html_entity_decode($m[1],ENT_QUOTES,'UTF-8');
        return str_starts_with($href,$prices) && (strlen($href)===strlen($prices) || substr($href,strlen($prices),1)==='?')?'href="'.esc_url($href.'#estimate').'"':$m[0];
    },$html);};
    $block['innerHTML']=$rewrite($block['innerHTML']);
    foreach($block['innerContent'] as &$part)if(is_string($part))$part=$rewrite($part);
    unset($part);
    $class=$block['attrs']['className']??'';
    $in_cta=$in_cta || preg_match('/\bpp-cta\b/',$class);
    if($block['blockName']==='core/image') {
        $pattern='~<figcaption\b[^>]*>\s*(?:Illustrative (?:interior|image|photography)|AI[- ]generated image)\s*</figcaption>~i';
        $block['innerHTML']=preg_replace($pattern,'',$block['innerHTML']);
        foreach($block['innerContent'] as &$chunk) if(is_string($chunk)) $chunk=preg_replace($pattern,'',$chunk);
        unset($chunk);
    }
    foreach($block['innerBlocks'] as &$child) $child=prime_refine_block($child,$in_cta);
    unset($child);
    if($in_cta && $block['blockName']==='core/column') {
        $html=serialize_block($block);
        if(str_contains($html,'wa.me/') && !str_contains($html,'tel:')) {
            $button=parse_blocks(prime_buttons('Call us','tel:+61491245156',true))[0];
            $block['innerBlocks'][]=$button;
            array_splice($block['innerContent'],count($block['innerContent'])-1,0,[null]);
        }
    }
    if(str_contains($class,'pp-estimator-section') && empty($block['attrs']['anchor'])) {
        $block['attrs']['anchor']='estimate';
        $block['innerHTML']=preg_replace('/<section /','<section id="estimate" ',$block['innerHTML'],1);
        foreach($block['innerContent'] as &$chunk) if(is_string($chunk)) $chunk=preg_replace('/<section /','<section id="estimate" ',$chunk,1);
        unset($chunk);
    }
    return $block;
}
function prime_refine_pages($ids) {
    foreach($ids as $key=>$id) {
        // Retain the approved Privacy and any legal body byte-for-byte.
        if(in_array($key,['privacy','terms'],true) || get_post_meta($id,'_prime_review_refined',true)==='2026-11-13') continue;
        $post=get_post($id);if(!$post)continue;
        $content=serialize_blocks(array_map('prime_refine_block',parse_blocks($post->post_content)));
        if($content!==$post->post_content) {
            add_post_meta($id,'_prime_pre_review_content',$post->post_content,true);
            wp_save_post_revision($id);
            $result=wp_update_post(wp_slash(['ID'=>$id,'post_content'=>$content]),true);
            if(is_wp_error($result))continue;
        }
        update_post_meta($id,'_prime_review_refined','2026-11-13');
    }
    update_option('prime_review_content_version','2026-11-13');
}
