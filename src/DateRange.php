<?php
/**
 * DateRange for Ninja Forms.
 *
 * @package     Soderlind\NinjaForms\DateRange
 * @author      Per Søderlind
 * @copyright   2021 Per Søderlind
 * @license     GPL-2.0+
 */

declare( strict_types = 1 );

namespace Soderlind\NinjaForms\DateRange;

/**
 * DateRange.
 */
final class DateRange {

	/**
	 * Object instance.
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Load instances once.
	 *
	 * @return object
	 */
	public static function instance() : object {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof DateRange ) ) {
			self::$instance = new DateRange();
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	 * Add hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'ninja_forms_register_fields', [ $this, 'register_fields' ] );
		add_filter( 'ninja_forms_field_settings', [ $this, 'field_settings' ] );
		add_action( 'nf_admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
	}

	/**
	 * Register Date Range field
	 *
	 * @param array $fields All fields.
	 *
	 * @return array
	 */
	public function register_fields( $fields ) {
		$fields['daterange'] = new Field();

		return $fields;
	}

	/**
	 * Add field settings.
	 *
	 * @param array $settings All setings.
	 *
	 * @return array
	 */
	public function field_settings( $settings ) {

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

	/**
	 * Enqueue setting field script.
	 *
	 * @return void
	 */
	public function admin_scripts() {
		wp_enqueue_script( 'date-setting-field', plugin_dir_url( DATERANGE_FILE ) . 'js/date-setting-field.js', [], DATERANGE_VERSION, true );
	}
}
