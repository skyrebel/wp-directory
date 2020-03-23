

<?php  
    et_get_mobile_header(); 
    
?>
<!-- Top bar -->
<div id="place-list-wrapper" >
    <section id="top-bar" class="section-wrapper"> 
        <div class="container">
            <div class="row">
                <div class="col-xs-6">
                    <h1 class="title-page"><?php _e("Places", ET_DOMAIN); ?></h1>
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
    <section class="section-wrapper"> 
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?php 
                        query_posts( array('post_type' => 'place', 'post_status' => 'publish') );
                        get_template_part( 'mobile/template/publish', 'places' );
                    ?>
                </div>
            </div>
        </div>
    </section>
</div>
    <?php  et_get_mobile_footer(); ?>