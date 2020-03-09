<?php 
$user       = get_user_by( 'id', get_query_var( 'author' ) );
$ae_users   = AE_Users::get_instance();
$user       = $ae_users->convert($user);

global $user_ID;
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
        /*case 'togos' :
            $current_section = 'togos';
            break;*/
        case 'pending' : 
            $current_section = 'pending';
            if($user_ID != $user->ID ) wp_redirect( home_url() );
            break;
        default:            
            break;
    }
}

et_get_mobile_header(); 
?>
    
 <!-- Top bar -->
    <section id="top-bar" class="section-wrapper profile"> 
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-9">
                        <h1 class="title-page">
                            <?php printf(__("%s's profile", ET_DOMAIN), $user->display_name); ?>
                        </h1>
                    </div>
                    <?php if(get_query_var( 'author' ) == $user_ID):?>
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
                    <?php endif;?>
                </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Top bar / End -->
    
    <!-- List News -->
    <section id="info-wrapper" class="section-wrapper"> 
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="info-user-wrapper">
                        <div class="avatar-user"><?php echo get_avatar( $user->ID, 70 ); ?></div>
                        <ul class="info-user">
                            <li><h2 class="name-user"><?php echo $user->display_name; ?></h2></li>
                            <li>
                                <i class="fa fa-map-marker"></i><?php echo ($user->location) ? $user->location : __("Earth", ET_DOMAIN)   ?>
                            </li>
                            <li>
                                <i class="fa fa-phone"></i><?php echo ($user->phone) ? $user->phone : __("No phone", ET_DOMAIN) ?>
                            </li>
                            <li>
                                <i class="fa fa-tree"></i> 
                                    <?php 
                                        $total_place =  ae_count_user_posts_by_type($user->ID, 'place'); 
                                        if($total_place > 1 ) {
                                            printf(__('Owned %d places', ET_DOMAIN), $total_place);
                                        }else {
                                            printf(__('Owned %d place', ET_DOMAIN), $total_place);
                                        }
                                        
                                    ?> 
                            </li>
                            <li>
                                <i class="fa fa-star"></i> 
                                <?php 
                                    $total_review = get_comments(array('user_id' => get_query_var( 'author' ), 'type' => 'review', 'status' => 'approve', 'meta_key' => 'et_rate_comment' ));
                                    printf(_n('%s review', '%s reviews', count($total_review), ET_DOMAIN),count($total_review) ) ;
                                    
                                ?>
                            </li>
                            <li>
                                <i class="fa fa-photo"></i>
                                <?php 
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
                                            );
                                        query_posts( $query_img_args );
                                        $total_picture = $wp_query->found_posts;
                                        wp_reset_query();
                                    printf(_n('%s picture', '%s pictures', $total_picture, ET_DOMAIN),$total_picture) ;
                                ?> 
                            </li>
                        </ul>
                        <div class="clearfix"></div>
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
                    <i class="fa fa-tree"></i>
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
            <!-- <li class="<?php if($current_section  == 'togos'){ echo  'active'; } ?>">
                <a href="#user_togo" role="tab" data-toggle="tab">
                    <i class="fa fa-heart"></i><?php _e("Togos", ET_DOMAIN); ?>
                </a>
            </li> -->
        </ul>

        <div class="tab-content">
            <!-- Tabs 1 / Start -->
            <div class="tab-pane fade active body-tabs in" id="user_place"> 
                <div class="container" id="place-list-wrapper">
                    <div class="row">
                        <div class="col-md-12">
                            <?php 
                                get_template_part( 'mobile/template/publish', 'places' );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tabs 1 / End -->   
            <!-- Tabs 2 -->
            <div class="tab-pane fade body-tabs" id="user_events">  
                <div class="container" >
                    <div class="row" >
                    <?php
                        query_posts(array(
                            'post_type' => 'place',
                            'post_status' => 'publish',
                            'author' => get_query_var('author'), 
                            'paged' => get_query_var('paged'),
                            'meta_query' => array(
                                'meta_key' => 'de_event_post', 
                                'meta_type' => 'NUMERIC'
                            )  
                        ));
                    ?>
                    <div class="col-md-12">
                        <div class="tab-pane body-tabs" id="events-list-wrapper">
                            <div class="section-detail-wrapper list-places fullwidth" id="list-events">
                                <?php if(have_posts()) {
                                    get_template_part( 'mobile/template/list', 'events' ); 
                                }else { ?>
                                    <div class="event-active-wrapper">
                                        <div class="col-md-9">
                                            <div class="event-wrapper tab-style-event">
                                                <h2 class="title-envent"><?php _e("There are no events yet.", ET_DOMAIN); ?></h2>
                                            </div>
                                        </div>
                                    </div>
                            <?php } ?>
                            </div>
                            <?php if(have_posts()) { de_mobile_event_pagination( $wp_query, 1, 'load_more' ); } ?>
                        </div></div>
                    <?php wp_reset_query(); ?>
                    </div>
                </div>
            </div> 
            <!--// Tabs 2 -->
            <!-- Tabs 3 / Start -->
            <div class="tab-pane fade body-tabs" id="user_review">  
                <div class="container" id="reviews-list-wrapper">
                    <div class="row">
                        <div class="col-md-12">
                        <ul class="list-place-review" id="list-reviews">
                        <?php 
                            global $ae_post_factory;
                            $review_object = $ae_post_factory->get('de_review');
                            $number = get_option( 'posts_per_page', 10 );
                            $all_cmts   = get_comments( array(
                                'user_id' => get_query_var( 'author' ),
                                'type'        => 'review',
                                'meta_key'    => 'et_rate_comment', 
                                'status'      => 'approve'
                            ) );
                            $query_args = array(
                                    'user_id' => get_query_var( 'author' ),
                                    'type'        => 'review',
                                    'meta_key'    => 'et_rate_comment', 
                                    'number'      => $number, 
                                    'status'      => 'approve', 
                                    'paginate' => 'load_more'
                                );
                            $reviews = get_comments( $query_args );
                            $comment_pages = ceil( count( $all_cmts ) / $number );
                        if(!empty($reviews)) {

                            foreach ( $reviews as $comment ) {
                                $de_review = $review_object->convert( $comment );
                                get_template_part( 'mobile/template/loop', 'review' );
                            }
                        }
                        else
                        {
                           ?><div class="event-active-wrapper">
                            <div class="col-md-9">
                                <div class="event-wrapper tab-style-event">
                                    <h2 class="title-envent"><?php _e("Currently, there are not review yet.", ET_DOMAIN); ?></h2>
                                </div>
                            </div>
                            </div><?php
                        }

                        ?>                            
                        </ul></div>
                        <?php ae_comments_pagination( $comment_pages, 1, $query_args ); ?>
                    </div>
                </div>
            </div>
            <!-- Tabs 3 / End -->
            <!-- Tabs 4 / Start -->
            <!-- <div class="tab-pane fade body-tabs" id="user_togo">    
                <div class="container" id="list-favorite">
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="list-places fullwidth" id="list-favorite">
                            <?php 
                                global $wp_query, $wp_rewrite, $ae_post_factory;
                                $post_object = $ae_post_factory->get('place');

                                $number     = get_option( 'posts_per_page', 10 );
                                // $paged      = (get_query_var('paged')) ? get_query_var('paged') : 1;  
                                // $offset     = ($paged - 1) * $number;  

                                $all_cmts   = get_comments( array(
                                        'user_id' => get_query_var( 'author' ),
                                        'type'        => 'favorite',
                                        'status'      => 'approve'
                                    ) );

                                $query_args = array(
                                        'user_id' => get_query_var( 'author' ),
                                        'type'        => 'favorite',
                                        'number'      => $number, 
                                        'status'      => 'approve',
                                        'paginate' => 'load_more'
                                    );
                                
                                $reviews = get_comments( $query_args );
                                if(!empty($reviews)) {
                                    $comment_pages = ceil( count( $all_cmts ) / $number );
                                    $place_obj = $ae_post_factory->get('place');
                                    foreach ( $reviews as $comment ) {
                                        $post = get_post($comment->comment_post_ID);
                                        $de_favorite = $place_obj->convert( $post,'thumbnail' );
                                        get_template_part( 'mobile/template/loop', 'place' );
                                    }
                                }else{
                                    ?><div class="event-active-wrapper">
                                    <div class="col-md-9">
                                        <div class="event-wrapper tab-style-event">
                                            <h2 class="title-envent"><?php _e("Currently, there are not favorite yet.", ET_DOMAIN); ?></h2>
                                        </div>
                                    </div>
                                    </div><?php
                                }
                            ?>                            
                            </ul>
                            <?php ae_comments_pagination( $comment_pages, 1, $query_args ); ?>
                        </div>
                    </div>
                </div>
            </div> -->
            <!-- Tabs 4 / End -->
        </div>
    </section>
    <!-- Tabs / End -->
    
<?php et_get_mobile_footer(); ?>