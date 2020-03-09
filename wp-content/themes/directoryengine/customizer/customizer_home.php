<?php 
add_action( 'customize_register', 'load_multicategory' );
add_action( 'customize_register','demo_section');
function demo_section( $wp_manager )
{
    
    $wp_manager->add_section( "main_color" , array(
        'title'             => __( 'Color', ET_DOMAIN ),
        'priority' => 11,
    ));
    $wp_manager->add_setting( "header_background_color" , array(
        'default'           =>  "#fff",
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));
    $wp_manager->add_control( new WP_Customize_Color_Control( $wp_manager,'header_background_color' , array(
        'label'             =>   __( 'Header Background', ET_DOMAIN ),
        'section'           =>  'main_color',
        'settings' => 'header_background_color',
        'priority' => 1,
    )));
    $wp_manager->add_setting( "body_bg_color" , array(
        'default'           =>  "#ecf0f1",
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));
    $wp_manager->add_control( new WP_Customize_Color_Control( $wp_manager,'body_bg_color' , array(
        'label'             =>   __( 'Body Background', ET_DOMAIN ),
        'section'           =>  'main_color',
        'settings' => 'body_bg_color',
        'priority' => 1,
    )));
     $wp_manager->add_setting( "footer_background_color" , array(
        'default'           =>  "#34495e",
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));
    $wp_manager->add_control( new WP_Customize_Color_Control( $wp_manager,'footer_background_color' , array(
        'label'             =>   __( 'Footer Background', ET_DOMAIN ),
        'section'           =>  'main_color',
        'settings' => 'footer_background_color',
        'priority' => 1,
    )));
    $wp_manager->add_setting( "copyright_bg_color" , array(
        'default'           =>  "#34495e",
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));
    $wp_manager->add_control( new WP_Customize_Color_Control( $wp_manager,'copyright_bg_color' , array(
        'label'             =>   __( 'Copyright Background', ET_DOMAIN ),
        'section'           =>  'main_color',
        'settings' => 'copyright_bg_color',
        'priority' => 1,
    )));
    $wp_manager->add_setting( "main_color_config" , array(
        'default'           =>  "#1d83d5",
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));
    $wp_manager->add_control( new WP_Customize_Color_Control( $wp_manager,'main_color_config' , array(
        'label'             =>   __( 'Main Color', ET_DOMAIN ),
        'section'           =>  'main_color',
        'settings' => 'main_color_config',
        'priority' => 1,
    )));
    //////// Home Pages
    $wp_manager->add_panel( 'panel_id', array(
        'priority' => 10,
        'title' => __( 'Block Home Pages', ET_DOMAIN ),
        'description' => __( 'Description of what this panel does.',ET_DOMAIN ),
    ) );
    /*****************
     * Block Search  *
     *****************/
    $wp_manager->add_section( 'search_section', array(
                    'title'          => __('Block Search',ET_DOMAIN ),
                    'priority'       => 1,
                    'panel' => 'panel_id',
                ) );
    $wp_manager->add_setting('block_search_background',array(
        'default' => '',
        ));
    $wp_manager->add_control( new WP_Customize_Cropped_Image_Control( $wp_manager,'block_search_background', array(
            'section'     => 'search_section',
            'label'       => __( 'Background Image',ET_DOMAIN ),
            'description' => __('The optimal dimensions are 1920x530 pixels.', ET_DOMAIN),           
            'width' => 1920,
            'height' => 580,
            'priority'    => 1
        ) ) );
    $wp_manager->add_setting( 'title_block_1', array(
            'default'        => __('Discover',ET_DOMAIN ),
        ) );

    $wp_manager->add_control( 'title_block_1', array(
        'label'   => __( 'Title',ET_DOMAIN ),
        'section' => 'search_section',
        'type'    => 'text',
        'priority' => 1
    ) );
    $wp_manager->add_setting( 'des_block_1', array(
            'default'        => __('Your Need',ET_DOMAIN ),
        ) );
    $wp_manager->add_control( 'des_block_1', array(
        'label'   => __( 'Description',ET_DOMAIN ),
        'section' => 'search_section',
        'type'    => 'textarea',
        'priority' => 1
    ) );

    /*******************
     * Block Location *
     *******************/
        $wp_manager->add_section( 'customiser_demo_section', array(
                    'title'          => __('Block Place Collection',ET_DOMAIN ),
                    'priority'       => 1,
                    'panel' => 'panel_id',

                ) );

        // Textbox control
        $wp_manager->add_setting( 'textbox_setting', array(
            'default'        => __('Place Collection',ET_DOMAIN ),
        ) );

        $wp_manager->add_control( 'textbox_setting', array(
            'label'   => __( 'Title',ET_DOMAIN ),
            'section' => 'customiser_demo_section',
            'type'    => 'text',
            'priority' => 1
        ) );
        // Checkbox control
        $wp_manager->add_setting( 'checkbox_setting', array(
            'default'        => '1',
        ) );

        $wp_manager->add_control( 'checkbox_setting', array(
            'label'   => __( 'Show areas count',ET_DOMAIN ),
            'section' => 'customiser_demo_section',
            'type'    => 'checkbox',
            'priority' => 2
        ) );
        // Select control
        $wp_manager->add_setting( 'select_setting', array(
                'default'        => '0',
            ) );

        $wp_manager->add_control( 'select_setting', array(
            'label'   => __( 'Order by',ET_DOMAIN ),
            'section' => 'customiser_demo_section',
            'type'    => 'select',
            'choices' => array("Name", "Slug", "Count"),
            'priority' => 4
        ) );
        $wp_manager->add_setting('get_dropdown_multipicker', array(
             'default'     => array(),
            ));

            $wp_manager->add_control(
          new Category_Dropdown_Custom_Control(
            $wp_manager,
            'get_dropdown_multipicker',
            array(
            'label'   => __('Choose a maximum of 5 locations from the existing locations in the back-end',ET_DOMAIN),
            'section' => 'customiser_demo_section',
            'settings'   => 'get_dropdown_multipicker',
            'priority' => 3
            )
          ));
    /***********************
     * Block popular place *
     ***********************/
        $wp_manager->add_section( 'place_section', array(
                    'title'          => __('Block Popular Places',ET_DOMAIN ),
                    'priority'       => 2,
                    'panel' => 'panel_id',
                ) );
        // Textbox control
        $wp_manager->add_setting( 'title_block_2', array(
            'default'        => __('Popular Places',ET_DOMAIN ),
        ) );
        $wp_manager->add_control( 'title_block_2', array(
            'label'   => __( 'Title',ET_DOMAIN ),
            'section' => 'place_section',
            'type'    => 'text',
            'priority' => 1
        ) );
    /***********************
     * Block Why           *
     ***********************/
         $wp_manager->add_section( 'block_3', array(
            'title'          => __('Block Why',ET_DOMAIN ),
            'priority'       => 3,
            'panel' => 'panel_id',
        ) );
         $wp_manager->add_setting('block_why_background',array(
        'default' => '',
        ));
        $wp_manager->add_control( new WP_Customize_Cropped_Image_Control( $wp_manager,'block_why_background', array(
            'section'     => 'block_3',
            'label'       => __( 'Background Image',ET_DOMAIN ),
            'description' => __('The optimal dimensions are 1920x530 pixels.', ET_DOMAIN),           
            'width' => 1920,
            'height' => 530,
            'priority'    => 1
        ) ) );
         $wp_manager->add_setting( 'title_block_3', array(
            'default'        => __('How DE Work',ET_DOMAIN ),
        ) );
         $wp_manager->add_control( 'title_block_3', array(
            'label'   => __( 'Title',ET_DOMAIN ),
            'section' => 'block_3',
            'type'    => 'text',
            'priority' => 1
        ) );
        block_item_icon($wp_manager,'why','block_3');
    /******************************
     * Block Review and category  *
     ******************************/
        $wp_manager->add_section( 'block_4', array(
            'title'          => __('Block Reviews And Categories',ET_DOMAIN ),
            'priority'       => 3,
            'panel' => 'panel_id',
        ) );
         $wp_manager->add_setting( 'title_block_4_review', array(
            'default'        => __('Review',ET_DOMAIN ),
        ) );
         $wp_manager->add_control( 'title_block_4_review', array(
            'label'   => __( 'Review',ET_DOMAIN ),
            'section' => 'block_4',
            'type'    => 'text',
            'priority' => 1,
            'description' => 'Title Review'
        ) );
        $wp_manager->add_setting( 'number_block_4_review', array(
            'default'        => 4
        ) );
         $wp_manager->add_control( 'number_block_4_review', array(
            'label'   => __( 'Number Place Review',ET_DOMAIN ),
            'section' => 'block_4',
            'type'    => 'number',
            'priority' => 1,
        ) );
        $wp_manager->add_setting( 'title_block_4_cat', array(
            'default'        => __('Categories',ET_DOMAIN ),
        ) );
         $wp_manager->add_control( 'title_block_4_cat', array(
            'label'   => __( 'Categories',ET_DOMAIN ),
            'section' => 'block_4',
            'type'    => 'text',
            'priority' => 1,
            'description' => 'Title Categories'
        ) );
        $wp_manager->add_setting( 'checkbox_count_cat', array(
            'default'        => '1',
        ) );

        $wp_manager->add_control( 'checkbox_count_cat', array(
            'label'   => __( 'Show posts count',ET_DOMAIN ),
            'section' => 'block_4',
            'type'    => 'checkbox',
            'priority' => 2
        ) );
        $wp_manager->add_setting( 'order_block_4_cat', array(
            'default'        => '0'
        ) );
         $wp_manager->add_control( 'order_block_4_cat', array(
             'label'   => __( 'Order by',ET_DOMAIN ),
            'section' => 'block_4',
            'type'    => 'select',
            'choices' => array("Name", "Slug", "Count"),
            'priority' => 1
        ) );
    /****************
     * Block How    *
     ****************/ 
         $wp_manager->add_section( 'block_5', array(
            'title'          => __('Block How',ET_DOMAIN ),
            'priority'       => 3,
            'panel' => 'panel_id',
        ) );
         $wp_manager->add_setting( 'title_block_5', array(
            'default'        => __('How DE Work',ET_DOMAIN ),
        ) );
         $wp_manager->add_control( 'title_block_5', array(
            'label'   => __( 'Title',ET_DOMAIN ),
            'section' => 'block_5',
            'type'    => 'text',
            'priority' => 1
        ) );
        block_item_icon($wp_manager,'how','block_5');
    /****************
     * Block Footer *
     ****************/ 
     $wp_manager->add_section( 'block_footer', array(
            'title'          => __('Block Footer',ET_DOMAIN ),
            'priority'       => 3,
            'panel' => 'panel_id',
        ) );
         $wp_manager->add_setting('block_footer_background',array(
        'default' => '',
        ));
        $wp_manager->add_control( new WP_Customize_Cropped_Image_Control( $wp_manager,'block_footer_background', array(
            'section'     => 'block_footer',
            'label'       => __( 'Background',ET_DOMAIN ),
            'description' => __('Leave empty if you want to use the default icon. The optimal dimensions are 85x85 pixels.', ET_DOMAIN),           
            'width' => 1920,
            'height' => 380,
            'priority'    => 1
        ) ) );
         $wp_manager->add_setting( 'title_block_footer', array(
            'default'        => __('You have several locations and save your time',ET_DOMAIN ),
        ) );
         $wp_manager->add_control( 'title_block_footer', array(
            'label'   => __( 'Title',ET_DOMAIN ),
            'section' => 'block_footer',
            'type'    => 'text',
            'priority' => 1
        ) ); 
        $wp_manager->add_setting( 'des_block_footer', array(
            'default'        => __('Lorem ipsum dolor sit amet, consectetur adipisicing elit',ET_DOMAIN ),
        ) );
        $wp_manager->add_control( 'des_block_footer', array(
            'label'   => __( 'Description',ET_DOMAIN ),
            'section' => 'block_footer',
            'type'    => 'textarea',
            'priority' => 1
        ) );
        $wp_manager->add_setting( 'title_button_block_footer', array(
            'default'        => __('Get Started',ET_DOMAIN ),
        ) );
         $wp_manager->add_control( 'title_button_block_footer', array(
            'label'   => __( 'Button text',ET_DOMAIN ),
            'section' => 'block_footer',
            'type'    => 'text',
            'priority' => 1
        ) ); 
}
function block_item_icon( $wp_manager,$name_section,$section)
{
    for($i = 1; $i<=3 ; $i++)
    {
        $title_item = $name_section.'_block_tile_item_'.$i;
        $des_item = $name_section.'_block_des_item_'.$i;
        $image_item = $name_section.'_block_image_item_'.$i;
        $wp_manager->add_setting($image_item,array(
            'default' => '',
            ));
        $wp_manager->add_control( new WP_Customize_Cropped_Image_Control( $wp_manager, $image_item, array(
            'section'     => $section,
            'label'       => __( 'Item'.$i,ET_DOMAIN ),
            'description' => __('Leave empty if you want to use the default icon. The optimal dimensions are 85x85 pixels.', ET_DOMAIN),           
            'width'       => 85,
            'height'      => 85,
            'flex_width'  => false,
            'flex_height' => false,
            'priority'    => 1
        ) ) );
        $wp_manager->add_setting( $title_item, array(
            'default'        => __('Your Need',ET_DOMAIN ),
        ) );
        $wp_manager->add_control( $title_item, array(
            'label'   => __( 'Title',ET_DOMAIN ),
            'section' => $section,
            'type'    => 'text',
            'priority' => 1
        ) );
        $wp_manager->add_setting( $des_item, array(
            'default'        => __('Your Need',ET_DOMAIN ),
        ) );
        $wp_manager->add_control( $des_item, array(
            'label'   => __( 'Description',ET_DOMAIN ),
            'section' => $section,
            'type'    => 'textarea',
            'priority' => 1
        ) );
    }
}
function load_multicategory(){
    if ( ! class_exists( 'WP_Customize_Control' ) )
    return NULL;
 class Category_Dropdown_Custom_Control extends WP_Customize_Control
 {
public function enqueue() {
      wp_enqueue_script( 'chosen' );
    }
    /**
     * Render the content of the category dropdown
     *
     * @return HTML
     */
    public function render_content()
       {
                ?>
                    <label>
                      <span class="customize-category-select-control"><?php echo esc_html( $this->label ); ?></span>
                    </label>
                     <?php 
                            ae_tax_dropdown( 'location' , 
                                                    array(  'attr' => 'data-customize-setting-link="get_dropdown_multipicker" multiple data-placeholder="'.__("Location", ET_DOMAIN).'"', 
                                                            'class' => 'chosen multi-tax-item tax-item required post-place-category',
                                                            'hide_empty' => false, 
                                                            'hierarchical' => true , 
                                                            'id' => 'place_location' , 
                                                            'show_option_all' => false 
                                                        ) 
                                                ) ;?> 
                    <script>jQuery(document).ready(function($) { 
                        $('.multi-tax-item').chosen({
                          width: '100%',
                          max_selected_options: 5,
                          inherit_select_classes: true
                      });
                        $('.multi-tax-item').on('change', function(e){
                          if ( 0 === $(this).find("option:selected").length ){
                            var api = wp.customize;
                            var control =wp.customize.control('get_dropdown_multipicker');
                            control.setting.set([]);
                          }
                        });
                    });
                    </script>
                <?php
       }
 }
}
function theme_slug_customizer_custom_control_css() { 
  ?>
  <style>
  .chosen-container{position:relative;display:inline-block;vertical-align:middle;font-size:13px;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}.chosen-container *{box-sizing:border-box}.chosen-container .chosen-drop{position:absolute;top:100%;left:-9999px;z-index:1010;width:100%;border:1px solid #aaa;border-top:0;background:#fff;box-shadow:none}.chosen-container.chosen-with-drop .chosen-drop{left:0}.chosen-container a{cursor:pointer}.chosen-container .search-choice .group-name,.chosen-container .chosen-single .group-name{margin-right:4px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;font-weight:normal;color:#999999}.chosen-container .search-choice .group-name:after,.chosen-container .chosen-single .group-name:after{content:":";padding-left:2px;vertical-align:top}.chosen-container-single .chosen-single{position:relative;display:block;overflow:hidden;padding:0 0 0 8px;height:25px;border:1px solid #aaa;border-radius:5px;background-color:#fff;background:linear-gradient(#fff 20%, #f6f6f6 50%, #eee 52%, #f4f4f4 100%);background-clip:padding-box;box-shadow:0 0 3px #fff inset,0 1px 1px rgba(0,0,0,0.1);color:#444;text-decoration:none;white-space:nowrap;line-height:24px}.chosen-container-single .chosen-default{color:#999}.chosen-container-single .chosen-single span{display:block;overflow:hidden;margin-right:26px;text-overflow:ellipsis;white-space:nowrap}.chosen-container-single .chosen-single-with-deselect span{margin-right:38px}.chosen-container-single .chosen-single abbr{position:absolute;top:6px;right:26px;display:block;width:12px;height:12px;font-size:1px}.chosen-container-single .chosen-single abbr:hover{background-position:-42px -10px}.chosen-container-single.chosen-disabled .chosen-single abbr:hover{background-position:-42px -10px}.chosen-container-single .chosen-single div{position:absolute;top:0;right:0;display:block;width:18px;height:100%}.chosen-container-single .chosen-single div b{display:block;width:100%;height:100%}.chosen-container-single .chosen-search{position:relative;z-index:1010;margin:0;padding:3px 4px;white-space:nowrap}.chosen-container-single .chosen-search input[type="text"]{margin:1px 0;padding:4px 20px 4px 5px;width:100%;height:auto;outline:0;border:1px solid #aaa;font-size:1em;font-family:sans-serif;line-height:normal;border-radius:0}.chosen-container-single .chosen-drop{margin-top:-1px;border-radius:0 0 4px 4px;background-clip:padding-box}.chosen-container-single.chosen-container-single-nosearch .chosen-search{position:absolute;left:-9999px}.chosen-container .chosen-results{color:#444;position:relative;overflow-x:hidden;overflow-y:auto;margin:0 4px 4px 0;padding:0 0 0 4px;max-height:240px;-webkit-overflow-scrolling:touch}.chosen-container .chosen-results li{display:none;margin:0;padding:5px 6px;list-style:none;line-height:15px;word-wrap:break-word;-webkit-touch-callout:none}.chosen-container .chosen-results li.active-result{display:list-item;width:100% !important;cursor:pointer}.chosen-container .chosen-results li.disabled-result{display:list-item;color:#ccc;cursor:default}.chosen-container .chosen-results li.highlighted{background-color:#3875d7;background-image:linear-gradient(#3875d7 20%, #2a62bc 90%);color:#fff}.chosen-container .chosen-results li.no-results{color:#777;display:list-item;background:#f4f4f4}.chosen-container .chosen-results li.group-result{display:list-item;font-weight:bold;cursor:default}.chosen-container .chosen-results li.group-option{padding-left:15px}.chosen-container .chosen-results li em{font-style:normal;text-decoration:underline}.chosen-container-multi .chosen-choices{position:relative;overflow:hidden;margin:0;padding:0 5px;width:100%;height:auto !important;height:1%;border:1px solid #aaa;background-color:#fff;background-image:linear-gradient(#eee 1%, #fff 15%);cursor:text}.chosen-container-multi .chosen-choices li{float:left;list-style:none}.chosen-container-multi .chosen-choices li.search-field{margin:0;padding:0;white-space:nowrap}.chosen-container-multi .chosen-choices li.search-field input[type="text"]{margin:1px 0;padding:0;height:25px;outline:0;border:0 !important;background:transparent !important;box-shadow:none;color:#999;font-size:100%;font-family:sans-serif;line-height:normal;border-radius:0}.chosen-container-multi .chosen-choices li.search-choice{position:relative;margin:3px 5px 3px 0;padding:3px 20px 3px 5px;border:1px solid #aaa;max-width:100%;border-radius:3px;background-color:#eeeeee;background-image:linear-gradient(#f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eee 100%);background-size:100% 19px;background-repeat:repeat-x;background-clip:padding-box;box-shadow:0 0 2px #fff inset,0 1px 0 rgba(0,0,0,0.05);color:#333;line-height:13px;cursor:default}.chosen-container-multi .chosen-choices li.search-choice span{word-wrap:break-word}.chosen-container-multi .chosen-choices li.search-choice .search-choice-close{position:absolute;top:4px;right:3px;display:block;width:12px;height:12px;font-size:1px}.chosen-container-multi .chosen-choices li.search-choice .search-choice-close:hover{background-position:-42px -10px}.chosen-container-multi .chosen-choices li.search-choice .search-choice-close::before{content:"\f00d";font-family:'FontAwesome';font-size:12px;position:relative;top:0px}.chosen-container-multi .chosen-choices li.search-choice-disabled{padding-right:5px;border:1px solid #ccc;background-color:#e4e4e4;background-image:linear-gradient(#f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eee 100%);color:#666}.chosen-container-multi .chosen-choices li.search-choice-focus{background:#d4d4d4}.chosen-container-multi .chosen-choices li.search-choice-focus .search-choice-close{background-position:-42px -10px}.chosen-container-multi .chosen-results{margin:0;padding:0}.chosen-container-multi .chosen-drop .result-selected{display:list-item;color:#ccc;cursor:default}.chosen-container-active .chosen-single{border:1px solid #5897fb;box-shadow:0 0 5px rgba(0,0,0,0.3)}.chosen-container-active.chosen-with-drop .chosen-single{border:1px solid #aaa;-moz-border-radius-bottomright:0;border-bottom-right-radius:0;-moz-border-radius-bottomleft:0;border-bottom-left-radius:0;background-image:linear-gradient(#eee 20%, #fff 80%);box-shadow:0 1px 0 #fff inset}.chosen-container-active.chosen-with-drop .chosen-single div{border-left:none;background:transparent}.chosen-container-active.chosen-with-drop .chosen-single div b{background-position:-18px 2px}.chosen-container-active .chosen-choices{border:1px solid #5897fb;box-shadow:0 0 5px rgba(0,0,0,0.3)}.chosen-container-active .chosen-choices li.search-field input[type="text"]{color:#222 !important}.chosen-disabled{opacity:0.5 !important;cursor:default}.chosen-disabled .chosen-single{cursor:default}.chosen-disabled .chosen-choices .search-choice .search-choice-close{cursor:default}.chosen-rtl{text-align:right}.chosen-rtl .chosen-single{overflow:visible;padding:0 8px 0 0}.chosen-rtl .chosen-single span{margin-right:0;margin-left:26px;direction:rtl}.chosen-rtl .chosen-single-with-deselect span{margin-left:38px}.chosen-rtl .chosen-single div{right:auto;left:3px}.chosen-rtl .chosen-single abbr{right:auto;left:26px}.chosen-rtl .chosen-choices li{float:right}.chosen-rtl .chosen-choices li.search-field input[type="text"]{direction:rtl}.chosen-rtl .chosen-choices li.search-choice{margin:3px 5px 3px 0;padding:3px 5px 3px 19px}.chosen-rtl .chosen-choices li.search-choice .search-choice-close{right:auto;left:4px}.chosen-rtl.chosen-container-single-nosearch .chosen-search,.chosen-rtl .chosen-drop{left:9999px}.chosen-rtl.chosen-container-single .chosen-results{margin:0 0 4px 4px;padding:0 4px 0 0}.chosen-rtl .chosen-results li.group-option{padding-right:15px;padding-left:0}.chosen-rtl.chosen-container-active.chosen-with-drop .chosen-single div{border-right:none}.chosen-rtl .chosen-search input[type="text"]{padding:4px 5px 4px 20px;direction:rtl}.chosen-rtl.chosen-container-single .chosen-single div b{background-position:6px 2px}.chosen-rtl.chosen-container-single.chosen-with-drop .chosen-single div b{background-position:-12px 2px}@media only screen and (-webkit-min-device-pixel-ratio: 1.5), only screen and (min-resolution: 144dpi), only screen and (min-resolution: 1.5dppx){.chosen-rtl .chosen-search input[type="text"],.chosen-container-single .chosen-single abbr,.chosen-container-single .chosen-single div b,.chosen-container-single .chosen-search input[type="text"],.chosen-container-multi .chosen-choices .search-choice .search-choice-close,.chosen-container .chosen-results-scroll-down span,.chosen-container .chosen-results-scroll-up span{background-size:52px 37px !important;background-repeat:no-repeat !important}}
  </style>
  <?php
}
add_action( 'customize_controls_print_styles', 'theme_slug_customizer_custom_control_css' );
?>