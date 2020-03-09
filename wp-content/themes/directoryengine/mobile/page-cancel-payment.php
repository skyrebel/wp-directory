<?php

et_get_mobile_header();
global $ad , $payment_return;
?>
	<section class="container">
		<div id="order-payment">
			<h6 class="text-notify"><?php _e("Your order was cancelled", ET_DOMAIN); ?></h6>
			<p class="text-cancel"><?php _e("It seems that you are busy at this moment, so order it again when you're free.", ET_DOMAIN); ?></p>
			<a href="<?php echo home_url(); ?>" class="btn-back-list"><?php _e("BACK TO HOMEPAGE", ET_DOMAIN); ?><i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
		</div>
	</section>
<?php 
et_get_mobile_footer();