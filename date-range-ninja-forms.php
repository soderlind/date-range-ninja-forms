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
 * Version:     1.1.0
 * Author:      Per Soderlind
 * Author URI:  https://soderlind.no
 * Text Domain: date-range-ninja-forms
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace Soderlind\NinjaForms\DateRange;

if ( ! defined( 'ABSPATH' ) ) {
	\wp_die();
}
const DATERANGE_FILE    = __FILE__;
const DATERANGE_VERSION = '1.1.0';

require_once \plugin_dir_path( DATERANGE_FILE ) . 'vendor/autoload.php';


/**
 * Load DateRange.
 *
 * Use instead of global
 *
 * @return object
 */
function DateRange() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return DateRange::instance();
}
DateRange();
