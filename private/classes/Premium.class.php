<?php

class Premium {

	public $customer_quote;
	public $location;

	function __construct() {
		if (isset($_SESSION["quote_id"])) {
			$this->customer_quote = Database::find_row_by_id('quotes', $_SESSION["quote_id"]);
		}
		if (!$this->customer_quote) {
			Utilities::redirect(ROOT);
		}
		$this->location = $this->get_location();
	}

	/**
	 * Get location
	 * @return array
	 */
	private function get_location() {
		$curl = curl_init();
		$date = date('Y-m-d\TH:s:i');
		$options = array (
			CURLOPT_URL => "http://api.thingstodowithdata.com/postcode/" . urlencode($this->customer_quote["postcode"]),
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_HEADER => 0
		);
		curl_setopt_array($curl, $options);
		$curl_response = curl_exec($curl);
		$response = json_decode($curl_response, true);
		if ($response["status"] === 'match' && $response["match_type"] === 'unit_postcode') {
			$location["latitude"] = $response["data"]["latitude"];
			$location["longitude"] = $response["data"]["longitude"];
			return $location;
		}
	}

	// Use crimes at location api

	// Use lat/long to get neighbourhood id https://data.police.uk/docs/method/neighbourhood-locate/

	// User neighbourhood id to get crimes at location - https://data.police.uk/docs/method/crimes-at-location/

	// Calculate premium
	// (total number of crimes in the postcode) * (Bike Market Value / 20) * (type of cover)

}