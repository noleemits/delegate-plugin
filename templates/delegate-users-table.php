<!-- delegate-users-table.php -->
<h2>Legacy Agent Panel</h2>
<?php if (!empty($delegate_users)): ?>
    <!-- Edit delegate form post -->
    <form method="post" action="" id="edit_delegate_form"></form>

    <!-- Delegates CRUD table -->
    <table class="delegate-users-table">
        <thead>
            <tr>
                <th>Email</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Documented Proof of Death</th>
                <th>Status</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($delegate_users as $delegate_user): ?>
                <tr>
                    <td>
                        <?php echo esc_html($delegate_user['email']); ?>
                    </td>
                    <?php if ($delegate_user['registered']): ?>
                        <td>
                            <?php if (isset($_GET['edit_delegate_user']) && $_GET['edit_delegate_user'] == $delegate_user['ID']): ?>
                                <input type="text" form="edit_delegate_form" name="edit_delegate_first_name"
                                    id="edit_delegate_first_name"
                                    value="<?php echo esc_attr(get_user_meta($delegate_user['ID'], 'first_name', true)); ?>" required>
                            <?php else: ?>
                                <?php echo esc_html(get_user_meta($delegate_user['ID'], 'first_name', true)); ?>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if (isset($_GET['edit_delegate_user']) && $_GET['edit_delegate_user'] == $delegate_user['ID']): ?>
                                <input type="text" form="edit_delegate_form" name="edit_delegate_phone" id="edit_delegate_phone"
                                    value="<?php echo esc_attr(get_user_meta($delegate_user['ID'], 'phone', true)); ?>" required>
                            <?php else: ?>
                                <?php echo esc_html(get_user_meta($delegate_user['ID'], 'phone', true)); ?>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if (isset($_GET['edit_delegate_user']) && $_GET['edit_delegate_user'] == $delegate_user['ID']): ?>
                                <label>
                                    <input type="checkbox" form="edit_delegate_form" name="edit_delegate_documented_proof"
                                        id="edit_delegate_documented_proof" <?php checked(get_user_meta($delegate_user['ID'], 'documented_proof_of_death', true), true); ?>>
                                    Documented Proof of Death
                                </label>
                            <?php else: ?>
                                <?php echo get_user_meta($delegate_user['ID'], 'documented_proof_of_death', true) ? 'Yes' : 'No'; ?>
                            <?php endif; ?>
                        </td>




                        <td>Registered</td>


                        <td>
                            <?php if (isset($_GET['edit_delegate_user']) && $_GET['edit_delegate_user'] == $delegate_user['ID']): ?>
                                <input form="edit_delegate_form" type="hidden" name="edit_delegate_user_id"
                                    value="<?php echo esc_attr($delegate_user['ID']); ?>">
                                <?php wp_nonce_field('edit_delegate_user_nonce', 'edit_delegate_user_nonce'); ?>
                                <input form="edit_delegate_form" type="submit" name="submit_edit_delegate" value="Save">
                            <?php else: ?>
                                <a
                                    href="<?php echo esc_url(add_query_arg('edit_delegate_user', $delegate_user['ID'], home_url('/my-account/delegates/'))); ?>">Edit</a>
                            <?php endif; ?>
                        </td>

                        <td>
                            <form method="post" action="" id="delete_delegate_form">
                                <input type="hidden" name="delete_delegate_user_id"
                                    value="<?php echo esc_attr($delegate_user['ID']); ?>">
                                <input type="hidden" name="delete_delegate_user_email"
                                    value="<?php echo esc_attr($delegate_user['email']); ?>">
                                <button type="submit" class="delete-delegate-btn" name="delete_delegate_submit"
                                    onclick="return confirm('Are you sure you want to delete this legacy agent?')">Delete</button>
                            </form>
                        </td>
                    <?php else: ?>
                        <td colspan="3">Pending Registration</td>
                        <td>Not Registered</td>
                        <td></td>
                        <td>
                            <form method="post" action="" id="delete_delegate_form">
                                <input type="hidden" name="delete_delegate_user_id"
                                    value="<?php echo esc_attr($delegate_user['ID']); ?>">
                                <input type="hidden" name="delete_delegate_user_email"
                                    value="<?php echo esc_attr($delegate_user['email']); ?>">
                                <button type="submit" class="delete-delegate-btn" name="delete_delegate_submit"
                                    onclick="return confirm('Are you sure you want to delete this legacy agent?')">Delete</button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="capitalize">No legacy agents found.</p>
<?php endif; ?>