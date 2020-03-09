
<?php  
et_get_mobile_header();
global $wp_query, $ae_post_factory;
// default args
$args = array(
    'post_type' => 'place', 
    'paged' => get_query_var( 'paged' ) , 
    // 'meta_key' => 'rating_score',
    'post_status' => array('publish'),
    's' => $_REQUEST['s'], 
    'radius' => 10
);

if(isset($_REQUEST['l']) && $_REQUEST['l'] != '') {
    $args['tax_query'] = array(
        array(
            'taxonomy'=>'location',
            'field'=> 'slug',
            'terms'=> $_REQUEST['l']
            )
        );
}
if(isset($_REQUEST['c']) && $_REQUEST['c'] != '') {
    $args['place_category'] = $_REQUEST['c'];
}
/**
 * generate nearby center
*/
if(isset($_REQUEST['center']) && $_REQUEST['center'] != '') {
    $center = explode(',', $_REQUEST['center']);
    $args['near_lat'] = $center[0];
    $args['near_lng'] = $center[1];
    unset($_REQUEST['center']);
    $args['radius'] = $_REQUEST['radius'] ;
}
// nearby radius

if($_REQUEST['days']) {
    $args['date_query'] = array(
        array(
            'column' => 'post_date_gmt',
            'after' => ($_REQUEST['days'] > 1) ? $_REQUEST['days'].' days ago ' : '1 day ago ',
        )
    );
}

$place_obj = $ae_post_factory->get('place');

// $search_query    =   $place_obj->nearbyPost($args);
$search_query    =   new WP_Query($args);
$wp_query = $search_query;
if ($wp_query->found_posts > 1) {
    $status = sprintf(__('%d places', ET_DOMAIN) , $wp_query->found_posts );
} else {
    $status = sprintf(__('%d place', ET_DOMAIN) , $wp_query->found_posts );
}
$convert = false;


?>
<div id="place-list-wrapper" >
<!-- Top bar -->
    <section id="top-bar" class="section-wrapper"> 
        <div class="container">
            <div class="row">
                <div class="col-xs-6">
                    <h1 id ="number_place" class="title-page"><?php echo $status; ?></h1>
                </div>
                <div class="col-xs-6">
                    <div class="section-wrapper">
                        <?php ae_tax_dropdown( 'place_category', 
                                            array( 'hierarchical' => true, 
                                                    'hide_empty' => true, 
                                                    'show_option_all' => __("Categories", ET_DOMAIN),
                                                    'value' => 'slug'
                                                )); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Top bar / End -->
    
    <!-- Top bar -->
    <section class="section-wrapper" > 
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ul class="list-places fullwidth " id="place-list">
                    <?php 
                        global $ae_post_factory, $user_ID, $post;
                        $data_arr = array();
                        while($search_query->have_posts()) { $search_query->the_post();
                            $place_obj = $ae_post_factory->get('place');
                            // covert post
                            $convert = $place_obj->convert($post, 'thumbnail');
                            $data_arr[] = $convert;
                            get_template_part( 'mobile/template/loop', 'place' );
                        }
                    ?>           
                    </ul>
                    <?php                     
                    echo '<script type="json/data" class="postdata" > ' . json_encode($data_arr) . '</script>'; 
                    ae_pagination($search_query, 1, 'load_more');
                    ?>

                </div>
            </div>
        </div>
    </section>
</div>
    <?php  et_get_mobile_footer(); ?>