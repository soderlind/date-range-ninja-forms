/**
 * Thanks to https://ninjaformsdev.slack.com/archives/C0EFXJF0D/p1524614990000175
 */

document.addEventListener('DOMContentLoaded', e => {
	const nfRadio = Backbone.Radio;
	const radioChannel = nfRadio.channel('daterange'); // 'daterange',  the $_type value, defined in date-range-ninja-forms.php

	const DateRange = class extends Marionette.Object {

		/**
		 * initialize()
		 *
		 * When initialize the form, listen to the radio chanel to see if there's a 'daterange' chanel.
		 */
		initialize() {
			this.listenTo(radioChannel, 'render:view', this.renderView);
		}
		/**
		 * renderView()
		 *
		 * When rendering the form (i.e. the view), attach custom javascript code and events.
		 */
		renderView(view) {

			let drDateFormat = view.model.get('dr_date_format');
			const drShowWeekNumbers = view.model.get('dr_show_week_numbers');
			const drStartOfWeek = view.model.get('dr_start_of_week');
			const drDisableWeekends = view.model.get('dr_disable_weekends');
			const drSelectBackward = view.model.get('dr_select_backward');
			const drSelectForward = view.model.get('dr_select_forward');
			const drAutoApply = view.model.get('dr_auto_apply');
			const drToolTip = view.model.get('dr_tooltip');
			const drTooltipSingular = view.model.get('dr_tooltip_singular');
			const drTooltipPlural = view.model.get('dr_tooltip_plural');
			const drMinMaxDate = view.model.get('dr_max_min_date');
			const drMinMaxDateStart = view.model.get('dr_min_date');
			const drMinMaxDateEnd = view.model.get('dr_max_date');
			const drMinMaxDays = view.model.get('dr_min_max_days');
			const drMinMaxDaysMin = view.model.get('dr_min_days');
			const drMinMaxDaysMax = view.model.get('dr_max_days');

			// console.table({
			// 	drDateFormat: drDateFormat,
			// 	drShowWeekNumbers: drShowWeekNumbers,
			// 	drStartOfWeek: drStartOfWeek,
			// 	drDisableWeekends: drDisableWeekends,
			// 	drSelectBackward: drSelectBackward,
			// 	drSelectForward: drSelectForward,
			// 	drAutoApply: drAutoApply,
			// 	drToolTip: drToolTip,
			// 	drTooltipSingular: drTooltipSingular,
			// 	drTooltipPlural: drTooltipPlural,
			// 	drMinMaxDate: drMinMaxDate,
			// 	drMinMaxDateStart: drMinMaxDateStart,
			// 	drMinMaxDateEnd: drMinMaxDateEnd,
			// 	drMinMaxDays: drMinMaxDays,
			// 	drMinMaxDaysMin: drMinMaxDaysMin,
			// 	drMinMaxDaysMax: drMinMaxDaysMax,
			// });

			// For "default" date format, convert PHP format to JS compatible format.
			if ('' == drDateFormat || 'default' == drDateFormat) {
				drDateFormat = this.convertDateFormat(drDateRange.dateFormat); // 'drDateRange' from wp_localize in date-range-ninja-forms.php
			}
			const daterangeField = view.el.getElementsByClassName('daterange')[0];

			let lang = drDateRange.lang.replace('_', '-');
			try {
				Intl.getCanonicalLocales(lang);
			} catch (error) {
				console.error('Invalid date format: %s, should look something like this: en-US', lang);
				let lang = 'en-US';
			}

			const litepickerConfig = {
				element: daterangeField,
				firstDay: drStartOfWeek,
				singleMode: 0,
				format: drDateFormat,
				disableWeekends: drDisableWeekends,
				numberOfMonths: 2,
				numberOfColumns: 2,
				showWeekNumbers: drShowWeekNumbers,
				selectBackward: drSelectBackward,
				selectForward: drSelectForward,
				showTooltip: drToolTip,
				autoApply: drAutoApply,
				lang: lang
			};

			if (drToolTip !== 0) {
				litepickerConfig.tooltipText = {
					one: drTooltipSingular,
					other: drTooltipPlural
				};
			}

			if (drMinMaxDate != 0) {
				if (typeof drMinMaxDateStart !== 'undefined' && drMinMaxDateStart !== '') {
					if (this.isValidDate(drMinMaxDateStart)) {
						litepickerConfig.minDate = drMinMaxDateStart;
					} else {
						console.error('Invalid date format: %s  Valid is YYYY-MM-YY. E.g.: 2020-02-29', drMinMaxDateStart);
					}
				}
			}

			if (drMinMaxDate != 0) {
				if (typeof drMinMaxDateEnd !== 'undefined' && drMinMaxDateEnd !== '') {
					if (this.isValidDate(drMinMaxDateEnd)) {
						litepickerConfig.maxDate = drMinMaxDateEnd;
					} else {
						console.error('Invalid date format: %s Valid is YYYY-MM-YY. E.g.: 2020-02-29', drMinMaxDateEnd);
					}
				}
			}

			if (drMinMaxDays != 0) {
				if (typeof drMinMaxDaysMin !== 'undefined' && drMinMaxDaysMin !== '' && drMinMaxDaysMin > 0) {
					litepickerConfig.minDays = drMinMaxDaysMin - 1;
				}
			}

			if (drMinMaxDays != 0) {
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
		}
		/**
		 * from https://github.com/wpninjas/ninja-forms/blob/83cccc6815c98a7ef50ca62704b2661eb53dd3cc/assets/js/front-end/controllers/fieldDate.js#L77-L136
		 * @param {*} dateFormat
		 */
		convertDateFormat(dateFormat) {
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

		/**
		 * From: https://stackoverflow.com/a/35413963
		 * @param {*} dateString
		 */
		isValidDate(dateString) {
			const regEx = /^\d{4}-\d{2}-\d{2}$/;
			if (!dateString.match(regEx)) return false;  // Invalid format
			const d = new Date(dateString);
			const dNum = d.getTime();
			if (!dNum && dNum !== 0) return false; // NaN value, Invalid date
			return d.toISOString().slice(0, 10) === dateString;
		}
	};

	new DateRange();

});

