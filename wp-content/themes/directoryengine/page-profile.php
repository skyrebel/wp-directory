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

$user       = get_user_by( 'id', $user_ID );
$ae_users   = AE_Users::get_instance();
$user       = $ae_users->convert($user);
$query_vars = $wp_query->query_vars;

/**
 * get author current section
*/
$review_url =   ae_get_option('author_review_url', 'reviews');
$togo_url   =   ae_get_option('author_togo_url', 'togos');

$current_section  = 'places';
get_header();

?>

<!-- Breadcrumb Blog -->
<div class="section-detail-wrapper breadcrumb-blog-page">
    <ol class="breadcrumb">
        <li><a href="<?php echo home_url() ?>" title="<?php echo get_bloginfo( 'name' ); ?>" ><?php _e("Home", ET_DOMAIN); ?></a></li>
        <li><a href="#" title="<?php echo $user->display_name ?>"><?php printf(__( 'Profile of %s' , ET_DOMAIN ), $user->display_name); ?></a></li>
    </ol>
</div>
<!-- Breadcrumb Blog / End -->

<div class="wrapper_profile">
    <div class="container">
        <div class="row">
            <div class="col-md-3 left-pad-right">
                <div class="col-left-profile">
                    <div class="left-profile">
                        <div class="user-info">
                            <div class="bar-info">
                                <h3 class="visible-md visible-lg"><?php _e('User Info',ET_DOMAIN);?></h3>
                                <h3 class="visible-sm visible-xs"><?php _e('User Info Of ',ET_DOMAIN);?><?php echo $user->display_name?></h3>
                                <?php if(is_user_logged_in()){ ?>
                                    <a href="#" class="edit-profile">
                                        <span id="edit-user">
                                            <i class="fa fa-pencil"></i>
                                        </span>
                                    </a>
                                <?php }?>
                            </div>
                            <div class="content-info" id="user_avatar_container">
                                <div class="avatar img-author" >
                                    <span class="author-avatar image" id="user_avatar_thumbnail">
                                        <?php echo get_avatar($user->ID, 150); ?>
                                    </span>
                                    <?php if(is_user_logged_in()){ ?>
                                        <a href="#" class="new-look" id="user_avatar_browse_button">
                                            <span>
                                                <i class="fa fa-upload"></i>
                                            </span>
                                        </a>
                                    <?php } ?>
                                    <span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'user_avatar_et_uploader' ); ?>"></span>
                                </div>
                                <div class="username visible-md visible-lg">
                                    <h4><?php echo $user->display_name?></h4>
                                    <!--<h5>Manager</h5>-->
                                </div>
                                <!-- <div class="clearfix"></div> -->
                                <div class="detail-info">
                                    <p class="location"><i class="fa fa-map-marker"></i><span><?php echo $user->location ? $user->location : __('Earth', ET_DOMAIN) ?></span></p>
                                    <p class="email"><i class="fa fa-envelope"></i><span><?php echo $user->user_email ? '<a href="mailto:'.$user->user_email.'">'.$user->user_email.'</a>' : __('No Email', ET_DOMAIN);?></span></p>
                                    <p class="phone"><i class="fa fa-phone"></i><span><?php echo $user->phone ? $user->phone : __('No phone', ET_DOMAIN) ?></span></p>
                                    <p class="facebook"><i class="fa fa-facebook-square"></i>
                                        <span><?php echo $user->facebook ? '<a target="_blank" href="'.$user->facebook.'">'.$user->facebook.'</a>' : '<a href="#">'.__('No facebook', ET_DOMAIN).'</a>' ?></span>
                                    </p>
                                </div>
                                <div class="post-place-profile">
                                    <a href="<?php echo et_get_page_link('post-place'); ?>" class="post-place-profile-btn">
                                        <i class="fa fa-map-marker"></i>
                                        <?php _e("Add Your place", ET_DOMAIN); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="pakage-info">
                            
                            <div class="bar-info">
                                <h3 class=""><?php _e('Package Info',ET_DOMAIN);?></h3>
                            </div>
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
                                // Check User had choose Package
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
                                <div class="content-package">
                                    <h3><?php echo (isset($package->post_title)) ? $package->post_title : __('No Package',ET_DOMAIN);?>
                                        <span>
                                            <?php 
                                            if(isset($package->post_title)){
                                                echo (!$package->et_price) ? __('Free',ET_DOMAIN) : ae_price($package->et_price);
                                            }
                                            ?>
                                        </span>
                                    </h3>
                                    <p>
                                        <span class="text"><i class="fa fa-pagelines"></i><?php _e('Total Place',ET_DOMAIN);?>:</span>
                                        <span class="number">
                                            <?php echo $number_post - $number_of_post .'/'. $number_post; ?>
                                        </span>
                                    </p>
                                </div>
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
            <div class="col-md-9 right-pad-left">
                <div class="col-right-profile">
                    <div class="right-profile">
                        <div class="tabs-rigth-profile">
                            <ul class="nav nav-tabs list-info-user-tabs">
                                <li class="<?php if($current_section  == 'places'){ echo  'active'; }?>">
                                    <a href="#tab-place" data-toggle="tab">
                                        <i class="fa fa-pagelines"></i>
                                        <?php
                                            $array_total_place = array('author'=>$user->ID, 'post_status'=>array('publish','pending','reject','archive','draft'), 'post_type' => 'place','posts_per_page' => -1);
                                            $query_total_place= new WP_Query( $array_total_place );
                                            $post_total = $query_total_place->found_posts;
                                            if($post_total > 1){
                                                printf(__('<span class="text">Places</span><span class="number">(%s)</span>',ET_DOMAIN), $post_total);
                                            }else{
                                                printf(__('<span class="text">Place</span><span class="number">(%s)</span>',ET_DOMAIN), $post_total);
                                            }
                                        ?>
                                    </a>
                                </li>
                                <li class="<?php if($current_section  == 'events'){ echo  'active'; } ?>">
                                    <a href="#tab-event" data-toggle="tab">
                                        <i class="fa fa-ticket"></i>
                                        <?php

                                            $total_events = ae_count_user_posts_by_type($user->ID, 'event');
                                            if($total_events > 1){
                                                printf(__('<span class="text">Events</span><span class="number">(%s)</span>',ET_DOMAIN), $total_events);
                                            }else{
                                                printf(__('<span class="text">Event</span><span class="number">(%s)</span>',ET_DOMAIN), $total_events);
                                            }  
                                        ?>
                                    </a>
                                </li>
                                <li class="<?php if($current_section  == 'reviews'){ echo  'active'; }?>">
                                    <a href="#tab-review" data-toggle="tab">
                                        <i class="fa fa-star"></i>
                                        <?php 
                                            $total_review = get_comments(array(
                                                'user_id' => $user->ID, 
                                                'type' => 'review', 
                                                'status' => 'approve',
                                                'meta_query' => array(
                                                    'relation' => 'AND',
                                                    array(
                                                        'key'       => 'et_rate_comment',
                                                        'value'     => '0',
                                                        'compare'   => '>'
                                                    )
                                                ),
                                                'post_status' => array('publish'),
                                                'post_type'   => 'place'
                                            ));
                                            $review_total  = count($total_review);
                                            if($review_total > 1 )  {
                                                printf(__('<span class="text">Reviews</span><span class="number">(%s)</span>', ET_DOMAIN) , $review_total);
                                            }else {
                                                printf(__('<span class="text">Review</span><span class="number">(%s)</span>', ET_DOMAIN) , $review_total);
                                            }
                                        ?>
                                    </a>
                                </li>
                                <li class="<?php if($current_section  == 'togos'){ echo  'active'; }?>">
                                    <a href="#tab-togo" data-toggle="tab">
                                        <i class="fa fa-pagelines"></i>
                                        <?php
                                            $togos = get_comments( array(
                                                'user_id' => $user->ID,
                                                'type'        => 'favorite',
                                                'status'      => 'approve',
                                                'post_status' => array('publish'),
                                                'post_type'   => 'place'
                                            ) );

                                            $total_togo = count($togos);
                                            printf(__('<span class="text">Togos</span><span class="number">(%s)</span>',ET_DOMAIN), $total_togo);
                                        ?>
                                    </a>
                                </li>
                                <li class="<?php if($current_section  == 'pictures'){ echo  'active'; }?>">
                                    <a href="#tab-picture" data-toggle="tab">
                                        <i class="fa fa-file-image-o"></i>
                                        <?php 
                                            add_filter( 'posts_orderby', 'order_by_post_status' );
                                            $pending = new WP_Query(array(
                                                'post_type'   => array('place','post'),
                                                'post_status' => array('publish'),
                                                'author' => $user->ID,
                                                'posts_per_page' => -1,
                                            ));
                                            remove_filter( 'posts_orderby', 'order_by_post_status' );

                                            $array_id = array();
                                            foreach ($pending->posts as $key => $value) {
                                                $array_id[] = $value->ID;
                                            }

                                            $query_img_args = array(
                                                'author'=> $user->ID,
                                                'post_type' => 'attachment',
                                                'post_mime_type' =>array(
                                                                'jpg|jpeg|jpe' => 'image/jpeg',
                                                                'gif' => 'image/gif',
                                                                'png' => 'image/png',
                                                                ),
                                                'post_status' => 'inherit',
                                                'posts_per_page' => -1,
                                                'post_parent__in' => $array_id

                                            );
                                            
                                            $query = new WP_Query( $query_img_args );
                                            $total_picture = $query->found_posts;
                                            wp_reset_query();
                                            if($total_picture > 1 )  {
                                                printf(__('<span class="text">Pictures</span><span class="number">(%s)</span>', ET_DOMAIN), $total_picture);
                                            }else {
                                                printf(__('<span class="text">Picture</span><span class="number">(%s)</span>', ET_DOMAIN), $total_picture);
                                            }
                                        ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="content-tabs-right-profile tab-content">
                            <?php 
                            get_template_part( 'template/profile-places' );
                            get_template_part( 'template/profile-events' );
                            get_template_part( 'template/profile-reviews' );
                            get_template_part( 'template/profile-togos' );
                            get_template_part( 'template/profile-pictures' );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer();  ?>
