<?php
$session    =   et_read_session ();
global $ad , $payment_return, $order_id, $user_ID;

$payment_type   = get_query_var( 'paymentType' );
if($payment_type == 'usePackage' || $payment_type == 'free' ){
    $payment_return = ae_process_payment($payment_type, $session);
     if($payment_return['ACK']) {
        $place_url = get_the_permalink($session['ad_id']);
        // Destroy session for order data
        et_destroy_session();
        // Redirect to project detail
        wp_redirect($place_url);
        exit;
    }
}

/**
 * get order
 */
$order_id = isset($_GET['order-id']) ? $_GET['order-id'] : '';
$order = new AE_Order($order_id);
$order_data = $order->get_order_data();
$ad = get_post($order_data['product_id']);
if($order_id && (current_user_can('manage_options') || $order_data['payer'] == $user_ID)){
    et_get_mobile_header();
    ?>
    <section class="container">
        <div id="order-payment">
            <h6><?php _e("Order Received", ET_DOMAIN); ?></h6>
            <p class="text"><?php _e("Thank you. Your order has been received!", ET_DOMAIN); ?></p>
            <div class="invoice-detail">
                <div class="row list-payment-cash">
                    <div class="name">
                        <div class="col-xs-12"><?php _e("Invoice no.", ET_DOMAIN); ?></div>
                        <div class="col-xs-12"><?php _e("Date", ET_DOMAIN); ?></div>
                        <div class="col-xs-12"><?php _e("Payment type", ET_DOMAIN); ?></div>
                        <div class="col-xs-12"><?php _e("Total", ET_DOMAIN); ?></div>
                    </div>
                    <div class="info">
                        <div class="col-xs-12"><?php echo $order_data['ID']; ?></div>
                        <div class="col-xs-12"><?php echo get_the_date(get_option('date_format'), $order_id); ?></div>
                        <div class="col-xs-12">
                            <?php
                                $payment_method_txt_arr = array(
                                    'paypal'    =>  '<p class="paypal">' . __('PayPal', ET_DOMAIN) . '</p>',
                                    'cash'      => '<p class="cash">'. __('Cash', ET_DOMAIN) .'</p>',
                                    '2checkout' => '<p class="checkout">'. __('2Checkout', ET_DOMAIN) .'</p>',
                                    'stripe'    => '<p class="stripe">' . __('Stripe', ET_DOMAIN),
                                    'molpay'    => '<p class="molpay">' . __('MOLPay', ET_DOMAIN),
                                    'pin'       => '<p class="pin">' . __('Pin', ET_DOMAIN),
                                    'payf'      => '<p class="payf">' . __('PayFast', ET_DOMAIN),
                                    'paymill'   => '<p class="paymill">' . __('Paymill', ET_DOMAIN),
                                    'sagepay'   => '<p class="sagepay">' . __('SagePay', ET_DOMAIN),
                                    'payu'      => '<p class="payu">' . __('PayUMoney', ET_DOMAIN),
                                );
                            ?>
                        <?php echo $payment_method_txt_arr[$order_data['payment']]; ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><?php ae_price($order_data['total'])?></div>
                    </div>
                </div>
            </div>
            <?php 
            if($order_data['payment'] == 'cash'){
                $cash_options = ae_get_option('cash');
                $cash_message = $cash_options['cash_message'];
                ?>
                <div class="invoice-note">
                    <p class="type-cash"><?php _e("CASH NOTE", ET_DOMAIN); ?></p>
                    <?php echo $cash_message; ?>
                </div>
                <?php
            }
        ?>
            <a href="<?php echo get_the_permalink($ad->ID); ?>" class="btn-back-list"><?php _e("VIEW LISTING DETAILS", ET_DOMAIN); ?> <i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
        </div>
    </section>
    <?php
    if($order_id && !get_post_meta($order_id, 'et_order_is_process_payment')) {
        //processs payment
        $payment_type = $order_data['payment'];
        $payment_return = ae_process_payment($payment_type , $session );
        update_post_meta($order_id, 'et_order_is_process_payment', true);
        et_destroy_session();
    }
    et_get_mobile_footer();
}else{
    // Redirect to 404
    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 ); exit();
}