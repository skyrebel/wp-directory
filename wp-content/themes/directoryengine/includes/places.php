<?php

/**
 * this file contain all function related to places
 */
add_action('init', 'de_init');
function de_init() {

    /**
     * register post type place to store place details
     */
    // add multilanguage filter and action
    $place_slug = ae_get_option('place_slug', 'place');
    do_action( 'register_string_for_translation', 'theme_directory_engine_slugs', 'place', $place_slug );
    $place_slug = apply_filters( 'translate_string', $place_slug, 'theme_directory_engine_slugs', 'place' );


    $place_archive_slug = ae_get_option('place_archive_slug', 'places');
    do_action( 'register_string_for_translation', 'theme_directory_engine_slugs', 'places', $place_archive_slug );
    $place_archive_slug = apply_filters( 'translate_string', $place_archive_slug, 'theme_directory_engine_slugs', 'places' );

    register_post_type('place', array(
        'labels' => array(
            'name' => __('Place', ET_DOMAIN) ,
            'singular_name' => __('Place', ET_DOMAIN) ,
            'add_new' => __('Add New', ET_DOMAIN) ,
            'add_new_item' => __('Add New Place', ET_DOMAIN) ,
            'edit_item' => __('Edit Place', ET_DOMAIN) ,
            'new_item' => __('New Place', ET_DOMAIN) ,
            'all_items' => __('All Places', ET_DOMAIN) ,
            'view_item' => __('View Place', ET_DOMAIN) ,
            'search_items' => __('Search Places', ET_DOMAIN) ,
            'not_found' => __('No Place found', ET_DOMAIN) ,
            'not_found_in_trash' => __('NoPlaces found in Trash', ET_DOMAIN) ,
            'parent_item_colon' => '',
            'menu_name' => __('Places', ET_DOMAIN)
        ) ,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => $place_slug
        ) ,
        'capability_type' => 'post',
        'has_archive' => $place_archive_slug,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array(
            'title',
            'editor',
            'author',
            'thumbnail',
            'excerpt',
            'comments',
            'custom-fields'
        )
    ));

    // register a post status: Reject
    register_post_status('reject', array(
        'label' => __('Reject', ET_DOMAIN) ,
        'private' => true,
        'public' => false,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('Reject <span class="count">(%s)</span>', 'Reject <span class="count">(%s)</span>') ,
    ));

    register_post_status('archive', array(
        'label' => __('Archive', ET_DOMAIN) ,
        'private' => false,
        'public' => true,
        'exclude_from_search' => true,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('Archive <span class="count">(%s)</span>', 'Archive <span class="count">(%s)</span>') ,
    ));

    /**
     * Create a taxonomy
     *
     * @uses  Inserts new taxonomy object into the list
     * @uses  Adds query vars
     *
     * @param string  Name of taxonomy object
     * @param array|string  Name of the object type for the taxonomy object.
     * @param array|string  Taxonomy arguments
     * @return null|WP_Error WP_Error if errors, otherwise null.
     */

    $labels = array(
        'name' => _x('Category', 'Taxonomy plural name', ET_DOMAIN) ,
        'singular_name' => _x('place category', 'Taxonomy singular name', ET_DOMAIN) ,
        'search_items' => __('Search Category', ET_DOMAIN) ,
        'popular_items' => __('Popular Category', ET_DOMAIN) ,
        'all_items' => __('All Category', ET_DOMAIN) ,
        'parent_item' => __('Parent place category', ET_DOMAIN) ,
        'parent_item_colon' => __('Parent place category', ET_DOMAIN) ,
        'edit_item' => __('Edit place category', ET_DOMAIN) ,
        'update_item' => __('Update place category', ET_DOMAIN) ,
        'add_new_item' => __('Add New place category', ET_DOMAIN) ,
        'new_item_name' => __('New place category Name', ET_DOMAIN) ,
        'add_or_remove_items' => __('Add or remove Category', ET_DOMAIN) ,
        'choose_from_most_used' => __('Choose from most used enginetheme ', ET_DOMAIN) ,
        'menu_name' => __('Category', ET_DOMAIN) ,
    );

    // add multilanguage filter and action
    $place_category_slug = ae_get_option('place_category_slug', 'place_category') ;
    do_action( 'register_string_for_translation', 'theme_directory_engine_slugs', 'place_category', $place_category_slug );
    $place_category_slug = apply_filters( 'translate_string', $place_category_slug, 'theme_directory_engine_slugs', 'place_category' );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_admin_column' => false,
        'hierarchical' => true,
        'show_tagcloud' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => $place_category_slug ,
            'hierarchical' => ae_get_option('place_category_hierarchical', false)
        ) ,
        'capabilities' => array(
            'manage_terms',
            'edit_terms',
            'delete_terms',
            'assign_terms'
        )
    );

    register_taxonomy('place_category', array(
        'place'
    ) , $args);

    $labels = array(
        'name' => _x('Locations', 'Taxonomy plural name', ET_DOMAIN) ,
        'singular_name' => _x('location', 'Taxonomy singular name', ET_DOMAIN) ,
        'search_items' => __('Search Locations', ET_DOMAIN) ,
        'popular_items' => __('Popular Locations', ET_DOMAIN) ,
        'all_items' => __('All Locations', ET_DOMAIN) ,
        'parent_item' => __('Parent location', ET_DOMAIN) ,
        'parent_item_colon' => __('Parent location', ET_DOMAIN) ,
        'edit_item' => __('Edit location', ET_DOMAIN) ,
        'update_item' => __('Update location', ET_DOMAIN) ,
        'add_new_item' => __('Add New location', ET_DOMAIN) ,
        'new_item_name' => __('New location Name', ET_DOMAIN) ,
        'add_or_remove_items' => __('Add or remove Locations', ET_DOMAIN) ,
        'choose_from_most_used' => __('Choose from most used enginetheme', ET_DOMAIN) ,
        'menu_name' => __('Location', ET_DOMAIN) ,
    );


    // add multilanguage filter and action
    $place_location_slug = ae_get_option('place_location_slug', 'location');
    do_action( 'register_string_for_translation', 'theme_directory_engine_slugs', 'location', $place_location_slug );
    $place_location_slug = apply_filters( 'translate_string', $place_location_slug, 'theme_directory_engine_slugs', 'location' );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_admin_column' => false,
        'hierarchical' => true,
        'show_tagcloud' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => ae_get_option('place_location_slug', 'location') ,
            'hierarchical' => ae_get_option('place_location_hierarchical', false)
        ) ,
        'capabilities' => array(
            'manage_terms'
        ) ,
    );
    $location_for_post_type =  array(
        'place'
    );
    $location_for_post_type = apply_filters('de_location_for_post_type', $location_for_post_type );
    register_taxonomy('location', $location_for_post_type , $args);
    // Add image for location
    et_active_taxonomy_image('location');

    $labels = array(
        'name' => _x('Place Tags', 'Taxonomy plural name', ET_DOMAIN) ,
        'singular_name' => _x('Place Tag', 'Taxonomy singular name', ET_DOMAIN) ,
        'search_items' => __('Search Place Tags', ET_DOMAIN) ,
        'popular_items' => __('Popular Place Tags', ET_DOMAIN) ,
        'all_items' => __('All Place Tags', ET_DOMAIN) ,
        'edit_item' => __('Edit Place Tag', ET_DOMAIN) ,
        'update_item' => __('Update Place Tag', ET_DOMAIN) ,
        'add_new_item' => __('Add New Place Tag', ET_DOMAIN) ,
        'new_item_name' => __('New Place Tag Name', ET_DOMAIN) ,
        'add_or_remove_items' => __('Add or remove Place Tag', ET_DOMAIN) ,
        'choose_from_most_used' => __('Choose from most used enginetheme', ET_DOMAIN) ,
        'menu_name' => __('Place Tag', ET_DOMAIN) ,
    );
    // add multilanguage filter and action
    $place_tag_slug = ae_get_option('place_tag_slug', 'place_tag');
    do_action( 'register_string_for_translation', 'theme_directory_engine_slugs', 'place_tag', $place_tag_slug );
    $place_tag_slug = apply_filters( 'translate_string', $place_tag_slug, 'theme_directory_engine_slugs', 'place_tag' );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_admin_column' => false,
        'hierarchical' => false,
        'show_tagcloud' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => ae_get_option('place_tag_slug', 'place_tag')
        ) ,
        'capabilities' => array(
            'manage_terms'
        ) ,
    );

    register_taxonomy('place_tag', array(
        'place'
    ) , $args);

    global $ae_post_factory;
    $place_field = array(
        'et_paid',

        // business info
        'et_phone',
        'et_emailaddress',
        'et_url',
        'et_fb_url',
        'et_google_url',
        'et_twitter_url',
        'et_video',
        'et_full_location',
        'et_location_lat',
        'et_location_lng',
        'open_time',
        'close_time',
        'serve_day',
        'serve_time',
        'cover_image',
        'cover_image_url',
        'video_position',
        'reject_message',
        // user can not change info
        'et_ad_order',
        'rating_score',
        'rating_score_comment',
        'reviews_count',
        'et_payment_package',
        'et_featured',
        'et_claimable',
        'et_claim_approve',
        'et_claim_info'
    );
    $place_field = apply_filters('ae_place_field', $place_field);
    $place_taxs = array(
        'place_category',
        'location'
    );
    $place_taxs = apply_filters('ae_place_taxs', $place_taxs);
    $ae_post_factory->set('place', new AE_Posts('place', $place_taxs , $place_field));
    /**
     * add review to post factory object
     */
    $review = new AE_Comments('review');
    $ae_post_factory->set('de_review', $review);

    $favorite = new AE_Comments('favorite');
    $ae_post_factory->set('de_favorite', $favorite);
}

