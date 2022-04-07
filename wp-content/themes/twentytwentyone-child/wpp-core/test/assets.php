<?php
/**
 * Описание файлв
 *
 * @package constructor.wpp
 * @version 1.0.0
 * @author WP_Panda
 */

defined( 'ABSPATH' ) || exit;

function wpp_assets_test( $array ) {
	$array['tester_js'] = [
		'url'        => 'http://yponton.rus/wp-includes/js/jquery/ui/mouse.js', //ссылка
		'screen'     => 'single', //экран
		'too'        => '', // front admin
		'version'    => '888', //версия
		'has_min'    => '', //есть ли минимифицированная версия
		'depth'      => [ 'jquery' ],
		'register'   => false, // вызов или регистрация
		'footer'     => true, //футер или хэдер
		'attributes' => [
			'goglgog'     => 'dddddddddddddddddd',
			'dfdfgdufgnd' => 'fffffffffff'
		],
		'defer'     => true,
		'localize'=>[
			'str_1' => 'vvvvvvvvvvvvv',
			'str_2' => 'thththt',
		]
	];

	return $array;
}

add_filter( 'wpp_scripts', 'wpp_assets_test' );
