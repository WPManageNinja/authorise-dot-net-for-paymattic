<?php

namespace AuthorizeDotNetForPaymattic\Settings;

use WPPayForm\App\Modules\FormComponents\BaseComponent;
use WPPayForm\Framework\Support\Arr;

if (!defined('ABSPATH')) {
    exit;
}

class AuthorizeElement extends BaseComponent
{
    public $gateWayName = 'authorize';

    public function __construct()
    {
        parent::__construct('authorize_gateway_element', 8);

        add_action('wppayform/validate_gateway_api_' . $this->gateWayName, array($this, 'validateApi'));
        add_filter('wppayform/validate_gateway_api_' . $this->gateWayName, function ($data, $form) {
            return $this->validateApi();
        }, 2, 10);
        add_action('wppayform/payment_method_choose_element_render_authorize', array($this, 'renderForMultiple'), 10, 3);
        add_filter('wppayform/available_payment_methods', array($this, 'pushPaymentMethod'), 2, 1);
    }

    public function pushPaymentMethod($methods)
    {
        $methods['authorize'] = array(
            'label' => 'authorize',
            'isActive' => true,
            'logo' => XENDIT_PAYMENT_FOR_PAYMATTIC_URL . 'assets/authorize.svg',
            'editor_elements' => array(
                'label' => array(
                    'label' => 'Payment Option Label',
                    'type' => 'text',
                    'default' => 'Pay with authorize.net'
                )
            )
        );
        return $methods;
    }


    public function component()
    {
        return array(
            'type' => 'authorize_gateway_element',
            'editor_title' => 'Authorize.net Payment',
            'editor_icon' => '',
            'conditional_hide' => true,
            'group' => 'payment_method_element',
            'method_handler' => $this->gateWayName,
            'postion_group' => 'payment_method',
            'single_only' => true,
            'editor_elements' => array(
                'label' => array(
                    'label' => 'Field Label',
                    'type' => 'text'
                )
            ),
            'field_options' => array(
                'label' => __('Xendit Payment Gateway', 'authorize-dot-net-for-paymattic')
            )
        );
    }

    public function validateApi()
    {
        $authorize = new AuthorizeSettings();
        return $authorize->getApiKeys();
    }

    public function render($element, $form, $elements)
    {
        do_action('wppayform_load_checkout_js_authorize');
        if (!$this->validateApi()) { ?>
            <p style="color: red">You did not configure Authorize.net payment gateway. Please configure authorize.net payment
                gateway from <b>Paymattic->Payment Gateway->Authorize.net Settings</b> to start accepting payments</p>
<?php return;
        }

        echo '<input data-wpf_payment_method="authorize" type="hidden" name="__authorize_payment_gateway" value="authorize" />';
    }

    public function renderForMultiple($paymentSettings, $form, $elements)
    {
        do_action('wppayform_load_checkout_js_authorize');
        $component = $this->component();
        $component['id'] = 'authorize_gateway_element';
        $component['field_options'] = $paymentSettings;
        $this->render($component, $form, $elements);
    }
}
