<?php
get_header();

// get_template_part('template/section' , 'map');
get_template_part( 'template/section', 'status' );
?>
    
    <!-- List Place -->
    <section id="list-places-wrapper">
        <div class="container">
            <?php 
                if(ae_user_can('manage_options')) {
                    get_template_part('template/pending' , 'places');
                }
                get_sidebar( 'top' );                
            ?>
            <!-- place list with sidebar -->
            <div class="row">
                <?php 
                    get_template_part( 'template/publish' , 'places' );
                    get_sidebar(); 
                ?>
            </div>
            <?php get_sidebar( 'bottom' );?>
        </div>
    </section>
    <!-- List Place / End -->  

<?php
get_footer();

