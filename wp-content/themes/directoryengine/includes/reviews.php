<?php

/**
 * place review class
 */
class AE_Review extends AE_Comments
{
    /**
     * @var $current_review
     */
    static $current_review;

    /**
     * @var $instance
     */
    static $instance;

    /**
     * return class $instance
     */
    public static function get_instance() {
        if (self::$instance == null) {

            self::$instance = new AE_Review();
        }
        return self::$instance;
    }

    /**
     * construct AE_Review
     */
    public function __construct() {
        $this->comment_type = 'review';
        $this->meta = array(
            'et_rate'
        );

        $this->post_arr = array();
        $this->author_arr = array();

        $this->duplicate = true;
        $this->limit_time = 120;
    }
}

/**
 * Class AE_ReviewAction
 */
class AE_ReviewAction extends AE_Base
{

    /**
     * PHP constructor.
     */
    public function __construct() {

        // $this->init_ajax();
        $this->add_action('preprocess_comment', 'process_review');

        // $this->add_action( 'comment_post' , 'update_rating');
        $this->add_action('wp_insert_comment', 'update_post_rating', 10, 2);

        $this->add_action('wp_insert_comment', 'send_mail', 11, 2);

        $this->add_action('trashed_comment',  'trash_comment', 10,2 );

        $this->add_action('untrashed_comment', 'untrash_comment', 10,2);

        $this->add_action('spammed_comment','trash_comment',11, 2);

        $this->add_action('unspammed_comment','untrash_comment',11, 2);

        $this->add_action('transition_comment_status', 'my_approve_comment_callback', 10, 3);

        $this->init_ajax();
    }




    function init_ajax() {

        $ce_priv_event = array(
            'ae-review-sync',
            'ae-add-favorite'
        );

        $this->add_ajax('ae-fetch-comments', 'fetch_comments', true, true);
        $this->add_filter('ae_convert_comment', 'convert_comment');
        foreach ($ce_priv_event as $key => $value) {
            $function = str_replace('ae-', '', $value);
            $function = str_replace('-', '_', $function);
            $this->add_ajax($value, $function, true, false);
        }
    }

    /**
     * catch filter ae_convert_comment to convert comment data
     * @param array $result
     * @return mixed
     */
    function convert_comment($result){
        $result->comment_content = htmlspecialchars ($result->comment_content,ENT_QUOTES);
        $result->time_ago = et_the_time(strtotime($result->comment_date));
        return $result;
    }

    /**
     * filter comment before new to check comment post ID and set comment type to review
     * @author Dakachi
     * @param Array $commentdata the array of comment data
     * @return array $comment_data
     */
    function process_review($commentdata) {
        global $user_ID, $current_user;
        $post = get_post($commentdata['comment_post_ID']);
        // comment on place
        if ($post->post_type == 'place') {
            if (isset($_POST['score']) || isset($_POST['comment_parent'])) {
                 // if have post score update comment to review
                $commentdata['comment_type'] = 'review';
                 if(!get_option('comment_moderation'))
                {
                       $commentdata['comment_approved'] = 1;
                }
                /**
                 * die if user not login and try to submit review
                 */
                if (!$commentdata['comment_parent'] && !$user_ID) {
                    wp_die(__('You have to login to post review.', ET_DOMAIN));
                }
            }
        }
        /**
         * Verify captcha key
         * @author Tuandq
         */
        $captcha = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
        $is_captcha = isset($request['is_captcha']) ? $request['is_captcha'] : '';
        if($post->post_type != 'place')
            $is_captcha = "true";
        if(ae_get_option('gg_captcha', false) && $is_captcha!=="true" && !current_user_can( 'administrator' ) && ae_get_option('gg_secret_key')){
            $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=".ae_get_option('gg_secret_key')."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
            $response = json_decode(wp_remote_retrieve_body($response));
            if(!$response->success){
                wp_die(__('Please enter a valid captcha!', ET_DOMAIN));
            }
        }
        $rate_score = 0;
        if(isset($_POST['score'])){
            $rate_score = round( $_POST['score'], 1);
        }
        if($rate_score > 0  ){
            $args = array(
                'author_email' =>$current_user->data->user_email,
                'post_id'=>$post->ID,
                'comment_type'=>'review'
                );
            $user_comments = get_comments($args);
            $flag = 0;
            if($user_comments){
                foreach ($user_comments as $key => $value) {
                    $rate = get_comment_meta($value->comment_ID, 'et_rate_comment' , true);
                    $rate = round( $rate, 1 );
                    if($rate && $rate > 0){
                        $flag = 1;
                        break;
                    }
                }
            }
            if($flag == 1){
                $flag == 0;
                wp_die(__("You can only rate a place for one time.", ET_DOMAIN));
            }
        }
        $time = 0;
        // review comment not too fast, should after 3 or 5 minute to post next review
        $comments = get_comments(array(
            'comment_type' => '',
            'post_id'=> $post->ID,
            'author_email' => $current_user->user_email,
            'number' => 1
        ));

        if (!empty($comments)) {
             // check latest comment
            $comment = $comments[0];
            $date = $comment->comment_date_gmt;
            $ago = time() - strtotime($date);
            $review = AE_Review::get_instance();
            if(isset($review->limit_time)){
                $time =  $review->limit_time;
            }
            //return error if comment to fast
            if ($ago < ((int)$time)) wp_die(__("Please wait 2 minutes after each action submission.", ET_DOMAIN));
        }


        return $commentdata;
    }

