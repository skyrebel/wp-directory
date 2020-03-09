<?php
/**
 * Loop Place Item (Status: Publish, Pending, Draft, Archive)
 */
global $ae_post_factory, $user_ID;
$place_obj = $ae_post_factory->get('place');
$post = $place_obj->current_post;
$time_to_expired = $post->time_to_expired;
$col = "col-lg-3 col-md-4 col-sm-4";

?>
<li id="post-<?php the_ID(); ?>" >
    <div <?php post_class($col.' place-item'); ?>>
        <div class="wrap-place-publishing">
            <?php if( ae_user_can( 'edit_others_posts' ) || $post->post_author == $user_ID) { ?>
            <ol class="box-edit-place">
                <li>
                    <a href="#edit_place" class="action edit" data-target="#" data-action="edit"><i class="fa fa-pencil"></i></a>
                </li>
                <li>
                    <a href="#" class="action archive" data-action="archive"><i class="fa fa-trash-o"></i></a>
                </li>
            </ol>
            <?php } ?>
            <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="place-publishing-img">
                <img src="<?php echo $post->the_post_thumnail; ?>" alt="<?php the_title(); ?>"/>
            </a>
            <?php if($time_to_expired){ ?>
                <span class="tag-remaining"><i class="fa fa-clock-o"></i> <?php echo $time_to_expired;?></span>
            <?php } ?>
            <h2 class="place-publishing-title">
                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
            </h2>
            <span class="place-publishing-map"><i class="fa fa-map-marker"></i>
                <span itemprop="latitude" id="latitude" content="<?php echo $post->et_location_lat;?>"></span>
                <span itemprop="longitude" id="longitude" content="<?php echo $post->et_location_lng;?>"></span>
                <span class="distance"></span>
                <span class="location"><?php echo $post->et_full_location ?></span>
            </span>
            <div class="rate-it" data-score="<?php echo $post->rating_score_comment ?>" data-id="<?php echo $post->ID; ?>"></div>
        </div>
    </div>
</li>
