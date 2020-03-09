<?php $pending_post = new WP_Query(array('post_type' => 'place', 'post_status' => 'pending', 'showposts' => -1)); ?>
<div class = "container">
    <div class="pending-place-wrapper">
         <h2> <?php _e("Pending Place", ET_DOMAIN); ?></h2>
        <div  class="pending-place-filter-wrap">
            <p class="pull-left"><span><?php echo $pending_post->found_posts ?></span><?php _e( "pending places", ET_DOMAIN) ?></p>
               <div class="pending-place-filter pull-right">            
                    <div class="place-category-filter">
                          <?php 
                            ae_tax_dropdown('place_category', array('hide_empty' => true,
                                'class' => 'chosen-single tax-item',
                                'hierarchical' => true, 
                                'show_option_all' => __("All categories", ET_DOMAIN) , 
                                'taxonomy' => 'place_category' ,
                                'value' => 'slug',
                            )); ?>
                    </div>
                     <div class="place-payment-filter">
                        <select class="chosen-single tax-item" name="payment_status">
                            <option value= 2>
                                <?php _e("All Payment Status", ET_DOMAIN); ?> 
                            </option>
                            <option value = 0>
                                <?php _e("Unpaid", ET_DOMAIN); ?> 
                            </option>     
                            <option value = 1>
                                <?php _e("Paid", ET_DOMAIN); ?> 
                            </option>                                         
                        </select>
                    </div>
                    <div class="place-newlest-filter">
                        <select class="chosen-single tax-item" name="order">  
                            <option value="DESC">
                                <?php _e("Newest", ET_DOMAIN); ?> 
                            </option>   
                            <option value="ASC">
                                <?php _e("Oldest", ET_DOMAIN); ?> 
                            </option>                                          
                        </select>
                    </div>
            </div>
        </div>
        <div  class="pending-places-wrap">
                <ul class="pending-place-list" data-load='1'>
                    <?php 
                        $pending = new WP_Query(array(
                            'post_type'         => 'place',
                            'post_status'       => array('pending'),
                            'paginate'          => 'page',
                            'showposts'         => get_option('posts_per_page',10),
                            'orderby'           => 'date',
                            'order'             => 'DESC',
                            'place_category'    => '',
                            'paged' => get_query_var( 'paged' )
                        ));
                        if($pending->have_posts()){
                            global $post, $ae_post_factory;
                            while ($pending->have_posts()) {
                                $pending->the_post();
                                /**
                                 * convert
                                 */
                                $ae_post    =   $ae_post_factory->get('place');
                                $convert    =   $ae_post->convert($post, 'big_post_thumbnail');
                                $post_arr[] =   $convert;
                                // get template template/author-loop-place.php
                                get_template_part( 'template/loop' , 'place-pending' );
                            }
                            echo '<script type="json/data" class="ae_query"  id="ae-pending-notification"> ' . json_encode($pending->query) . '</script>';
                            echo '<script type="json/data" class="postdata"  id="ae-pending-notification"> ' . json_encode($post_arr) . '</script>';
                        }
                        else {
                                _e("No place found", ET_DOMAIN);
                            }
                    ?>
                </ul>
                <div class="paginations-wrapper">
                <?php
                    ae_pagination_pending($pending, 1, 'page'); 
                ?>
                </div>
                <div
        </div>
    </div>
</div>