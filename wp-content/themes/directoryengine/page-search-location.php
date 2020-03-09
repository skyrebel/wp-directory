<?php
/**
 * Template Name: Search-location
*/
get_header();
?>
<style>
#google_canvas {
    position: fixed !important;
    margin-top: -50px;
   /* height:860px; */
}
</style>
<!-- Breadcrumb List users -->
<?php
		$keywords = ( !empty( $_GET['search_keywords'] ) )?sanitize_text_field( $_GET['search_keywords'] ):'';
        $location = ( !empty( $_GET['search_location'] ) )?sanitize_text_field( $_GET['search_location'] ):'';
        $latitude = ( !empty( $_GET['latitude'] ) )?sanitize_text_field( $_GET['latitude'] ):'';
        $longitude = ( !empty( $_GET['longitude'] ) )?sanitize_text_field( $_GET['longitude'] ):'';
 ?>
 <div class="search-location-wrap job_listings <?php if(!et_load_mobile()) echo 'search-location-no-mobile'; ?>">
    <div class="row">
        <div class="col-lg-6 col-md-12">
            <form id="search-location-form" class = "job_filters" action="">
                <div class="row">
                    <div class="col-md-6">
                        <input class="text-field" type="text" name="search_keywords" id="search_keywords" placeholder="<?php _e("Enter keyword ...", ET_DOMAIN); ?>" value="<?php echo esc_attr( $keywords ); ?>" >
                    </div>
                    <div class="col-md-6">
                        <div class="sl-address">
                            <input class="text-field" type="text" id="address" placeholder="<?php _e("Address ...", ET_DOMAIN); ?>" value="<?php echo esc_attr( $location ); ?>" >
                            <span class= "locate-me"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="search_categories">
                              <?php 
                                ae_tax_dropdown('place_category', array('hide_empty' => true,
                                    'class' => 'chosen-single tax-item de-chosen-single',
                                    'hierarchical' => true, 
                                    'show_option_all' => __("All categories", ET_DOMAIN) , 
                                    'taxonomy' => 'place_category' ,
                                    'value' => 'slug',
                                    'selected' => (isset($_GET['place_category']) && $_REQUEST['place_category']) ? $_REQUEST['place_category'] : ''
                                )); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="search_location">
                              <?php 
                                ae_tax_dropdown('place_location', array('hide_empty' => true,
                                    'class' => 'chosen-single tax-item de-chosen-single',
                                    'hierarchical' => true, 
                                    'show_option_all' => __("All Location", ET_DOMAIN) , 
                                    'taxonomy' => 'location' ,
                                    'value' => 'slug',
                                    'selected' => (isset($_GET['place_location']) && $_REQUEST['place_location']) ? $_REQUEST['place_location'] : ''
                                )); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                         <p class="sl-slider-range">With in: <span id= "numRadius">&lt; <?php echo ae_get_option('radius_search',50) ?> <?php echo de_unit_text()?></span></p>
                        <input type="text" class="slider-value-default slider-ranger-radius"
                                                data-slider-min="0" data-slider-max="<?php echo ae_get_option('radius_search',50) ?>" 
                                                data-slider-step="1" data-slider-value="0" 
                                                data-slider-orientation="horizontal" data-slider-selection="before" 
                                                data-slider-tooltip="show"
                                            />
                        <input type="hidden" name="latitude" id="latitude" value="<?php echo esc_attr( $latitude ); ?>">
                        <input type="hidden" name="longitude" id="longitude" value="<?php echo esc_attr( $longitude ); ?>">
                        <input type="hidden" name="place_tax" id="place_tax">
                    </div>
                    <div class="col-md-12" style="text-align: center;">
                        <button type="button" class="search-btn" id="update_results"><?php _e('Search', ET_DOMAIN); ?></button>
                    </div>
                </div>
            </form>
            <div class="search-location-line"></div>
            <div class="result-search-location">
                <div class="filter-reset">
                    <div class="result-pagination">
                        <div class="nrp"><span class ="total_place_search"></span><?php _e('RESULTS', ET_DOMAIN); ?></div> 
                         <!-- <div class='prp' style = "display:none"><span class='number_place_page'></span><span>of </span><span class ="total_location"></span></div>  -->
                    </div>
                    <div class="reset-pagination"><span id="reset_search"><i class="fa fa-repeat" aria-hidden="true"></i><?php _e('Reset', ET_DOMAIN); ?></span></div>
                </div>
                <ul class="search-location-list-place row">
                </ul>
                <div class="search-location-pagination">
                    <!-- <div class="paginations-wrapper main-pagination ">
                    </div> -->
                </div>
            </div>
        </div>
         <div class="col-lg-6 col-md-12 visible-lg" style="padding-right: 0;">
            <div id="google_canvas_wrap">
                <div id="google_canvas"></div>
            </div>
            
        </div>
    </div>
</div>     
<?php
 get_footer(); 
