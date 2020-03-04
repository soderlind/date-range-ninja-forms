/**
 * Thanks to https://ninjaformsdev.slack.com/archives/C0EFXJF0D/p1524614990000175
 */

(function ($) {
	var nfRadio = Backbone.Radio;
	var radioChannel = nfRadio.channel('daterange'); // 'daterange',  the $_type value, defined in date-range-ninja-forms.php

	var selectedHTML = Marionette.Object.extend({

		/**
		 * initialize()
		 *
		 * When initialize the form, listen to the radio chanel to see if there's a 'daterange' chanel.
		 */
		initialize: function () {
			this.listenTo(radioChannel, 'render:view', this.renderView);
		},
		/**
		 * renderView()
		 *
		 * When rendering the form (i.e. the view), attach custom javascript code and events.
		 */
		renderView: function (view) {
			var drDateFormat = view.model.get('dr_date_format');
			var drShowWeekNumbers = view.model.get('dr_show_week_numbers');
			var drStartOfWeek = view.model.get('dr_start_of_week');
			var drDisableWeekends = view.model.get('dr_disable_weekends');
			var drSelectBackward = view.model.get('dr_select_backward');
			var drSelectForward = view.model.get('dr_select_forward');
			var drToolTip = view.model.get('dr_tooltip');
			var drTooltipSingular = view.model.get('dr_tooltip_singular');
			var drTooltipSingular = view.model.get('dr_tooltip_singular');
			var drTooltipPlural = view.model.get('dr_tooltip_plural');
			var drMinMaxDate = view.model.get('dr_start_end_date');
			var drMinMaxDateStart = view.model.get('dr_min_date');
			var drMinMaxDateEnd = view.model.get('dr_max_date');
			var drMinMaxDays = view.model.get('dr_min_max_days');
			var drMinMaxDaysMin = view.model.get('dr_min_days');
			var drMinMaxDaysMax = view.model.get('dr_max_days');



			// For "default" date format, convert PHP format to JS compatible format.
			if ('' == drDateFormat || 'default' == drDateFormat) {
				drDateFormat = this.convertDateFormat(drDateRange.dateFormat); // 'drDateRange' from wp_localize in date-range-ninja-forms.php
			}

			let daterangeField = $(view.el).find('.daterange')[0];

			let lang = drDateRange.lang.replace('_', '-');
			try {
				Intl.getCanonicalLocales(lang);
			} catch (error) {
				console.error(error)
				let lang = 'en-US';
			}
			/**
			 * Note, you can replace the code below with your own date range plugin. If
			 * you do, remeber to load needed libraries using wp_enqueue_script/style in
			 * date-range-ninja-forms-php
			 */

			const litepickerConfig = {
				element: daterangeField,
				firstDay: drStartOfWeek,
				singleMode: false,
				format: drDateFormat,
				disableWeekends: drDisableWeekends,
				numberOfMonths: 2,
				numberOfColumns: 2,
				showWeekNumbers: drShowWeekNumbers,
				selectBackward: drSelectBackward,
				selectForward: drSelectForward,
				showTooltip: drToolTip,
				lang: lang,
			};

			if (drToolTip !== false) {
				litepickerConfig.tooltipText = {
					one: drTooltipSingular,
					other: drTooltipPlural
				};
			}

			if (drMinMaxDate !== false) {
				if (typeof drMinMaxDateStart !== 'undefined' && drMinMaxDateStart !== '') {

					minDate = moment(drMinMaxDateStart, 'YYYY-MM-YY', true).format();
					if (minDate !== 'Invalid date') {
						litepickerConfig.minDate = minDate;
					} else {
						console.error('Invalid date format: %s  Valid is YYYY-MM-YY. E.g.: 2020-02-29', drMinMaxDateStart);
					}
				}
			}

			if (drMinMaxDate !== false) {
				if (typeof drMinMaxDateEnd !== 'undefined' && drMinMaxDateEnd !== '') {
					maxDate = moment(drMinMaxDateEnd, 'YYYY-MM-YY', true).format();
					if (maxDate !== 'Invalid date') {
						litepickerConfig.maxDate = maxDate;
					} else {
						console.error('Invalid date format: %s Valid is YYYY-MM-YY. E.g.: 2020-02-29', drMinMaxDateEnd);
					}
				}
			}

			if (drMinMaxDays !== false) {
				if (typeof drMinMaxDaysMin !== 'undefined' && drMinMaxDaysMin !== '' && drMinMaxDaysMin > 0) {
					litepickerConfig.minDays = drMinMaxDaysMin - 1;
				}
			}
			if (drMinMaxDays !== false) {
				if (typeof drMinMaxDaysMax !== 'undefined' && drMinMaxDaysMax !== '' && drMinMaxDaysMax > 0) {
					litepickerConfig.maxDays = drMinMaxDaysMax - 1;
				}
			}


			if (typeof drDateRange.dropdowns !== 'undefined' && drDateRange.dropdowns !== '[]') {
				litepickerConfig.dropdowns = drDateRange.dropdowns;
			}

			if (typeof drDateRange.buttontext !== 'undefined' && drDateRange.buttontext !== '[]') {
				litepickerConfig.buttonText = drDateRange.buttontext;
			}



			// https://wakirin.github.io/Litepicker/
			const picker = new Litepicker(litepickerConfig);
		},
		/**
		 * from https://github.com/wpninjas/ninja-forms/blob/83cccc6815c98a7ef50ca62704b2661eb53dd3cc/assets/js/front-end/controllers/fieldDate.js#L77-L136
		 * @param {*} dateFormat
		 */
		convertDateFormat: function (dateFormat) {
			// http://php.net/manual/en/function.date.php
			// https://github.com/dbushell/Pikaday/blob/master/README.md#formatting
			// Note: Be careful not to add overriding replacements. Order is important here.

			/** Day */
			dateFormat = dateFormat.replace('D', 'ddd'); // @todo Ordering issue?
			dateFormat = dateFormat.replace('d', 'DD');
			dateFormat = dateFormat.replace('l', 'dddd');
			dateFormat = dateFormat.replace('j', 'D');
			dateFormat = dateFormat.replace('N', ''); // Not Supported
			dateFormat = dateFormat.replace('S', ''); // Not Supported
			dateFormat = dateFormat.replace('w', 'd');
			dateFormat = dateFormat.replace('z', ''); // Not Supported

			/** Week */
			dateFormat = dateFormat.replace('W', 'W');

			/** Month */
			dateFormat = dateFormat.replace('M', 'MMM'); // "M" before "F" or "m" to avoid overriding.
			dateFormat = dateFormat.replace('F', 'MMMM');
			dateFormat = dateFormat.replace('m', 'MM');
			dateFormat = dateFormat.replace('n', 'M');
			dateFormat = dateFormat.replace('t', '');  // Not Supported

			// Year
			dateFormat = dateFormat.replace('L', ''); // Not Supported
			dateFormat = dateFormat.replace('o', 'YYYY');
			dateFormat = dateFormat.replace('Y', 'YYYY');
			dateFormat = dateFormat.replace('y', 'YY');

			// Time - Not supported
			dateFormat = dateFormat.replace('a', '');
			dateFormat = dateFormat.replace('A', '');
			dateFormat = dateFormat.replace('B', '');
			dateFormat = dateFormat.replace('g', '');
			dateFormat = dateFormat.replace('G', '');
			dateFormat = dateFormat.replace('h', '');
			dateFormat = dateFormat.replace('H', '');
			dateFormat = dateFormat.replace('i', '');
			dateFormat = dateFormat.replace('s', '');
			dateFormat = dateFormat.replace('u', '');
			dateFormat = dateFormat.replace('v', '');

			// Timezone - Not supported
			dateFormat = dateFormat.replace('e', '');
			dateFormat = dateFormat.replace('I', '');
			dateFormat = dateFormat.replace('O', '');
			dateFormat = dateFormat.replace('P', '');
			dateFormat = dateFormat.replace('T', '');
			dateFormat = dateFormat.replace('Z', '');

			// Full Date/Time - Not Supported
			dateFormat = dateFormat.replace('c', '');
			dateFormat = dateFormat.replace('r', '');
			dateFormat = dateFormat.replace('u', '');

			return dateFormat;
		}
	});

	new selectedHTML();

})(jQuery)

