<?php

class Utilities {

	public static $personal_details = [
		'title' => [
			'name' => 'title',
			'type' => 'text',
			'required' => true,
			'maxlength' => 20,
			'database' => true,
		],
		'first_name' => [
			'name' => 'first_name',
			'type' => 'text',
			'required' => true,
			'maxlength' => 75,
			'database' => true,
		],
		'surname' => [
			'name' => 'surname',
			'type' => 'text',
			'required' => true,
			'maxlength' => 75,
			'database' => true,
		],
		'email' => [
			'name' => 'email',
			'type' => 'email',
			'required' => true,
			'maxlength' => 200,
			'database' => true,
		],
		'date_of_birth' => [
			'name' => 'date_of_birth',
			'type' => 'date',
			'required' => true,
			'maxlength' => 10,
			'database' => true,
		],
		'house_number' => [
			'name' => 'house_number',
			'type' => 'text',
			'required' => true,
			'maxlength' => 20,
			'database' => true,
		],
		'postcode' => [
			'name' => 'postcode',
			'type' => 'text',
			'required' => true,
			'maxlength' => 8,
			'database' => true,
		],
		'submit_cancel' => [
			'name' => 'next',
			'type' => 'submit',
			'value' => 'submit_personal_details',
		]
	];

	public static $bike_details = [
		'manufacturer' => [
			'name' => 'manufacturer',
			'type' => 'text',
			'required' => true,
			'maxlength' => 100,
			'database' => true,
		],
		'model' => [
			'name' => 'model',
			'type' => 'text',
			'required' => true,
			'maxlength' => 100,
			'database' => true,
		],
		'market_value' => [
			'name' => 'market_value',
			'type' => 'text',
			'required' => true,
			'maxlength' => 100,
			'database' => true,
		],
		'submit_cancel' => [
			'name' => 'next',
			'type' => 'submit',
			'value' => 'submit_bike_details',
		]
	];

	public static $cover_type = [
		'policy_start_date' => [
			'name' => 'policy_start_date',
			'type' => 'date',
			'required' => true,
			'maxlength' => 100,
			'database' => true,
		],
		'type_of_cover' => [
			'name' => 'type_of_cover',
			'type' => 'select',
			'options' => ['Bronze', 'Silver', 'Gold'],
			'required' => true,
			'maxlength' => 100,
			'database' => true,
		],
		'submit_cancel' => [
			'name' => 'get_quote',
			'type' => 'submit',
			'value' => 'submit_cover_type',
		]
	];

	public static $retrieve_quote = [
		'quote_retrieval' => [
			'name' => 'quote_retrieval',
			'type' => 'text',
			'required' => true,
			'maxlength' => 100,
			'database' => true,
		],
		'submit_cancel' => [
			'name' => 'retrieve_quote',
			'type' => 'submit',
			'value' => 'submit_quote_retrieval',
		]
	];

	/**
	 * Redirect and exit
	 * @param string $location Where to redirect to
	 */
	public static function redirect($location) {
		header("Location: " . ROOT . $location);
		exit;
	}

	/**
	 * PDO Error Catching
	 * @param array $e error object
	 * @param string $sql query
	 * @return null
	 */
	public static function pdo_caught($e, $sql = '')  {
		if (ENV === "local") {
			$o = '<pre style="background: #A13B3B; padding: 2em; color: white; margin: 2em; font-family: Monaco, \'Courier New\', Courier, monospace; line-height: 1.8; letter-spacing: .1em; font-size: .7em; overflow: scroll;">';
			$o .= '<h2 style="margin-top: 0;">Message</h2>';
			$o .= $e->getMessage();
			$o .= '<h2>Code</h2>';
			$o .= $e->getCode();
			$o .= '<h2>Trace</h2>';
			$o .= $e->getTraceAsString();
			if (!empty($sql)) {
				$o .= '<h2>Query</h2>';
				$o .= $sql;
			}
			$o .= '</pre>';
			echo $o;
		} else {
			echo "Sorry, there is a problem with this page. Please contact us for further support.";
		}
		exit;
	}

}