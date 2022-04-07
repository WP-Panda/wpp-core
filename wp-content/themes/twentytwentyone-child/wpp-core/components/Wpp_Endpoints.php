<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Wpp_Pf_Endpoints
 * Регистрация корнечных точек
 */
class Wpp_Endpoints {

	function __construct() {
		add_action( 'init', [
			__CLASS__,
			'add_endpoints'
		] );
		add_action( 'template_include', [
			__CLASS__,
			'change_template'
		] );

		#add_action( 'wpp_nav_points', [
		#	__CLASS__,
		#	'endpoints_nav'
		#] );

		//add_action( 'init', 'do_rewrite' );
	}


	function do_rewrite() {
		$args = self::endpoint_settings();
		foreach ( $args as $one_point => $val ) {
			// Правило перезаписи
			add_rewrite_rule( sprintf( '^(%s)/([^/]*)/([^/]*)/?', $one_point ),
				sprintf( 'index.php?%s=$matches[1]', $one_point ),
				'top' );
		}
	}


	/**
	 * Добавление конечных точек.
	 */
	public static function add_endpoints() {
		$args = self::endpoint_settings();

		foreach ( $args as $one_point => $val ) {
			if ( ! empty( $val['parent_point'] ) ) :
				continue;
			endif;

			if ( ! empty( $one_point ) ) {
				$mask = ! empty( $val['places'] ) ? esc_attr( $val['places'] ) : EP_ROOT;
				add_rewrite_endpoint( $one_point, $mask );
			}
		}
	}

	/**
	 * Get page title for an endpoint.
	 *
	 * @param string $endpoint Endpoint key.
	 *
	 * @return array
	 */
	public static function endpoint_settings() {
		$end_points = [];

		/*
		 * Пример массива
		 'for' => 'all', 'logged', 'not_logged'
		'models'      => array (
		'title'    => __( 'Item Title' ),
		'icons'    => '',
		'template' => '/templates/pages/list-models',
		'order'    => 5,
		'caps'     => 'manage_options',
		'places'   => EP_ROOT
		),
		 */

		return apply_filters( 'wpp_endpoints_args', [] );
	}

	/**
	 * Замена шаблона
	 *
	 * @param $template
	 *
	 * @return string
	 */
	public static function change_template( $template ) {
		$point = self::get_current_endpoint();

		if ( empty( $point ) ) {
			return $template;
		}

		$args = self::endpoint_settings();

		if ( get_query_var( $point, false ) !== false ) {

			//отдать 404 когда запрещен доступ
			if ( ! empty( $args[ $point ]['for'] ) && ( 'logged' === $args[ $point ]['for'] || 'not_logged' === $args[ $point ]['for'] ) ) {
				if ( ( is_user_logged_in() && 'not_logged' === $args[ $point ]['for'] ) || ( ! is_user_logged_in() && 'logged' === $args[ $point ]['for'] ) ) {
					//фильтр что бы не отдавать не 404
					$flag = apply_filters( 'wpp_end_point_404_send', true );

					$template = get_query_template( '404' );
					//фильр для замены 404 шаблока точки
					$template = apply_filters( "wpp_end_point_404_template", $template );

					if ( true === $flag ) {
						global $wp_query;
						$wp_query->set_404();
						status_header( 404 );
						nocache_headers();

						return $template;
					}
				}
			}


			if ( ! empty( $args[ $point ]['template'] ) ) {
				$template = wpp_get_template_part( $args[ $point ]['template'], [], true );
			}

			if ( empty( $template ) ) {
				$template = WPP_CORE_DIR . 'components/templates/default-endpoint.php';

				//фильтр для амены шаблона по умолчанию
				$template = apply_filters( "wpp_end_point_default_template", $template );
			}

			//фильтр для конкретной конечной точки
			$template = apply_filters( "wpp_end_{$point}_point_template", $template );
		}

		return $template;
	}

	/**
	 * Get query current active query var.
	 *
	 * @return string|bool
	 */
	public static function get_current_endpoint() {
		global $wp;

		$args = self::endpoint_settings();

		foreach ( $args as $key => $value ) {
			if ( isset( $wp->query_vars[ $key ] ) ) {
				return $key;
			}
		}

		return false;
	}

	public static function endpoints_nav() {
		$navs = self::endpoint_settings();
		uasort( $navs, wpp_fr_make_comparer( 'order' ) );
		$out = '';

		$one_item = <<<ITEM
			<li class="nav-item start">
			    <a href="%s" class="nav-link nav-toggle">
			        %s
			        <span class="title">%s</span>
			    </a>
			</li>
ITEM;

		foreach ( $navs as $nav => $item ) {
			//показывать в меню или нет
			if ( empty( $item['in-nav'] ) || true !== $item['in-nav'] ) {
				continue;
			}

			$out .= sprintf( $one_item,
				home_url( '/' . $nav ),
				! empty( $item['icons'] ) ? sprintf( '<i class="material-icons">%s</i>',
					esc_attr( $item['icons'] ) ) : null,
				esc_html( $item['title'] ) );

			// если есть дочерние страницы
			$child_pages = $item['child'];

			if ( ! empty( $child_pages ) && is_array( $child_pages ) ) :

				uasort( $child_pages, wpp_fr_make_comparer( 'order' ) );

				foreach ( $child_pages as $one_child => $child_item ) :

					if ( empty( $child_item['in-nav'] ) || true !== $child_item['in-nav'] ) {
						continue;
					}

					$out .= sprintf( $one_item,
						home_url( '/' . $nav . '/' . $one_child ),
						! empty( $child_item ['icons'] ) ? sprintf( '<i class="material-icons">%s</i>',
							esc_attr( $child_item ['icons'] ) ) : null,
						esc_html( $child_item ['title'] ) );

				endforeach;
			endif;
		}


		return $out;
	}
}

new Wpp_Endpoints();