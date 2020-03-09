<?php
/**
 * Template Name: Login
*/
global $user_ID;
// user already login redirect to home page
if($user_ID) {
    // isset redirect url
    if(isset($_REQUEST['redirect'])) {
        wp_redirect($_REQUEST['redirect']);
        exit;
    }
    wp_redirect(home_url());
    exit;
}

et_get_mobile_header();
if(have_posts()) { the_post();
?>

<div id="page-authentication" >
    <div id="login">
        <!-- Top bar -->
        <section id="top-bar" class="section-wrapper"> 
            <div class="container">
                <div class="row">
                    <div class="col-xs-6">
                        <h1 class="title-page"><?php _e("Login", ET_DOMAIN); ?></h1>
                    </div>
                </div>
            </div>
        </section>
        <!-- Top bar / End -->
        
        <!-- List News -->
        <section id="login-page-wrapper" class="section-wrapper"> 
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="login-page">
                            <div class="message">
                            </div>
                            <div class="content-news">
                                <form method="post" class="signin_form form_modal_style" id="signin_form">
                                    <div class="form-field user_name">
                                        <label style="display: block"><?php _e("Username or Email", ET_DOMAIN) ?></label>
                                        <input type="text" class="text-field email_user"  name="user_login" id="sig_name" />
                                    </div>
                                    <div class="form-field user_password">
                                        <label style="display: block"><?php _e("Password", ET_DOMAIN) ?></label>
                                        <input type="password" class="text-field password_user" id="sig_pass" name="user_pass" />
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="form-field submit_singin" style="padding-top: 20px">
                                        <p class="form-submit">
                                            <input name="submit" type="submit" id="submit" value="<?php _e( 'SIGN IN' , ET_DOMAIN ); ?>"/>
                                            &nbsp;&nbsp;
                                            <?php 
                                                $register_user = de_check_register();
                                                if($register_user)
                                                    echo '<a href="#" class="link_sign_up"> '. __("Sign up", ET_DOMAIN) .'</a>';
                                            ?>
                                        </p>                                    
                                            <?php
                                            if( function_exists('ae_render_social_button')){
                                                $icon_classes = array(
                                                    'fb' => 'fa fa-facebook-square',
                                                    'gplus' => 'fa fa-google-plus-square',
                                                    'tw' => 'fa fa-twitter-square',
                                                    'lkin' => 'fa fa-linkedin-square'
                                                    );
                                                $button_classes = array(
                                                    'fb' => 'sc-icon color-facebook',
                                                    'gplus' => 'sc-icon color-google',
                                                    'tw' => 'sc-icon color-twitter',
                                                    'lkin' => 'sc-icon color-linkedin'
                                                    );
                                                ae_render_social_button( $icon_classes, $button_classes ); 
                                            }
                                         ?>
                                        <p><a href="#" class="link_forgot_pass"><?php _e("Forgot password", ET_DOMAIN) ?>&nbsp;<i class="fa fa-question-circle"></i></a></p>
                                     </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- List News / End -->
    </div>

    <div id="register" style="display:none;">
        <!-- Top bar -->
        <section id="top-bar" class="section-wrapper"> 
            <div class="container">
                <div class="row">
                    <div class="col-xs-6">
                        <h1 class="title-page"><?php _e("Register", ET_DOMAIN); ?></h1>
                    </div>
                </div>
            </div>
        </section>
        <!-- Top bar / End -->
        
        <!-- List News -->
        <section id="login-page-wrapper" class="section-wrapper"> 
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="login-page">
                            <div class="message">
                            </div>
                            <div class="content-news">
                                <form method="post" class="signin_form form_modal_style" id="signup_form">
                                    <div class="form-field user_name">
                                        <label style="display: block"><?php _e("Username", ET_DOMAIN) ?></label>
                                        <input type="text" class="text-field email_user"  name="user_login" id="sig_name" />
                                    </div>
                                    <div class="form-field user_name">
                                        <label style="display: block"><?php _e("Email", ET_DOMAIN) ?></label>
                                        <input type="text" class="text-field email_user"  name="user_email" id="sig_name" />
                                    </div>
                                    <div class="form-field user_password">
                                        <label style="display: block"><?php _e("Password", ET_DOMAIN) ?></label>
                                        <input type="password" class="text-field password_user" id="sig_pass" name="user_pass" />
                                    </div>
                                    <div class="form-field user_password">
                                        <label style="display: block"><?php _e("Retype  Password", ET_DOMAIN) ?></label>
                                        <input type="password" class="text-field password_user" id="repeat_pass" name="repeat_pass" />
                                    </div>
                                    <?php if(ae_get_option('gg_captcha')){ ?>
                                        <div class="form-field user_password">
                                            <?php ae_gg_recaptcha(); ?>
                                        </div>
                                    <?php } ?>
                                    <div class="clearfix"></div>
                                    <div class="form-field submit_singin" style="padding-top: 20px">
                                        <p class="form-submit">
                                            <input name="submit" type="submit" id="submit" value="<?php _e( 'SIGN UP' , ET_DOMAIN ); ?>" />
                                            &nbsp<a href="#" class="link_sign_in"><?php _e("Sign in", ET_DOMAIN) ?></a>
                                        </p>                                    
                                     </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- List News / End -->
    </div>

    <div id="forgotpass" style="display:none;">
        <!-- Top bar -->
        <section id="top-bar" class="section-wrapper"> 
            <div class="container">
                <div class="row">
                    <div class="col-xs-8">
                        <h1 class="title-page"><?php _e("Request password", ET_DOMAIN); ?></h1>
                    </div>
                </div>
            </div>
        </section>
        <!-- Top bar / End -->
        
        <!-- List News -->
        <section id="login-page-wrapper" class="section-wrapper"> 
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="login-page">
                            <div class="message">
                            </div>
                            <div class="content-news">
                            <form method="post" class="signin_form form_modal_style" id="forgotpass_form">
                                <div class="form-field user_name">
                                    <label style="display: block"><?php _e("Your Email", ET_DOMAIN) ?></label>
                                    <input type="text" class="text-field email email_user"  name="user_email" id="forgotpassmail" />
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-field submit_singin" style="padding-top: 20px">
                                    <p class="form-submit">
                                        <input name="submit" type="submit" id="submit" value="<?php _e( 'SUBMIT' , ET_DOMAIN ); ?>"/>
                                        &nbsp<a href="#" class="link_sign_in"><?php _e("Sign in", ET_DOMAIN) ?></a>
                                    </p>                                    
                                 </div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- List News / End -->
    </div>
</div>

<?php
}
et_get_mobile_footer();