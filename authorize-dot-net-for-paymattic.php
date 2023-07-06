<?php

/**
 * @package authorize-dot-net-for-paymattic
 * 
 */

/** 
 * Plugin Name: Authorize.net Payment for paymattic
 * Plugin URI: https://paymattic.com/
 * Description: Authorize.net payment gateway for paymattic. Authorize.net is a leading provider of payment gateway services.
 * Version: 1.0.0
 * Author: WPManageNinja LLC
 * Author URI: https://paymattic.com/
 * License: GPLv2 or later
 * Text Domain: authorize-dot-net-for-paymattic
 * Domain Path: /language
 */

if (!defined('ABSPATH')) {
    exit;
}

defined('ABSPATH') or die;

define('AUTHORIZE_DOT_NET_FOR_PAYMATTIC', true);
define('AUTHORIZE_DOT_NET_FOR_PAYMATTIC_DIR', __DIR__);
define('AUTHORIZE_DOT_NET_FOR_PAYMATTIC_URL', plugin_dir_url(__FILE__));
define('AUTHORIZE_DOT_NET_FOR_PAYMATTIC_VERSION', '1.0.0');


if (!class_exists('AuthorizeDotNetForPaymattic')) {
    class AuthorizeDotNetForPaymattic
    {
        public function boot()
        {
            if (!class_exists('AuthorizeDotNetForPaymattic\API\AuthorizeProcessor')) {
                $this->init();
            };
        }

        public function init()
        {
            require_once AUTHORIZE_DOT_NET_FOR_PAYMATTIC_DIR . '/API/AuthorizeProcessor.php';
            (new AuthorizeDotNetForPaymattic\API\AuthorizeProcessor())->init();

            $this->loadTextDomain();
        }

        public function loadTextDomain()
        {
            load_plugin_textdomain('authorize-dot-net-for-paymattic', false, dirname(plugin_basename(__FILE__)) . '/language');
        }

        public function hasPro()
        {
            return defined('WPPAYFORMPRO_DIR_PATH') || defined('WPPAYFORMPRO_VERSION');
        }

        public function hasFree()
        {

            return defined('WPPAYFORM_VERSION');
        }

        public function versionCheck()
        {
            $currentFreeVersion = WPPAYFORM_VERSION;
            $currentProVersion = WPPAYFORMPRO_VERSION;

            return version_compare($currentFreeVersion, '4.3.2', '>=') && version_compare($currentProVersion, '4.3.2', '>=');
        }

        public function renderNotice()
        {
            add_action('admin_notices', function () {
                if (current_user_can('activate_plugins')) {
                    echo '<div class="notice notice-error"><p>';
                    echo __('Please install & Activate Paymattic and Paymattic Pro to use authorize-dot-net-for-paymattic plugin.', 'authorize-dot-net-for-paymattic');
                    echo '</p></div>';
                }
            });
        }

        public function updateVersionNotice()
        {
            add_action('admin_notices', function () {
                if (current_user_can('activate_plugins')) {
                    echo '<div class="notice notice-error"><p>';
                    echo __('Please update Paymattic and Paymattic Pro to use authorize-dot-net-for-paymattic plugin!', 'authorize-dot-net-for-paymattic');
                    echo '</p></div>';
                }
            });
        }
    }

    add_action('init', function () {

        $authorize = (new AuthorizeDotNetForPaymattic);

        if (!$authorize->hasFree() || !$authorize->hasPro()) {
            $authorize->renderNotice();
        } else if (!$authorize->versionCheck()) {
            $authorize->updateVersionNotice();
        } else {
            $authorize->boot();
        }
    });
}
