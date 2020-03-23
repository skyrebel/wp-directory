<?php
/**
 * Template Name: Front Page ver 2
*/

get_header('ver2');
global $de_place_query, $post;;

$args = array(
    'post_type' => 'place', 
    'paged' => get_query_var( 'paged' ) , 
    //'meta_key' => 'rating_score',
    'post_status' => array('publish')
);

$de_place_query    =   new WP_Query($args);

if ($de_place_query->found_posts > 1) {
    $status = sprintf(__('%d places', ET_DOMAIN) , $de_place_query->found_posts );
} else {
    $status = sprintf(__('%d place', ET_DOMAIN) , $de_place_query->found_posts );
}
// get_template_part('template/section' , 'map');

if(have_posts()) { the_post();
    
?>
    <!-- Bar Post Place -->
    <section id="bar-post-place-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-9 col-xs-8">
                    <h2 class="top-title-post-place">
                        <?php echo $status; ?>
                    </h2>
                </div>
                <div class="col-md-3 col-xs-4">
                    <a href="<?php echo et_get_page_link('post-place'); ?>" class="btn btn-post-place">
                        <i class="fa fa-map-marker"></i>
                        <?php _e("Submit a place", ET_DOMAIN); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <!-- Bar Post Place / End -->
    
    <!-- Page Blog -->
    <section id="blog-page">
        <div class="container">
            <div class="section-detail-wrapper padding-top-bottom-20">
                <?php 
                    the_content(); 
                ?>
            </div>
        </div>
    </section>
    <!-- Page Blog / End -->   

<?php
}

get_footer();

