<?php

class Premium {

	public $quote;

	function __construct() {
		if (isset($_SESSION["quote_id"])) {
			$this->quote = Database::find_row_by_id('quotes', $_SESSION["quote_id"]);
		}
	}

	// Use crimes at location api

	// Take postcode and work out latitude and longitude

	// Use lat/long to get neighbourhood id https://data.police.uk/docs/method/neighbourhood-locate/

	// User neighbourhood id to get crimes at location - https://data.police.uk/docs/method/crimes-at-location/

	// Calculate premium
	// (total number of crimes in the postcode) * (Bike Market Value / 20) * (type of cover)

}