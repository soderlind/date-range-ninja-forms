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
 * Version:     0.0.5
 * Author:      Per Soderlind
 * Author URI:  https://soderlind.no
 * Text Domain: date-range-ninja-forms
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace Soderlind\NinjaForms\DateRange;

define('DR_VERSION_NUMBER', '0.0.5');
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

			protected $_settings = [
				'dr_date_format',
				'dr_start_of_week',
				'dr_tooltip_fieldset',
				'dr_max_min_date_fieldset',
				'dr_min_max_days_fieldset',
				'dr_show_week_numbers',
				'dr_disable_weekends',
				'dr_select_backward',
				'dr_select_forward',
			]; // maps to the settings array, see the ninja_forms_field_settings filter below.

			protected $_settings_exclude = [ 'default', 'input_limit_set', 'disable_input' ]; // remove noice

			public function __construct() {
				 parent::__construct();

				$this->_nicename = __('Date Range', 'date-range-ninja-forms');
				$this->init();
			}

			public function process( $field, $data ) {
				return $data;
			}

			public function init() {
				add_filter('ninja_forms_field_template_file_paths', [ $this, 'register_template_path' ]);
				add_action('ninja_forms_enqueue_scripts', [ $this, 'scripts' ]);
				add_action('init', [ $this, 'load_textdomain' ] );

			}

			/**
			 * Register the template path for the plugin
			 *
			 * @param array $file_paths
			 *
			 * @return array
			 */
			public function register_template_path( $file_paths ) {
				$file_paths[] = plugin_dir_path(__FILE__) . 'template/';
				return $file_paths;
			}

			/**
			 * Enqueue scripts
			 *
			 * js/date-range.js connects the Litepicker script with ninja forms
			 *
			 * @return void
			 */
			public function scripts() {
				wp_enqueue_script('moment', '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js', [ 'jquery' ], DR_VERSION_NUMBER, true);
				wp_enqueue_script('lightpicker', '//cdn.jsdelivr.net/npm/litepicker/dist/js/main.js', [ 'jquery' ], DR_VERSION_NUMBER, true);
				wp_enqueue_script('date-range', plugin_dir_url(__FILE__) . 'js/date-range.js', [ 'lightpicker' ], DR_VERSION_NUMBER, true);
				wp_localize_script(
					'date-range', 'drDateRange', [
						'dateFormat' => get_option('date_format'),
						'lang'       => apply_filters( 'date_range_lang', get_locale()),
						'dropdowns'  => apply_filters( 'date_range_dropdowns', wp_json_encode( [] ) ),
						'buttontext' => apply_filters( 'date_range_buttontext', wp_json_encode( [] ) ),
					]
				);
			}

			public function load_textdomain() {
				load_plugin_textdomain( 'date-range-ninja-forms', false, dirname( plugin_basename(__FILE__) ) . '/languages' );
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
			'label'   => __('Date Format', 'date-range-ninja-forms'),
			'width'   => 'one-half',
			'group'   => 'primary',
			'options' => [
				[
					'label' => sprintf(__('WP Settings (%s)', 'date-range-ninja-forms'), get_option('date_format')),
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
			'value'   => 'default',  // the initial selected value
		];

		$settings['dr_start_of_week'] = [
			'name'    => 'dr_start_of_week',
			'type'    => 'select',
			'label'   => __('Start of Week', 'date-range-ninja-forms'),
			'width'   => 'one-half',
			'group'   => 'primary',
			'options' => [
				[
					'label' => __('Sunday', 'date-range-ninja-forms'),
					'value' => '0',
				],
				[
					'label' => __('Monday', 'date-range-ninja-forms'),
					'value' => '1',
				],
				[
					'label' => __('Tuesday', 'date-range-ninja-forms'),
					'value' => '2',
				],
				[
					'label' => __('Wednesday', 'date-range-ninja-forms'),
					'value' => '3',
				],
				[
					'label' => __('Thursday', 'date-range-ninja-forms'),
					'value' => '4',
				],
				[
					'label' => __('Friday', 'date-range-ninja-forms'),
					'value' => '5',
				],
				[
					'label' => __('Saturday', 'date-range-ninja-forms'),
					'value' => '6',
				],
			],
			'value'   => get_option('start_of_week'),  // the initial selected value
		];

		/*
		|--------------------------------------------------------------------------
		| Advanced Settings
		|--------------------------------------------------------------------------
		|
		| The least commonly used settings for a field.
		*/

		$tooltip['dr_tooltip'] = [
			'name'  => 'dr_tooltip',
			'type'  => 'toggle',
			'label' => esc_html__('Show Tool Tip', 'date-range-ninja-forms'),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => false,
		];

		$tooltip['dr_tooltip_singular'] = [
			'name'  => 'dr_tooltip_singular',
			'type'  => 'textbox',
			'label' => esc_html__('Singular', 'date-range-ninja-forms'),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => 'day',
			'deps'  => [
				'dr_tooltip' => 1,
			],
		];

		$tooltip['dr_tooltip_plural'] = [
			'name'  => 'dr_tooltip_plural',
			'type'  => 'textbox',
			'label' => esc_html__('Plural', 'date-range-ninja-forms'),
			// 'placeholder' => 'days',
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => 'days',
			'deps'  => [
				'dr_tooltip' => 1,
			],
		];

		$settings['dr_tooltip_fieldset'] = [
			'name'     => 'dr_tooltip_fieldset',
			'type'     => 'fieldset',
			'label'    => esc_html__('Tooltip', 'date-range-ninja-forms'),
			'width'    => 'full',
			'group'    => 'advanced',
			'settings' => $tooltip,
		];

		$start_end['dr_max_min_date'] = [
			'name'  => 'dr_max_min_date',
			'type'  => 'toggle',
			'label' => esc_html__('Limit Range', 'date-range-ninja-forms'),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => false,
		];

		$start_end['dr_min_date'] = [
			'name'  => 'dr_min_date',
			'type'  => 'textbox',
			'label' => esc_html__('Start Date', 'date-range-ninja-forms'),
			'help'  => esc_html__('The minimum/earliest date that can be selected.', 'date-range-ninja-forms'),
			'placeholder' => 'YYYY-MM-DD',
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => '',
			'deps'  => [
				'dr_max_min_date' => 1,
			],
		];
		$start_end['dr_max_date'] = [
			'name'  => 'dr_max_date',
			'type'  => 'textbox',
			'label' => esc_html__('End Date', 'date-range-ninja-forms'),
			'help'  => esc_html__('The maximum/latest date that can be selected.', 'date-range-ninja-forms'),
			'placeholder' => 'YYYY-MM-DD',
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => '',
			'deps'  => [
				'dr_max_min_date' => 1,
			],
		];

		$min_max['dr_min_max_days'] = [
			'name'  => 'dr_min_max_days',
			'type'  => 'toggle',
			'label' => esc_html__('Set Min / Max Days', 'date-range-ninja-forms'),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => false,
		];


		$min_max['dr_min_days'] = [
			'name'  => 'dr_min_days',
			'type'  => 'number',
			'label' => esc_html__('Minimum days', 'date-range-ninja-forms'),
			'help'  => esc_html__('The minimum days of the selected range.', 'date-range-ninja-forms'),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => '0',
			'deps'  => [
				'dr_min_max_days' => 1,
			],
		];
		$min_max['dr_max_days'] = [
			'name'  => 'dr_max_days',
			'type'  => 'number',
			'label' => esc_html__('Maximum Days', 'date-range-ninja-forms'),
			'help'  => esc_html__('The maximum days of the selected range.', 'date-range-ninja-forms'),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => '0',
			'deps'  => [
				'dr_min_max_days' => 1,
			],
		];

		$settings['dr_max_min_date_fieldset'] = [
			'name'     => 'dr_max_min_date_fieldset',
			'type'     => 'fieldset',
			'label'    => esc_html__('Control date range', 'date-range-ninja-forms'),
			'width'    => 'full',
			'group'    => 'advanced',
			'settings' => $start_end,
		];

		$settings['dr_min_max_days_fieldset'] = [
			'name'     => 'dr_min_max_days_fieldset',
			'type'     => 'fieldset',
			'label'    => esc_html__('Number of days', 'date-range-ninja-forms'),
			'width'    => 'full',
			'group'    => 'advanced',
			'settings' => $min_max,
		];

		$settings['dr_show_week_numbers'] = [
			'name'  => 'dr_show_week_numbers',
			'type'  => 'toggle',
			'label' => esc_html__('Show Week Numbers', 'date-range-ninja-forms'),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => false,
		];

		$settings['dr_disable_weekends'] = [
			'name'  => 'dr_disable_weekends',
			'type'  => 'toggle',
			'label' => esc_html__('Disable Weekends', 'date-range-ninja-forms'),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => false,
		];

		$settings['dr_select_backward'] = [
			'name'  => 'dr_select_backward',
			'type'  => 'toggle',
			'label' => esc_html__('Select Backward', 'date-range-ninja-forms'),
			'help'  => esc_html__('Select second date before the first selected date.', 'date-range-ninja-forms'),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => false,
		];
		$settings['dr_select_forward'] = [
			'name'  => 'dr_select_forward',
			'type'  => 'toggle',
			'label' => esc_html__('Select Forward', 'date-range-ninja-forms'),
			'help'  => esc_html__('Select second date after the first selected date.', 'date-range-ninja-forms'),
			'width' => 'one-third',
			'group' => 'advanced',
			'value' => false,
		];

		return $settings;
	}
);
