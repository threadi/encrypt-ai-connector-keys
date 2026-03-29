<?php
/**
 * Plugin Name:       Encrypt AI Connector Keys
 * Description:       Do not save your API connector keys unencrypted in your database. Let them encrypt.
 * Requires at least: 7.0
 * Requires PHP:      8.0
 * Version:           @@VersionNumber@@
 * Author:            Thomas Zwirner
 * Author URI:        https://www.thomaszwirner.de
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       encrypt-ai-connector-keys
 *
 * @package encrypt-ai-connector-keys
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

// do nothing if PHP-version is not 8.0 or newer.
if ( PHP_VERSION_ID < 80000 ) { // @phpstan-ignore if.alwaysFalse,smaller.alwaysFalse
	return;
}

// include the autoloader.
require_once __DIR__ . '/vendor/autoload.php';

// include the main file with the crypt loader.
require_once __DIR__ . '/settings.php';

/**
 * Set the callbacks to encrypt and decrypt the AI keys.
 *
 * @return void
 */
function encrypt_ai_connector_keys_set_callbacks(): void {
	// check each connector.
	foreach ( wp_get_connectors() as $connector ) {
		// bail if setting_name is not set.
		if ( empty( $connector['authentication']['setting_name'] ) ) {
			continue;
		}

		// add a filter to decrypt the settings string on reading this option.
		add_filter(
			'option_' . $connector['authentication']['setting_name'],
			static function ( $value ) {
				return encrypt_ai_connector_keys_get_crypt_method()->decrypt( $value );
			}
		);

		// add a filter to encrypt the string during saving it.
		add_filter(
			'pre_update_option_' . $connector['authentication']['setting_name'],
			static function ( $value ) {
				return encrypt_ai_connector_keys_get_crypt_method()->encrypt( $value );
			}
		);
	}
}
add_action( 'init', 'encrypt_ai_connector_keys_set_callbacks', 100 );
