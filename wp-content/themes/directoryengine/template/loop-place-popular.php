<?php
global $ae_post_factory, $user_ID;
$place_obj = $ae_post_factory->get('place');
$post = $place_obj->get_current_post();
$user = get_userdata($post->post_author);
$total_count = get_comments(array('post_id' => $post->ID, 'type' => 'review', 'count' => true, 'status' => 'approve'));
?>


<div class="col-md-6">
    <div class="de-popular-place">
        <div class="popular-img">
            <?php if ($post->the_post_thumnail) { ?>
                <a href="<?php echo the_permalink(); ?>" class="img-place" title="<?php the_title(); ?>">
                    <img src="<?php echo $post->the_post_thumnail; ?>" alt="<?php the_title(); ?>"
                         title="<?php the_title(); ?>">
                </a>
            <?php } ?>
        </div>
        <div class="popular-info">
            <?php if (isset($post->multi_overview_score) && $post->multi_overview_score ) { ?>
                <div class="popular-rating-number"><span><?php echo $post->multi_overview_score; ?></span></div>
            <?php } ?>
            <div class="popular-title">
                <h2>
                    <a href="<?php the_permalink(); ?>"><?php echo mb_strimwidth(get_the_title(), 0, 22, '...'); ?></a>
                </h2>
                <div class="rate-it" data-score="<?php echo $post->rating_score_comment; ?>"></div>
            </div>
            <div class="popular-address">
                <p><i class="fa fa-map-marker"></i><?php echo mb_strimwidth($post->et_full_location, 0, 32, '...'); ?></p>
                <p><i class="fa fa-globe"></i><?php echo $post->tax_input['location'][0]->name; ?></p>
                <p><i class="fa fa-phone"></i><?php $phone = ($post->et_phone) ? $post->et_phone : __("No Phone", ET_DOMAIN); echo $phone; ?></p>
            </div>
            <div class="popular-author">
                <?php echo get_avatar($user->ID, 30); ?>
                <?php echo $user->display_name ?>
                <span><i class="fa fa-commenting" aria-hidden="true"></i><?php echo $total_count ?></span>
            </div>
        </div>
    </div>
</div>

<?php /*
<div class="place-popular col-sx-12 col-sm-6 col-md-6 col-lg-6">
    <div class="row place">
        <div class="popular-image-feature col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <?php if ($post->the_post_thumnail) { ?>
                <a href="<?php echo the_permalink(); ?>" class="img-place" title="<?php the_title(); ?>">
                    <img src="<?php echo $post->the_post_thumnail; ?>" alt="<?php the_title(); ?>"
                         title="<?php the_title(); ?>">
                </a>
            <?php } ?>
        </div>
        <div class="place-pop-info col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="place-brand">
                <div class="brand-title">
                    <h4>
                        <a href="<?php the_permalink(); ?>"><?php echo mb_strimwidth(get_the_title(), 0, 22, '...'); ?></a>
                    </h4>
                    <div class="rate-it" data-score="<?php echo $post->rating_score_comment; ?>"></div>
                </div>
                <?php if ($post->multi_overview_score) { ?>
                    <div class="rating"><?php echo $post->multi_overview_score; ?></div>
                <?php } ?>
            </div>
            <hr>
            <div class="place-desc">
                <div class="place-address">
                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                    <?php echo mb_strimwidth($post->et_full_location, 0, 32, '...'); ?>
                </div>
                <div class="place-country">
                    <i class="fa fa-globe" aria-hidden="true"></i>
                    <?php echo $post->tax_input['location'][0]->name; ?>
                </div>
                <div class="place-phone">
                    <i class="fa fa-phone" aria-hidden="true"></i>
                    <?php $phone = ($post->et_phone) ? $post->et_phone : __("No Phone", ET_DOMAIN);
                    echo $phone; ?>
                </div>
            </div>
            <hr>
            <div class="place-info">
                <div class="user-added">
                    <?php echo get_avatar($user->ID, 50); ?>
                    <span><?php echo $user->display_name ?></span>
                </div>
                <div class="place-comment-number">
                    <i class="fa fa-commenting-o" aria-hidden="true"></i>
                    <span><?php echo $total_count ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

*/ ?>