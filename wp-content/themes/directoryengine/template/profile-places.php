<?php 
/**
 * Author Places 
 * @author ThanhTu
 */
global $user;
$count_post = de_count_post_by_user_id('place', $user->ID);
$post_total_publish = $count_post->publish;
$post_total_pending = $count_post->pending;
$post_total_reject = $count_post->reject;
$post_total_archive = $count_post->archive;
$post_total_draft = $count_post->draft;
?>
<div class="content-place tab-pane fade in active" id="tab-place">
    <!--content place-->
    <?php
        if($post_total_publish || $post_total_pending || $post_total_reject || $post_total_archive || $post_total_draft){
            ?>
            <ul class="nav nav-tabs list-place-tabs">
                <li class="select-place">
                    <select class="chosen-single" name="post_status" id="post_status">
                        <option value="publish" data-type="<?php _e('Publishing', ET_DOMAIN);?>"><?php printf(__('Publishing (%s)',ET_DOMAIN),$post_total_publish);?></option>
                        <option value="pending" data-type="<?php _e('Pending', ET_DOMAIN);?>"><?php printf(__('Pending (%s)',ET_DOMAIN),$post_total_pending);?></option>
                        <option value="archive" data-type="<?php _e('Overdue', ET_DOMAIN);?>"><?php printf(__('Overdue (%s)',ET_DOMAIN),$post_total_archive);?></option>
                        <option value="reject" data-type="<?php _e('Rejected', ET_DOMAIN);?>"><?php printf(__('Rejected (%s)',ET_DOMAIN),$post_total_reject);?></option>
                        <option value="draft" data-type="<?php _e('Draft', ET_DOMAIN);?>"><?php printf(__('Draft (%s)',ET_DOMAIN),$post_total_draft);?></option>
                    </select>
                </li>
                <li class="place-search">
                    <div class="box-search">
                        <input type="text" class="search" value="" id="place_search" name="place_search" placeholder="<?php _e('Enter keywords...',ET_DOMAIN);?>">
                        <span class="btn-search-place"><i class="fa fa-search"></i></span>
                    </div>
                </li>
            </ul>
            <?php
        }
    ?>

    <div class="content-place-tabs tab-content">
        <div id="place-publishing" class="author-place-block tab-pane fade in active">
            <ul class="list-place-publishing" data-id="publishing" id="tab-place-publishing">
            	<?php 
            		/**
                	 * Loop Status Publish
                	 */
            		global $post, $ae_post_factory;
					$place_obj = $ae_post_factory->get('place');
					$paged = ( get_query_var('page') ) ? get_query_var('page') : 1;

					$query_args = array(
						'orderby' 		=> 'meta_value',
						'order'			=> 'DESC',
						'post_status' 	=> 'publish',
						'posts_per_page' => $paged,
						'showposts'		=> get_option('posts_per_page'),
						'author'        => $user->ID,
                        'meta_key'      => 'et_featured'
					);

                    $array_total_place = array('author'=>$user->ID, 'post_status'=>array('publish','pending','reject','archive','draft'), 'post_type' => 'place','posts_per_page' => -1);
                    $query_total_place= new WP_Query( $array_total_place );
                    $post_total = $query_total_place->found_posts;;

					$query = $place_obj->query($query_args);
					if($query->have_posts()){
	            		global $post, $ae_post_factory;
	                    $post_arr   =   array();
	                    while($query->have_posts()) { $query->the_post();
	                        $ae_post    =   $ae_post_factory->get('place');
	                        $convert    =   $ae_post->convert($post);
	                        $post_arr[] =   $convert;
	                        get_template_part( 'template/profile', 'loop-place' );
	                    }
	                    echo '<script type="json/data" class="postdata" > ' . json_encode($post_arr) . '</script>';   
                    }
                    elseif($post_total < 1) { ?>
                        <li class="col-md-12">
                            <div class="event-active-wrapper">
                                <div class="col-md-12">
                                    <div class="event-wrapper tab-style-event">
                                        <h2 class="title-envent no-title-envent "><?php _e( "Currently, there are not place yet.", ET_DOMAIN ); ?></h2>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php }
            	?>
            </ul>
            <?php
				echo "<div class='paginations-wrapper'>";
				ae_pagination($query, 1);
				echo "</div>";
	            wp_reset_query();
            ?>
        </div>        
    </div>
    <!--/content place-->
</div>