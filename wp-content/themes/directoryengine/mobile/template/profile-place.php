<?php 
    global $user;
    $count_post = de_count_post_by_user_id('place', $user->ID);
    $post_total_publish = $count_post->publish;
    $post_total_pending = $count_post->pending;
    $post_total_reject = $count_post->reject;
    $post_total_archive = $count_post->archive;
    $post_total_draft = $count_post->draft;
    if($post_total_publish || $post_total_pending || $post_total_reject || $post_total_archive || $post_total_draft){
        ?>
        <div class="container-fluid choose-place-action">
            <div class="row">
                <div class="col-xs-5">
                    <select name="post_status" id="post_status">
                        <option value="publish" data-type="<?php _e('Publishing');?>"><?php printf(__('Publishing (%s)',ET_DOMAIN),$post_total_publish);?></option>
                        <option value="pending" data-type="<?php _e('Pending');?>"><?php printf(__('Pending (%s)',ET_DOMAIN),$post_total_pending);?></option>
                        <option value="archive" data-type="<?php _e('Overdue');?>"><?php printf(__('Overdue (%s)',ET_DOMAIN),$post_total_archive);?></option>
                        <option value="reject" data-type="<?php _e('Rejected');?>"><?php printf(__('Rejected (%s)',ET_DOMAIN),$post_total_reject);?></option>
                        <option value="draft" data-type="<?php _e('Draft');?>"><?php printf(__('Draft (%s)',ET_DOMAIN),$post_total_draft);?></option>
                    </select>
                </div>
                <div class="col-xs-7">
                    <div class="box-search">
                        <input type="text" value="" class="search" id="place_search" name="place_search" placeholder="<?php _e('Enter keywords...',ET_DOMAIN);?>">
                        <span class="btn-search-place"><i class="fa fa-search"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
?>
<div class="container" id="place-list-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="place-action active" id="publishing">
                <ul class="list-places fullwidth" id="place-list">
                <?php 
                    global $wp_query, $post, $ae_post_factory, $user_ID, $user;
                    /**
                     * generate nearby center
                    */
                    $paged = ( get_query_var('page') ) ? get_query_var('page') : 1;
                    $args = array(
                        'post_type' => 'place',
                        'orderby'       => 'date',
                        'order'         => 'DESC',
                        'post_status'   => 'publish',
                        'posts_per_page' => $paged,
                        'showposts'		=> get_option('posts_per_page'),
                        'author'        => $user->ID
                    );
                    $array_total_place = array('author'=>$user->ID, 'post_status'=>array('publish','pending','reject','archive','draft'), 'post_type' => 'place','posts_per_page' => -1);
                    $query_total_place= new WP_Query( $array_total_place );
                    $post_total = $query_total_place->found_posts;;

                    $place_obj = $ae_post_factory->get('place');
                    // $search_query    =   $place_obj->nearbyPost($args);
                    $search_query = new WP_Query( $args );
                    $data_arr = array();
                    if($search_query->have_posts()){
                        while($search_query->have_posts()) { 
                            $search_query->the_post(); 
                            
                            $place_obj = $ae_post_factory->get('place');
                            // covert post
                            $convert = $place_obj->convert($post, 'thumbnail');
                            $data_arr[] = $convert;
                            get_template_part( 'mobile/template/loop', 'place' );

                        }
                    } elseif($post_total < 1){
                ?>
                    <div class="event-active-wrapper">
                        <div class="col-md-9">
                            <div class="event-wrapper tab-style-event">
                                <h2 class="title-envent"><?php _e("Currently, there are not place yet.", ET_DOMAIN); ?></h2>
                            </div>
                        </div>
                    </div>
                <?php } ?>                        
                </ul>
                <?php
                    echo '<script type="json/data" class="postdata" > ' . json_encode($data_arr) . '</script>';
                    echo '<div class="paginations-wrapper">';
                    ae_pagination($search_query, 1, 'load_more');
                    echo '</div>';
                    wp_reset_postdata();
                ?>
            </div>
        </div>
    </div>
</div>