<!DOCTYPE html>
<!--[if IE 7]>
	<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
	<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link href='//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,300,400,600,700' rel='stylesheet' type='text/css'>
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <!--[if lt IE 9]>
    <script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
    <![endif]-->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php 
        global $user_ID, $wp_query;
        ae_favicon();
        wp_head(); 

        wp_enqueue_script('jquery');
        wp_enqueue_script('modernizr', get_template_directory_uri() . '/js/modernizr.min.js', array() , true, false);
        wp_enqueue_script('dl-menu', get_template_directory_uri() . '/mobile/js/dl-menu.js', array('jquery') , true);
        if(is_page_template('page-profile.php')){
            wp_enqueue_script('profile', get_template_directory_uri() . '/mobile/js/profile.js', array('appengine' , 'dl-menu', 'chosen') ,ET_VERSION,  true); 
        }else{
            wp_enqueue_script('main', get_template_directory_uri() . '/mobile/js/main.js', array('appengine' , 'dl-menu', 'chosen') ,ET_VERSION,  true); 
        }
        wp_enqueue_script('front', get_template_directory_uri() . '/mobile/js/front.js', array('jquery','backbone', 'marker','appengine') , true, false);
        
        $place_active = '';
        $review_active = '';
        $nearby_active = '';
        $blog_active = '';

        if(is_post_type_archive( 'place' ) || is_page_template( 'page-front.php' )) $place_active = 'active';
        if(is_page( 'blog' )) $blog_active = 'active';
        if(is_page_template('page-list-reviews.php' )) $review_active = 'active';
        if(isset($_REQUEST['center'])) {
            $nearby_active = 'active';
            $place_active = '';
        }
        $post_place_active = '';
        if(is_page('post-place')) $post_place_active = 'active';  
        echo '<style>#menu-footer ul li { width : 25% !important; }</style>';


    // render cat color css
    $cat = new AE_Category(array(
        'taxonomy' => 'place_category'
    ));
    $category = $cat->getAll();
    ?>
    <style type="text/css">
        <?php foreach ($category as $key => $value) { ?>
        .place-wrapper .img-place .cat-<?php echo $value->term_id;  ?>  .ribbon {
            background: <?php echo $value->color; ?>;
        }
        .place-wrapper .img-place .cat-<?php echo $value->term_id;  ?>  .ribbon:after {
                content: "";
                position: absolute;
                display: block;
                border: 9px solid <?php echo $value->color; ?>;
                z-index: -1;
                bottom: 0;
            }
           .place-wrapper .img-place  .cat-<?php echo $value->term_id;  ?>  .ribbon:after {
                right: -15px;
                border-left-width: 1.5em;
                border-right-color: transparent;
            }
            .sl-ribbon-event.cat-<?php echo $value->term_id;  ?>:before { 
                content: "";
                border : 14px solid <?php echo $value->color; ?>;
                z-index: -1;
                top: 0;
                left: -15px;
                position: absolute;
                border-left-width: 0.5em;
                border-left-color: transparent;
            }
        <?php } ?>
        <?php if(!has_nav_menu('et_mobile_header')) { ?>
            #menu-footer ul li{width: 25%;}
        <?php } ?>
        .carousel-list .moxie-shim.moxie-shim-html5{
            z-index: 1000;
            width: 70px !important;
            height: 70px !important;
        }
    </style>
</head>
<body <?php body_class(); ?>  >
	<div class="marsk-black"></div>
    <!-- Menu Bottom -->
	<section id="menu-footer" data-size="big">
    	<ul>
        	<li>
                <a class="<?php echo $place_active; ?>" href="<?php echo get_post_type_archive_link( 'place' ); ?>"><i class="fa fa-map-marker"></i><?php _e("Places", ET_DOMAIN); ?></a>
            </li>
            <li>
                <a class="<?php echo $nearby_active; ?>" href="#" id="search-nearby"><i class="fa fa-compass"></i><?php _e("Nearby", ET_DOMAIN); ?></a>
                <form id="nearby" action="<?php echo get_post_type_archive_link('place')  ?>" method="get" >
                    <input type="hidden" name="center" id="center_nearby" />
                </form>
            </li>
            <li>
                <a class="<?php echo $post_place_active; ?>" href="<?php echo et_get_page_link('post-place') ?>"><i class="fa fa-plus"></i><?php _e('Submit', ET_DOMAIN)?></a>
            </li>
            <li>
                <a class="<?php echo $review_active; ?>" href="<?php echo et_get_page_link('list-reviews') ?>">
                    <i class="fa fa-comment"></i><?php _e("Reviews", ET_DOMAIN); ?>
                </a>
            </li>
        </ul>
    </section>
    <!-- Menu Bottom / End -->
	
    <!-- Topbar -->
    <?php if(!AE_Users::is_activate($user_ID)) { ?> 
    <div class="top-bar-wrapper">
    	<span class="icon-top-bar"><i class="fa fa-bullhorn"></i></span>
    	<p class="content-top-bar"><?php _e("Please confirm your email address to complete your registration process.", ET_DOMAIN); ?></p>
    	<div class="clearfix"></div>
    </div>
    <?php } ?>
    <!-- Topbar / End -->
    
	<!-- Header -->
    
	<header >
    	<div class="container">
        	<div class="row">
            	<div class="col-xs-3">
                	<?php if(has_nav_menu('et_mobile_header')) { ?>
                        <li><a href="#" class="dl-trigger"><i class="fa fa-bars" aria-hidden="true"></i></a></li>
                    <?php } ?>
                </div>
                <div class="col-xs-6">
                	<a href="<?php echo home_url(); ?>" class="logo"><?php ae_mobile_logo() ?></a>
                </div>
                
                <div class="col-xs-3">
                    <?php if($user_ID) { ?>
                	   <a href="<?php echo et_get_page_link('profile'); ?>" class="avatar-author-header"><?php echo get_avatar( $user_ID, 30 ); ?></a>
                    <?php } else { ?>
                        <a title="<?php _e("login", ET_DOMAIN); ?>" href="<?php echo et_get_page_link('login'); ?>" class="avatar-author-header"><i class="fa fa-user"></i></a>
                    <?php } ?>
                </div>
               
            </div>
        </div>
    </header>
    <!-- Header / End -->
		<?php 
            if(has_nav_menu('et_mobile_header')) {
                wp_nav_menu( array( 'theme_location' => 'et_mobile_header' , 
                                    'menu_class' => 'dl-menu',
                                    'container' => 'div',
                                    'container_class' => 'dl-menuwrapper',
                                    'container_id'    => 'dl-menu',
                                    'walker' => new DE_Menu_Walker()
                                ) 
                            );
            }
        ?>	
    <!-- /dl-menuwrapper -->
    <?php 
    if( is_active_sidebar( 'de_mobile_top' ) ) {
        dynamic_sidebar( 'de_mobile_top' );
    }
