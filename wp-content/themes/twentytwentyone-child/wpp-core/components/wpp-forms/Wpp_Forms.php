<?php

class Wpp_Forms {

	private $setting = [];
	private $form_id = '';
	private $template_part = 'wpp-core/components/wpp-forms/fields/';

	private $params = [
		'type'         => 'text',
		//тип элемента
		#'order'     => 5,
		'title'        => false,
		'deck'         => false,
		'help_text'    => false,
		'class'        => [],
		//класс элемента
		'atts'         => [],
		//атрибуты массив
		'wrap'         => true,
		// wrap если передать false - выводится не будет, если передать true ли ничего не передавать то выведется разметка по умолчапнию, если передать свою разметку быдет выводится она
		'wrap_classes' => [],
		//может быть строкой, но лучше массив))) классы для обертки
		'label'        => true,
		'label_class' => [],
		'required'     => false,
		'value'        => '',
		'placeholder'  => ''
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


		foreach ( $setting as $field_id => $valls ) {
			$data = wp_parse_args( $setting[ $field_id ], $this->params );

			//принудительно убрать обертку у скрытого поля
			if ( 'hidden' === $data['type'] ) {
				$data['wrap'] = false;
			}

			$out = '';

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
				$this->placeholder($data),
				$this->required( $data ) );

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
		} elseif ( ! empty( $data['wrap'] ) && ! is_bool( $data['wrap'] ) ) {
			$html_wrap = $data['wrap'];
			//обертка по умолчанию
		} else {
			$html_wrap = '<div%1$s>%2$s</div>';
		}

		$classes = $this->classes_generate( 'wrap', $data, $field_id );

		//замена обертки для всех полей
		$html_wrap = apply_filters( 'wpp_field_wrap_html', $html_wrap );

		//замена обертки для полей конкретного типа
		$html_wrap = apply_filters( "wpp_field_wrap_{$field_id}_html", $html_wrap );

		return sprintf( $html_wrap, $classes, $html );
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

		//слить с переопределенными классами
		if ( ! empty( $data["{$emenent}_classes"] ) ) {
			if ( is_string( $data["{$emenent}_classes"] ) ) {
				$data["{$emenent}_classes"] = explode( ' ', $data["{$emenent}_classes"] );
			}

			if ( is_array( $data["{$emenent}_classes"] ) ) {
				$classes = array_merge( $classes, $data["{$emenent}_classes"] );
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