/**
 * Class DE_PlaceAction
 */
class DE_PlaceAction extends AE_Base
{
    /**
     *
     */
    function __construct() {
        $this->post_type = 'place';
        $this->disable_plan = ae_get_option('disable_plan', false);

        $this->mail = AE_Mailing::get_instance();

        /**
         * filter post query to add order by param
         */
        $this->add_action('pre_get_posts', 'pre_get_places');

        /**
         * catch ad change status event, update expired date
         */
        $this->add_action('transition_post_status', 'change_post_status', 10, 3);
        /**
         * add action publish ad, update ad order and related ad in a package
         */
        $this->add_action('ae_publish_post', 'publish_post_action');

        /**
         * catch ajax action sync post
         */
        $this->add_ajax('ae-sync-post', 'post_sync');

        /**
         * ajax fetch posts
         */
        $this->add_ajax('ae-fetch-posts', 'fetch_posts');

        // add ajax load more post in single post
        $this->add_ajax('de-next-place', 'next_place');

        //Button back place
        $this->add_ajax('button_go_back_place', 'button_back_place');

        /**
         * hook to filter convert place add more data to place info
         */
        $this->add_filter('ae_convert_' . $this->post_type, 'convert_place');

        // filter args before update  and insert
        $this->add_filter('ae_pre_insert_' . $this->post_type, 'filter_pre_insert');
        $this->add_filter('ae_pre_update_' . $this->post_type, 'filter_pre_insert');

        $this->add_action('trashed_post' , 'trash_post_child');
    }
    function trash_post_child($post_id){
        if(empty($post_id)) return false;

        // Update status of post type is Event when post parent is trash
        $args_event = array(
            'posts_per_page' => -1,
            'post_parent'   => $post_id,
            'post_type'     => array ('event'),
            'post_status'   => array ( 'publish', 'archive', 'pending' )
        );

        $children = get_children( $args_event);

        foreach ($children as $key => $value) {
            wp_update_post(array(
                'ID'           => $value->ID,
                'post_status'  => 'trash'
            ));
        }

        // Update status of comment have when post parent is trash
        $args_comment = array(
            'post_id'       => $post_id,
            'status'        => 'approve',
            'type'          => 'review'
        );
        $comments = get_comments($args_comment);
        foreach ($comments as $key => $value) {
            wp_set_comment_status( $value->comment_ID, 'trash' );
        }
    }
    /**
     * filter pre get posts
     * @param $query
     * @return
     */
    function pre_get_places($query) {
        global $current_user;

        if ($query->is_main_query() && is_author()) {
            $query->set('post_type', $this->post_type);
            if ($current_user->user_login == $query->query['author_name']) {
                $query->set('post_status', array(
                    'pending',
                    'reject',
                    'archive',
                    'draft',
                    'publish'
                ));
            }
        }
        // set default post type query in place category, location listing
        if ($query->is_main_query() && (is_tax('place_category') || is_tax('location'))) {
            $query->set('post_type', $this->post_type);
            $query->set('post_status', 'publish');
        }

        /**
         * is main query and cach request showposts
         */
        if (isset($_REQUEST['showposts']) && $query->is_main_query()) {
            $query->set('showposts', $_REQUEST['showposts']);
        }

        /**
         * if is post type archive set status to publish
         */
        if (is_post_type_archive($this->post_type) && !is_admin()) {
            if (!isset($query->query_vars['post_status'])) {
                $query->set('post_status', 'publish');
            }
        }

        /**
         * filter orderby
         */
        if (isset($_REQUEST['sortby'])
            && ( $query->is_main_query() || (isset($query->query_vars['meta_key']) && $query->query_vars['meta_key'] == 'rating_score_comment') )
        ) {
            if ($_REQUEST['sortby'] !== 'date') {
                // $query->query_vars['meta_key'] = $_REQUEST['sortby'];
                $query->set('orderby', 'meta_value_num');
            } else {

                // order by date
                $query->set('orderby', 'date');
            }

            // order desc
            $query->set('order', 'DESC');
        }
        // order by rating score
        if(isset($_REQUEST['query']['orderby']) && $_REQUEST['query']['orderby'] == 'rating_score_comment') {
            $query->set('orderby', 'meta_value_num date');
            if( !isset($_REQUEST['query']['meta_key'] ) ) {
                $query->query_vars['meta_key'] = $_REQUEST['query']['orderby'];
                $query->meta_query = array(
                     //check to see if et_featured has been filled out
                    'relation' => 'OR',
                    array(
                         //check to see if date has been filled out
                        'key' => $_REQUEST['query']['orderby'],
                        'compare' => 'BETWEEN',
                        'value' => array(
                            0,
                            5
                        )
                    ),
                    array(
                         //if no et_featured has been added show these posts too
                        'key' => $_REQUEST['query']['orderby'],
                        'value' => '',
                        'compare' => 'NOT EXISTS'
                    )
                );
            }else{
                // order by rating score for feature block
                if($_REQUEST['query']['meta_key'] == 'et_featured') {
                    $query->set('orderby', 'menu_order');
                    $query->set('meta_query' , array(
                        array(
                            'key' => 'rating_score_comment',
                            // 'value' => array(0, 5),
                            // 'compare' => 'BETWEEN'
                        ),
                        array(
                            'key' => 'et_featured',
                            'value' => '1',
                            'compare' => 'LIKE'
                        )
                    ));
                }
                if($_REQUEST['query']['meta_key'] == 'de_event_post') {
                    // order by rating score for event block
                    $query->set('orderby', 'menu_order');
                    $query->set('meta_query' , array(
                        array(
                            'key' => 'rating_score_comment',
                            'value' => array(0, 5),
                            'compare' => 'BETWEEN'
                        ),
                        array(
                            'key' => 'de_event_post',
                            'value' => '',
                            'compare' => '!=',
                            'type' => 'NUMERIC'
                        )
                    ));
                }
            }
        }
        // Orderby featured
        // if(is_search()){
        //     $query->set('meta_key', 'et_featured');
        //     $query->set('orderby', 'meta_value_num date');
        // }

        $is_search = ! (empty($_REQUEST['query']) || empty($_REQUEST['query']['s']));
        if ((et_load_mobile() && !is_single() ) && !$is_search && (isset($query->query_vars['orderby']) && $query->query_vars['orderby'] != 'rand')) {
            if (!is_author() && isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == $this->post_type) {
                if(!isset($query->query_vars['near_lat']) || $query->query_vars['near_lat'] == '' ){
                    $query->set('orderby', 'meta_value_num date');
                }
                // if (isset($query->query_vars['meta_value'])) {
                //     unset($query->query_vars['meta_value']);
                // }
                $query->meta_query = array(
                     //check to see if et_featured has been filled out
                    'relation' => 'OR',
                    array(
                         //check to see if date has been filled out
                        'key' => 'et_featured',
                        'compare' => 'IN',
                        'value' => array(
                            0,
                            1
                        )
                    ) ,
                    array(
                         //if no et_featured has been added show these posts too
                        'key' => 'et_featured',
                        'value' => 1,
                        'compare' => 'NOT EXISTS'
                    )
                );
            }
        }
        return $query;
    }
    /**
     * filter order and replace menu_order by mt1.value
     * mt1.meta_value is the meta value when join with table post meta base on rating_score meta key
     * @param  String $orderby Wordpress post query orderby string
     * @return String
     *
     * @since  1.8.5
     * @author  Dakachi
     */
    function orderbyreplace($orderby) {
        global $wpdb;
        return str_replace($wpdb->posts.'.menu_order', 'mt1.meta_value+0', $orderby);
    }

