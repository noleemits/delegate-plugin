<?php
// delegate-dashboard-members-tab.php

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

/**
 * Woocomerce my-account new Tab definition for Delegates
 */
function delegates_members_tab($items)
{
    $items['members'] = 'Members';
    return $items;
}
add_filter('woocommerce_account_menu_items', 'delegates_members_tab');


function delegates_members_tab_endpoint()
{
    add_rewrite_endpoint('members', EP_ROOT | EP_PAGES);
    flush_rewrite_rules();
}

add_action('init', 'delegates_members_tab_endpoint');


function delegates_members_tab_content()
{
    $current_user = wp_get_current_user();
    $user_email = $current_user instanceof WP_User ? $current_user->user_email : '';
    $members = delegates_get_members($user_email);
    // Initialize the variable to store member names and their orders
    $members_with_orders = array();
    if (isset($members) && !empty($members)) {
        foreach ($members as $member_id) {
            // Get WooCommerce orders for each member
            $orders = wc_get_orders(
                array(
                    'customer' => $member_id,
                )
            );
            // Initialize an array to store orders for this member
            $member_orders = array();
            foreach ($orders as $order) {
                // Initialize an array to store product details for each order
                $product_list = array();
                // Loop through order items
                foreach ($order->get_items() as $item_id => $item) {
                    // Get product details
                    $product = $item->get_product();
                    // Add product details to the product list array
                    $product_list[] = array(
                        'product_name' => $product->get_name(),
                        'quantity' => $item->get_quantity(),
                        'total_cost' => strip_tags(wc_price($item->get_total())), // Format cost as currency
                    );
                }
                // Extract relevant order data
                $order_data = array(
                    'order_number' => $order->get_order_number(),
                    'order_details_header' => date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($order->get_date_created())),
                    'order_status' => $order->get_status(),
                    'product_list' => $product_list,
                    'product_total' => strip_tags($order->get_formatted_order_total()),
                );
                // Add order data to the member's orders array
                $member_orders[] = $order_data;
            }
            // Get the member's email
            $member_data = get_userdata($member_id);
            $member_email = $member_data ? $member_data->user_email : '';
            // Get the member's name
            $member_name = get_user_meta($member_id, 'first_name', true);
            // Add member name and orders data to the main array
            $members_with_orders[] = array(
                'member_name' => $member_name,
                'member_email' => $member_email,
                'member_id' => $member_id,
                'orders' => $member_orders,
            );
        }
    }

    // Include the delegate members table template
    include DELEGATES_PLUGIN_PATH . '/templates/delegate-members-table.php';
}


add_action('woocommerce_account_members_endpoint', 'delegates_members_tab_content');


function delegates_get_members($user_email)
{
    $args = array(
        'meta_query' => array(
            array(
                'key' => 'delegate_emails',
                'value' => $user_email,
                'compare' => 'LIKE',
            ),
        ),
        'fields' => 'ids',
    );
    $user_query = new WP_User_Query($args);
    $user_ids = $user_query->get_results();
    return $user_ids;
}

