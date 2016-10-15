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
	public $display_form;
	public $errors;

	function __construct() {
		global $errors;

		// Check Quote Step
		$this->quote_step = $this->check_quote_step();
		
		// New form object
		$this->form = new Form();
		
		// Check if it is submission
		if ($this->form->check_submission()) {
			$submitted_values = $this->form->handle_submission($this->quote_step);
			if (!empty($errors)) {
				$_SESSION["errors"] = $errors;
				$this->errors = $this->display_quote_errors();
			} else {
				// Insert/update
				var_dump($submitted_values);
			}
		}

		// Store submitted values

		// Set Quote Form
		$this->display_quote_step_form();
	}

	/**
	 * Check Step
	 * @return mixed
	 */
	private function check_quote_step() {
		$step = filter_input(INPUT_GET, 'step', FILTER_SANITIZE_STRING);
		$allowed_steps = ['personal-details', 'bike-details', 'cover-type'];
		$found = array_search($step, $allowed_steps);
		if ($found === 0 || $found) {
			return $step;
		}
		if ($step && !$found) {
			Utilities::redirect('/');
		}
	}

	/**
	 * Display quote form based on step
	 * @return null
	 */
	private function display_quote_step_form() {
		if ($this->quote_step == 'personal-details') {
			$this->display_form = $this->form->display_form(Utilities::$personal_details);
		} elseif ($this->quote_step == 'bike-details') {
			$this->display_form = $this->form->display_form(Utilities::$bike_details);
		} elseif ($this->quote_step == 'cover-type') {
			$this->display_form = $this->form->display_form(Utilities::$cover_type);
		}
	}

	/**
	 * Display quote errors
	 * @return string
	 */
	private function display_quote_errors() {
		if (isset($_SESSION["errors"]) && !empty($_SESSION["errors"])) {
			$o = '<ul>';
			foreach ($_SESSION["errors"] as $field => $value) {
				$o .= '<li>' . $value . '</li>';
			}
			$o .= '</ul>';
			$_SESSION["errors"] = null; // Clear
			return $o;
		}
	}

}