    /**
     * catch event change ad status, update expired date
     * @param $new_status
     * @param $old_status
     * @param $post
     * @return bool|void
     */
    public function change_post_status($new_status, $old_status, $post) {

        // not is post type controled
        if ($post->post_type != $this->post_type) return;

        /**
         * check post package data
         */
        global $ae_post_factory;
        $pack = $ae_post_factory->get('pack');

        $sku = get_post_meta($post->ID, 'et_payment_package', true);
        $package = $pack->get($sku);

        $old_expiration = get_post_meta($post->ID, 'et_expired_date', true);

        /**
         * if an ad didnt have a package, force publish
         */
        if (!$package || is_wp_error($package)) {
            if ($new_status == 'publish') {
                do_action('ae_publish_post', $post->ID);
            }
            $this->mail->change_status($new_status, $old_status, $post);
            return false;
        };

        if (isset($package->et_duration)) {
            $duration = (int)$package->et_duration;
            if ($new_status == 'pending') {

                // clear ad expired date and post view when change from archive to pending
                if ($old_status == "archive" || $old_status == "draft") {

                    /**
                     * reset post expired date
                     */
                    update_post_meta($post->ID, 'et_expired_date', '');

                    /**
                     * reset post view
                     */
                    update_post_meta($post->ID, 'post_view', 0);

                    /**
                     * change post date
                     */
                    wp_update_post(array(
                        'ID' => $post->ID,
                        'post_date' => ''
                    ));
                }
            } elseif ($new_status == 'publish') {
                // update post expired date when publish
                if ($old_status == "archive" || $old_status == "draft") {
                    // force update expired date if job is change from draft or archive to publish
                    $expired_date = date('Y-m-d h:i:s', strtotime("+{$duration} days"));
                    update_post_meta($post->ID, 'et_expired_date', $expired_date);
                } else {

                    // update expired date when the expired date less then current time
                    if (empty($old_expiration) || current_time('timestamp') > strtotime($old_expiration)) {
                        $expired_date = date('Y-m-d h:i:s', strtotime("+{$duration} days"));
                        update_post_meta($post->ID, 'et_expired_date', $expired_date);

                        // echo get_post_meta( $post->ID, 'et_expired_date' , true );
                    }
                }
            }
        }

        // delete
        if ($package->et_not_duration == 1) {
            delete_post_meta($post->ID, 'et_expired_date');
        }

        if ($new_status == 'publish') {
            do_action('ae_publish_post', $post->ID);
        }


        /**
         * send mail when change ad status
         */
        $this->mail->change_status($new_status, $old_status, $post);
    }

