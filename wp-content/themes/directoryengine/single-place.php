<?php
do_action("before_single_place");
get_header();

if(have_posts()) { the_post();
    global $post, $ae_post_factory , $current_user;
    $place_obj = $ae_post_factory->get('place');
    $place = $place_obj->convert($post , 'big_post_thumbnail');


    $place_marker = array('ID'=> $place->ID, 'post_title'=> $place->post_title, 'permalink'=> $post->guid, 'latitude' => $place->et_location_lat, 'longitude'=> $place->et_location_lng );
    $sum = 0;
    $cats = $place->tax_input['place_category'];
    if(isset($cats['0'])){
        $sum = $cats['0']->count;
        $place_marker = wp_parse_args(array('term_taxonomy_id'=> $cats['0']->term_id), $place_marker);
    }
    // if($cats){
    //     foreach ($cats as $key => $value) {
    //        $sum += $value->count;
    //     }
    echo '<script type="data/json"  id="total_place">'. json_encode(array('number' => $sum, 'current_place'=> $place_marker) ) .'</script>';     
    if(isset($cats['0']->slug)){
        echo '<script type="data/json"  id="place_cat_slug">'. json_encode(array('slug' => $cats['0']->slug) ) .'</script>';     
    }
}

if( $place->cover_image ) {
    $cover = $place->cover_image;
    $cover_image_url = wp_get_attachment_image_src( $cover, 'full' );
?>  
    <div class="google-map-wrapper">
        <?php get_template_part( 'template/section', 'video' ); ?>
        <!-- cover image -->
        <section id="single-place-cover" style="background:url(<?php echo $cover_image_url[0]; ?>) no-repeat center center / cover cadetblue;">

        </section>
    </div>
    <!-- cover image / End -->
<?php 
}else {
    get_template_part('template/section' , 'map');    
}

 ?>
	<!-- Single Place -->
    <section id="single-place" class="<?php if(!et_load_tablet()) echo 'not_is_tablet'; else echo 'is_tablet'; ?>">
    	<div class="container">
        	<div class="row">
            	<!-- Column left -->
            	<div class="col-md-9 col-xs-12">
                	<div class="single-place-wrapper " data-id="<?php the_ID(); ?>" id="main-single">
                        <?php
                            get_template_part( 'template/single-place', 'option-list' );
                         ?>
                        <div class="detail-place-right-wrapper cat-<?php echo $place->place_category[0]; ?>">
                            <div itemscope itemtype="http://schema.org/Place" >
                                <?php
                                    get_template_part('template/single-place', 'breadcrumb');
                                    get_template_part('template/single-place', 'details');
                                    get_template_part('template/single-place', 'events');
                                    comments_template();
                                ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div id="single-more-place" ></div>
                    <div class="paginations-wrapper">
                        <div class="paginations">
                            <a id="post-inview" class=" inview load-more-post"><?php _e("Load more", ET_DOMAIN); ?></a>
                        </div>
                    </div>
                </div>
                <!-- Column left / End -->
            
                <!-- Column right -->
                <?php get_sidebar( 'single' ); ?>            
                <!-- Column right / End -->
            </div>
        </div>
    </section>
    <?php
        ob_start();
        wp_title( '|', true, 'right' );
        $pageTitle = ob_get_clean();
     ?>
    <!-- Single Place / End -->
    <?php 
        $args = array('link' => get_permalink($post->ID), 'pageTitle' => $pageTitle,  'id' => $post->ID, 'ID' => $post->ID);
        $array_place = (array)$place;
        $args = wp_parse_args(  $array_place, $more);  
    ?>
    <script type="json/data" id="place_id" class="place_id_<?php  echo $post->ID ; ?>"><?php echo json_encode( $args); ?></script> 
    <input type="hidden" id="current_user_id" value="<?php echo get_current_user_id(); ?>" />
    <?php
        //Get place related
        $de_place = new DE_PlaceAction();
        $related_place = $de_place->de_get_related_place($place->ID);
        $arr_related = array();
        foreach ($related_place as $key=>$value) {
            $arr_related[] = $value->ID;
        }
        ?>
        <script type="json/data" id="json_related_place">
            <?php
                echo json_encode($arr_related);
            ?>
        </script>
    <?php
    get_footer();



