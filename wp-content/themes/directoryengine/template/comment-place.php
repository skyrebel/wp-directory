<?php
global $post, $ae_post_factory, $current_user, $user_ID;
$place_obj = $ae_post_factory->get('place');
$place = $place_obj->current_post;


$total_count = get_comments(array( 'post_id' => $post->ID, 'type' => 'review', 'count' => true, 'status' => 'approve','meta_key' => 'et_rate_comment', ));
$comments = get_comments(array('type' => 'review', 'post_id' => $post->ID));
$total_comment = get_comments(array('type' => 'review', 'status' => 'approve', 'post_id' => $post->ID, 'count' => true));
?>
    <div class="comments" id="review-list">
        <div class="section-detail-wrapper review-form">
            <div class="review-wrapper">
                <h3 class="title-comments title-number-review">
                    <?php
                    $comment_count = $total_comment - $total_count;
                    if ($total_count > 0) {
                        if ($total_count > 1) {
                            printf(__('%d REVIEWS & ', ET_DOMAIN), $total_count);
                        } else {
                            printf(__('%d REVIEW & ', ET_DOMAIN), $total_count);
                        }
                    }

                    if ($comment_count === 1) {
                        printf(__('%d COMMENT', ET_DOMAIN), $comment_count);
                    } else {
                        printf(__('%d COMMENTS', ET_DOMAIN), $comment_count);
                    }
                    ?>
                </h3>

                <ul class="media-list comment-list">
                    <?php

                    // if( have_comments() ){

                    wp_list_comments(array('callback' => 'de_list_review'), $comments);

                    if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
                        <nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
                            <div class="nav-previous"><?php previous_comments_link(__('&larr; Older Reviews', ET_DOMAIN)); ?></div>
                            <div class="nav-next"><?php next_comments_link(__('Newer Reviews &rarr;', ET_DOMAIN)); ?></div>
                        </nav><!-- #comment-nav-below -->
                    <?php endif; // Check for comment navigation.
                    // }
                    ?>

                </ul>
            </div>
        </div>
        <div style="display:none">
            <?php
            $disable_comment_review = ae_get_option('disable_comment_review');
            if (!$disable_comment_review) {
                comment_form(array(
                    'comment_field' => '<div class="form-item"><label class="" for="comment">' . __('Comment', ET_DOMAIN) . '</label>
                                                    <div class="input">
                                                        <textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea>
                                                    </div> <div id="comment-captcha"></div> </div>',
                    //'must_log_in'          => '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.', ET_DOMAIN ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
                    'logged_in_as' => '',
                    'comment_notes_before' => '',
                    'comment_notes_after' => '',
                    'id_form' => 'commentform',
                    'id_submit' => 'submit',
                    'title_reply' => __("ADD REPLY", ET_DOMAIN),
                    'title_reply_to' => __('Leave a Reply to %s', ET_DOMAIN),
                    'cancel_reply_link' => __('CANCEL', ET_DOMAIN),
                    'label_submit' => __('SUBMIT', ET_DOMAIN),

                ));
            }
            ?>
        </div>
        <?php if ($user_ID) { ?>
            <?php
            $is_review = false;

            if (class_exists('DE_PlaceAction')) {
                //check if exist review
                $place_object = new DE_PlaceAction();
                $is_review = $place_object->check_is_rating($place->ID);
            }

            ?>
            <!-- review form comment without comment parent -->
            <div id="review" class="comment-respond">
                <h3 id="reply-title" class="comment-reply-title">
                    <?php if (!$is_review) _e('ADD REVIEW', ET_DOMAIN); else _e('ADD COMMENT', ET_DOMAIN); ?>
                </h3>
                <form action="<?php echo site_url('wp-comments-post.php') ?>" method="post" class="comment-form"
                      id="submit-comment">
                    <?php
                    if (!$is_review) {
                        ?>
                        <h5 class="rate">
                            <?php _e('Rate to review this place or just leave a comment.', ET_DOMAIN); ?>
                            <div class="rating-it"></div>
                        </h5>
                        <?php
                    }
                    ?>
                    <div class="form-item row">
                        <label for="comment" class="col-sm-2">
                            <?php if (!$is_review) _e('REVIEW', ET_DOMAIN); else  _e('COMMENT', ET_DOMAIN); ?>
                        </label>
                        <div class="input col-sm-10">
                            <textarea placeholder="<?php _e("description", ET_DOMAIN); ?>" id="comment" name="comment"
                                      cols="45" rows="8" aria-required="true"></textarea>
                        </div>
                        <?php do_action('ae_button_upload_image'); ?>
                        <?php if (!current_user_can('administrator') && ae_get_option('gg_captcha')) { ?>
                            <div class="gg-captcha">
                                <?php ae_gg_recaptcha(); ?>
                            </div>
                        <?php } ?>
                        <p class="form-submit">
                            <input name="submit" type="submit" id="submit" value="<?php _e('SUBMIT', ET_DOMAIN); ?>"/>
                            <input type="hidden" name="comment_post_ID" value="<?php echo $post->ID ?>"
                                   id="comment_post_ID"/>
                            <input type="hidden" name="comment_parent" id="comment_parent" value="0"/>
                            <input type="hidden" name="post_status" id="post_status"
                                   value="<?php echo $post->post_status; ?>"/>
                        </p>
                    </div>
                </form>

            </div>
        <?php } else { ?>
            <a name="review"></a>
            <!--echo '<h3 id="reply-title" class="comment-reply-title ae-comment-reply-title">' . __( 'YOU MUST <a class="authenticate" href="#login_register">LOGIN</a> TO SUBMIT A REVIEW' , ET_DOMAIN ) . '</h3>';-->
            <h3 id="reply-title"
                class="comment-reply-title ae-comment-reply-title"><?php printf(__('YOU MUST %s TO SUBMIT A REVIEW', ET_DOMAIN), '<a class="authenticate" href="#login_register">LOGIN</a>'); ?></h3>
        <?php } ?>


    </div>

