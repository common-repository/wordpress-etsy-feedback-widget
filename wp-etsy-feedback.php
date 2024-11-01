<?php

/*
  Plugin Name: WP Etsy Feedback
  Plugin URI: http://www.whalesharkwebsites.com/2010/07/27/wordpress-etsy-feedback-plugin/
  Description: Display your latest shop feedback in a widget.
  Author: martin
  Version: 2.2
  Author URI: http://www.whalesharkwebsites.com
 */

global $wp_version;
$exit_msg = 'WP Wall requires WordPress 2.6 or newer.
<a href="http://codex.wordpress.org/Upgrading_WordPress">Please
    update!</a>';

if (version_compare($wp_version, "2.3", "<")) {
    exit($exit_msg);
}

require 'jsonwrapper.php';

add_action('init', 'wp_etsy_feedback_init');

function etsy_widget_control() {
// get saved options
    $options = get_option('wp_etsy_feedback');
// handle user input
    if ($_POST["feedback_submit"]) {
        $options['feedback_title'] = strip_tags(stripslashes(
                                $_POST["feedback_title"]));
        update_option('wp_etsy_feedback', $options);
    }
    $title = $options['feedback_title'];
// print out the widget control
    include('wp-etsy-feedback-widget-control.php');
}

function wp_etsy_feedback_widget_plugin_menu() {

    add_options_page('Etsy Feedback Options', 'Etsy Feedback', 'manage_options', 'my-unique-identifier', 'wp_etsy_plugin_options');
}

function wp_etsy_plugin_options() {

    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    $options = get_option('wp_etsy_feedback');
    $etsy_id = $options['etsy_id'];
    $etsy_count = $options['etsy_count'];

    // see if the user has updated options
    if (isset($_POST['hidden_field']) && $_POST['hidden_field'] == 'Y') {

        // update the options
        $etsy_id = $_POST['etsy_id'];
        $options['etsy_id'] = $etsy_id;
        $etsy_count = $_POST['etsy_count'];
        $options['etsy_count'] = $etsy_count;

        // Save the posted value in the database
        update_option('wp_etsy_feedback', $options);

        // Put an settings updated message on the screen
        $updated_message = "Updated id to <b>" . $options['etsy_id'] . '</b>, count to <b>' . $options['etsy_count'] . "</b>";

        // set last updated option to force the plugin to regenerate the cache when it is next run
        $options = get_option('wp_etsy_feedback');
        $options['last_update_time'] = 1;
        update_option('wp_etsy_feedback', $options);
    }
    include('wp-etsy-feedback-options.php');
}

function etsy_widget($args = array()) {

    extract($args);

    $options = get_option('wp_etsy_feedback');
    $title = $options['feedback_title'];

    // print the theme compatibility code
    echo $before_widget;
    echo $before_title . $title . $after_title;
// include our widget
    include('wp-etsy-feedback-widget.php');
    echo $after_widget;
}

function wp_etsy_feedback_activate() {
// init last updated option to force the plugin to generate the cache when it is first run
    $options = get_option('wp_etsy_feedback');
    $options['last_update_time'] = 1;
    $options['etsy_count'] = 5;
    update_option('wp_etsy_feedback', $options);

   
}

function wp_etsy_feedback_init() {
    // register widget
    register_sidebar_widget('WP Etsy Feedback', 'etsy_widget');

    // register widget control
    register_widget_control('WP Etsy Feedback', 'etsy_widget_control');

    // add options page
    add_action('admin_menu', 'wp_etsy_feedback_widget_plugin_menu');

    // register activation code to initialize cache date
    register_activation_hook(__FILE__, 'wp_etsy_feedback_activate');
}
?>
