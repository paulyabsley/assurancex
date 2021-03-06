<?php

class Form {

	/**
	 * Format field name
	 * @param string The field name e.g. field-name
	 * @return string The formatted field name e.g. Field Name
	 */
	public function format_field_name($field) {
		$field_formatted = ucwords(str_replace('_', ' ', $field));
		return $field_formatted;
	}

	/**
	 * Check for form submission
	 * @return bool
	 */
	public function check_submission() {
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Handle form submissions
	 * @return
	 */
	public function handle_submission($quote_step) {
		switch ($quote_step) {
			case 'personal-details':
				$fields = Utilities::$personal_details;
				break;
			case 'bike-details':
				$fields = Utilities::$bike_details;
				break;
			case 'cover-type':
				$fields = Utilities::$cover_type;
				break;
			case 'retrieve-quote':
				$fields = Utilities::$retrieve_quote;
				break;
			default:
				break;
		}
		foreach ($fields as $field => $options) {
			// Only for fields that have db_value
			if (isset($options["database"])) {
				$name = $options["name"]; // Fieldname
				if (isset($options["type"])) {
					$value = $_POST[$name]; // Submitted value
					// Put values into session submitted_values array. Fieldname as the index
					$_SESSION["submitted_values"][$name] = htmlspecialchars($value);
					$db_values[$name] = $value; // Also put values into db_values array
				} else {
					$db_values[$name] = $options["value"]; // Non form fields use value from own options
				}
				// Validation
				if (isset($options["required"])) {
					Validation::validate_required($name, $value);
				}
			}
		}
		return $db_values;
	}

	/**
	 * Display Form
	 * @param array $fields
	 * @return string
	 */
	public function display_form($fields) {
		$form_fields = '';
		foreach ($fields as $options) {
			$form_fields .= $this->display_field($options);
		}
		$o = '';
		$o .= '<form method="post">';
		// To do: Add CSRF token
		$o .= $form_fields;
		$o .= '</form>';
		return $o;
	}

	/**
	 * Display form fields
	 * @param array options
	 * @return string
	 */
	public function display_field($options) {
		if (isset($options["type"])) {
			switch ($options["type"]) {
				case "select":
					$field = $this->display_label_select($options);
					break;
				case "submit":
					$field = $this->display_submit($options);
					break;
				default:
					$field = $this->display_label_input($options);
					break;
			}
			return $field;
		}
	}

	/**
	 * Display Label Input
	 * @param array $field Options for the field
	 * @return string 
	 */
	private function display_label_input($field) {
		global $errors;
		$submitted_values = (isset($_SESSION["submitted_values"])) ? $_SESSION["submitted_values"] : '';
		if (isset($_SESSION["quote_id"])) {
			$submitted_values = Database::find_row_by_id('quotes', $_SESSION["quote_id"]);
		}
		$fieldname = $field["name"];
		$fieldname_formatted = static::format_field_name($fieldname);
		$error = $value = $required = $maxlength = '';
		// Error
		if (isset($errors[$fieldname])) {
			$error = ' class="error"';
		}
		// Type
		if (isset($field["type"])) {
			$type = $field["type"];
		} else {
			$type = 'text';
		}
		// Maxlength
		if (isset($field["maxlength"])) {
			$maxlength = ' maxlength="' . intval($field["maxlength"]) . '"';
		}
		// Required
		if (isset($field["required"])) {
			$required = ' required';
		}
		// Value
		if (isset($submitted_values) && isset($submitted_values[$fieldname])) {
			$value = ' value="' . htmlspecialchars($submitted_values[$fieldname]) . '"';
		}
		// Output
		$o = '<div>';
		$o .= '<label for="' . $fieldname . '"' . $error .'>' . $fieldname_formatted . '</label>';
		$o .= '<input type="' . $type . '" name="' . $fieldname . '" id="' . $fieldname . '"' . $error;
		$o .= $maxlength;
		// $o .= $required;
		$o .= $value;
		$o .= '>';
		$o .= '</div>';
		return $o;
	}

	/**
	 * Display form Label Select
	 * @param array $field Options for the field
	 * @return string 
	 */
	private function display_label_select($field) {
		global $errors;
		$submitted_values = (isset($_SESSION["submitted_values"])) ? $_SESSION["submitted_values"] : '';
		if (isset($_SESSION["quote_id"])) {
			$submitted_values = Database::find_row_by_id('quotes', $_SESSION["quote_id"]);
		}
		$fieldname = $field["name"];
		$fieldname_formatted = static::format_field_name($fieldname);
		$error = $required = $current_value = $default_value = '';
		// Error
		if (isset($errors[$fieldname])) {
			$error = ' class="error"';
		}
		// Required
		if (isset($field["required"])) {
			$required = ' required';
		}
		// Value
		if (isset($submitted_values) && isset($submitted_values[$fieldname])) {
			$current_value = $submitted_values[$fieldname];
		}
		// Default value
		if (isset($field["default_value"])) {
			$default_value = $field["default_value"];
		}
		$o = '<div>';
		$o .= '<label' . $error . ' for="' . $fieldname . '">' . $fieldname_formatted . '</label>';
		$o .= '<select id="' . $fieldname . '" name="' . $fieldname . '"';
		$o .= $error;
		// $o .= $required;
		$o .= '>';
		$o .= '<option value="' . $default_value . '">Select a ' . $fieldname_formatted . '</option>';
		foreach ($field["options"] as $option) {
			if ($current_value === $option) {
				$selected = ' selected';
			} else {
				$selected = '';
			}
			$o .= '<option value="' . $option . '"' . $selected .'>' . $option . '</option>';
		}
		$o .= '</select>';
		$o .= '</div>';
		return $o;
	}

	/**
	 * Display form Submit
	 * @param array $field Options for the field
	 * @return string formatted HTML
	 */
	function display_submit($field) {
		if (isset($field["name"])) {
			$submit_text = static::format_field_name($field["name"]);
		} else {
			$submit_text = 'Save';
		}
		$value = '';
		if (isset($field["value"])) {
			$value = ' value=' . $field["value"];
		}
		return '<button name="' . $field["name"] . '"' . $value . '">' . $submit_text . '</button>';
	}

}