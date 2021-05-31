/**
 * Thanks to https://ninjaformsdev.slack.com/archives/C0EFXJF0D/p1524614990000175
 */
// @ts-check
document.addEventListener(
	"DOMContentLoaded",
	() => {
		const nfRadio = Backbone.Radio;
		const radioChannel = nfRadio.channel("daterange"); // 'daterange',  the $_type value, defined in date-range-ninja-forms.php
		const submitChannel = nfRadio.channel("submit");
		const fieldsChannel = nfRadio.channel("fields");

		const DateRange = class extends Marionette.Object {
			fieldType = "daterange";
			fieldID = 0;
			picker = {};
			daterangeRequired = 0;

		/**
		 * initialize()
		 *
		 * When initialize the form, listen to the radio chanel to see if there's a 'daterange' chanel.
		 */
		initialize() {
			this.listenTo(radioChannel, "render:view", this.renderView);

			this.listenTo(
				submitChannel,
				"validate:field",
				this.validateRequiredField,
			);
			this.listenTo(submitChannel, "validate:field", this.saveField); // when a field isn't required
			this.listenTo(
				fieldsChannel,
				"change:model",
				this.useCustomRequiredField,
			);
		}

		/**
		 * Use custom required field
		 *
		 * @param {*} model
		 */
		useCustomRequiredField(model) {
			if (this.fieldType !== model.get("type")) {
				return true;
			}
			if (0 === model.get("required")) {
				return;
			}
			if ({} === this.picker) {
				return true;
			}

			if (
				typeof this.picker.getDate === "function" &&
				null == this.picker.getDate()
			) {
				this.daterangeRequired = 1;
				this.fieldID = model.get("id");
				model.set("required", 0);
			}
			return true;
		}

		/**
		 * validateRequireField()
		 *
		 * For required data rage field, check that it has a value.
		 * Saves validated dates.
		 *
		 * @param {*} model
		 */
		validateRequiredField(model) {
			if (this.fieldType !== model.get("type")) {
				return true;
			}
			if (0 === this.daterangeRequired) {
				return;
			}
			if (!this.picker) {
				return true;
			}

			if (
				true ===
				dayjs(this.picker.getDate(), this.getDateFormat(model), true).isValid()
			) {
				this.addDates(model);
				// Remove Error from Model
				fieldsChannel.request(
					"remove:error",
					model.get("id"),
					"required-error",
				);
			} else {
				// Add Error to Model
				fieldsChannel.request(
					"add:error",
					model.get("id"),
					"required-error",
					nfi18n.validateRequiredField,
				);
			}
		}

		/**
		 * saveField()
		 *
		 * Used when the field is not required.
		 *
		 * @param {*} model
		 */
		saveField(model) {
			if (this.fieldType !== model.get("type")) {
				return true;
			}
			if (1 === this.daterangeRequired) {
				return true;
			}
			if (!this.picker) {
				return true;
			}

			if (
				typeof this.picker.getDate === "function" &&
				null != this.picker.getDate() &&
				this.picker.getDate() > 0
			) {
				this.addDates(model);
			}
		}

		/**
		 * addDates()
		 *
		 * Add dates to the model.
		 *
		 * @param {*} model
		 */
			addDates(model) {
				const dateFormat = this.getDateFormat(model);
				const startDate = dayjs(this.picker.getStartDate()).format(dateFormat);
				const endDate = dayjs(this.picker.getEndDate()).format(dateFormat);

				if (this.singleMode) model.set("value", startDate);
				else model.set("value", `${startDate} - ${endDate}`);
			}

		/**
		 * renderView()
		 *
		 * When rendering the form (i.e. the view), attach custom javascript code and events.
		 */
			renderView(view) {
				const litepickerConfig = {
					element: view.el.getElementsByClassName("daterange")[0],
					firstDay: view.model.get("start_of_week"),
					format: this.getDateFormat(view.model),
					lang: () => {
						let lang = drDateRange.lang.replace("_", "-");
						try {
							Intl.getCanonicalLocales(lang);
						} catch (error) {
							console.error(
								"Invalid date format: %s. Should look something like this: en-US",
								lang,
							);
							let lang = "en-US";
						}
						return lang;
					},
					singleMode: 0,
					disableWeekends: view.model.get("disable_weekends"),
					numberOfMonths: 2,
					numberOfColumns: 2,
					showWeekNumbers: view.model.get("show_week_numbers"),
					selectBackward: view.model.get("select_backward"),
					selectForward: view.model.get("select_forward"),
					showTooltip: view.model.get("tooltip"),
					autoApply: view.model.get("auto_apply"),
					lockDays: [
						// 2021
						'2021-01-01', '2021-03-28', '2021-04-01', '2021-04-02', '2021-04-04', 
						'2021-04-05', '2021-04-30', '2021-05-13', '2021-05-23', '2021-05-24',
						'2021-06-05', '2021-12-24', '2021-12-25', '2021-12-26', '2021-12-31',
					
						// 2022
						'2021-01-01', '2021-04-10', '2021-04-14', '2021-04-15', '2021-04-17', 
						'2021-04-18', '2021-05-13', '2021-05-26', '2021-06-05', '2021-06-06', 
						'2021-12-24', '2021-12-25', '2021-12-26', '2021-12-31'
					],
					onShow: () => {
						fieldsChannel.request(
							"remove:error",
							this.fieldID,
							"required-error",
						);
					},
				};

				if (0 !== litepickerConfig.showTooltip) {
					litepickerConfig.tooltipText = {
						one: view.model.get("tooltip_singular"),
						other: view.model.get("tooltip_plural"),
					};
				}

				if (0 !== view.model.get("max_min_date")) {
					let minMaxDateStart = view.model.get("min_date");
					let minMaxDateEnd = view.model.get("max_date");
					const urlParams = new URLSearchParams(window.location.search);

					// get the start date and check if its a query string
					if (minMaxDateStart.includes('{querystring:')){
						// get query string key from input date and replace whitespace
						const qStringStart = minMaxDateStart.split(':')[1].replace('}', '');
						// get query string value by key
						const queryDateStart = urlParams.get(qStringStart);
						// if the value is not null. Set it as the chosen date and remove any unwanted whitespace.
						if (queryDateStart) minMaxDateStart = queryDateStart.replace(/\s/g, '');;
					}

					// get the end date and check if its a query string...
					if (minMaxDateEnd.includes('{querystring:')){
						const qStringEnd = minMaxDateEnd.split(':')[1].replace('}', '');
						const queryDateEnd = urlParams.get(qStringEnd);
						if (queryDateEnd) minMaxDateEnd = queryDateEnd.replace(/\s/g, '');
					}

					if (typeof minMaxDateStart !== "undefined" && minMaxDateStart !== "") {
						if (this.isValidDate(minMaxDateStart)) {
							let currentDate = new Date();
							currentDate.setDate(currentDate.getDate() + 4);
							
							// some locations needs extra days to aquire the vehicle
							const location = urlParams.get('location');
							if (location && location === '16') {
								// add 2 days
								currentDate.setDate(currentDate.getDate() + 2);
							};

							// if the selected min date is in the past, use the current date instead
							if (new Date(minMaxDateStart) < currentDate) litepickerConfig.minDate = currentDate;
							// else use the selected date
							else litepickerConfig.minDate = minMaxDateStart;
						} else {
							console.error(
								"Invalid date format: %s. Valid is YYYY-MM-YY. E.g.: 2020-02-29",
								minMaxDateStart,
							);
						}
					}

					if (typeof minMaxDateEnd !== "undefined" && minMaxDateEnd !== "") {
						if (this.isValidDate(minMaxDateEnd)) {
							litepickerConfig.maxDate = minMaxDateEnd;
						} else {
							console.error(
								"Invalid date format: %s. Valid is YYYY-MM-YY. E.g.: 2020-02-29",
								minMaxDateEnd,
							);
						}
					}
				}

				if (0 !== view.model.get("min_max_days")) {
					const minMaxDaysMin = view.model.get("min_days");
					const minMaxDaysMax = view.model.get("max_days");

					if (
						typeof minMaxDaysMin !== "undefined" &&
						minMaxDaysMin !== "" &&
						minMaxDaysMin > 0
					) {
						litepickerConfig.minDays = minMaxDaysMin - 1;
					}

					if (
						typeof minMaxDaysMax !== "undefined" &&
						minMaxDaysMax !== "" &&
						minMaxDaysMax > 0
					) {
						litepickerConfig.maxDays = minMaxDaysMax - 1;

						// if max days is one, set single date
						if (minMaxDaysMax === 1 && minMaxDaysMin === 0){
							litepickerConfig.singleMode = 1;
							this.singleMode = 1;
						}
					}
				}

				if (
					typeof drDateRange.dropdowns !== "undefined" &&
					drDateRange.dropdowns !== "[]"
				) {
					litepickerConfig.dropdowns = drDateRange.dropdowns;
				}

				if (
					typeof drDateRange.buttontext !== "undefined" &&
					drDateRange.buttontext !== "[]"
				) {
					litepickerConfig.buttonText = drDateRange.buttontext;
				}

				// https://wakirin.github.io/Litepicker/
				this.picker = new Litepicker(litepickerConfig);
			}

		/**
		 *
		 * @param {*} model
		 */
			getDateFormat(model) {
				let dateFormat = model.get("date_format");
				if ("" === dateFormat || "default" === dateFormat) {
					dateFormat = this.convertDateFormat(drDateRange.dateFormat); // 'DateRange' from wp_localize in date-range-ninja-forms.php
				}
				return dateFormat;
			}

		/**
		 * from https://github.com/wpninjas/ninja-forms/blob/83cccc6815c98a7ef50ca62704b2661eb53dd3cc/assets/js/front-end/controllers/fieldDate.js#L77-L136
		 * @param {*} dateFormat
		 */
			convertDateFormat(dateFormat) {
				// http://php.net/manual/en/function.date.php
				// https://github.com/dbushell/Pikaday/blob/master/README.md#formatting
				// Note: Be careful not to add overriding replacements. Order is important here.
				/** Day*/
				dateFormat = dateFormat.replace("D", "ddd"); // @todo Ordering issue?
				dateFormat = dateFormat.replace("d", "DD");
				dateFormat = dateFormat.replace("l", "dddd");
				dateFormat = dateFormat.replace("j", "D");
				dateFormat = dateFormat.replace("N", ""); // Not Supported
				dateFormat = dateFormat.replace("S", ""); // Not Supported
				dateFormat = dateFormat.replace("w", "d");
				dateFormat = dateFormat.replace("z", ""); // Not Supported

				/** Week*/
				dateFormat = dateFormat.replace("W", "W");

				/** Month*/
				dateFormat = dateFormat.replace("M", "MMM"); // "M" before "F" or "m" to avoid overriding.
				dateFormat = dateFormat.replace("F", "MMMM");
				dateFormat = dateFormat.replace("m", "MM");
				dateFormat = dateFormat.replace("n", "M");
				dateFormat = dateFormat.replace("t", ""); // Not Supported

				// Year
				dateFormat = dateFormat.replace("L", ""); // Not Supported
				dateFormat = dateFormat.replace("o", "YYYY");
				dateFormat = dateFormat.replace("Y", "YYYY");
				dateFormat = dateFormat.replace("y", "YY");

				// Time - Not supported
				dateFormat = dateFormat.replace("a", "");
				dateFormat = dateFormat.replace("A", "");
				dateFormat = dateFormat.replace("B", "");
				dateFormat = dateFormat.replace("g", "");
				dateFormat = dateFormat.replace("G", "");
				dateFormat = dateFormat.replace("h", "");
				dateFormat = dateFormat.replace("H", "");
				dateFormat = dateFormat.replace("i", "");
				dateFormat = dateFormat.replace("s", "");
				dateFormat = dateFormat.replace("u", "");
				dateFormat = dateFormat.replace("v", "");

				// Timezone - Not supported
				dateFormat = dateFormat.replace("e", "");
				dateFormat = dateFormat.replace("I", "");
				dateFormat = dateFormat.replace("O", "");
				dateFormat = dateFormat.replace("P", "");
				dateFormat = dateFormat.replace("T", "");
				dateFormat = dateFormat.replace("Z", "");

				// Full Date/Time - Not Supported
				dateFormat = dateFormat.replace("c", "");
				dateFormat = dateFormat.replace("r", "");
				dateFormat = dateFormat.replace("u", "");

				return dateFormat;
			}

		/**
		 * From: https://stackoverflow.com/a/35413963
		 * @param {*} dateString
		 */
			isValidDate(dateString) {
				const regEx = /^\d{4}-\d{2}-\d{2}$/;
				if (!dateString.match(regEx)) {
					return false;
				} // Invalid format
				const d = new Date(dateString);
				const dNum = d.getTime();
				if (!dNum && dNum !== 0) {
					return false;
				} // NaN value, Invalid date
				return d.toISOString().slice(0, 10) === dateString;
			}
		};

		new DateRange();
	},
);
