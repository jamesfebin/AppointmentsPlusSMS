<?php

class App_Period {

	private $_start;
	private $_end;

	public function __construct ($start, $end) {
		$this->_start = is_numeric($start) ? $start : strtotime($start);
		$this->_end = is_numeric($end) ? $end : strtotime($end);
	}

	public function contains ($start, $end) {
		$start = is_numeric($start) ? $start : strtotime($start);
		$end = is_numeric($end) ? $end : strtotime($end);
		return defined('APP_USE_LEGACY_BOUNDARIES_CALCULUS') && APP_USE_LEGACY_BOUNDARIES_CALCULUS
			? $this->_contains_exact($start, $end)
			: $this->_contains_boundaries($start, $end)
		;
	}

	public function get_start () { return $this->_start; }
	public function get_end () { return $this->_end; }

	// doesn't handle enclosed appointments
	protected function _contains_exact ($start, $end) {
		return (
			$this->_start >= $start 
			&& 
			$this->_end <= $end
		);
	}
	// detects appointments overlap
	protected function _contains_boundaries ($start, $end) {
		return (
			$end > $this->_start
			&&
			$start < $this->_end
		);
	}


}