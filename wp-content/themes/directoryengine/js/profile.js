(function(Views, Models, Collections, $, Backbone) {
    $(document).ready(function(){
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

        // PLACE REVIEW ITEM
        PlaceReviewItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'review-item col-lg-3 col-md-4 col-sm-4',
            template: _.template($('#ae-review-loop').html()),
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                // after render view
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
                            location_lng = position.coords.longitude;

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

        ListPlaceReviewProfile = Views.ListPost.extend({
            tagName: 'ul',
            itemView: PlaceReviewItem,
            itemClass: 'review-item'
        });


        $('.content-reviews').each(function() {
            if ($(this).find('.postdata').length > 0) {
                var postdata = JSON.parse($(this).find('.postdata').html()),
                    collection = new Collections.Comments(postdata);
                // set action to 'ae-fetch-comments'
                collection.action = 'ae-fetch-comments';
                new ListPlaceReviewProfile({
                    itemView: PlaceReviewItem,
                    collection: collection,
                    el: $(this).find('ul')
                });
                new Views.BlockControl({
                    collection: collection,
                    el: $(this)
                });
            }
        });
        // PLACE REVIEW ITEM

        // PLACE PLACE ITEM
        PlaceProfileItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'place-item',
            template: _.template($('#ae-place-loop').html()),
            modelEvents: {
                "change": "modelChanged",
                "change:post_status": "statusChange"
            },
            modelChanged : function(model){
                var view = this,
                    $container = this.$('div.wrap-place-publishing'),
                    $container_reject = this.$('div.wrap-place-reason');

                // Change content on Block Place-Item (Status: Publish, pending, archive, draft)
                $container.find('.place-publishing-title a').text(model.get('post_title'));
                $container.find('.place-publishing-img img').attr('src',model.get('the_post_thumnail'));
                $container.find('.place-publishing-map span.location').text(model.get('et_full_location'));

                // Change content on Block Place-Item (Status: Reject)
                $container_reject.find('.place-publishing-title a').text(model.get('post_title'));
                $container_reject.find('.place-publishing-img img').attr('src',model.get('the_post_thumnail'));
                $container_reject.find('.place-publishing-map span.location').text(model.get('et_full_location'));
            },
            statusChange: function(model) {
                if(model.changed.post_status === 'archive' || model.changed.post_status === 'trash'){
                    $('.list-place-tabs li').each(function () {
                        if($(this).hasClass('active')){
                            $('a',this).click();
                            return;
                        }
                    });
                }
            },
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                // after render view
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

        ListPlaceProfile = Views.ListPost.extend({
            tagName: 'ul',
            itemView: PlaceProfileItem,
            itemClass: 'place-item'
        });

        if( $('.content-place').length > 0 ){
            $('.content-place').each(function() {
                if( $(this).find('.postdata').length > 0 ){
                    var postdata   = JSON.parse($(this).find('.postdata').html()),
                        collection = new Collections.Posts(postdata);
                } else {
                    collection = new Collections.Posts();
                }
                new ListPlaceProfile({
                    el : $(this).find('.list-place-publishing'),   
                    collection : collection ,
                    itemView: PlaceProfileItem,
                    itemClass: 'place-item '
                });

                /**
                 * init block control list blog
                 */
                new Views.BlockControl({
                    collection: collection,
                    el: $(this),
                    onAfterFetch : function( result, res){
                        // Update result 
                        $('.list-place-tabs li a.click-type').each(function () {
                            var type = $(this).data('type');
                            $('span',this).text('(' + res.total_status[type] + ')');
                            $(this).parent().removeClass('active');
                            if(res.type_status == type){
                                $(this).parent().addClass('active');
                            }
                        });
                        if(res.total)
                        {
                           $('#tab-place-publishing .event-active-wrapper').hide();
                        }

                        // Update Chosen-single
                        $('.chosen-single').trigger('chosen:updated');
                    },
                    switchTo: function() {
                        // if (this.$('.list-option-filter').length == 0) return;
                        return;
                    }
                });
            });
        }
        // PLACE PLACE ITEM

        // PLACE TOGOS ITEM
        PlaceTogoItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'togo-item col-lg-3 col-md-4 col-sm-4',
            template: _.template($('#ae-togo-loop').html()),
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                // after render view
                var view = this;
                var post_data = view.model.get('post_data');
                view.$('.rate-it').raty({
                    half: true,
                    score: post_data.rating_score_comment,
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

        ListPlaceTogoProfile = Views.ListPost.extend({
            tagName: 'ul',
            itemView: PlaceTogoItem,
            itemClass: 'togo-item'
        });

        $('.content-togo').each(function() {
            if ($(this).find('.postdata').length > 0) {
                var postdata = JSON.parse($(this).find('.postdata').html()),
                    collection = new Collections.Comments(postdata);
                // set action to 'ae-fetch-comments'
                collection.action = 'ae-fetch-favorite';

                new ListPlaceTogoProfile({
                    itemView: PlaceTogoItem,
                    collection: collection,
                    el: $(this).find('ul')
                });
                new Views.BlockControl({
                    collection: collection,
                    el: $(this)
                });
            }
        });
        // PLACE TOGOS ITEM

        // PLACE PICTURE ITEM
        AE.Models.Picture = Backbone.Model.extend({});

        AE.Collections.Pictures = Backbone.Collection.extend({
            model: AE.Models.Picture,
            action: 'ae-fetch-pictures',
            initialize: function() {
                this.paged = 1;
            }
        });

        PlacePictureItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'picture-item col-md-3 col-sm-4 col-xs-6',
            template: _.template($('#ae-picture-loop').html()),
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                this.$('.fancybox').magnificPopup({
                    type: 'image',
                    gallery: {
                        enabled: true
                    }
                    // other options
                });
            }
        });

        ListPlacePictureProfile = Views.ListPost.extend({
            tagName: 'ul',
            itemView: PlacePictureItem,
            itemClass: 'picture-item'
        });

        $('.content-picture').each(function() {
            if ($(this).find('.postdata').length > 0) {
                var postdata = JSON.parse($(this).find('.postdata').html()),
                    collection = new Collections.Pictures(postdata);
                new ListPlacePictureProfile({
                    itemView: PlacePictureItem,
                    collection: collection,
                    el: $(this).find('ul')
                });
                new Views.BlockControl({
                    collection: collection,
                    el: $(this)
                });
            }
        });
        // PLACE PICTURE ITEM


        // PLACE EVENT ITEM

        Collections.Events = Backbone.Collection.extend({
            model: AE.Models.Event,
            action: 'ae-fetch-events',
            initialize: function() {
                this.paged = 1;
            }
        });

        EventItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'event-item col-md-12',
            template: _.template($('#ae-event-loop').html()),
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                // after render view
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

        ListEventProfile = Views.ListPost.extend({
            tagName: 'ul',
            itemView: EventItem,
            itemClass: 'event-item'
        });

        $('.content-events').each(function() {
            if ($(this).find('.postdata').length > 0) {
                var postdata = JSON.parse($(this).find('.postdata').html()),
                    collection = new Collections.Events(postdata);

                new ListEventProfile({
                    itemView: EventItem,
                    collection: collection,
                    el: $(this).find('ul')
                });
                new Views.BlockControl({
                    collection: collection,
                    el: $(this)
                });
            }
        });
        // PLACE EVENT ITEM
    });

    /**
     * modal edit profile
     */
    Views.EditProfileModal = AE.Views.Modal_Box.extend({
        events: {
            'submit form#submit_edit_profile': 'saveProfile',
        },
        initialize: function() {
            AE.Views.Modal_Box.prototype.initialize.call();
            this.blockUi = new AE.Views.BlockUi();
            this.user = this.model;
        },
        resetUploader: function() {
            this.avatar_uploader.controller.splice();
            this.avatar_uploader.controller.refresh();
            this.avatar_uploader.controller.destroy();
        },
        saveProfile: function(event) {
            event.preventDefault();
            this.submit_validator = $("form#submit_edit_profile").validate({
                rules: {
                    display_name: "required",
                    user_location: "required",
                    facebook: {
                        url: true
                    },
                }
            });
            var form = $(event.currentTarget),
                $button = form.find("input.btn-submit"),
                data = form.serializeObject(),
                view = this;
            /**
             * scan all fields in form and set the value to model user
             */
            form.find('input, textarea, select').each(function() {
                view.user.set($(this).attr('name'), $(this).val());
            })
            if (this.submit_validator.form() && !form.hasClass("processing")) {
                this.user.set('do', 'profile');
                this.user.request('update', {
                    beforeSend: function() {
                        view.blockUi.block($button);
                        form.addClass('processing');
                        $('.btn-submit.update_profile').val(de_front.texts.updating);
                    },
                    success: function(result, status, jqXHR) {
                        form.removeClass('processing');
                        if (status.success) {
                            //render data
                            var div = $(".content-info"),
                                display_name = div.find('.username h4'),
                                location = div.find('.detail-info .location span'),
                                phone = div.find('.detail-info .phone span'),
                                facebook = div.find('.detail-info .facebook span a');
                            display_name.text(result.get('display_name'));
                            if(result.get('location') !== "")
                                location.text(result.get('location'));
                            else
                                location.text(de_front.texts.earth);
                            if(result.get('phone') !== "")
                                phone.text(result.get('phone'));
                            else
                                phone.text(de_front.texts.no_phone);
                            if (result.get('facebook') !== "") {
                                facebook.text(result.get('facebook')).attr('href', result.get('facebook'));
                            }else{
                                div.find('.detail-info .facebook span').html('<a href="#">No facebook</a>');
                            }
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'success',
                            });
                            $('.btn-submit.update_profile').val(de_front.texts.update_profile);
                            view.closeModal();
                        } else {
                            alert(status.msg);
                            // AE.pubsub.trigger('ae:notification', {
                            //     msg: status.msg,
                            //     notice_type: 'error',
                            // });
                            // view.closeModal();
                        }
                        view.blockUi.unblock();
                    }
                });
            }
        },
        changePassword: function(event) {
            event.preventDefault();
            this.change_pass_validator = this.$("form#submit_edit_password").validate({
                rules: {
                    old_password: "required",
                    new_password: "required",
                    re_password: {
                        required: true,
                        equalTo: "#new_password1"
                    },
                }
            });
            var form = $(event.currentTarget),
                $button = form.find("input.btn-submit"),
                data = form.serializeObject(),
                view = this;
            if (this.change_pass_validator.form()) {
                this.user.set('content', data);
                this.user.save('do_action', 'changePassword', {
                    beforeSend: function() {
                        view.blockUi.block($button);
                    },
                    success: function(result, status, jqXHR) {
                        if (status.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'success',
                            });
                            window.location.href = status.redirect;
                        } else {
                            $('#edit_profile').modal('hide');
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'error',
                            });
                        }
                        view.blockUi.unblock();
                    }
                });
            }
        }
    });

    /**
     * front-end control
     */
    Views.Author = Backbone.View.extend({
        el: 'body.page-template-page-profile',
        model: [],
        events: {
            'click a.edit-profile': 'openEditProfileModal'
        },
        initialize: function() {
            var sAgent = window.navigator.userAgent,
                iPad = sAgent.indexOf('iPad');

            this.blockUi = new AE.Views.BlockUi();
            this.user = this.model;
            this.uploaderID = 'user_avatar';
            var $container = $("#user_avatar_container"),
                view = this;
            if (typeof this.avatar_uploader === "undefined") {
                this.avatar_uploader = new AE.Views.File_Uploader({
                    el: $container,
                    uploaderID: this.uploaderID,
                    thumbsize: 'thumbnail',
                    multipart_params: {
                        _ajax_nonce: $container.find('.et_ajaxnonce').attr('id'),
                        data: {
                            method: 'change_avatar',
                            author: view.user.get('ID')
                        },
                        imgType: this.uploaderID,
                    },
                    cbUploaded: function(up, file, res) {
                        if (res.success) {
                            $('#' + this.container).parents('.desc').find('.error').remove();
                        } else {
                            $('#' + this.container).parents('.desc').append('<div class="error">' + res.msg + '</div>');
                        }
                    },
                    beforeSend: function(ele) {
                        button = $(ele).find('.image');
                        view.blockUi.block(button);
                    },
                    success: function(res) {
                        if( iPad > 0) {
                            setTimeout(function () {
                                $('#user_avatar_container .moxie-shim').css({
                                    'width': '135px',
                                    'height': '135px',
                                    'top': '28px',
                                    'left': '28px'
                                });
                            }, 1000);
                        }
                        if (res.success === false) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error',
                            });
                        }
                        view.blockUi.unblock();
                    }
                });

                if( iPad > 0) {
                    setTimeout(function () {
                        $('#user_avatar_container .moxie-shim').css({
                            'width': '135px',
                            'height': '135px',
                            'top': '28px',
                            'left': '28px'
                        });
                    }, 1000);
                    this.avatar_uploader.controller.init();
                    this.avatar_uploader.controller.refresh();
                }
            }
        },
        /**
         * Open Edit Profile if current user is logged in
         */
        openEditProfileModal: function(event) {
            event.preventDefault();
            if (typeof this.editProfilemodal === 'undefined') {
                this.editProfilemodal = new Views.EditProfileModal({
                    el: $("#edit_profile"),
                    model: this.user
                });
            }
            this.editProfilemodal.openModal();
        }
    });

})(AE.Views, AE.Models, AE.Collections, jQuery, Backbone);