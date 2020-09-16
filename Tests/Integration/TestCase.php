<?php

namespace Screenfeed\AutoWPDB\Tests\Integration;

use Brain\Monkey;
use Screenfeed\AutoWPDB\Tests\TestCaseTrait;
use WP_UnitTestCase;

abstract class TestCase extends WP_UnitTestCase {
	use TestCaseTrait;

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function return_true() {
		return true;
	}

	public function return_false() {
		return false;
	}
}
