<?php 

class Session {

	public $message;

	function __construct() {
		session_start();
		$this->check_message();
	}

	public function message($msg = '') {
		if (!empty($msg)) {
			// Set message
			$_SESSION['message'] = $msg;
		} else {
			// Get message
			return $this->message;
		}
	}

	private function check_message() {
		if (isset($_SESSION['message'])) {
			// Add it as an attirbute and erase the stored version
			$this->message = $_SESSION['message'];
			unset($_SESSION['message']);
		} else {
			$this->message = '';
		}
	}

}