    /**
     * catch hook wp_insert_comment to send mail
     * @param int $comment_id
     * @param $comment
     * @author ThanhTu
     */

    function send_mail($comment_id, $comment){
        global $wpdb, $current_user;
        $post_id = $comment->comment_post_ID;
        $post = get_post($post_id);
        $user_data = get_userdata($post->post_author);

        if($post->post_type == 'place' && $comment->comment_type == 'review'){

            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $headers .= "From: ".get_option('blogname')." < ".get_option('admin_email') ."> \r\n";
            // Subject
            $subject = sprintf(__('Your place have a new comment!', ET_DOMAIN));

            // Content Mail
            $content = ae_get_option('ae_comment_place_mail');
            $content = str_ireplace('[display_name]', $user_data->display_name , $content);
            $content = str_ireplace('[place_link]', get_permalink($post->ID) , $content);
            $content = str_ireplace('[place_title]', $post->post_title , $content);
            $content = str_ireplace('[comment_author]', $comment->comment_author , $content);
            $content = str_ireplace('[comment_author_email]', $comment->comment_author_email , $content);
            $content = str_ireplace('[comment_date]', $comment->comment_date , $content);
            $content = str_ireplace('[comment_message]', $comment->comment_content , $content);
            $content = str_ireplace('[comment_link]', get_comment_link($comment->comment_ID), $content);
            return wp_mail($user_data->user_email, $subject, $content, $headers );
        }
    }

    /**
     * catch hook wp_insert_comment to update rating
     * @param int $comment_id
     * @param $comment
     * @author Dakachi
     */
    function update_post_rating($comment_id, $comment) {
       global $wpdb;
        $post_id = $comment->comment_post_ID;
        $post = get_post($post_id);
        if ($post->post_type == 'place') {
            // check comment rating post and update comment meta
            if (isset($_POST['score']) && $_POST['score']) {
                $rate = round( $_POST['score'], 1 );
                if ($rate > 5) $rate = 5;
                // et_rate story rating point for each comment
                //update_comment_meta($comment_id, 'et_rate', $rate);
                update_comment_meta($comment_id, 'et_rate_comment', $rate);
            }
            // check carousel comment nad update carousel comment meta
            if(isset($_POST['et_carousel_comment']) && $_POST['et_carousel_comment']){
                update_comment_meta($comment_id, 'et_carousel_comment', $_POST['et_carousel_comment']);
            }

            // update post rating score
            $sql = "SELECT AVG(M.meta_value)  as rate_point, COUNT(C.comment_ID) as count
                    FROM    $wpdb->comments as C
                        JOIN $wpdb->commentmeta as M
                                on C.comment_ID = M.comment_id
                    WHERE   M.meta_key = 'et_rate_comment'
                            AND C.comment_post_ID = $post_id
                            AND C.comment_approved = 1";

            $results = $wpdb->get_results($sql);

            // update post rating score
            update_post_meta($post_id, 'rating_score_comment',round($results[0]->rate_point,1));
            update_post_meta($post_id, 'reviews_count', $results[0]->count);
            }
        }

    /**
     * @param $new_status
     * @param $old_status
     * @param $comment
     */
    function my_approve_comment_callback($new_status, $old_status, $comment) {
        if ($old_status != $new_status) {
            if ($new_status == 'approved' || $new_status == 'unapproved') {
                $this->update_post_rating($comment->comment_ID, $comment);
            }
        }
    }

