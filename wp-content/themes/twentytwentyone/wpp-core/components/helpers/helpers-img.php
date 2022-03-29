<?php

defined( 'ABSPATH' ) || exit;


if ( ! function_exists( 'wpp_image_placeholder' ) ) :

	/**
	 * Изображение заполнитель
	 */
	function wpp_image_placeholder( $image = null, $return = 'array', $size = 'thumbinail' ) {
		if ( ! empty( $image ) ) {
			$uploads   = wp_upload_dir();
			$file_path = str_replace( $uploads['baseurl'], $uploads['basedir'], $image[0] );
		} else {
			$image = [];
		}

		if ( empty( $file_path ) || ! file_exists( $file_path ) ) {
			$placeholder_image = get_option( 'wpp_placeholder_image', 0 );

			if ( empty( $placeholder_image ) ) {
				$image[0] = WPP_CORE_URL . 'assets/img/placeholder.jpg';
			} else {
				$image_opt = wp_get_attachment_image_src( $placeholder_image );

				if ( ! empty( $image_opt ) ) {
					$image[0] = $image_opt[0];
				}
			}
		}

		$img = 'array' === $return ? $image : $image[0];

		return apply_filters( 'wpp_placeholder_img', $img, $return );
	}


endif;