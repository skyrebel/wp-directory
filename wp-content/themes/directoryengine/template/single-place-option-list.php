<?php 
    global $post, $ae_post_factory , $current_user, $user_ID;
    $place_obj = $ae_post_factory->get('place');
    $place     = $place_obj->current_post;

    /**
     * get favorite comment and check added or not
    */
    $favorite = get_comments(array(
        'post_id'      => $post->ID,
        'type'         => 'favorite',
        'author_email' => $current_user->user_email,
        'number'       => 1
    ));
    $report = get_comments(array(
        'post_id'      => $post->ID,
        'type'         => 'report',
        'author_email' => $current_user->user_email,
        'number'       => 1
    ));
?>
<div class="list-option-left-wrapper">
    <!-- user action -->
    <ul class="list-option-left pinned-custom">
        <!-- favorite -->
        <?php if (empty($favorite)) { ?>
            <li class="tooltip-style" data-toggle="tooltip" data-placement="right" title="<?php  _e( 'Add to favorite' , ET_DOMAIN ); ?>">
                <a class="<?php if($user_ID) {echo 'favorite';} else {echo 'authenticate';} ?>" class="" data-id="<?php echo $post->ID; ?>">
                    <i class="fa fa-heart"></i>
                </a>
            </li>
        <?php }else { ?>
            <li class="tooltip-style" data-toggle="tooltip" data-placement="right" title="<?php  _e( 'Remove item from favorite list' , ET_DOMAIN ); ?>">
                <a class="loved" class="" data-id="<?php echo $post->ID; ?>" data-favorite-id="<?php echo $favorite[0]->comment_ID; ?>">
                    <i class="fa fa-heart"></i>
                </a>
            </li>
        <?php }

        $api = get_option('et_addthis_api', '');
        //$api  =   'ra-525f557a07fee94d';
        if ($api)
            $api = '#pubid=' . $api;
        
        ?>
        <!--// favorite -->
        <!-- social share -->
        <li class="share-social">
        	<a class=""><i class="fa fa-share-square-o"></i></a>
            <ul class="list-share-social addthis_toolbox">
            	<li class="tooltip-style" data-toggle="tooltip" data-placement="top" title="<?php _e( 'Share on Facebook' , ET_DOMAIN ); ?>">
                    <a id="addthis_button_facebook  sharing-btn" class="addthis_button_facebook at300b sharing-btn btn-fb" 
                        onclick="window.open(this.href, '_blank', 'resizable=no,status=no,location=no,toolbar=no,menubar=no,fullscreen=no,scrollbars=no,dependent=no'); return false;"
                        href="http://api.addthis.com/oexchange/0.8/forward/facebook/offer?url=<?php echo $place->permalink; ?>&title=<?php echo $place->post_title; ?>" rel="nofollow"> 
                        <i class="fa fa-facebook"></i>
                    </a>
                </li>
                <li class="tooltip-style" data-toggle="tooltip" data-placement="top" title="<?php _e( 'Share on Twitter' , ET_DOMAIN ); ?>">
                    <a id="addthis_button_twitter  sharing-btn" class="addthis_button_twitter at300b sharing-btn btn-tw" 
                        onclick="window.open(this.href, '_blank', 'resizable=no,status=no,location=no,toolbar=no,menubar=no,fullscreen=no,scrollbars=no,dependent=no'); return false;"
                        href="http://api.addthis.com/oexchange/0.8/forward/twitter/offer?url=<?php echo $place->permalink; ?>&title=<?php echo $place->post_title; ?>" rel="nofollow">
                        <i class="fa fa-twitter"></i>
                    </a>
                </li>
                <li class="tooltip-style" data-toggle="tooltip" data-placement="top" title="<?php _e( 'Share on Google Plus' , ET_DOMAIN ); ?>">
                    <a  id="addthis_button_google_plusone_share  sharing-btn" class="addthis_button_google_plusone_share at300b sharing-btn btn-gg"  
                        onclick="window.open(this.href, '_blank', 'resizable=no,status=no,location=no,toolbar=no,menubar=no,fullscreen=no,scrollbars=no,dependent=no'); return false;"
                        href="http://api.addthis.com/oexchange/0.8/forward/googleplus/offer?url=<?php echo $place->permalink; ?>&title=<?php echo $place->post_title; ?>" rel="nofollow">
                        <i class="fa fa-google-plus"></i>
                    </a>
                </li>
            </ul>

        <!--// social share -->

        <li class="tooltip-style" data-toggle="tooltip" data-placement="right" title="<?php _e( 'Write a review' , ET_DOMAIN ); ?>">
            <?php
                $review_url = '';
                if(isset($current)) {
                    $review_url = $place->permalink.'#review';
                }
            ?>
            <a class="write-review" data-href="<?php echo $review_url; ?>" ><i class="fa fa-pencil"></i></a>
        </li>
        <?php if( empty($report) ){ ?>
        <li class="tooltip-style" data-toggle="tooltip" data-placement="right" title="<?php _e( 'Report' , ET_DOMAIN ); ?>">
            <a class="<?php if($user_ID) {echo 'report';} else {echo 'authenticate';} ?>" id="report_<?php echo $post->ID; ?>" data-user="<?php echo $current_user->ID ?>" data-id="<?php echo $post->ID; ?>">
                <i class="fa fa-flag"></i>
            </a>
        </li><!-- Report -->
        <?php } ?>
    </ul>
    <!--// user action -->
</div>