    /**
     * sync review (create)
     */
    function review_sync() {
        $args = $_POST['content'];

        /**
         * validate data
         */
        if (empty($args['comment_content']) || empty($args['comment_post_ID'])) {
            wp_send_json(array(
                'success' => false,
                'msg' => __("Please fill in required field.", ET_DOMAIN)
            ));
        }

        $review = AE_Review::get_instance();
        $comment = $review->insert($args);

        if (!is_wp_error($comment)) {
            wp_send_json(array(
                'success' => true,
                'msg' => __("Your review has been submitted.", ET_DOMAIN)
            ));
        } else {
            wp_send_json(array(
                'success' => false,
                'msg' => $comment->get_error_message()
            ));
        }
    }

    /**
     * fetch comment
     */
    function fetch_comments() {

        global $ae_post_factory;
        $review_object = $ae_post_factory->get('de_review');
         // get review object
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 2;
        $query = $_REQUEST['query'];

        $query['page'] = $page;
        //add_filter( 'comments_clauses' , array($this, 'groupby') );
        $data = $review_object->fetch($query);
        if (!empty($data)) {
            $data['success'] = true;
            wp_send_json($data);
        } else {
            wp_send_json(array(
                'success' => false,
                'data' => $data
            ));
        }
    }

    /**
     * @param $args
     * @return mixed
     */
    function groupby( $args ){
        global $wpdb;
        $args['groupby'] = ' ' .$wpdb->comments.'.comment_post_ID';
        return $args;
    }

    /**
     * catch hook trashed_comment to strash comment
     * @param $comment_id
     */
    function trash_comment($comment_id)  {
        $comment = get_comment($comment_id);
        $post_id = $comment->comment_post_ID;
        $post = get_post($post_id);
        // $post_meta = get_post_meta( $post_id);
        // $comment_meta = get_comment_meta($comment_id);
        global $wpdb;

        if ($post->post_type == 'place') {

            // update post rating score
            $sql = "SELECT AVG(M.meta_value)  as rate_point, COUNT(C.comment_ID) as count
                    FROM $wpdb->comments as C
                    join $wpdb->commentmeta as M
                        on C.comment_ID = M.comment_id
                            and M.meta_key = 'et_rate_comment'
                            and C.comment_post_ID = $post_id
                            and C.comment_approved = 1";
            $results = $wpdb->get_results($sql);

            $rate_point = 0;
            if($results[0]->rate_point > 0){
                $rate_point = $results[0]->rate_point;
            }

            update_post_meta($post_id, 'rating_score_comment',round($results[0]->rate_point, 1));
            update_post_meta($post_id, 'reviews_count', $results[0]->count);

            $sql = "SELECT M.meta_value  as rate_point
                        FROM $wpdb->comments as C
                            join $wpdb->commentmeta as M
                            ON C.comment_ID = M.comment_id
                        WHERE   M.meta_key = 'et_multi_rate'
                                AND C.comment_post_ID = $post_id
                                AND C.comment_approved = 1";
            $results = $wpdb->get_results($sql);

            $meta_array = array();
            $count_multi_review = 0;
            foreach ($results as $key => $value) {
                if ($this->isSerialized($value->rate_point)) {
                    foreach (unserialize($value->rate_point) as $criteria => $criteria_value) {
                        if (!isset($meta_array[$criteria])) {
                            $meta_array[$criteria] = 0;
                        }
                        $meta_array[$criteria] += (float)$criteria_value;
                    }
                    $count_multi_review++;
                }
            }


            $sum = 0;
            $overview = 0;
            if (!empty($meta_array)) {
                foreach ($meta_array as $key => $value) {
                    $meta_array[$key] = $value/$count_multi_review;
                    $sum+= $meta_array[$key];
                }
                $overview = round($sum/count($meta_array), 1);
            }

            // update post rating score
            update_post_meta($post_id, 'multi_overview_score', $overview);
            // update post rating_score
            update_post_meta($post_id, 'rating_score', $overview);
            update_post_meta($post_id, 'multi_rating_score', $meta_array);
            // post review count
            update_post_meta($post_id, 'multi_reviews_count',  $count_multi_review);
            update_post_meta($post_id, 'reviews_count',  $count_multi_review);
        }
    }

