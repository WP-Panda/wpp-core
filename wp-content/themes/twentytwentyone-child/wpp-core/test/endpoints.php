<?php

function wpp_endpoint_test( $endpoints ) {
	/*
	* Пример массива
	 */

	$endpoints['wpp_test'] = [
		'for'      => 'all',
		'title'    => __( 'Wpp Test Endpoint' ),
		'icons'    => '',
		'template' => '/templates/pages/list-models.php',
		'order'    => 5,
		'caps'     => 'manage_options',
		'places'   => EP_ROOT
	];

	return $endpoints;
}

add_filter( 'wpp_endpoints_args', 'wpp_endpoint_test' );