<?php
/**
 * File for the main function to load the crypt method.
 *
 * @package encrypt-ai-connector-keys
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use CryptForWordPress\Crypt;

/**
 * Return the crypt object to use.
 *
 * @return Crypt
 */
function encrypt_ai_connector_keys_get_crypt_method(): Crypt {
	/**
	 * Variable for the object.
	 */
	static $crypt_object = null;

	/**
	 * Get the object one time.
	 */
	if ( null === $crypt_object ) {
		// define the configuration for encryption of strings.
		$config = array(
			'openssl' => array(
				'hash_algorithm' => 'sha256',
			),
		);

		/**
		 * Filter the configuration for encryption of strings.
		 *
		 * @since 1.0.0 Available since 1.0.0.
		 * @param array<string,mixed> $config The configuration.
		 */
		$config = apply_filters( 'encrypt_ai_connector_keys_crypt_config', $config );

		// get the crypt object.
		$crypt_object = new Crypt( __FILE__ );

		// add its configuration.
		$crypt_object->set_config( $config );
	}

	// return the crypt object.
	return $crypt_object;
}
