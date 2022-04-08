<?php

class Wpp_Forms {

	private $setting = [];
	private $form_id = '';
	private $template_part = 'wpp-core/components/wpp-forms/fields/';

	private $params = [
		'type'         => 'text',
		//тип элемента
		'order'     => 0,
		'title'        => '',
		//заголовок поля
		'desc'         => '',
		//описание поля
		'desc_html'    => '<i%1$s>%2$s</i>',
		//разметка описания
		// false - выводится не будет, если передать свою разметку будет выводиться она
		// 1 - class
		// 2 - описание из поля deck если его нет описание выводтся не будет
		'desc_classes' => [],
		//может быть строкой, но лучше массив))) классы для описания
		//'help_text'    => false,
		'classes'      => [],
		//дополнительные классы поля
		'atts'         => [],
		//атрибуты массив
		'wrap'         => '<div%1$s>%2$s</div>',
		//разметка обертки
		// false - выводится не будет, если передать свою разметку будет выводиться она
		// шаблон разметки по умолчанию
		// 1 - class
		// 2 - html код поля
		'wrap_classes' => [],
		//может быть строкой, но лучше массив))) классы для обертки
		'label'        => '<label for="%1$s"%2$s>%3$s%4$s</label>',
		// разметка лэйбла
		// false - выводится не будет, если передать свою разметку будет выводиться она
		// шаблон разметки по умолчанию
		// 1 - id поля
		// 2 - class
		// 3 - параметр title если его нет label выводится не будет
		// 4 - html код поля
		'label_class'  => [],
		//может быть строкой, но лучше массив))) классы для лэйбла
		'required'     => false,
		//обязательно к заполнению или нет
		'value'        => '',
		//значение
		'placeholder'  => ''
		//подсказка в поле
	];

	public function __construct( $setting, $form_id = '' ) {
		$this->setting = $setting;
		$this->form_id = $form_id;
	}


	/**
	 * Отрисовка формы
	 * @return void
	 */
	public function render() {
		$setting = $this->setting;

		$setting = wp_list_sort( $setting,  'order', 'ASC', true );
		$out = '';
		foreach ( $setting as $field_id => $valls ) {
			$data = wp_parse_args( $setting[ $field_id ], $this->params );

			//принудительно убрать лишнее для скрытых полей
			if ( 'hidden' === $data['type'] ) {
				$data['wrap']  = false;
				$data['label'] = false;
				$data['desc'] = false;
			}



			switch ( $data['type'] ):
				case 'text':
				case 'hidden':
				default:
					$template = wpp_get_template_part( "{$this->template_part}text-input", [], true );
					break;
			endswitch;

			$classes = $this->classes_generate( $data['type'], $data, $field_id );
			$html    = sprintf( $template,
				$data['type'],
				$field_id,
				$classes,
				$this->value( $data ),
				$this->atts( $data ),
				$this->placeholder( $data ),
				$this->required( $data ) );

			$html .= $this->field_desc($data, $field_id);

			$html = $this->field_label( $data, $field_id, $html );

			$out .= $this->field_wrap( $data, $field_id, $html );
		}

		echo $out;
	}


	/**
	 * Атрибут - required
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	private function required( $data ) {
		return ! empty( $data['required'] ) && is_bool( $data['required'] ) ? ' required="required"' : '';
	}

	/**
	 * Атрибут - placeholder
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	private function placeholder( $data ) {
		return ! empty( $data['placeholder'] ) ? " placeholder=\"{$data['placeholder']}\"" : '';
	}

	/**
	 * Атрибут - value
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	private function value( $data ) {
		return isset( $data['value'] ) ? " value=\"{$data['value']}\"" : '';
	}

	/**
	 * Сборка дополнительных атрибутов
	 *
	 * @param $data
	 *
	 * @return string
	 */
	private function atts( $data ) {
		$atts = '';
		if ( ! empty( $data['atts'] ) && is_array( $data['atts'] ) ) {
			foreach ( $data['atts'] as $key => $val ) {
				$atts .= sprintf( ' %s="%s"', $key, $val );
			}
		}

		return $atts;
	}