    /**
     * action trigger when publish an ad
     * @param $ad_id
     */
    function publish_post_action($ad_id) {

        if (get_post_type($ad_id) != $this->post_type) return;

        $order = get_post_meta($ad_id, 'et_ad_order', true);
        if ($order) {

            /**
             * update order status
             */
            if (!isset($_POST['_et_nonce'])) wp_update_post(array(
                'ID' => $order,
                'post_status' => 'publish'
            ));

            $ads = new WP_Query(array(
                'post_type' => $this->post_type,
                'post_status' => array(
                    'pending'
                ) ,
                'meta_value' => $order,
                'meta_key' => 'et_ad_order',
                'posts_per_page' => - 1,
                'orderby' => 'post_date',
                'order' => 'DESC',
                'post__not_in' => array(
                    $ad_id
                )
            ));

            if (!$ads->have_posts()) return;

            /**
             * update ads in same package
             */
            $use_pending = ae_get_option('use_pending');

            if ($use_pending && !is_super_admin()) {
                foreach ((array)$ads->posts as $ad) {
                    wp_update_post(array(
                        'ID' => $ad->ID,
                        'post_status' => 'pending'
                    ));
                    update_post_meta($ad->ID, 'et_paid', 1);
                }
            } else {
                foreach ((array)$ads->posts as $ad) {
                    wp_update_post(array(
                        'ID' => $ad->ID,
                        'post_status' => 'publish'
                    ));
                    update_post_meta($ad->ID, 'et_paid', 1);
                }
            }
        }
    }

