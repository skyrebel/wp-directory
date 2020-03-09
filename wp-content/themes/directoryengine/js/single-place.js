(function($, Views, Models, Collections) {
    $(document).ready(function() {
        $('.not_is_tablet #main-single .pinned-custom').pin({
            containerSelector: "#main-single",
            padding: {
                top: 100,
                bottom: 120
            }
        });
        Collections.Events = Backbone.Collection.extend({
            model: AE.Models.Event,
            action: 'ae-fetch-events',
            initialize: function() {
                this.paged = 1;
            }
        });
        EventItem = Views.PostItem.extend({
            template: _.template($('#de-event-item').html()),
            // class name define column 
            className: 'event-wrapper event-item',
            onItemRendered: function() {
            }
        });
        ListEvent = Views.ListPost.extend({
            tagName: 'div',
            itemView: EventItem,
            itemClass: 'event-item'
        });
        if ($('#block-events .postdata').length > 0) {
            var postdata = JSON.parse($('#block-events').find('.postdata').html()),
                collection = new Collections.Events(postdata);
            new ListEvent({
                el: $('#list-events'),
                collection: collection
            });
        }

        

        /*Remove captcha from post review to post comment*/
        $('.comment-reply-link').click(function(){
            $('.g-recaptcha').prependTo('#comment-captcha');   
        });
        /*Remove captcha from post comment to post review*/
        $('#cancel-comment-reply-link').click(function(){
            $('.g-recaptcha').prependTo('.gg-captcha');
        });
    });

    /**
     * control single place view
     */
    Views.SinglePost = Backbone.Marionette.View.extend({
        el: 'body.single',
        events: {
            // user click on action button such as edit, archive, reject
            'click a.place-action': 'acting',
            // slide to review section
            'click a.write-review': 'slideToReview',
            'click a.sroll-review': 'jumbToReview',
            // add to favorite
            'click a.favorite': 'favorite',
            // add to favorite
            'click a.loved': 'removeFavorite',
            // send report to admins
            'click a.report': 'openReportModal',
            //claim a place
            'click a.claim-place': 'openClaimModal',
            // trigger when user load next post
            'click a.load-more-post#post-inview': 'loadNextPost' ,

            'mouseover  .not_is_tablet .list-option-left .share-social' : 'showShare',
            'mouseleave  .not_is_tablet .list-option-left .share-social' : 'hideShare',
            'click  .is_tablet .list-option-left .share-social' : 'toggleShare',
            // Redirect to place and go to #review
            'click .review_place' : 'reviewPlace',
            'submit form#submit-comment': 'submitComment',
        },
        /**
         * Open Report Modal
         */
        openReportModal: function(event) {
            event.preventDefault();
            var $target = $(event.currentTarget),
                view = this;
            if (typeof this.Reportmodal === 'undefined') {
                this.Reportmodal = new Views.ReportModal({
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
        /**
         * Open Claim Modal
         */        
        openClaimModal: function(event) {
            event.preventDefault();
            var $target = $(event.currentTarget),
                view = this;
            if(typeof this.claimModal === 'undefined' ){
                this.claimModal = new Views.ClaimModal({
                    el: $("#claim_modal"),
                    place_id : $target.attr('data-id'),
                    user_id  : $target.attr('data-user'),
                    model : JSON.parse($('.place_id_' + $target.attr('data-id')).html()),
                });
            }
            this.claimModal.model = JSON.parse($('.place_id_' + $target.attr('data-id')).html());
            this.claimModal.place_id = $target.attr('data-id');
            this.claimModal.user_id  = $target.attr('data-user');    
            this.claimModal.initialize();
            this.claimModal.openModal();
        },               
        showShare : function (event) {
            event.preventDefault();
            var $target = $(event.currentTarget);
            $target.find('.list-share-social').addClass('active');
        }, 
        hideShare : function (event) {
            event.preventDefault();
            var $target = $(event.currentTarget);
            $target.find('.list-share-social').removeClass('active');
        },
        toggleShare : function (event) {
            event.preventDefault();
            var $target = $(event.currentTarget);
            $target.find('.list-share-social').toggleClass('active').toggle();
        },  

        initialize: function(options) {
            var view = this;
            //count related
            view.k = 0;
            // bind all external event
            _.bindAll(this, 'updateUrl');
            this.options = _.extend(options, this.options);
            //AE.pubsub.on('ae:after:editPost', 'editPostSucess');
            $('.tooltip-style').tooltip();

            //Get related place
            this.related_place = {};
            if($('#json_related_place').length > 0) {
                this.related_place = jQuery.parseJSON($('#json_related_place').html());
            }
            //If not related place remove button load more
            if(Object.keys(this.related_place).length == 0) {
                $('#single-place .paginations #post-inview').remove();
            }

            view.model.set('id', view.model.get('ID'));
            view.model.set('ID', view.model.get('ID'));
            view.model.set('pageTitle', view.model.get('post_title'));
            view.model.set('link', view.model.get('permalink'));

            view.blockUi = new Views.BlockUi();
            view.collection = new Collections.Posts();
            view.collection.add(view.model);
            $('.popup-video').magnificPopup({
                type: 'iframe'
            });

            view.carouselComment = new Views.CarouselComment({
                el: $('#comment_gallery_container'),
                model: '',
                name_item: 'et_carousel_comment',
                uploaderID :  'carousel_comment',
            });

            //Call event back button
            view.back_button_browser();

        },

        /**
         * listing event back button browser
         */

        back_button_browser: function() {
            var view = this;
            var viewed_places = $.cookie('viewed_places');
            if(viewed_places != '' && typeof viewed_places != 'undefined') {
                var result = viewed_places.split('|');
                var total = result.length;
                var id_place_back = result[total-2];
                window.addEventListener("popstate", function(e) {
                    $.ajax({
                        type: 'post',
                        url: ae_globals.ajaxURL,
                        data: {
                            action: 'button_go_back_place',
                            id_place: id_place_back
                        },
                        beforeSend: function() {
                            view.blockUi.block($('body'));
                        },
                        success: function(res) {
                            view.blockUi.unblock();
                            if(res.status == 'error') {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: 'don\'t back place',
                                    notice_type: 'error',
                                });
                            } else {
                                window.location.href = res.permalink;
                            }
                        }
                    });
                });
            }
        },


        /**
         * event callback when user click on action button
         * edit
         * archive
         * reject
         * toggleFeatured
         * approve
         */
        acting: function(e) {
            // e.preventDefault();
            var target = $(e.currentTarget),
                action = target.attr('data-action'),
                id = target.parents('.single-place-wrapper').attr('data-id'),
                model = this.collection.get(id),
                view = this;
            // fetch model data
            switch (action) {
                case 'create_event':
                    //trigger an event will be catch by AE.App to open modal edit
                    $.ajax({
                        type: 'get',
                        url: ae_globals.ajaxURL,
                        data: {
                            action: 'ae-check-event',
                            post_parent: model.get('ID')
                        },
                        beforeSend: function() {
                            view.blockUi.block(target.parents('.dropdown'));
                        },
                        success: function(res) {
                            view.blockUi.unblock();
                            if (res.success) {
                                AE.pubsub.trigger('ae:model:onCreateEvent', model);
                            } else {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'error',
                                });
                            }
                        }
                    });
                    break;
                case 'edit':
                    //trigger an event will be catch by AE.App to open modal edit
                    AE.pubsub.trigger('ae:model:onEdit', model);
                    break;
                case 'reject':
                    //trigger an event will be catch by AE.App to open modal reject
                    AE.pubsub.trigger('ae:model:onReject', model);
                    break;
                case 'archive':
                    // archive a model
                    //model.set('do', 'archivePlace');
                    if (confirm(ae_globals.confirm_message)) {
                        model.save('post_status', 'archive', {
                            beforeSend: function() {
                                view.blockUi.block(target.parents('.dropdown'));
                            },
                            success: function(result, status) {
                                view.blockUi.unblock();
                                if (status.success) {
                                    AE.pubsub.trigger('ae:notification', {
                                        msg: status.msg,
                                        notice_type: 'success',
                                    });
                                    window.location.reload();
                                } else {
                                    AE.pubsub.trigger('ae:notification', {
                                        msg: status.msg,
                                        notice_type: 'error',
                                    });
                                }
                            }
                        });
                    }
                    break;
                case 'toggleFeature':
                    // toggle featured
                    //model.set('do', 'toggleFeature');
                    if (parseInt(model.get('et_featured')) === 1) {
                        model.set('et_featured', 0);
                    } else {
                        model.set('et_featured', 1);
                    }
                    model.save('', '', {
                        beforeSend: function() {
                            view.blockUi.block(target.parents('.dropdown'));
                        },
                        success: function(result, status) {
                            view.blockUi.unblock();
                            if (status.success) {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: status.msg,
                                    notice_type: 'success',
                                });
                                window.location.reload();
                            } else {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: status.msg,
                                    notice_type: 'error',
                                });
                            }
                        }
                    });
                    break;
                case 'approve':
                    // publish a model
                    model.save('publish', '1', {
                        beforeSend: function() {
                            view.blockUi.block(target.parents('.dropdown'));
                        },
                        success: function(result, status) {
                            view.blockUi.unblock();
                            if (status.success) {
                                window.location.href = model.get('permalink');
                            }
                        }
                    });
                    break;
                default:
                    break;
            }
        },
        // slide to review
        slideToReview: function(event) {
            //prevent the default action for the click event
            event.preventDefault();
            var target = $(event.currentTarget);
            if(target.attr('data-href') != '') {
                window.history.pushState('','',target.attr('data-href'));
                location.reload();
            }
            else {
                //get the top offset of the target anchor
                if( ae_globals.user_ID === '0'){
                    var target_offset = $(".ae-comment-reply-title").offset();
                }
                else{
                    var target_offset = $("#review").offset();
                }
                var target_top = target_offset.top;
                //goto that anchor by setting the body scroll top to anchor top
                $('html, body').animate({
                    scrollTop: target_top - 200
                }, 500, 'easeOutExpo');
            }
        },
        jumbToReview: function(event) {
            //prevent the default action for the click event
            event.preventDefault();
            //get the top offset of the target anchor
            var target_offset = $("#review-list").offset();
            var target_top = target_offset.top;
            //goto that anchor by setting the body scroll top to anchor top
            $('html, body').animate({
                scrollTop: target_top - 200
            }, 500, 'easeOutExpo');
        },
        /**
         * add place to favorite list ( togos list )
         */
        favorite: function(event) {
            event.preventDefault();
            var $target = $(event.currentTarget),
                view = this,
                favorite = new Models.Favorite({
                    comment_post_ID: $target.attr('data-id'),
                    sync: 'add'
                });
            console.log(favorite);
            favorite.save('sync', 'add', {
                beforeSend: function() {
                    view.blockUi.block($target);
                },
                success: function(result, res, xhr) {
                    view.blockUi.unblock();
                    if(res.success === true) {
                        $target.closest('li').attr('data-original-title', res.text);
                        $target.addClass('loved').removeClass('favorite');
                        $target.attr('data-favorite-id', res.data);
                    }
                    else{
                        AE.pubsub.trigger('ae:notification',{msg:res.msg,notice_type: 'error'});
                    }
                }
            });
        },
        /**
         * add place to favorite list ( togos list )
         */
        removeFavorite: function(event) {
            event.preventDefault();
            var $target = $(event.currentTarget),
                view = this,
                favorite = new Models.Favorite({
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
                        $target.closest('li').attr('data-original-title', res.text);
                        $target.addClass('favorite').removeClass('loved');
                    }
                    else{
                        AE.pubsub.trigger('ae:notification',{msg:res.msg,notice_type: 'error'});
                    }
                }
            });
        },
        // after edit post reload
        editPostSucess: function(model) {
            if (res.success) {
                window.location.reload();
            }
        },
        /**
         * load next post
         */
        loadNextPost: function(event) {
            event.preventDefault();
            this.next_post = this.related_place[this.k];
            this.k++;
            var model = this.next_post,
                $target = $(event.currentTarget),
                view = this;

            $.ajax({
                url: ae_globals.ajaxURL,
                type: 'get',
                data: {
                    action: 'de-next-place',
                    id: model,
                    current: view.model.get('id'),
                },
                beforeSend: function() {
                    view.blockUi.block($target);
                },
                success: function(res) {
                    view.blockUi.unblock();
                    if (res.success) {
                        // append content to view
                        $('#single-more-place').append(res.content);
                        //Hide button load more if related last
                        
                        if(Object.keys(view.related_place).length == view.k) {
                            $target.remove();
                        }
                        // Code that will load the dynamic content
                        // Once that's all done, call addthis.toolbox()
                        // addthis.toolbox('.addthis_toolbox');
                        // bind popup gallery to place images
                        $('#single-place-' + res.post_id).find('.fancybox').magnificPopup({
                            type: 'image',
                            gallery: {
                                enabled: true
                            },
                            // other options
                        });
                        $('.not_is_tablet #single-place-' + res.post_id).find(".list-option-left").pin({
                            containerSelector: '#single-place-' + res.post_id,
                            padding: {
                                top: 100, 
                                bottom : 120
                            }
                        });

                        /**
                         * create model place to control data
                         */
                        var model = new Models.Post({
                            id: res.post_id,
                            ID: res.post_id,
                            pageTitle: res.pageTitle,
                            link: res.link
                        });
                        // update document title and link
                        document.title = res.pageTitle;

                        // update url 
                        // window.history.pushState({
                        //     "html": res.content,
                        //     "pageTitle": res.pageTitle
                        // }, "", res.link);
                        // add model place to single place collection
                        view.collection.add(model);
                        // bind inview to change title, url when user scroll to place
                        view.bindInview();
                        // fetch model data
                        model.fetch();
                        // update rating score
                        $('.rate-it').raty({
                            readOnly: true,
                            half: true,
                            score: function() {
                                return $(this).attr('data-score');
                            },
                            hints: raty.hint
                        });

                        $('.multi-rate-it').raty({
                            readOnly: true,
                            half: true,
                            score: function() {
                                return $(this).attr('data-score');
                            },
                            hints: raty.hint
                        });
                        
                        // Render Distance
                        var location_lat, location_lng;
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                // Get GeoLocation of device
                                location_lat = position.coords.latitude;
                                location_lng =  position.coords.longitude;

                                var $itemSinglePlace =  $('#single-place-' + res.post_id),
                                    lat_item = $itemSinglePlace.find('#latitude').attr('content'),
                                    lng_item = $itemSinglePlace.find('#longitude').attr('content');
                                var dist = distance(lat_item, lng_item, location_lat, location_lng);
                                $itemSinglePlace.find('.distance').text(dist + ' -');

                            });
                        }
                    } else {
                        $target.remove();
                    }
                }
            });
        },
        // bind inview to place item to update url
        bindInview: function() {
            var view = this;
            this.$('.single-place-wrapper').bind('inview', view.updateUrl);
        },
        // update browser url when scroll to an item in group
        updateUrl: function(event, isVisible) {
            var view = this;
            if (!isVisible) {
                this.inViewVisible = false;
                return;
            }
            var $target = $(event.currentTarget),
                id = $target.attr('data-id'),
                model = this.collection.get(parseInt(id));
            if(model.get('post_title')){               
                document.title = model.get('post_title');
            }else{
                document.title = model.get('pageTitle');
            }

            history.pushState({
                "html": $target.html(),
                "pageTitle": model.get('pageTitle'),
                "link" : model.get('link')
            }, "single_place_load_more", model.get('link'));
        },

        /**
         * Submit comment/review
         */
        submitComment: function(){
            var post_status = $('#post_status').val();
            if(post_status == 'pending'){
                AE.pubsub.trigger('ae:notification',{msg:de_front.texts.submit_pending_error,notice_type: 'error'});
                return false;
            }
        }
    });
    /*
    *
    * Claim Modal Views
    * @place
    *
    */
    Views.ClaimModal = Views.Modal_Box.extend({
        events : {
            'submit form#submit_claim': 'submitClaim',
            'click a.deny-claim': 'denyClaim'
        },
        initialize: function() {
            AE.Views.Modal_Box.prototype.initialize.call();
            this.blockUi = new Views.BlockUi();
            this.initValidator();
            if(this.model.et_claim_info !== null && this.model.et_claim_info !== '' && this.model.et_claim_info.length !== 0){
                this.fillForm();
            }
            else{
                $('#claim_user_request').val($('#current_user_id').val());
            }
        },
        fillForm: function() {
            var claim_info = this.model.et_claim_info;
            $('#claim_user_request').val(claim_info.user_request);
            $('#display_name').val(claim_info.display_name);
            $('#location').val(claim_info.location);
            $('#phone').val(claim_info.phone);
            $('textarea#message').val(claim_info.message);
        },
        initValidator: function() {
            /**
             * post form validate
             */
            $("form#submit_claim").validate({
                ignore: "",
                rules: {
                    display_name: "required",
                    location: "required",
                    phone: {
                        required: true,
                        number: true
                    },
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
        denyClaim: function(event){
            event.preventDefault();
            var btn = $(event.currentTarget);
            $("input#claim_action").val('deny');
            this.deny = true;
            this.denyBtn = btn;
            $('.trigger-claim').trigger('click');
        },   
        submitClaim: function(event){
            event.preventDefault();
            event.stopPropagation();

            var $form = $(event.currentTarget),
            button    = $form.find('.btn-submit'),
            textarea  = $form.find('textarea'),
            content   = $form.serializeObject(),
            view      = this;

            content.place_id = this.place_id;

            $.ajax({
                url: ae_globals.ajaxURL,
                type: 'POST',
                data: {
                    action: 'ae_claim_place',
                    content: content
                },
                beforeSend: function() {
                    if(view.deny == true){
                        view.blockUi.block(view.denyBtn);
                    }
                    else{
                        view.blockUi.block(button);
                    }
                },
                success: function(res) {
                    if(res.success)
                        notice_type = "success";
                    else
                        notice_type = "error";
                    AE.pubsub.trigger('ae:notification', {
                        msg: res.msg,
                        notice_type: notice_type
                    });
                    //reset form
                    $form[0].reset();
                    //unblock button
                    view.blockUi.unblock();
                    //close modal
                    view.closeModal();
                    //reload
                    window.location.reload();
                }
            });
        },
    });
    /*
    *
    * Report Modal Views
    * @place
    *
    */
    Views.ReportModal = Views.Modal_Box.extend({
        events : {
            'submit form#submit_report': 'submitReport',
        },
        initialize: function() {
            AE.Views.Modal_Box.prototype.initialize.call();
            this.blockUi = new Views.BlockUi();
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
            report    = new Models.Report({
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
                    $('a#report_'+view.place_id).remove();
                    //unblock button                  
                    view.blockUi.unblock();
                    //close modal
                    view.closeModal();
                }
            });
        },        
    });
    /**
     * model favorite
     */
    Models.Favorite = Backbone.Model.extend({
        action: 'ae-sync-favorite',
        initialize: function() {}
    });
    /**
     * model report
     */
    Models.Report = Backbone.Model.extend({
        action: 'ae-sync-report',
        initialize: function() {}   
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

    if($('body.single').length > 0){
        var location_lat, location_lng;
        if (parseInt(ae_globals.geolocation)) {
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
})(jQuery, AE.Views, AE.Models, AE.Collections);