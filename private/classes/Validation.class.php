<?php

class Validation {

	/**
	 * Formats field names for error messages
	 * @param string $fieldname The name of the field
	 * @reutrn string The formatted fieldname with underscores removed
	 */
	private static function fieldname_as_text($fieldname) {
		$fieldname = str_replace("_", " ", $fieldname);
		$fieldname = ucwords($fieldname);
		return $fieldname;
	}

	/**
	 * Check for presence
	 * @param mixed $value The value being checked
	 * @return bool True or False
	 */
	private static function has_presence($value) {
		// use === to avoid false positives
		// empty() would consider "0" to be empty
		$value = trim($value);
		return isset($value) && $value !== "";
	}

	/**
	 * Validate values for presence
	 * @param string $name Field name
	 * @param string $value Field value
	 */
	public static function validate_required($name, $value) {
		global $errors;
		$value = trim($value);
		if (!static::has_presence($value)) {
			$errors[$name] = static::fieldname_as_text($name) . " is required";
		}
	}

}