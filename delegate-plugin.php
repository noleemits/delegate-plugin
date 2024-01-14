<?php
/*
Plugin Name: Delegate Plugin
Description: Custom plugin for Legacy Agents Users.
Version: 1.0
Author: KitelyTech Development
*/

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}


define('DELEGATES_PLUGIN_PATH', plugin_dir_path(__FILE__));


include_once(DELEGATES_PLUGIN_PATH . 'delegate-extra-fields.php');
include_once(DELEGATES_PLUGIN_PATH . 'delegate-dashboard-account-details-tab.php');
include_once(DELEGATES_PLUGIN_PATH . 'delegate-dashboard-delegates-tab.php');
include_once(DELEGATES_PLUGIN_PATH . 'delegate-dashboard-members-tab.php');
include_once(DELEGATES_PLUGIN_PATH . 'delegate-registration.php');


// Enqueue stylesheeta
function enqueue_plugin_styles()
{
    wp_enqueue_style('delegates-styles', plugin_dir_url(__FILE__) . 'css/style.css');
}
add_action('wp_enqueue_scripts', 'enqueue_plugin_styles');


/**
 * Active & Deactive Functions
 */
function delegates_add_user_fields()
{
    register_meta(
        'user',
        'documented_proof_of_death',
        array(
            'type' => 'boolean',
            'description' => 'Documented Proof of Death',
            'single' => true,
            'show_in_rest' => true
        )
    );
    register_meta(
        'user',
        'date_of_birth',
        array(
            'type' => 'string',
            'description' => 'Date of Birth',
            'single' => true,
            'show_in_rest' => true,
        )
    );
    register_meta(
        'user',
        'skip_delegate',
        array(
            'type' => 'string', // Save as string
            'description' => 'Skip Delegate',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_skip_delegate',
            'auth_callback' => function () {
                return current_user_can('edit_users');
            },
        )
    );
    register_meta(
        'user',
        'delegate_emails',
        array(
            'type' => 'string', // Save as string
            'description' => 'Delegate Emails',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => function () {
                return current_user_can('edit_users');
            },
        )
    );
}
function delegates_plugin_activate()
{
    add_action('init', 'delegates_add_user_fields');
}
function delegates_plugin_deactivate()
{
}
// Sanitization callback for 'skip_delegate'
function sanitize_skip_delegate($value)
{
    // Ensure the value is either 'on' or 'off'
    return ($value === 'on' || $value === 'off') ? $value : 'off';
}

/**
 * Activate & Deactive hooks
 */
register_activation_hook(__FILE__, 'delegates_plugin_activate');
register_deactivation_hook(__FILE__, 'delegates_plugin_deactivate');



/**
 * Woocomerce custom workflow triggers definition
 */

function my_custom_triggers($triggers)
{

    include_once(DELEGATES_PLUGIN_PATH . 'customer_automatewoo_delegate_added_trigger.php');
    include_once(DELEGATES_PLUGIN_PATH . 'customer_automatewoo_registration_without_delegates_trigger.php');

    // set a unique name for the trigger and then the class name
    $triggers['delegate_added_custom_trigger'] = 'Customer_AutomateWoo_Delegate_Added_Trigger';
    $triggers['add_delegate_reminder_trigger'] = 'Customer_AutomateWoo_Registration_Without_Delegates_Trigger';

    return $triggers;
}
add_filter('automatewoo/triggers', 'my_custom_triggers');



/**
 * WooCommerce My Account Page Logout Redirect
 */
add_action('wp_logout', 'owp_redirect_after_logout');
function owp_redirect_after_logout()
{
    wp_redirect('/my-account/');
    exit();
}
