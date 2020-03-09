<?php
get_header();
global $ae_post_factory, $post, $wp_query;

// default args
$args = array(
    'post_type' => 'place', 
    'paged' => get_query_var( 'paged' ),
    'post_status' => array('publish')
);
$wp_query   =   new WP_Query($args);
$found_posts = '<span class="found_post">'.$wp_query->found_posts.'</span>';
$plural = sprintf(__('%s places ',ET_DOMAIN), $found_posts);
$singular = sprintf(__('%s place',ET_DOMAIN),$found_posts);
$convert = false;
?>
<div>
    <!-- Bar Post Place -->
    <section id="bar-post-place-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-9 col-xs-8">
                    <h2 class="top-title-post-place">                  
                        <span class="found_search plural <?php if( $found_posts > 1 ) { echo 'hide'; } ?>" >
                            <?php echo $plural; ?>
                        </span>
                        <span class="found_search singular <?php if( $found_posts <= 1 ) { echo 'hide'; } ?>">
                            <?php echo $singular; ?>
                        </span>
                    </h2>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
get_footer();