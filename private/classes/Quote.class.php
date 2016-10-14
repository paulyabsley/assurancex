<?php

// Collect/store user details
// Collect/store bike details
// Collect/store cover type

// DB tables for user and quote
// Users holds user details
// Quotes holds quote details which contains bike details, cover types, id for retrieving quotes

class Quote {

	public $quote_step;
	public $form;

	function __construct() {
		// Check Step
		$this->quote_step = $this->check_quote_step();
		if (!$this->quote_step) {
			Utilities::redirect('../../../public/');
		}

		// Check if it is submission

		// Perform validation

		// Store submitted values

		// Display form
		if ($this->quote_step == 'user-details') {
			$this->form = $this->display_form(Utilities::$user_details);
		} elseif ($this->quote_step == 'bike_details') {
			$this->form = $this->display_form(Utilities::$bike_details);
		} elseif ($this->quote_step == 'cover-type') {
			$this->form = $this->display_form(Utilities::$cover_type);
		}
	}

	/**
	 * Check Step
	 * @return mixed
	 */
	public function check_quote_step() {
		$step = filter_input(INPUT_GET, 'step', FILTER_SANITIZE_STRING);
		$allowed_steps = ['user-details', 'bike-details', 'cover-type'];
		$found = array_search($step, $allowed_steps);
		if ($found === 0 || $found) {
			return $step;
		}
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
		$fieldname_formatted = Utilities::format_field_name($fieldname);
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
		$output = '<div class="row">';
		$output .= '<label for="' . $fieldname . '"' . $error .'>' . $fieldname_formatted . '</label>';
		$output .= '<input type="' . $type . '" name="' . $fieldname . '" id="' . $fieldname . '"' . $error;
		$output .= $maxlength;
		$output .= $required;
		$output .= $value;
		$output .= '>';
		$output .= '</div>';
		return $output;
	}

	/**
	 * Display form Submit
	 * @param array $field Options for the field
	 * @return string formatted HTML
	 */
	function display_submit($field) {
		if (isset($field["name"])) {
			$submit_text = Utilities::format_field_name($field["name"]);
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