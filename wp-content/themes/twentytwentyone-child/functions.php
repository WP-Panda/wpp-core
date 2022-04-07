<?php

add_action( 'wp_enqueue_scripts', 'twenty_twenty_one_enqueue_styles' );
function twenty_twenty_one_enqueue_styles() {
	wp_enqueue_style( 'twenty-twenty-one-style-child', get_template_directory_uri() . '/style.css',['twenty-twenty-one-style'] );
}

require_once 'wpp-core/init.php';