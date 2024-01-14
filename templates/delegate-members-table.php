<!-- delegate-members-table.php -->
<h2>Members Panel</h2>
<?php if (!empty($members_with_orders) && count($members_with_orders) > 0): ?>

    <table border="1">
        <thead>
            <tr>
                <th>Member Info</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($members_with_orders as $member_data) {
                $member_name = $member_data['member_name'];
                $member_email = $member_data['member_email'];
                $orders = $member_data['orders'];

                if (empty($orders)) {
                    // Display only member name and email if there are no orders
                    echo '<tr>';
                    echo '<td>Name: ' . esc_html($member_name) . '<br>Email: ' . esc_html($member_email) . '</td>';
                    echo '</tr>';
                } else {
                    foreach ($orders as $order_data) {
                        echo '<tr>';
                        echo '<td>Name: ' . esc_html($member_name) . '<br>Email: ' . esc_html($member_email) . '</td>';
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
