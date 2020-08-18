<?php
/**
 * Test Case for DBUtilities’s integration tests.
 *
 * @package Screenfeed\AutoWPDB\Tests\Integration
 */

namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBUtilities;

use Screenfeed\AutoWPDB\Tests\Integration\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
	protected $table_name        = 'wp_foobar';
	protected $target_table_name = 'wp_targettable';
	protected $drop_table        = false;
	protected $drop_target_table = false;
	public $logs;

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->logs = [];

		add_filter( 'screenfeed_autowpdb_can_log', [ $this, 'return_true' ] );
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown(): void {
		global $wpdb;

		parent::tearDown();
		$this->logs = [];

		if ( $this->drop_table ) {
			$query  = "DROP TEMPORARY TABLE IF EXISTS `{$this->table_name}`";
			$result = $wpdb->query( $query );
		}

		if ( $this->drop_target_table ) {
			$query  = "DROP TEMPORARY TABLE IF EXISTS `{$this->target_table_name}`";
			$result = $wpdb->query( $query );
		}

		remove_filter( 'screenfeed_autowpdb_can_log', [ $this, 'return_true' ] );
		remove_filter( 'screenfeed_autowpdb_can_log', [ $this, 'return_false' ] );
	}

	public function log( $message ) {
		$this->logs[] = $message;
	}
}
