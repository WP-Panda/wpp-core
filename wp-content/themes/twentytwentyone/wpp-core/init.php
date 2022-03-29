<?php
/**
 * @package WppCore
 * @version 1.0.0
 * @author WP_Panda
 */

defined( 'ABSPATH' ) || exit;

define( 'WPP_CORE_DIR', __DIR__ . '/' );
define('WPP_CORE_URL', get_stylesheet_directory_uri() . '/wpp-core/');

/**
 * Подключение файлов
 *
 * @param $files
 * @param $dir
 *
 * @return false|void
 */
if ( ! function_exists( 'wpp_require' ) ) :
	function wpp_require( $files = [], $dir = null ) {
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
endif;

$array = [
	'components/init',
	'test/tax-post-type'
];

wpp_require( $array, __DIR__ );