    /**
     * catch filter ae_convert_post to convert place data
     * @param array $result
     * @return array
     */
    public function convert_place($result) {
        global $user_ID;

        /**
         * return feauted image id
         */
        $result->featured_image = get_post_thumbnail_id($result->ID);
        if (!isset($result->the_post_thumnail) || $result->the_post_thumnail == '') {
            $default_thumbnail_img = ae_get_option('default_thumbnail_img', '');
            if($default_thumbnail_img && isset($default_thumbnail_img['medium'][0])){
                $attach_id = $default_thumbnail_img['attach_id'];
                $src = wp_get_attachment_image_src($attach_id, array(
                                '270',
                                '280'
                            ));
                $result->the_post_thumnail = $src[0];
            }
        }
        /**
         * generate all image thumbnail size
         */
        if (ae_user_can('edit_others_posts') || $user_ID == $result->post_author) {
            $result->big_post_thumbnail = wp_get_attachment_image_src($result->featured_image, 'big_post_thumbnail');
            $result->big_post_thumbnail = $result->big_post_thumbnail[0];
            $result->medium_post_thumbnail = wp_get_attachment_image_src($result->featured_image, 'medium_post_thumbnail');
            $result->medium_post_thumbnail = $result->medium_post_thumbnail[0];
            $result->review_post_thumbnail = wp_get_attachment_image_src($result->featured_image, 'review_post_thumbnail');
            $result->review_post_thumbnail = $result->review_post_thumbnail[0];
        }
        $result->small_post_thumbnail = wp_get_attachment_image_src($result->featured_image, 'small_post_thumbnail');
        $result->small_post_thumbnail = $result->small_post_thumbnail[0];

        /**
         * return carousels
         */
        if (current_user_can('manage_options') || $result->post_author == $user_ID) {
            $children = get_children(array(
                'numberposts'    => 15,
                'order'          => 'ASC',
                'post_mime_type' => 'image',
                'post_parent'    => $result->ID,
                'post_type'      => 'attachment'
            ));

            $result->et_carousels = array();

            foreach ($children as $key => $value) {
                $result->et_carousels[] = $key;
            }

            /**
             * set post thumbnail in one of carousel if the post thumbnail doesnot exists
             */
            if (has_post_thumbnail($result->ID)) {
                $thumbnail_id = get_post_thumbnail_id($result->ID);
                if (!in_array($thumbnail_id, $result->et_carousels)) $result->et_carousels[] = $thumbnail_id;
            }
        }
        /**
         * return claim approve
         */
        $result->et_claim_approve = get_post_meta( $result->ID, 'et_claim_approve', true );
        /**
         * return claimable
         */
        $result->et_claimable = get_post_meta( $result->ID, 'et_claimable', true );
        /**
         * return claim info
         */
        $result->et_claim_info = get_post_meta( $result->ID, 'et_claim_info', true );
        $post = get_post($result->ID);

        $result->post_date = '';
        if(isset($post->post_date)) {
            $result->post_date = et_the_time(strtotime($post->post_date));
        }
        $result->time_to_expired = '';
        if($result->post_status == 'publish' && $post->et_expired_date) {
            $result->time_to_expired = _date_diff(time(), strtotime($post->et_expired_date));
        }
        if($result->post_status == 'reject' && $post->reject_message == ''){
            $result->reject_message = __('No Message',ET_DOMAIN);
        }
        $result->trim_post_content = wp_trim_words(strip_tags($post->post_content),20);
        $result->renew_place = et_get_page_link('post-place', array('id' => $result->ID));
        /*Paid status*/
        if(get_post_meta( $result->ID, 'et_paid', true )==0)
            $paid_status = __("Unpaid", ET_DOMAIN);
        elseif (get_post_meta( $result->ID, 'et_paid', true )==1)
            $paid_status = __("Paid", ET_DOMAIN);
        else
            $paid_status = __("Free", ET_DOMAIN);
        $result->paid_status = $paid_status;
        /**
         * View count
         */
        $view_count = get_post_meta( $result->ID, 'view_count', true );
        if($view_count != ""){
            $result->view_count = (int)$view_count;
        }else{
            $result->view_count = 0;
        }
        //Add critical cate if it not exist set first cat
        if( !empty($tax['place_category'][0]) && empty($result->de_critical_cate) && $tax = $result->tax_input) {
            $result->de_critical_cate = $tax['place_category'][0]->term_id;
        }

        return $result;
    }

    /**
     * @param $args
     * @return mixed|void
     */
    public function filter_pre_insert($args) {
        /**
         * checking old data
         */
        if ($args['method'] == 'update') {
            $prev_post = get_post($args['ID']);

            // get current status and compare to display msg.

            if ($prev_post->post_status == 'reject') {

                // change post status to pending when edit rejected ad
                $args['post_status'] = 'pending';
            }
        }
        // validate
        return $this->validate_data($args);
    }

