<?php
global $post, $wp_query,$ae_post_factory, $user_ID;
et_get_mobile_header(); 
$number         =   get_option('posts_per_page');
$args = array (
    'number' => $number,
    'count_total' => true,
    'role'=> 'author',
    'paginate'=>'load_more',
    'page_list_user' => 1
    );
$users_query    =   new WP_User_Query($args);

$total_users  =   $users_query->total_users;
$users  =   $users_query->results;

$total_pages  =   ceil($total_users/$number);

$ae_users   =   AE_Users::get_instance();
$found_posts = '<span class="found_post">'.$total_users.'</span>';
if($total_users <=1){
   $count_user =  sprintf(__('%s User', ET_DOMAIN) , $found_posts );
}
else{
    $count_user =  sprintf(__('%s Users', ET_DOMAIN) , $found_posts);
}
?>
<!-- Top bar -->
<section id="top-bar" class="section-wrapper"> 
    <div class="container">
        <div class="row">
            <div class="col-xs-6">
                <span class="title-page"><?php echo $count_user;?></span>
            </div>
            <div class="col-xs-6 search-list-user-page">
                <input class="search" type="text" name="user_search" id="user_search" placeholder="<?php _e('Keyword', ET_DOMAIN); ?>" value="" />
            </div>
        </div>
    </div>
</section>
    <!-- Top bar / End -->
<!-- Page List Users -->
<section id="list-user-page">
    <div class="container">
            <div class="row">
            
                <!-- Column left -->
                <div class="col-md-9 col-xs-12">
                	<div class="list-user-page-wrapper">
                        <ul class="list-user-page-info">
                        <?php 
                            if($users){
                                foreach ($users as $key => $value) {
                                    $user       = $ae_users->convert($value);
                                    $user_arr[] = $user;
                                    get_template_part('mobile/template/user', 'item'); 
                                }
                            wp_reset_query();
                            echo '<script type="json/data" class="userdata" > ' . json_encode($user_arr) . '</script>';    
                            }
                            else{
                                _e('0 user are found', ET_DOMAIN);
                            }
                        ?>
                        </ul>
                        <div class="paginations-wrapper">
                        <?php 
                             ae_user_pagination($args, $total_pages, 1);    
                        ?>
                        </div>
                    </div>
                    
                </div>
                <!-- Column left / End --> 
        </div>
    </div>
</section>
<!-- Page List Users / End -->   

<?php
et_get_mobile_footer();
