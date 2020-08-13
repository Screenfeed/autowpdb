<?php
/**
 * Test Case for DBUtilities’s unit tests.
 *
 * @package Screenfeed\AutoWPDB\Tests\Unit
 */

namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBUtilities;

use Screenfeed\AutoWPDB\Tests\Unit\TestCase as BaseTestCase;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilities as MockDBUtilities;

abstract class TestCase extends BaseTestCase {
	protected $table_name = 'wp_foobar';
	protected $logger     = '\Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilities::local_log';

	/**
	 * Cleans up the test environment after each test.
	 */
	protected function tearDown(): void {
		parent::tearDown();
		MockDBUtilities::$mocks = [];
		MockDBUtilities::$logs  = [];
	}
}
