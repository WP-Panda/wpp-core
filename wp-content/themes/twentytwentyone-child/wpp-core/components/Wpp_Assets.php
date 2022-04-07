<?php

class Wpp_Assets {

	private static $scripts = [];
	private static $styles = [];
	private static $preff = 'arb_att_&#';
	private static $pattern = '/arb_att_&#(.+)/';


	public static function init() {
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'parse_scripts' ] );
		add_filter( 'script_loader_tag', [ __CLASS__, 'script_loader_tag' ], 10, 3 );
		add_filter( 'style_loader_tag', [ __CLASS__, 'style_loader_tag' ], 10, 4 );
	}


	/**
	 * Массив скриптов
	 * @return mixed|void
	 */
	private static function get_scripts() {
		/*
		 пример элемента массива
		'id' => [
		'logged' => 'all', 'login', 'not_login'
			//'url'        => '', //ссылка
			'screen'     => '', //экран
			'too'        => '', // front admin
			//'version'    => '', //версия
			'has_min'    => '', //есть ли минимифицированная версия
			//'depth'      => [],
			//'register'   => false, // вызов или регистрация
			//'footer'     => true, //футер или хэдер
			//'attributes' => [
				'' => ''
			],
		//'localize'=[]
		]; */
		return apply_filters( 'wpp_scripts', self::$scripts );
	}

	/**
	 * Массив стилей
	 * @return mixed|void
	 */
	private static function get_styles() {
		/*
		 пример элемента массива
		'id' => [

			//'url'        => '', //ссылка
			'screen'     => '', //экран
			'too'        => '', // front admin
			//'version'    => '', //версия
			'has_min'    => '', //есть ли минимифицированная версия
			//'depth'      => [],
			'register'   => false, // вызов или регистрация
			'footer'     => true, //футер или хэдер
			'attributes' => [
				'' => ''
			],
		'media' => ''
		]; */
		return apply_filters( 'wpp_styles', self::$styles );
	}

	/**
	 * Пдключение скриптов
	 * @return void
	 */
	public static function parse_scripts() {
		$scripts_array = self::get_scripts();

		if ( ! empty( $scripts_array ) ) {
			foreach ( $scripts_array as $script_key => $script_data ) {
				if ( ! empty( $script_data['url'] ) ) {

					//если только для зареганных или для не зареганных
					if ( ! empty( $script_data['logged'] ) ) {
						if ( ( 'login' === $script_data['logged'] && ! is_user_logged_in() ) || ( 'not_login' === $script_data['logged'] && is_user_logged_in() ) ) {
							continue;
						}
					}

					//если только для определнных экранов
					if ( ! empty( $script_data['screen'] ) ) {
						if ( ! call_user_func( 'is_' . $script_data['screen'] ) ) {
							continue;
						}
					}

					//регистрация скрипта
					wp_register_script( $script_key,
						sanitize_url( $script_data['url'] ),
						$script_data['depth'] ?? [],
						$script_data['version'] ?? null,
						false !== (bool) $script_data['footer']
					);

					//вызов скрипта
					if ( empty( $script_data['register'] ) ) {
						wp_enqueue_script( $script_key );
					}

					//Добавление произвольных атрибутов
					if ( ! empty( $script_data['attributes'] ) && is_array( $script_data['attributes'] ) ) {
						foreach ( $script_data['attributes'] as $key => $val ) {
							wp_script_add_data( $script_key, self::$preff . $key, $val );
						}
					}

					//
					if ( ! empty( $script_data['localize'] ) && is_array( $script_data['localize'] ) ) {
						if ( empty( $script_data['localize']['ajax_url'] ) ) {
							$script_data['localize']['ajax_url'] = admin_url( 'admin-ajax.php' );
						}

						if ( empty( $script_data['localize']['security'] ) ) {
							$script_data['localize']['security'] = wp_create_nonce( $script_key );
						}

						wp_localize_script( $script_key,
							self::convert_id_to_js_key( $script_key ),
							$script_data['localize'] );
					}
				}
			}
		}

		$styles_array = self::get_styles();

		if ( ! empty( $styles_array ) ) {
			foreach ( $styles_array as $style_key => $style_data ) {
				if ( ! empty( $style_data['url'] ) ) {
					//регистрация стиля
					wp_register_style( $style_key,
						sanitize_url( $style_data['url'] ),
						$style_data['depth'] ?? [],
						$style_data['version'] ?? null,
						$style_data['media'] ?? ''
					);

					//вызов скрипта
					if ( empty( $style_data['register'] ) ) {
						wp_enqueue_style( $style_key );
					}

					//Добавление произвольных атрибутов
					if ( ! empty( $style_data['attributes'] ) && is_array( $style_data['attributes'] ) ) {
						foreach ( $style_data['attributes'] as $key => $val ) {
							wp_style_add_data( $style_key, self::$preff . $key, $val );
						}
					}
				}
			}
		}
	}

	/**
	 * Добавление произвольных атрибутов
	 *
	 * @param $tag
	 * @param $handle
	 * @param $src
	 * @param $media
	 * @param $isStyle
	 *
	 * @return mixed|string
	 * @see https://gist.github.com/WP-Panda/847599069b17c9a3778cee4fa010a359
	 *
	 */
	private static function custom_atts( $tag, $handle, $src, $media, $isStyle ) {
		$extraAttrs = [];
		$nodeName   = '';

		// Get the WP_Dependency instance for this handle, and grab any extra fields
		if ( $isStyle ) {
			$nodeName   = 'link';
			$extraAttrs = wp_styles()->registered[ $handle ]->extra;
		} else {
			$nodeName   = 'script';
			$extraAttrs = wp_scripts()->registered[ $handle ]->extra;
		}

		// Check stored properties on WP resource instance against our pattern
		$attribsToAdd = [];
		foreach ( $extraAttrs as $fullAttrKey => $attrVal ) {
			$matches = [];
			preg_match( self::$pattern, $fullAttrKey, $matches );
			if ( count( $matches ) > 1 ) {
				$attrKey                  = $matches[1];
				$attribsToAdd[ $attrKey ] = $attrVal;
			}
		}

		// Actually do the work of adding attributes to $tag
		if ( count( $attribsToAdd ) ) {
			$dom = new DOMDocument();
			@$dom->loadHTML( $tag );
			/** @var {DOMElement[]} */
			$resourceTags = $dom->getElementsByTagName( $nodeName );
			foreach ( $resourceTags as $resourceTagNode ) {
				foreach ( $attribsToAdd as $attrKey => $attrVal ) {
					$resourceTagNode->setAttribute( $attrKey, $attrVal );
				}
			}
			$headStr = $dom->saveHTML( $dom->getElementsByTagName( 'head' )[0] );
			// Capture content between <head></head>. Kind of hackish, but should be faster than preg_match
			$content = substr( $headStr, 7, ( strlen( $headStr ) - 15 ) );

			return $content;
		}

		return $tag;
	}

	public static function script_loader_tag( $tag, $handle, $src ) {
		return self::custom_atts( $tag, $handle, $src, null, false );
	}

	public static function style_loader_tag( $tag, $handle, $src, $media ) {
		return self::custom_atts( $tag, $handle, $src, $media, true );
	}

	/**
	 * Преобразование ключа в ID
	 *
	 * @param $string
	 *
	 * @return array|string|string[]
	 */
	private static function convert_id_to_js_key( $string ) {
		$pre_str = ucwords( str_replace( [ '-', '_' ], ' ', $string ) );
		$string  = str_replace( ' ', '', $pre_str );

		return $string;
	}
}