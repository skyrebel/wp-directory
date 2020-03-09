<?php
global $post, $ae_post_factory , $current_user,$user_ID;
do_action("before_single_place");
et_get_mobile_header();
$total_count = get_comments(array( 'post_id' => $post->ID, 'type' => 'review', 'count' => true, 'status' => 'approve','meta_key' => 'et_rate_comment', ));
if(have_posts()) { the_post();
    
    $place_obj = $ae_post_factory->get('place');
    $place = $place_obj->convert($post , 'big_post_thumbnail');
    $place_marker = array('ID'=> $place->ID, 'post_title'=> $place->post_title, 'permalink'=> $post->guid, 'latitude' => $place->et_location_lat, 'longitude'=> $place->et_location_lng );
    $sum = 0;
    $cats = $place->tax_input['place_category'];
     if(isset($cats['0'])){
        $sum = $cats['0']->count;
        $place_marker =  wp_parse_args(array('term_taxonomy_id'=> $cats['0']->term_id), $place_marker);
    }
    echo '<script type="data/json"  id="total_place">'. json_encode(array('number' => $sum, 'current_place'=> $place_marker) ) .'</script>';  
    if(isset($cats['0']->slug)){   
        echo '<script type="data/json"  id="place_cat_slug">'. json_encode(array('slug' => $cats['0']->slug) ) .'</script>';     
    }
    $favorite = get_comments(array(
        'post_id'      => $post->ID,
        'type'         => 'favorite',
        'author_email' => $current_user->user_email,
        'number'       => 1
    ));
     $report = get_comments(array(
        'post_id'      => $post->ID,
        'type'         => 'report',
        'author_email' => $current_user->user_email,
        'number'       => 1
    ));
if($place->cover_image_url) {
    $cover = $place->cover_image;
    $cover_image_url = wp_get_attachment_image_src( $cover, 'full' );
    $cover_image_url = $cover_image_url[0];
?>
	<!-- Top bar -->
	<section id="top-bar" class="section-wrapper"> 
    	<div class="container">
        	<div class="row">
            	<div class="col-xs-12">
                	<h1 class="title-page"><?php the_title(); ?></h1>
                </div>
            </div>
        </div>
    </section>
    <!-- Top bar / End -->
    
    <!-- Image Place -->
	<section id="img-place" class="section-wrapper" style="background:url(<?php echo $cover_image_url; ?>) no-repeat center center / cover; height:150px;"> 	
     <div itemscope itemtype="http://schema.org/ImageObject">
        <img itemprop="contentUrl"  src="<?php echo $cover_image_url; ?>" height ="0px" height="0px" >
     </div>
    </section>
<?php
}else {  
    get_template_part('mobile/template/section' , 'map'); 
}
?>
<!-- Image Place / End -->
<!-- Tabs -->
<section id="tabs-place-review-wrapper" class="section-wrapper">
    <div itemscope itemtype="http://schema.org/Place" >    
	 	<ul class="nav nav-tabs list-user-info list-place-info" role="tablist" id="myTab">
            <li class="active">
                <a href="#information_place" role="tab" data-toggle="tab">
                    <i class="fa fa-info-circle"></i><?php _e("Info", ET_DOMAIN); ?>
                </a>
            </li>
            <li>
                <a href="#gallery_place" role="tab" data-toggle="tab">
                    <i class="fa fa-picture-o"></i>
                    <?php _e("Gallery", ET_DOMAIN); ?>
                </a>
            </li>
            <li>
                <a href="#event_place" role="tab" data-toggle="tab">
                    <i class="fa fa-calendar"></i><?php _e("Event", ET_DOMAIN); ?>
                </a>
            </li>
            <li class="review_place">
                <a href="#review_place" role="tab" data-toggle="tab">
                    <i class="fa fa-comments"></i><?php _e("Review", ET_DOMAIN); ?>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <!-- Tabs 1 / Start -->
            <div class="tab-pane fade body-tabs active in" id="information_place">	
            	<div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="info-place-wrapper">
                                <div>
                                    <h1 class="title-place"><?php the_title(); ?></h1>
                                    <div itemscope itemtype="http://data-vocabulary.org/Recipe">
                                        <meta itemprop="name" content='<?php echo $place->post_title;?>' >
                                        <div style="display:inline-block" class="mobile-rate-stars" itemscope itemtype="http://data-vocabulary.org/Review-aggregate">
                                            <div class="rate-it" data-score="<?php echo average_rating('et_rate_comment',$place->ID); ?>"></div>
                                            <meta itemprop="rating" content="<?php echo $place->rating_score; ?>">
                                            <!--<meta itemprop="best" content="5">-->
                                            <meta itemprop="votes" content="<?php echo $place->reviews_count;?>">
                                            <a  class="number-review <?php if(is_singular()) {echo 'sroll-review' ;} ?>" href="<?php if(is_singular()) {echo '#review-list' ;}  else { the_permalink();} ?>">
                                               (<?php  printf(__( '%d reviews' , ET_DOMAIN ), $total_count) ?>
                                                    <?php if( ae_get_option("enable_view_counter",false) ){ 
                                                    if( $place->view_count > 1){
                                                        printf(__( '/ %s views' , ET_DOMAIN ), number_format($place->view_count) );
                                                    }else{
                                                        printf(__( '/ %d view' , ET_DOMAIN ), $place->view_count);
                                                    }
                                                } ?> )
                                            </a>
                                        </div>
                                    </div>
                                    <?php do_action('de_multirating_render_review'); ?>
                                </div>
                                <ul class="info-place">
                                	<li class="address-place">
                                        <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                                            <span class="word-wrap" itemprop="addressLocality">
                                                <i class="fa fa-map-marker"></i>
                                                <span class="distance"></span>
                                                <?php echo ($place->et_full_location) ? $place->et_full_location : __( 'No specify address' , ET_DOMAIN );; ?>
                                            </span>
                                        </div>
                                        <div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
                                            <meta itemprop="latitude" id="latitude" content="<?php echo $place->et_location_lat; ?>" />
                                            <meta itemprop="longitude" id="longitude" content="<?php echo $place->et_location_lng; ?>" />
                                        </div>   
                                    </li>
                                    <?php if($place->et_phone){ ?>
                                        <li itemprop="telephone" class="phone-place" <?php _e("Phone", ET_DOMAIN); ?>>
                                            <i class="fa fa-phone"></i><?php echo ($place->et_phone) ? $place->et_phone : __( 'No specify phone' , ET_DOMAIN );; ?>
                                        </li>
                                    <?php } ?>                                   
                                    <?php if($place->et_url){ ?>
                                        <li class="website-place" title="<?php _e("Website", ET_DOMAIN); ?>">
                                            <?php 
                                                echo ($place->et_url) ? '<i class="fa fa-link"></i><a rel="nofollow" href="http://'.str_replace(array('http://', 'https://'), '',$place->et_url ) .'" >'.$place->et_url.'</a>' : __( 'No specify website' , ET_DOMAIN );
                                            ?>
                                        </li>
                                    <?php } ?>                                       
                                    <?php if($place->serve_time){ ?>
                                        <li class="date-time-place">
                                            <div class="date-time">
                                                <i class="fa fa-clock-o"></i>
                                                <?php echo display_serve_time($place->serve_time);?>
                                            </div>
                                        </li>
                                    <?php }else{
                                        if($place->open_time && $place->close_time){ ?>
                                            <li class="time-place">
                                                <i class="fa fa-clock-o"></i>
                                                <?php  
                                                    if($place->open_time && $place->close_time) {
                                                        printf(__("%s to %s.", ET_DOMAIN), $place->open_time, $place->close_time);
                                                    }else{
                                                        // no specify serve time
                                                        if(!$place->open_time && !$place->close_time) {
                                                            _e("No specify serve time", ET_DOMAIN);
                                                        }
                                                        // specify open time
                                                        if( $place->open_time ) {
                                                            printf(__("Open at: %s", ET_DOMAIN) , $place->open_time);
                                                        }
                                                        // specify close time
                                                        if( $place->close_time ) {
                                                            printf(__("Close at: %s", ET_DOMAIN) , $place->close_time );
                                                        }
                                                    }
                                                ?>
                                            </li>
                                        <?php } ?>
                                        <li class="calendar-place">
                                            <i class="fa fa-calendar"></i>
                                            <?php de_serve_day($place->serve_day); ?>
                                        </li>
                                    <?php }?>
                                    <?php 
                                        if(function_exists('et_render_custom_field')) { 
                                            et_render_custom_meta_theme($post); 
                                            et_render_custom_taxonomy_theme($post);
                                        } 
                                    ?>
                                    <?php 
                                        do_action('list_info_place_mobile');
                                    ?>
                                </ul>
                            </div>
                            <div class="share-place-wrapper">
                                <div class="share-social">
                                    <label>Share:</label>
                                    <a class="facebook-social" onclick="window.open(this.href, '_blank', 'resizable=no,status=no,location=no,toolbar=no,menubar=no,fullscreen=no,scrollbars=no,dependent=no'); return false;"
                        href="http://api.addthis.com/oexchange/0.8/forward/facebook/offer?url=<?php echo $place->permalink; ?>&title=<?php echo $place->post_title; ?>" rel="nofollow"></a>
                                    <a class="twitter-social" onclick="window.open(this.href, '_blank', 'resizable=no,status=no,location=no,toolbar=no,menubar=no,fullscreen=no,scrollbars=no,dependent=no'); return false;"
                        href="http://api.addthis.com/oexchange/0.8/forward/twitter/offer?url=<?php echo $place->permalink; ?>&title=<?php echo $place->post_title; ?>" rel="nofollow"></a>
                                    <a class="google-plus-social" onclick="window.open(this.href, '_blank', 'resizable=no,status=no,location=no,toolbar=no,menubar=no,fullscreen=no,scrollbars=no,dependent=no'); return false;"
                        href="http://api.addthis.com/oexchange/0.8/forward/googleplus/offer?url=<?php echo $place->permalink; ?>&title=<?php echo $place->post_title; ?>" rel="nofollow"></a>
                                </div>
                                <div class="place-settings">                                  
                                        <?php if (empty($favorite)) { ?>
                                        <div class="place-add-favorite">
                                            <i class="fa fa-heart" aria-hidden="true"></i>
                                            <a class="<?php if($user_ID) {echo 'favorite';} else {echo 'authenticate';} ?>" class="" data-id="<?php echo $post->ID; ?>"><?php _e("Add to favorite", ET_DOMAIN);?></a>
                                        </div>
                                        <?php }else { ?> 
                                        <div class="place-remove-favorite">                                           
                                             <i class="fa fa-times" aria-hidden="true"></i>
                                             <a class="loved" class="" data-id="<?php echo $post->ID; ?>" data-favorite-id="<?php echo $favorite[0]->comment_ID; ?>"><?php _e("Remove Favorite", ET_DOMAIN);?></a>
                                        </div>
                                        <?php } ?>                                                                                                                                  
                                        <div class="place-report">
                                             <?php if( empty($report) ){ ?>
                                                <i class="fa fa-flag" aria-hidden="true"></i>
                                                <a class="<?php if($user_ID) {echo 'report';} else {echo 'authenticate';} ?>" id="report_<?php echo $post->ID; ?>" data-user="<?php echo $current_user->ID ?>" data-id="<?php echo $post->ID; ?>"><?php _e("Report", ET_DOMAIN);?></a>
                                             <?php } else {?>
                                                <i class="fa fa-flag" aria-hidden="true"></i>
                                                <a href= "#"><?php _e("Reported", ET_DOMAIN);?></a>
                                             <?php } ?> 
                                        </div>                                  
                                </div>
                            </div>
                            <div class="des-place-wrapper">
                                <h2 class="title-des"><?php _e("Description:", ET_DOMAIN); ?></h2>
                                <div class="content">
                                	<?php the_content(); ?>
                                </div>
                                <?php echo get_the_term_list($post, 'place_tag', '<div class="place-meta"><span class="tag-links">', '', '</span></div>' ); ?>
                            </div>
                            <a data-user="<?php echo $place->post_author; ?>" href="<?php if(is_user_logged_in()) { echo 'javascript:void(0)'; }else { echo et_get_page_link('login', array('redirect' => get_permalink($place->ID))) ; } ?>" class="print-no contact-owner-link <?php if(is_user_logged_in()) { echo 'contact-owner'; }else { echo 'authenticate'; } ?>">
                                <?php _e("CONTACT OWNER", ET_DOMAIN); ?>
                            </a>
                            <?php if($place->cover_image_url) {
                                echo '<div class="mobile-map-wrapper">';
                                get_template_part('mobile/template/section' , 'map'); 
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tabs 1 / End -->    
            <!-- Tabs 2 / Start -->
            <div class="tab-pane fade body-tabs" id="gallery_place">	
            	<div class="container">
                    <div class="row">
                        <ul class="gallery-image">
                            <?php 
                                $attachment = get_children( array(
                                    'numberposts' => 15,
                                    'order' => 'ASC',
                                    'post_mime_type' => 'image',
                                    'post_parent' => $post->ID,
                                    'post_type' => 'attachment'
                                  ),OBJECT );

                                foreach ($attachment as $key => $att) {
                                    $image = wp_get_attachment_image_src( $att->ID, 'thumbnail' );
                                    $image_full = wp_get_attachment_image_src( $att->ID, 'full' );
                                    echo    '<li class="col-xs-4">
                                                <a class="fancybox" title="'. get_the_title() .'" href="'. $image_full[0] .'">
                                                    <img alt="'. get_the_title() .'" src="'. $image[0] .'">
                                                </a>
                                            </li>';
                                }              
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Tabs 2 / End --> 
            <!-- Tabs 3 / Start -->
            <div class="tab-pane fade body-tabs" id="event_place">	
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                        <?php get_template_part('mobile/template/single-place', 'events'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tabs 3 / End -->
            <!-- Tabs 4 / Start -->
            <div class="tab-pane fade body-tabs" id="review_place">	
            	<div class="container">
                    <div class="row">
                    <?php comments_template('/mobile/comments.php'); ?>
                    </div>
                </div>
            </div>
            <!-- Tabs 4 / End -->
        </div>
    </div>
</section>
<!-- Tabs / End -->
<?php
    ob_start();
    wp_title( '|', true, 'right' );
    $pageTitle = ob_get_clean();
    $args = array('link' => get_permalink($post->ID), 'pageTitle' => $pageTitle,  'id' => $post->ID, 'ID' => $post->ID);
    
    $array_place = (array)$place;
    $args = wp_parse_args(  $array_place, $more);  
 ?>
<!-- Single Place / End -->
<script type="json/data" id="place_id"><?php echo json_encode($args); ?></script> 
<script type="text/javascript">
    var hash = window.location.hash.substring(1);
    if (hash.length !== 0) { 
        jQuery('.tab-content').children().removeClass('active in');
        jQuery('.tab-content').children('#review_place').addClass('active in');
        jQuery('.list-place-info').children('li').removeClass('active');
        jQuery('.list-place-info').children('.review_place').addClass('active');
    }
</script>
<?php
}

et_get_mobile_footer();

