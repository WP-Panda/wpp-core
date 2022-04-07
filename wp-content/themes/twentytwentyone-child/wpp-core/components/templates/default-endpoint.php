<?php

defined( 'ABSPATH' ) || exit;

get_header();

echo 'Шаблон по умолчанию для конечной точки';

$array = [
	'input' => [
		'type'      => 'text',
		'order'     => 5,
		'title'     => 'Заголовок',
		'deck'      => 'Подпись подпись подпись подпись',
		'help_text' => 'Хелп Текст Хелп Текст Хелп Текст Хелп Текст',
		'class'     => ['my_custom_clas'],
		'atts'=>['data-i'=>'88','data-tedt'=>82525],
		//'wrap' => '<div%1$s>ijjjkkm%2$s</div>',
		//'wrap' => false,
		'wrap_classes' => ['class1','wpp_text_wrap'],
		'label' => 1,
		'required'=> true,
		'value'=>0,
		'placeholder'=>'лайсхолдер'
	]
];

$form = new Wpp_Forms( $array );

$form->render();

/*$array2 = ['85262'];

$form2 = new Wpp_Forms($array2);

$form2->render();*/


get_footer();