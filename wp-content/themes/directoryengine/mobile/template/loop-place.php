<?php
global $ae_post_factory, $user_ID;
$place_obj = $ae_post_factory->get('place');
$post = $place_obj->current_post;
// et_location_lat et_location_lng
?>
<li <?php post_class( 'post-item' ); ?> >
    <div class="place-wrapper">
        <a href="<?php the_permalink(); ?>" class="img-place">
            <img src="<?php echo $post->the_post_thumnail; ?>" alt="<?php the_title(); ?>"/>
            <?php if(isset($post->ribbon) && $post->ribbon){ ?>
            <div class="cat-<?php echo $post->place_category[0]; ?>">
                <div class="ribbon">
                    <span class="ribbon-content"><?php echo $post->ribbon; ?></span>
                </div>
            </div>
            <?php } ?>
        </a>
        <div class="place-detail-wrapper">
            <h2 class="title-place"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" ><?php the_title(); ?></a></h2>
            <span class="address-place"><i class="fa fa-map-marker"></i>
                <span itemprop="latitude" id="latitude" content="<?php echo $post->et_location_lat;?>"></span>
                <span itemprop="longitude" id="longitude" content="<?php echo $post->et_location_lng;?>"></span>
                <span class="distance"></span>
                <?php echo $post->et_full_location; ?>
            </span>
            <div class="rate-it" data-score="<?php echo $post->rating_score_comment; ?>"></div>
            <?php 
                if( ae_get_option("enable_view_counter",false) ){
                    echo '<div class="view-count"><i class="fa fa-eye"></i> '.number_format($post->view_count).'</div>';
                }
            ?>
            
        </div>
        <!--
        <div class="place-config">
            <i class="fa fa-cog edit-config"></i>
        </div>
        <div class="edit-place-post">
            <i class="fa fa-history place-extend"></i>
            <i class="fa fa-pencil place-edit"></i>
            <i class="fa fa-trash-o place-remove"></i>
        </div>
        -->
    </div>
</li>