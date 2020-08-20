<?php
/**
 * Test Case for Table’s integration tests.
 *
 * @package Screenfeed\AutoWPDB\Tests\Integration
 */

namespace Screenfeed\AutoWPDB\Tests\Integration\src\Table;

use Screenfeed\AutoWPDB\Tests\Integration\LogsTrait;
use Screenfeed\AutoWPDB\Tests\Integration\TemporaryTableTrait;
use Screenfeed\AutoWPDB\Tests\Integration\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
	use LogsTrait;
	use TemporaryTableTrait;

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->empty_logs();
		$this->init_temporary_tables();

		add_filter( 'screenfeed_autowpdb_can_log', [ $this, 'return_true' ] );
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown(): void {
		parent::tearDown();

		$this->empty_logs();
		$this->maybe_drop_temporary_tables();

		remove_filter( 'screenfeed_autowpdb_can_log', [ $this, 'return_true' ] );
		remove_filter( 'screenfeed_autowpdb_can_log', [ $this, 'return_false' ] );
	}
}
