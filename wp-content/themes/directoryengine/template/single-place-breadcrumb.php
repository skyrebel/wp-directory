<?php
global $post, $ae_post_factory , $current_user;
$place_obj     = $ae_post_factory->get('place');
$place         = $place_obj->current_post;
$et_claim_info = $place->et_claim_info;

$term = $place->place_category;
$categoy = new AE_Category();
$color = !empty($term[0]) ? $categoy->get_term_color($term[0],'place_category') : 1;
$disable_plan    = ae_get_option('disable_plan');
?>

<div class="section-detail-wrapper ">
    <!-- // breadcrumb -->
    <ol class="breadcrumb" style="border-left-color: <?php echo ($color == 1) ? '#F59236': $color;?>">
        <li itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb">
            <a href="<?php echo home_url(); ?>" title="<?php _e("Home", ET_DOMAIN); ?>" itemprop="url">
                <span itemprop="title"><?php _e("Home", ET_DOMAIN); ?></span>
            </a>
        </li>
        <?php
        if ( class_exists( 'WPSEO_Primary_Term' ) ) {

            $primary_term       = new WPSEO_Primary_Term( 'place_category', $place->ID );
            $primary_term_id    =  $primary_term->get_primary_term();

            if( $primary_term_id ){
                echo '<li>';
                $term = get_term( $primary_term_id );
                if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
                    echo  '<a href="'.get_term_link($term).'">'. $term->name .'</a>';
                }
                echo '</li>';
            } else if( isset($place->tax_input['place_category'][0]) ) {
                $cat_id = $place->tax_input['place_category'][0]->term_id;
                echo de_get_tax_parents($cat_id, 'place_category', true , '');

            }

        } else if(isset($place->tax_input['place_category'][0])) {
            $cat_id = $place->tax_input['place_category'][0]->term_id;
            echo de_get_tax_parents($cat_id, 'place_category', true , '');

        }

        ?>
        <li itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb">
            <a title="<?php the_title(); ?>" href="<?php the_permalink(); ?>" itemprop="url">
                <span itemprop="title"><?php the_title(); ?></span>
            </a>
        </li>
    </ol>
    <!-- // breadcrumb -->
    <?php
    $detector = AE_MobileDetect::get_instance();

    if( ($current_user->ID == $post->post_author || current_user_can( 'administrator' )) && !$detector->isTablet()  ){ ?>
    <!-- owner action, admin action -->
    <div class="dropdown">
        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
        <?php   if( $current_user->ID == $post->post_author ) {
                    _e("For owner", ET_DOMAIN);
                } else {
                    _e( 'Admin Control' , ET_DOMAIN );
                }
            ?>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right single-place-control" role="menu" aria-labelledby="dropdownMenu1">
            <li role="presentation">
                <a class="place-action edit" role="menuitem" data-action="edit"  data-target="#edit_place" >
                    <i class="fa fa-pencil"></i> <?php _e("Edit", ET_DOMAIN); ?>
                </a>
            </li>
            <li role="presentation">
                <a class="place-action create_event edit" data-toggle="modal" data-action="create_event" >
                    <i class="fa fa-calendar "></i> <?php _e("Create Event", ET_DOMAIN); ?>
                </a>
            </li>
            <?php if(current_user_can( 'administrator' )){
                // pending control
                if($place->post_status == 'pending' || $place->post_status == 'archive' || $place->post_status == 'reject') { ?>
                    <li role="presentation">
                        <a class="place-action approve" role="menuitem" data-action="approve">
                            <i class="fa fa-check"></i> <?php _e("Approve", ET_DOMAIN); ?>
                        </a>
                    </li>
                <?php }
                if($place->post_status == 'pending') { ?>
                    <li role="presentation">
                        <a class="place-action reject" role="menuitem" data-action="reject">
                            <i class="fa fa-times"></i> <?php _e("Reject", ET_DOMAIN); ?>
                        </a>
                    </li>
                <?php }
                if($place->post_status !== 'archive') {
                 ?>
                    <li role="presentation">
                        <a class="place-action archive" role="menuitem" data-action="archive">
                            <i class="fa fa-trash-o"></i> <?php _e("Archive", ET_DOMAIN); ?>
                        </a>
                    </li>
                <?php } ?>
            <?php } ?>

            <?php
                if( $place->et_claimable && !$place->et_claim_approve && !empty($et_claim_info) && current_user_can( 'manage_options' ) ){
            ?>
            <li role="presentation">
                <a data-id="<?php echo $place->ID ?>" class="<?php if($user_ID) {echo 'claim-place';} else {echo 'authenticate';} ?>">
                    <i class="fa fa-bullhorn "></i><?php _e("Approve this claim", ET_DOMAIN); ?>
                </a>
            </li>
            <?php
                }
            ?>
            <?php
            $package = $ae_post_factory->get('pack');
            $plan = $package->get($place->et_payment_package);
            $price = -1;
            if(isset($plan->et_price) && $plan->et_price != '' ){
                $price = (int)$plan->et_price;
            }
            ?>
            <?php if(!$disable_plan && (!isset($place->et_payment_package) || $place->et_payment_package == '' || $price == 0)){ ?>
             <li role="presentation">
                <a class="place-action edit" href="<?php echo et_get_page_link('post-place', array('id' => $place->ID)) ?>">
                    <i class="fa fa-level-up"></i> <?php _e("Upgrade", ET_DOMAIN); ?>
                </a>
            </li>
                <?php } ?>
            <?php
                if(has_action('btn_addtocollection') || current_user_can( 'administrator' ) || $current_user->ID == $post->post_author){
                    do_action('btn_addtocollection');
                }
            ?>
        </ul>
    </div>

    <?php
        } else {
            if( $place->et_claimable &&  empty($et_claim_info) ){
    ?>
        <a data-id="<?php echo $place->ID ?>" class="<?php if($user_ID) {echo 'claim-place';} else {echo 'no-claim authenticate';} ?>">
           <?php _e("Claim this place", ET_DOMAIN); ?>
        </a>
            <?php
            }
        }
    ?>
    <!--// owner action, admin action -->
    <div class="clearfix"></div>
</div>