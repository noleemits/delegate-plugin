<?php
// customer_automatewoo_registration_without_delegates_trigger.php
require_once ABSPATH . 'wp-content/plugins/automatewoo/automatewoo.php';

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

/**
 * Trigger with: do_action('customer_registration_without_delegates', $user_id );
 */
class Customer_AutomateWoo_Registration_Without_Delegates_Trigger extends \AutomateWoo\Trigger
{

    /**
     * Define which data items are set by this trigger, this determines which rules and actions will be available
     *
     * @var array
     */
    public $supplied_data_items = array('customer');

    /**
     * Set up the trigger
     */
    public function init()
    {
        $this->title = __('Customer Registration Without Delegates', 'automatewoo-custom');
        $this->group = __('Custom Triggers', 'automatewoo-custom');
    }

    /**
     * Add any fields to the trigger (optional)
     */
    public function load_fields()
    {
    }

    /**
     * Defines when the trigger is run
     */
    public function register_hooks()
    {
        add_action('customer_registration_without_delegates', array($this, 'catch_hooks'));
    }

    /**
     * Catches the action and calls the maybe_run() method.
     *
     * @param $user_id
     */
    public function catch_hooks($user_id)
    {

        // get/create customer object from the user id
        $customer = AutomateWoo\Customer_Factory::get_by_user_id($user_id);

        $this->maybe_run(
            array(
                'customer' => $customer,
            )
        );
    }

    /**
     * Performs any validation if required. If this method returns true the trigger will fire.
     *
     * @param $workflow AutomateWoo\Workflow
     * @return bool
     */
    public function validate_workflow($workflow)
    {
        return true;
    }
}
