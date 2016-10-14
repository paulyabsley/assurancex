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
		if ($this->quote_step) {
			$form = new Form();
			if ($this->quote_step == 'user-details') {
				$this->form = $form->display_form(Utilities::$user_details);
			} elseif ($this->quote_step == 'bike_details') {
				$this->form = $form->display_form(Utilities::$bike_details);
			} elseif ($this->quote_step == 'cover-type') {
				$this->form = $form->display_form(Utilities::$cover_type);
			}
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
}