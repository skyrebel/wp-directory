<script type="text/template" id="ae-place-notification">
    <div class="pending-place-wrap">
        <a class="pending-place-img" href="{{=permalink }}" title="{{=post_title}}" >
           <img src="{{=the_post_thumnail}}" alt="{{=post_title}}" />
        </a>
        <div class="pending-place-content">
            <div class="pending-place-title-wrap">
                <span style="background-color: #d35400; color: #FFF;">{{= tax_input['place_category'][0].name }}</span>
                <h2><a href="<?php the_title();?>" title="<?php the_title();?>">{{=post_title}}</a></h2>
                <p><i class="fa fa-map-marker"></i>{{= et_full_location }}</p>
            </div>
           <div class="pending-place-location-wrap">
                <div class="pending-place-location">
                    <p><i class="fa fa-globe"></i>{{= tax_input['location'][0].name }}</p>
                    <p><i class="fa fa-phone"></i>{{= et_phone }}</p>
                </div>
            </div>
            <div class="pending-place-author-wrap">
                <div class="pending-place-author">
                    <p>
                        <a href="">
                            {{= avatar_author_search}}
                            {{= display_name}}
                        </a>
                    </p>
                </div>
            </div>
            <div class="pending-place-status">
                <span class="unpaid">{{=paid_status}}</span>
            </div>
            <div class="pending-place-action-wrap">
                <div class="pending-place-action action-pending-place">
                    <span class="action-approve action" data-action="approve"><i class="fa fa-check"></i></span>
                    <span class="action-remove action" data-action="reject"><i class="fa fa-times"></i></span>
                </div>
            </div>
        </div>
    </div>
</script>