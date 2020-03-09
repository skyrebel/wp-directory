<?php
/**
 * Template Name: Search Location Html
*/
get_header();
?>
<style>
#google_canvas {
   height:560px;
} 
</style>
<!-- Breadcrumb List users -->
<?php
		$keywords = ( !empty( $_GET['search_keywords'] ) )?sanitize_text_field( $_GET['search_keywords'] ):'';
        $location = ( !empty( $_GET['search_location'] ) )?sanitize_text_field( $_GET['search_location'] ):'';
        $latitude = ( !empty( $_GET['latitude'] ) )?sanitize_text_field( $_GET['latitude'] ):'';
        $longitude = ( !empty( $_GET['longitude'] ) )?sanitize_text_field( $_GET['longitude'] ):'';
 ?>

<div class="search-location-wrap">
    <div class="row">
        <div class="col-lg-6 col-md-12">
            <form id="search-location-form" action="">
                <div class="row">
                    <div class="col-md-6">
                        <input class="text-field" type="text" name="search_keywords" id="search_keywords" placeholder="Enter keyword..." value="<?php echo esc_attr( $keywords ); ?>" >
                    </div>
                    <div class="col-md-6">
                        <div class="sl-address">
                            <input class="text-field" type="text" id="address" placeholder="Address..." value="<?php echo esc_attr( $location ); ?>" >
                            <span><i class="fa fa-map-marker" aria-hidden="true"></i></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="search_categories">
                              <?php 
                                ae_tax_dropdown('place_category', array('hide_empty' => true,
                                    'class' => 'chosen-single tax-item',
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
                                    'class' => 'chosen-single tax-item',
                                    'hierarchical' => true, 
                                    'show_option_all' => __("All Location", ET_DOMAIN) , 
                                    'taxonomy' => 'location' ,
                                    'value' => 'slug',
                                    'selected' => (isset($_GET['place_location']) && $_REQUEST['place_location']) ? $_REQUEST['place_location'] : ''
                                )); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <p class="sl-slider-range">With in: <span>&lt; 140 km</span></p>
                        <input type="text" class="slider-value-default slider-ranger-radius"
                                                data-slider-min="0" data-slider-max="50" 
                                                data-slider-step="1" data-slider-value="25" 
                                                data-slider-orientation="horizontal" data-slider-selection="before" 
                                                data-slider-tooltip="show"
                                            />
                        <input type="hidden" name="latitude" id="latitude" value="<?php echo esc_attr( $latitude ); ?>">
                        <input type="hidden" name="longitude" id="longitude" value="<?php echo esc_attr( $longitude ); ?>">
                        <input type="hidden" name="place_tax" id="place_tax">
                    </div>
                    <div class="col-md-12" style="text-align: center;">
                        <button type="button" class="search-btn" id="update_results">SEARCH</button>
                    </div>
                </div>
            </form>
            <div class="search-location-line"></div>
            <div class="result-search-location">
                <div class="filter-reset">
                    <div class="result-pagination">
                        <div class="nrp"><span>12</span>RESULTS</div>
                        <div class="prp"><span>1-4</span><span>of</span>76</div>
                    </div>
                    <div class="reset-pagination"><span><i class="fa fa-repeat" aria-hidden="true"></i>Reset</span></div>
                </div>
                <ul class="search-location-list-place row">
                    <li class="col-md-6 col-sm-6 col-xs-12">
                        <div class="sl-place-wrap">
                            <div class="sl-place-img">
                                <a href=""><img src="https://lab.enginethemes.com/refactor-de/wp-content/uploads/2017/01/CMS_Creative_164657191_Kingfisher.jpg" alt=""></a>
                                <div class="sl-place-address">
                                    <p><i class="fa fa-map-marker"></i><span>2 km</span></p>
                                    <p><i class="fa fa-globe" aria-hidden="true"></i>vietnam</p>
                                    <p><i class="fa fa-phone" aria-hidden="true"></i>091254256</p>
                                </div>
                                <div class="sl-ribbon-event">
                                    <span>50% OFF</span>
                                </div>
                            </div>
                            <div class="sl-place-info">
                                <div class="sl-place-title">
                                    <a href="">Dufferin Grove Park Dufferin Grove Park </a>
                                    <div class="rate-it" data-score="1.5"></div>
                                    <div class="rating">1.5</div>
                                </div>
                                <div class="sl-place-author">
                                    <div class="sl-author-avatar">
                                        <a href="">
                                            <img src="http://localhost/de/wp-content/uploads/2016/08/xu-huong-lua-chon-vay-dam-dep-gia-re-phong-cach-han-quoc-duoc-gioi-tre-ua-chuong-1191-150x150.jpg" alt="">
                                            Aministator day nha tui user dien ro
                                        </a>
                                        
                                    </div>
                                    <div class="sl-place-view"><span><i class="fa fa-eye" aria-hidden="true"></i>85</span><span><i class="fa fa-commenting" aria-hidden="true"></i>58</span></div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="col-md-6 col-sm-6 col-xs-12">
                        <div class="sl-place-wrap">
                            <div class="sl-place-img">
                                <a href=""><img src="https://lab.enginethemes.com/refactor-de/wp-content/uploads/2017/01/CMS_Creative_164657191_Kingfisher.jpg" alt=""></a>
                                <div class="sl-place-address">
                                    <p><i class="fa fa-map-marker"></i><span>2 km</span></p>
                                    <p><i class="fa fa-globe" aria-hidden="true"></i>vietnam</p>
                                    <p><i class="fa fa-phone" aria-hidden="true"></i>091254256</p>
                                </div>
                                <div class="sl-ribbon-event">
                                    <span>50% OFF</span>
                                </div>
                            </div>
                            <div class="sl-place-info">
                                <div class="sl-place-title">
                                    <a href="">Dufferin Grove Park Dufferin Grove Park </a>
                                    <div class="rate-it" data-score="1.5"></div>
                                    <div class="rating">1.5</div>
                                </div>
                                <div class="sl-place-author">
                                    <div class="sl-author-avatar">
                                        <a href="">
                                            <img src="http://localhost/de/wp-content/uploads/2016/08/xu-huong-lua-chon-vay-dam-dep-gia-re-phong-cach-han-quoc-duoc-gioi-tre-ua-chuong-1191-150x150.jpg" alt="">
                                            Aministator day nha tui user dien ro
                                        </a>
                                        
                                    </div>
                                    <div class="sl-place-view"><span><i class="fa fa-eye" aria-hidden="true"></i>85</span><span><i class="fa fa-commenting" aria-hidden="true"></i>58</span></div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="search-location-pagination">
                    <div class="result-pagination">
                        <div class="nrp"><span>76</span>RESULTS</div>
                    </div>
                    
                    <div class="paginations-wrapper main-pagination">
                        <a href="javascript:void(0)" class="page-link prev">Prev</a>
                        <a href="javascript:void(0)" class="page-link">1</a>
                        <a href="javascript:void(0)" class="page-link">2</a>
                        <a href="javascript:void(0)" class="page-link">3</a>
                        <a href="javascript:void(0)" class="page-link next">Next</a>
                    </div>
                    <div class="result-pagination right">
                        <div class="prp"><span>1-4</span><span>of</span>76</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 visible-lg" style="padding-right: 0;">
            <div id="google_canvas"></div>
        </div>
    </div>
