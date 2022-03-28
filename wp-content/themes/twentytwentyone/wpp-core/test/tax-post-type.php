<?php

function wpp_reg( $array ) {
	$array['case_items'] = [
		'cir'          => true,
		'single'       => 'Пункт',
		'nominative'   => 'Пункт', //именительный падеж кто? что?
		'genitive'     => 'Пункт', //родительный кого? чего?
		'dative'       => 'Пункт', // дательный кого? что?
		'instrumental' => 'Пунктом', //творительный кем? чем?
		'plural'       => 'Пункты', //множественное
		'plurals'      => 'Пунктов'// множественное 2
	];


	return $array;
}

//add_filter( 'wpp_register_post_types', 'wpp_reg' );

/**
 * Регистрация кастомной таксономии
 *
 * @param $array
 *
 * @return mixed
 */
function wpp_reg_case( $array ) {
	$array['cases'] = [
		'img'          => true,
		'cir'          => true,
		'single'       => 'Кейс',
		'nominative'   => 'Кейс', //именительный падеж кто? что?
		'genitive'     => 'Кейс', //родительный кого? чего?
		'dative'       => 'Кейс', // дательный кого? что?
		'instrumental' => 'Кейсом', //творительный кем? чем?
		'plural'       => 'Кейсы', //множественное
		'plurals'      => 'Кейсов',// множественное 2,
		'post_types'   => [ 'post' ]
	];

	return $array;
}

add_filter( 'wpp_register_taxonomies', 'wpp_reg_case' );


/**
 *
 */
function wpp_img_tests( $args ) {
	$args['category'] = 'category';

	return $args;
}

add_filter( 'wpp_tax_imgs_targets', 'wpp_img_tests', 50 );