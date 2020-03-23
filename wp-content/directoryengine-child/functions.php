<?php

add_action('widgets_init', 'de_register_sidebars_2');
function de_register_sidebars_2() {
/**
    * Creates a sidebar on top List
    * @param string|array  Builds Sidebar based off of 'name' and 'id' values.
    */
    $args = array(
        'name'          => __( 'Top Fullwidth Sidebar 2', ET_DOMAIN ),
        'id'            => 'de-fullwidth-top-2',
        'description'   => '',
        'class'         => '',
        'before_widget' => '<div id="%1$s" class=" widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>'
    );
    register_sidebar( $args );

$args = array(
        'name'          => __( 'Top Fullwidth Sidebar 3', ET_DOMAIN ),
        'id'            => 'de-fullwidth-top-3',
        'description'   => '',
        'class'         => '',
        'before_widget' => '<div id="%1$s" class=" widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>'
    );
    register_sidebar( $args );
}