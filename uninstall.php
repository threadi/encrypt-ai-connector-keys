<?php
/**
 * Tasks to run during uninstallation of this plugin.
 *
 * @package encrypt-ai-connector-keys
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

// if uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// include the autoloader.
require_once __DIR__ . '/vendor/autoload.php';

// include the main file with the crypt loader.
require_once __DIR__ . '/settings.php';

// run the uninstallation for the crypt methods.
encrypt_ai_connector_keys_get_crypt_method()->uninstall();
