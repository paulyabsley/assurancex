<?php 

class Html {

	public $h;
	public $b;
	public $f;

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
		$o .= '<div class="wrapper">';
		return $o;
	}

	/**
	 * Output Foot
	 * @return string
	 */
	public function f() {
		$o = '</div>';
		$o .= '</body>';
		$o .= '</html>';
		return $o;
	}

}