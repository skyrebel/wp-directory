(function($, Views, Models, Collections) {
    $(document).ready(function() {
        
        /**::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
        /**::                                                                         :*/
        /**::  This routine calculates the distance between two points (given the     :*/
        /**::  latitude/longitude of those points).                                   :*/
        /**::                                                                         :*/
        /**::  Definitions:                                                           :*/
        /**::    South latitudes are negative, east longitudes are positive           :*/
        /**::                                                                         :*/
        /**::  Passed to function:                                                    :*/
        /**::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
        /**::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
        /**::    unit = the unit you desire for results                               :*/
        /**::           where: 'MILE' is statute miles (default)                      :*/
        /**::                  'KM' is kilometers                                     :*/
        /**::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
        function distance(lat1, lon1, lat2, lon2) {

            var radlat1 = Math.PI * lat1/180;
            var radlat2 = Math.PI * lat2/180;
            var theta = lon1-lon2;
            var radtheta = Math.PI * theta/180;
            var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
            dist = Math.acos(dist);
            dist = dist * 180/Math.PI;
            // don vi dam
            dist = dist * 60 * 1.1515;
            var unit = ae_globals.units_of_measurement;
            if(unit === 'km' ) {
                // If distance smaller than 1 Km => convert unit to Metre
                if(dist < 1){
                    dist = Math.ceil(dist * 1000) + ' m';
                }else{
                    // Convert unit to Kilometer
                    // 1 Mi = 1.609 Km
                    dist = Math.ceil(dist * 1.609) + ' Km';
                }                
            }else{
                // Convert unit to Miles
                dist = Math.ceil(dist) + ' Mi';
            }
            return dist;
        }

        $('.search-location-marker').click(function(){
             if (parseInt(ae_globals.geolocation)) {
                GMaps.geolocate({
                    success: function(position) {
                        var coords = position.coords;
                        this.view_templates = coords;
                    latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    var geocoder = new google.maps.Geocoder();
                     geocoder.geocode({
                         "latLng":latlng
                     }, function (results, status) {
                         if (status == google.maps.GeocoderStatus.OK) {
                            $('#search_address_search').val(results[0].formatted_address);
                            var target       = $('#search-places');
                            var form         = target.find( '.place_search_form' );
                            form.find(':input[name="latitude"]' ).val(position.coords.latitude);
                            form.find(':input[name="longitude"]' ).val(position.coords.longitude);                          
                         }
                     });
                    },
                    error: function(error) {
                        AE.pubsub.trigger('ae:notification',{msg:ae_globals.geolocation_failed + ': '+ error.message,notice_type: 'error',});
                    },
                    not_supported: function() {
                        alert(ae_globals.browser_supported);
                        AE.pubsub.trigger('ae:notification',{msg: ae_globals.browser_supported,notice_type: 'error',});
                    }
                });
            }   
        });
        if(document.getElementById('search_address_search') !== null){
        var input_add = document.getElementById('search_address_search');
            if(ae_globals.gg_map_apikey)
            {
                var autocomplete_add = new google.maps.places.Autocomplete(input_add);
                autocomplete_add.addListener('place_changed', function() {        
                        var place = autocomplete_add.getPlace();              
                        var target       = $('#search-places');
                        var form         = target.find( '.place_search_form' );
                        form.find(':input[name="latitude"]' ).val(place.geometry.location.lat());
                        form.find(':input[name="longitude"]' ).val(place.geometry.location.lng()); 
                     });
            }
        }
        /** Distance between two points */
        $('.slider-value-default').slider({
                min: 0,
                max: parseInt(ae_globals.number_radius),
                step: 1,
                orientation: 'horizontal',
                value: 0,
                selection: 'before',
                tooltip: 'show',
                handle: 'round',
                formater: function(value) {
                    return value;
                }

            });
        $('.form-group>i.fa-map-marker').click(function () {
            $('#search_address_search').focus();
        });

        /** BlockControl AREA**/

        
        AreaItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'area-item col-md-4 col-sm-4 col-xs-6',
            template: _.template($('#ae-area-loop').html()),
        });
        ListArea = Views.ListPost.extend({
            tagName: 'ul',
            itemView: AreaItem,
            itemClass: 'area-item'
        });

        if($('.block-area').length > 0 ){
            $('.block-area').each(function(){
                var collection = new Collections.Posts();
                collection.action = 'ae-sync-areas';
                new ListArea({
                        el : $(this).find('.list-areas'),   
                        collection : collection ,
                        itemView: AreaItem,
                        itemClass: ''
                    });

                new Views.BlockControl({
                    collection: collection,
                    el: $(this),
                    onAfterInit: function(){
                    },
                    onAfterFetch : function(result, res){
                        console.log(result);
                    },
                    switchTo: function() {
                        // if (this.$('.list-option-filter').length == 0) return;
                        return;
                    }
                });
            });
        }

        /** BlockControl AREA**/
        if ($('#ae-place-loop').length > 0) {
            PostItem = Views.PostItem.extend({
                template: _.template($('#ae-place-loop').html()),
                onItemBeforeRender: function() {

                    var parent = this.$el.parents('ul'),
                        img = parent.attr('data-thumb');
                    /**
                     * check thumbnail size and set to model post thumbnail
                     */
                    if (typeof img !== 'undefined') {
                        var thum = this.model.get(img);
                        if (thum) {
                            this.model.set('the_post_thumnail', thum);
                        }
                    }

                },

                onItemRendered: function() {
                    if (typeof this.model.get('page') !== 'undefined') {
                        this.$el.addClass('page-' + this.model.get('page'));
                    } else {
                        this.$el.addClass('page-1');
                    }
                    var view = this;
                    this.$('.rate-it').raty({
                        half: true,
                        score: view.model.get('rating_score_comment'),
                        readOnly: true,
                        hints: raty.hint
                    });
                    // Render distance between two points
                    if(parseInt(ae_globals.geolocation) === 1) {
                        var location_lat, location_lng;
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                // Get GeoLocation of device
                                location_lat = position.coords.latitude;
                                location_lng =  position.coords.longitude;

                                var lat_item = view.model.get('et_location_lat'),
                                    lng_item = view.model.get('et_location_lng');
                                var dist = distance(lat_item, lng_item, location_lat, location_lng);
                                view.$('.distance', this).text(dist + ' -');
                            });
                        }
                    }
                }
            });

            ListView = Views.ListPost.extend({
                tagName: 'ul',
                itemView: PostItem,
                itemClass: 'place-item'
            });

            /**
             * index control list pending and publish
             */
            //$('#publish_place_wrapper').
            if ($('#publish-places .postdata').length > 0) {
                var postdata = JSON.parse($('#publish-places .postdata').html()),
                    publish_collection = new Collections.Posts(postdata);
            }else {
                var publish_collection = new Collections.Posts();
            }
            new Views.BlockControl({
                collection: publish_collection,
                el: '.publish_place_wrapper',
                thumbnail: $('#publish-places').attr('data-thumb'),
                onAfterInit: function(){
                    var view = this;
                    if(typeof view.query != 'undefined'){
                        if(ae_globals.is_search == 1){
                            view.query['is_search'] = parseInt(ae_globals.is_search);
                        }
                        if(ae_globals.is_tax == 1){
                            view.query['is_tax'] = ae_globals.is_tax;
                        }
                    }
                },
                onAfterFetch : function(result, res){
                    var view = this;
                    if (res && res.success) {
                        $('.top-title-post-place span.found_search').each(function(){
                            $(this).text(res.status);
                        });
                    }
                },

            });
            

            /**
             * initialize pending collections
             */
            if ($('#pending-places').length > 0) {
                var postdata = JSON.parse($('#pending-places .postdata').html()),
                    pending_collection = new Collections.Posts(postdata);
            } else {
                var pending_collection = new Collections.Posts();
            }

            new Views.Index({
                el: $('#list-places-wrapper'),
                pending: pending_collection,
                publish: publish_collection
            });
            // end index control publish and pending place

            $('.block-posts').each(function() {
                var postdata = JSON.parse($(this).find('.postdata').html()),
                    collection = new Collections.Posts(postdata);
                var defaultdisplay = $(this).attr('data-defaultdisplay');
                new ListView({
                    itemView: PostItem,
                    collection: collection,
                    el:  $(this).find('ul')
                });

                new Views.BlockControl({
                    collection: collection,
                    el: $(this),
                    thumbnail: $(this).attr('data-thumb'),
                    grid : (defaultdisplay === '1') ? 'list' : 'grid',
                    onAfterChangeView : function(){
                        this.switchTo();
                    },
                    onAfterLoadMore : function(){
                        this.switchTo();
                    },
                    onAfterFetch: function(){
                        this.switchTo();
                    },
                    switchTo: function() {
                        var view = this;
                        if (view.grid == 'grid') {
                            view.$('ul > li').addClass('col-md-3 col-xs-6').removeClass('col-md-12');
                            // view.$('ul > li').addClass('col-md-4').removeClass('col-md-12');
                            view.$('ul').removeClass('fullwidth');
                        } else {
                            view.$('ul > li').removeClass('col-md-3 col-xs-6').addClass('col-md-12');
                            // view.$('ul > li').removeClass('col-md-4').addClass('col-md-12');
                            view.$('ul').addClass('fullwidth');
                        }
                    },
                    order : function(event){
                        event.preventDefault();
                        var $target = $(event.currentTarget),
                            name = $target.attr('data-sort'),
                            meta_key = $target.attr('data-meta-key'),
                            random = $target.attr('data-random'),
                            post_in = $target.attr('data-post-in'),
                            view = this;
                        if (name !== 'undefined') {
                            view.$('.orderby').removeClass('active');
                            $target.addClass('active');
                            /**
                             * set orderby arg to query
                             */
                            view.query['orderby'] = name;
                            if(random==='true'){
                                view.query['post__in'] = $('#data-random-post').val();
                            }
                            if(meta_key==='latest')
                                view.query['meta_key'] = '';
                            else if(meta_key==='rating_score_comment'){
                                view.query['orderby'] = meta_key;
                                view.query['meta_key'] = meta_key;
                            }
                            if(post_in!=="")
                                view.query['post__in'] =  post_in;
                            view.query['order'] = "DESC";
                            view.page = 1;
                            // fetch post
                            view.fetch($target);
                        }
                    },
                });
            });

            /**
             * review block control
             */
            ReviewItem = Views.PostItem.extend({
                template: _.template($('#de-review-item').html()),
                // class name define column 
                className: 'col-md-4 col-sm-6 review-item',
                onItemRendered: function() {
                    var view = this;
                    this.$('.rate-it').raty({
                        half: true,
                        score: view.model.get('rating_score_comment'),
                        readOnly: true,
                        hints: raty.hint
                    });
                    
                    // Render distance between two points
                    if(parseInt(ae_globals.geolocation) === 1) {
                        var location_lat, location_lng;
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                // Get GeoLocation of device
                                location_lat = position.coords.latitude;
                                location_lng =  position.coords.longitude;

                                var post_data = view.model.get('post_data'),
                                    lat_item = post_data.et_location_lat,
                                    lng_item = post_data.et_location_lng;
                                var dist = distance(lat_item, lng_item, location_lat, location_lng);
                                view.$('.distance', this).text(dist + ' -');
                            });
                        }
                    }
                }
            });

            ListReview = Views.ListPost.extend({
                tagName: 'ul',
                itemView: ReviewItem,
                itemClass: 'review-item'
            });

            $('.comment-block').each(function() {
                if ($(this).find('.postdata').length > 0) {
                    var postdata = JSON.parse($(this).find('.postdata').html()),
                        collection = new Collections.Comments(postdata);
                    // set action to 'ae-fetch-comments'
                    collection.action = 'ae-fetch-comments';

                    new ListReview({
                        itemView: ReviewItem,
                        collection: collection,
                        el: $(this).find('ul')
                    });

                    new Views.BlockControl({
                        collection: collection,
                        el: $(this)
                    });
                }
            });


            BlogItem = Views.PostItem.extend({
                template: _.template($('#ae-post-loop').html()),
                // class name define column 
                className: 'post type-post'
            });
            ListBlog = Views.ListPost.extend({
                tagName: 'ul',
                itemView: BlogItem,
                itemClass: 'type-post'
            });


            $('.blog-wrapper').each(function() {
                if ($(this).find('.postdata').length > 0) {
                    var postdata = JSON.parse($(this).find('.postdata').html()),
                        collection = new Collections.Blogs(postdata);

                    new ListBlog({
                        itemView: BlogItem,
                        collection: collection,
                        el:$(this).find('ul')
                    });
                    if(typeof de_blog === 'undefined'){
                        de_blog = new Views.BlockControl({
                            collection: collection,
                            el: $(this)
                        });    
                    }
                    
                }
            });
        }
    });

})(jQuery, AE.Views, AE.Models, AE.Collections);