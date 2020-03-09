(function($, AE, Views, Models, Collections) {
    Views.PostForm = Views.SubmitPost.extend({
        onAfterInit: function() {
            var view = this;
            view.carousels = new Views.Carousel({
                el: $('#gallery_container'),
                model: view.model,
                name_item: 'et_carousel',
                uploaderID :  'carousel',
            });
            /*// bind date picker to opendate
            this.$('.open-time').timepicker({
                // appendTo: 'div.time-picker-body',
                // modalBackdrop: true,
                step: 15,
                setTime: (view.model.get('open_time') !== '') ? view.model.get('open_time') : '8:00 AM',
                timeFormat: ae_globals.time_format
            });
            // bind date picker to opendate
            this.$('.close-time').timepicker({
                // appendTo: 'div.time-picker-body',
                step: 15,
                setTime: (view.model.get('close_time') !== '') ? view.model.get('close_time') : '8:00 PM',
                timeFormat: ae_globals.time_format
            });  */
            // Serve Time
            var serve_time = view.model.get('serve_time');
            if(!$.isEmptyObject(serve_time)){
                this.$('ul.date-list li.bdate').each(function(){
                    var name    = $(this).data('name');
                    object  = serve_time[name],
                        open_time = object.open_time,
                        close_time = object.close_time;
                    if(open_time != "" && close_time != ""){
                        $(this).addClass('vbdate');
                        $(this).next().addClass('nbdate');
                        $(this).attr('data-original-title', open_time + ' to ' + close_time);
                        $(this).attr('open-time', open_time);
                        $(this).attr('close-time', close_time);
                    }else{
                        $(this).removeClass('vbdate');
                        $(this).next().removeClass('nbdate');
                        $(this).attr('data-original-title', "");
                        $(this).attr('open-time', "");
                        $(this).attr('close-time', "");
                    }
                });
            }else{
                this.$('ul.date-list li.bdate').each(function(){
                    $(this).removeClass('vbdate');
                    $(this).next().removeClass('nbdate');
                    $(this).attr('data-original-title', "");
                    $(this).attr('open-time', "");
                    $(this).attr('close-time', "");
                });
            }
        },
        onBeforeShowNextStep: function (event){
            var view = this;
            if(parseInt(this.use_plan) === 1 && this.user_login){
                view.$('.step-wrapper .step-content-wrapper').removeClass('content');                
            }            
        },
        onLimitFree: function() {
            AE.pubsub.trigger('ae:notification', {
                msg: ae_globals.limit_free_msg,
                notice_type: 'error',
            });
        },
        onAfterShowNextStep: function(step) {
            $('.step-heading').find('i.fa-caret-down').removeClass('fa-caret-right fa-caret-down').addClass('fa-caret-right');
            $('.step-' + step).find('.step-heading i.fa-caret-right').removeClass('fa-caret-right').addClass('fa-caret-down');
        },
        onAfterSelectStep: function(step) {
            $('.step-heading').find('i.fa-caret-down').removeClass('fa-caret-right fa-caret-down').addClass('fa-caret-right');
            step.find('i.fa-caret-right').removeClass('fa-caret-right').addClass('fa-caret-down');
        },
        // on after Submit auth fail
        onAfterAuthFail: function(model, res) {
            AE.pubsub.trigger('ae:notification', {
                msg: res.msg,
                notice_type: 'error',
            });
        },
        onAfterPostFail: function(model, res) {
            AE.pubsub.trigger('ae:notification', {
                msg: res.msg,
                notice_type: 'error',
            });
        },
        onAfterSelectPlan : function($step, $li){
            var label = $li.attr('data-label');
            $step.find('.text-heading-step').html(label);
            if(typeof(captcha) != "undefined" && captcha !== null && captcha.is_captcha === "true")
                $('.gg-captcha').css("display","none");
        },
        // trigger set serve time
        onBeforeSubmitPost : function (model, view){
            if( view.$el.find('textarea').length > 0 ){
                view.$el.find('textarea').each(function() {
                    view.model.set( $(this).attr('name'),  $(this).val() );    
                });
            }
            if(typeof(captcha) != "undefined" && captcha !== null && captcha.is_captcha === "true")
                view.model.set('is_captcha','true');
            var li = $('ul.date-list li.bdate');
            $(li).each(function(){
                var name = $(this).data('name');
                    open_time = $(this).attr('open-time'),
                    close_time = $(this).attr('close-time');
                    
                view.model.set('serve_time['+name+'][open_time]',open_time);
                view.model.set('serve_time['+name+'][close_time]',close_time);
            });
        },
        onAfterShowNextStep: function(next, viewstep){
            // Scroll to ID when submit post
            if(next == 'payment'){
                $("html, body").animate({ scrollTop: $('#step-post').offset().top - 150 }, 1000);
            }
            setTimeout(function(){ google.maps.event.trigger(map, 'resize'); }, 2000);
        }
    });

    Views.Post_Place = Backbone.View.extend({
        el: 'body',
        initialize: function() {
            $('.open-time').timepicker({
                'timeFormat': "H:i",
                'appendTo': '.container-open-time',
                'maxTime': '24',
                'step': 30,
                'lang': {'am': 'AM', 'pm': 'PM'},
                'noneOption': [{'label': 'None','value': 'none'}],
            });
            $('.close-time').timepicker({
                'timeFormat': "H:i",
                'appendTo': '.container-close-time',
                'maxTime': '24',
                'step': 30,
                'lang': {'am': ' AM', 'pm': ' PM'},
                'noneOption': [{'label': 'None','value': 'none'}]
            });
            $('li.bdate[data-toggle="tooltip"]').tooltip({'html': true});
            $('li.bdate').each(function(index) {
                $(this).attr('open-time','');
                $(this).attr('close-time', '');
            });
        },
        events: {
            'change #et_claimable' : 'setClaimable',
            'click span.select-date-all' : 'SelectDateAll',
            'click li.bdate' : 'SelectButtonDate',
            'change input.open-time' : 'InputOpenTime',
            'change input.close-time' : 'InputCloseTime',
            'click span.reset-all': 'ResetAllDate'
        },
        setClaimable: function(event){
            event.preventDefault();
            var value = $("#et_claimable_value");
            value.val(value.val() == 0 ? 1 : 0);
        },
        /*
        *   Click select date all open time
        */
        SelectDateAll: function(e) {
            var ev = e.target;
            $(ev).toggleClass('active');
            if($(ev).hasClass('active')) {
                $(ev).text(ae_globals.translate_deselect);
                $('li.bdate').addClass('active');
            } else {
                $(ev).text(ae_globals.translate_select);
                $('li.bdate').removeClass('active');
            }
        },
        /*
        *   Click button date
        */
        SelectButtonDate: function(e) {
            var ev = e.target;
            var active = false;
            $(ev).toggleClass('active');
            if($(ev).hasClass('active')) {
                $('.select-date-all').addClass('active');
                $('.select-date-all').text(ae_globals.translate_deselect);
                $('.open-time').val($(ev).attr('open-time'));
                $('.close-time').val($(ev).attr('close-time'));
            } else {
                $('.open-time').val('');
                $('.close-time').val('');
                $('li.bdate').each(function() {
                    if($(this).hasClass('active')) {
                        active = true;
                    }
                });
                if(!active) {
                    $('.select-date-all').removeClass('active');
                    $('.select-date-all').text(ae_globals.translate_select);
                }
            }
        },
        /*
        * Button date active
        */
        ButtonDateActive: function() {
            var active = false;
            $('li.bdate').each(function(index) {
                if($(this).hasClass('active')) {
                    active = true;
                }
            });
            if(!active) {
                $('.select-date-all').removeClass('active');
                $('.select-date-all').text(ae_globals.translate_select);
            }
            return active;
        },
        /*
        *   Change input open time
        */
        InputOpenTime: function(e) {
            var ev = e.target;
            var _this = this;
            var active = _this.ButtonDateActive();
            // Check Timeformat
            if($(ev).val() != "none" && !/^([01]?[0-9]|2[0-4]):[0-5][0-9]$/.test($(ev).val())){
                alert(ae_globals.invalid_time);
                $(ev).val("");
                return;  
            }
            if(active) {
                $('li.bdate').each(function(index) {
                    if($(this).hasClass('active')) {
                        $(this).attr('open-time', $(ev).val());
                        var open = $(this).attr('open-time');
                        var close = $(this).attr('close-time');
                        if(open == 'none') {
                            $('.close-time').val('');
                            $(this).attr('data-original-title', '');
                            $(this).removeClass('vbdate');
                            $(this).removeClass('active');
                            $(this).next().removeClass('nbdate');
                            $(this).attr('open-time','');
                            $(this).attr('close-time','');
                        }
                    }
                });
            } else {
                $(ev).val('');
                $('.close-time').val('');
            }
        },
        /*
        *   Change input close time
        */
        InputCloseTime: function(e) {
            var ev = e.target;
            var _this = this;
            var active = _this.ButtonDateActive();
            // Check Timeformat
            if($(ev).val() != "none" && !/^([01]?[0-9]|2[0-4]):[0-5][0-9]$/.test($(ev).val())){
                alert(ae_globals.invalid_time);
                $(ev).val("");
                return;  
            }
            if(active) {
                $('li.bdate').each(function(index) {
                    if($(this).hasClass('active')) {
                        $(this).attr('close-time', $(ev).val());
                        var open = $(this).attr('open-time');
                        var close = $(this).attr('close-time');
                        if(close == 'none') {
                            $('.open-time').val('');
                            $(this).attr('data-original-title', '');
                            $(this).removeClass('vbdate');
                            $(this).removeClass('active');
                            $(this).next().removeClass('nbdate');
                            $(this).attr('open-time','');
                            $(this).attr('close-time','');
                        } else {
                            if (open == 'none' || open == '') {
                                $('.close-time').val('');
                                $(this).attr('data-original-title', '');
                                $(this).removeClass('vbdate');
                                $(this).removeClass('active');
                                $(this).next().removeClass('nbdate');
                                $(this).attr('open-time','');
                                $(this).attr('close-time','');
                            } else {
                                $(this).addClass('vbdate');
                                $(this).removeClass('active');
                                $(this).next().addClass('nbdate');
                                $(this).attr('data-original-title', open + '<span> to </span>' + close);
                            }
                        }
                    }
                });
                $('.select-date-all').removeClass('active');
                $('.select-date-all').text(ae_globals.translate_select);
            } else {
                $(ev).val('');
                $('.open-time').val('');
            }
        },
        /*
        * Reset All Date None
        */
        ResetAllDate: function() {
            $('li.bdate').each(function() {
                $(this).removeClass('vbdate').removeClass('nbdate');
                $(this).attr('open-time', '').attr('close-time', '').attr('data-original-title', '');
                var text = $(this).text();
            });
            $('.time-picker').val('');
            $('.open-input input').val('');
        }

    });

    new Views.Post_Place();

})(jQuery, window.AE, window.AE.Views, window.AE.Models, window.AE.Collections);