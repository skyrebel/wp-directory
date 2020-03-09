<?php
/**
 * Template Name: Rest Password
*/
global $post;

if(!isset($_GET['user_login']) || !isset($_GET['key'])) {
    wp_redirect(home_url());
}

get_header();
if(have_posts()) { the_post();

$user_login = isset($_REQUEST['user_login']) ? $_REQUEST['user_login'] :'';
$key        = isset($_REQUEST['key']) ? $_REQUEST['key'] :'';
?>
<!-- Breadcrumb Blog -->
<div class="section-detail-wrapper breadcrumb-blog-page">
    <ol class="breadcrumb">
        <li><a href="<?php echo home_url() ?>" title="<?php echo get_bloginfo( 'name' ); ?>" ><?php _e("Home", ET_DOMAIN); ?></a></li>
        <li><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></li>
    </ol>
</div>
<!-- Breadcrumb Blog / End -->

<!-- Page Blog -->
<section id="blog-page">
    <div class="container">
            <div class="row">
            
                <!-- Column left -->
                <div class="col-md-9 col-xs-12">
                
                    <div class="blog-wrapper">
                        <!-- post title -->
                        <div class="section-detail-wrapper padding-top-bottom-20">
                            <h1 class="media-heading title-blog"><?php _e("Reset password", ET_DOMAIN);  ?></h1>
                            <div class="clearfix"></div>
                        </div>
                        <!--// post title -->
                        
                        <div class="section-detail-wrapper padding-top-bottom-20 authentication-form">
                            <form role="form" id="resetpass_form">
                                <input type="hidden" value="<?php echo esc_attr($user_login) ;?>" name="user_login" id="user_login"/>  
                                <input type="hidden" value="<?php echo esc_attr($key) ;?>" name="user_key" id="user_key"/> 
                                <div class="form-group form-field">
                                    <label for="new_password"><?php _e("Your new password", ET_DOMAIN); ?></label>
                                    <input type="password" name="new_password" class="new_password form-control" id="new_password" placeholder=""/>
                                </div>
                                <div class="form-group form-field">
                                    <label for="re_new_password"><?php _e("Retype your password", ET_DOMAIN); ?></label>
                                    <input type="password" name="re_new_password" class="re_new_password form-control" id="re_new_password" placeholder=""/>
                                </div>
                                <div class="submit-style">
                                    <input type="submit" value="Submit" class="btn-submit"/>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
                <!-- Column left / End --> 
                
                <!-- Column right -->
                <?php get_sidebar( 'single' ); ?>
                <!-- Column right / End -->
        </div>
    </div>
</section>
<!-- Page Blog / End -->   

<?php
    //the_content();
}

get_footer();

