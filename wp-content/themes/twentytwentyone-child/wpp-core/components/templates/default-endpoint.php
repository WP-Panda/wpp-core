<?php

defined( 'ABSPATH' ) || exit;

get_header();

echo 'Шаблон по умолчанию для конечной точки';

$array = [
	'input' => [
		'type'      => 'text',
		'order'     => 25,
		'title'     => 'Заголовок 25',
		'desc'         => 'Подпись подпись подпись подпись',
		//описание поля
		'desc_html'    => '<br><i%1$s>(%2$s)</i>',
		//разметка описания
		// false - выводится не будет, если передать свою разметку будет выводиться она
		// 1 - class
		// 2 - описание из поля deck если его нет описание выводтся не будет
		'desc_classes' => ['oooooooo oooo'],
		'help_text' => 'Хелп Текст Хелп Текст Хелп Текст Хелп Текст',
		'classes'     => ['my_custom_clas'],
		'atts'=>['data-i'=>'88','data-tedt'=>82525],
		//'wrap' => '<div%1$s>ijjjkkm%2$s</div>',
		//'wrap' => false,
		'wrap_classes' => ['class1','wpp_text_wrap'],
		//'label' => true,
		'required'=> true,
		'value'=>0,
		'placeholder'=>'лайсхолдер'
	],
	'comput' => [
	'type'      => 'text',
	'order'     => 5,
	'title'     => 'Заголовок 5',
	'desc'         => 'Подпись подпись подпись подпись',
	//описание поля
	'desc_html'    => '<br><i%1$s>(%2$s)</i>',
	//разметка описания
	// false - выводится не будет, если передать свою разметку будет выводиться она
	// 1 - class
	// 2 - описание из поля deck если его нет описание выводтся не будет
	'desc_classes' => ['oooooooo oooo'],
	'help_text' => 'Хелп Текст Хелп Текст Хелп Текст Хелп Текст',
	'classes'     => ['my_custom_clas'],
	'atts'=>['data-i'=>'88','data-tedt'=>82525],
	//'wrap' => '<div%1$s>ijjjkkm%2$s</div>',
	//'wrap' => false,
	'wrap_classes' => ['class1','wpp_text_wrap'],
	//'label' => true,
	'required'=> true,
	'value'=>0,
	'placeholder'=>'лайсхолдер'
],
	'compute' => [
		'type'      => 'text',
		'title'     => 'Заголовок 10',
		'desc'         => 'Подпись подпись подпись подпись',
		'order'     => 10,
		//описание поля
		'desc_html'    => '<br><i%1$s>(%2$s)</i>',
		//разметка описания
		// false - выводится не будет, если передать свою разметку будет выводиться она
		// 1 - class
		// 2 - описание из поля deck если его нет описание выводтся не будет
		'desc_classes' => ['oooooooo oooo'],
		'help_text' => 'Хелп Текст Хелп Текст Хелп Текст Хелп Текст',
		'classes'     => ['my_custom_clas'],
		'atts'=>['data-i'=>'88','data-tedt'=>82525],
		//'wrap' => '<div%1$s>ijjjkkm%2$s</div>',
		//'wrap' => false,
		'wrap_classes' => ['class1','wpp_text_wrap'],
		//'label' => true,
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