(function($) {
	$(document).ready(function() {
        $('#dl-menu').dlmenu();
        $('.dl-trigger').click(function() {
            //$('#menu-footer').find('.active').removeClass('active');
            if ($(this).hasClass('dl-active')) {
                window.scrollTo(0, 0);
                //$(this).addClass('active');
            }
        });
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


        if(parseInt(ae_globals.geolocation) === 1) {
            if($('.post-item').length > 0) {
                var location_lat, location_lng;
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        // Get GeoLocation of device
                        location_lat = position.coords.latitude;
                        location_lng =  position.coords.longitude;

                        $('.post-item').each(function () {
                            var lat_item = $('#latitude', this).attr('content'),
                                lng_item = $('#longitude', this).attr('content');
                            var dist = distance(lat_item, lng_item, location_lat, location_lng);
                            $('.distance', this).text(dist + ' -');
                        });
                    });
                }
            }
        }

        /**
         * toggle search form
         */
        $('.search-btn, .btn-close-form').click(function(e) {
            $('.search-field').focus();
            $('body').toggleClass('overflow-hidden');
            $option_search = $('.search-form-wrapper');
            $marsk = $('.marsk-black');
            // toggle search form
            $marsk.fadeToggle(300);
            //$btn_topsearch.toggleClass('active');
            $option_search.slideToggle(300, 'easeInOutSine', function(event) {
                $('.slider-ranger').slider();
            });
        });
        
        //mobile block control
        DE_Mobile = AE.Views.BlockControl.extend({
            onAfterInit: function() {
                var view = this;
                if ($(window).scrollTop() === $(document).height() - $(window).height()) {
                    view.$('.inview').click();
                }
                var inviewid = view.$('.inview').attr('id');
                $('#' + inviewid).bind('inview', function(event, isVisible) {
                    if (!isVisible || view.scrolled) {
                        this.inViewVisible = false;
                        return;
                    }
                    view.loadMore(event);
                });
            },
            onAfterLoadMore: function(result, res) {
                var view = this;
                if (res.success) {
                    if (res.max_num_pages <= view.page || res.data.length === 0) {
                        view.$('.inview').hide();
                    }
                } else {
                    view.$('.inview').hide();
                }
            }, 
            onAfterFetch : function (result, res) {
                var view = this;
                if (res.success && res.max_num_pages > 1) {
                    view.$('.inview').show();
                } else {
                    view.$('.inview').hide();
                }
            }
        });

		// Profile Place
        if($('#user_place').length > 0 ){
            PostItem = AE.Views.PostItem.extend({
                template: _.template($('#ae-place-loop').html()),
                className: 'post-item',
                events: {
                    'click .edit-config': 'showEditConfig'
                },
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

                                var lat_item = view.model.get('et_location_lat'),
                                    lng_item = view.model.get('et_location_lng');
                                var dist = distance(lat_item, lng_item, location_lat, location_lng);
                                view.$('.distance', this).text(dist + ' -');
                            });
                        }
                    }
                },
                showEditConfig: function(event){
                    $target = $(event.currentTarget);
                    $target.parents('.place-wrapper').toggleClass('active-edit');
                    $target.parents('.place-wrapper').children('.edit-place-post').slideToggle("slow");
                }
            });

            ListPlace = AE.Views.ListPost.extend({
                tagName: 'ul',
                itemView: PostItem,
                itemClass: 'post-item'
            });

            $('#user_place').each(function() {
                if( $(this).find('.postdata').length > 0 ){
                    var postdata   = JSON.parse($(this).find('.postdata').html()),
                        collection = new AE.Collections.Posts(postdata);
                } else {
                    collection = new AE.Collections.Posts();
                }
                new ListPlace({
                    el : $(this).find('#place-list'),   
                    collection : collection ,
                    itemView: PostItem,
                    itemClass: 'post-item '
                });

                /**
                 * init block control list blog
                 */
                new AE.Views.BlockControl({
                    collection: collection,
                    el: $(this),
                    onAfterFetch : function( result, res){
                       
                    }
                });
            });
        }
        // Profile Place

        /**
         * Event list control
         */
        if ($('#place-events-list').length > 0) {
            PostItem = AE.Views.PostItem.extend({
                template: _.template($('#ae-place-loop').html()),
                className: 'post-item',
                events: {
                    'click .edit-config': 'showEditConfig'
                },
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

                                var lat_item = view.model.get('et_location_lat'),
                                    lng_item = view.model.get('et_location_lng');
                                var dist = distance(lat_item, lng_item, location_lat, location_lng);
                                view.$('.distance', this).text(dist + ' -');
                            });
                        }

                    }
                },
                showEditConfig: function(event){
                    $target = $(event.currentTarget);
                    $target.parents('.place-wrapper').toggleClass('active-edit');
                    $target.parents('.place-wrapper').children('.edit-place-post').slideToggle("slow");
                }
            });
            ListPlace = AE.Views.ListPost.extend({
                tagName: 'ul',
                itemView: PostItem,
                itemClass: 'post-item'
            });
            if ($('.postdata').length > 0) {
                var collection = new AE.Collections.Posts(JSON.parse($('.postdata').html()));
            } else {
                var collection = new AE.Collections.Posts();
            }
            collection.action = 'ae-fetch-events';
            new ListPlace({
                itemView: PostItem,
                collection: collection,
                el: '#place-events-list'
            });
            new DE_Mobile({
                collection: collection,
                el: '#place-list-wrapper'
            });
        } 
        //place overdue list control

        /**
         * review list control
         */
        if ($('#list-reviews').length > 0) {
            /**
             * review block control
             */
            ReviewItem = AE.Views.PostItem.extend({
                template: _.template($('#de-review-item').html()),
                className: 'post-item',
                events: {
                    'click .edit-config': 'showEditConfig'
                },
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
                                    lat_item = post_data['et_location_lat'],
                                    lng_item = post_data['et_location_lng'];
                                var dist = distance(lat_item, lng_item, location_lat, location_lng);
                                view.$('.distance', this).text(dist + ' -');
                            });
                        }

                    }
                },
                showEditConfig: function(event){
                    $target = $(event.currentTarget);
                    $target.parents('.place-wrapper').toggleClass('active-edit');
                    $target.parents('.place-wrapper').children('.edit-place-post').slideToggle("slow");
                }
            });
            ListReview = AE.Views.ListPost.extend({
                tagName: 'ul',
                itemView: ReviewItem,
                itemClass: 'post-item'
            });
            var collection = new AE.Collections.Comments();
            new ListReview({
                itemView: ReviewItem,
                collection: collection,
                el: '#list-reviews'
            });
            new DE_Mobile({
                collection: collection,
                el: '#reviews-list-wrapper'
            });
        } 
        //review list control

        /**
         * Event list control
         */
        if ($('#place-events-list').length > 0) {

            /**
             * Event block control
             */
            EventItem = AE.Views.PostItem.extend({
                template: _.template($('#ae-event-loop').html()),
                className: 'post-item',
                events: {
                    'click .edit-config': 'showEditConfig'
                },
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
                                view.$('.distance').text(dist + ' -');
                            });
                        }

                    }
                },
                showEditConfig: function(event){
                    $target = $(event.currentTarget);
                    $target.parents('.place-wrapper').toggleClass('active-edit');
                    $target.parents('.place-wrapper').children('.edit-place-post').slideToggle("slow");
                }
            });
            ListEvent = AE.Views.ListPost.extend({
                tagName: 'ul',
                itemView: EventItem,
                itemClass: 'post-item'
            });
            var collection = new AE.Collections.Comments();
            collection.action = 'ae-fetch-events';
            
            new ListEvent({
                itemView: EventItem,
                collection: collection,
                el: '#place-events-list'
            });
            new DE_Mobile({
                collection: collection,
                el: '#list-events'
            });
        } 
        //Event list control

        /**
         * Togo list control
         */
        if ($('#list-favorite').length > 0) {
            /**
             * review block control
             */
            FavoriteItem = AE.Views.PostItem.extend({
                template: _.template($('#ae-togo-loop').html()),
                className: 'post-item',
                events: {
                    'click .edit-config': 'showEditConfig'
                },
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
                                view.$('.distance').text(dist + ' -');
                            });
                        }

                    }
                },
                showEditConfig: function(event){
                    $target = $(event.currentTarget);
                    $target.parents('.place-wrapper').toggleClass('active-edit');
                    $target.parents('.place-wrapper').children('.edit-place-post').slideToggle("slow");
                }
            });
            ListFavorite = AE.Views.ListPost.extend({
                tagName: 'ul',
                itemView: FavoriteItem,
                itemClass: 'post-item'
            });
            var collection = new AE.Collections.Comments();
            collection.action = 'ae-fetch-favorite';
            new ListFavorite({
                itemView: FavoriteItem,
                collection: collection,
                el: '#list-favorite'
            });
            new DE_Mobile({
                collection: collection,
                el: '#list-favorite-wrapper'
            });
        } 
        //Togo list control

        /**
         * Picture list control
         */
        if ($('#list-picture').length > 0) {
            /**
             * review block control
             */
            PictureItem = AE.Views.PostItem.extend({
                template: _.template($('#ae-picture-loop').html()),
                className: 'col-xs-6',
                events: {
                    'click .edit-config': 'showEditConfig'
                },
                onItemRendered: function() {
                    var view = this;
                    this.$('.rate-it').raty({
                        half: true,
                        score: view.model.get('rating_score_comment'),
                        readOnly: true,
                        hints: raty.hint
                    });
                },
                showEditConfig: function(event){
                    $target = $(event.currentTarget);
                    $target.parents('.place-wrapper').toggleClass('active-edit');
                    $target.parents('.place-wrapper').children('.edit-place-post').slideToggle("slow");
                }
            });
            ListPicture = AE.Views.ListPost.extend({
                tagName: 'ul',
                itemView: PictureItem,
                itemClass: 'col-xs-6'
            });
            var collection = new AE.Collections.Comments();
            collection.action = 'ae-fetch-pictures';
            new ListPicture({
                itemView: PictureItem,
                collection: collection,
                el: '#list-picture'
            });
            new DE_Mobile({
                collection: collection,
                el: '#list-picture-wrapper'
            });
        } 
        //Picture list control

        $('#search-nearby').click(function(event) {
            navigator.geolocation.getCurrentPosition(searchNearby, errorHandle);
        });

        function searchNearby(position) {
            var coords = position.coords;
            $('#nearby').find('input').val(coords.latitude + ',' + coords.longitude);
            $('#nearby').submit();
        }

        function errorHandle(error) {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    alert(de_front.texts.request_geo);
                    return;
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert(de_front.texts.location_unavailable);
                    return;
                    break;
                case error.TIMEOUT:
                    alert(de_front.texts.request_time_out);
                    return;
                    break;
                case error.UNKNOWN_ERROR:
                    alert(de_front.texts.error_occurred);
                    return;
                    break;
            }
            alert(de_front.texts.request_geo);
        }
        $('form').validate();
	});
})(jQuery);