</div>
   
<?php
    get_template_part( 'template/place-search' , 'radius' );
    get_footer(); 
?>
<script>
(function ($ , Views, Models, Collections,AE) {
   $(document).ready(function(){
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
          className: 'col-md-3 col-xs-6 place-item ae-item',
		  initialize: function() {	  	
			    this.template = _.template( $("#search_room_template").html());
			},
		 events: {
            // user click on action button such as edit, archive, reject
              'click .action': 'acting'
         },
         acting: function(e) {
            e.preventDefault();
            var target = $(e.currentTarget),
                action = target.attr('data-action'),
                view = this;
            switch (action) {
                case 'edit':
                    //trigger an event will be catch by AE.App to open modal edit
                    AE.pubsub.trigger('ae:model:onEdit', this.model);
                    break;
                case 'reject':
                    //trigger an event will be catch by AE.App to open modal reject
                    AE.pubsub.trigger('ae:model:onReject', this.model);
                    break;
                case 'archive':
                    if (confirm(ae_globals.confirm_message)) {
                        // archive a model
                        this.model.set('archive', 1);
                        this.model.save('archive', '1', {
                            beforeSend: function() {
                                view.blockItem();
                            },
                            success: function(result, res, xhr) {
                                AE.pubsub.trigger('ae:post:archiveSuccess', result, res, xhr);
                                view.unblockItem();
                            }
                        });
                    }else{
                        return false;
                    }
                    break;
                case 'toggleFeature':
                    // toggle featured
                    this.model.save('et_featured', 1);
                    break;
                case 'approve':
                    // publish a model
                    this.model.save('publish', '1', {
                        beforeSend: function() {
                            view.blockItem();
                        },
                        success: function(result, res, xhr) {
                            view.triggerMethod("before:approve", view, res);
                            view.unblockItem();
                        }
                    });
                    break;
                case 'delete':
                    if (confirm(ae_globals.confirm_message)) {
                        // archive a model
                        this.model.save('delete', '1', {
                            beforeSend: function() {
                                view.blockItem();
                            },
                            success: function(result, res, xhr) {
                                view.unblockItem();
                                if(res.success){
                                    view.model.destroy();
                                }
                            }
                        });
                    }
                    break;
                default:
                    //trigger an event will be catch by AE.App to open modal edit
                    AE.pubsub.trigger('ae:model:on' + action, this.model);
                    break;
            }
        },
		  render: function(){
			this.$el.html( this.template(this.model.toJSON()));
			this.$('.rate-it').raty({
                        half: true,
                        score: this.model.get('rating_score'),
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
    <div class="place-wrapper">
        <div class="hidden-img">	
        <# if(the_post_thumnail) { #>
            <a href="{{= permalink }}" class="img-place" title="{{= post_title }}">
                <img src="{{= the_post_thumnail }}">
                <# if(ribbon != '' ) {  #>
                <div class="cat-{{= place_category[0] }}" >
                    <div class="ribbon">
                        <span class="ribbon-content" title="{{= ribbon }}" >{{= ribbon }}</span>
                    </div>
                </div>
                <# } #>
            </a>
        <# } #>
        <# if(et_featured == 1) { #>
            <span class="tag-featured"><i class="fa fa-flag"></i><?php _e('Featured',ET_DOMAIN)?></span>
        <# } #>        
        </div>
        <div class="place-detail-wrapper">  
        	<h2 class="title-place"><a href="{{= permalink }}" title="{{= post_title }}">{{= post_title }}</a></h2>
            <span class="address-place"><i class="fa fa-map-marker"></i><span class="distance">{{= distance }}</span> {{= et_full_location }}</span>
            <div class="content-place">{{= trim_post_content }}</div>
            <div class="clearfix rate-view">
                <div class="rate-it rate-cus" data-score="{{= rating_score }}"></div>
                <?php if( ae_get_option("enable_view_counter",false) ): ?>
                    <div class="view-count limit-display  tooltip-style"  data-toggle="tooltip" data-placement="top" title="{{= view_count}}">
                        <i class="fa fa-eye"></i> {{= view_count.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")}}
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</script>