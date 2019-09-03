<?php
/**
 * Date Range for Ninja Forms
 *
 * @package     Date Range for Ninja Forms
 * @author      Per Soderlind
 * @copyright   2018 Per Soderlind
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Date Range field for Ninja Forms
 * Plugin URI: https://github.com/soderlind/date-range-ninja-forms
 * GitHub Plugin URI: https://github.com/soderlind/date-range-ninja-forms
 * Description: description
 * Version:     0.0.3
 * Author:      Per Soderlind
 * Author URI:  https://soderlind.no
 * Text Domain: date-range-ninja-forms
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace Soderlind\NinjaForms\DateRange;

define( 'DR_VERSION_NUMBER', '0.0.3' );
/**
 * Register Date Range field
 */
add_filter(
	'ninja_forms_register_fields',
	function( $fields ) {
		$fields['daterange'] = new class extends \NF_Abstracts_Input { // anonymous class, PHP 7.x requiered
			protected $_name = 'daterange';
			protected $_type = 'daterange';

			protected $_nicename = 'Date Range';

			protected $_section = 'common';

			protected $_icon = 'calendar';

			protected $_templates = 'daterange'; // maps to fields-daterange.html, path set in register_template_path()

			protected $_test_value = '';

			protected $_settings = [ 'dr_date_format' ]; // maps to the settings array, see the ninja_forms_field_settings filter below.

			protected $_settings_exclude = [ 'default', 'input_limit_set', 'disable_input' ]; // remove noice

			public function __construct() {
				 parent::__construct();

				$this->_nicename = __( 'Date Range', 'date-range-ninja-forms' );
				$this->init();
			}

			public function process( $field, $data ) {
				return $data;
			}

			public function init() {
				add_filter( 'ninja_forms_field_template_file_paths', [ $this, 'register_template_path' ] );
				add_action( 'ninja_forms_enqueue_scripts', [ $this, 'scripts' ] );
				add_action( 'wp_enqueue_scripts', [ $this, 'style' ] );
			}

			/**
			 * Register the template path for the plugin
			 *
			 * @param array $file_paths
			 *
			 * @return array
			 */
			public function register_template_path( $file_paths ) {
				$file_paths[] = plugin_dir_path( __FILE__ ) . 'template/';
				return $file_paths;
			}

			/**
			 * Enqueue scripts
			 *
			 * js/date-range.js connects the lightpick.js script with ninja forms
			 *
			 * @return void
			 */
			public function scripts() {
				wp_enqueue_script( 'moment', '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment-with-locales.min.js', [ 'jquery' ], DR_VERSION_NUMBER, true );
				wp_enqueue_script( 'lightpick', plugin_dir_url( __FILE__ ) . 'js/lightpick.js', [ 'moment' ], DR_VERSION_NUMBER, true );
				wp_enqueue_script( 'date-range', plugin_dir_url( __FILE__ ) . 'js/date-range.js', [ 'lightpick' ], DR_VERSION_NUMBER, true );
				wp_localize_script( 'date-range', 'drDateRange', [ 'dateFormat' => get_option( 'date_format' ) ] );
			}

			/**
			 * Enqueue style
			 *
			 * @return void
			 */
			public function style() {
				wp_enqueue_style( 'lightpick', plugin_dir_url( __FILE__ ) . 'js/lightpick.css', [], DR_VERSION_NUMBER );
			}

		};

		return $fields;
	}
);


/**
 * Add field settings
 */
add_filter(
	'ninja_forms_field_settings',
	function( $settings ) {
		$settings['dr_date_format'] = [
			'name'    => 'dr_date_format',
			'type'    => 'select',
			'label'   => __( 'Date Format', 'date-range-ninja-forms' ),
			'width'   => 'full',
			'group'   => 'primary',
			'options' => [
				[
					'label' => sprintf( __( 'WP Settings (%s)', 'date-range-ninja-forms' ), get_option( 'date_format' ) ),
					'value' => 'default',
				],
				[
					'label' => __( 'm/d/Y', 'date-range-ninja-forms' ),
					'value' => 'MM/DD/YYYY',
				],
				[
					'label' => __( 'm-d-Y', 'date-range-ninja-forms' ),
					'value' => 'MM-DD-YYYY',
				],
				[
					'label' => __( 'm.d.Y', 'date-range-ninja-forms' ),
					'value' => 'MM.DD.YYYY',
				],
				[
					'label' => __( 'd/m/Y', 'date-range-ninja-forms' ),
					'value' => 'DD/MM/YYYY',
				],
				[
					'label' => __( 'd-m-Y', 'date-range-ninja-forms' ),
					'value' => 'DD-MM-YYYY',
				],
				[
					'label' => __( 'd.m.Y', 'date-range-ninja-forms' ),
					'value' => 'DD.MM.YYYY',
				],
				[
					'label' => __( 'Y-m-d', 'date-range-ninja-forms' ),
					'value' => 'YYYY-MM-DD',
				],
				[
					'label' => __( 'Y/m/d', 'date-range-ninja-forms' ),
					'value' => 'YYYY/MM/DD',
				],
				[
					'label' => __( 'Y.m.d', 'date-range-ninja-forms' ),
					'value' => 'YYYY.MM.DD',
				],
				[
					'label' => __( 'l, F d Y', 'date-range-ninja-forms' ),
					'value' => 'dddd, MMMM D YYYY',
				],
			],
			'value'   => 'default',  // the initial selected value
		];

		return $settings;
	}
);