    /**
     * catch hook untrashed_comment to unstrash comment
     * @param $comment_id
     */
    function untrash_comment($comment_id){
        $comment = get_comment($comment_id);
        $post_id = $comment->comment_post_ID;
        $post = get_post($post_id);
        // $post_meta = get_post_meta( $post_id);
        // $comment_meta = get_comment_meta($comment_id);
        global $wpdb;

        if ($post->post_type == 'place') {
            // update post rating score
            $sql = "SELECT AVG(M.meta_value)  as rate_point, COUNT(C.comment_ID) as count
                    FROM    $wpdb->comments as C
                        JOIN $wpdb->commentmeta as M
                                on C.comment_ID = M.comment_id
                    WHERE   M.meta_key = 'et_rate_comment'
                            AND C.comment_post_ID = $post_id
                            AND C.comment_approved = 1";

            $results = $wpdb->get_results($sql);

            // update post rating score
            update_post_meta($post_id, 'rating_score', $results[0]->rate_point);
            update_post_meta($post_id, 'reviews_count', $results[0]->count);
        }
    }


}

/**
 * class AE_Favorite
 * declare favorite data and config
 * @author Dakachi
*/
class AE_Favorite extends AE_Comments
{
    /**
     * return class $instance
     */
    public static $instance;

    /**
     * @return AE_Favorite
     */
    public static function get_instance() {
        if (self::$instance == null) {

            self::$instance = new AE_Favorite();
        }
        return self::$instance;
    }

    /**
     * construct AE_Favorite
     */
    public function __construct() {
        $this->comment_type = 'favorite';
        $this->meta = array();

        $this->post_arr = array();
        $this->author_arr = array();
        // not allow duplicate, user just can post one favorite (comment) on a post
        $this->duplicate = false;
        // set limit time for each submision post
        $this->limit_time = 120;

    }
}
/**
 * class AE_Favorite
 * declare favorite data and config
 * @author Dakachi
*/
class AE_Report extends AE_Comments
{
    /**
     * return class $instance
     */

    public static $instance;

    /**
     * @return AE_Report $instance
     */
    public static function get_instance() {
        if (self::$instance == null) {

            self::$instance = new AE_Report();
        }
        return self::$instance;
    }

    /**
     * PHP Construct
     */
    public function __construct() {
        $this->comment_type = 'report';
        $this->meta         = array();

        $this->post_arr     = array();
        $this->author_arr   = array();
        // not allow duplicate, user just can post one favorite (comment) on a post
        $this->duplicate    = false;
        // set limit time for each submision post
        $this->limit_time   = 120;
    }
}
/**
 * class AE_FavoriteAction init all action work with class AE_Favorite
 * @author Dakachi
 * @version 1.0
*/
class AE_FavoriteAction extends AE_Base
{
    /**
     * construct AE_FavoriteAction
     */
    public function __construct() {
        $this->comment = AE_Favorite::get_instance();

        $this->add_ajax('ae-sync-favorite', 'sync_favorite', true, false);
        $this->add_ajax('ae-fetch-favorite', 'fetch_favorite', true, false);
        //Before getting the comments, on the WP_Comment_Query object for each comment
        $this->add_action('pre_get_comments', 'comment_admin_list');
    }

    public function sync_favorite(){
        $action = $_REQUEST['sync'];
        switch ($action) {
            case 'add':
                $this->add_favorite($_REQUEST);
                break;

            default:
                $this->remove_favorite($_REQUEST);
                break;
        }
    }

    /**
     * ajax callback fetch post
     * @author ThanhTu
     * @since 1.0
     */
    function fetch_favorite(){
        global $ae_post_factory;
        $review_object = $ae_post_factory->get('de_favorite');
        // get review object
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 2;
        $query = $_REQUEST['query'];

        $query['page'] = $page;
        //add_filter( 'comments_clauses' , array($this, 'groupby') );
        $data = $review_object->fetch($query);

        ob_start();
        ae_comments_pagination($query['total'],$page, array(
            'user_id' => $query['user_id'],
            'type'        => 'review',
            'status'      => 'approve',
            'number' => $query['number'],
            'total' => $query['total'],
            'post_type' => 'place',
            'page' => $page,
            'paginate' => $query['paginate']
        ));

        $paginate = ob_get_clean();
        $result = array(
            'max_num_pages' => $query['total'],
            'success' => true,
            'paginate' => $paginate,
            'total' => $query['total'],
            'data' => $data['data']
        );
        if (!empty($data)) {
            $data['success'] = true;
            wp_send_json($result);
        } else {
            wp_send_json(array(
                'success' => false,
                'data' => $result
            ));
        }
    }

