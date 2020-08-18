<?php
/**
 * Test Case for DBUtilities’s unit tests.
 *
 * @package Screenfeed\AutoWPDB\Tests\Unit
 */

namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBUtilities;

use Screenfeed\AutoWPDB\Tests\Unit\TestCase as BaseTestCase;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesUnit;

abstract class TestCase extends BaseTestCase {
	protected $table_name        = 'wp_foobar';
	protected $target_table_name = 'wp_targettable';
	public $logs;

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->logs = [];
		DBUtilitiesUnit::$mocks = [];
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown(): void {
		parent::tearDown();
		$this->logs = [];
		DBUtilitiesUnit::$mocks = [];
	}

	public function log( $message ) {
		$this->logs[] = $message;
	}
}
