/**
 * Add date picker to DateRange action setting.
 *
 * @author Per Soderlind http://soderlind.no
 *
 */
document.addEventListener(
	"DOMContentLoaded",
	() => {
		const nfRadio = Backbone.Radio; // rome-ignore lint/js/noUndeclaredVariables: Backbone is an external object.

		const DateRangeMinDateSettingChannel = nfRadio.channel("setting-min_date");
		const DateRangeMaxDateSettingChannel = nfRadio.channel("setting-max_date");

		const DateField = class extends Marionette.Object { // rome-ignore lint/js/noUndeclaredVariables: Marionett is an external object.
			nfTextboxStyle = {
				"background": "#f9f9f9",
				"border": "0",
				"marginTop": "7px",
				"padding": "12px 15px",
				"width": "100%",
				"height": "41px",
			};
			/**
			 * initialize()
			 *
			 */
			initialize() {
				this.listenTo(
					DateRangeMinDateSettingChannel,
					"render:setting",
					this.renderDateField,
				);
				this.listenTo(
					DateRangeMaxDateSettingChannel,
					"render:setting",
					this.renderDateField,
				);
			}

			/**
			 * Convert the textbox (input type="text") to date field (input type="date").
			 *
			 * - If empty, set the date to "today", in the format YYYY-MM-DD.
			 * - Set the style of the field to the same as a textbox.
			 *
			 * @see https://caniuse.com/input-datetime
			 *
			 * @param {*} settingModel
			 * @param {*} dataModel
			 * @param {*} view
			 */
			renderDateField(settingModel, dataModel, view) {
				const element = view.el.getElementsByClassName("setting")[0];
				element.attributes["type"].value = "date";
				element.attributes["pattern"] = "d{4}-d{2}-d{2}";
				Object.assign(element.style, this.nfTextboxStyle);
			}
		};

		new DateField();
	},
);
