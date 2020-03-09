<?php 
global $ae_post_factory, $user_ID;
$place_obj = $ae_post_factory->get('place');
$post = $place_obj->get_current_post();
$user       = get_user_by( 'id', $post->post_author);
$ae_users   = AE_Users::get_instance();
$user       = $ae_users->convert($user);
/*Get paid status*/
if($post->et_paid==0)
    $paid_status = __("Unpaid", ET_DOMAIN);
elseif ($post->et_paid==1)
    $paid_status = __("Paid", ET_DOMAIN);
else
    $paid_status = __("Free", ET_DOMAIN);
//print_r($post);

?>
<li class="pending-item">
     <div class="pending-place-wrap">
        <a class="pending-place-img" href="<?php the_permalink(); ?>" title="<?php the_title();?>" >
           <img src="<?php echo $post->the_post_thumnail; ?>" alt="<?php the_title();?>" />
        </a>
        <div class="pending-place-content">
            <div class="pending-place-title-wrap">
                <span style="background-color: #d35400; color: #FFF;"> <?php echo get_cat_name( $post->place_category[0]); ?></span>
                <h2><a href="<?php the_title();?>" title="<?php the_title();?>"><?php the_title();?></a></h2>
                <p><i class="fa fa-map-marker"></i><?php echo $post->et_full_location ?></p>
            </div>
           <div class="pending-place-location-wrap">
                <div class="pending-place-location">
                    <p><i class="fa fa-globe"></i> <?php echo get_cat_name( $post->location[0]); ?></p>
                    <p><i class="fa fa-phone"></i><?php echo $post->et_phone ?></p>
                </div>
            </div>
            <div class="pending-place-author-wrap">
                <div class="pending-place-author">
                    <p>
                        <a href="">
                             <?php echo get_avatar($user->ID, 50); ?>
                             <?php echo $user->display_name?>
                        </a>
                    </p>
                </div>
            </div>
            <div class="pending-place-status">
                <span class="unpaid"><?php echo $paid_status; ?></span>
            </div>
            <div class="pending-place-action-wrap">
                <div class="pending-place-action action-pending-place">
                    <span class="action-approve action" data-action="approve"><i class="fa fa-check"></i></span>
                    <span class="action-remove action" data-action="reject"><i class="fa fa-times"></i></span>
                </div>
            </div>
        </div>
    </div>
</li>