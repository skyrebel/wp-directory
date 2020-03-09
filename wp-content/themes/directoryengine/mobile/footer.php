<?php

/**
 * Mobile Footer Template
*/
?>
<?php
if (is_active_sidebar('de-footer-1') || is_active_sidebar('de-footer-2')
    || is_active_sidebar('de-footer-3') || is_active_sidebar('de-footer-4')
) { ?>
    <footer class="<?= (is_front_page()) ? 'homepage' : ''?>">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <?php if (is_active_sidebar('de-footer-1')) dynamic_sidebar('de-footer-1'); ?>
                </div>
                <div class="col-md-3 col-sm-6">
                    <?php if (is_active_sidebar('de-footer-2')) dynamic_sidebar('de-footer-2'); ?>
                </div>
                <div class="col-md-3 col-sm-6">
                    <?php if (is_active_sidebar('de-footer-3')) dynamic_sidebar('de-footer-3'); ?>
                </div>
                <div class="col-md-3 col-sm-6">
                    <?php if (is_active_sidebar('de-footer-4')) dynamic_sidebar('de-footer-4'); ?>
                </div>
            </div>
        </div>
    </footer>
    <!-- FOOTER / End -->
<?php }
$copyright = ae_get_option('copyright');
$has_nav_menu = has_nav_menu('et_footer');
$col = 'col-md-6 col-sm-6';
if ($has_nav_menu) $col = 'col-lg-4';
?>
<!-- Copyright -->

<div class="copyright-wrapper">
    <div class="container">
        <div class="row footer-copyright">
            <?php if (!wp_is_mobile()) : ?>
            <div class="col-sm-6">
                <a href="#"><?php echo ae_get_option('blogname'); ?></a> <?php echo $copyright ?>
            </div>
            <div class="col-sm-6 social-icons">
                <ul class="social-network social-circle">
                    <?php
                          if( ae_get_option('site_linkedin') ) {
                            echo '<li><a href="'. ae_get_option('site_linkedin') .'" class="linkedin-icon"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>';}
                          if( ae_get_option('site_facebook') ) {
                            echo '<li><a href="'. ae_get_option('site_facebook') .'" class="facebook-icon"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>'; }
                          if( ae_get_option('site_twitter') ) {
                            echo '<li><a href="'. ae_get_option('site_twitter') .'" class="twitter-icon"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>';}
                          if( ae_get_option('site_google') ) {
                            echo '<li><a href="'. ae_get_option('site_google') .'" class="google-plus-icon"><i class="fa fa-google-plus" aria-hidden="true"></i></a></li>';}
                          if( ae_get_option('site_vimeo') ) {
                            echo '<li><a href="'. ae_get_option('site_vimeo') .'" class="vimeo-icon"><i class="fa fa-vimeo" aria-hidden="true"></i></a></li>';}
                            ?>
                </ul>
            </div>
            <?php ; else: ?>
            <div class="col-sm-12">
                <a href="#"><?php echo ae_get_option('blogname'); ?></a> <?php echo $copyright ?>
            </div>
            <div class="col-sm-12 social-icons">
                <ul class="social-network social-circle">
                    <?php
                    if( ae_get_option('site_linkedin') ) {
                        echo '<li><a href="'. ae_get_option('site_linkedin') .'" class="linkedin-icon"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>';}
                    if( ae_get_option('site_facebook') ) {
                        echo '<li><a href="'. ae_get_option('site_facebook') .'" class="facebook-icon"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>'; }
                    if( ae_get_option('site_twitter') ) {
                        echo '<li><a href="'. ae_get_option('site_twitter') .'" class="twitter-icon"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>';}
                    if( ae_get_option('site_google') ) {
                        echo '<li><a href="'. ae_get_option('site_google') .'" class="google-plus-icon"><i class="fa fa-google-plus" aria-hidden="true"></i></a></li>';}
                    if( ae_get_option('site_vimeo') ) {
                        echo '<li><a href="'. ae_get_option('site_vimeo') .'" class="vimeo-icon"><i class="fa fa-vimeo" aria-hidden="true"></i></a></li>';}
                    ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
get_template_part( 'mobile/template/js-loop', 'place' );
get_template_part( 'mobile/template/js-loop', 'review' );
get_template_part( 'mobile/template/js-loop', 'blog' );
get_template_part( 'mobile/template/js-loop', 'togo' );
get_template_part( 'mobile/template/js-loop', 'event' );
get_template_part( 'mobile/template/js-loop', 'picture' );
get_template_part( 'mobile/template/js-user', 'item' );
 // user logged in and in author or single place
if( is_user_logged_in() && ( is_author() || is_singular( 'place' ) ) ) {
    get_template_part( 'template/modal', 'contact' );
}
wp_footer();
?>
</body>
</html>