	/**
	 * html код обертки поля
	 *
	 * @param array $data - параметры поля
	 * @param string $field_id - id поля
	 * @param string $html - код который надо завернуть в обертку
	 *
	 * @return string
	 */
	private function field_wrap( $data, $field_id, $html ): string {
		// выключить обертку
		if ( is_bool( $data['wrap'] ) && false === $data['wrap'] ) {
			return $html;
			//кастомная обертка для поля
		} else {
			$html_wrap = $data['wrap'];
		}

		$classes = $this->classes_generate( 'wrap', $data, $field_id );

		//замена обертки для всех полей
		$html_wrap = apply_filters( 'wpp_field_wrap_html', $html_wrap );

		//замена обертки для полей конкретного типа
		$html_wrap = apply_filters( "wpp_field_wrap_{$field_id}_html", $html_wrap );

		return sprintf( $html_wrap, $classes, $html );
	}

	/**
	 * html код обертки поля
	 *
	 * @param array $data - параметры поля
	 * @param string $field_id - id поля
	 * @param string $html - код который надо завернуть в обертку
	 *
	 * @return string
	 */
	private function field_label( $data, $field_id, $html ): string {
		// выключить label
		if ( ( is_bool( $data['label'] ) && false === $data['label'] ) || empty( $data['title'] ) ) {
			return $html;
			//кастомный label для поля
		} else {
			$label = $data['label'];
		}

		$classes = $this->classes_generate( 'label', $data, $field_id );

		//замена label для всех полей
		$label = apply_filters( 'wpp_field_label_html', $label );

		//замена label для полей конкретного типа
		$label = apply_filters( "wpp_field_label_{$field_id}_html", $label );

		return sprintf( $label, $field_id, $classes, $data['title'] ?? $field_id, $html );
	}

	/**
	 * html код описания
	 *
	 * @param array $data - параметры поля
	 * @param string $field_id - id поля
	 *
	 * @return string
	 */
	private function field_desc( $data, $field_id ): string {
		// выключить описание
		if ( ( is_bool( $data['desc_html'] ) && false === $data['desc_html'] ) || empty( $data['desc'] ) ) {
			return '';
			//кастомная разметка описания
		} else {
			$desc = $data['desc_html'];
		}

		$classes = $this->classes_generate( 'desc', $data, $field_id );

		//замена описания для всех полей
		$desc = apply_filters( 'wpp_field_desc_html', $desc );

		//замена описания для полей конкретного типа
		$desc = apply_filters( "wpp_field_desc_{$field_id}_html", $desc );

		return sprintf( $desc, $classes, $data['desc'] );
	}


	/**
	 * Генерация атрибута class
	 *
	 * @param string $emenent - тип элемента
	 * @param array $data - параметры поля
	 * @param string $field_id - id поля
	 *
	 * @return string - html строка классов
	 */
	private function classes_generate( $emenent, $data, $field_id ) {
		/*
		 * Обработка классов
		 */
		//массив классов по умолчaнию
		$classes = [
			"wpp_field_{$emenent}",
			"wpp_{$data['type']}_{$emenent}",
			"wpp_{$field_id}_{$emenent}",
		];

		//поправка для получения класса по умолчанию, что бы класс самого элемента в массиве выглядел красиво без нижнего подчеркивания
		$key_el = $emenent === $data['type'] ? $data['classes'] : $data["{$emenent}_classes"];


		//слить с переопределенными классами
		if ( ! empty( $key_el ) ) {
			if ( is_string( $key_el ) ) {
				$key_el = explode( ' ', $key_el );
			}

			if ( is_array( $key_el ) ) {
				$classes = array_merge( $classes, $key_el );
			}
		}

		//классы обертки для всех полей
		$classes = apply_filters( "wpp_field_{$emenent}_classes", $classes );

		//классы обертки конкретного поля
		$classes = apply_filters( "wpp_field_{$emenent}_{$data['type']}_classes", $classes );

		$classes = implode( ' ', array_unique( $classes ) );


		return ! empty( $classes ) ? sprintf( ' class="%s"', $classes ) : '';
	}

}