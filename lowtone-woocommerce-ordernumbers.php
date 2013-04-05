<?php
/*
 * Plugin Name: WooCommerce Order Numbers
 * Plugin URI: http://wordpress.lowtone.nl/woocommerce-ordernumbers
 * Plugin Type: plugin
 * Description: Create order numbers using custom formatting.
 * Version: 1.0
 * Author: Lowtone <info@lowtone.nl>
 * Author URI: http://lowtone.nl
 * License: http://wordpress.lowtone.nl/license
 */
/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\plugins\lowtone\woocommerce\ordernumbers
 */

namespace lowtone\woocommerce\ordernumbers {

	use lowtone\Util,
		lowtone\types\datetime\DateTime,
		lowtone\content\packages\plugins\Plugin,
		WC_Order;

	// Includes
	
	if (!include_once WP_PLUGIN_DIR . "/lowtone-content/lowtone-content.php") 
		return trigger_error("Lowtone Content plugin is required", E_USER_ERROR);

	Plugin::init(array(
			Plugin::INIT_PACKAGES => array("lowtone"),
			Plugin::INIT_MERGE_PATH => __NAMESPACE__
		));

	add_action("woocommerce_new_order", function($orderId) {
		if (!is_null(orderNumber($orderId)))
			return;

		$order = new WC_Order($orderId);

		$date = DateTime::fromString($order->order_date);

		$year = $date->format("Y");
		$userId = get_current_user_id();

		$countTotal = countTotal();
		$countYear = countYear($year);
		$countUser = countUser($userId);

		// Increment
		
		$countTotal++;

		$countYear["total"] += 1;
		$countYear["months"][$date->format("m")]["total"] += 1;
		$countYear["months"][$date->format("m")]["days"][$date->format("d")]["total"] += 1;

		$countUser++;

		// Create order number

		$orderNumber = preg_replace_callback("/%([^%]+)%/", function($matches) use ($order, $date, $userId, $countTotal, $countYear, $countUser) {
			@list($key, $pad, $split) = array_map("trim", explode(":", $matches[1]));
			@list($padLength, $padString) = array_map("trim", explode(",", $pad));
			@list($splitLength, $splitSep) = array_map("trim", explode(",", $split));

			$key = strtolower($key);

			$prep = function($input) use ($padLength, $padString, $splitLength, $splitSep) {
				return split(pad($input, $padLength, $padString), $splitLength, $splitSep);
			};

			switch ($key) {
				case "year":
					return $prep($date->format("Y"));

				case "monthnum":
					return $prep($date->format("m"));

				case "day":
					return $prep($date->format("d"));

				case "week":
					return $prep($date->format("W"));

				case "order_id":
					return $prep($order->id);

				case "order_count":
					return $prep($countTotal);

				case "year_count":
					return $prep(@$countYear["total"]);

				case "month_count":
					return $prep(@$countYear["months"][$date->format("m")]["total"]);

				case "day_count":
					return $prep(@$countYear["months"][$date->format("m")]["days"][$date->format("d")]["total"]);

				case "user_id":
					return $prep($userId);

				case "user_count":
					return $prep($countUser);

			}

			$options = array(
					"key" => $key,
					"pad" => array(
							"length" => $padLength,
							"string" => $padString
						),
					"split" => array(
							"length" => $splitLength,
							"sep" => $splitSep
						),
					"count_total" => $countTotal,
					"count_year" => $countYear,
					"count_user" => $countUser
				);

			return apply_filters("lowtone_woocommerce_ordernumbers_replace", $key, $matches[0], $order, $options, $prep);
		}, format());

		// Set order number
		
		orderNumber($orderId, $orderNumber);

		// Update order count
		
		countTotal($countTotal);
		countYear($year, $countYear);
		countUser($userId, $countUser);
	});

	add_filter("woocommerce_order_number", function($orderNumber, $order) {
		return orderNumber($order->id) ?: $orderNumber;
	}, 10, 2);