?>
<script>
(function ($ , Views, Models, Collections,AE) {
   $(document).ready(function(){
        var heightw = $(window).height();
        var heighth = $('#header-wrapper').height();
        var f_offset = c_offset = 0;
        var heightadmin_bar = 0;
        if($('body').hasClass('admin-bar')) {
            heightadmin_bar = 32;
        }
        $('#google_canvas').height(heightw - heighth - heightadmin_bar);
        $('#google_canvas').width($('#google_canvas_wrap').width());

        
        $(window).scroll(function(event) {
            if($('footer').offset() != undefined) {
                f_offset = $('footer').offset().top;
            } else {
                c_offset = $('.copyright-wrapper').offset().top;
            }
            if($(this).scrollTop() >= heighth) {
                $('#google_canvas').css({
                    'position' : 'fixed',
                    'top' : 50 + heightadmin_bar,
                    'right' : 0,
                    'z-index' : 99
                });

                if((($(this).scrollTop() + (heightw - heighth) + heightadmin_bar + 50) >= f_offset) && (f_offset > 0)) {
                    $('#google_canvas').css({
                        'position' : 'fixed',
                        'top' : 50 + heightadmin_bar - (($(this).scrollTop() + (heightw - heighth)) - f_offset),
                        'right' : 0,
                        'z-index' : 99
                    });
                }

                if((($(this).scrollTop() + (heightw - heighth) + heightadmin_bar + 50) >= c_offset) && (c_offset > 0)) {
                    $('#google_canvas').css({
                        'position' : 'fixed',
                        'top' : 50 + heightadmin_bar - (($(this).scrollTop() + (heightw - heighth)) - c_offset),
                        'right' : 0,
                        'z-index' : 99
                    });
                }

            } else {
                $('#google_canvas').css({
                    'position' : 'fixed',
                    'top' : heighth - $(this).scrollTop() + 50 + heightadmin_bar,
                    'right' : 0,
                    'z-index' : 99
                });
            }
        });
	    var peopleCollection;
	    //AE.pubsub.on('de:getdataRadius',getdataRadius);
	    Models.TaskModel = Backbone.Model.extend({
		});
	    Collections.TaskList = Backbone.Collection.extend({
        model: Models.TaskModel,	
       });
	   //taskList = new Collections.TaskList();
       var collection = new Collections.Posts();
	   MapView = new Views.Map_View_Remake({
				collection: collection,
				el: $('body'),
				latitude: ae_globals.map_center.latitude,
				longitude: ae_globals.map_center.longitude, 
		});
	   Views.TodoView = Backbone.View.extend({
		  tagName: "li",
          className: 'col-md-6 col-sm-6 col-xs-12',
		  initialize: function() {	  	
			    this.template = _.template( $("#search_room_template").html());
			},
		  render: function(){
			this.$el.html( this.template(this.model.toJSON()));
			this.$('.rate-it').raty({
                        half: true,
                        score: this.model.get('rating_score_comment'),
                        readOnly: true,
                        hints: raty.hint
                    });
			return this;
		  }
		});
		
  });
})(jQuery, AE.Views, AE.Models,AE.Collections, window.AE);        
</script> 
<script type="text/template" id="search_room_template">
<?php global $user_ID;?>
    <div class="sl-place-wrap">
        <div class="sl-place-img">	
        <# if(the_post_thumnail) { #>
            <a href="{{= permalink }}" class="img-place" title="{{= post_title }}">
                <img src="{{= the_post_thumnail }}">
                <div class="sl-place-address">
                    <p><i class="fa fa-map-marker"></i> <# if(distance_location!= 'no') { #><span class="distance">{{= distance_location}} - </span><# } #>{{= et_full_location }}</p>
                    <p><i class="fa fa-globe" aria-hidden="true"></i>{{= tax_input['location'][0].name }}</p>
                    <p><i class="fa fa-phone" aria-hidden="true"></i>{{= et_phone }}</p>
                </div>
            </a>
                
                <# if(ribbon != '' ) {  #>
                    <div class="sl-ribbon-event cat-{{= place_category[0] }}" style="background:{{= color_cat}}">
                        <span title="{{= ribbon }}" >{{= ribbon }}</span>
                    </div>
                <# } #>
        <# } #>        
        </div>
        <div class="sl-place-info">
            <div class="sl-place-title">
                <a href="{{= permalink }}" title="{{= post_title }}">{{= post_title }}</a>
                <div class="rate-it" data-score="{{= rating_score_comment }}"></div>
               <?php if (is_plugin_active('de_multirating/de_multirating.php')) { ?>
                    <# if(multi_overview_score != 0) { #>
                        <div class="rating">{{= multi_overview_score}}</div>
                    <# } #>
                <?php } ?>
            </div> 
            <div class="sl-place-author">
                <div class="sl-author-avatar">
                    <a href="">
                       {{= avatar_author_search}}
                       {{= display_name}}
                    </a>
                    
                </div>
                  <?php if( ae_get_option("enable_view_counter",false) ): ?>
                        <div class="sl-place-view"><span><i class="fa fa-eye" aria-hidden="true"></i>{{= view_count}}</span><span><i class="fa fa-commenting" aria-hidden="true"></i>{{= total_count_comment}}</span></div>
                   <?php endif; ?>
            </div>
        </div>
    </div>
</script>