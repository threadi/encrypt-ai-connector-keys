<?php
/**
 * Plugin Name:       Encrypt AI Connector Keys
 * Description:       Do not store your API connector keys in plain text in your database. Encrypt them.
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
require_once __DIR__ . '/class-encrypt-ai-connector-hooks.php';

/**
 * Set the callbacks to encrypt and decrypt the AI keys.
 *
 * Hint: priority must be lower than on "_wp_connectors_pass_default_keys_to_ai_client" but higher than 10.
 *
 * @return void
 */
function encrypt_ai_connector_keys_set_callbacks(): void {
	// bail if wp_get_connectors() does not exist.
	if ( ! function_exists( 'wp_get_connectors' ) ) {
		return;
	}

	// check each connector.
	foreach ( wp_get_connectors() as $connector ) {
		// bail if "setting_name" is not set.
		if ( empty( $connector['authentication']['setting_name'] ) ) {
			continue;
		}

		// add the hook to read the value.
		Encrypt_AI_Connector_Hooks::register_decrypt( $connector );

		// add the hook to write the encrypted value.
		Encrypt_AI_Connector_Hooks::register_encrypt( $connector );
	}
}
add_action( 'init', 'encrypt_ai_connector_keys_set_callbacks', 15 );

/**
 * Encrypt existing keys on activation.
 *
 * @return void
 */
function encrypt_ai_connector_activation(): void {
	// bail if wp_get_connectors() does not exist.
	if ( ! function_exists( 'wp_get_connectors' ) ) {
		return;
	}

	// check each connector.
	foreach ( wp_get_connectors() as $connector ) {
		// bail if "setting_name" is not set.
		if ( empty( $connector['authentication']['setting_name'] ) ) {
			continue;
		}

		// get the actual value.
		$key = get_option( $connector['authentication']['setting_name'] );

		// bail if no key is set.
		if ( empty( $key ) ) {
			continue;
		}

		// save it encrypted.
		update_option( $connector['authentication']['setting_name'], encrypt_ai_connector_keys_get_crypt_method()->encrypt( $key ) );
	}
}
register_activation_hook( __FILE__, 'encrypt_ai_connector_activation' );

/**
 * Decrypt encrypted keys on deactivation.
 *
 * @return void
 */
function encrypt_ai_connector_deactivation(): void {
	// bail if wp_get_connectors() does not exist.
	if ( ! function_exists( 'wp_get_connectors' ) ) {
		return;
	}

	// remove all hooks.
	Encrypt_AI_Connector_Hooks::remove_hooks();

	// check each connector.
	foreach ( wp_get_connectors() as $connector ) {
		// bail if "setting_name" is not set.
		if ( empty( $connector['authentication']['setting_name'] ) ) {
			continue;
		}

		// get the actual value.
		$key = get_option( $connector['authentication']['setting_name'] );

		// bail if no key is set.
		if ( empty( $key ) ) {
			continue;
		}

		// save it decrypted.
		update_option( $connector['authentication']['setting_name'], encrypt_ai_connector_keys_get_crypt_method()->decrypt( $key ) );
	}
}
register_deactivation_hook( __FILE__, 'encrypt_ai_connector_deactivation' );
