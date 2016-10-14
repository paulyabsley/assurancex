<?php

class Utilities {

	public static $user_details = [
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
			'type' => 'text',
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
			'value' => 'page',
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
			'value' => 'page',
		]
	];

	public static $cover_type = [
		'policy_start_date' => [
			'name' => 'policy_start_date',
			'type' => 'text',
			'required' => true,
			'maxlength' => 100,
			'database' => true,
		],
		'type_of_cover' => [
			'name' => 'type_of_cover',
			'type' => 'radio',
			'required' => true,
			'maxlength' => 100,
			'database' => true,
		],
		'submit_cancel' => [
			'name' => 'get_quote',
			'type' => 'submit',
			'value' => 'page',
		]
	];

	/**
	 * Redirect and exit
	 * @param string $location Where to redirect to
	 */
	public static function redirect($location) {
		header("Location: $location");
		exit;
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

}