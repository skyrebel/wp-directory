<?php
get_header();
?>
    <!-- List Place -->
    <div id="list-places-pending-wrapper">
        <div class="container">
            <?php 
                if(ae_user_can('manage_options')) {
                    get_template_part('template/list','pending-place');
                }
            ?>
        </div>
    </div>
    <!-- List Place / End -->
<?php
get_footer();