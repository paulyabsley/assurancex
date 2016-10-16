<?php

class Premium {

	public $customer_quote;
	public $location;
	public $market_value;
	public $type_of_cover;
	public $crime_level; // Number of street level crimes in the area
	public $price_multiplyer;
	public $quote_premium;
	public $display_premium;

	function __construct() {
		if (isset($_SESSION["quote_id"])) {
			$this->customer_quote = Database::find_row_by_id('quotes', $_SESSION["quote_id"]);
		}
		if (!$this->customer_quote) {
			Utilities::redirect(ROOT);
		}
		$this->market_value = $this->customer_quote["market_value"];
		$this->type_of_cover = $this->customer_quote["type_of_cover"];
		// Set price multiplyer
		$this->get_price_multiplyer();
		// Set location
		$this->get_location();
		// Set neighbourhood
		$this->get_neighbourhood();
		// Crime level
		$this->get_crimes_at_location();
		// Calculate premium
		$this->get_premium();
		// Output
		$this->display_premium();
	}

	/**
	 * Get price multiplyer
	 * @return null
	 */
	private function get_price_multiplyer() {
		if (isset($this->type_of_cover)) {
			switch ($this->type_of_cover) {
				case 'Bronze':
					$this->price_multiplyer = 1;
					break;
				case 'Silver':
					$this->price_multiplyer = 2;
					break;
				case 'Gold':
					$this->price_multiplyer = 3;
					break;
				default:
					break;
			}
		}
	}

	/**
	 * Get location
	 * @return null
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
			$endpoint = 'https://data.police.uk/api/locate-neighbourhood?q=';
			$endpoint .= $this->location["latitude"];
			$endpoint .= ',' . $this->location["longitude"];
			$options = [
				CURLOPT_URL => $endpoint,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_HEADER => 0,
				CURLOPT_SSL_VERIFYPEER => false
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
	 * Use https://data.police.uk/docs/method/crimes-at-location/ or https://data.police.uk/docs/method/crime-street/
	 * @return null
	 */
	private function get_crimes_at_location() {
		$curl = curl_init();
		// Crimes at location
		// $endpoint = 'https://data.police.uk/api/crimes-at-location?';
		// Street level crimes
		$endpoint = 'https://data.police.uk/api/crimes-street/all-crime?';
		$endpoint .= 'lat=' . $this->location["latitude"];
		$endpoint .= '&lng=' . $this->location["longitude"];
		// $endpoint .= '&date=' . date('Y-m'); // Polic API doesn't appear to have data for current month
		$options = [
			CURLOPT_URL => $endpoint,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_SSL_VERIFYPEER => false
		];
		curl_setopt_array($curl, $options);
		$curl_response = curl_exec($curl);
		$response = json_decode($curl_response, true);
		if ($response) {
			$i = 1;
			foreach ($response as $value) {
				if ($value["category"] == 'bicycle-theft') {
					$i++;
				}
			}
			// $this->crime_level = count($response); // Including all crimes is a bit much
			$this->crime_level = $i; // Just bicycle thefts
			// Store crime level
			$update = Database::update_row_by_id('quotes', ['crime_level' => $this->crime_level], $_SESSION["quote_id"]);
		}
		curl_close($curl);
	}

	/**
	 * Calculate premium
	 * (total number of crimes in the postcode) * (Bike Market Value / 20) * (type of cover)
	 * @return null
	 */
	private function get_premium() {
		if (isset($this->crime_level) && isset($this->market_value) && isset($this->price_multiplyer)) {
			$this->quote_premium = $this->crime_level * ($this->market_value / 20) * $this->price_multiplyer;
			// Store quoted premium
			$update = Database::update_row_by_id('quotes', ['quote_premium' => $this->quote_premium], $_SESSION["quote_id"]);
		}
	}

	/**
	 * Display premium
	 * @return null
	 */
	private function display_premium() {
		$o = '<h1>Your Quote</h1>';
		if ($this->quote_premium) {
			$o .= '<p>£' . $this->quote_premium . '</p>';
			$o .= '<p>Your quote has been saved. You can use the following code to retrieve it: ' . $this->customer_quote["quote_retrieval"] . '</p>';
			$o .= '<a href="#">Checkout with PayPal</a>';
		} else {
			$o .= '<p>We\'re sorry, there was a problem calculating your premium. Adjust your information or call us on 08000 890891.</p>';
		}
		$this->display_premium = $o;
	}

}