	add_filter("woocommerce_shortcode_order_tracking_order_id", function($orderId) {
		global $wpdb;

		$query = $wpdb->prepare("SELECT `post_id` FROM {$wpdb->postmeta} WHERE `meta_key`='_lowtone_woocommerce_ordernumber' AND `meta_value`=%s", trim($orderId));

		return NULL !== ($orderId = $wpdb->get_var($query)) ? $orderId : false;
	});

	add_filter("woocommerce_general_settings", function($settings) {
		$settings[] = array(
			"name" => __("Order numbers", "lowtone_woocommerce_ordernumbers"), 
			"type" => "title", 
			"desc" => __("Provide a template for formatting new order numbers. Tags can be used to add dynamic values to the order number. A tag consists of a key surrounded by percent signs like %year%. The key refers to a value that will replace the tag, the previous example for instance would add the year in which the order was placed to the order number. Below is a list of available tags (additional tags could be added by plugins).", "lowtone_woocommerce_ordernumbers"),  
			"id" => "lowtone_woocommerce_ordernumbers_options" 
		);

		$settings[] = array(
			"name" => __("Order number format", "lowtone_woocommerce_ordernumbers"),
			"desc" => __("A template used for creating new order numbers.", "lowtone_woocommerce_ordernumbers"),
			"id" => "lowtone_woocommerce_ordernumbers_format",
			"type" => "text",
			"css" => "min-width:560px;",
			"std" => esc_attr(format())
		);

		add_action("woocommerce_admin_field_html", function($value) {
			if ($value["id"] != "lowtone_woocommerce_ordernumbers_doc")
				return;

			echo '<tr valign="top">' .
				'<th scope="row" class="titledesc">' .
				(trim($title = @$value["title"]) ? '<label>' . esc_html($title) . '</label>' : '') .
				'</th>' .
				'<td class="forminp forminp-html">' .
				$value["content"] .
				'</td>' .
				'</tr>';
		});

		$tagDescriptions = array(
			"year" => __("The year in which the order was placed.", "lowtone_woocommerce_ordernumbers"),
			"monthnum" => __("The numeric representation for the month in which the order was placed.", "lowtone_woocommerce_ordernumbers"),
			"day" => __("The day of the month in which the order was placed.", "lowtone_woocommerce_ordernumbers"),
			"week" => __("The ISO-8601 week number for the year in which the order was placed.", "lowtone_woocommerce_ordernumbers"),
			"order_id" => __("The ID of the order.", "lowtone_woocommerce_ordernumbers"),
			"order_count" => __("The total number of orders placed.", "lowtone_woocommerce_ordernumbers"),
			"year_count" => __("The number of orders placed in the year in which the order was placed.", "lowtone_woocommerce_ordernumbers"),
			"month_count" => __("The number of orders placed in the month in which the order was placed.", "lowtone_woocommerce_ordernumbers"),
			"day_count" => __("The number of orders placed on the day on which the order was placed.", "lowtone_woocommerce_ordernumbers"),
			"user_id" => __("The ID for the user who placed the order.", "lowtone_woocommerce_ordernumbers"),
			"user_count" => __("The total number of orders placed by the user who placed the order.", "lowtone_woocommerce_ordernumbers"),
		);

		$tagDescriptions = apply_filters("lowtone_woocommerce_ordernumbers_tag_descriptions", $tagDescriptions);
		
		$settings[] = array(
			"type" => "html",
			"id" => "lowtone_woocommerce_ordernumbers_doc",
			"content" => '<table class="lowtone woocommerce ordernumbers tags"><thead><tr><th>' . __("Key", "lowtone_woocommerce_ordernumbers") . '</th><th>' . __("Value", "lowtone_woocommerce_ordernumbers") . '</th></tr></thead><tbody>' . 
				implode(array_map(function($description, $tag) {
					return '<tr><th>' . $tag . '</th><td>' . $description . '</td></tr>';
				}, $tagDescriptions, array_keys($tagDescriptions))) . 
				'</tbody></table>' . 
				'<p>' . __('Modifiers can be added behind the key to convert the value to a default format. Modifiers are separated from the key and each other by a colon. Currenty two modifiers are available, namely (in this order):', "lowtone_woocommerce_ordernumbers") . '</p>' . 
				'<p><ul>' . 
				'<li>' . __('<strong>Length</strong> &mdash; Padding could be used to extend the value to a default length. For instance when using the order_count value padding could be used to create an four-digit number even for the lower numbers by adding some zeros to the left of the number (e.g. "1" becomes "0001"). Padding is defined by two values separated by a comma, the first value being the desired length, the optional second value the character used for the padding which defaults to "0". When the given length is less then the length of the value characters will be removed from the left side of the value until it matches the required length.', "lowtone_woocommerce_ordernumbers") . '</li>' . 
				'<li>' . __('<strong>Split</strong> &mdash; The second modifier could be used to make the order number more readable. Like the padding it consists of two values separated by a comma, the first being the size for each part and the second optional value being a character used to glue the parts together which defaults to "-".', "lowtone_woocommerce_ordernumbers") . '</li>' . 
				'</ul>'
		);

		$settings[] = array(
			"type" => "sectionend", 
			"id" => "lowtone_woocommerce_ordernumbers_options"
		);

		return $settings;
	});
	
