<?php
/**
 * @package WppCore
 * @version 1.0.0
 * @author WP_Panda
 */

defined( 'ABSPATH' ) || exit;


$array = [
	'WPP_Tax_Term_Img',
	'Wpp_Custom_Taxonomy',
	'Wpp_Assets',
	'helpers/init'
];

wpp_require( $array, __DIR__ );