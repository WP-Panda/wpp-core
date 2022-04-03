<?php

/**
 *
 * @param       $file
 * @param array $args
 * @param array $cache_args
 *
 * @return bool|string
 * @version 1.0.6
 *
 * @since   1.0.0
 */

/**
 * подключение файла
 *
 * @param string $file - путь к файлу
 * @param array $args - передаваемые в него параметры
 * @param bool $return - вернуть или напечатать
 * @param bool $in_core - вывести из ядра
 *
 * @return false|string|void
 */
function wpp_get_template_part( $file, $args = [], $return = false, $in_core = false ) {

	//$args = wp_parse_args( $args );

	//поиск в текущей теме если переопределено в дочерней
	$template = get_stylesheet_directory() . '/' . $file . '.php';

	//если файл не найден поищем в родительской теме
	if ( ! file_exists( $template ) ) {
		$fallback = get_template_directory() . '/' . $file . '.php';
		$template = file_exists( $fallback ) ? $fallback : false;
	}

	//если файла нет и указано подключение из ядра, то подключим из ядра
	if ( empty( $template ) && ! empty( $in_core ) ) {
		$template = WPP_CORE_DIR . '/' . $file . '.php';
	}


	if ( is_file( $template ) ) {
		ob_start();
		require( $template );
		$data   = ob_get_clean();

		//если надо вернуть - вернем
		if ( ! empty( $return ) ) {
			return $data;
		}

		//напечатем
		echo $data;
	} else {
		return false;
	}
}
