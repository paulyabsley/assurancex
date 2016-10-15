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
	public $restart;

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
				$store = $this->store_quote($submitted_values);
			}
		}

		// Set Quote Form
		$this->display_quote_step_form();

		// Set Restart
		$this->display_quote_restart_link();
	}

	/**
	 * Create quote database table
	 * @return null
	 */
	public function create_quote_table() {
		$sql = "CREATE TABLE IF NOT EXISTS `quotes` (
		`id` INT NOT NULL AUTO_INCREMENT,
		`title` VARCHAR(45) NULL,
		`first_name` VARCHAR(100) NULL,
		`surname` VARCHAR(100) NULL,
		`email` VARCHAR(255) NULL,
		`date_of_birth` DATE NULL,
		`house_number` VARCHAR(10) NULL,
		`postcode` VARCHAR(8) NULL,
		`manufacturer` VARCHAR(100) NULL,
		`model` VARCHAR(100) NULL,
		`market_value` VARCHAR(100) NULL,
		`policy_start_date` VARCHAR(100) NULL,
		`type_of_cover` VARCHAR(100) NULL,
		`quote_retrieval` VARCHAR(15) NULL,
		`received` DATETIME NULL,
		PRIMARY KEY (`id`));";
		$table = Database::create_table($sql);
	}

	/**
	 * Check Step
	 * @return mixed
	 */
	private function check_quote_step() {
		$step = filter_input(INPUT_GET, 'step', FILTER_SANITIZE_STRING);
		$allowed_steps = ['personal-details', 'bike-details', 'cover-type', 'new', 'premium'];
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
		if ($this->quote_step == 'new') {
			$_SESSION["quote_id"] = $_SESSION["submitted_values"] = null;
			Utilities::redirect('/quote/personal-details/');
		} elseif ($this->quote_step == 'personal-details') {
			$this->display_form = $this->form->display_form(Utilities::$personal_details);
		} elseif ($this->quote_step == 'bike-details') {
			$this->display_form = $this->form->display_form(Utilities::$bike_details);
		} elseif ($this->quote_step == 'cover-type') {
			$this->display_form = $this->form->display_form(Utilities::$cover_type);
		}
	}

	/**
	 * Display quote restart link
	 * @return null
	 */
	private function display_quote_restart_link() {
		if (isset($_SESSION["quote_id"])) {
			$this->restart = '<br><a href="' . ROOT . '/quote/new/">New Quote</a>';
		}
	}

	/**
	 * Store quote
	 * @return 
	 */
	private function store_quote($submitted_values) {
		if ($this->quote_step == 'personal-details') {
			$db_values = $this->check_submitted_values($submitted_values, 'personal_details');
			if (isset($_SESSION["quote_id"])) {
				$update = Database::update_row_by_id('quotes', $db_values, $_SESSION["quote_id"]);
			} else {
				$insert = Database::insert_row('quotes', $db_values);
				$row = Database::last_inserted_row();
				$_SESSION["quote_id"] = $row;
			}
			if (isset($update) || isset($insert)) {
				$_SESSION["submitted_values"] = null; // Clear
				Utilities::redirect('/quote/bike-details/');
			}
		} elseif ($this->quote_step == 'bike-details') {
			$db_values = $this->check_submitted_values($submitted_values, 'bike_details');
			$update = Database::update_row_by_id('quotes', $db_values, $_SESSION["quote_id"]);
			if (isset($update)) {
				$_SESSION["submitted_values"] = null; // Clear
				Utilities::redirect('/quote/cover-type/');
			}
		} elseif ($this->quote_step == 'cover-type') {
			$db_values = $this->check_submitted_values($submitted_values, 'cover_type');
			$update = Database::update_row_by_id('quotes', $db_values, $_SESSION["quote_id"]);
			if (isset($update)) {
				$_SESSION["submitted_values"] = null; // Clear
				Utilities::redirect('/quote/premium/');
			}
		}
	}

	/**
	 * Verify submmitted values
	 * @param array $submitted_values
	 * @param string $fieldset
	 * @return array $db_values
	 */
	private function check_submitted_values($submitted_values, $fieldset) {
		$db_values = [];
		foreach (Utilities::${$fieldset} as $key => $value) {
			if (isset($value["database"])) {
				$db_values[$key] = $submitted_values[$key];
			}
		}
		return $db_values;
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