    /**
     * ajax callback sync post details
     * - update
     * - insert
     * - delete
     */
    function post_sync() {
        $request = $_REQUEST;
        global $ae_post_factory, $user_ID;
        if (!AE_Users::is_activate($user_ID)) {
            if($request['user_confirm'])
                $msg = __("Please open a new tab and confirm to complete the place submission",ET_DOMAIN);
            else
                $msg = __("Your account is pending. You have to activate your account to continue this step.", ET_DOMAIN);
            wp_send_json(array(
                'false',
                'msg' => $msg
            ));
        };
        /**
         * Verify captcha key
         * @author Tuandq
         */
        $captcha = isset($request['g-recaptcha-response']) ? $request['g-recaptcha-response'] : '';
        $is_captcha = isset($request['is_captcha']) ? $request['is_captcha'] : '';
        if(ae_get_option('gg_captcha', false) && $is_captcha!=="true" && !current_user_can( 'administrator' ) && $request['method'] == 'create' && ae_get_option('gg_secret_key')){
            //check google recaptcha
            $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=".ae_get_option('gg_secret_key')."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
            $response = json_decode(wp_remote_retrieve_body($response));
            if(!$response->success){
                wp_send_json(array(
                    'success' => false,
                    'msg' => __("Please enter a valid captcha!", ET_DOMAIN)
                ));
            }
        }
        // unset package data when edit place if user can edit others post
        if (isset($request['ID']) && !isset($request['renew']) && !isset($request['archive']) ) {
            unset($request['et_payment_package']);
        }
        $flag = false;
        if (isset($request['renew']) && $request['renew'] == 1 && isset($request['et_payment_package']) && $request['et_payment_package'] == ''  ) {
            $flag = true;
            unset($request['et_payment_package']);
        }
        if (isset($request['archive'])) {
            $request['post_status'] = 'archive';
        }
        if (isset($request['publish'])) {
            $request['post_status'] = 'publish';
        }
        if (isset($request['delete'])) {
            $request['post_status'] = 'trash';
        }

        $place = $ae_post_factory->get($this->post_type);


        // sync place
        $result = $place->sync($request);

        //var_dump($result);

        if (!is_wp_error($result)) {
            //Update rating post if active plugin multi_rating
            if(isset($request['de_critical_cate']) && isset($request['ID'])) {
                $de_multi_rating = new DE_Multirating_Plugin();
                $de_multi_rating->sync_rating_post($request['ID']);
            }

             wp_update_post(array(
                    'ID' => $result->ID
                ));
            // update place carousels
            if (isset($request['et_carousels'])) {

                // loop request carousel id
                foreach ($request['et_carousels'] as $key => $value) {
                    $att = get_post($value);

                    // just admin and the owner can add carousel
                    if (current_user_can('manage_options') || $att->post_author == $user_ID) {
                        wp_update_post(array(
                            'ID' => $value,
                            'post_parent' => $result->ID
                        ));
                    }
                }

                if (current_user_can('manage_options') || $att->post_author == $user_ID) {
                    /**
                     * featured image not null and should be in carousels array data
                     */
                    if (!isset($request['featured_image']) || $request['featured_image'] == '' ) {
                        set_post_thumbnail($result->ID, $value);
                    }
                }
            }

            /**
             * check payment package and check free or use package to send redirect link
             */
            if (isset($request['et_payment_package']) && $request['et_payment_package'] != '' ) {

                // check seller use package or not
                $check = AE_Package::package_or_free($request['et_payment_package'], $result);

                if ($check['success']) {
                    $result->redirect_url = $check['url'];
                }
                $result->response = $check;

                // check seller have reached limit free plan
                $check = AE_Package::limit_free_plan($request['et_payment_package']);
                if ($check['success']) {

                    // false user have reached maximum free plan
                    $response['success'] = false;
                    $response['msg'] = $check['msg'];

                    // send response to client
                    wp_send_json($response);
                }
            }
            // check payment package
            /**
             * check disable plan and submit place to view details
             */
            if ($this->disable_plan && ($request['method'] == 'create' || $flag)) {
                /**
                 * send email to admin
                 */
                $ae_mail = AE_Mailing::get_instance();
                $ae_mail->new_post_alert($result->ID);
                update_post_meta($result->ID, 'et_featured', '0');
                // disable plan, free to post place
                $response = array(
                    'success' => true,
                    'data' => array(

                        // set redirect url
                        'redirect_url' => $result->permalink
                    ) ,
                    'msg' => __("Submit place successfull.", ET_DOMAIN)
                );

                // send response
                wp_send_json($response);
            }

            // send json data to client
            wp_send_json(array(
                'success' => true,
                'data' => $result,
                'msg' => __("Update place successfull!", ET_DOMAIN),
            ));
        } else {

            // update false
            wp_send_json(array(
                'success' => false,
                'data' => $result,
                'msg' => $result->get_error_message()
            ));
        }
    }