<?php
/**
 * desktop version review list call back
 * @param array $comment
 * @param array $args
 * @param string $depth
 */
function de_list_review($comment, $args, $depth)
{
    global $user_ID, $post;
    $GLOBALS['comment'] = $comment;
    $disable_comment_review = ae_get_option('disable_comment_review');
    if ($disable_comment_review) {
        $depth = 1;
        $args['max_depth'] = 1;
    }
    $user = get_user_by('email', $comment->comment_author_email);
    $name_user = $user ? $user->display_name : $comment->comment_author;
    if ($comment->comment_approved == 1) {
        ?>
        <li class="media" id="li-comment-<?php comment_ID(); ?>">
            <div id="comment-<?php comment_ID(); ?>" class="not-padding">
                <a class="pull-left avatar-comment" href="#">
                    <?php echo get_avatar($comment->comment_author_email, 60); ?>
                </a>
                <div class="media-body">
                    <h4 class="media-heading">
                        <?php
                        echo '<span>'. $name_user .'</span>';
                        $rate = get_comment_meta($comment->comment_ID, 'et_rate_comment', true);
                        if (!$comment->comment_parent && $rate) {
                            ?>
                            <div class="rate-it" data-score='<?php echo $rate; ?>'></div>
                        <?php } ?>
                    </h4>
                    <div class="comment-text"><?php comment_text(); ?></div>
                    <span class="time-review"><i
                                class="fa fa-clock-o"></i><?php echo ae_the_time(strtotime($comment->comment_date)); ?></time></span>
                    <?php
                    comment_reply_link(array_merge($args, array(
                        'reply_text' => __('&nbsp;&nbsp;|&nbsp;&nbsp; Reply', ET_DOMAIN),
                        'depth' => $depth,
                        'max_depth' => $args['max_depth'])));
                    ?>
                </div>
            </div>
            <?php do_action('ae_carousel_comment', $comment); ?>
        </li>
        <?php
    } elseif ($user_ID) {
        // If this review not yet approve, display status pending
        if (ae_user_can('administrator') || $comment->user_id == $user_ID) {
            ?>
            <li class="media" id="li-comment-<?php comment_ID(); ?>">
                <div id="comment-<?php comment_ID(); ?>">
                    <a class="pull-left avatar-comment" href="#">
                        <?php echo get_avatar($comment->comment_author_email, 60); ?>
                    </a>
                    <div class="media-body">
                        <h4 class="media-heading">
                            <?php
                            if (isset($user)):
                                echo '<span>'. $user->display_name .'</span>';
                            endif;
                            $rate = get_comment_meta($comment->comment_ID, 'et_rate_comment', true);
                            if (!$comment->comment_parent && $rate) {
                                ?>
                                <div class="rate-it" data-score='<?php echo $rate; ?>'></div>
                            <?php } ?>
                            <span style="float:right"><?php _e('Waiting for approval', ET_DOMAIN); ?></span>
                        </h4>
                        <div class="comment-text"><?php comment_text(); ?></div>
                        <span class="time-review"><i
                                    class="fa fa-clock-o"></i><?php echo ae_the_time(strtotime($comment->comment_date)); ?></time></span>
                    </div>
                </div>
                <?php do_action('ae_carousel_comment', $comment); ?>
            </li>
            <?php
        }
    }
}