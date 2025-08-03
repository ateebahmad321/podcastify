<?php
/**
 * Meta Box APi
 *
 * @package Podcastify
 */

 // Add array of fields.
namespace Podcastify\Classes;

if ( ! class_exists( 'Meta_Box_Api' ) ) :

	class Meta_Box_Api {


		public function __construct() {

		}

		/**
		 * Text field call back.
		 *
		 * @since 1.0.0
		 * @param array $args
		 * @return string
		 */
		public function callback_text( array $args ):string {
			$value = esc_attr( $args['value'] );
			$type  = $args['type'] ?? 'text';
			$class = $args['class'] ?? '';

			$desc = $this->get_field_description( $args );
			$html = sprintf( '<label class="wppfy-meta-label  wppfy-label" for="wpp-%1$s">%2$s</label><input name="%3$s" value="%4$s"class="%5$s wppfy-text" id="wpp-%6$s" type="%7$s" />%8$s', $args['name'], $args['label'], $args['name'], $args['value'], $class, $args['name'], $args['type'], $desc );

			return $html;
		}

		/**
		 * Number field callback.
		 *
		 * @since 1.0.0
		 * @param array $args
		 * @return void
		 */
		public function callback_number( array $args ) {
			return $this->callback_text( $args );
		}
		/**
		 * Get field description.
		 *
		 * @since 1.0.0
		 * @param array $args argument.
		 * @return sting
		 */
		public function get_field_description( array $args ):string {
			if ( ! empty( $args['desc'] ) ) {
				$desc = sprintf( '<p class="description">%s</p>', $args['desc'] );
			} else {
				$desc = '';
			}

			return $desc;
		}

		/**
		 * Select call back
		 *
		 * @param array $args
		 * @return string
		 */
		public function callback_select( array $args ):string {

			$value = esc_attr( $args['value'] );
			$class = $args['class'] ?? '';
			$html  = sprintf( '<label class="wppfy-meta-label  wppfy-label" for="wpp-%1$s">%2$s</label>', $args['name'], $args['label'] );
			$html .= sprintf( '<select class="%1$s wppfy-select" name="%2$s" id="wpp-%2$s">', $class, $args['name'] );
			foreach ( $args['options'] as $key => $label ) {
				$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
			}
			$html .= sprintf( '</select>' );
			$html .= $this->get_field_description( $args );

			return $html;
		}

		/**
		 * Checkbox callback.
		 *
		 * @since 1.0.0
		 * @param array $args
		 * @return string
		 */
		public function callback_checkbox( array $args ):string {

			$value = esc_attr( $args['value'] );
			$class = $args['class'] ?? '';

			$html  = sprintf( '<label for="wppfy-%1$s" class="wppfy-meta-label  wppfy-label">%2$s</label>', $args['name'], $args['label'] );
			$html .= sprintf( '<input type="hidden" name="%1$s" value="off" />', $args['name'] );
			$html .= sprintf( '<label class="wppfy-label checkbox-label" for="wpp-%2$s"><input type="checkbox" class="%1$s wppfy-checkbox" id="wpp-%2$s" name="%2$s" value="on" %3$s /><span class="check-slider"></span></label>', $class, $args['name'], checked( $value, 'on', false ) );
			$html .= $this->get_field_description( $args );
			return $html;
		}


		/**
		 * Checkbox callback.
		 *
		 * @since 1.0.0
		 * @param array $args
		 * @return string
		 */
		public function callback_radio( array $args ):string {
			$value = esc_attr( $args['value'] );
			$class = $args['class'] ?? '';
			$html  = '<div wppfy-radio-wrapper>';
			foreach ( $args['options'] as $key => $label ) {
				$html .= sprintf( '<label for="wppfy-%1$s-%2$s wppfy-meta-label  wppfy-label">', $args['name'], $key );
				$html .= sprintf( '<input type="radio" class="%5$s wppfy-radio" id="wpp-%1$s-%2$s" name="%1$s" value="%3$s" %4$s />', $args['name'], $key, $key, checked( $value, $key, false ), $class );
				$html .= sprintf( '%1$s</label><br>', $label );
			}
			$html .= '</div>' . $this->get_field_description( $args );

			return $html;
		}


		/**
		 * File callback.
		 *
		 * @since 1.0.0
		 * @param array $args
		 * @return string
		 */
		public function callback_file( array $args ):string {
			$value        = esc_attr( $args['value'] );
			$class        = $args['class'] ?? '';
			$button_label = $args['button-label'] ?? __( 'Upload file', 'podcastify' );
			// 'upload_frame_label'
			$html  = sprintf( '<label class="wppfy-meta-label  wppfy-label" for="wpp-%1$s">%2$s</label>', $args['name'], $args['label'] );
			$html .= sprintf( '<input name="%1$s" value="%2$s" class="%3$s wppfy-text" id="wpp-%1$s" type="text" />', $args['name'], $args['value'], $class );

			$html .= sprintf( '<button id="%1$s" class="button wppfy-button upload-file-button %2$s" type="button">%3$s</button>', 'upload-' . $args['name'], $class, $button_label );
			$html .= $this->get_field_description( $args );

			return $html;
		}

		/**
		 * Date callback.
		 *
		 * @since 1.0.0
		 * @param array $args
		 * @return string
		 */
		public function callback_date( array $args ):string {

			wp_enqueue_script( 'jquery-ui-datepicker' );
			$args['class'] = 'wpp-date';
			$args['type']  = 'text';
			return $this->callback_text( $args );
		}

		/**
		 * Sanitize number field.
		 *
		 * @param int $value parameter.
		 * @return int integer to return.
		 */
		public function sanitize_number_field( $value ): int {
			return (int) $value;
		}

	}
endif; // class exist.

