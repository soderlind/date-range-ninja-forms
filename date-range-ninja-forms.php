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
 * Description: Add a Date Range field to your Ninja Forms.
 * Version:     0.2.1
 * Author:      Per Soderlind
 * Author URI:  https://soderlind.no
 * Text Domain: date-range-ninja-forms
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace Soderlind\NinjaForms\DateRange;

define( 'DR_VERSION_NUMBER', '0.2.2' );
/**
 * Register Date Range field
 */
add_filter(
	'ninja_forms_register_fields',
	function( $fields ) {
		$fields['daterange'] = new class() extends \NF_Abstracts_Input { // Anonymous class, PHP 7.x requiered.

			/**
			 * Field name.
			 *
			 * @var string
			 */
			protected $_name = 'daterange';

			/**
			 * Field type.
			 *
			 * @var string
			 */
			protected $_type = 'daterange';

			/**
			 * Field name.
			 *
			 * @var string
			 */
			protected $_nicename = 'Date Range';

			/**
			 * Field section.
			 *
			 * @var string
			 */
			protected $_section = 'common';

			/**
			 * Dashicon for field.
			 *
			 * @var string
			 */
			protected $_icon = 'calendar';

			/**
			 * Template name. Maps to fields-daterange.html, path set in register_template_path()
			 *
			 * @var string
			 */
			protected $_templates = 'daterange';

			/**
			 * Test value.
			 *
			 * @var string
			 */
			protected $_test_value = '';

			/**
			 * Setting IDs.
			 *
			 * @var array
			 */
			protected $_settings = [
				'date_format',
				'start_of_week',
				'tooltip_fieldset',
				'tooltip',
				'tooltip_singular',
				'tooltip_singular',
				'tooltip_plural',
				'min_max_days',
				'max_min_date_fieldset',
				'min_max_date',
				'min_date',
				'max_date',
				'min_max_days_fieldset',
				'min_max_days',
				'min_days',
				'max_days',
				'show_week_numbers',
				'disable_weekends',
				'select_backward',
				'select_forward',
				'auto_apply',
			]; // maps to the settings array, see the ninja_forms_field_settings filter below.

			/**
			 * Exclude fields.
			 *
			 * @var array
			 */
			protected $_settings_exclude = [ 'default', 'input_limit_set', 'disable_input' ]; // remove noice.

			/**
			 * Constructor.
			 */
			public function __construct() {
				parent::__construct();

				$this->_nicename = __( 'Date Range', 'date-range-ninja-forms' );
				$this->init();
			}


			/**
			 * Process the field.
			 *
			 * @param array $field Fields.
			 * @param array $data  Data.
			 * @return array
			 */
			public function process( $field, $data ) {
				return $data;
			}

			/**
			 * Init.
			 *
			 * @return void
			 */
			public function init() {
				add_filter( 'ninja_forms_field_template_file_paths', [ $this, 'register_template_path' ] );
				add_action( 'ninja_forms_enqueue_scripts', [ $this, 'scripts' ] );
				add_action( 'init', [ $this, 'load_textdomain' ] );
			}

			/**
			 * Register the template path for the plugin
			 *
			 * @param array $file_paths Template paths.
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
			 * The js/date-range.js file connects the Litepicker script with ninja forms.
			 *
			 * @return void
			 */
			public function scripts() {
				wp_enqueue_script( 'dayjs', '//cdnjs.cloudflare.com/ajax/libs/dayjs/1.8.35/dayjs.min.js', [], DR_VERSION_NUMBER, true );
				wp_enqueue_script( 'lightpicker', '//cdn.jsdelivr.net/npm/litepicker/dist/js/main.js', [ 'dayjs' ], DR_VERSION_NUMBER, true );
				wp_enqueue_script( 'date-range', plugin_dir_url( __FILE__ ) . 'js/date-range.js', [ 'lightpicker' ], DR_VERSION_NUMBER, true );
				wp_localize_script(
					'date-range',
					'drDateRange',
					[
						'dateFormat' => get_option( 'date_format' ),
						'lang'       => apply_filters( 'date_range_lang', get_locale() ),
						'dropdowns'  => apply_filters( 'date_range_dropdowns', wp_json_encode( [] ) ),
						'buttontext' => apply_filters( 'date_range_buttontext', wp_json_encode( [] ) ),
					]
				);
			}

			/**
			 * Load translation.
			 *
			 * @return void
			 */
			public function load_textdomain() {
				load_plugin_textdomain( 'date-range-ninja-forms', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
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

		$settings['date_format'] = [
			'name'    => 'date_format',
			'type'    => 'select',
			'label'   => __( 'Date Format', 'date-range-ninja-forms' ),
			'width'   => 'one-half',
			'group'   => 'primary',
			'options' => [
				[
					/* translators: Get the date from WordPress settings. */
					'label' => sprintf( __( 'WP Settings (%s)', 'date-range-ninja-forms' ), get_option( 'date_format' ) ),
					'value' => 'default',
				],
				[
					'label' => 'm/d/Y',
					'value' => 'MM/DD/YYYY',
				],
				[
					'label' => 'm-d-Y',
					'value' => 'MM-DD-YYYY',
				],
				[
					'label' => 'm.d.Y',
					'value' => 'MM.DD.YYYY',
				],
				[
					'label' => 'd/m/Y',
					'value' => 'DD/MM/YYYY',
				],
				[
					'label' => 'd-m-Y',
					'value' => 'DD-MM-YYYY',
				],
				[
					'label' => 'd.m.Y',
					'value' => 'DD.MM.YYYY',
				],
				[
					'label' => 'Y-m-d',
					'value' => 'YYYY-MM-DD',
				],
				[
					'label' => 'Y/m/d',
					'value' => 'YYYY/MM/DD',
				],
				[
					'label' => 'Y.m.d',
					'value' => 'YYYY.MM.DD',
				],
				[
					'label' => 'l, F d Y',
					'value' => 'dddd, MMMM D YYYY',
				],
			],
			'value'   => 'default',  // the initial selected value.
		];

		$settings['start_of_week'] = [
			'name'    => 'start_of_week',
			'type'    => 'select',
			'label'   => __( 'Start of Week', 'date-range-ninja-forms' ),
			'width'   => 'one-half',
			'group'   => 'primary',
			'options' => [
				[
					'label' => __( 'Sunday', 'date-range-ninja-forms' ),
					'value' => '0',
				],
				[
					'label' => __( 'Monday', 'date-range-ninja-forms' ),
					'value' => '1',
				],
				[
					'label' => __( 'Tuesday', 'date-range-ninja-forms' ),
					'value' => '2',
				],
				[
					'label' => __( 'Wednesday', 'date-range-ninja-forms' ),
					'value' => '3',
				],
				[
					'label' => __( 'Thursday', 'date-range-ninja-forms' ),
					'value' => '4',
				],
				[
					'label' => __( 'Friday', 'date-range-ninja-forms' ),
					'value' => '5',
				],
				[
					'label' => __( 'Saturday', 'date-range-ninja-forms' ),
					'value' => '6',
				],
			],
			'value'   => get_option( 'start_of_week' ),  // the initial selected value.
		];

		/*
		|--------------------------------------------------------------------------
		| Advanced Settings
		|--------------------------------------------------------------------------
		|
		| The least commonly used settings for a field.
		*/

		$tooltip['tooltip'] = [
			'name'  => 'tooltip',
			'type'  => 'toggle',
			'label' => esc_html__( 'Show Tool Tip', 'date-range-ninja-forms' ),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => false,
		];

		$tooltip['tooltip_singular'] = [
			'name'  => 'tooltip_singular',
			'type'  => 'textbox',
			'label' => esc_html__( 'Singular', 'date-range-ninja-forms' ),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => 'day',
			'deps'  => [
				'tooltip' => 1,
			],
		];

		$tooltip['tooltip_plural'] = [
			'name'  => 'tooltip_plural',
			'type'  => 'textbox',
			'label' => esc_html__( 'Plural', 'date-range-ninja-forms' ),
			// 'placeholder' => 'days',
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => 'days',
			'deps'  => [
				'tooltip' => 1,
			],
		];

		$settings['tooltip_fieldset'] = [
			'name'     => 'tooltip_fieldset',
			'type'     => 'fieldset',
			'label'    => esc_html__( 'Tooltip', 'date-range-ninja-forms' ),
			'width'    => 'full',
			'group'    => 'advanced',
			'settings' => $tooltip,
		];

		$start_end['max_min_date'] = [
			'name'  => 'max_min_date',
			'type'  => 'toggle',
			'label' => esc_html__( 'Limit Range', 'date-range-ninja-forms' ),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => 0,
		];

		$start_end['min_date'] = [
			'name'        => 'min_date',
			'type'        => 'textbox',
			'label'       => esc_html__( 'Start Date', 'date-range-ninja-forms' ),
			'help'        => esc_html__( 'The minimum/earliest date that can be selected.', 'date-range-ninja-forms' ),
			'placeholder' => 'YYYY-MM-DD',
			'width'       => 'one-third',
			'group'       => 'advanced',
			'value'       => '',
			'deps'        => [
				'max_min_date' => 1,
			],
		];
		$start_end['max_date'] = [
			'name'        => 'max_date',
			'type'        => 'textbox',
			'label'       => esc_html__( 'End Date', 'date-range-ninja-forms' ),
			'help'        => esc_html__( 'The maximum/latest date that can be selected. Leave blank if indefinite.', 'date-range-ninja-forms' ),
			'placeholder' => 'YYYY-MM-DD',
			'width'       => 'one-third',
			'group'       => 'advanced',
			'value'       => '',
			'deps'        => [
				'max_min_date' => 1,
			],
		];

		$min_max['min_max_days'] = [
			'name'  => 'min_max_days',
			'type'  => 'toggle',
			'label' => esc_html__( 'Set Min / Max Days', 'date-range-ninja-forms' ),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => 0,
		];

		$min_max['min_days'] = [
			'name'  => 'min_days',
			'type'  => 'number',
			'label' => esc_html__( 'Minimum days', 'date-range-ninja-forms' ),
			'help'  => esc_html__( 'The minimum days of the selected range.', 'date-range-ninja-forms' ),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => '0',
			'deps'  => [
				'min_max_days' => 1,
			],
		];
		$min_max['max_days'] = [
			'name'  => 'max_days',
			'type'  => 'number',
			'label' => esc_html__( 'Maximum Days', 'date-range-ninja-forms' ),
			'help'  => esc_html__( 'The maximum days of the selected range.', 'date-range-ninja-forms' ),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => '0',
			'deps'  => [
				'min_max_days' => 1,
			],
		];

		$settings['max_min_date_fieldset'] = [
			'name'     => 'max_min_date_fieldset',
			'type'     => 'fieldset',
			'label'    => esc_html__( 'Control date range', 'date-range-ninja-forms' ),
			'width'    => 'full',
			'group'    => 'advanced',
			'settings' => $start_end,
		];

		$settings['min_max_days_fieldset'] = [
			'name'     => 'min_max_days_fieldset',
			'type'     => 'fieldset',
			'label'    => esc_html__( 'Number of days', 'date-range-ninja-forms' ),
			'width'    => 'full',
			'group'    => 'advanced',
			'settings' => $min_max,
		];

		$settings['show_week_numbers'] = [
			'name'  => 'show_week_numbers',
			'type'  => 'toggle',
			'label' => esc_html__( 'Show Week Numbers', 'date-range-ninja-forms' ),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => 0,
		];

		$settings['disable_weekends'] = [
			'name'  => 'disable_weekends',
			'type'  => 'toggle',
			'label' => esc_html__( 'Disable Weekends', 'date-range-ninja-forms' ),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => 0,
		];

		$settings['select_backward'] = [
			'name'  => 'select_backward',
			'type'  => 'toggle',
			'label' => esc_html__( 'Select Backward', 'date-range-ninja-forms' ),
			'help'  => esc_html__( 'Select second date before the first selected date.', 'date-range-ninja-forms' ),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => 0,
		];

		$settings['select_forward'] = [
			'name'  => 'select_forward',
			'type'  => 'toggle',
			'label' => esc_html__( 'Select Forward', 'date-range-ninja-forms' ),
			'help'  => esc_html__( 'Select second date after the first selected date.', 'date-range-ninja-forms' ),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => 0,
		];

		$settings['auto_apply'] = [
			'name'  => 'auto_apply',
			'type'  => 'toggle',
			'label' => esc_html__( 'Auto Apply', 'date-range-ninja-forms' ),
			'help'  => esc_html__( 'When enabled, hide the apply and cancel buttons, and automatically apply a new date range as soon as two dates are clicked.', 'date-range-ninja-forms' ),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => 1,
		];

		return $settings;
	}
);
