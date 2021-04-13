<?php
/**
 * DateRange for Ninja Forms: Field
 *
 * @package     Soderlind\NinjaForms\DateRange
 * @author      Per Søderlind
 * @copyright   2020 Per Søderlind
 * @license     GPL-2.0+
 */

declare( strict_types = 1 );

namespace Soderlind\NinjaForms\DateRange;

class Field extends \NF_Abstracts_Input {

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
		$file_paths[] = plugin_dir_path( DATERANGE_FILE ) . 'template/';
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
		wp_enqueue_script( 'dayjs', '//cdnjs.cloudflare.com/ajax/libs/dayjs/1.8.35/dayjs.min.js', [], DATERANGE_VERSION, true );
		wp_enqueue_script( 'lightpicker', '//cdn.jsdelivr.net/npm/litepicker/dist/js/main.js', [ 'dayjs' ], DATERANGE_VERSION, true );
		wp_enqueue_script( 'date-range', plugin_dir_url( DATERANGE_FILE ) . 'js/date-range.js', [ 'lightpicker' ], DATERANGE_VERSION, true );
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
		load_plugin_textdomain( 'date-range-ninja-forms', false, dirname( plugin_basename( DATERANGE_FILE ) ) . '/languages' );
	}
}