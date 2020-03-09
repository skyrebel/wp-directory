<?php
class DE_Multirating_Plugin {
    public function __construct()
    {
        //add action active plugin de_multiranting
        add_action('activate_de_multirating/de_multirating.php', array($this, 'activate_update_rating'),10,2);
        add_action('deactivate_de_multirating/de_multirating.php', array($this, 'deactivate_update_rating'), 10,2);
    }

    /**
     * active plugin call
     * @author: Dang Bui
     * @return  array update comment meta
     */
    public function activate_update_rating() {
        $comments = get_comments();
        $arr_post = array();
        $result = array();
        foreach ($comments as $k=>$v) {
            if( ! empty($v)) {
                $this->sync_rating_comment($v->comment_ID, $v->comment_post_ID);
                $arr_post[] = $v->comment_post_ID;
            }
        }

        //Sync post
        foreach (array_unique(array_filter($arr_post)) as $key=>$value) {
            $this->sync_rating_post($value);
        }

        return $result;
    }
    /**
     * deactivate_update_rating
     */
    public function deactivate_update_rating() {
        $posts = get_posts(array(
            'post_type'   => 'place',
            'post_status' => 'publish',
            'numberposts'   => -1,
        ));
        if( ! empty($posts)) {
            foreach ($posts as $k => $v) {
                $this->sync_default_rating_post($v->ID);
            }
        }
    }

    /**
     * Sync comment
     * @param $comment_id
     * @author: Dang Bui
     */

    public function sync_rating_comment($comment_id, $post_id) {
        //Get array critical array name
        $arr_critical_name = $this->get_array_critical_post($post_id);
        $et_rate = get_comment_meta($comment_id, 'et_rate', true);
        $et_multi_rate = get_comment_meta($comment_id,'et_multi_rate',true);
        if( ! empty($et_rate) && empty($et_multi_rate) && ! empty($arr_critical_name)) {
            $value_multi_ranting = $this->make_array_multi_rate($arr_critical_name, $et_rate);
            //Update et multi rate
            update_comment_meta($comment_id,'et_rate_comment',$value_multi_ranting);
        }
    }

    /**
     * Sync default rating
     * @param $post_id
     * @author: Dang Bui
     */
    public function sync_default_rating_post($post_id) {
        global $wpdb;
        // update post rating score
        $sql = "SELECT M.meta_value  as rate_point
                        FROM $wpdb->comments as C
                            join $wpdb->commentmeta as M
                            ON C.comment_ID = M.comment_id
                        WHERE   M.meta_key = 'et_rate'
                                AND C.comment_post_ID = $post_id
                                AND C.comment_approved = 1";
        $results = $wpdb->get_results($sql);

        $count_review = 0;
        $total = 0;
        $overview = 0;
        if( ! empty($results)) {
            foreach ($results as $key => $value) {
                if($value->rate_point > 0) {
                    $total += $value->rate_point;
                    $count_review++;
                }
            }
            $overview = round($total/$count_review, 1);
        }

        // update post rating_score
        update_post_meta($post_id, 'rating_score', $overview);
        update_post_meta($post_id, 'multi_overview_score', $overview);
        // post review count
        update_post_meta($post_id, 'multi_reviews_count',  $count_review);

    }

    /**
     * Sync multi rating post
     * @param $post_id
     * @author: Dang Bui
     */

    public function sync_rating_post($post_id) {
        global $wpdb;
        //Get rating critical
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

    /**
     *Check serialized data from  post meta value
     *
     *@since version 1.0
     *@param string $str
     *@return true if this string is isSerialized from array / false if this isn't
     */
    public function isSerialized( $str ) {
        return ( $str == serialize( false ) || @unserialize( $str ) !== false );
    }

    /**
     * Make array multi rate
     * @author: Dang Bui
     * @param $arr_critical_name
     * @param $et_rate
     * @return  array multi rate
     */
    public function make_array_multi_rate($arr_critical_name, $et_rate) {
        if(empty($arr_critical_name))
            return false;
        $result = array();
        foreach ($arr_critical_name as $k => $v) {
            $result[$v] = $et_rate;
        }

        return $result;
    }

    /**
     * Get array critical name in post
     * @author: Dang Bui
     * @param $post_id
     * @return array critical name in post |false if not get
     */
    public function get_array_critical_post($post_id) {
        $enable_critical = ae_get_option('enable_critical');
        $option_critical = get_option('option_critical');
        if(empty($option_critical))
            return false;

        if( ! get_post($post_id))
            return false;

        $critical_name = array();
        if($enable_critical) {
            $critical = $this->get_id_critical_post($post_id);
            if( ! empty($critical) && array_key_exists($critical, $option_critical)) {
                $arr_critical = $option_critical[$critical];
                foreach ($arr_critical as $k=>$v) {
                    $term_name = $this->get_term($v);
                    if( ! empty($term_name))
                        $critical_name[] = $term_name[0]->name;
                }
            }
        } else {
            $default = array(
                'orderby' => ae_get_option('de_multirating_orderby', 'name'),
                'order' => ae_get_option('de_multirating_order', 'DESC')
            );
            $term_list = wp_get_object_terms($post_id, 'review_criteria', $default);
            foreach ($term_list as $key => $value) {
                array_push($critical_name, $value->name);
            }
        }

        if(empty($critical_name))
            return false;

        return $critical_name;
    }
    /**
     * Get term
     * @author: Dang Bui
     * @param $term_id
     * @return array term
     */
    public function get_term($term_id) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT * FROM $wpdb->terms WHERE term_id = %d", $term_id);
        $result = $wpdb->get_results($sql);
        return $result;
    }
    /**
     * Get critical in post
     * @author: Dang Bui
     * @param $post_id
     * @return id critical in post |false if not get
     */
    public function get_id_critical_post($post_id){
        $critical_cate = 0;
        $critical_cate = get_post_meta($post_id, 'de_critical_cate');
        if( ! $critical_cate) {
            //if critical not exist get term first of taxonomy place_category
            $term = $this->get_term_in_post($post_id, 'place_category');

            if( ! empty($term[0])) {
                $critical_cate =  $term[0]->term_id;
            }
        } else {
            $critical_cate = $critical_cate[0];
        }

        if($critical_cate == 0)
            return false;

        return $critical_cate;
    }
    /**
     * Get term in post
     * @author: Dang Bui
     * @param $post_id
     * @param $tax name
     * @return array term
     */
    public function get_term_in_post($post_id, $tax) {
        $result = wp_get_post_terms($post_id, $tax);

        return $result;
    }
}

new DE_Multirating_Plugin();


