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
 * Plugin Name: Date Range for Ninja Forms
 * Plugin URI: https://github.com/soderlind/date-range-ninja-forms
 * GitHub Plugin URI: https://github.com/soderlind/date-range-ninja-forms
 * Description: description
 * Version:     0.0.1
 * Author:      Per Soderlind
 * Author URI:  https://soderlind.no
 * Text Domain: date-range-ninja-forms
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

add_filter(
	'ninja_forms_register_fields',
	function( $fields ) {
		$fields['daterange'] = new class extends \NF_Abstracts_Input {
			protected $_name = 'daterange';
			protected $_type = 'daterange';

			protected $_nicename = 'Date Range';

			protected $_section = 'common';

			protected $_icon = 'calendar';

			protected $_templates = 'daterange';

			protected $_test_value = '';

			protected $_settings = [ 'date_range_default', 'date_range_advanced' ];

			protected $_settings_exclude = [ 'default', 'input_limit_set', 'disable_input' ];

			public function __construct() {
				 parent::__construct();

				$this->_nicename = __( 'Date Range', 'ninja-forms' );
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

			public function scripts() {
				wp_enqueue_script( 'moment', '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment-with-locales.min.js', [ 'jquery' ], rand(), true );
				// wp_enqueue_script( 'daterangepicker', '//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', ['moment'], rand(), true );
				wp_enqueue_script( 'lightpick', plugin_dir_url( __FILE__ ) . 'js/lightpick.js', [ 'moment' ], rand(), true );
				wp_enqueue_script( 'date-range', plugin_dir_url( __FILE__ ) . 'js/date-range.js', [ 'lightpick' ], rand(), true );
			}

			public function style() {
				// wp_enqueue_style( 'lightpick', '//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css', [] );
				wp_enqueue_style( 'lightpick', plugin_dir_url( __FILE__ ) . 'js/lightpick.css', [ ] );

			}

		};

		return $fields;
	}
);

add_filter(
	'ninja_forms_field_settings',
	function( $settings ) {

		$settings['date_range_default'] = [
			'name'  => 'date_range_default',
			'type'  => 'toggle',
			'label' => __( 'Default To ..', 'ninja-forms' ),
			'width' => 'one-half',
			'group' => 'primary',
		];

		$settings['date_range_advanced'] = [
			'name'    => 'date_range_advanced',
			'type'    => 'select',
			'label'   => __( 'Format', 'ninja-forms' ),
			'width'   => 'full',
			'group'   => 'primary',
			'options' => [
				[
					'label' => __( 'aa', 'ninja-forms' ),
					'value' => 'aa',
				],
				[
					'label' => __( 'bb', 'ninja-forms' ),
					'value' => 'bb',
				],
			],
			'value'   => 'bb',
		];

		return $settings;
	}
);
