jQuery(function($) {
    // Function to toggle delegate email fields and labels based on checkbox state
    function toggleDelegateEmailFields() {
        var skipDelegateCheckbox = $('#skip_delegate');
        var emailFields = $('#register_delegate_email_1, #register_delegate_email_2');
        var emailLabels = $('label[for^="register_delegate_email_"]');
        var emailDescriptions = $('#register_delegate_email_2-description');

        emailFields.toggle(!skipDelegateCheckbox.prop('checked'));
        emailLabels.toggle(!skipDelegateCheckbox.prop('checked'));
        emailDescriptions.toggle(!skipDelegateCheckbox.prop('checked'));
        // emailFields.prop('required', !skipDelegateCheckbox.prop('checked'));
    }

    // Initial toggle on page load
    toggleDelegateEmailFields();

    // Bind the toggle function to the change event of the checkbox
    $('#skip_delegate').change(toggleDelegateEmailFields);
});