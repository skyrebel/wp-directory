<?php 
/**
 *	Template Name: Cancel Payment
 */

get_header();

?>
    <section class="container">
        <div id="order-payment">
            <h6 class="text-notify"><?php _e("Your order was cancelled", ET_DOMAIN); ?></h6>
            <p class="text-cancel"><?php _e("It seems that you are busy at this moment, so order it again when you're free.", ET_DOMAIN); ?></p>
            <a href="<?php echo home_url(); ?>" class="btn-back-list">BACK TO HOMEPAGE <i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
        </div>
    </section>
<?php
get_footer();