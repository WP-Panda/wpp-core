<?php
/**
 * @package WppCore
 * @version 1.0.0
 * @author WP_Panda
 */

defined( 'ABSPATH' ) || exit;


class WppCore {

	/**
	 * WppCore version.
	 *
	 * @var string
	 */
	public $version = '0.8.5';

	/**
	 * The single instance of the class.
	 *
	 * @var WppCore
	 */
	protected static $_instance = null;


	/**
	 * Main WooCommerce Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @return WppCore - Main instance.
	 * @see WPP()
	 * @since 2.1
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * WppCore Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->start_classes();
	}

	/**
	 * Define WPP Constants.
	 */
	private function define_constants() {
		$this->define( 'WPP_CORE_VERSION', $this->version );
		$this->define( 'WPP_CORE_DIR', __DIR__ . '/' );
		$this->define( 'WPP_CORE_URL', get_stylesheet_directory_uri() . '/wpp-core/' );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string $name Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {

		$array = [
			//core functions
			'components/core-functions/for-templates',

			//сlasses
			'components/wpp-forms/Wpp_Forms',

			'components/WPP_Tax_Term_Img',
			'components/Wpp_Custom_Taxonomy',
			'components/Wpp_Assets',
			'components/Wpp_Endpoints',

			//helpers
			'components/helpers/init',

			//setting
			'for-themes/setting-filters',

			//test
			'test/init',
		];


		$this->require( $array, __DIR__ );
	}

	public function start_classes(){

		WPP_Tax_Term_Img::init();
		Wpp_Custom_Taxonomy::init();
		Wpp_Assets::init();
		new Wpp_Endpoints();
	}

	/**
	 * Подключение
	 * @param $files
	 * @param $dir
	 *
	 * @return false|void
	 */
	public function require( $files = [], $dir = null ) {

		if ( empty( $files ) ) {
			return false;
		}

		foreach ( $files as $file ) :

			$dir       = ! empty( $dir ) ? $dir : '';
			$file_path = $dir . '/' . $file . '.php';

			if ( file_exists( $file_path ) ) {
				require_once $file_path;
			}

		endforeach;
	}


}

/** Returns the main instance of WC.
 *
 * @return WppCore
 * @since  2.1
 */
function WPP() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return WppCore::instance();
}

// Global for backwards compatibility.
$GLOBALS['wpp'] = WPP();