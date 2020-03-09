(function(Views, Models, Collections, $, Backbone) {
    $(document).ready(function(){
        /**
         * review block control
         */
        var AuthorReviewItem = Views.PostItem.extend({
            template: _.template($('#de-review-item').html()),
            // class name define column 
            className: 'col-md-3 col-sm-6 review-item',
            onItemRendered: function() {
                var view = this;
                this.$('.rate-it').raty({
                    half: true,
                    score: view.model.get('rating_score_comment'),
                    readOnly: true,
                    hints: raty.hint
                });
            }
        });
        ListReview = Views.ListPost.extend({
            tagName: 'ul',
            itemView: AuthorReviewItem,
            itemClass: 'review-item'
        });

        $('.author-comment-block').each(function() {
            if ($(this).find('.postdata').length > 0) {
                var postdata = JSON.parse($(this).find('.postdata').html()),
                    collection = new Collections.Comments(postdata);
                // set action to 'ae-fetch-comments'
                collection.action = 'ae-fetch-comments';
                new ListReview({
                    itemView: AuthorReviewItem,
                    collection: collection,
                    el: $(this).find('ul')
                });
                new Views.BlockControl({
                    collection: collection,
                    el: $(this)
                });
            }
        });

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
            });
            if (this.submit_validator.form() && !form.hasClass("processing")) {
                this.user.set('do', 'profile');
                this.user.request('update', {
                    beforeSend: function() {
                        view.blockUi.block($button);
                        form.addClass('processing');
                    },
                    success: function(result, status, jqXHR) {
                        form.removeClass('processing');
                        if (status.success) {
                            //render data
                            var ul = $("ul#author_info"),
                                location = ul.find('li.location span'),
                                phone = ul.find('li.phone span'),
                                facebook = ul.find('li.facebook span a'),
                                display_name = $('.name-author');
                            display_name.text(result.get('display_name'));
                            location.text(result.get('location'));
                            phone.text(result.get('phone'));
                            if (result.get('facebook') !== "") {
                                facebook.text(result.get('facebook')).attr('href', result.get('facebook'));
                            } else {
                                ul.find('li.facebook span').html('<a href="#">No facebook</a>');
                            }
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'success',
                            });
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
                        //console.log('chay');
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
        el: 'body.author',
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
                                $('#user_avatar_container .moxie-shim').css({'width': '135px', 'height': '135px'});
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

                if( iPad > 0){ 
                    setTimeout(function(){
                        $('#user_avatar_container .moxie-shim').css({'width':'135px','height':'135px'});
                    },1000);
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