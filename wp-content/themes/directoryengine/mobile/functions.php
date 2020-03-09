<?php
/**
 * this file contain all mobile functions
*/
function mobile(){

}


/**
 * Display the paginate of event on device Mobile
 * @param Array $query
 * $return string HTML of Paginate
 */
function de_mobile_event_pagination($query){
	$query_var  =   array();
    
    /**
     * posttype args
    */
    $query_var['post_type']     =   $query->query_vars['post_type'] != ''  ? $query->query_vars['post_type'] : 'post' ;
    $query_var['post_status']   =   isset( $query->query_vars['post_status'] ) ? $query->query_vars['post_status'] : 'publish';
    $query_var['orderby']       =   isset( $query->query_vars['orderby'] ) ? $query->query_vars['orderby'] : 'date';
    // taxonomy args
    $query_var['place_category']   =   isset( $query->query_vars['place_category'] ) ? $query->query_vars['place_category'] : '';
    $query_var['location']   =   isset( $query->query_vars['location'] ) ? $query->query_vars['location'] : '';
    $query_var['showposts']   =   isset( $query->query_vars['showposts'] ) ? $query->query_vars['showposts'] : '';
    /**
     * order
    */
    $query_var['order']         =   $query->query_vars['order'];
    
    if(!empty($query->query_vars['meta_key']))
        $query_var['meta_key']      =   isset( $query->query_vars['meta_key'] ) ? $query->query_vars['meta_key'] : 'rating_score';

    $query_var  =   array_merge($query_var, $query->query );

    echo '<script type="application/json" class="ae_query">'. json_encode($query_var). '</script>';
    
    echo '<div class="paginations">';
    echo '<a id="event-inview" class="inview load-more-post" >'. __("Load more", ET_DOMAIN) .'</a>';
    echo '</div>';
}
add_action( 'wp_ajax_de-mobile-fetch-events', 'de_mobile_fetch_events' );
add_action( 'wp_ajax_nopriv_de-mobile-fetch-events', 'de_mobile_fetch_events' );
function de_mobile_fetch_events(){
	query_posts( $_REQUEST['query'] );
	if(have_posts()) {
		ob_start();
		get_template_part( 'mobile/template/list', 'events' ); 
		$content = ob_get_clean();
		wp_send_json( array('success' => true, 'data' => $content) );	
	}else {
		wp_send_json_error();
	}
}

// function my_style_method() {
// 	if(et_load_mobile()) {
// 		wp_enqueue_script(
// 			'custom_css',
// 			get_stylesheet_directory_uri() . '/custom-mobile.css'
// 		);
// 	}

// }
// add_action('wp_enqueue_scripts', 'my_style_method');
add_action('login_init', 'redirect_login');
function redirect_login(){
        $flag = ae_get_option('login-init');
        $re = (de_check_register()) ? true : false;
        if(!is_user_logged_in()){
            if($flag && $re){
                wp_redirect( home_url() );
            }
        }
    }