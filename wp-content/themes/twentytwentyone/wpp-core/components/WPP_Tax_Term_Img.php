<?php
/**
 * @package teplo-centr.tw1.rus
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;


class WPP_Tax_Term_Img {

	public static $field = 'wpp_thumbnail_id';

	/*
	 * Initialize the class and start calling our hooks and filters
	 * @since 1.0.0
	*/
	public static function init() {
		add_action( 'init', [ __CLASS__, 'actions' ], 10, 1 );
	}

	public static function load_media() {
		wp_enqueue_media();
	}

	public static function actions() {
		$tax_names = apply_filters( 'wpp_tax_imgs_targets', [] );
		wpp_log_data( $tax_names, 'im' );
		if ( ! empty( $tax_names ) ) :
			foreach ( $tax_names as $tax_name ) :


				$name = is_array( $tax_name ) ? $tax_name[0] : $tax_name;

				add_action( "{$name}_add_form_fields", [ __CLASS__, 'add_category_image' ], 10, 3 );
				add_action( "created_{$name}", [ __CLASS__, 'save_category_image' ], 10, 3 );
				add_action( "{$name}_edit_form_fields", [ __CLASS__, 'update_category_image' ], 10, 3 );
				add_action( "edited_{$name}", [ __CLASS__, 'save_category_image' ], 10, 3 );

				// Add columns.
				add_filter( "manage_edit-{$name}_columns", [ __CLASS__, 'wpp_cat_columns' ] );
				add_filter( "manage_{$name}_custom_column", [ __CLASS__, 'wpp_cat_column' ], 10, 3 );


				add_action( 'admin_enqueue_scripts', [ __CLASS__, 'load_media' ] );
				add_action( 'admin_footer', [ __CLASS__, 'add_script' ] );

			endforeach;
		endif;
	}

	/**
	 * Cхранение поля
	 *
	 * @param mixed $term_id Term ID being saved.
	 * @param mixed $tt_id Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 */
	public static function save_category_image( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( isset( $_POST[ self::$field ] ) && '' !== $_POST[ self::$field ] ) {
			update_term_meta( $term_id, self::$field, absint( $_POST[ self::$field ] ) );
		} else {
			delete_term_meta( $term_id, self::$field );
		}
	}

	/**
	 * Category thumbnail fields.
	 */
	public static function add_category_image() {
		?>
        <div class="form-field term-group">
            <label for="<?php
			echo self::$field; ?>"><?php
				_e( 'Image', 'wpp-fr' ); ?>
            </label>
            <input type="hidden" id="<?php
			echo self::$field; ?>" name="<?php
			echo self::$field; ?>" class="<?php
			echo self::$field; ?>" value="">
            <div id="category-image-wrapper"></div>
            <p>
                <input type="button" class="button button-secondary wpp_tax_media_button" id="wpp_tax_media_button"
                       name="wpp_tax_media_button" value="<?php
				_e( 'Add Image', 'wpp-fr' ); ?>"/>
                <input type="button" class="button button-secondary wpp_tax_media_remove" id="wpp_tax_media_remove"
                       name="wpp_tax_media_remove" value="<?php
				_e( 'Remove Image', 'wpp-fr' ); ?>"/>
            </p>
        </div>
		<?php
	}


	/**
	 * Edit category thumbnail field.
	 *
	 * @param mixed $term Term (category) being edited.
	 */
	public static function update_category_image( $term, $taxonomy ) { ?>
        <tr class="form-field term-group-wrap">
            <th scope="row">
                <label for="<?php
				echo self::$field; ?>"><?php
					_e( 'Image', 'wpp-fr' ); ?></label>
            </th>
            <td>
				<?php
				$image_id = get_term_meta( $term->term_id, self::$field, true ); ?>
                <input type="hidden" id="<?php
				echo self::$field; ?>" name="<?php
				echo self::$field; ?>" value="<?php
				echo $image_id; ?>">
                <div id="category-image-wrapper">
					<?php
					if ( $image_id ) { ?>
						<?php
						echo wp_get_attachment_image( $image_id, 'thumbnail' ); ?>
						<?php
					} ?>
                </div>
                <p>
                    <input type="button" class="button button-secondary wpp_tax_media_button"
                           id="wpp_tax_media_button" name="wpp_tax_media_button"
                           value="<?php
					       _e( 'Add Image', 'wpp-fr' ); ?>"/>
                    <input type="button" class="button button-secondary wpp_tax_media_remove"
                           id="wpp_tax_media_remove" name="wpp_tax_media_remove"
                           value="<?php
					       _e( 'Remove Image', 'wpp-fr' ); ?>"/>
                </p>
            </td>
        </tr>
		<?php
	}


	/**
	 * Thumbnail column added to category admin.
	 *
	 * @param mixed $columns Columns array.
	 *
	 * @return array
	 */
	public static function wpp_cat_columns( $columns ) {
		$new_columns = [];

		if ( isset( $columns['cb'] ) ) {
			$new_columns['cb'] = $columns['cb'];
			unset( $columns['cb'] );
		}

		$new_columns['wpp_thumb'] = __( 'Image', 'wpp-fr' );

		$columns           = array_merge( $new_columns, $columns );
		$columns['handle'] = '';

		return $columns;
	}

	/**
	 * Thumbnail column value added to category admin.
	 *
	 * @param string $columns Column HTML output.
	 * @param string $column Column name.
	 * @param int $id Product ID.
	 *
	 * @return string
	 */
	public static function wpp_cat_column( $columns, $column, $id ) {
		if ( 'wpp_thumb' === $column ) {
			$thumbnail_id = get_term_meta( $id, self::$field, true );

			if ( $thumbnail_id ) {
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			} else {
				$image = wpp_image_placeholder('','src');
			}

			$image   = str_replace( ' ', '%20', $image );
			$columns .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail',
					'wpp-fr' ) . '" class="wp-post-image" height="48" width="48" />';
		}
		if ( 'handle' === $column ) {
			$columns .= '<input type="hidden" name="term_id" value="' . esc_attr( $id ) . '" />';
		}

		return $columns;
	}

	/*
	 * Add script
	 * @since 1.0.0
	 */
	public static function add_script() { ?>

        <script>
            jQuery(document).ready(function ($) {

                function wpp_media_upload(button_class) {

                    var _custom_media = true,
                        _orig_send_attachment = wp.media.editor.send.attachment;

                    $('body').on('click', button_class, function (e) {

                        var $button_id = '#' + $(this).attr('id'),
                            $button = $($button_id);
                        _custom_media = true;

                        wp.media.editor.send.attachment = function (props, attachment) {

                            if (_custom_media) {
                                $('#<?php echo self::$field; ?>').val(attachment.id);
                                $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                                $('#category-image-wrapper .custom_media_image').attr('src', attachment.url).css('display', 'block');
                            } else {
                                return _orig_send_attachment.apply($button_id, [props, attachment]);
                            }

                        }

                        wp.media.editor.open($button);
                        return false;

                    });
                }

                wpp_media_upload('.wpp_tax_media_button.button');

                $('body').on('click', '.wpp_tax_media_remove', function () {

                    $('#<?php echo self::$field; ?>').val('');
                    $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');

                });

                // Thanks: http://stackoverflow.com/questions/15281995/wordpress-create-category-ajax-response
                $(document).ajaxComplete(function (event, xhr, settings) {

                    var queryStringArr = settings.data.split('&');
                    if ($.inArray('action=add-tag', queryStringArr) !== -1) {
                        var xml = xhr.responseXML;
                        $_response = $(xml).find('term_id').text();
                        if ($_response != "") {
                            // Clear the thumb image
                            $('#category-image-wrapper').html('');
                        }
                    }

                });
            });
        </script>
		<?php
	}
}

WPP_Tax_Term_Img::init();


function wpp_term_img( $id ) {
	$thumbnail_id = get_term_meta( $id, WPP_Tax_Term_Img::$field, true );

	if ( $thumbnail_id ) {
		$image = wp_get_attachment_thumb_url( $thumbnail_id );
	} else {
		$image = wpp_image_placeholder();
	}

	$image = str_replace( ' ', '%20', $image );

	echo '<img src="' . esc_url( $image ) . '" alt="" />';
}