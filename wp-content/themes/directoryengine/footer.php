<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @since DirectoryEngine 1.0
 */
if (!(is_singular() && !is_page_template('page-front.php')) && !is_category() && !is_date() && !is_author()) {
    get_sidebar('fullwidth-bottom');
}
?>
</div>
<!-- FOOTER -->
<?php
if (is_active_sidebar('de-footer-1') || is_active_sidebar('de-footer-2')
    || is_active_sidebar('de-footer-3') || is_active_sidebar('de-footer-4')
) { ?>
    <footer>
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
            <div class="col-sm-8">
                <a href="#"><?php echo ae_get_option('blogname'); ?></a> <?php echo $copyright ?>
            </div>
            <div class="col-sm-4 social-icons">
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
        </div>
    </div>
</div>
<!-- Copyright / End -->
<?php
wp_footer(); ?>
</body>
</html>