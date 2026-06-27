<?php
/**
 * File, which handles the updates from GitHub repository for this plugin.
 *
 * @package encrypt-ai-connector-keys
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Object to hande updates from GitHub repository for this plugin.
 */
class Encrypt_Ai_Updates {
	/**
	 * The GitHub user.
	 *
	 * @var string
	 */
	private string $github_user = 'threadi';

	/**
	 * The GitHub repository.
	 *
	 * @var string
	 */
	private string $github_repository = 'encrypt-ai-connector-keys';

	/**
	 * Instance of actual object.
	 *
	 * @var ?Encrypt_Ai_Updates
	 */
	private static ?Encrypt_Ai_Updates $instance = null;

	/**
	 * Constructor, not used as this a Singleton object.
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return instance of this object as singleton.
	 *
	 * @return Encrypt_Ai_Updates
	 */
	public static function get_instance(): Encrypt_Ai_Updates {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check' ), 100, 1 );
	}

	/**
	 * Retrieve update infos from the GitHub repository.
	 *
	 * @param object $data The object with the plugin-data.
	 *
	 * @return object
	 */
	public function check( object $data ): object {
		// bail if we are in development mode.
		if ( wp_is_development_mode( 'plugin' ) ) {
			return $data;
		}

		// bail if we already have data.
		if ( ! empty( $data->response[ plugin_basename( ENCAIKEYS ) ] ) ) {
			return $data;
		}

		// create URL for request.
		$url = 'https://api.github.com/repos/' . $this->github_user . '/' . $this->github_repository . '/releases/latest';

		// create HTTP header.
		$args     = array(
			'method'      => 'GET',
			'httpversion' => '1.1',
			'timeout'     => 30,
			'redirection' => 0,
			'headers'     => array(
				'Accept: application/json',
			),
			'body'        => array(),
		);
		$response = wp_remote_get( $url, $args );

		// bail on error.
		if ( is_wp_error( $response ) ) {
			return $data;
		}

		// bail if http status is not 200.
		if ( 200 !== absint( wp_remote_retrieve_response_code( $response ) ) ) {
			return $data;
		}

		// get the contents from the response.
		$response_data = wp_remote_retrieve_body( $response );

		// get contents as array.
		$file = json_decode( $response_data );

		// format the plugin-data from GitHub to WordPress object.
		if ( $file ) {
			// bail if no asset is available.
			if ( empty( $file->assets ) ) {
				return $data;
			}

			// get the new version number from GitHub.
			$new_version_number = preg_replace( '/[^0-9.]/', '', $file->tag_name ); // @phpstan-ignore property.notFound

			// only return a response if the new version number is higher than the current version.
			if ( version_compare( $new_version_number, ENCAIKEYS_VERSION, '>' ) ) {
				foreach ( $file->assets as $asset ) {
					// bail if this asset is not a ZIP file or entry already exist.
					if ( 'application/zip' !== $asset->content_type || ! empty( $data->response[ plugin_basename( ENCAIKEYS ) ] ) ) { // @phpstan-ignore property.notFound
						continue;
					}

					// create the object with the infos.
					$res              = new stdClass();
					$res->slug        = $this->github_repository; // @phpstan-ignore property.notFound
					$res->plugin      = plugin_basename( ENCAIKEYS );
					$res->new_version = $new_version_number; // @phpstan-ignore property.notFound
					$res->tested      = '7.0'; // @phpstan-ignore property.notFound
					$res->package     = $asset->browser_download_url; // @phpstan-ignore property.notFound

					// add it to the data object.
					$data->response[ plugin_basename( ENCAIKEYS ) ] = $res; // @phpstan-ignore property.notFound
				}
			} else {
				// set info about no available update.
				$res = (object) array(
					'id'            => plugin_basename( ENCAIKEYS ),
					'slug'          => $this->github_repository,
					'plugin'        => plugin_basename( ENCAIKEYS ),
					'new_version'   => ENCAIKEYS_VERSION,
					'url'           => '',
					'package'       => '',
					'icons'         => array(),
					'banners'       => array(),
					'banners_rtl'   => array(),
					'tested'        => '',
					'requires_php'  => '',
					'compatibility' => new stdClass(),
				);
				$data->no_update[ plugin_basename( ENCAIKEYS ) ] = $res; // @phpstan-ignore property.notFound
			}
		}

		// return resulting data.
		return $data;
	}
}
