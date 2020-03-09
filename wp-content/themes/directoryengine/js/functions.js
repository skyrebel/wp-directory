(function($, AE, Views, Models, Collections, Backbone) {
    // the pub/sub object for managing event throughout the app
    AE.pubsub = AE.pubsub || {};
    _.extend(AE.pubsub, Backbone.Events);
    // create a shorthand for our pubsub
    var pubsub = pubsub || AE.pubsub;
    Models.Event = Backbone.Model.extend({
        action: 'ae-sync-event',
        initialize: function() {}
    });
    /**
     * authenitaction modal box handle user login, register
     */
    Views.AuthModal = Views.Modal_Box.extend({
        events: {
            // user login
            'submit form.signin_form': 'doLogin',
            // user register
            'submit form.signup_form': 'doRegister',
            // user forgot pass
            'submit form.forgotpass_form': 'doSendPassword',
            // close modal, reset form
            'click  button.close': 'resetAuthForm',
            // open modal sign up
            'click a.link_sign_up': 'openSingup',
            // open modal forgot
            'click a.link_forgot_pass': 'openForgot',
            // open modal sign in
            'click a.link_sign_in': 'openSingin'
        },
        /**
         * init view setup Block Ui and Model User
         */
        initialize: function() {
            AE.Views.Modal_Box.prototype.initialize.call();
            this.blockUi = new Views.BlockUi();
            this.user = this.model;
            this.initValidator();
        },
        /**
         *  open Modal Sign Up
         */
        openSingup: function(event) {
            event.preventDefault();
            this.current_form = "#signup_form";
            $('#signin_form').fadeOut("slow", function() {
                $(this).css({
                    'z-index': 1
                });
                $('.modal-title-sign-in').empty().text(de_front.texts.sign_up);
                $('#signup_form').fadeIn(500).css({
                    'z-index': 2
                });
            });           
            /*move captcha from modal signup to step 2*/
            $('#login_register').on('hidden.bs.modal', function () {
                if(typeof(grecaptcha) != "undefined" && grecaptcha !== null)
                    grecaptcha.reset();
                $('.g-recaptcha').prependTo('.step2-captcha');
            })               
        },
        /**
         *  open Modal Forgot
         */
        openForgot: function(event) {
            event.preventDefault();
            this.current_form = "#forgotpass_form";
            $('#signin_form').fadeOut("slow", function() {
                $(this).css({
                    'z-index': 1
                });
                $('.modal-title-sign-in').empty().text(de_front.texts.forgotpass);
                $('#forgotpass_form').fadeIn(500).css({
                    'z-index': 2
                });
            });
        },
        /**
         *  open Modal Sign In
         */
        openSingin: function(event) {
            event.preventDefault();
            $(this.current_form).fadeOut("slow", function() {
                $(this).css({
                    'z-index': 1
                });
                $('.modal-title-sign-in').empty().text(de_front.texts.sign_in);
                $('#signin_form').fadeIn(500).css({
                    'z-index': 2
                });
            });
        },
        /**
         * init form validator rules
         * can override this function by using prototype
         */
        initValidator: function() {
            // login rule
            this.login_validator = $("form.signin_form").validate({
                rules: {
                    user_login: "required",
                    user_pass: "required"
                }
            });
            /**
             * register rule
             */
            this.register_validator = $("form.signup_form").validate({
                rules: {
                    user_login: "required",
                    user_pass: "required",
                    user_email: {
                        required: true,
                        email: true
                    },
                    re_password: {
                        required: true,
                        equalTo: "#reg_pass"
                    }
                }
            });
            /**
             * forgot pass email rule
             */
            this.forgot_validator = $("form.forgotpass_form").validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                }
            });
        },
        /**
         * user login,catch event when user submit login form
         */
        doLogin: function(event) {
            event.preventDefault();
            event.stopPropagation();
            /**
             * call validator init
             */
            this.initValidator();
            var form = $(event.currentTarget),
                button = form.find('input.btn-submit'),
                view = this;
            /**
             * scan all fields in form and set the value to model user
             */
            form.find('input, textarea, select').each(function() {
                view.user.set($(this).attr('name'), $(this).val());
            });
            // check form validate and process sign-in
            if (this.login_validator.form() && !form.hasClass("processing")) {
                this.user.set('do', 'login');
                this.user.request('read', {
                    beforeSend: function() {
                        view.blockUi.block(button);
                        form.addClass('processing');
                    },
                    success: function(user, status, jqXHR) {
                        view.blockUi.unblock();
                        form.removeClass('processing');
                        // trigger event process authentication
                        AE.pubsub.trigger('ae:user:auth', user, status, jqXHR);
                        view.closeModal();
                        if(typeof(captcha) != "undefined" && captcha !== null && captcha.is_captcha === "true")
                           window.location.reload();
                    }
                });
            }
        },
        /**
         * user sign-up catch event when user submit form signup
         */
        doRegister: function(event) {
            event.preventDefault();
            event.stopPropagation();
            /**
             * call validator init
             */
            this.initValidator();
            var form = $(event.currentTarget),
                button = form.find('input.btn-submit'),
                view = this;
            /**
             * scan all fields in form and set the value to model user
             */
            form.find('input, textarea, select').each(function() {
                view.user.set($(this).attr('name'), $(this).val());
            });
            // check form validate and process sign-up
            if (this.register_validator.form() && !form.hasClass("processing")) {
                this.user.set('do', 'register');
                this.user.request('create', {
                    beforeSend: function() {
                        view.blockUi.block(button);
                        form.addClass('processing');
                    },
                    success: function(user, status, jqXHR) {
                        view.blockUi.unblock(button);
                         if(status.success){
                            view.blockUi.unblock();
                            form.removeClass('processing');
                            // trigger event process authentication
                            AE.pubsub.trigger('ae:user:auth', user, status, jqXHR);
                            view.closeModal();
                        }else{
                             form.removeClass('processing');
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'error'
                            });
                            if(typeof grecaptcha != 'undefined'){
                                grecaptcha.reset();
                            }
                        }
                    }
                });
            }
        },
        /**
         * user forgot password
         */
        doSendPassword: function(event) {
            event.preventDefault();
            event.stopPropagation();
            /**
             * call validator init
             */
            this.initValidator();
            var form = $(event.currentTarget),
                email = form.find('input.email').val(),
                button = form.find('input.btn-submit'),
                view = this;
            if (this.forgot_validator.form() && !form.hasClass("processing")) {
                this.user.set('user_login', email);
                this.user.set('do', 'forgot');
                this.user.request('read', {
                    beforeSend: function() {
                        view.blockUi.block(button);
                        form.addClass('processing');
                    },
                    success: function(user, status, jqXHR) {
                        form.removeClass('processing');
                        view.blockUi.unblock();
                        if (status.success) {
                            view.closeModal();
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'success'
                            });
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            }
        },
        /**
         * reset form status to origin
         */
        resetAuthForm: function(event) {
            event.preventDefault();
            var view = this;
            // show form login
            this.$("form.signin_form").fadeIn('slow', function() {
                // empty content
                $('.modal-title-sign-in').empty().text(de_front.texts.sign_in);
                // hide form signup
                view.$("form.signup_form").hide();
                // hide form forgot pass
                view.$("form.forgotpass_form").hide();
            });
        }
    });

    /**
     * modal view create/edit a event
     */
    Views.CreateEvent = Views.Modal_Box.extend({
        events: {
            'submit form#event_form': 'submit'
        },
        initialize: function(options) {
            AE.Views.Modal_Box.prototype.initialize.call();
            this.blockUi = new Views.BlockUi();
            this.model = new Models.Event();
            this.initValidator;
            DPGlobal.dates = ae_globals.dates;
            /**
             * set up date picker
             */
            var nowTemp = new Date(),
                now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0),
                //check in date
                checkin = $('#event_start_date').datepicker({
                    appendTo : '.event-start-date',
                    weekStart : parseInt(ae_globals.start_of_week),
                    // onRender: function(date) {
                    //     return date.valueOf() < now.valueOf() ? 'disabled' : '';
                    // }
                    // format : ae_globals.date_format
                }).on('changeDate', function(ev) {
                    if (ev.date.valueOf() > checkout.date.valueOf()) {
                        var newDate = new Date(ev.date);
                        newDate.setDate(newDate.getDate() + 1);
                        checkout.setValue(newDate);
                    }
                    checkin.hide();
                    $('#event_close_date')[0].focus();
                }).data('datepicker');

            // close event
            var checkout = $('#event_close_date').datepicker({
                // format : ae_globals.date_format,
                appendTo : '.event-close-date',
                weekStart : parseInt(ae_globals.start_of_week)
                // onRender: function(date) {
                //     return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
                // }
            }).on('changeDate', function(ev) {
                checkout.hide();
            }).data('datepicker');
            // event_banner
            this.uploaderID = 'event_banner';
            var $container = $("#event_banner_container"),
                view = this;
            if (typeof this.banner_uploader === "undefined") {
                this.banner_uploader = new AE.Views.File_Uploader({
                    el: $container,
                    uploaderID: this.uploaderID,
                    thumbsize: 'medium',
                    multipart_params: {
                        _ajax_nonce: $container.find('.et_ajaxnonce').attr('id'),
                        data: {
                            method: 'event_banner'
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
                    beforeSend: function() {
                        view.blockUi.block($container);
                    },
                    success: function(res) {
                        if (res.success === false) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error',
                            });
                        }else{
                            view.model.set('featured_image', res.data.attach_id);
                        }
                        view.blockUi.unblock();
                    }
                });
            }
            //set up date picker
        },
        /**
         * init form validator
         */
        initValidator: function() {
            /**
             * post form validate
             */
            $("form#event_form").validate({
                ignore: "",
                rules: {
                    post_title: "required",
                    short_tag: "required",
                    event_content: "required",
                },
                errorPlacement: function(label, element) {
                    // position error label after generated textarea
                    if (element.is("textarea")) {
                        label.insertAfter(element.next());
                    } else {
                        $(element).closest('div').append(label);
                    }
                    AE.pubsub.trigger('ae:notification', {
                        msg: ae_globals.msg,
                        notice_type: 'error',
                    });
                }
            });
        },
        onCreateEvent: function(place) {
            this.model.set('post_parent', place.get('ID'));
            // open the modal
            this.openModal();
            // setup fields
            setTimeout(function() {
                if (typeof tinyMCE !== 'undefined') {
                    tinymce.EditorManager.execCommand('mceAddEditor', true, "event_content");
                    // tinymce.EditorManager.get('event_content').setContent(view.model.get('post_content'));
                }
            }, 500);
        },
        
        onEditEvent : function(model){
            this.model = model;
            this.setupFields();

            this.openModal();
        }, 
        /**
         * setup modal form data by model
        */
        setupFields : function(){
            var view = this,
                form_field = view.$('.form-field'),
                location = this.model.get('location'),
                cover_image = view.model.get('large_thumbnail');
            AE.pubsub.trigger('AE:beforeSetupFields', this.model);

            setTimeout(function() {
                if (typeof tinyMCE !== 'undefined') {
                    tinymce.EditorManager.execCommand('mceAddEditor', true, "event_content");
                    tinymce.EditorManager.get('event_content').setContent(view.model.get('post_content'));
                }
            }, 500);
            /**
             * update form value for input, textarea select
             */
            form_field.find('input[type="text"],input[type="hidden"], textarea,select').each(function() {
                var $input = $(this);
                $input.val(view.model.get($input.attr('name')));
                // trigger chosen update if is select
                if ($input.get(0).nodeName === "SELECT") {
                    $input.trigger('chosen:updated');
                }
            });
            form_field.find('input[type="radio"]').each(function() {
                var $input = $(this),
                    name = $input.attr('name');
                if ($input.val() === view.model.get(name)) {
                    $input.attr('checked', true);
                }
            });
            /**
             * update cover image view
             */
            if (cover_image) {
                view.$('#event_banner_thumbnail').html('').append('<img style="width: 300px;" src="'+cover_image+'" />' );
            } 
        },

        submit: function(event) {
            event.preventDefault();
            var $form = $(event.currentTarget),
                temp = new Array(),
                view = this;
            if ($('form#event_form').valid()) {
                /**
                 * update model from input, textarea, select
                 */
                view.$el.find('input,textarea,select').each(function() {
                    view.model.set($(this).attr('name'), $(this).val());
                });
                /**
                 * update input check box to model
                 */
                view.$el.find('input[type=checkbox]:checked').each(function() {
                    var name = $(this).attr('name');
                    temp.push($(this).val());
                    view.model.set(name, temp);
                });
                /**
                 * update input radio to model
                 */
                view.$el.find('input[type=radio]:checked').each(function() {
                    view.model.set($(this).attr('name'), $(this).val());
                });
                view.model.save('', '', {
                    beforeSend : function(){
                        view.blockUi.block($form);
                    }, 
                    success : function(result, status, jqXHR){
                        view.blockUi.unblock();
                        if(status.success) {
                            pubsub.trigger('ae:notification', {
                                msg: status.msg,
                                notice_type: 'success',
                            });
                            window.location.reload();                            
                        }else {
                            pubsub.trigger('ae:notification', {
                                notice_type : 'error', 
                                msg : status.msg
                            });
                        }
                    }
                });
            }
        }
    });
    Views.SearchForm = Backbone.View.extend({
        el: '#header-wrapper',
        events: {
            'click .search-btn': 'triggerSearchForm'
        },
        //init search form view
        initialize: function(options) {
            _.bindAll(this, 'showMap', 'errorHandle');
            var view = this;
            this.$('.slider-ranger').on('slide', function(ev) {
                var value = ev.value;
                $('#' + $(this).attr('data-name')).val(value);
                $('.' + $(this).attr('data-name')).html(value);
            });
            /*this.$('.nearby').on('slideStart', function() {
                navigator.geolocation.getCurrentPosition(view.showMap, view.errorHandle);
            });*/
            this.$('form').validate();
        },
        /**
         * slide down search form
         */
        triggerSearchForm: function(event) {
            // HEADER TOP OPTION SEARCH
            $option_search = $('.option-search-form-wrapper');
            $marsk = $('.marsk-black');
            $btn_topsearch = $('ul.top-menu-right li.top-search');
            // toggle search form
            $marsk.fadeToggle(300);
            $btn_topsearch.toggleClass('active');
            $option_search.slideToggle(300, 'easeInOutSine', function(event) {
                $('.slider-ranger').slider({
                    tooltip: 'always'
                });
            });
        },
        /**
         * catch  position request
         */
        showMap: function(position) {
            var coords = position.coords;
            $('#center').val(coords.latitude + ',' + coords.longitude);
            AE.pubsub.trigger('de:getCurrentPosition', position.coords);
        },
        // handle error when get user location
        errorHandle: function() {
            alert(de_front.texts.request_geo);
        }
    });
})(jQuery, window.AE, window.AE.Views, window.AE.Models, window.AE.Collections, Backbone);
jQuery.fn.serializeObject = function() {
    var self = this,
        json = {},
        push_counters = {},
        patterns = {
            "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
            "key": /[a-zA-Z0-9_]+|(?=\[\])/g,
            "push": /^$/,
            "fixed": /^\d+$/,
            "named": /^[a-zA-Z0-9_]+$/
        };
    this.build = function(base, key, value) {
        base[key] = value;
        return base;
    };
    this.push_counter = function(key) {
        if (push_counters[key] === undefined) {
            push_counters[key] = 0;
        }
        return push_counters[key]++;
    };
    jQuery.each(jQuery(this).serializeArray(), function() {
        // skip invalid keys
        if (!patterns.validate.test(this.name)) {
            return;
        }
        var k,
            keys = this.name.match(patterns.key),
            merge = this.value,
            reverse_key = this.name;
        while ((k = keys.pop()) !== undefined) {
            // adjust reverse_key
            reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');
            // push
            if (k.match(patterns.push)) {
                merge = self.build([], self.push_counter(reverse_key), merge);
            }
            // fixed
            else if (k.match(patterns.fixed)) {
                merge = self.build([], k, merge);
            }
            // named
            else if (k.match(patterns.named)) {
                merge = self.build({}, k, merge);
            }
        }
        json = jQuery.extend(true, json, merge);
    });
    return json;
};