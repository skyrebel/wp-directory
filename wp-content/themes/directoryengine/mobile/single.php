<?php
et_get_mobile_header();
if(have_posts()) { the_post();
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
    
    <!-- List News -->
	<section id="list-news" class="section-wrapper"> 
    	<div class="container">
        	<div class="row">
            	<div class="col-xs-12">
                	<div class="news-wrapper-single">
                        <h2 class="title-news">
                        	<a href="#"><?php the_title(); ?></a>
                            <span class="time"><i class="fa fa-calendar"></i><?php the_date(); ?></span>
                        </h2>
                        <div class="content-news">
                            <?php the_content(); ?>
                        </div>
                        <?php 
                        comments_template();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- List News / End -->
<?php
}
et_get_mobile_footer();