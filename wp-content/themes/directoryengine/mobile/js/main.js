jQuery(document).ready(function($) {

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
    /**::           where: 'MILE' is statute miles (default)                         :*/
    /**::                  'KM' is kilometers                                      :*/
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
        var autocomplete_add = new google.maps.places.Autocomplete(input_add);
        autocomplete_add.addListener('place_changed', function() {        
                var place = autocomplete_add.getPlace();              
                var target       = $('#search-places');
                var form         = target.find( '.place_search_form' );
                form.find(':input[name="latitude"]' ).val(place.geometry.location.lat());
                form.find(':input[name="longitude"]' ).val(place.geometry.location.lng()); 
             });
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
    if(parseInt(ae_globals.geolocation) === 1) {
        // List Place item
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

        // List place review item
        if($('.review-item').length > 0) {
            var location_lat, location_lng;
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    // Get GeoLocation of device
                    location_lat = position.coords.latitude;
                    location_lng =  position.coords.longitude;

                    $('.review-item').each(function () {
                        var lat_item = $('#latitude', this).attr('content'),
                            lng_item = $('#longitude', this).attr('content');
                        var dist = distance(lat_item, lng_item, location_lat, location_lng);
                        $('.distance', this).text(dist + ' -');
                    });
                });
            }
        }

        if($('body.single').length > 0){
            var location_lat, location_lng;
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    // Get GeoLocation of device
                    location_lat = position.coords.latitude;
                    location_lng =  position.coords.longitude;

                    $('body.single').each(function(){
                        var lat_item = $('#latitude', this).attr('content'),
                            lng_item = $('#longitude', this).attr('content');
                        var dist = distance(lat_item, lng_item, location_lat, location_lng);
                        $('.distance', this).text(dist + ' -');
                    });
                });
            }
        }
    }
    /** End Distance between two points */

    $('#dl-menu').dlmenu();
    $('.dl-trigger').click(function() {
        //$('#menu-footer').find('.active').removeClass('active');
        if ($(this).hasClass('dl-active')) {
            window.scrollTo(0, 0);
            //$(this).addClass('active');
        }
    });
    $('.triagle-setting.mobile-setting').click(function(event) {
        $(this).parents('.place-wrapper').toggleClass('active');
        $(this).parents('.post-item').find('.list-option-place li').toggleClass('active');
    });
    // Tab
    $('#myTab a').click(function(e) {
        e.preventDefault();
        $(this).tab('show');
    });
    // Menu Bottom
    var height_1 = $('#menu-footer').height();
    var lastScrollTop = 0;
    $(window).scroll(function() {
        var st = $(this).scrollTop();
        if (st > lastScrollTop && st > 50) {
            // scrolling down
            if ($('#menu-footer').data('size') === 'big') {
                $('#menu-footer').data('size', 'small');
                $('#menu-footer').stop().animate({
                    bottom: '-' + height_1 + 'px',
                    opacity: '0'
                }, 300);
            }
        } else {
            // scrolling up
            if ($('#menu-footer').data('size') === 'small') {
                $('#menu-footer').data('size', 'big');
                $('#menu-footer').stop().animate({
                    bottom: '0',
                    opacity: '1'
                }, 300);
            }
        }
        lastScrollTop = st;
    });
    /**
     * toggle search form
     */
    $('.fancybox').magnificPopup({
        type: 'image',
        gallery: {
            enabled: true
        },
        // other options
    });
    $('.rate-it').raty({
        readOnly: true,
        half: true,
        score: function() {
            return $(this).attr('data-score');
        },
        hints: raty.hint
    });
    $('.rating-it').raty({
        half: true,
        hints: raty.hint
    });
    $('.list-images > li').each(function(){
        var w_li = 33.3333;
        var parentWidth = $(window).width();
        parentWidth -= 58;
        console.log("w_li:" + w_li);
        console.log(parentWidth);
        w_li = (parentWidth * w_li)/100;
        $(this).css({'height': w_li+'px'});
    });

    /*
    *   change selec place action
    */
    $('#choose-place-action').change(function(event) {
        $('.place-action').removeClass('active');
        var id = event.target.options[event.target.selectedIndex].value;
        $('#'+id).addClass('active');
    });

    // $('.edit-config').click(function() {
    //     console.log($(this).parents('.place-wrapper').next());
    //     $(this).parents('.place-wrapper').next().slideToggle("slow");
    // });
    /*Remove captcha from post review to post comment*/
    $('.comment-reply-link').click(function(){
        $('.g-recaptcha').prependTo('#comment-captcha');   
    });
    /*Remove captcha from post comment to post review*/
    $('#cancel-comment-reply-link').click(function(){
        $('.g-recaptcha').prependTo('.gg-captcha');
    });

});
(function($) {
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
        /**::           where: 'MILE' is statute miles (default)                         :*/
        /**::                  'KM' is kilometers                                      :*/
        /**::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
        function distance(lat1, lon1, lat2, lon2) {
            var radlat1 = Math.PI * lat1/180;
            var radlat2 = Math.PI * lat2/180;
            var radlon1 = Math.PI * lon1/180;
            var radlon2 = Math.PI * lon2/180;
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


        new AE.Views.SearchForm();
        Authentication = Backbone.View.extend({
            events: {
                // open signup form
                'click .link_sign_up': 'toggleSignupForm',
                // open login form
                'click .link_sign_in': 'toggleSigninForm',
                // open forgot pass form
                'click .link_forgot_pass': 'toggleForgotPassForm',
                // user submit login form
                'submit form#signin_form': 'login',
                // user subnmit register form
                'submit form#signup_form': 'register',
                // user forgot password
                'submit form#forgotpass_form': 'requestPassword',
            },
            initialize: function() {
                this.user = new AE.Models.User();
                this.blockUi = new AE.Views.BlockUi();
            },
            /**
             * hide signin form and show register form
             */
            toggleSignupForm: function(event) {
                event.preventDefault();
                $('#login').fadeOut();
                $('#register').fadeIn(500);
            },
            /**
             * hide register form and show login form
             */
            toggleSigninForm: function(event) {
                event.preventDefault();
                $('#register').fadeOut();
                $('#forgotpass').fadeOut();
                $('#login').fadeIn(500);
            },
            /**
             * show forgot pass form
             */
            toggleForgotPassForm: function(event) {
                event.preventDefault();
                $('#login').fadeOut();
                $('#forgotpass').fadeIn(500);
            },
            /**
             * login request
             */
            login: function(event) {
                event.preventDefault();
                var form = $(event.currentTarget),
                    button = form.find('input[type="submit"]'),
                    view = this,
                    message = form.parents('.login-page').find('.message');
                /**
                 * scan all fields in form and set the value to model user
                 */
                form.find('input, textarea, select').each(function() {
                    view.user.set($(this).attr('name'), $(this).val());
                });
                // check form validate and process sign-in
                view.user.set('do', 'login');
                view.user.request('read', {
                    beforeSend: function() {
                        view.blockUi.block(button);
                        form.addClass('processing');
                    },
                    success: function(user, status, jqXHR) {
                        view.blockUi.unblock();
                        form.removeClass('processing');
                        // trigger event process authentication
                        AE.pubsub.trigger('ae:user:auth', user, status, jqXHR);
                        if (status.success) {
                            window.location.reload();
                        } else {
                            //message.html('').append('<p>' + status.msg + '</p>');
                            //message.addClass('error').removeClass('success');
                        }
                    }
                });
            },
            /**
             * register request
             */
            register: function(event) {
                event.preventDefault();
                var form = $(event.currentTarget),
                    button = form.find('input[type="submit"]'),
                    view = this,
                    message = form.parents('.login-page').find('.message');
                /**
                 * scan all fields in form and set the value to model user
                 */
                form.find('input, textarea, select').each(function() {
                    view.user.set($(this).attr('name'), $(this).val());
                });
                // check form validate and process sign-in
                view.user.set('do', 'register');
                view.user.request('create', {
                    beforeSend: function() {
                        view.blockUi.block(button);
                        form.addClass('processing');
                    },
                    success: function(user, status, jqXHR) {
                        view.blockUi.unblock();
                        form.removeClass('processing');
                        // trigger event process authentication
                        AE.pubsub.trigger('ae:user:auth', user, status, jqXHR);
                        if (status.success) {
                            //message.html('').append('<p>' + status.msg + '</p>');
                            //message.addClass('success').removeClass('error');
                            setTimeout(function() {
                                window.location.reload();
                            }, 5000);
                        } else {
                            if(typeof(grecaptcha) != "undefined" && grecaptcha !== null)
                                grecaptcha.reset();
                            //message.html('').append('<p>' + status.msg + '</p>');
                            //message.addClass('error').removeClass('success');
                        }
                    }
                });
            },
            /**
             * request password
             */
            requestPassword: function(event) {
                event.preventDefault();
                var form = $(event.currentTarget),
                    button = form.find('input[type="submit"]'),
                    email = form.find('input.email').val(),
                    view = this,
                    message = form.parents('.login-page').find('.message');
                // check form validate and process sign-in
                view.user.set('do', 'forgot');
                this.user.set('user_login', email);
                view.user.request('read', {
                    beforeSend: function() {
                        view.blockUi.block(button);
                        form.addClass('processing');
                    },
                    success: function(user, status, jqXHR) {
                        view.blockUi.unblock();
                        form.removeClass('processing');
                        // trigger event process authentication
                        AE.pubsub.trigger('ae:user:forgotpass', user, status, jqXHR);
                        message.html('').append('<p>' + status.msg + '</p>');
                        if (status.success) {
                            message.addClass('success').removeClass('error');
                        } else {
                            message.addClass('error').removeClass('success');
                        }
                    }
                });
            }

        });
        new Authentication({
            el: $('#page-authentication')
        });
        /**
         * block control mobile
         */
        // DE_Mobile = Backbone.View.extend({
        //     events: {
        //         // ajax load more 
        //         'click a.load-more-post': 'loadMore',
        //         // filter 
        //         'change select ': 'selectFilter',
        //     },
        //     initialize: function() {
        //         var view = this;
        //         _.bindAll(this, 'loadMore');
        //         if (this.$('.ae_query').length > 0) {
        //             this.query = JSON.parse(this.$('.ae_query').html());
        //             this.query.page = 1;
        //         } else {
        //             this.$('.paginations').remove();
        //         }
        //         this.blockUi = new AE.Views.BlockUi();
        //         if ($(window).scrollTop() == $(document).height() - $(window).height()) {
        //             view.loadMore();
        //         }
        //         var inviewid = view.$('.inview').attr('id');
        //         $('#' + inviewid).bind('inview', function(event, isVisible) {
        //             if (!isVisible || view.scrolled) {
        //                 this.inViewVisible = false;
        //                 return;
        //             }
        //             view.loadMore();
        //         });
        //     },
        //     selectFilter: function(event) {
        //         var $target = $(event.currentTarget),
        //             name = $target.attr('name'),
        //             view = this;
        //         if (name !== 'undefined') {
        //             view.query[name] = $target.val();
        //             view.page = 1;
        //             // fetch page
        //             view.fetch($target);
        //         }
        //     },
        //     // fetch post
        //     fetch: function($target) {
        //         var view = this,
        //             page = view.page;
        //         view.collection.fetch({
        //             wait: true,
        //             remove: true,
        //             reset: true,
        //             data: {
        //                 query: view.query,
        //                 page: view.page,
        //                 action: 'ae-fetch-posts',
        //                 paginate: true,
        //                 thumbnail: 'thumbnail',
        //             },
        //             beforeSend: function() {
        //                 view.blockUi.block($target);
        //             },
        //             success: function(result, res, xhr) {
        //                 view.blockUi.unblock();
        //                 if (res.success && res.max_num_pages > 1) {
        //                     view.$('.inview').show();
        //                 } else {
        //                     view.$('.inview').hide();
        //                 }
        //                 // view.collection.reset();
        //             }
        //         });
        //     },
        //     /**
        //      * load more places
        //      */
        //     loadMore: function(e) {
        //         var view = this;
        //         view.page = view.query.paged;
        //         view.page++;
        //         view.query.paged++;
        //         // collection fetch
        //         this.collection.fetch({
        //             remove: false,
        //             data: {
        //                 query: view.query,
        //                 page: view.page,
        //                 paged: view.page,
        //                 action: 'ae-fetch-posts',
        //                 paginate: true,
        //                 thumbnail: 'thumbnail',
        //             },
        //             beforeSend: function() {
        //                 view.blockUi.block(view.$('.inview'));
        //             },
        //             success: function(result, res, xhr) {
        //                 view.blockUi.unblock();
        //                 if (res.success) {
        //                     if (res.max_num_pages <= view.page || res.data.length == 0) {
        //                         view.$('.inview').hide();
        //                     }
        //                 } else {
        //                     view.$('.inview').hide();
        //                 }
        //             }
        //         });
        //     },
        // }); // block control mobile
        /**
         * contact modal
         */
        DE_Contact = Backbone.View.extend({
            events: {
                'click a.contact-owner': 'openContactModal'
            },
            initialize: function() {
                this.user = new AE.Models.User();
                this.blockUi = new AE.Views.BlockUi();
            },
            openContactModal: function(event) {
                event.preventDefault();
                var $target = $(event.currentTarget);
                if (typeof this.editContactmodal === 'undefined') {
                    this.editContactmodal = new AE.Views.ContactModal({
                        el: $("#contact_message"),
                        model: this.user,
                        user_id: $target.attr('data-user')
                    });
                }
                this.editContactmodal.user_id = $target.attr('data-user');
                this.editContactmodal.openModal();
            },
        });
        new DE_Contact({
            el: $('body')
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
                 if (res.success && res.total)
                 {
                    view.$('#number_place').html(res.total + " Places");   
                 } 
                          
            }
        });

        /**
         * place list control
         */
        if ($('#place-list').length > 0) {
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
            new ListPlace({
                itemView: PostItem,
                collection: collection,
                el: '#place-list'
            });
            new DE_Mobile({
                collection: collection,
                el: '#place-list-wrapper'
            });
        } //place list control

        /**
         * place pending list control
         */
        if ($('#place-pending-list').length > 0) {
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
            new ListPlace({
                itemView: PostItem,
                collection: collection,
                el: '#place-pending-list'
            });
            new DE_Mobile({
                collection: collection,
                el: '#place-list-wrapper'
            });
        } //place pending list control

        /**
         * place overdue list control
         */
        if ($('#place-overdue-list').length > 0) {
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
            new ListPlace({
                itemView: PostItem,
                collection: collection,
                el: '#place-overdue-list'
            });
            new DE_Mobile({
                collection: collection,
                el: '#place-list-wrapper'
            });
        } //place overdue list control

        /**
         * place overdue list control
         */
        if ($('#place-rejected-list').length > 0) {
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
            new ListPlace({
                itemView: PostItem,
                collection: collection,
                el: '#place-rejected-list'
            });
            new DE_Mobile({
                collection: collection,
                el: '#place-list-wrapper'
            });
        } //place overdue list control

        /**
         * place overdue list control
         */
        if ($('#place-draft-list').length > 0) {
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
            new ListPlace({
                itemView: PostItem,
                collection: collection,
                el: '#place-draft-list'
            });
            new DE_Mobile({
                collection: collection,
                el: '#place-list-wrapper'
            });
        } //place overdue list control

        /**
         * place overdue list control
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
            new ListPlace({
                itemView: PostItem,
                collection: collection,
                el: '#place-events-list'
            });
            new DE_Mobile({
                collection: collection,
                el: '#place-list-wrapper'
            });
        } //place overdue list control



        /**
         * review list control
         */
        if ($('#list-reviews').length > 0) {
            //console.log("test");
            /**
             * review block control
             */
            ReviewItem = AE.Views.PostItem.extend({
                template: _.template($('#de-review-item').html()),
                className: 'col-xs-12',
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
                    if(parseInt(ae_globals.geolocation) == 1) {
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
                itemClass: 'col-xs-12'
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
        } //review list control
        if ($('.list-user-page-info').length > 0) {
            /**
             * review block control
             */
            UserListItem = AE.Views.PostItem.extend({
                tagName: 'li',
                className: 'user-list-item',
                template: _.template($('#de-user-item').html()),
                onItemBeforeRender: function() {
                    // before render view
                },
                onItemRendered: function() {
                    // after render view
                }
            });
            ListUsers = AE.Views.ListPost.extend({
                tagName: 'ul',
                itemView: UserListItem,
                itemClass: 'user-list-item'
            });
            if ($('.list-user-page-info').length > 0) {
                $('.list-user-page-info').each(function() {
                    if ($(this).find('.userdata').length > 0) {
                        var userdata = JSON.parse($(this).find('.userdata').html()),
                            collection = new AE.Collections.Users(userdata);
                    } else {
                        collection = new AE.Collections.Users();
                    }
                    new ListUsers({
                        el: $(this),
                        collection: collection,
                        itemView: UserListItem,
                        itemClass: 'user-list-item'
                    });
                    /**
                     * init block control list blog
                     */
                    new AE.Views.BlockControl({
                        collection: collection,
                        el: $('body')
                    });
                });
            }
        } //review list control
        /**
         * block list control
         */
        if ($('#list-blog').length > 0) {
            /**
             * review block control
             */
            BlogItem = AE.Views.PostItem.extend({
                template: _.template($('#ae-post-loop').html()),
                className: 'news-wrapper',
                tagName: 'div'
            });
            ListBlog = AE.Views.ListPost.extend({
                tagName: 'div',
                itemView: BlogItem,
                itemClass: 'col-xs-12'
            });
            var collection = new AE.Collections.Blogs();
            new ListBlog({
                itemView: BlogItem,
                collection: collection,
                el: '#list-blog'
            });
            new DE_Mobile({
                collection: collection,
                el: '#list-news'
            });
        }
        /**
         * // blog list control
         */

        /**
         * block control events on mobile
         */
        DE_MobileEvents = Backbone.View.extend({
            events: {
                // ajax load more 
                'click a.load-more-post': 'loadMore'
            },
            initialize: function() {
                var view = this;
                _.bindAll(this, 'loadMore');
                if (this.$('.ae_query').length > 0) {
                    this.query = JSON.parse(this.$('.ae_query').html());
                    this.query.paged = 1;
                    if ($(window).scrollTop() == $(document).height() - $(window).height()) {
                        view.loadMore();
                    }
                    var inviewid = view.$('.inview').attr('id');
                    $('#' + inviewid).bind('inview', function(event, isVisible) {
                        if (!isVisible || view.scrolled) {
                            this.inViewVisible = false;
                            return;
                        }
                        view.loadMore();
                    });
                } else {
                    this.$('.paginations').remove();
                }
                this.blockUi = new AE.Views.BlockUi();
            },
            /**
             * load more places
             */
            loadMore: function(e) {
                var view = this;
                view.page = view.query.paged;
                view.page++;
                view.query.paged++;
                // collection fetch
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'get',
                    data: {
                        query: view.query,
                        page: view.page,
                        action: 'de-mobile-fetch-events',
                        paginate: true
                    },
                    beforeSend: function() {
                        view.blockUi.block(view.$('.inview'));
                        view.query.paged++;
                    },
                    success: function(res) {
                        view.blockUi.unblock();
                        if (res.success) {
                            $('#list-events').append(res.data);
                            view.$('.rate-it').raty({
                                half: true,
                                score: function() {
                                    return $(this).attr('data-score');
                                },
                                readOnly: true,
                                hints: raty.hint
                            });
                        } else {
                            view.$('.inview').hide();
                        }
                    }
                });
            }
        });

        new AE.Views.CarouselComment({
            el: $('#comment_gallery_container'),
            model: '',
            name_item: 'et_carousel_comment',
            uploaderID :  'carousel_comment',
        });
        SinglePost = Backbone.Marionette.View.extend({
            el: 'body.single',
            events: {
                'click a.favorite': 'favorite',
                'click a.loved': 'removeFavorite',
                'click a.report': 'openReportModal',
            },
            initialize: function(options) {
                view = this,
                view.blockUi = new AE.Views.BlockUi();
            },
            openReportModal: function(event) {
            event.preventDefault();
            var $target = $(event.currentTarget),
                view = this;
            if (typeof this.Reportmodal === 'undefined') {
                this.Reportmodal = new ReportModal({
                    el: $("#report"),
                    place_id: $target.attr('data-id'),
                    user_id : $target.attr('data-user'),
                    model: view.model
                });
            }
            this.Reportmodal.place_id = $target.attr('data-id');
            this.Reportmodal.user_id  = $target.attr('data-user');
            this.Reportmodal.openModal();
            },
            favorite: function(event) {
                event.preventDefault();
                var $target = $(event.currentTarget),
                view = this,
                favorite = new AE.Models.Favorite({
                    comment_post_ID: $target.attr('data-id'),
                    sync: 'add'
                });              
                favorite.save('sync', 'add', {
                beforeSend: function() {
                     view.blockUi.block($target);
                },
                success: function(result, res, xhr) {
                    view.blockUi.unblock();
                        if(res.success === true) {
                            //$target.closest('li').attr('data-original-title', res.text);
                            $target.addClass('loved').removeClass('favorite');
                            //$target.attr('data-favorite-id', res.data);
                            $target.html('Remove Favorite');
                            $('.place-settings i').removeClass('fa-heart').addClass('fa-times');
                            $target.attr('data-favorite-id', res.data);
                        }
                        else{
                            AE.pubsub.trigger('ae:notification',{msg:res.msg,notice_type: 'error'});
                        }
                    }
                });
            },
            removeFavorite: function(event) {
            event.preventDefault();
            var $target = $(event.currentTarget),
                view = this,
                favorite = new AE.Models.Favorite({
                    id: $target.attr('data-favorite-id'),
                    ID: $target.attr('data-favorite-id'),
                    sync: 'remove'
                });
            favorite.save('sync', 'remove', {
                beforeSend: function() {
                    view.blockUi.block($target);
                },
                success: function(result, res, xhr) {
                    view.blockUi.unblock();
                    if(res.success === true) {
                        //$target.closest('li').attr('data-original-title', res.text);
                        $target.addClass('favorite').removeClass('loved');
                        $target.html('Add to favorite');
                         $('.place-settings i').removeClass('fa-times').addClass('fa-heart');
                    }
                    else{
                        AE.pubsub.trigger('ae:notification',{msg:res.msg,notice_type: 'error'});
                    }
                }
            });
        },
        });
        Report = Backbone.Model.extend({
                action: 'ae-sync-report',
                initialize: function() {}   
            });
        ReportModal = AE.Views.Modal_Box.extend({
                events : {
                    'submit form#submit_report': 'submitReport',
                },
                initialize: function() {
                    AE.Views.Modal_Box.prototype.initialize.call();
                    this.blockUi = new AE.Views.BlockUi();
                    this.initValidator();
                },
                initValidator: function() {
                    /**
                     * post form validate
                     */
                    $("form#submit_report").validate({
                        ignore: "",
                        rules: {
                            message: "required",
                        },
                        errorPlacement: function(label, element) {
                            // position error label after generated textarea
                            if (element.is("textarea")) {
                                label.insertAfter(element.next());
                            } else {
                                $(element).closest('div').append(label);
                            }
                        }
                    });
                },        
                submitReport: function(event){
                    event.preventDefault();
                    event.stopPropagation();

                    var $form = $(event.currentTarget),
                    button    = $form.find('.btn-submit'),
                    textarea  = $form.find('textarea'),
                    view      = this,
                    report    = new Report({
                        comment_post_ID: view.place_id,
                        comment_content: textarea.val(),
                        user_report: view.user_id,
                        sync: 'add'
                    });
                    report.save('sync', 'add', {
                        beforeSend: function() {
                            view.blockUi.block(button);
                        },
                        success: function(result, res, xhr) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success'
                            });
                            //reset form
                            $form[0].reset();
                            //remove button                         
                            if(res.success){
                                 $('a#report_'+view.place_id).remove();
                                $( ".place-report i" ).after( "<a>Reported</a>" );
                            }
                            //unblock button                  
                            view.blockUi.unblock();
                            //close modal
                            view.closeModal();
                        }
                    });
                },        
        });
        new SinglePost({model : ""});
        new DE_MobileEvents({
            el: $('#events-list-wrapper')
        });
        // block control mobile
        $('#search-nearby').click(function(event) {
            navigator.geolocation.getCurrentPosition(searchNearby, errorHandle);
        });

        function searchNearby(position) {
            var coords = position.coords
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
        $('#submit-comment').submit(function(){
            var post_status = $('#post_status').val();
            if(post_status == 'pending'){
                AE.pubsub.trigger('ae:notification',{msg:de_front.texts.submit_pending_error,notice_type: 'error'});
                return false;
            }
        });
    });
})(jQuery);