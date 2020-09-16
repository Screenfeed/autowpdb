<?php

namespace Screenfeed\AutoWPDB\Tests;

trait LogsTrait {
	private $logs = [];

	public function empty_logs() {
		$this->logs = [];
	}

	public function get_logs() {
		return $this->logs;
	}

	public function log( $message ) {
		$this->logs[] = $message;
	}
}
