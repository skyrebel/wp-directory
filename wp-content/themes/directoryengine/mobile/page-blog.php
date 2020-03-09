<?php
et_get_mobile_header();
?>

    <!-- Top bar -->
	<section id="top-bar" class="section-wrapper"> 
    	<div class="container">
        	<div class="row">
            	<div class="col-xs-6">
                	<h1 class="title-page">
                		<?php the_title(); ?>
                    </h1>
                </div>
            </div>
        </div>
    </section>
    <!-- Top bar / End -->
    
    <?php
    $args = array(
    'post_type' => 'post', 
    'post_status'=> 'publish',
    'orderby'=>'date',
    'order'> 'DESC'
    );
    query_posts($args);
    get_template_part( 'mobile/template/publish' , 'posts');
    wp_reset_query();
et_get_mobile_footer();