    /**
     * ae-add-favorite ajax callback
     * @since 1.0
     * @param $request
     */
    function add_favorite($request) {
        global $user_ID;
        $args = array();

        /**
         * validate data
         */
        if (empty($request['comment_post_ID'])) {
            wp_send_json(array(
                'success' => false,
                'msg' => __("Please fill in required field.", ET_DOMAIN)
            ));
        }

        if (!$user_ID) {
            wp_send_json(array(
                'success' => false,
                'msg' => __("You have to login.", ET_DOMAIN)
            ));
        }

        /**
         * set favorite data
         */
        $args['comment_post_ID'] = $_REQUEST['comment_post_ID'];
        $args['comment_approved'] = 1;
        $args['comment_content'] = __('Love it.', ET_DOMAIN);
        $args['type'] = 'favorite';

        $comment = $this->comment->insert($args);

        if (!is_wp_error($comment)) {
            wp_send_json(array(
                'success' => true,
                'msg' => __("Added to favorite.", ET_DOMAIN),
                'text' => __("Remove item from favorite list", ET_DOMAIN),
                'data' => $comment
            ));
        } else {
            wp_send_json(array(
                'success' => false,
                'msg' => $comment->get_error_message()
            ));
        }
    }

    /**
     * user remove favorite
     * @since 1.0
     * @param  array $request
     */
    function remove_favorite ($request) {
        global $current_user;
        $comment = get_comment( $request['ID'], OBJECT );
        if(!current_user_can( 'edit_others_posts' )) {

            if( $comment->comment_author_email  !== $current_user->user_email ) {
                wp_send_json( array('success' => false) );
            }
        }
        if( $comment->comment_type == $this->comment->comment_type ) {
            wp_delete_comment( $comment->comment_ID , true );
            wp_send_json( array('success' => true , 'text' => __("Remove favorite successfull.", ET_DOMAIN)) );
        }else {
            wp_send_json( array('success' => false , 'text' => __("Cannot remove comment.", ET_DOMAIN)) );
        }
    }
    /**
     * Filter list comment remove favorite
     * @author: Dang Bui
     * @param $param
     * @return mixed
     */
    function comment_admin_list($param) {
        if(is_admin())
        {
            $current_screen = get_current_screen();
            if($current_screen->base == 'edit-comments')
                $param->query_vars['type__not_in'] = array('favorite');
        }
        return $param;
    }
}
/**
 * new class
*/
new AE_FavoriteAction();

/**
 * class AE_ReportAction init all action work with class AE_Report
 * @author Dakachi
 * @version 1.0
*/
class AE_ReportAction extends AE_Base
{
    /**
     * construct AE_ReportAction
     */
    public function __construct() {
        $this->comment = AE_Report::get_instance();
        $this->add_ajax('ae-sync-report', 'sync_report', true, false);
    }

    public function sync_report(){
        $action = $_REQUEST['sync'];
        switch ($action) {
            case 'add':
                $this->add_report($_REQUEST);
                break;

            default:
                break;
        }
    }

    /**
     * ae-add-favorite ajax callback
     * @since 1.0
     * @param array $request
     */
    function add_report($request) {
        global $user_ID;
        $args = array();

        /**
         * validate data
         */
        if (empty($request['comment_post_ID'])) {
            wp_send_json(array(
                'success' => false,
                'msg' => __("Please fill in required field.", ET_DOMAIN)
            ));
        }

        if (!$user_ID) {
            wp_send_json(array(
                'success' => false,
                'msg' => __("You have to login.", ET_DOMAIN)
            ));
        }

        /**
         * set favorite data
         */
        $args['comment_post_ID']  = $_REQUEST['comment_post_ID'];
        $args['comment_approved'] = 1;
        $args['comment_content']  = $_REQUEST['comment_content'];
        $args['type']             = 'report';

        $comment = $this->comment->insert($args);

        if (!is_wp_error($comment)) {

            do_action( 'ae_after_report', $_REQUEST );

            wp_send_json(array(
                'success' => true,
                'msg'     => __("You've reported to admins successfully.", ET_DOMAIN),
                'data'    => $comment
            ));
        } else {
            wp_send_json(array(
                'success' => false,
                'msg'     => $comment->get_error_message()
            ));
        }
    }
}
/**
 * new class
*/
new AE_ReportAction();