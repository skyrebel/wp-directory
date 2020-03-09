<?php
    global $ae_post_factory;
    $review_object = $ae_post_factory->get('de_review'); // get review object
    $de_review = $review_object->current_comment;
    /**
     * review author details
    */
    $author = isset($de_review->author_data) ? $de_review->author_data : '';
    $post_data = $de_review->post_data;
    // $comments = wp_count_comments($post_data->ID);
    $col = 'col-md-4';
    if(is_author()) $col = 'col-md-3';
?>
    <?php /*
    <div class="section-review-content">
        <div class="review-image">
            <img src="<?php echo $post_data->the_post_thumnail; ?>" alt="<?php echo $post_data->post_title ?>"/>
        </div>
        <div class="review-info">
            <div class="place-brand">
                <div class="brand-title">
                    <h4><a href="<?php echo get_comment_link($de_review->comment_ID); ?>"><?php echo $post_data->post_title; ?></a></h4>
                </div>
            </div>
            <hr>
            <div class="place-quote">
                <i class="fa fa-quote-left" aria-hidden="true"></i>
                <span><?php echo mb_strimwidth($de_review->comment_content,0,54,'...') ?></span>
            </div>
            <div class="place-info">
                <div class="user-reviewed pull-left">
                    <span>&nbsp;&nbsp;</span>
                    <h5>
                        <?php if($author) { ?>
                            <a href="<?php echo $author->author_url; ?>" title="<?php echo $author->display_name; ?>" class="name-author"><?php echo $author->display_name; ?></a>
                        <?php }else { ?>
                            <a href="#" title="<?php echo $de_review->comment_author; ?>" class="name-author"><?php echo $de_review->comment_author; ?></a>
                        <?php } ?>
                    </h5>
                </div>
                <div class="star pull-right rate-it" data-score="<?php echo  get_comment_meta($de_review->comment_ID, 'et_rate_comment' , true); ?>"> </div>
            </div>
        </div>
    </div>
    */ ?>
      
<div class="de-review-item">
    <div class="review-item-img">
        <a href="<?php echo get_comment_link($de_review->comment_ID); ?>"><img src="<?php echo $post_data->the_post_thumnail; ?>" alt="<?php echo $post_data->post_title ?>"/></a>
    </div>
    <div class="review-item-info">
        <h2><a href="<?php echo get_comment_link($de_review->comment_ID); ?>"><?php echo $post_data->post_title; ?></a></h2>
        <p class="review-item-cmt"><i class="fa fa-quote-left" aria-hidden="true"></i><?php echo mb_strimwidth($de_review->comment_content,0,54,'...') ?></p>
        <div class="review-item-author">
            <p>
                <?php if($author) { ?>
                    <a href="<?php echo $author->author_url; ?>" title="<?php echo $author->display_name; ?>" class="name-author"><?php echo $author->display_name; ?></a>
                <?php }else { ?>
                    <a href="#" title="<?php echo $de_review->comment_author; ?>" class="name-author"><?php echo $de_review->comment_author; ?></a>
                <?php } ?>
            </p>
            <div class="rate-it" data-score="<?php echo  get_comment_meta($de_review->comment_ID, 'et_rate_comment' , true); ?>"></div>
        </div>
    </div>
</div>

