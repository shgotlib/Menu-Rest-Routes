<?php 
/**
 * Menu Rest Routes
 * 
 * @package             Nav_Rest_Routes
 * @author              Shlomi Gottlieb
 * @license             GPL-2.0+
 * 
 * @wordpress-plugin
 * Plugin name:         Menu Rest Routes
 * Description:         Adds a Rest Routes for nav menus to WP REST API, include the menu items and theme locations. Supports in "Menu Icons" Plugin to get the menu item's icon.
 * Version:             1.0.0
 * Author:              Shlomi Gottlieb
 * License:             GPL-2.0+
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 */
class Nav_Rest_Routes {

    private $taxonomy_name = 'nav_menu';

    function __construct() {
        add_action( 'rest_api_init', array($this ,'nav_menu_rest_support'), 25 );
        add_action( 'rest_api_init', array($this ,'add_items_to_menu_rest'), 50 );
        add_action( 'rest_api_init', array($this ,'add_theme_location'), 50 );
    }

    /**
     * Add REST API support to nav_menu taxonomy.
    */
    function nav_menu_rest_support() {
        global $wp_taxonomies;
    
        if ( isset( $wp_taxonomies[ $this->taxonomy_name ] ) ) {
            $wp_taxonomies[ $this->taxonomy_name ]->show_in_rest = true;
            $wp_taxonomies[ $this->taxonomy_name ]->rest_base = $this->taxonomy_name;
            $wp_taxonomies[ $this->taxonomy_name ]->rest_controller_class = 'WP_REST_Terms_Controller';
        }    
    }

    /**
     * Add REST API support to menu it menu items.
     * Add support in the icon if the "Menu Icons" Plugin is activated.
     * see https://wordpress.org/plugins/menu-icons/
     */
    function add_items_to_menu_rest() {
        if ( function_exists( 'register_rest_field' ) ) {
            register_rest_field( $this->taxonomy_name,
                'items',
                array(
                    'get_callback' => array($this ,'get_rest_for_menu'),
                    'schema'       => null,
                )
            );
        } elseif ( function_exists( 'register_api_field' ) ) {
            register_api_field( $this->taxonomy_name,
                'items',
                array(
                    'get_callback' => array($this ,'get_rest_for_menu'),
                    'schema'       => null,
                )
            );
        }
    }

    /**
     * Add REST API support to theme location for each menu.
     * see https://codex.wordpress.org/Navigation_Menus
     */
    function add_theme_location() {
        if ( function_exists( 'register_rest_field' ) ) {
            register_rest_field( $this->taxonomy_name,
                'theme_locations',
                array(
                    'get_callback'      => array($this ,'get_rest_for_location'),
                    'schema'            => null,
                )
            );
        } elseif ( function_exists( 'register_api_field' ) ) {
            register_api_field( $this->taxonomy_name,
                'theme_locations',
                array(
                    'get_callback' => array($this ,'get_rest_for_location'),
                    'schema'       => null,
                )
            );
        }
    }

    function get_rest_for_location($object, $field_name, $request) {
        $locations = get_nav_menu_locations();
        return array_keys($locations, $object['id']);
    }

    /**
     * return rest fields for menu items.
     */
    function get_rest_for_menu($object, $field_name, $request) {
        $menu_object = wp_get_nav_menu_object( $object['id'] );
        $args = [];
        if(! $menu_object) {
            return null;
        }
        $items = wp_get_nav_menu_items( $menu_object->term_id );
        foreach ($items as $item) {
            // try to get the icon
            $icon = get_post_meta($item->ID, 'menu-icons', true);
            if (is_array($icon) && $icon['type'] == 'image') {
                // get the url of the icon for your convenience
                $icon['icon_url'] = wp_get_attachment_url($icon['icon']);
            }
            $item->icon = $icon;

            $args[] = $item;
            
        }
        return $args;
    }
}

new Nav_Rest_Routes();