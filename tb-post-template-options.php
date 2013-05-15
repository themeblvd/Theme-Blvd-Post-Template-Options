<?php
/*
Plugin Name: Theme Blvd Post Template Options
Description: This plugins adds a meta box to reveal available custom fields you can use with Post List/Grid page templates of a Theme Blvd theme.
Version: 1.0.0
Author: Jason Bobich
Author URI: http://jasonbobich.com
License: GPL2

    Copyright 2012  Jason Bobich

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/

define( 'TB_PTO_PLUGIN_VERSION', '1.0.0' );
define( 'TB_PTO_PLUGIN_DIR', dirname( __FILE__ ) ); 
define( 'TB_PTO_PLUGIN_URI', plugins_url( '' , __FILE__ ) );

/**
 * Setup Post Template Options plugin.
 */
class Theme_Blvd_Post_Template_Options {
 
    /**
     * Only instance of object.  
     */
    private static $instance = null;

    /**
     * Whether or not the plugin can run.  
     */
    private $run = true;
 
    /**
     * Creates or returns an instance of this class.
     *
     * @return  Theme_Blvd_Post_Template_Options A single instance of this class.
     */
    public static function get_instance() {
        if( self::$instance == null ) {
            self::$instance = new self;
        }
        return self::$instance;
    }
 
    /**
     * Initiate plugin.
     */
    private function __construct() {
        add_action( 'plugins_loaded', array( $this, 'localize' ) );
        add_action( 'after_setup_theme', array( $this, 'check' ) );
        add_action( 'after_setup_theme', array( $this, 'run' ) );
    }

    /**
     * Check to make sure Theme Blvd framework v2.3 is present.
     */
    public function check() {
        if( ! class_exists( 'Theme_Blvd_Meta_Box' ) ) {
            add_action( 'admin_notices', array( $this, 'show_nag' ) );
            add_action( 'admin_init', array( $this, 'hide_nag' ) );
            $this->run = false;
        }
    }

    /**
     * Load plugin's textdomain "themeblvd_pto"
     */
    public function localize() {
       load_plugin_textdomain( 'themeblvd_tpo', false, TB_PTO_PLUGIN_DIR . '/lang' );
    }

    /**
     * Handle nag message 
     */
    public function show_nag() {
        global $current_user;
        if( ! get_user_meta( $current_user->ID, 'tb_pto_no_framework' ) ){
            echo '<div class="updated">';
            echo '<p>'.__( 'You currently have the "Theme Blvd Post Template Options" plugin activated, however you are not using a theme with Theme Blvd Framework v2.3+, and so this plugin will not do anything.', 'themeblvd_pto' ).'</p>';
            echo '<p><a href="?tb_nag_ignore=tb_pto_no_framework">'.__('Dismiss this notice', 'themeblvd_pto').'</a> | <a href="http://www.themeblvd.com" target="_blank">'.__('Visit ThemeBlvd.com', 'themeblvd_pto').'</a></p>';
            echo '</div>';
        }
    }
    public function hide_nag() {
        global $current_user;
        if ( isset( $_GET['tb_nag_ignore'] ) ) {
            add_user_meta( $current_user->ID, $_GET['tb_nag_ignore'], 'true', true );
        }
    }

    /**
     * Add meta box.
     */
    public function run() {
        if( $this->run ) {
            
            global $_themeblvd_pto_meta_box;

            // Setup Config for meta box
            $config = array(
                'id'        => 'tb_pto_options',  
                'title'     => __( 'Post Template Options', 'themeblvd' ),
                'page'      => array( 'page' ),
                'context'   => 'normal',
                'priority'  => 'low'
            );
            $config = apply_filters( 'themeblvd_pto_config', $config );

            // Setup options for meta box
            $options = array(
                array(
                    'id'        => 'desc',
                    'desc'      => __( '<p>Below are the custom fields you can use with the Post List and Post Grid page templates. When working with these options, you can find a lot of helpful information by viewing WordPress\'s Codex page on the <a href="http://codex.wordpress.org/Class_Reference/WP_Query" target="_blank">WP Query</a>.</p><p class="note">Note: When using the Post List template, categories excluded from Appearance > Theme Options > Content > Primary Posts will be excluded here by default. Using the "cat" or "category_name" custom fields will override this.</p>', 'themeblvd_pto' ),
                    'type'      => 'info'
                ),
                array(
                    'id'        => 'cat',
                    'name'      => __( 'cat', 'themeblvd_pto' ),
                    'desc'      => __( 'Category ID(s) to include/exclude.<br>Ex: 1<br>Ex: 1, 2, 3<br>Ex: -1,-2,-3', 'themeblvd_pto' ),
                    'type'      => 'text'
                ),
                array(
                    'id'        => 'category_name',
                    'name'      => __( 'category_name', 'themeblvd_pto' ),
                    'desc'      => __( 'Category slug(s) to include.<br>Ex: cat-1<br>Ex: cat-1, cat-2', 'themeblvd_pto' ),
                    'type'      => 'text'
                ),
                array(
                    'id'        => 'posts_per_page',
                    'name'      => __( 'posts_per_page', 'themeblvd_pto' ),
                    'desc'      => __( 'Number of posts per page. Only for Post List template; Post Grid uses rows*columns.', 'themeblvd_pto' ),
                    'type'      => 'text'
                ),
                array(
                    'id'        => 'orderby',
                    'name'      => __( 'orderby', 'themeblvd_pto' ),
                    'desc'      => __( 'What to order posts by -- date, title, rand, etc.<br>(<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">Learn More</a>)', 'themeblvd_pto' ),
                    'type'      => 'text'
                ),
                array(
                    'id'        => 'order',
                    'name'      => __( 'order', 'themeblvd_pto' ),
                    'desc'      => __( 'How to order posts -- ASC or DESC.<br>(<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">Learn More</a>)', 'themeblvd_pto' ),
                    'type'      => 'text'
                ),
                array(
                    'id'        => 'query',
                    'name'      => __( 'query', 'themeblvd_pto' ),
                    'desc'      => __( 'A custom query string. This will override other options.<br>Ex: tag=baking<br>Ex: post_type=my_type&my_tax=my_term', 'themeblvd_pto' ),
                    'type'      => 'text'
                ),
                array(
                    'id'        => 'columns',
                    'name'      => __( 'columns', 'themeblvd_pto' ),
                    'desc'      => __( 'Number of columns for Post Grid template. When empty, this will default to 3.', 'themeblvd_pto' ),
                    'type'      => 'text'
                ),
                array(
                    'id'        => 'rows',
                    'name'      => __( 'rows', 'themeblvd_pto' ),
                    'desc'      => __( 'Number of rows for Post Grid template. When empty, this will default to 4.', 'themeblvd_pto' ),
                    'type'      => 'text'
                )
            );
            $options = apply_filters( 'themeblvd_pto_options', $options );

            // Create Meta Box object
            $_themeblvd_pto_meta_box = new Theme_Blvd_Meta_Box( $config, $options );

        }
    }
}
Theme_Blvd_Post_Template_Options::get_instance();