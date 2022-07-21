<?php

namespace LN\Flohkiste;

defined('ABSPATH') || exit;

class Cpt
{
    public function __construct($pluginFile)
    {

        add_filter('single_template', [$this, 'kitaevents_post_type_template']);
        $this->pluginFile = $pluginFile;
        add_action('init', [$this, 'kitaevents']);
        add_action('cmb2_admin_init', [$this, 'cmb2_sample_metaboxes']);

        Helper::debug(dirname(__FILE__) . '/templates/kitaevents-single-template.php');
    }

    public function kitaevents()
    {
        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => _x('KiTa Events', 'Post Type General Name', 'flohkiste'),
            'singular_name'       => _x('KiTa Event', 'Post Type Singular Name', 'flohkiste'),
            'menu_name'           => __('KiTa Events', 'flohkiste'),
            'parent_item_colon'   => __('Parent KiTa Event', 'flohkiste'),
            'all_items'           => __('All KiTa Events', 'flohkiste'),
            'view_item'           => __('View KiTa Event', 'flohkiste'),
            'add_new_item'        => __('Add New KiTa Event', 'flohkiste'),
            'add_new'             => __('Add New', 'flohkiste'),
            'edit_item'           => __('Edit KiTa Event', 'flohkiste'),
            'update_item'         => __('Update KiTa Event', 'flohkiste'),
            'search_items'        => __('Search KiTa Event', 'flohkiste'),
            'not_found'           => __('Not Found', 'flohkiste'),
            'not_found_in_trash'  => __('Not found in Trash', 'flohkiste'),
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => __('kitaevents', 'flohkiste'),
            'description'         => __('KiTa Events', 'flohkiste'),
            'labels'              => $labels,
            
            'supports'            => array('title', 'revisions'),
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-star-half',
            'can_export'          => true,
            'has_archive'         => true,

            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'show_in_rest'        => true,
            'capability_type'     => 'post',

            // This is where we add taxonomies to our CPT
            'taxonomies'          => array('category'),
        );

        // Registering your Custom Post Type
        register_post_type('kitaevents', $args);
    }

    /**
     * Define the metabox and field configurations.
     */
    function cmb2_sample_metaboxes()
    {

        /**
         * Initiate the metabox
         */
        $cmb = new_cmb2_box(array(
            'id'            => 'kitaevent-metabox',
            'title'         => __('KiTa Event information', 'flohkiste'),
            'object_types'  => array('kitaevents',), // Post type
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true, // Show field names on the left
            // 'cmb_styles' => false, // false to disable the CMB stylesheet
            // 'closed'     => true, // Keep the metabox closed by default
        ));

        // Regular text field
        $cmb->add_field(array(
            'name'       => __('Title of the KiTaEvent', 'flohkiste'),
            'desc'       => __('Title of the KiTaEvent', 'flohkiste'),
            'id'         => 'kitaevent-title',
            'type'       => 'text',
            'show_on_cb' => 'cmb2_hide_if_no_cats', // function should return a bool value
            // 'sanitization_cb' => 'my_custom_sanitization', // custom sanitization callback parameter
            // 'escape_cb'       => 'my_custom_escaping',  // custom escaping callback parameter
            // 'on_front'        => false, // Optionally designate a field to wp-admin only
            // 'repeatable'      => true,
        ));

        // Regular text field
        $cmb->add_field(array(
            'name'       => __("Subtitle of the KiTaEvent", 'flohkiste'),
            'desc'       => __('Subtitle of the KiTaEvent', 'flohkiste'),
            'id'         => 'kitaevent-subtitle',
            'type'       => 'text',
            'show_on_cb' => 'cmb2_hide_if_no_cats', // function should return a bool value
            // 'sanitization_cb' => 'my_custom_sanitization', // custom sanitization callback parameter
            // 'escape_cb'       => 'my_custom_escaping',  // custom escaping callback parameter
            // 'on_front'        => false, // Optionally designate a field to wp-admin only
            // 'repeatable'      => true,
        ));

        // Add other metaboxes as needed
        $cmb->add_field(array(
            'name'    => __('Event text', 'flohkiste'),
            'desc'    => __('Describe the KiTa Event', 'flohkiste'),
            'id'      => 'kitaevent-text',
            'type'    => 'wysiwyg',
            'options' => array(),
        ));
        /*
        $cmb->add_field(array(
            'name'    => __('Beginning of the event', 'flohkiste'),
            'desc'    => __('Choose the day for event start', 'flohkiste'),
            'type' => 'text_date',
            'id'   => 'kitaevent-start',
            // 'timezone_meta_key' => 'wiki_test_timezone',
            // 'date_format' => 'l jS \of F Y',
        ));

        $cmb->add_field(array(
            'name'    => __('End day of the event', 'flohkiste'),
            'desc'    => __('Choose the day for event end', 'flohkiste'),
            'type' => 'text_date',
            'id' => 'kitaevent-end-day',
            // 'timezone_meta_key' => 'wiki_test_timezone',
            // 'date_format' => 'l jS \of F Y',
        ));
*/
        $cmb->add_field(array(
            'name'    => __('Beginning time of the event', 'flohkiste'),
            'desc'    => __('Choose the time for beginning', 'flohkiste'),
            'id'   => 'kitaevent-time-start',
            'type' => 'text_datetime_timestamp',
        ));

        $cmb->add_field(array(
            'name'    => __('End time of the event', 'flohkiste'),
            'desc'    => __('Choose the time for End of the event', 'flohkiste'),
            'id'   => 'kitaevent-time-end',
            'type' => 'text_datetime_timestamp',
        ));
    }

    public static function kitaevents_post_type_template($single_template)
    {
        global $post;

        if ('kitaevents' === $post->post_type) {
            $single_template = dirname(__FILE__) . '/templates/kitaevents-single-template.php';
        }

        return $single_template;
    } //end mte_get_event_post_type_template function

}
