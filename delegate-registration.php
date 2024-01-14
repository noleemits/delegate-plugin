<?php
// delegate-registration.php

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}


/**
 * To add WooCommerce registration form custom fields.
 */
function delegates_add_register_form_field()
{
    wp_enqueue_script('delegate-register-scripts', plugin_dir_url(__FILE__) . 'scripts/delegate-register-scripts.js', array('jquery'), '1.0', true);

    //Custom field for legacy description in checkout page	
    $legacy_description = '';
    if( get_field('legacy_description') ){
        $legacy_description = get_field('legacy_description'); 
    };


    $delegate_fields = array(
        'billing_first_name' => array(
            'label' => 'First name',
            'type' => 'text',
            'required' => true,
        ),
        'billing_last_name' => array(
            'label' => 'Last name',
            'type' => 'text',
            'required' => true,
        ),
        'billing_phone' => array(
            'label' => 'Phone',
            'type' => 'text',
            'required' => true,
        ),
        'date_of_birth' => array(
            'label' => 'Date of Birth',
            'type' => 'date',
            'required' => true,
        ),
        'billing_address_1' => array(
            'label' => 'Address',
            'type' => 'text',
            'required' => true,
        ),
        'billing_address_2' => array(
            'label' => 'Address Line 2',
            'type' => 'text',
            'required' => false, // Set to false as this is an optional field
            'class' => ['address-field'],
        ),
        'billing_city' => array(
            'label' => 'City',
            'type' => 'text',
            'required' => true,
        ),
        'billing_state' => array(
            'label' => 'State',
            'type' => 'state',
            'required' => true,
            'class' => ['address-field'],
            'validate' => ['state']
        ),
        'skip_delegate' => array(
            'label' => 'Skip assigning a Legacy Agent',
            'type' => 'checkbox',
            'required' => false,
        ),
        'register_delegate_email_1' => array(
	    'description' => $legacy_description,
            'label' => 'Legacy Agent 1 e-mail',
            'type' => 'text',
            'required' => true, // false if skip_delegate is checked
        ),
        'register_delegate_email_2' => array(
            'description' => 'Max 2 Legacy Agents per member. Legacy Agents can be added/edited after registration.',
            'label' => 'Legacy Agent 2 e-mail',
            'type' => 'text',
            'required' => false,
        ),
    );

    foreach ($delegate_fields as $field_key => $field_info) {
        woocommerce_form_field(
            $field_key,
            array(
                'type' => $field_info['type'],
                'required' => $field_info['required'],
                'label' => $field_info['label'],
                'description' => isset($field_info['description']) ? $field_info['description'] : '',
                'class' => isset($field_info['class']) ? $field_info['class'] : '',
                'validate' => isset($field_info['validate']) ? $field_info['validate'] : '',
            ),
            (isset($_POST[$field_key]) ? sanitize_text_field($_POST[$field_key]) : '')
        );
    }
}
add_action('woocommerce_register_form', 'delegates_add_register_form_field', 10);





// /**
//  * To validate WooCommerce registration form custom fields.
//  */
function delegates_validate_fields($username, $email, $errors)
{
    $delegate_fields = array(
        'billing_first_name' => array(
            'label' => 'First name',
            'type' => 'text',
            'required' => true,
        ),
        'billing_last_name' => array(
            'label' => 'Last name',
            'type' => 'text',
            'required' => true,
        ),
        'billing_phone' => array(
            'label' => 'Phone',
            'type' => 'text',
            'required' => true,
        ),
        'date_of_birth' => array(
            'label' => 'Date of Birth',
            'type' => 'date',
            'required' => true,
        ),
        'billing_address_1' => array(
            'label' => 'Address',
            'type' => 'text',
            'required' => true,
        ),
        'billing_address_2' => array(
            'label' => 'Address Line 2',
            'type' => 'text',
            'required' => false, // Set to false as this is an optional field
            'class' => ['address-field'],
        ),
        'billing_city' => array(
            'label' => 'City',
            'type' => 'text',
            'required' => true,
        ),
        'billing_state' => array(
            'label' => 'State',
            'type' => 'text',
            'required' => true,
        ),
        'skip_delegate' => array(
            'label' => 'Skip assigning a legacy agent',
            'type' => 'checkbox',
            'required' => false,
        ),
        'register_delegate_email_1' => array(
            'label' => 'Legacy agent 1 e-mail',
            'type' => 'email',
            'required' => true, // Check if skip_delegate is not checked
        ),
        'register_delegate_email_2' => array(
            'label' => 'Legacy agent 2 e-mail',
            'type' => 'email',
            'required' => false,
        ),
    );

    // Loop through delegate fields and check for required validation
    foreach ($delegate_fields as $field_key => $field_info) {
        if ($field_info['required'] && (!isset($_POST['skip_delegate']) || $_POST['skip_delegate'] !== '1')) {
            if ($field_info['type'] === 'email') {
                $field_value = isset($_POST[$field_key]) ? sanitize_email($_POST[$field_key]) : '';
            } else {
                $field_value = isset($_POST[$field_key]) ? sanitize_text_field($_POST[$field_key]) : '';
            }

            if (empty($field_value)) {
                $errors->add($field_key . '_error', sprintf(__('%s is required.', 'your-text-domain'), $field_info['label']));
            }
        }
    }

    // Return the modified $errors array
    return $errors;
}
add_action('woocommerce_register_post', 'delegates_validate_fields', 10, 3);






