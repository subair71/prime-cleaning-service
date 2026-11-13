<?php
if(getenv('PRIME_DISPOSABLE_TEST_DB')!=='yes'||!getenv('PRIME_TEST_WP_ROOT'))exit('Disposable WordPress required.');
$_SERVER['HTTP_HOST']='127.0.0.1:4173';$_SERVER['REQUEST_URI']='/';
require getenv('PRIME_TEST_WP_ROOT').'/wp-load.php';
$n=0;function review_check($label,$ok){global $n;$n++;if(!$ok){fwrite(STDERR,"FAIL $label\n");exit(1);}}
$settings=get_option(PCE_Approved::OPTION);$privacy=prime_page_object('privacy')->post_content;$terms=prime_page_object('terms')->post_content;
$original=prime_b('image','<figure class="wp-block-image prime-service-photo"><img src="test.webp" alt="Clean floor"/><figcaption class="wp-element-caption">Illustrative interior</figcaption></figure>',['className'=>'prime-service-photo']);
$original.=prime_p('Client edit to preserve.').prime_launch_cta('Test heading','carpet');
$clean=serialize_blocks(array_map('prime_refine_block',parse_blocks($original)));
review_check('caption removed from serialized block',!str_contains($clean,'Illustrative interior'));
review_check('alt and client edits retained',str_contains($clean,'alt="Clean floor"')&&str_contains($clean,'Client edit to preserve.'));
review_check('three final actions',str_contains($clean,'tel:+61491245156')&&str_contains($clean,'wa.me/')&&str_contains($clean,'prime_service=carpet'));
review_check('carpet selections and anchor retained',str_contains($clean,'prime_service=carpet#estimate'));
review_check('block transformation idempotent',serialize_blocks(array_map('prime_refine_block',parse_blocks($clean)))===$clean);
$legit=prime_b('image','<figure><img src="test.webp" alt="Room"/><figcaption>Cleaning preparation guidance</figcaption></figure>');
review_check('useful captions retained',serialize_blocks(array_map('prime_refine_block',parse_blocks($legit)))===$legit);
prime_install_approved_pages();
review_check('approved privacy unchanged',prime_page_object('privacy')->post_content===$privacy);
review_check('terms unchanged',prime_page_object('terms')->post_content===$terms);
review_check('pricing settings unchanged',get_option(PCE_Approved::OPTION)===$settings);
foreach(prime_pages_map() as $key=>$v){$p=prime_page_object($key);review_check($key.' no caption badges',!preg_match('~<figcaption[^>]*>Illustrative~i',$p->post_content));}
review_check('review preview clearly labelled',str_contains(prime_reviews_section(),'placeholder text, not customer reviews'));
$public=get_option('blog_public');update_option('blog_public',1);
review_check('placeholder disabled on indexable site',!str_contains(prime_reviews_section(),'Lorem ipsum'));
review_check('missing reviews hidden completely',prime_reviews_section()==='');
review_check('Google source accepted',prime_review_source_valid('https://www.google.com/maps/reviews/fixture'));
review_check('arbitrary source rejected',!prime_review_source_valid('https://google.com.example.invalid/review'));
$id=wp_insert_post(['post_type'=>'prime_review','post_status'=>'publish','post_title'=>'QA fixture, not a customer','post_content'=>prime_p('Lorem ipsum placeholder.')]);
update_post_meta($id,'_prime_review_approved',1);update_post_meta($id,'_prime_review_source','https://www.google.com/maps/reviews/fixture');
review_check('published placeholder rejected',prime_reviews_section()==='');
wp_update_post(['ID'=>$id,'post_content'=>prime_p('QA-only review integration fixture, not real customer feedback.')]);
review_check('approved sourced entry renders',str_contains(prime_reviews_section(),'QA-only review integration fixture'));
wp_update_post(['ID'=>$id,'post_status'=>'draft']);
review_check('draft review hidden',prime_reviews_section()==='');
wp_delete_post($id,true);update_option('blog_public',$public);
$row=PCE_DB::get_enquiries(['limit'=>1])[0];$snapshot=$row['estimate_breakdown'];
foreach(['contacted','won','lost','new'] as $status){PCE_DB::update_enquiry_status($row['id'],$status);review_check('admin status '.$status,PCE_DB::get_enquiry($row['id'])['status']===$status);}
review_check('status change leaves snapshot frozen',PCE_DB::get_enquiry($row['id'])['estimate_breakdown']===$snapshot);
review_check('invalid status rejected',PCE_DB::update_enquiry_status($row['id'],'invented')===false);
PCE_DB::update_enquiry_status($row['id'],$row['status']);
echo "$n review, migration, publication and status checks passed.\n";
