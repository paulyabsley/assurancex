<?php

class Form {

	function __construct() {

	}

	/**
	 * Format field name
	 * @param string The field name e.g. field-name
	 * @return string The formatted field name e.g. Field Name
	 */
	public static function format_field_name($field) {
		$field_formatted = ucwords(str_replace('_', ' ', $field));
		return $field_formatted;
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
				// case "select":
				// 	$field = $this->display_label_select($options);
				// 	break;
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
		global $submitted_values;
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
		$o .= $required;
		$o .= $value;
		$o .= '>';
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