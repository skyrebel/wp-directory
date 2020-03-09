<?php
et_get_mobile_header();
?>

    <!-- Top bar -->
	<section id="top-bar" class="section-wrapper"> 
    	<div class="container">
        	<div class="row">
            	<div class="col-xs-6">
                	<h1 class="title-page"><?php _e("Blog", ET_DOMAIN); ?></h1>
                </div>
            </div>
        </div>
    </section>
    <!-- Top bar / End -->
    
    <?php
        get_template_part( 'mobile/template/publish' , 'posts');
     ?>

<?php
et_get_mobile_footer();