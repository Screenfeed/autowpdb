<?php
/**
 * Test Case for Worker's unit tests.
 *
 * @package Screenfeed\AutoWPDB\Tests\Unit
 */

namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBWorker\Worker;

use Screenfeed\AutoWPDB\DBWorker\Worker;
use Screenfeed\AutoWPDB\Tests\LogsTrait;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
	use LogsTrait;

	protected $table_name        = 'wp_foobar';
	protected $target_table_name = 'wp_targettable';

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->empty_logs();
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown(): void {
		parent::tearDown();
		$this->empty_logs();
	}
}
