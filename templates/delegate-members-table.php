<!-- delegate-members-table.php -->
<h2>Members Panel</h2>
<?php if (!empty($members_with_orders) && count($members_with_orders) > 0): ?>

    <table border="1">
        <thead>
            <tr>
                <th>Member Info</th>
                <th>Order Number</th>
                <th>Order Details Header</th>
                <th>Product List</th>
                <th>Product Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($members_with_orders as $member_data) {
                $member_name = $member_data['member_name'];
                $member_id = $member_data['member_id'];
                $member_email = $member_data['member_email'];
                $orders = $member_data['orders'];

                if (empty($orders)) {
                    // Display only member name if there are no orders
                    echo '<tr>';
                    echo '<td>ID: ' . esc_html($member_id) . '<br>Name: ' . esc_html($member_name) . '<br>Email: ' . esc_html($member_email) . '</td>';
                    echo '<td colspan="4">No orders</td>';
                    echo '</tr>';
                } else {
                    foreach ($orders as $order_data) {
                        echo '<tr>';
                        echo '<td>ID: ' . esc_html($member_id) . '<br>Name: ' . esc_html($member_name) . '<br>Email: ' . esc_html($member_email) . '</td>';
                        echo '<td>' . esc_html($order_data['order_number']) . '</td>';

                        // Display Order Details Header (including order status)
                        echo '<td>';
                        echo 'Date: ' . esc_html($order_data['order_details_header']) . '<br>';
                        echo 'Status: ' . esc_html($order_data['order_status']) . '<br>';
                        echo '</td>';

                        // Display Product List
                        echo '<td>';
                        foreach ($order_data['product_list'] as $product) {
                            echo 'Product: ' . esc_html($product['product_name']) . '<br>';
                            echo 'Quantity: ' . esc_html($product['quantity']) . '<br>';
                            echo 'Total Cost: ' . esc_html($product['total_cost']) . '<br>';
                            echo '<br>';
                        }
                        echo '</td>';

                        // Display Product Total with HTML escaped
                        echo '<td>' . esc_html($order_data['product_total']) . '</td>';
                        echo '</tr>';
                    }
                }
            }
            ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No members found.</p>
<?php endif; ?>