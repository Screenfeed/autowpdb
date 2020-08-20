<?php
/**
 * Test Case for Table’s integration tests.
 *
 * @package Screenfeed\AutoWPDB\Tests\Integration
 */

namespace Screenfeed\AutoWPDB\Tests\Integration\src\Table;

use Screenfeed\AutoWPDB\Tests\Integration\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
	protected $table_name        = 'foobar';
	protected $target_table_name = 'targettable';
	protected $drop_table        = false;
	protected $drop_target_table = false;
	public $logs;

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp(): void {
		global $wpdb;

		parent::setUp();
		$this->logs              = [];
		$this->table_name        = $wpdb->prefix . $this->table_name;
		$this->target_table_name = $wpdb->prefix . $this->target_table_name;

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
