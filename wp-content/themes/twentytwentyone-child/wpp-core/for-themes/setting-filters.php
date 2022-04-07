<?php
defined( 'ABSPATH' ) || exit;



//заменить отсутстыующие изображения запонителем имя опции - change_empty_image
add_filter( 'wp_get_attachment_image_src', 'wpp_image_placeholder', 10 );