    /**
     * ajax callback fetch post
     * @author Dakachi
     * @version 1.0
     */
    function fetch_posts() {

        global $ae_post_factory;
        $place = $ae_post_factory->get($this->post_type);
        $page = 1;
        if( isset( $_REQUEST['page'] ) && $_REQUEST['page'] != '' ){
            $page = $_REQUEST['page'];
        }
        extract($_REQUEST);

        $thumb = isset($_REQUEST['thumbnail']) ? $_REQUEST['thumbnail'] : 'thumbnail';

        /** @var Array $query */
        $status = isset($query['post_status']) ? $query['post_status'] : 'publish';
        $query_args = array(
            'paged' => $page,
            'thumbnail' => $thumb,
            'post_status' => $status,
            'showposts' => $query['showposts'],
            'posts_per_page' => ae_get_option('posts_per_page'),
        );
        // query author
        if(isset($query['author']) && $query['author']){
            $query_args['author'] = $query['author'];
        }
        // query search place
        if(isset($query['place_search']) && $query['place_search']){
            $query_args['s'] = $query['place_search'];
        }

        /** query all status */
        $total_status = array();
        $array_status = array('publish','pending','archive','reject','draft');
        foreach($array_status as $key => $value){
            $query_status = array(
                'post_status' => $value
            );
            if(isset($query['place_search']) && $query['place_search']){
                $query_status['s'] = $query['place_search'];
            }
            if(isset($query['author']) && $query['author']){
                $query_status['author'] = $query['author'];
            }
            $post = $place->fetch($query_status);

            if(!empty($post)){
                $total_status[$value] = $post['query']->found_posts;
            }else{
                $total_status[$value] = 0;
            }
        }
        /** query all status */

        $plural_string = __('%s places', ET_DOMAIN);
        $singular_string = __('%s place', ET_DOMAIN);

        // query place category
        $term = false;
        if (isset($query['place_category']) && $query['place_category']) {
            $query_args['place_category'] = $query['place_category'];
            $term = get_term_by('slug', $query_args['place_category'], 'place_category');

            $plural_string = __('%d places in "%s"', ET_DOMAIN);
            $singular_string = __('%d place in "%s"', ET_DOMAIN);
        }

        // query location
        if (isset($query['location']) && $query['location']) {
            $query_args['location'] = $query['location'];
            $query_args['tax_query'] = array(
                    array(
                        'taxonomy'=>'location',
                        'field'=> 'slug',
                        'terms'=> $query['location']
                        )
                    );
        }
        else {
            $query_args['location'] = '';
            $query_args['tax_query'] = array();
        }

        if (isset($query['place_tag']) && $query['place_tag']) {
            $query_args['place_tag'] = $query['place_tag'];
        }

        if (isset($query['radius'])) {
            $query_args['radius']  = $query['radius'];
        }
        /**
         *fetch data for neaby by widget
         *
         *@since version 1.8.4
         */
           // echo $query['near_lng']; exit();
        if( isset( $query['near_lat'] ) && isset( $query['near_lng'] )  ){
            $query_args['near_lat'] = $query['near_lat'];
            $query_args['near_lng'] = $query['near_lng'];
        }
        $query_args = wp_parse_args($query_args, $query);

        // add filter order by when try to order block fetured/event by rating score
        add_filter('posts_orderby', array($this, 'orderbyreplace')) ;
        //Paginate is none
        if(isset($query_args['post__in']) && $query_args['post__in'])
            $query_args['post__in'] = explode(",", $query_args['post__in']);
        /**
         * fetch data
         */
        $data = $place->fetch($query_args);
        // remove filter order by try to order block feature/event by rating score
        remove_filter('posts_orderby', array($this, 'orderbyreplace')) ;

        // get the pagination html string
        $paginate = '';
        if($page < $data['max_num_pages']){
            if($_REQUEST['paginate'] == 'refresh'){
                ob_start();
                de_refresh($data['query'], $page, $_REQUEST['paginate'], '',$_REQUEST['query']['meta_key']);
                $paginate = ob_get_clean();
            }else{
                ob_start();
                ae_pagination($data['query'], $page, $_REQUEST['paginate']);
                $paginate = ob_get_clean();
            }
        }

        if($page == $data['max_num_pages'] && $_REQUEST['paginate'] == 'page'){
            ob_start();
            ae_pagination($data['query'], $page, $_REQUEST['paginate']);
            $paginate = ob_get_clean();
        }


        if (!empty($data)) {

            /* return status filter*/
            $found_posts = $data['query']->found_posts;
            if ($found_posts > 1) {
                if ($term) {
                    $status = sprintf($plural_string, $found_posts, $term->name);
                } else {
                    $status = sprintf($plural_string, $found_posts);
                }
            } else {
                if ($term) {
                    $status = sprintf($singular_string, $found_posts, $term->name);
                } else {
                    $status = sprintf($singular_string, $found_posts);
                }
            }
        }

        $content_query = '<script type="application/json" class="ae_query">'. json_encode($query_args). '</script>';

        /**
         * send data to client
         */
        if (!empty($data)) {
            wp_send_json(array(
                'data'          => $data['posts'],
                'paginate'      => $paginate,
                'msg'           => __("Successs", ET_DOMAIN) ,
                'success'       => true,
                'max_num_pages' => $data['max_num_pages'],
                'status'        => $status,
                'type_status'   => isset($query['post_status']) ? $query['post_status'] : '',
                'total'         => $data['query']->found_posts,
                'total_status'  => $total_status,
                'query_noti'    => $content_query,
            ));
        } else {
            wp_send_json(array(
                'success'       => false,
                'data'          => array(),
                'max_num_pages' => 0,
                'total'         => 0,
                'type_status'   => isset($query['post_status']) ? $query['post_status'] : '',
                'total_status'  => $total_status,
                'query_noti'    => $content_query
            ));
        }
    }


    /**
     * catch ajax get next place in single place
     * @request : id
     */
    function next_place() {
        global $ae_post_factory;
        $place_object = $ae_post_factory->get('place');


        // false if not send id
        if (!isset($_REQUEST['id']))
            wp_send_json(array(
                'success' => false
            ));

        // get post details
        $id = $_REQUEST['id'];
        $query = new WP_Query(array(
            'post__in' => array(
                $id
            ) ,
            'post_type' => 'place',
            'post_status' => 'publish'
        ));

        /**
         * post exist
         */
        global $post;
        $next = '';
        $prev = '';
        $link = '';
        $title = '';

        if ($query->have_posts()) {
            ob_start();
            while ($query->have_posts()) {
                $query->the_post();
                $place = $place_object->convert($post);
                $current = $_REQUEST['current'];

//                get_template_part('template/single-place', 'more');

                include(locate_template('template/single-place-more.php'));
                $title = get_the_title($id) . ' | ' . ae_get_option('blogname');
                break;
            }
            $content = ob_get_clean();

            $link = get_permalink($id);

            // send data to client
            wp_send_json(array(
                'success' => true,
                'content' => $content,
                'next' => $next,
                'prev' => $prev,
                'link' => $link,
                'post_id' => $id, //$post->ID,
                'pageTitle' => $title
            ));
        } else {

            // post not exist
            wp_send_json(array(
                'success' => false
            ));
        }
    }

