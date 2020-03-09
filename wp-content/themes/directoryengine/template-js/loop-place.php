<script type="text/template" id="ae-place-loop">
<?php global $user_ID;?>
    <div class="place-wrapper">
        <div class="hidden-img">
		<!-- button event for admin control  -->
		<?php if(ae_user_can( 'edit_others_posts' ) || (is_author() && $user_ID == get_query_var( 'author' ) ) ) { ?>
		<ol class="edit-place-option">            
			<li style="display:inline-block"><a href="#" class="action" data-action="edit"><i class="fa fa-pencil"></i></a></li>
			<# if(post_status === 'pending'){ #>
			<li style="display:inline-block"><a href="#" class="action" data-action="reject"><i class="fa fa-times"></i></a></li>
			<li style="display:inline-block"><a href="#" class="action" data-action="approve"><i class="fa fa-check"></i></a></li>
			<# } #>
			<# if( post_status === 'publish') { #>
			<li style="display:inline-block"><a href="#" class="action" data-action="archive"><i class="fa fa-trash-o"></i></a></li>                
			<# } #>
		</ol>
		<?php } ?>
		
        <# if(the_post_thumnail) { #>
            <a href="{{= permalink }}" class="img-place" title="{{= post_title }}">
                <img src="{{= the_post_thumnail }}">
                <# if(ribbon != '' ) {  #>
                <div class="cat-{{= place_category[0] }}" >
                    <div class="ribbon">
                        <span class="ribbon-content" title="{{= ribbon }}" >{{= ribbon }}</span>
                    </div>
                </div>
                <# } #>
            </a>
        <# } #>      
        </div>
        <div class="place-detail-wrapper">  
        	<h2 class="title-place"><a href="{{= permalink }}" title="{{= post_title }}">{{= post_title }}</a></h2>
            <span class="address-place"><i class="fa fa-map-marker"></i><span class="distance"></span> {{= et_full_location }}</span>
            <div class="content-place">{{= trim_post_content }}</div>
            <div class="clearfix rate-view">
                <div class="rate-it rate-cus" data-score="{{= rating_score_comment }}"></div>
                <?php if( ae_get_option("enable_view_counter",false) ): ?>
                    <div class="view-count limit-display  tooltip-style"  data-toggle="tooltip" data-placement="top" title="{{= view_count}}">
                        <i class="fa fa-eye"></i> {{= view_count.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")}}
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</script>