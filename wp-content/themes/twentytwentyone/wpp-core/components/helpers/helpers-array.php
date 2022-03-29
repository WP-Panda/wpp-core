<?php
/**
 * Вспомогательные функции для массивов
 *
 * @package wp.dev
 * @version 1.0.0
 * @author WP_Panda
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sanitize array by empty elements
 * Удаление пустых элементов из массива
 *
 * @param $array
 *
 * @return array|bool
 */
function wpp_empty_array_clear( $array ) {

	if ( ! is_array( $array ) ) {
		return false;
	}

	foreach ( $array as $k => $v ) {
		if ( is_array( $v ) ) {
			$array[ $k ] = wpp_empty_array_clear( $v );
			if ( count( $array[ $k ] ) == false ) {
				unset( $array[ $k ] );
			}
		} else {
			if ( $v === '' || $v === null || $v == false ) {
				unset( $array[ $k ] );
			}
		}
	}

	return $array;
}