	add_action("plugins_loaded", function() {
		load_plugin_textdomain("lowtone_woocommerce_ordernumbers", false, basename(__DIR__) . "/assets/languages");
	});

	// Functions
	
	function format($newVal = NULL) {
		if (!is_null($newVal))
			return update_option("lowtone_woocommerce_ordernumbers_format", $newVal);

		return get_option("lowtone_woocommerce_ordernumbers_format") ?: "#%order_id%";
	}

	function countTotal($newVal = NULL) {
		if (!is_null($newVal))
			return update_option("lowtone_woocommerce_ordernumbers_count_total", $newVal);
			
		return get_option("lowtone_woocommerce_ordernumbers_count_total") ?: 0;
	}

	function countYear($year = NULL, $newVal = NULL) {
		if (is_null($year))
			$year = DateTime::now()->format("Y");

		if (!is_null($newVal))
			return update_option("lowtone_woocommerce_ordernumbers_count_" . $year, $newVal);

		return get_option("lowtone_woocommerce_ordernumbers_count_" . $year) ?: array();
	}

	function countUser($userId = NULL, $newVal = NULL) {
		if (is_null($userId))
			$userId = get_current_user_id();

		if (!is_null($newVal))
			return update_user_meta($userId, "lowtone_woocommerce_ordernumbers_count", $newVal);

		return get_user_meta($userId, "lowtone_woocommerce_ordernumbers_count", true) ?: 0;
	}

	/**
	 * Fetch or update the order number for the order with the given ID.
	 * @param int $orderId The ID for the required order.
	 * @param string|NULL $newVal An optional new value for the order number.
	 * @return string|bool|NULL Returns NULL if no order ID was provided or no
	 * order number is available for the required order. If a new value is 
	 * provided TRUE is returned when the order number is successfully updated 
	 * or FALSE if not. Returns the order number on a successful fetch.
	 */
	function orderNumber($orderId, $newVal = NULL) {
		if (!is_numeric($orderId))
			return NULL;

		if (!is_null($newVal))
			return update_post_meta($orderId, "_lowtone_woocommerce_ordernumber", $newVal);

		return get_post_meta($orderId, "_lowtone_woocommerce_ordernumber", true) ?: NULL;
	}

	function pad($input, $length, $string = NULL) {
		if (!is_numeric($length))
			return $input;

		if (strlen($input) > $length)
			return substr($input, -$length);

		return str_pad($input, $length, ($string ?: "0"), STR_PAD_LEFT);
	}

	function split($input, $length, $sep = NULL) {
		if (!is_numeric($length) || $length < 1) 
			return $input;

		return implode(($sep ?: "-"), str_split($input, $length));
	}

}