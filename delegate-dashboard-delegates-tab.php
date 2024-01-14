<?php
// delegate-dashboard-delegates-tab.php

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}


/**
 * Delegate Users Functions
 */

// Function to get delegate emails as an array
function get_delegate_emails($user_id)
{
    $delegate_emails = get_user_meta($user_id, 'delegate_emails', true);
    return !empty($delegate_emails) ? array_map('trim', explode(',', $delegate_emails)) : array();
}

// Function to delete delegate
function delete_delegate_user($delegate_email_to_delete, $user_id)
{
    $delegate_emails = get_user_meta($user_id, 'delegate_emails', true);
    if ($delegate_emails) {
        // Convert the comma-separated string to an array
        $delegate_emails_array = explode(',', $delegate_emails);
        // Check if the delegate email exists in the array
        if (in_array($delegate_email_to_delete, $delegate_emails_array)) {
            // Remove the delegate email from the array
            $updated_delegate_emails = array_diff($delegate_emails_array, array($delegate_email_to_delete));
            // Convert the array back to a comma-separated string
            $updated_delegate_emails_string = implode(', ', $updated_delegate_emails);
            // Update user meta with the updated delegate emails string
            update_user_meta($user_id, 'delegate_emails', $updated_delegate_emails_string);
            // Check if the updated delegate emails array is empty
            if (empty($updated_delegate_emails)) {
                // If empty, update skip_delegate to 'on'
                update_user_meta($user_id, 'skip_delegate', 'on');
                // Trigger AutomateWoo Workflow to remind the customer to add delegetes if the checkbox is checked
                do_action('customer_registration_without_delegates', $user_id);
            }
        }
    }
}




// Process delegate submission
function add_delegate($new_delegate_email, $user_id)
{
    // Retrieve current delegate emails
    $delegate_emails = get_user_meta($user_id, 'delegate_emails', true);
    // Convert the string to an array
    $delegate_emails_array = !empty($delegate_emails) ? array_map('trim', explode(',', $delegate_emails)) : array();
    // Get the user's email
    $user_email = get_user_by('ID', $user_id)->user_email;
    // Check if the new email is not the same as the user's email and not already in the array
    if ($new_delegate_email !== $user_email && !in_array($new_delegate_email, $delegate_emails_array)) {
        // Add the new email to the array
        $delegate_emails_array[] = $new_delegate_email;
        // Convert the array back to a comma-separated string
        $delegate_emails_string = implode(',', $delegate_emails_array);
        // Update user meta with the new delegate emails
        update_user_meta($user_id, 'delegate_emails', $delegate_emails_string);
        update_user_meta($user_id, 'latest_delegate_email_added', $new_delegate_email);
        update_user_meta($user_id, 'skip_delegate', 'off');
        // Trigger the custom email workflow action when a new delegate email is added
        do_action('customer_delegate_added', $user_id);
    }
}


/**
 * Woocomerce my-account new Tab definition for Delegates
 */
function delegates_tab($items)
{
    $items['delegates'] = 'Legacy Agent(s)';
    return $items;
}
add_filter('woocommerce_account_menu_items', 'delegates_tab');


function delegates_tab_endpoint()
{
    add_rewrite_endpoint('delegates', EP_ROOT | EP_PAGES);
    flush_rewrite_rules();
}

add_action('init', 'delegates_tab_endpoint');


function delegates_tab_content()
{
    $user_id = get_current_user_id();
    $delegate_emails = get_delegate_emails($user_id);

    // Ensure $delegate_emails is an array
    if (!is_array($delegate_emails)) {
        $delegate_emails = array();
    }

    $delegate_users = array();

    foreach ($delegate_emails as $delegate_email) {
        $delegate_id = email_exists($delegate_email);
        if ($delegate_id) {
            // User already exists, fetch user data
            $delegate_users[] = array(
                'email' => $delegate_email,
                'registered' => true,
                'ID' => $delegate_id,
            );
        } else {
            // User doesn't exist
            $delegate_users[] = array(
                'email' => $delegate_email,
                'registered' => false,
                'ID' => null,
            );
        }
    }

    // Include the delegate users table template
    include DELEGATES_PLUGIN_PATH . '/templates/delegate-users-table.php';
    if (count($delegate_emails) < 2) {
        include DELEGATES_PLUGIN_PATH . '/templates/delegate-form.php';
    }
}
add_action('woocommerce_account_delegates_endpoint', 'delegates_tab_content');




/**
 * Woocomer my-account new Tab Delegates Content and Form submit
 */

function handle_delegate_user_form()
{
    // ADD DELEGATE
    if (isset($_POST['submit_new_delegate'])) {
        $user_id = get_current_user_id();
        $new_delegate_email = sanitize_email($_POST['new_delegate_email']);
        add_delegate($new_delegate_email, $user_id);
        wp_safe_redirect(home_url('/my-account/delegates/'));
        exit;
    }
    // REMOVE DELEGATE
    if (isset($_POST['delete_delegate_user_email'])) {
        $user_id = get_current_user_id();
        $delegate_email_to_delete = sanitize_email($_POST['delete_delegate_user_email']);
        delete_delegate_user($delegate_email_to_delete, $user_id);
        wp_safe_redirect(home_url('/my-account/delegates/'));
        exit();
    }
    // EDIT DELEGATE
    if (isset($_POST['submit_edit_delegate'])) {
        // Get user data from edit form fields
        $edit_delegate_user_id = absint($_POST['edit_delegate_user_id']);
        $edit_delegate_first_name = sanitize_text_field($_POST['edit_delegate_first_name']);
        $edit_delegate_phone = sanitize_text_field($_POST['edit_delegate_phone']);
        $edit_delegate_documented_proof = isset($_POST['edit_delegate_documented_proof']) ? true : false;
        // Update user meta for the edited user
        update_user_meta($edit_delegate_user_id, 'first_name', $edit_delegate_first_name);
        update_user_meta($edit_delegate_user_id, 'billing_first_name', $edit_delegate_first_name);
        update_user_meta($edit_delegate_user_id, 'phone', $edit_delegate_phone);
        update_user_meta($edit_delegate_user_id, 'billing_phone', $edit_delegate_phone);
        update_user_meta($edit_delegate_user_id, 'documented_proof_of_death', $edit_delegate_documented_proof);
        wp_safe_redirect(home_url('/my-account/delegates/'));
        exit;
    }
}

add_action('init', 'handle_delegate_user_form');
