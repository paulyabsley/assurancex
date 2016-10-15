<?php

class Premium {

	public $customer_quote;
	public $location;
	public $neighbourhood;

	function __construct() {
		if (isset($_SESSION["quote_id"])) {
			$this->customer_quote = Database::find_row_by_id('quotes', $_SESSION["quote_id"]);
		}
		if (!$this->customer_quote) {
			Utilities::redirect(ROOT);
		}
		// Set location
		$this->get_location();
		// Set neighbourhood
		$this->get_neighbourhood();
		// Crime level
		$this->get_crimes_at_location();
	}

	/**
	 * Get location
	 * @return array
	 */
	private function get_location() {
		if (empty($this->customer_quote["latitude"]) && empty($this->customer_quote["longitude"])) {
			$curl = curl_init();
			$options = [
				CURLOPT_URL => 'http://api.thingstodowithdata.com/postcode/' . urlencode($this->customer_quote["postcode"]),
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_HEADER => 0
			];
			curl_setopt_array($curl, $options);
			$curl_response = curl_exec($curl);
			var_dump($curl_response);
			$response = json_decode($curl_response, true);
			if ($response["status"] === 'match' && $response["match_type"] === 'unit_postcode') {
				$location["latitude"] = $response["data"]["latitude"];
				$location["longitude"] = $response["data"]["longitude"];
				// Store location
				$update = Database::update_row_by_id('quotes', $location, $_SESSION["quote_id"]);
				// Set location
				$this->location["latitude"] = $location["latitude"];
				$this->location["longitude"] = $location["longitude"];
			}
			curl_close($curl);
		} else {
			$this->location["latitude"] = $this->customer_quote["latitude"];
			$this->location["longitude"] = $this->customer_quote["longitude"];
		}
	}

	/**
	 * Get neighbourhood
	 * Use lat/long to get neighbourhood id https://data.police.uk/docs/method/neighbourhood-locate/
	 * @return null
	 */
	private function get_neighbourhood() {
		if (empty($this->customer_quote["neighbourhood"]) && !empty($this->location)) {
			$curl = curl_init();
			$endpoint = 'http://data.police.uk/api/locate-neighbourhood?q=';
			$endpoint .= $this->location["latitude"];
			$endpoint .= ',' . $this->location["longitude"];
			$options = [
				CURLOPT_URL => $endpoint,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_HEADER => 0
			];
			curl_setopt_array($curl, $options);
			$curl_response = curl_exec($curl);
			$response = json_decode($curl_response, true);
			if ($curl_response && isset($response["neighbourhood"])) {
				// Set Neighbourhood
				$this->neighbourhood = $response["neighbourhood"];
				// Store Neighbourhood
				$update = Database::update_row_by_id('quotes', ['neighbourhood' => $this->neighbourhood], $_SESSION["quote_id"]);
			}
			curl_close($curl);
		} else {
			$this->neighbourhood = $this->customer_quote["neighbourhood"];
		}
	}

	/**
	 * Get crimes at location
	 * User neighbourhood id to get crimes at location - https://data.police.uk/docs/method/crimes-at-location/
	 * @return null
	 */
	private function get_crimes_at_location() {
		// if (empty($this->customer_quote["neighbourhood"]) && !empty($this->location)) {
			$curl = curl_init();
			$endpoint = 'http://data.police.uk/api/crimes-at-location?';
			$endpoint .= 'lat=' . $this->location["latitude"];
			$endpoint .= '&lng=' . $this->location["longitude"];
			$options = [
				CURLOPT_URL => $endpoint,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_HEADER => 0
			];
			curl_setopt_array($curl, $options);
			$curl_response = curl_exec($curl);
			var_dump($curl_response);
			$response = json_decode($curl_response, true);
			// if ($curl_response && isset($response["neighbourhood"])) {
			// 	// Set Neighbourhood
			// 	$this->neighbourhood = $response["neighbourhood"];
			// 	// Store Neighbourhood
			// 	$update = Database::update_row_by_id('quotes', ['neighbourhood' => $this->neighbourhood], $_SESSION["quote_id"]);
			// }
			curl_close($curl);
		// } else {
			// $this->neighbourhood = $this->customer_quote["neighbourhood"];
		// }
	}

	// Calculate premium
	// (total number of crimes in the postcode) * (Bike Market Value / 20) * (type of cover)

}