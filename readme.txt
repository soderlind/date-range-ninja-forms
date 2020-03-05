=== Date Range for Ninja Forms ===
Contributors: PerS
Donate link: https://soderlind.no/donate/
Tags: date
Requires at least: 4.9.8
Tested up to: 5.4
Stable tag: trunk
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add a Date Range field to Ninja Forms.

== Description ==

Add a Date Range field to your Ninja Forms.

== Filters ==

Add the filters to your child theme functions.php

= `date_range_lang` =

Override the value returned from get_locale().

E.g. if using Polylang, add:

`
add_filter( 'date_range_lang', function( $locale ) {
	if ( function_exists( 'pll_current_language' ) ) {
		$locale = pll_current_language( 'locale' );
	}
	return $locale;
} );
`

= `date_range_dropdowns` =

Enable dropdowns for months, years.

If `maxYear` is `null` then `maxYear` will be equal to `(new Date()).getFullYear()`.

`
add_filter( 'date_range_dropdowns', function( $dropdowns ) {

	$dropdowns = [
		'minYear' => 2020,
		'maxYear' => 2030,
		'months'  => false,
		'years'   => true, // show dropdown for years.
	];

	return $dropdowns;
} );
`

= `date_range_buttontext` =

Text for buttons.

`
add_filter( 'date_range_buttontext', function( $buttontext ) {

	$buttontext = [
		'apply'         => 'Apply',
		'cancel'        => 'Cancel',
		'previousMonth' => '<svg .../></svg>',
		'nextMonth'     => '<svg .../></svg>',
	];

	return $buttontext;
} );
`


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/data-range-ninja-forms` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use Ninja Forms to add the Date Range field.

== Screenshots ==

1. Settings.
2. Using Ninja Forms to add the Date Range field.
3. Date Range at the front-end.

== Changelog ==

= 0.0.7 =

* Refactor JavaScript to ES6.

= 0.0.6 =

* Fix breaking bug

= 0.0.5 =

* Add more settings.
* Add [filters](#filters): `date_range_lang`, `date_range_dropdowns` and `date_range_buttontext`.
* Add `languages/date-range-ninja-forms.pot`

= 0.0.4 =

* Replace Lightpick, no longer maintained, with [Litepicker](https://github.com/wakirin/Litepicker)

= 0.0.3 =

* In Ninja Forms builder, select WP Settings date.

= 0.0.2 =

* Set date format in Ninja Form builder

= 0.0.1 =

* Initial release.


