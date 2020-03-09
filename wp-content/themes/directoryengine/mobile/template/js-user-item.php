<script type="text/template" id="de-user-item">
<div class="row">
	<div class="col-xs-12 user-list-item-profile">
    	<a href="{{= author_url }}" class="list-user-page-avatar">{{= avatar }}</a>
        <div class="info-name-user">
        	<a href="{{= author_url }}" class="name">{{= display_name }}</a>
            <span class="location-user">
                <i class="fa fa-map-marker"></i>
                <# if(location !== ''){ #>
                    {{= location}}
                <# } else{ #>
                    <?php _e("Earth", ET_DOMAIN); ?>
                <# } #>
                </span>
        </div>
    </div>
    <div class="col-xs-12 user-list-item-places">
    	<ul class="list-item-place-user">
        <# if(place_list.length > 0){ #>
            <# for(i = 0 ; i < place_list.length; i++){ #>
                <# if(i < 3 ){ #>
                    <li><a href="{{= place_list[i].permalink }}"><img src="{{= place_list[i].the_post_thumnail }}"></a></li>
                <# }else if(i == 4){  #>
                    <li><a href="{{= author_url }}" class="last-item-place-user">+</li>
                <# } #>
            <# } #>
        <# }else{#>
            <?php _e("There's no place", ET_DOMAIN); ?>
        <# } #>    
        </ul>
    </div>
</div>
</script>