    /**
     * validate data
     * @param $data
     * @return mixed|void
     */
    public function validate_data($data) {

        $require_fields = apply_filters('de_place_required_fields', array(
            'et_full_location',
            'location',
            'place_category',
            'et_carousels'
        ));

        if (!current_user_can('manage_options')) {
            if ( !isset($data['et_payment_package']) && !$this->disable_plan && isset($data['renew']) && $data['renew'] == 1) {
                // if not disable package plan
                return new WP_Error('empty_package', __("Cannot create a place with an empty package.", ET_DOMAIN));
            }

            if (!isset($data['post_content']) || $data['post_content'] == '') {
                return new WP_Error('ad_empty_content', __("You should enter short description for your place.", ET_DOMAIN));
            }

            if (!isset($data['post_title']) || $data['post_title'] == '') {
                return new WP_Error('ad_empty_content', __("Your place should have a title.", ET_DOMAIN));
            }

            if (!isset($data['place_category']) && in_array('place_category', $require_fields) && !is_admin()) {
                return new WP_Error('invalid_category', __("An ad should has a category!", ET_DOMAIN));
            }

            if (!isset($data['location']) && in_array('location', $require_fields)) {
                return new WP_Error('invalid_location', __("An ad should has a location!", ET_DOMAIN));
            }

            if ((!isset($data['et_full_location']) || $data['et_full_location'] == '') && in_array('et_full_location', $require_fields)) {
                return new WP_Error('invalid_address', __("You should enter a valid address for your place!", ET_DOMAIN));
            }
            if ((!isset($data['et_carousels']) || $data['et_carousels'] == '') && in_array('et_carousels', $require_fields)) {
                return new WP_Error('invalid_carousels', __("You have to upload at least one photo!", ET_DOMAIN));
            }
        }

        /**
         * unsert featured et_featured param if user cannot  edit others posts
         */
        if (!ae_user_can('edit_others_posts')) {
            unset($data['et_featured']);
            // unset($data['post_status']);
            unset($data['et_expired_date']);
            unset($data['rating_score_comment']);
        }
        // if user create new place, set the rating score to 0
        if(!isset($data['ID']) ) $data['rating_score_comment'] = 0;
        /**
         * check payment package is valid or not
         * set up featured if this package is featured
         */
        if (isset($data['et_payment_package']) && $data['et_payment_package'] != '') {

            /**
             * check package plan exist or not
             */
            global $ae_post_factory;
            $package = $ae_post_factory->get('pack');

            $plan = $package->get($data['et_payment_package']);
            if (!$plan) return new WP_Error('invalid_plan', __("You have selected an invalid plan.", ET_DOMAIN));

            /**
             * if user can not edit others posts the et_featured will no be unset and check,
             * this situation should happen when user edit/add post in backend.
             * Force to set featured post
             */
            if (!isset($data['et_featured']) || !$data['et_featured']) {
                $data['et_featured'] = 0;
                if (isset($plan->et_featured) && $plan->et_featured) {
                    $data['et_featured'] = 1;
                }
            }
        }

        /**
         * check max category options, filter ad category
         */
        $max_cat = ae_get_option('max_cat', 3);
        if ($max_cat && !current_user_can('edit_others_posts')) {

            /**
             * check max category user can set for a place
             */
            $num_of_cat = count($data['place_category']);
            if ($max_cat < $num_of_cat) {
                for ($i = $max_cat; $i < $num_of_cat; $i++) {
                    unset($data['place_category'][$i]);
                }
            }
        }

        return apply_filters('de_place_validate_data', $data);
    }

    /**
     * List event button back Browser in place
     * @author: Dang Bui
     * @return object place back
     */
    public function button_back_place() {
        $id_place = $_POST['id_place'];
        if( ! isset($_POST['id_place'])) {
            $result = array('status' => 'error');
            wp_send_json($result);
        }

        $result = get_post($id_place);
        $permalink = get_permalink($id_place);
        $result->permalink = $permalink;
        if( ! $result)
            $result = array('status' => 'error');

        wp_send_json($result);
    }

    /**
     * @param $post_ID
     * @return bool
     * @author: Dang Bui
     */
    public function check_is_rating($post_ID) {
        global $user_ID, $wpdb;
        $sql =  $wpdb->prepare("SELECT count(*) FROM $wpdb->comments as C
                INNER JOIN $wpdb->commentmeta as M ON C.comment_ID = M.comment_id
                WHERE C.comment_post_ID = %d AND C.user_id = %d AND ((M.meta_key = 'et_rate_comment' AND M.meta_value > 0)
                OR (M.meta_key = 'et_multi_rate' AND M.meta_value NOT IN ('a:0:{}','')))", $post_ID, $user_ID);

        $results = $wpdb->get_var($sql);
        $results = (int)$results;
        if($results > 0)
            return true;

        return false;
    }

    /**
     * @param $post_id
     * @param $taxonomy
     * @return bool
     * @author: Dang Bui
     */

    public function de_get_related_place($post_id, $taxonomy = 'place_category') {
        $tax_query = get_the_terms($post_id, $taxonomy);

        $arr_tax_query['relation'] = 'OR';
        if ( $tax_query && ! is_wp_error( $tax_query ) ) {
            foreach ($tax_query as $k=>$v) {
                $arr_tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => $v->slug
                );
            }
        }

        $args_p = array(
            'post__not_in' => array($post_id),
            'post_type'    => 'place',
            'numberposts' => -1,
            'orderby' => 'rand',
            'order'    => 'ASC',
            'tax_query' => array(
                $arr_tax_query
            )
        );
        $arr_post = get_posts($args_p);

        return $arr_post;
    }

}