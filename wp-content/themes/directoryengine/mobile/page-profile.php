<?php
/**
 * Template Name: Page Profile
 * version 1.0
 * @author: enginethemes
 */

if(!is_user_logged_in()){
    wp_redirect(home_url());
    exit;
}

global $wp_query, $wp_rewrite, $current_user,$de_place_query , $user_ID,$post;

et_get_mobile_header();
$user       = get_user_by( 'id', $user_ID );
$ae_users   = AE_Users::get_instance();
$user       = $ae_users->convert($user);


/**
 * get author current section
*/
$review_url =   ae_get_option('author_review_url', 'reviews');
$togo_url   =   ae_get_option('author_togo_url', 'togos');

$current_section  = 'places';
// set current section to reviews lists
if(isset($wp_query->query_vars['author_tab']) ) {
    switch ($wp_query->query_vars['author_tab']) {
        case 'reviews':
            $current_section = 'reviews';
            break;
        case 'togos' :
            $current_section = 'togos';
            break;
        case 'pending' : 
            $current_section = 'pending';
            if($user_ID != $user->ID ) wp_redirect( home_url() );
            break;
        default:            
            break;
    }
}
?>
<!-- Top bar -->
    <section id="top-bar" class="section-wrapper profile"> 
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-9">
                        <h1 class="title-page">
                        <?php printf(__("%s profile", ET_DOMAIN), $user->display_name); ?>
                        </h1>
                    </div>
                    <div class="col-xs-3">
                        <a class="logout" href="<?php echo wp_logout_url(home_url()); ?>">
                            <?php 
                                if(strlen(__("Logout", ET_DOMAIN)) > 7){
                                    echo "<i class='fa fa-sign-out'></i>";                                
                                }else{
                                    _e("Logout", ET_DOMAIN); 
                                }
                            ?>
                        </a>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Top bar / End -->
    
    <!-- List News -->
    <section id="info-wrapper" class="section-wrapper"> 
        <div class="container">
            <ul class="nav nav-tabs list-profile-tabs">
                <li class="active col-xs-6"><a href="#user-info" data-toggle="tab"><?php _e('User Info',ET_DOMAIN);?></a></li>
                <li class="col-xs-6"><a href="#package-info" data-toggle="tab"><?php _e('Package Info',ET_DOMAIN);?></a></li>
            </ul>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="tab-content">
                        <div id="user-info" class="tab-pane fade in active">
                            <div class="info-user-wrapper">
                                <div class="avatar-user"><?php echo get_avatar($user->ID, 150); ?></div>
                                <h2 class="name-user"><?php echo $user->display_name; ?></h2>
                                <div class="clearfix"></div>
                                <div class="detail-info">
                                    <p><i class="fa fa-map-marker"></i>
                                    	<?php echo $user->location ? $user->location : __('Earth', ET_DOMAIN) ?>
                                    </p>
                                    <p><i class="fa fa-envelope"></i>
                                    	<?php echo $user->user_email ? '<a href="mailto:'.$user->user_email.'">'.$user->user_email.'</a>' : __('No Email', ET_DOMAIN);?>
                                    </p>
                                    <p><i class="fa fa-phone">
                                    	</i><?php echo $user->phone ? $user->phone : __('No phone', ET_DOMAIN) ?>
                                    </p>
                                    <p><i class="fa fa-facebook-square"></i>
                                    	<?php echo $user->facebook ? '<a target="_blank" href="'.$user->facebook.'">'.$user->facebook.'</a>' : '<a href="#">'.__('No facebook', ET_DOMAIN).'</a>' ?>
                                    </p>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div id="package-info" class="tab-pane fade">
                        <?php 
                        $ae_pack = $ae_post_factory->get('pack');
                        $packs = $ae_pack->fetch();
                        $orders = AE_Payment::get_current_order($user->ID);
                        $package_data = AE_Package::get_package_data($user->ID);
                        $count_qty = 0;
                        foreach ($packs as $package){
                            $sku = $package->sku;
                            if (isset($package_data[$sku]) && $package_data[$sku]['qty'] > 0){
                                $count_qty++;
                            }
                        }
                        if(count($packs) > 0 && $count_qty > 0):
                        foreach ($packs as $package) :
                            $number_of_post = $package->et_number_posts;
                            $sku = $package->sku;
                            $text = '';
                            if (isset($package_data[$sku]) && $package_data[$sku]['qty'] > 0) :
                                $number_post = $package->et_number_posts;
                                $order = get_post($orders[$sku]);
                                $number_of_post = $package_data[$sku]['qty'];

                                // Total event place
                                $args_event = array(
                                    'post_type'         => 'place',
                                    'author'            => $user->ID,
                                    'posts_per_page'    => -1,
                                    'post_status'       => array('publish','pending','archive','reject','draft'),
                                    'meta_query'        => array(
                                        array(
                                            'key'       => 'et_payment_package',
                                            'value'     => $sku,
                                            'compare'   => '=',
                                        ),
                                        array(
                                            'key'       => 'de_event_post',
                                            'value'     => '',
                                            'compare'   => '!=',
                                            'type'      => 'NUMERIC'
                                        ),
                                    )
                                );
                                $query_event_post = new WP_Query($args_event);
                                $arr_post = array();
                                // foreach ID post_parent 
                                foreach ($query_event_post->posts as $key => $value) {
                                    $arr_post[] = $value->ID;
                                }
                        ?>
                            <h2><?php echo (isset($package->post_title)) ? $package->post_title : __('No Package',ET_DOMAIN);?>
                                <span style="float:right;">
                                    <?php 
                                    if(isset($package->post_title)){
                                        echo (!$package->et_price) ? __('Free',ET_DOMAIN) : ae_price($package->et_price);
                                    }
                                    ?>
                                </span>
                            </h2>
                            <p><span class="text"><i class="fa fa-pagelines"></i>
                            	<?php _e('Total Place',ET_DOMAIN);?>:</span>
                            	<span class="number">
                            		<?php echo $number_post - $number_of_post.'/'.$number_post ; ?>
                                </span>
                            </p>
                            </br>
                        <?php 
                            wp_reset_query();
                            endif;
                        endforeach;
                        else:
                        ?>
                        <div class="content-package">
                            <h3><?php echo _e('No Package',ET_DOMAIN);?>
                        </div>
                        <?php endif;?>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
    <!-- List News / End -->
    
    <!-- Tabs -->
    <section id="tabs-user-review-wrapper" class="section-wrapper">
        <ul class="nav nav-tabs list-user-info" role="tablist" id="myTab">
            <li class="<?php if($current_section  == 'places'){ echo  'active'; } ?>">
                <a href="#user_place" role="tab" data-toggle="tab">
                    <i class="fa fa-pagelines"></i>
                    <?php _e("Places", ET_DOMAIN); ?>
                </a>
            </li>
            <li class="<?php if($current_section  == 'events'){ echo  'active'; } ?>">
                <a href="#user_events" role="tab" data-toggle="tab">
                    <i class="fa fa-ticket"></i><?php _e("Events", ET_DOMAIN); ?>
                </a>
            </li>
            <li class="<?php if($current_section  == 'reviews'){ echo  'active'; } ?>">
                <a href="#user_review" role="tab" data-toggle="tab">
                    <i class="fa fa-star"></i><?php _e("Reviews", ET_DOMAIN); ?>
                </a>
            </li>
            <li class="<?php if($current_section  == 'togos'){ echo  'active'; } ?>">
                <a href="#user_togo" role="tab" data-toggle="tab">
                    <i class="fa fa-pagelines"></i><?php _e("Togos", ET_DOMAIN); ?>
                </a>
            </li>
            <li>
                <a href="#user_picture" role="tab" data-toggle="tab">
                    <i class="fa fa-file-image-o"></i><?php _e("Pictures", ET_DOMAIN); ?>
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Tabs 1 / Start -->
            <div class="tab-pane fade active body-tabs in" id="user_place">
                <?php get_template_part('mobile/template/profile','place');?>
            </div>
            <!-- Tabs 1 / End -->   
            <!-- Tabs 2 -->
            <div class="tab-pane fade body-tabs" id="user_events">  
                <?php get_template_part('mobile/template/profile','event');?>
            </div> 
            <!--// Tabs 2 -->
            <!-- Tabs 3 / Start -->
            <div class="tab-pane fade body-tabs" id="user_review">  
                <?php get_template_part('mobile/template/profile','review');?>
            </div>
            <!-- Tabs 3 / End -->
            <!-- Tabs 4 / Start -->
            <div class="tab-pane fade body-tabs" id="user_togo">    
                <?php get_template_part('mobile/template/profile','togo');?>
            </div>
            <!-- Tabs 4 / End -->
            <!-- Tabs 5 / Start -->
            <div class="tab-pane fade body-tabs" id="user_picture">    
                <?php get_template_part('mobile/template/profile','picture');?>
            </div>
            <!-- Tabs 5 / End -->

        </div>
    </section>
    <!-- Tabs / End -->

<?php
et_get_mobile_footer();