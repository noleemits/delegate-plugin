<?php
// custom-triggers.php

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

/**
 * User Profile functions and actions to show and save extra profile fields
 */
function delegates_show_extra_profile_fields($user)
{
    ?>
    <h3>Legacy Agent Info</h3>

    <table class="form-table">
        <!-- Add documented_proof_of_death field -->
        <tr>
            <th><label for="documented_proof_of_death">Documented Proof of Death</label></th>
            <td><input type="checkbox" name="documented_proof_of_death" id="documented_proof_of_death" <?php checked(get_user_meta($user->ID, 'documented_proof_of_death', true), true); ?>></td>
        </tr>
        <!-- Add date_of_birth field -->
        <tr>
            <th><label for="date_of_birth">Date of Birth</label></th>
            <td><input type="date" name="date_of_birth" id="date_of_birth"
                    value="<?php echo esc_attr(get_user_meta($user->ID, 'date_of_birth', true)); ?>"></td>
        </tr>
        <!-- Add skip_delegate field -->
        <tr>
            <th><label for="skip_delegate">Skip assigning a Legacy Agent for now</label></th>
            <td><input type="checkbox" name="skip_delegate" id="skip_delegate" <?php checked(get_user_meta($user->ID, 'skip_delegate', true), 'on'); ?>></td>
        </tr>
        <!-- Add delegate_emails field -->
        <tr>
            <th><label for="delegate_emails">Legacy Agents Email</label></th>
            <td>
                <?php
                $delegate_emails = get_user_meta($user->ID, 'delegate_emails', true);
                $delegate_emails_display = !empty($delegate_emails) ? implode(',', array_map('trim', explode(',', $delegate_emails))) : '';
                ?>
                <textarea name="delegate_emails" id="delegate_emails" rows="3"
                    cols="50"><?php echo esc_textarea($delegate_emails_display); ?></textarea>
                <p class="description">Enter legacy agent's email addresses, separated by commas without spaces.</p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'delegates_show_extra_profile_fields');
add_action('edit_user_profile', 'delegates_show_extra_profile_fields');



function delegates_save_extra_profile_fields($user_id)
{
    // Check if the current user has permission to edit the user profile
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    // Save the 'Documented Proof of Death' field (checkbox)
    $documented_proof_of_death = isset($_POST['documented_proof_of_death']) ? true : false;
    update_user_meta($user_id, 'documented_proof_of_death', $documented_proof_of_death);

    // Save the 'Date of Birth' field
    $date_of_birth = isset($_POST['date_of_birth']) ? sanitize_text_field($_POST['date_of_birth']) : '';
    update_user_meta($user_id, 'date_of_birth', $date_of_birth);

    // Save the 'Skip assigning a delegate' field
    $skip_delegate = isset($_POST['skip_delegate']) ? sanitize_text_field($_POST['skip_delegate']) : 'off';
    update_user_meta($user_id, 'skip_delegate', $skip_delegate);

    // Save the 'Delegate Emails' field as a string
    $delegate_emails = isset($_POST['delegate_emails']) ? sanitize_text_field($_POST['delegate_emails']) : '';

    if (!empty($delegate_emails)) {
        update_user_meta($user_id, 'delegate_emails', $delegate_emails);
    } else {
        // If the field is empty, remove the meta key to avoid storing old data
        delete_user_meta($user_id, 'delegate_emails');
    }
}
add_action('personal_options_update', 'delegates_save_extra_profile_fields');
add_action('edit_user_profile_update', 'delegates_save_extra_profile_fields');

