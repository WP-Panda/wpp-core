<?php

function wpp_endpoint_account( $endpoints ) {
	/*
	* Пример массива
	 */

	$endpoints['account'] = [
		'for'      => 'logged',
		'title'    => __( 'My Account' ),
		'icons'    => '',
		'template' => '/templates/pages/list-models.php',
		'order'    => 5,
		//'caps'     => 'manage_options',
		'places'   => EP_ROOT
	];

	return $endpoints;
}

add_filter( 'wpp_endpoints_args', 'wpp_endpoint_account' );



function wpp_account_pages($pages){

	$pages['main'] = [
		'point' => 'account',
		'title'    => __( 'My Account' ),
		'icons'    => '',
		'template' => '/templates/pages/list-models.php',
		'order'    => 15,
	];

	$pages['page_2'] = [
		'point' => 'account',
		'title'    => __( 'My Account' ),
		'icons'    => '',
		'template' => '/templates/pages/list-models.php',
		'order'    => 5,
	];

	return $pages;

}