<?php
function block_2()
{
    $title = get_theme_mod('textbox_setting', 'Place Collection');
    $count = get_theme_mod('checkbox_setting');
    $select = get_theme_mod('select_setting');
    $orderby = array("name", "slug", "count");
    $id_loc = get_theme_mod('get_dropdown_multipicker');
    $include = $id_loc ? implode(',', $id_loc) : '';
    $atts = array(
        'el_class' => '',
        'title' => $title,
        'style' => 'horizontal',
        'count' => $count,
        'hide_empty' => false,
        'orderby' => $orderby[$select],
        'custom_css' => '',
        'number' => '5',
        'include' => $include,
    );
    $args = wp_parse_args($atts, array(
            'count' => true,
            'hide_empty' => false,
            'title' => __('Default Title', ET_DOMAIN),
        )
    );
    if (!isset($args['orderby'])) {
        $args['order'] = 'ASC';
        $args['orderby'] = 'name';
    }
    if ($orderby[$select] == 'count')
        $args['order'] = 'DESC';
    else
        $args['order'] = 'ASC';
    $loca = new AE_Category(array('taxonomy' => 'location'));

    $get_all_args = $args;
    $get_all_args['number'] = 0;
    $all_areas = $loca->getAll($get_all_args);

    $areas = $loca->getAll($args);
    if ($areas) {
        $sort = array();
        if ($args['orderby'] == 'count')
            $sort_by = SORT_DESC;
        else
            $sort_by = SORT_ASC;
            $orderby = $args['orderby'];
        foreach ($areas as $key => $value) {
            if ($orderby == 'name')
                $sort[] = $value->name;
            else if ($orderby == 'slug')
                $sort[] = $value->slug;
            else if ($orderby == 'count')
                $sort[] = $value->count;
            else if ($orderby == 'id')
                $sort[] = $value->term_id;
        }
        array_multisort($sort, $sort_by, $areas);
    }

    ?>

        <div class="de-collection-wrapper">
            <div class="container">
                <h2 class="de-section-title"><?php echo $args['title']; ?></h2>
                <div class="de-collection-wrap">
                    <div class="row">
                        <?php foreach ($areas as $key => $loca) {  ?>
                            <div class="col-md-6">
                                <div class="de-collection-large">
                                    <a href="<?php echo get_term_link($loca, 'place_category') ?>">
                                        <?php
										$tax_img = et_taxonomy_image_url($loca->term_id, 'large', TRUE);
										if(empty($tax_img)) { ?>
                                            <span></span>
                                        <?php } else { ?>
                                            <img src="<?php echo et_taxonomy_image_url($loca->term_id, 'large', TRUE); ?>"/>
                                        <?php } ?>
                                        <div class="collection-info">
                                            <h2><?php echo $loca->name; ?></h2>
                                            <?php
                                                if ($args['count'])
                                                    echo '<span>' . $loca->count . ' ' . __("Places", ET_DOMAIN) . '</span>';
                                                ?>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php
                            break;
                            }
                         ?>

                        <div class="col-md-6">
                            <div class="de-collection-small-wrap">
                                <div class="row">
                                    <?php foreach ($areas as $key => $loca) {  ?>
                                        <?php
                                            if($key == 0) continue;
                                            if($key > 4) break;
                                        ?>
                                        <div class="col-sm-6">
                                            <div class="de-collection-small">
                                                <a href="<?php echo get_term_link($loca, 'place_category') ?>">
                                                    <?php
													$tax_img1 = et_taxonomy_image_url($loca->term_id, 'large', TRUE);
													if(empty($tax_img1)) { ?>
                                                        <span></span>
                                                    <?php } else { ?>
                                                        <img src="<?php echo et_taxonomy_image_url($loca->term_id, 'large', TRUE); ?>"/>
                                                    <?php } ?>
                                                    <div class="collection-info">
                                                        <h2><?php echo $loca->name; ?></h2>
                                                        <?php
                                                            if ($args['count'])
                                                                echo '<span>' . $loca->count . ' ' . __("Places", ET_DOMAIN) . '</span>';
                                                            ?>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?php
}

function block_3()
{
    $title = get_theme_mod('title_block_2', 'Popular Places');
    if (is_plugin_active('de_multirating/de_multirating.php')) {
        $array_meta = array(
            'relation' => 'OR',
            array(
                'key' => 'multi_overview_score',
                'value' => '0',
                'compare' => '>'
            ),
            array(
                'key' => 'rating_score_comment',
                'value' => '0',
                'compare' => '>'
            ),
        );
    } else {
        $array_meta = array(
            'relation' => 'OR',
            array(
                'key' => 'rating_score_comment',
                'value' => '0',
                'compare' => '>',
            ),
            array(
                'key' => 'reviews_count_comment',
                'value' => '0',
                'compare' => '>'
            )
        );
    }
    global $ae_post_factory;
    $count_query2 = 0;
    $place_obj = $ae_post_factory->get('place');

    $query_args = array(
        'post_type' => 'place',
        'showposts' => 4,
        'post_status' => 'publish',
        'meta_query' => $array_meta,
        'orderby' => 'meta_value',
    );
    $query_args_no_meta = array(
        'post_type' => 'place',
        'post_status' => 'publish',
    );
    $query = $place_obj->query($query_args);
    if (is_plugin_active('de_multirating/de_multirating.php')) {
        $query->posts = sort_place($query->posts, 'rating', 'rating_score_comment', 'multi_overview_score');
    } else {
        $query->posts = sort_place($query->posts, 'comment', 'reviews_count_comment', 'rating_score_comment');
    }
    if ($query->have_posts()) {
        ///////
        $ret = array();
        foreach ($query->posts as $post_ids) {
            $ret[] = $post_ids->ID;
        }
        $count_query1 = count($query->posts);
        $count_query2 = 4 - $count_query1;
        ///////
        $query_args_no_meta['post__not_in'] = $ret;
        $query_args_no_meta['showposts'] = $count_query2;
        $query2 = $place_obj->query($query_args_no_meta);
    } else {
        $query = $place_obj->query(array(
            'post_type' => 'place',
            'showposts' => 4,
            'post_status' => 'publish',
        ));
    }
    ?>

    <div class="de-popular-wrapper">
        <div class="container">
            <h2 class="de-section-title"><?php echo $title ?></h2>
            <div class="de-popular-wrap">
                <div class="row">

                    <?php
                        if ($query->have_posts()) {
                            global $post, $ae_post_factory;
                            while ($query->have_posts()) {
                                $query->the_post();
                                $ae_post = $ae_post_factory->get('place');
                                $convert = $ae_post->convert($post, 'big_post_thumbnail');
                                $post_arr[] = $convert;
                                get_template_part('template/loop', 'place-popular');
                            }
                            wp_reset_postdata();
                        }
                        if ($count_query2) {
                            if ($query2->have_posts()) {
                                global $post, $ae_post_factory;
                                while ($query2->have_posts()) {
                                    $query2->the_post();
                                    $ae_post = $ae_post_factory->get('place');
                                    $convert = $ae_post->convert($post,'big_post_thumbnail');
                                    get_template_part('template/loop', 'place-popular');
                                }
                                wp_reset_postdata();
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php
}

function block_4()
{
    $title = get_theme_mod('title_block_3', 'Why works with <span>DirectoryEngine</span>');
    $style = '';
    $customize_why_background = get_theme_mod('block_why_background');
    $attach_data = et_get_attachment_data($customize_why_background);
    if (!empty($attach_data))
        $style = 'style = "background-image:url(' . $attach_data['full']['0'] . ')"'; ?>

    <div class="de-why-work-wrapper" <?php echo $style ?> >
        <div class="container">
            <h2><?php echo $title ?></h2>
            <div class="de-why-work-wrap">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="de-why-work">
                            <div class="why-work-icon">
                                <img class="icon-top-safe"
                                 src="<?php echo load_icon_image('why_block_image_item_1', 'img/top_safe.png') ?>"/>
                            </div>
                            <h2><?php echo get_theme_mod('why_block_tile_item_1', 'ADVANCED SEARCH') ?></h2>
                            <p><?php  echo get_theme_mod('why_block_des_item_1','Easy searching & filtering based on custom criteria.') ?></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="de-why-work">
                            <div class="why-work-icon">
                                <img class="icon-secured-transaction" src="<?php echo load_icon_image('why_block_image_item_2', 'img/secured_transaction.png') ?>" />
                            </div>
                            <h2><?php echo get_theme_mod('why_block_tile_item_2', 'CLAIM A LISTING') ?></h2>
                            <p><?php echo get_theme_mod('why_block_des_item_2','Import different listings, mark as “Claimable”, & let owners claim them.') ?></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="de-why-work">
                            <div class="why-work-icon">
                                <img class="icon-top-sellers" src="<?php echo load_icon_image('why_block_image_item_3', 'img/top_sellers.png') ?>"/>
                            </div>
                            <h2><?php echo get_theme_mod('why_block_tile_item_3', 'MULTI PAYMENT GATEWAYS') ?></h2>
                            <p><?php  echo get_theme_mod('why_block_des_item_3','Support both global and local payment gateways.') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function block_5()
{
    global $ae_post_factory;
    $review_object = $ae_post_factory->get('de_review'); // get review object
    $number = get_theme_mod('number_block_4_review', 4);
    $query_args = array(
        'number' => $number,
        'status' => 'approve',
        'meta_key' => 'et_rate_comment',
        'type' => 'review',
        'post_status' => 'publish',
        'post_type' => 'place',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'et_rate_comment',
                'value' => '0',
                'compare' => '>'
            )
        )
    ); ?>

    <div class="de-review-category-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="de-review-wrapper">
                        <h2 class="de-section-title"><?php echo get_theme_mod('title_block_4_review', 'Reviews') ?></h2>
                        <div class="de-review-wrap">
                            <div class="carousel slide" id="myCarousel" data-ride="carousel" data-interval="3000">
                                <div class="carousel-inner">
                                    <?php
                                    $reviews = get_comments($query_args);
                                    $class_active = '';
                                    if (!empty($reviews)) {
                                        foreach ($reviews as $key => $comment) {
                                            if ($key === 0) $class_active = 'active';
                                            else  $class_active = '';
                                            // convert review object
                                            if ($key % 2 === 0) echo '<div class="item ' . $class_active . '">';
                                            $convert = $review_object->convert($comment, 'review_post_thumbnail');
                                            get_template_part('template/loop', 'review-home');
                                            if ($key == count($reviews) - 1 || $key % 2 != 0) echo '</div>';
                                        } ?>
                                        <ol class="carousel-indicators">
                                        <?php $ol = round(count($reviews) / 2);
                                        for ($i = 0; $i < $ol; $i++) { ?>
                                            <li data-target="#myCarousel"
                                                data-slide-to="<?php echo $i ?>" <?php echo $i === 0 ? 'class="active"' : '' ?>></li>
                                        <?php } ?>
                                        </ol><?php
                                        $review_object->reset();
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="de-category-wrapper">
                        <h2 class="de-section-title"><?php echo get_theme_mod('title_block_4_cat', 'Categories') ?></h2>

                        <div class="de-category-wrap">
                            <div class="row">
                                <?php category_list_home(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function block_6()
{
    ?>

    <div class="de-how-work-wrapper">
        <div class="container">
            <h2><?php echo get_theme_mod('title_block_5', 'How <span>DirectoryEngine</span> works?'); ?></h2>
            <div class="de-how-work-wrap">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="de-how-work">
                            <div class="how-work-icon">
                                <img class="icon-action-work-1" src="<?php echo load_icon_image('how_block_image_item_1', 'img/check.png') ?>">
                            </div>
                            <h2><?php echo get_theme_mod('how_block_tile_item_1', 'Search a Place') ?></h2>
                            <p><?php echo get_theme_mod('how_block_des_item_1','At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum.') ?></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="de-how-work">
                            <div class="how-work-icon">
                                <img class="icon-action-work-2" src="<?php echo load_icon_image('how_block_image_item_2', 'img/group.png') ?>">
                            </div>
                            <h2><?php echo get_theme_mod('how_block_tile_item_2', 'Contact Owner') ?></h2>
                            <p><?php echo get_theme_mod('how_block_des_item_2','At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum.') ?></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="de-how-work">
                            <div class="how-work-icon">
                                <img class="icon-action-work-3" src="<?php echo load_icon_image('how_block_image_item_3', 'img/form.png') ?>">
                            </div>
                            <h2><?php echo get_theme_mod('how_block_tile_item_3', 'Reviews & Comments') ?></h2>
                            <p><?php echo get_theme_mod('how_block_des_item_3','At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum.') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}
function block_footer()
{
    $style = '';
    $customize_footer_background = get_theme_mod('block_footer_background');
    $attach_data = et_get_attachment_data($customize_footer_background);
    if (!empty($attach_data))
        $style = 'style = "background-image:url(' . $attach_data['full']['0'] . ')"';
    ?>

    <div class="de-get-started-wrapper" <?php echo $style ?>>
        <div class="container">
            <div class="de-get-started-wrap">
                <h2><?php echo get_theme_mod('title_block_footer', 'You have several locations and save your time') ?></h2>
                <p><?php echo get_theme_mod('des_block_footer', 'Thank you for showing an interest in DirectoryEngine!') ?></p>
                <a class="de-started-btn" href="<?php echo et_get_page_link('post-place'); ?>"><?php echo get_theme_mod('title_button_block_footer', 'Get Started') ?></a>
            </div>
        </div>
    </div>
    <?php
}

function category_list_home()
{
    $select = get_theme_mod('order_block_4_cat', '0');
    $orderby = array("name", "slug", "count");
    $count = get_theme_mod('checkbox_count_cat', 1);

    $args = array(
            'number' => 8,
            'style' => 'vertical',
            'count' => false,
            'hide_empty' => false,
            'orderby' => $orderby[$select],
        );

    if (!isset($args['orderby'])) {
        $args['order'] = 'ASC';
        $args['orderby'] = 'name';
    }
    if ($orderby[$select] == 'count')
        $args['order'] = 'DESC';
    else
        $args['order'] = 'ASC';
    $cat = new AE_Category(array('taxonomy' => 'place_category'));
    $category = $cat->getAll($args);

    //shuffle( $categories );
    foreach ($category as $key => $cat) {
        ?>

        <div class="col-sm-6 col-xs-12">
            <a class="de-category-item list-cat-home color_category_<?php echo $cat->term_id; ?>" style="color: <?php echo $cat->color;?>;" data-color="<?php echo $cat->color;?>" data-id="<?php echo $cat->term_id;?>" href="<?php echo get_term_link($cat, 'place_category') ?>">
                <span><i class="fa <?php echo $cat->icon; ?>" aria-hidden="true"></i><?php echo $cat->name; ?></span>
                <?php if ($count) { ?>
                    <span class="de-quantity-places">(<?php echo $cat->count; ?>)</span>
                <?php } ?>
            </a>
        </div>


        <?php
    }
}

function load_icon_image($name, $url_image)
{
    $customize_why_block_background = get_theme_mod($name);
    $attach_data = et_get_attachment_data($customize_why_block_background);
    $img_url = ($attach_data) ? $attach_data['full']['0'] : get_template_directory_uri() . '/' . $url_image;
    return $img_url;
}

function sort_place($array_post, $name, $cri1, $cri2)
{
    $sort = array();
    $sort = $array_post;
    for ($startIndex = 0; $startIndex < count($sort); ++$startIndex) {
        $smallestIndex = $startIndex;
        for ($currentIndex = $startIndex + 1; $currentIndex < count($sort); ++$currentIndex) {
            if ($name === 'rating') {
                $rating1 = get_post_meta($sort[$currentIndex]->ID, $cri1, true);
                $rating2 = get_post_meta($sort[$smallestIndex]->ID, $cri1, true);
                $comment_review1 = $sort[$currentIndex]->comment_count;
                $comment_review2 = $sort[$smallestIndex]->comment_count;
            } else {
                $rating1 = $sort[$currentIndex]->comment_count;
                $rating2 = $sort[$smallestIndex]->comment_count;
            }
            if (get_post_meta($sort[$currentIndex]->ID, $cri2, true) === get_post_meta($sort[$smallestIndex]->ID, $cri2, true)) {
                if ($rating1 === $rating2) {
                    if ($name === 'rating') {
                        if ($comment_review1 === $comment_review2) {
                            if ($sort[$currentIndex]->ID > $sort[$smallestIndex]->ID) {
                                $smallestIndex = $currentIndex;
                            }
                        }
                        if ($comment_review1 > $comment_review2) {
                            $smallestIndex = $currentIndex;
                        }
                    } else {
                        if ($sort[$currentIndex]->ID > $sort[$smallestIndex]->ID) {
                            $smallestIndex = $currentIndex;
                        }
                    }
                }
                if ($rating1 > $rating2) {
                    $smallestIndex = $currentIndex;
                }
            } elseif (get_post_meta($sort[$currentIndex]->ID, $cri2, true) > get_post_meta($sort[$smallestIndex]->ID, $cri2, true)) {
                $smallestIndex = $currentIndex;
            }
        }

        // Swap our start element with our smallest element
        $temp = $sort[$startIndex];
        $sort[$startIndex] = $sort[$smallestIndex];
        $sort[$smallestIndex] = $temp;
    }
    return $sort;
}

function load_new_homepages()
{
    global $de_place_query, $post;
    $args = array(
        'post_type' => 'place',
        'paged' => get_query_var('paged'),
        //'meta_key' => 'rating_score',
        'post_status' => array('publish')
    );

    $de_place_query = new WP_Query($args);
//get_template_part('template/section' , 'map');
    echo '<script type="data/json"  id="total_place">' . json_encode(array('number' => $de_place_query->found_posts)) . '</script>';
//$title_1 = get_theme_mod( 'title_block_1','Discover <span>your city</span> now' );
    $style = '';
    $customize_search_background = get_theme_mod('block_search_background');
    $img_url = et_get_attachment_data($customize_search_background);
    if (!empty($img_url))
        $style = 'style = "background:url(' . $img_url['full']['0'] . ') no-repeat"';
    ?>

    <div class="de-search-wrapper" <?php echo $style ?> >
        <div class="de-search-wrap">
            <div class="de-search-desc">
                <h1><?php echo get_theme_mod('title_block_1', 'Discover <span>your city</span> now'); ?></h1>
                <h4><?php echo get_theme_mod('des_block_1', 'Use advanced filters for search in categories, locations <br>
                    and radius of your position.'); ?></h4>
                <i class="fa fa-chevron-down" aria-hidden="true"></i>
            </div>
             <div class="de-search-form" id="search-places">
                <form role="search" class="place_search_form" action="<?php echo et_get_page_link('search-location'); ?>" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="de-scontrol">
                                <input type="text" name="search_keywords" class="keyword-search" placeholder="<?php _e("Enter keyword ...", ET_DOMAIN); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="de-scontrol">
                                <input type="text" name="search_location" id="search_address_search" class="address-search" placeholder="<?php _e("Address ...", ET_DOMAIN); ?>">
                                <i class="fa fa-map-marker address-search-icon search-location-marker" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="de-scontrol">
                                <?php
                                    ae_tax_dropdown('place_category', array('hide_empty' => true,
                                        'class' => 'chosen-single tax-item de-chosen-single',
                                        'hierarchical' => true,
                                        'show_option_all' => __("All categories", ET_DOMAIN),
                                        'taxonomy' => 'place_category',
                                        'value' => 'slug'
                                    )); ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                             <div class="de-scontrol">
                                 <?php
                                    ae_tax_dropdown('place_location', array('hide_empty' => true,
                                        'class' => 'chosen-single tax-item de-chosen-single',
                                        'hierarchical' => true,
                                        'show_option_all' => __("All Location", ET_DOMAIN),
                                        'taxonomy' => 'location',
                                        'value' => 'slug'
                                    )); ?>
                             </div>
                        </div>
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                    </div>
                    <button class="btn btn-primary submit-search de-search-btn" type="submit"><?php _e('Search', ET_DOMAIN); ?></button>
                </form>
            </div>
        </div>


    </div>


    <?php
        block_2();
        block_3();
        block_4();
        block_5();
        block_6();
        block_footer();
}

?>