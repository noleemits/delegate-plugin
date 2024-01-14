<?php
// delegate-dashboard-account-details-tab.php

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

/**
 * To include WooCommerce update user form custom fields
 */
function add_customer_custom_fields_to_edit_account_form()
{
    $user = wp_get_current_user();
    $user_id = $user->ID;

    // Phone
    woocommerce_form_field(
        'billing_phone',
        array(
            'type' => 'tel',
            'class' => array('woocommerce-Input', 'woocommerce-Input--text', 'input-text'),
            'label' => __('Phone', 'woocommerce'),
            'required' => true,
            'default' => esc_attr(get_user_meta($user_id, 'billing_phone', true)),
        )
    );

    // Date of Birth
    woocommerce_form_field(
        'date_of_birth',
        array(
            'type' => 'text',
            'class' => array('woocommerce-Input', 'woocommerce-Input--text', 'input-text'),
            'label' => __('Date of Birth', 'woocommerce'),
            'required' => true,
            'default' => esc_attr(get_user_meta($user_id, 'date_of_birth', true)),
        )
    );

    // Address
    woocommerce_form_field(
        'billing_address_1',
        array(
            'type' => 'text',
            'class' => array('woocommerce-Input', 'woocommerce-Input--text', 'input-text'),
            'label' => __('Address', 'woocommerce'),
            'required' => true,
            'default' => esc_attr(get_user_meta($user_id, 'billing_address_1', true)),
        )
    );

    // City
    woocommerce_form_field(
        'billing_city',
        array(
            'type' => 'text',
            'class' => array('woocommerce-Input', 'woocommerce-Input--text', 'input-text'),
            'label' => __('City', 'woocommerce'),
            'required' => true,
            'default' => esc_attr(get_user_meta($user_id, 'billing_city', true)),
        )
    );

    // State
    $user_state = get_user_meta($user_id, 'billing_state', true);
    woocommerce_form_field(
        'billing_state',
        array(
            'type' => 'state',
            'class' => array('woocommerce-Input', 'woocommerce-Input--text', 'input-text'),
            'label' => __('State', 'woocommerce'),
            'required' => true,
            'default' => $user_state,
        )
    );
}

add_action('woocommerce_edit_account_form', 'add_customer_custom_fields_to_edit_account_form');


/**
 * To save WooCommerce update user form custom fields
 */
function save_customer_custom_fields_account_details($user_id)
{
    // Phone
    if (isset($_POST['billing_phone'])) {
        $phone = sanitize_text_field($_POST['billing_phone']);
        update_user_meta($user_id, 'phone', $phone);
        update_user_meta($user_id, 'billing_phone', $phone);
    }
    // Date of birth
    if (isset($_POST['date_of_birth'])) {
        $date_of_birth = sanitize_text_field($_POST['date_of_birth']); // date_of_birth is a text field
        update_user_meta($user_id, 'date_of_birth', $date_of_birth);
    }
    // Address
    if (isset($_POST['billing_address_1'])) {
        $billing_address = sanitize_text_field($_POST['billing_address_1']);
        update_user_meta($user_id, 'billing_address_1', $billing_address);
    }
    // City
    if (isset($_POST['billing_city'])) {
        $billing_city = sanitize_text_field($_POST['billing_city']);
        update_user_meta($user_id, 'billing_city', $billing_city);
    }
    // State
    if (isset($_POST['billing_state'])) {
        $billing_state = sanitize_text_field($_POST['billing_state']);
        update_user_meta($user_id, 'billing_state', $billing_state);
    }
}

add_action('woocommerce_save_account_details', 'save_customer_custom_fields_account_details', 12, 1);
