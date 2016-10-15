<?php 

class Html {

	public $h; // head
	public $b; // body
	public $f; // foot

	function __construct($t) {
		$this->h = $this->h($t);
		$this->b = $this->b();
		$this->f = $this->f();
	}

	/**
	 * Output Head
	 * @param string $t
	 * @return string
	 */
	public function h($t) {
		$o = '<!DOCTYPE html>';
		$o .= '<html>';
		$o .= '<head>';
		$o .= '<meta charset="UTF-8">';
		$o .= '<meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">';
		$o .= '<title>' . $t . '</title>';
		$o .= '<link rel="stylesheet" href="/css/style.css">';
		return $o;
	}

	/**
	 * Output Body
	 * @return string
	 */
	public function b() {
		$o = '</head>';
		$o .= '<body>';
		return $o;
	}

	/**
	 * Output Foot
	 * @return string
	 */
	public function f() {
		$o = '</body>';
		$o .= '</html>';
		return $o;
	}

	/**
	 * Output errors
	 * @param array $errors Associative array of errors
	 * @return string Formatted error panel or empty string
	 */
	public static function display_errors($errors = array(), $display_type) {
		$output = '';
		if (!empty($errors)) {
			if ($display_type == "alert") {
				$output .= '<div data-alert class="alert-box alert radius">';
				foreach ($errors as $key => $error) {
					$output .=  h($error) . '</br>';
				}
			} else {
				$output .= '<div class="panel">';
				$output .= '<h5>Correct the following errors:</h5>';
				$output .= '<ul>';
				foreach ($errors as $key => $error) {
					$output .= '<li>' . h($error) . '</li>';
				}
				$output .= '</ul>';
			}
			$output .= '</div>';
		}
		return $output;
	}

}