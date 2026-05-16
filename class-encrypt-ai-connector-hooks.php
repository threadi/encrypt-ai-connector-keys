<?php
/**
 * File to handle the usage of hooks in this plugin.
 *
 * @package encrypt-ai-connector-keys
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Object to handle the usage of hooks in this plugin.
 */
class Encrypt_AI_Connector_Hooks {

	/**
	 * List of hooks.
	 *
	 * @var array<string,mixed>
	 */
	private static array $callbacks = array();

	/**
	 * Register a single hook to decrypt the value of a single connector.
	 *
	 * @param array<string,mixed> $connector The connector.
	 * @return void
	 */
	public static function register_decrypt( array $connector ): void {
		// create the hook name.
		$hook = 'option_' . $connector['authentication']['setting_name'];

		// prepare the callback.
		$callback = static function ( $value ) {
			return encrypt_ai_connector_keys_get_crypt_method()->decrypt( $value );
		};

		// add this hook to the list of all hooks with their callbacks.
		self::$callbacks[ $hook ] = $callback;

		// add the hook.
		add_filter( $hook, $callback );
	}

	/**
	 * Register a single hook to encrypt the value of a single connector.
	 *
	 * @param array<string,mixed> $connector The connector.
	 * @return void
	 */
	public static function register_encrypt( array $connector ): void {
		// create the hook name.
		$hook = 'pre_update_option_' . $connector['authentication']['setting_name'];

		// prepare the callback.
		$callback = static function ( $value ) {
			return encrypt_ai_connector_keys_get_crypt_method()->encrypt( $value );
		};

		// add this hook to the list of all hooks with their callbacks.
		self::$callbacks[ $hook ] = $callback;

		// add the hook.
		add_filter( $hook, $callback );
	}

	/**
	 * Remove all hooks we have set.
	 *
	 * @return void
	 */
	public static function remove_hooks(): void {
		foreach ( self::$callbacks as $hook => $callback ) {
			remove_filter( $hook, $callback );
		}
	}
}
