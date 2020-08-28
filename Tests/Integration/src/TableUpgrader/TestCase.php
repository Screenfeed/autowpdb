<?php
/**
 * Test Case for TableUpgrader’s integration tests.
 *
 * @package Screenfeed\AutoWPDB\Tests\Integration
 */

namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableUpgrader;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableUpgrader;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesIntegration;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Integration\TemporaryTableTrait;
use Screenfeed\AutoWPDB\Tests\Integration\TestCase as BaseTestCase;
use Screenfeed\AutoWPDB\Tests\LogsTrait;

abstract class TestCase extends BaseTestCase {
	use LogsTrait;
	use TemporaryTableTrait;

	protected $table_def;
	protected $table;
	protected $upgrader;

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

	/** ----------------------------------------------------------------------------------------- */
	/** TOOLS =================================================================================== */
	/** ----------------------------------------------------------------------------------------- */

	protected function init( $args = [], $table_def = [] ) {
		$this->table_def = new CustomTable();

		if ( ! empty( $table_def ) ) {
			foreach ( $table_def as $prop => $value ) {
				$method = 'set_' . $prop;
				$this->table_def->$method( $value );
			}
		}

		$this->table    = new Table( $this->table_def, DBUtilitiesIntegration::class );
		$this->upgrader = new TableUpgrader( $this->table, $args );
	}

	protected function insertVersionInDb( $version = null ) {
		if ( ! isset( $version ) ) {
			$version = $this->table_def->get_table_version();
		}

		if ( $this->table_def->is_table_global() && is_multisite() ) {
			update_site_option( $this->upgrader->get_db_version_option_name(), $version );
		} else {
			update_option( $this->upgrader->get_db_version_option_name(), $version );
		}
	}

	protected function deleteDbVersion() {
		$this->invokeMethod( 'delete_db_version', $this->upgrader );
	}

	protected function assertDbVersionIs( $version = null ) {
		if ( ! isset( $version ) ) {
			$version = $this->table_def->get_table_version();
		}

		$this->assertSame( $version, $this->upgrader->get_db_version() );
	}

	protected function assertTableIsReady() {
		global $wpdb;

		$table_short_name = $this->table_def->get_table_short_name();
		$table_name       = $this->table_def->get_table_name();

		$this->assertTrue( $this->getPropertyValue( 'table_ready', $this->upgrader ) );
		$this->assertObjectHasAttribute( $table_short_name, $wpdb );
		$this->assertSame( $wpdb->$table_short_name, $table_name );

		if ( $this->table_def->is_table_global() ) {
			$this->assertContains( $table_short_name, $wpdb->global_tables );
		} else {
			$this->assertContains( $table_short_name, $wpdb->tables );
		}
	}

	protected function assertTableIsNotReady() {
		global $wpdb;

		$table_short_name = $this->table_def->get_table_short_name();

		$this->assertFalse( $this->getPropertyValue( 'table_ready', $this->upgrader ) );
		$this->assertObjectNotHasAttribute( $table_short_name, $wpdb );

		if ( $this->table_def->is_table_global() ) {
			$this->assertNotContains( $table_short_name, $wpdb->global_tables );
		} else {
			$this->assertNotContains( $table_short_name, $wpdb->tables );
		}
	}

	protected function resetTableReady() {
		global $wpdb;

		$table_short_name = $this->table_def->get_table_short_name();

		unset( $wpdb->$table_short_name );
		$wpdb->global_tables = array_diff( $wpdb->global_tables, [ $table_short_name ] );
		$wpdb->tables        = array_diff( $wpdb->tables, [ $table_short_name ] );
	}
}