/**
 * To save WooCommerce registration form custom fields and delegates.
 */
function delegates_woo_save_reg_form_fields($customer_id)
{
    if (isset($_POST['billing_first_name'])) {
        $first_name = sanitize_text_field($_POST['billing_first_name']);
        update_user_meta($customer_id, 'first_name', $first_name);
        update_user_meta($customer_id, 'billing_first_name', $first_name);
    }

    if (isset($_POST['billing_last_name'])) {
        $last_name = sanitize_text_field($_POST['billing_last_name']);
        update_user_meta($customer_id, 'last_name', $last_name);
        update_user_meta($customer_id, 'billing_last_name', $last_name);
    }

    if (isset($_POST['billing_phone'])) {
        $phone = sanitize_text_field($_POST['billing_phone']);
        update_user_meta($customer_id, 'phone', $phone);
        update_user_meta($customer_id, 'billing_phone', $phone);
    }

    if (isset($_POST['date_of_birth'])) {
        $date_of_birth = sanitize_text_field($_POST['date_of_birth']); // Assuming date_of_birth is a text field
        update_user_meta($customer_id, 'date_of_birth', $date_of_birth);
    }

    if (isset($_POST['billing_address_1'])) {
        $billing_address = sanitize_text_field($_POST['billing_address_1']);
        update_user_meta($customer_id, 'billing_address_1', $billing_address);
    }

    
    if (isset($_POST['billing_address_2'])) {
        $billing_address_2 = sanitize_text_field($_POST['billing_address_2']);
        update_user_meta($customer_id, 'billing_address_2', $billing_address_2);
     }


    if (isset($_POST['billing_city'])) {
        $billing_city = sanitize_text_field($_POST['billing_city']);
        update_user_meta($customer_id, 'billing_city', $billing_city);
    }

    if (isset($_POST['billing_state'])) {
        $billing_state = sanitize_text_field($_POST['billing_state']);
        update_user_meta($customer_id, 'billing_state', $billing_state);
    }

    $skip_delegate = isset($_POST['skip_delegate']) ? "on" : "off"; // 'on' if checked, 'off' if unchecked
    update_user_meta($customer_id, 'skip_delegate', $skip_delegate);

    if ($skip_delegate == "on") {
        // Trigger AutomateWoo Wordflow to remind the customer to add delegetes if the checkbox is checked
        do_action('customer_registration_without_delegates', $customer_id);
    } elseif ($skip_delegate == "off") {
        // Save delegate emails only if the checkbox is not checked
        $delegate_email_1 = isset($_POST['register_delegate_email_1']) ? sanitize_email($_POST['register_delegate_email_1']) : '';
        $delegate_email_2 = isset($_POST['register_delegate_email_2']) ? sanitize_email($_POST['register_delegate_email_2']) : '';
        if (!empty($delegate_email_1)) {
            add_delegate($delegate_email_1, $customer_id);
        }
        if (!empty($delegate_email_2)) {
            add_delegate($delegate_email_2, $customer_id);
        }
    }
}
add_action('woocommerce_created_customer', 'delegates_woo_save_reg_form_fields');
