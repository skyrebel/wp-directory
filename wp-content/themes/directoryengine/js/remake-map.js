(function(Views, Models, $, Backbone, Collections) {
    Views.Map_View_Remake = Backbone.View.extend({
        // load info window content template
        events: {'click .locate-me': 'InitMapLocation', 'change select ': 'selectFilter', 'click #pagination': 'pagination','click #update_results':'Update_results','click #reset_search':'reset_search'},
        // initialize view
        initialize: function(options) {
            _.bindAll(this, 'setCenter', 'renderMap');
            var view = this;
            view.search_radius = parseInt(ae_globals.number_radius);
            this.view_templates = { };
            if ($('#google_canvas').length === 0) {
                return;
            }
            if(typeof MarkerClusterer !== 'undefined'){
                MarkerClusterer.prototype.MARKER_CLUSTER_IMAGE_PATH_ = ae_globals.imgURL+'m';
                MarkerClusterer.prototype.MARKER_CLUSTER_IMAGE_EXTENSION_ = "png";
            }
            $(".slider-ranger-radius").on("slideStop", function(slideEvt) {               
                var latitude  =  $('#latitude').val();
                var longitude  =  $('#longitude').val();
                var position = {radius :slideEvt.value, latitude: latitude,longitude: longitude};
                if(latitude && longitude)
                {         
                  AE.pubsub.trigger('de:getRadiusPosition', position, this);
                  view.search_radius = slideEvt.value;
                }
                 
            });
            $(".slider-ranger-radius").on("slide", function(slideEvt) {
                var unit = ae_globals.units_of_measurement;
                var temp = unit === 'km' ? "Km" :"Miles";
                $("#numRadius").text('< ' + slideEvt.value +' '+temp );
            });
            view.options = _.extend(this, options);
            view.center = new google.maps.LatLng(this.options.latitude, this.options.longitude);
            view.map_options = {
                'zoom': parseInt(ae_globals.map_zoom),
                'center': view.center,
                'mapTypeId': google.maps.MapTypeId.ROADMAP,
                'scrollwheel': false,
                'zoomControl': true,
                'styles':[ { "featureType": "poi.business", "stylers": [ { "visibility": "off" } ] } ] 
            };

            this.template = _.template($('#ae_info_content_template').html());
            // map marker collections
            view.currentMarker = null;
            // map marker cluster
            this.initMapWindow();
            // Map for default save-widget
            this.map = new google.maps.Map(document.getElementById("google_canvas"), view.map_options);
            var map_style = ae_globals.global_map_style;

            if( map_style != null ){
                if( ae_globals.map_typestyle != 1){
                    // remove point and transit on mobile
                    map_style.push(
                        {featureType: "poi", stylers: [{ visibility: 'off' }]},
                        {featureType: "transit.station.bus", stylers: [{ visibility: 'off' }]}
                    );
                }else{
                    map_style.push(
                        {featureType: "poi", stylers: [{ visibility: 'on' }]},
                        {featureType: "transit.station.bus", stylers: [{ visibility: 'on' }]}
                    );
                }
            }else{
                if( ae_globals.map_typestyle != 1){
                    // remove point and transit on mobile
                    this.map.set('styles',[
                        {featureType: "poi", stylers: [{ visibility: 'off' }]},
                        {featureType: "transit.station.bus", stylers: [{ visibility: 'off' }]}
                    ]);
                }
            }

            if(ae_globals.global_map_style){
                this.map.setOptions({
                    styles: map_style
                });
            }

            this.categories = [];
            if ($('#de-categories-data').length > 0) {
                this.categories = JSON.parse($('#de-categories-data').html());
            }
            this.initMapIcon();
            
            this.lockView = true;
            this.nearby = false;
            if ($('#nearby_location').length > 0) {
                this.nearby = true;
            }
            //bind event when user give location
            AE.pubsub.on('de:getRadiusPosition', this.setRadius, this);
            AE.pubsub.on('de:getCurrentPosition', this.setCenter, this);
            AE.pubsub.on('de:map:drawGeoLocate', this.drawGeoLocate, this);
            AE.pubsub.on('de:map:GeoLocate', this.geoLocate, this);
            AE.pubsub.on('de:getdataRadius', this.getdataRadius, this);

           var input = (document.getElementById('address'));
           if(ae_globals.gg_map_apikey)
           {
             autocomplete = new google.maps.places.Autocomplete(input);
             autocomplete.addListener('place_changed', function() {        
                   var place = autocomplete.getPlace();
                   // map.setCenter(place.geometry.location.lat(), place.geometry.location.lng());
                    var position = {coords : {latitude: place.geometry.location.lat(),longitude: place.geometry.location.lng()}}; 
                   $('#latitude').val(place.geometry.location.lat());
                   $('#longitude').val(place.geometry.location.lng());               
                   AE.pubsub.trigger('de:map:drawGeoLocate', position);
               });
            }
            view.blockUi = new Views.BlockUi();
            view.renderMap('init',0);
            view.currpage = 0;
        },
		InitMapLocation : function(){
			 var view = this;
			 if (parseInt(ae_globals.geolocation)) {
                GMaps.geolocate({
                    success: function(position) {
                        var coords = position.coords;
                        this.view_templates = coords;
					$('#center').val(coords.latitude + ',' + coords.longitude);
                    $('#latitude').val(coords.latitude);
                    $('#longitude').val(coords.longitude);
					latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
					var geocoder = new google.maps.Geocoder();
                     geocoder.geocode({
                         "latLng":latlng
                     }, function (results, status) {
                         if (status == google.maps.GeocoderStatus.OK) {
                            $('#address').val(results[0].formatted_address);
                            //document.getElementById('google_canvas').innerHTML = results[0].formatted_address;
                         }
                     });
					AE.pubsub.trigger('de:map:drawGeoLocate', position);
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
		},
        selectFilter: function (event){
             var $target = $(event.currentTarget),
             name = $target.attr('name');
            var view = this;
            var radius = view.search_radius ;
            var latitude  =  $('#latitude').val();
            var longitude  =  $('#longitude').val();
            var address = $('#address').val();
            if(!latitude && !longitude || address === '')
            {
                 this.map.setCenter(this.center);
                 radius = 88898;
            }
            view.show_pins(radius);         
        },
        Update_results: function (){
            var view = this;
            view.renderMap('init',1);
        },
        reset_search: function (){
             var view = this;
             view.search_radius = 88898;
             $('.tax-item').val('').trigger("chosen:updated");
             $('#search-location-form').find('input:text, input:hidden').val('');
             $('.slider-ranger-radius').slider('refresh');
             var unit = ae_globals.units_of_measurement;
             var temp = unit === 'km' ? "Km" :"Miles";
             $("#numRadius").text('< ' + parseInt(ae_globals.number_radius) +' '+ temp );
              view.renderMap('init',1);
        },
        show_pins : function(radiuslo)
        {
            var view = this;
            var result,cat,radius;
            if(ae_globals.gg_map_apikey){
            var temp_markers = new Array();
            var bounds = new google.maps.LatLngBounds();
            if(typeof view.markerCluster !== 'undefined' ) {
              view.markerCluster.clearMarkers();
            }
            var s = $('select[name="place_category"] :selected').attr('class');
            var results_no  =   0; 
            var hasicon = 0;   
            if(typeof(s) !=='undefined'){
                 var patt1 = /cat-([^ ]+)/.exec(s)[1]; 
                 result = s.match(patt1);
                 cat =  parseInt(result[0]);
                 hasicon = 1;    
            }
            else{
                cat = 'all';
            }
            radius = view.checkRadius(radiuslo,1);
            for (var i = 0; i<view.markers.length; i++) {
                if(!view.getcat(view.markers[i].category,cat))
                {
                     view.markers[i].setVisible(false); 
                }
                else if (!view.getlocation(view.markers[i].location))
                {

                }
                else if (!view.getdistance(view.markers[i].distance,radius))
                {
                     view.markers[i].setVisible(false);
                } 
                else  
                {
                      view.markers[i].setVisible(true);
                      temp_markers.push(view.markers[i]);
                      bounds.extend(view.markers[i].position);
                      results_no++;
                       if(hasicon){
                          view.setIconMarker(view.markers[i],cat);
                          //view.markers[i]['icon'] = view.icons[cat];

                       }
                       else
                       {
                         view.setIconMarker(view.markers[i],view.markers[i].term);
                       }
                }
            }
            if(results_no !==0){
                view.markerCluster.addMarkers(temp_markers);
              if( parseInt(ae_globals.fitbounds)) {
                view.map.fitBounds(bounds);
              }
            }
          }else
          radius = 88898;
          view.renderData(radius);           
        },
        setIconMarker :function (marker,term){
              var view = this;
              var icon = view.icons[term],
              color = view.colors[term],
              fontClass = view.fontClass[term];
               if (typeof color === 'undefined') {
                    color = '#F59236';
                }
                if (typeof fontClass === 'undefined') {
                    fontClass = 'fa-map-marker';
                }
              var labelContent = "<span><i style='color:" + color + ";' class='fa " + fontClass + "'></i><span>";
              marker['icon'] = icon;
              marker['labelContent'] = labelContent;
        },
        getcat : function (category,cat){
            if (cat !=='' && cat !==' ' && cat !=='all'){
                 if(  ( category.indexOf(cat)> -1 )   && category!=='' && cat!==''){
                    return true;
                } else{
                    return false;
                }
          }
          else{
                return true;
            }
        },
        getlocation : function (location){
            var s = $('select[name="place_location"] :selected').attr('class');
            var loc,result;  
            if(typeof(s) !=='undefined'){
                 var patt1 = /cat-([^ ]+)/.exec(s)[1]; 
                 result = s.match(patt1);
                 loc =  parseInt(result[0]);  
            }
            else{
                loc = 'all';
            }
            if (loc !=='' && loc !==' ' && loc !=='all'){
                 if(  ( location.indexOf(loc)> -1 )   && location!=='' && loc!==''){
                    return true;
                    } else{
                        return false;
                    }
              }
              else{
                    return true;
                }
        },
        checkRadius : function(slideRadius,ver) {
          var view = this;
            var radius;
            var target       = $('.job_listings');
            var form         = target.find( '.job_filters' );
            var latitude = form.find( 'input[name="latitude"]').val();
            var longitude = form.find( 'input[name="longitude"]').val();
            var address = form.find( 'input[id="address"]').val();
            var bounds = ae_globals.gg_map_apikey ? this.get_bound_distance() : 88898;  
            if(latitude && longitude && address != '')
            {  
                 radius = slideRadius;
            }
            else
            {
              if(view.currentMarker !== null) {
                 view.currentMarker.setMap(null);
                view.currentMarker = null;
              }
                radius = bounds;
            }
            return radius;
        },
        getdistance : function (markers,radius){
           var unit = ae_globals.units_of_measurement;
            if(unit === 'km' ) {
             radius = radius/1.609344;
            }
            if( radius >  markers){
                return true;
            }else{
                return  false;
            } 
        },
        geoLocate : function(successEventId){
            GMaps.geolocate({
                success: function(position) {
                    AE.pubsub.trigger(successEventId, position);
                },
                error: function(error) {
                    AE.pubsub.trigger('ae:notification',{msg: ae_globals.geolocation_failed + ': '+ error.message,notice_type: 'error',});
                },
                not_supported: function() {
                    alert(ae_globals.browser_supported);
                    AE.pubsub.trigger('ae:notification',{msg: ae_globals.browser_supported,notice_type: 'error',});
                }
            });
        },
        drawGeoLocate : function(position){
            var view = this;
            if(typeof this.model === 'undefined' || this.nearby || !this.lockView) {
                AE.pubsub.trigger('de:getCurrentPosition', position.coords);
                 /*view.map.setZoom(15);*/
            }
            var latLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            if(view.currentMarker === null) {
                var icon = {
                    url: ae_globals.current_possition_img,
                    size: new google.maps.Size(90, 90),
                };
                view.currentMarker = new google.maps.Marker({
                    map: view.map,
                    animation: google.maps.Animation.DROP,
                    title: ae_globals.current_possition_title,
                    position: latLng,
                    icon: icon
                });
            }
            else
            {
                view.currentMarker.setPosition(latLng);
                view.currentMarker.setAnimation(google.maps.Animation.DROP);
            }
            if(view.search_radius > parseInt(ae_globals.number_radius))
            {
                view.search_radius = parseInt(ae_globals.number_radius);
            }
            view.renderMap('init',1);
        },
        getdataRadius : function(result,page){
           var view = this;
           view.resultPlace = result;
           this.options.collection.reset();
           this.options.collection.add(this.pagination(result,page));
           this.$(".result-search-location").find('ul').html('');
           this.options.collection.each(this.addOne, this);
           if(view.resultPlace.length > parseInt(ae_globals.posts_per_page)){
               this.draw();
            }
        },
        addOne: function(todo) {
               var view = new Views.TodoView({model: todo}); 
                this.$(".result-search-location").find('ul').append(view.render().el);                        
            },
       addContextMenu : function(){
            this.map.setContextMenu({
                control: 'map',
                options: [{
                    title: 'Add marker',
                    name: 'add_marker',
                    action: function(e) {
                        this.addMarker({
                            lat: e.latLng.lat(),
                            lng: e.latLng.lng(),
                            title: 'New marker'
                        });
                    }
                }, {
                    title: 'Center here',
                    name: 'center_here',
                    action: function(e) {
                        this.setCenter(e.latLng.lat(), e.latLng.lng());
                    }
                }]
            });
        },
        // this function init map marker icon 
        initMapIcon: function() {
            var view = this;
            this.icons = {};
            this.colors = {};
            this.fontClass = {};
            _.each(this.categories, function(element) {
                var icon = {
                    path: 'M 50 -119.876 -50 -119.876 -50 -19.876 -13.232 -19.876 0.199 0 13.63 -19.876 50 -19.876 Z',
                    fillColor: element.color,
                    fillOpacity: 1,
                    scale: 0.3,
                    strokeColor: element.color,
                    strokeWeight: 0
                };
                if(element.parent !== 0 && typeof element.icon === 'undefined') {
                    view.icons[element.term_id] = view.icons[element.parent];
                    view.fontClass[element.term_id] = view.fontClass[element.parent];
                }else {
                    view.icons[element.term_id] = icon;
                    view.fontClass[element.term_id] = element.icon;
                }
                if(element.parent !== 0 && typeof element.color === 'undefined'){
                    view.colors[element.term_id] = view.colors[element.parent];
                }
                else{
                    view.colors[element.term_id] = element.color;
                }

            });
            view.labelAnchor = new google.maps.Point(10, 31);
        },

        // initialize map infowindow
        initMapWindow : function() {
            var view = this,
            // init map info window
            iw1 = new InfoBubble({
                content: '',
                // position: new google.maps.LatLng(-35, 151),
                shadowStyle: 0,
                padding: 0,
                borderRadius: 0,
                arrowSize: 0,
                borderWidth: 5,
                borderColor: '#ccc',
                disableAutoPan: false,
                backgroundColor: '#fff',
                arrowStyle: 0,
                maxWidth: 280,
                minWidth: 260,
                minHeight: 70,
                maxHeight: 900,
                autoPan: true
            });

            view.infoWindow = iw1;
        },
        /**
         * render map call ajax to get marker data
         * @author Dakachi
         */
        renderMap: function(init,ver) {
            var view = this,
                cat = '',
                data = {
                    action: 'de_get_map_data'
                };
            radiuslo = view.search_radius;
            view.markers = [];
            var keywords;
            var target       = $('.job_listings');
            var form         = target.find( '.job_filters' );
            if(ver === 0){
                var latitude = form.find( 'input[name="latitude"]').val();
                var longitude = form.find( 'input[name="longitude"]').val();
                var address = form.find( 'input[id="address"]').val();
                var bounds = ae_globals.gg_map_apikey ? this.get_bound_distance():88898;   
                if(latitude && longitude && address != '')
                {  
                    var latLng = new google.maps.LatLng(latitude,longitude);
                    this.map.setCenter(latLng);
                    if(view.currentMarker === null) {
                        var icon = {
                            url: ae_globals.current_possition_img,
                            size: new google.maps.Size(90, 90),
                        };
                        view.currentMarker = new google.maps.Marker({
                            map: view.map,
                            animation: google.maps.Animation.DROP,
                            title: ae_globals.current_possition_title,
                            position: latLng,
                            icon: icon
                        });
                    }
                    else
                    {
                        view.currentMarker.setPosition(latLng);
                        view.currentMarker.setAnimation(google.maps.Animation.DROP);
                    }
                     radiuslo = radiuslo;
                }
                else
                {
                    var bounds = ae_globals.gg_map_apikey ? this.get_bound_distance() : 88898;
                    radiuslo = bounds;
                }
            }
            var keywords  = form.find( ':input[name="search_keywords"]' ).val();
             if(typeof(keywords) !=='undefined'){
                keywords =  keywords;
             }
            /**
             * ajax request get all place on map
             */
            var i = 100,
                k = 1;
                current_place = Array();
            if ($('#total_place').length > 0) {
                i = JSON.parse($('#total_place').html());
                current_place = Array(i.current_place);
                i = i.number;
            }

            // get category data to query post in category
            if ($('#place_cat_slug').length > 0) {
                cat = JSON.parse($('#place_cat_slug').html());
                cat = cat.slug;
                radiuslo = 8890;
            }
            if( view.loaded === true && ae_globals.single_map_marker === "1") return;

            data.paged = k;
            data.showposts = 50;
            data.place_category = cat;
            if( ae_globals.is_single && ae_globals.single_map_marker === "1" ){
                data = current_place;
                view.ajaxSuccess(data);
            }else{
                if(ae_globals.gg_map_apikey){
                var center = this.map.getCenter();                    
                  data.query = {center :  String(center.lat()) + ',' + String(center.lng()) , radius : this.get_bound_distance() , s: keywords};
                }
                else
                {
                  data.query = { radius : 88898, s: keywords};
                }
                if(init == 'init') {
                    $.ajax({
                        type: 'get',
                        url: ae_globals.ajaxURL,
                        data: data,
                        beforeSend: function() {
                            view.blockUi.block('#google_canvas');
                        },
                        success: function(resp) {
                            view.blockUi.unblock();
                            if(IsJsonString(resp)){
                                resp = JSON.parse(resp);
                            }
                            if (typeof resp.data !== 'undefined' && resp.data.length > 0) {
                                var data = resp.data;
                                var result = resp.results;
                                view.ajaxSuccess(data,result);
                            }
                            else{
                                if(typeof view.markerCluster !== 'undefined' ) {
                                    view.markerCluster.clearMarkers();
                                }
                                $(".result-search-location").find('ul').html(''); 
                                 $(".search-location-pagination").html('');
                            }
                            view.show_pins(radiuslo);
                        }
                    });
                }
            }
        },
        renderData : function(radiuslo){
            var view = this,
                cat = '',
                data = {
                    action: 'de_get_map_data'
                };
            var keywords='';
            var target       = $('.job_listings');
            var form         = target.find( '.job_filters' );         
            keywords  = form.find( ':input[name="search_keywords"]' ).val();
            var categories = form.find( 'select[name="place_category"]').val();
            var location = form.find( 'select[name="place_location"]').val();
            var latitude = form.find( 'input[name="latitude"]').val();
            var longitude = form.find( 'input[name="longitude"]').val();
            var address = form.find( 'input[id="address"]').val();
            if(latitude && longitude && address != '')
            {  
                 var center = new google.maps.LatLng(latitude,longitude);
            }
           else
           {
                 var center = this.map.getCenter();  
           }
            cat = categories;
            /**
             * ajax request get all place on map
             */
            var i = 100,
                k = 1;
            data.paged = k;
            data.showposts = 50;
            data.place_category = cat;
            if(ae_globals.gg_map_apikey)           
              data.query = {center :  String(center.lat()) + ',' + String(center.lng()) , radius : radiuslo, s: keywords,location: location};
            else
              data.query = { radius : radiuslo, s: keywords,location: location};
                $.ajax({
                    type: 'get',
                    url: ae_globals.ajaxURL,
                    data: data,
                    beforeSend: function() {
                        view.blockUi.block('.result-search-location');
                    },
                    success: function(resp) {
                        view.blockUi.unblock();
                        if(IsJsonString(resp)){
                            resp = JSON.parse(resp);
                        }
                        if (typeof resp.data !== 'undefined' && resp.data.length > 0) {
                            var result = resp.data;
                            var distance_unit = view.check_location_distance();
                            if(latitude && longitude && address != '')
                              {  
                                result.forEach(function (item) {
                                  item.distance = distance_address(item.et_location_lat,item.et_location_lng,distance_unit.lat,distance_unit.lng,0);
                                  item.distance_location = distance_address(item.et_location_lat,item.et_location_lng,distance_unit.lat,distance_unit.lng,1);
                                });
                                result.sort(function(a, b) {
                                  return parseFloat(a.distance) - parseFloat(b.distance);
                                });
                              }
                            else
                            {
                                result.forEach(function (item) {
                                  item.distance_location = 'no'
                                });
                            }
                            if ($('.main-pagination').length <= 0 && result.length != parseInt(ae_globals.posts_per_page)) { 
                              $(".search-location-pagination" ).append('<div class="paginations-wrapper main-pagination"></div>'); 
                            }
                            if(result.length <= parseInt(ae_globals.posts_per_page))
                            {
                                $(".search-location-pagination").html('');
                            }
                            view.currpage = 0;
                            view.currentPage = 0;
                            AE.pubsub.trigger('de:getdataRadius', result);
                            $('.result-pagination .total_place_search').html(resp.data.length);
                            $(".job_listings").removeClass("no_places");
                            $('.job_listings').height('auto');
                        }
                        else
                        {
                             $(".result-search-location").find('ul').html('<li class ="no_result_location"><span>Oops!</span> No Results were found. </li>'); 
                             $(".search-location-pagination").html('');
                             $('.result-pagination .total_place_search').html(0);
                             $(".job_listings").addClass("no_places");
                             var heightw = $(window).height();
                              var heighth = $('#header-wrapper').height();
                              var f_offset = c_offset = 0;
                              var heightadmin_bar = 0;
                              if($('body').hasClass('admin-bar')) {
                                  heightadmin_bar = 32;
                              }
                             $('.search-location-no-mobile').height(heightw - heighth - heightadmin_bar);
                        }
                        /*if(resp.data.length > parseInt(ae_globals.posts_per_page))
                        {
                              $(".result-pagination .prp" ).show();
                              $(".result-pagination .number_place_page" ).text('1- '+ parseInt(ae_globals.posts_per_page));
                              $(".result-pagination .total_location" ).text(resp.data.length);
                        }
                        else
                        {
                            $(".result-pagination .prp" ).hide();
                        }*/
                    }
                });
        },
        pagination : function(result,pages){
          var view = this;
            var page = 1;
           if( typeof pages !== 'undefined')
           {
              page = pages;
           }
            var total = view.resultPlace.length; //total items in array    
            var limit = parseInt(ae_globals.posts_per_page); //per page  
            var totalPages = Math.ceil( total/ limit ); //calculate total pages
            page = Math.max(page, 1); //get 1 page when $_GET['page'] <= 0
            page = Math.min(page, totalPages); //get last page when $_GET['page'] > $totalPages
            var offset = (page - 1) * limit;
            if( offset < 0 ) offset = 0;
            var DataPlace = result.slice(offset, limit*page);
            if(total%limit==0)
            {
              view.Totalpagination = total/limit;
            }
            else{
             view.Totalpagination = parseInt(total/limit)+1;
            }
            return DataPlace;
        },
        selectPage : function(pageIndex) {
          var view = this;
          view.currentPage = pageIndex;
          view.draw();
          this.getdataRadius(view.resultPlace,view.currentPage + 1);
        },
       getInterval : function() {
                var halfDisplayed = 1.5,
                displayedPages = 3;
                var view =this;
                return {
                    start: Math.ceil(view.currentPage > halfDisplayed ? Math.max(Math.min(view.currentPage - halfDisplayed, ( view.Totalpagination - displayedPages)), 0) : 0),
                    end: Math.ceil(view.currentPage> halfDisplayed ? Math.min(view.currentPage + halfDisplayed,  view.Totalpagination) : Math.min(displayedPages,  view.Totalpagination))
                };
        },
        appendItem : function(pageIndex, opts) {
                var options, link;
                var view =this;
                pageIndex = pageIndex < 0 ? 0 : (pageIndex < view.Totalpagination ? pageIndex : view.Totalpagination - 1);

                options = jQuery.extend({
                    text: pageIndex + 1,
                    classes: ''
                }, opts || {});

                if (pageIndex == view.currentPage) {
                    link = jQuery('<a href="javascript:void(0)" class ="page-link current">' + (options.text) + '</a>');
                } else {
                    link = jQuery('<a href="javascript:void(0)" class="page-link">' + (options.text) + '</a>');
                    link.bind('click', function() {
                        view.selectPage(pageIndex);
                        if(pageIndex != 0) 
                         $( ".main-pagination a" ).first().removeClass("current");  
                    });
                }

                if (options.classes) {
                    link.addClass(options.classes);
                }
                $('.main-pagination').append(link);
        },
        draw : function() {
              var view =this;
              var edges = 2;
               jQuery('.main-pagination').empty();
                var interval = view.getInterval(),
                    i;
                // Generate Prev link
                if (view.currpage > 1) {
                    view.appendItem(view.currentPage - 1, {
                        text: 'Prev',
                        classes: 'prev'
                    });
                }
                // Generate start edges
                if (interval.start > 0 && edges > 0) {
                    var end = Math.min(edges, interval.start);
                    for (i = 0; i < end; i++) {
                        view.appendItem(i);
                    }
                    if (edges < interval.start) {
                         $('.main-pagination').append('<span class="ellipse">...</span>');
                    }
                }

                // Generate interval links
                for (i = interval.start; i < interval.end; i++) {
                   view.appendItem(i);
                }
                $( ".main-pagination a" ).first().addClass("current");
                var period_val = $(".main-pagination a.current").text();
                view.currpage = parseInt(period_val.match(/\d+/)[0]);
                // Generate end edges
                if (interval.end <  view.Totalpagination && edges > 0) {
                    if ( view.Totalpagination - edges > interval.end) {
                        $('.main-pagination').append('<span class="ellipse">...</span>');
                    }
                    var begin = Math.max( view.Totalpagination - edges, interval.end);
                    for (i = begin; i <  view.Totalpagination; i++) {
                       view.appendItem(i);
                    }
                }
                if(view.currpage !== view.Totalpagination)
                view.appendItem(view.currpage, {
                    text: 'Next',
                    classes: 'next'
                });
        },
        get_bound_distance : function() {
            var bounds = new google.maps.LatLngBounds();
            var center = bounds.getCenter();
            var ne = bounds.getNorthEast();

            // r = radius of the earth in statute miles
            var r = 3378;  

            // Convert lat or lng from decimal degrees into radians (divide by 57.2958)
            var lat1 = center.lat(); 
            var lon1 = center.lng();
            var lat2 = ne.lat();
            var lon2 = ne.lng();

            // distance = circle radius from center to Northeast corner of bounds
            var dis = r * Math.acos(Math.sin(lat1) * Math.sin(lat2) + 
              Math.cos(lat1) * Math.cos(lat2) * Math.cos(lon2 - lon1));
            return dis*1000;
        },

        check_location_distance: function(){
          var view = this;
          var target       = $('.job_listings');
          var form         = target.find( '.job_filters' );         
          var latitude = form.find( 'input[name="latitude"]').val();
          var longitude = form.find( 'input[name="longitude"]').val();
          var address = form.find( 'input[id="address"]').val();

          if(latitude && longitude && address != '')
          {  
               return {
                lat: latitude,
                lng: longitude
               }
          }
          else
          {
            if(view.options.latitude && view.options.longitude)
            {
               return {
                lat: view.options.latitude,
                lng: view.options.longitude
               }
            }
            else
            {
                 return {
                lat: this.map.getCenter().lat(),
                lng: this.map.getCenter().lng()
               }
            }
          }
        },
        /**
         * after successful request map data
         */
        ajaxSuccess: function(data,result) {
            var view = this;
            var bounds = new google.maps.LatLngBounds();
            var distance_unit = view.check_location_distance();
            var dist; 
            for (var i = 0; i < data.length; i++) {
                var content = '',
                    // place latitude and longitude
                    latLng = new google.maps.LatLng(data[i].latitude, data[i].longitude),
                    // get place category
                    term = data[i].term_taxonomy_id,
                    color = view.colors[term],
                    fontClass = view.fontClass[term],
                    icon = view.icons[term];
                bounds.extend(latLng);
                if (typeof color === 'undefined') {
                    color = '#F59236';
                }
                if (typeof fontClass === 'undefined') {
                    fontClass = 'fa-map-marker';
                }
                if (typeof icon === 'undefined') {
                    var icon = {
                        path: 'M 50 -119.876 -50 -119.876 -50 -19.876 -13.232 -19.876 0.199 0 13.63 -19.876 50 -19.876 Z',
                        fillColor: '#F59236',
                        fillOpacity: 1,
                        scale: 0.3,
                        strokeColor: '#F59236',
                        strokeWeight: 0
                    };
                }
                if (typeof icon.fillColor === 'undefined') {
                     icon.fillColor = '#000';
                     icon.strokeColor = '#000';
                }
                dist = distance(data[i].latitude, data[i].longitude, distance_unit.lat, distance_unit.lng);
                var marker = new MarkerWithLabel({
                    position: latLng,
                    icon: icon,
                    labelContent: "<span><i style='color:" + color + ";' class='fa " + fontClass + "'></i><span>",
                    labelAnchor: view.labelAnchor,
                    labelClass: "map-labels", // the CSS class for the label
                    labelStyle: {
                        opacity: 1.0
                    },
                    category: data[i].place_category,
                    location: data[i].location,
                    distance: dist,
                    term : term
                });
                // set marker content using in multichoice
                marker.content = '';
                marker.ID = data[i].ID;
                view.markers.push(marker);
                // attach info window
                view.attachMarkerInfowindow(marker, content, data[i]);
                if ( typeof view.model !== 'undefined' && view.model.get('ID') == data[i]['ID'] && ae_globals.is_search != 1 && view.loaded !== true ) {
                    var model_data = view.model.toJSON(),
                        content = view.template(model_data);
                    marker.content = content;
                    view.map.setCenter(latLng);
                    /**
                     * set content for info window
                     */
                    view.infoWindow.setContent(content);
                    // set border color for info window
                    view.infoWindow.setBorderColor(color);
                    // open info window
                    view.infoWindow.open(this.map, marker);
                    view.map.setZoom(15);
                    google.maps.event.addListener(view.map,'idle',function(){
                        // display rating on map after the map is loaded
                        $('.infowindow .rate-it').raty({
                            readOnly: true,
                            half: true,
                            score: function() {
                                return $(this).attr('data-score');
                            },
                            hints: raty.hint
                        });
                    });
                    // set the flag to check the single marker info window is trigger or not
                    view.loaded = true;
                }
            }
            if(typeof view.markerCluster !== 'undefined' ) {
                view.markerCluster.clearMarkers();
            }
            // init map cluster
            view.markerCluster = new MarkerClusterer(view.map, view.markers, {
                zoomOnClick: true,
                gridSize: 20
            });
            // bind event click on cluster for multi marker in a position
            view.markerCluster.onClick = function(icon) {
                return view.multiChoice(icon.cluster_);
            };
            if (typeof view.model === 'undefined' && parseInt(ae_globals.fitbounds)) {
                //  Fit these bounds to the map
                view.map.fitBounds(bounds);

                // Set min-zoom of map is 1
                if(view.map.getZoom() == 0) view.map.setZoom(1);
                // Set center of fitbounds
                //view.map.setCenter(bounds.getCenter());
            }
            if (ae_globals.is_search && parseInt(ae_globals.fitbounds)){
                view.map.fitBounds(bounds);
            }
        },
        /**
         * attach info window to a marker
         * @param marker google marker object
         * @param content the info window content
         * @param data object data
         */
        attachMarkerInfowindow: function(marker, content, data) {
            var view = this,
                term = data.term_taxonomy_id;
            google.maps.event.addListener(marker, 'click', function() {
                if( marker.content === '' ) {
                    $.ajax({
                        type: 'get',
                        url: ae_globals.ajaxURL,
                        data: {action : 'de-get-map-info', ID : marker.ID},
                        beforeSend: function() {
                            view.blockUi.block($('#google_canvas'));
                        },
                        success: function(resp) {
                            view.blockUi.unblock();
                            $('div.map-element').html(resp.data.content);
                            if(!ae_globals.is_single){
                                $('.rate-it').raty({
                                    readOnly: true,
                                    half: true,
                                    score: function() {
                                        return $(this).attr('data-score');
                                    },
                                    hints: raty.hint
                                });
                            }
                            var content = $('div.map-element').html();
                            /**
                             * set content for info window
                             */
                            view.infoWindow.setContent(content);
                            // set border color for info window
                            var color = view.colors[term];
                            if (typeof color === 'undefined') {
                                color = '#F59236';
                            }
                            view.infoWindow.setBorderColor(color);
                            // open info window
                            view.infoWindow.open(view.map, marker);
                            marker.content = content;

                        }
                    });
                }else {
                    view.infoWindow.setContent(marker.content);
                    // set border color for info window
                    var color = view.colors[term];
                    if (typeof color === 'undefined') {
                        color = '#F59236';
                    }
                    $('.rate-it').raty({
                        readOnly: true,
                        half: true,
                        score: function() {
                            return $(this).attr('data-score');
                        },
                        hints: raty.hint
                    });

                    view.infoWindow.setBorderColor(color);
                    // open info window
                    view.infoWindow.open(view.map, marker);
                }
            });
        },
        /**
         * bind multi marker detail to cluster
         */
        multiChoice: function(clickedCluster) {
            var view = this;
            if (clickedCluster.getMarkers().length > 1) {
                var markers = clickedCluster.getMarkers();
                var market_id = [];
                for (var i = 0; i < markers.length; i++) {
                    market_id.push(markers[i].ID);
                }
                if(markers[0].content === ''){
                    $.ajax({
                        type: 'get',
                        url: ae_globals.ajaxURL,
                        data: {action : 'de-get-map-info', IDs : market_id},
                        beforeSend: function() {
                            view.blockUi.block($('#google_canvas'));
                        },
                        success: function(resp) {
                            view.blockUi.unblock();
                            $('div.map-element').html(resp.data.content);
                            $('.rate-it').raty({
                                readOnly: true,
                                half: true,
                                score: function() {
                                    return $(this).attr('data-score');
                                },
                                hints: raty.hint
                            });
                            var content = $('div.map-element').html();
                            /**
                             * set content for info window
                             */
                            view.infoWindow.setBorderColor('#F59236');
                            view.infoWindow.setContent('<div class="jobs-wrapper">' + content + '</div>');
                            view.infoWindow.open(view.map, markers[0]);
                            markers[0].content = '<div class="jobs-wrapper">' + content + '</div>';
                            // return false;
                        }
                    });
                }
                else{
                    view.infoWindow.setBorderColor('#F59236');
                    view.infoWindow.setContent(markers[0].content);
                    view.infoWindow.open(view.map, markers[0]);
                }

            }
            return true;
        },
        /**
         * catch event when get user position and set map to his location
         */
        setCenter: function(coords) {
            var latLng = new google.maps.LatLng(coords.latitude, coords.longitude);
            this.map.setCenter(latLng);
        },
        setRadius: function(coords) {
            var view = this;
            this.setCenter(coords);          
            view.show_pins(coords.radius);       
        },
        /**
         * create button on Map
         */
        googleMapButton: function (text, className) {
            "use strict";
            var controlDiv = document.createElement("div");
            controlDiv.className = className;
            controlDiv.index = 1;
            controlDiv.style.padding = "10px";
            // set CSS for the control border.
            var controlUi = document.createElement("div");
            controlUi.style.backgroundColor = "rgb(255, 255, 255)";
            controlUi.style.color = "#565656";
            controlUi.style.cursor = "pointer";
            controlUi.style.textAlign = "center";
            controlUi.style.boxShadow = "rgba(0, 0, 0, 0.298039) 0px 1px 9px -1px";
            controlDiv.appendChild(controlUi);
            // set CSS for the control interior.
            var controlText = document.createElement("div");
            controlText.style.fontFamily = "Roboto,Arial,sans-serif";
            controlText.style.fontSize = "11px";
            controlText.style.paddingTop = "8px";
            controlText.style.paddingBottom = "8px";
            controlText.style.paddingLeft = "8px";
            controlText.style.paddingRight = "8px";
            controlText.innerHTML = text;
            controlUi.appendChild(controlText);
            $(controlUi).on("mouseenter", function () {
                controlUi.style.backgroundColor = "rgb(235, 235, 235)";
                controlUi.style.color = "#000";
            });
            $(controlUi).on("mouseleave", function () {
                controlUi.style.backgroundColor = "rgb(255, 255, 255)";
                controlUi.style.color = "#565656";
            });
            return controlDiv;
        },
        FullScreenControl: function (map, enterFull, exitFull) {
            var view = this;
            "use strict";
            if (enterFull === void 0) { enterFull = null; }
            if (exitFull === void 0) { exitFull = null; }
            if (enterFull == null) {
                enterFull = "Full screen";
            }
            if (exitFull == null) {
                exitFull = "Exit full screen";
            }
            var controlDiv = view.googleMapButton(enterFull, "fullScreen");
            var fullScreen = false;
            var interval;
            var mapDiv = view.map.getDiv();
            var divStyle = mapDiv.style;
            if (mapDiv.runtimeStyle) {
                divStyle = mapDiv.runtimeStyle;
            }
            var originalPos = divStyle.position;
            var originalWidth = divStyle.width;
            var originalHeight = divStyle.height;
            // ie8 hack
            if (originalWidth === "") {
                originalWidth = mapDiv.style.width;
            }
            if (originalHeight === "") {
                originalHeight = mapDiv.style.height;
            }
            var originalTop = divStyle.top;
            var originalLeft = divStyle.left;
            var originalZIndex = divStyle.zIndex;
            var bodyStyle = document.body.style;
            if (document.body.runtimeStyle) {
                bodyStyle = document.body.runtimeStyle;
            }
            var originalOverflow = bodyStyle.overflow;
            controlDiv.goFullScreen = function () {
                var center = view.map.getCenter();
                mapDiv.style.position = "fixed";
                mapDiv.style.width = "100%";
                mapDiv.style.height = "100%";
                mapDiv.style.top = "30";
                mapDiv.style.left = "0";
                mapDiv.style.zIndex = "10000";
                document.body.style.overflow = "hidden";
                $(controlDiv).find("div div").html(exitFull);
                fullScreen = true;
                google.maps.event.trigger(view.map, "resize");
                view.map.setCenter(center);
                // this works around street view causing the map to disappear, which is caused by Google Maps setting the 
                // css position back to relative. There is no event triggered when Street View is shown hence the use of setInterval
                interval = setInterval(function () {
                    if (mapDiv.style.position !== "fixed") {
                        mapDiv.style.position = "fixed";
                        google.maps.event.trigger(map, "resize");
                    }
                }, 100);
            };
            controlDiv.exitFullScreen = function () {
                var center = view.map.getCenter();
                if (originalPos === "") {
                    mapDiv.style.position = "relative";
                } else {
                    mapDiv.style.position = originalPos;
                }
                mapDiv.style.width = originalWidth;
                mapDiv.style.height = originalHeight;
                mapDiv.style.top = originalTop;
                mapDiv.style.left = originalLeft;
                mapDiv.style.zIndex = originalZIndex;
                document.body.style.overflow = originalOverflow;
                $(controlDiv).find("div div").html(enterFull);
                fullScreen = false;
                google.maps.event.trigger(view.map, "resize");
                view.map.setCenter(center);
                clearInterval(interval);
            };
            // setup the click event listener
            google.maps.event.addDomListener(controlDiv, "click", function () {
                if (!fullScreen) {
                    controlDiv.goFullScreen();
                } else {
                    controlDiv.exitFullScreen();
                }
            });
            return controlDiv;
        }
    });
	function IsJsonString(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }
    function distance(lat1, lon1, lat2, lon2) {
        var dist =  Math.acos( Math.sin(lat1 * 0.0175) * Math.sin( lat2 * 0.0175) + Math.cos(lat1 * 0.0175) * Math.cos( lat2 * 0.0175 ) * Math.cos( (lon2  * 0.0175 ) - ( lon1 * 0.0175 ) ) ) * 3959;       
        return dist;
    }
    function distance_address(lat1, lon1, lat2, lon2, ver) {
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
        if(ver === 1){
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
        }
        return dist;
    }
})(AE.Views, AE.Models, jQuery, Backbone, AE.Collections);