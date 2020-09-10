<?php
/**
 * Test Case for Basic’s integration tests.
 *
 * @package Screenfeed\AutoWPDB\Tests\Integration
 */

namespace Screenfeed\AutoWPDB\Tests\Integration\src\CRUD\Basic;

use Screenfeed\AutoWPDB\CRUD\Basic;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Integration\TemporaryTableTrait;
use Screenfeed\AutoWPDB\Tests\Integration\TestCase as BaseTestCase;
use Screenfeed\AutoWPDB\Tests\LogsTrait;

abstract class TestCase extends BaseTestCase {
	use LogsTrait;
	use TemporaryTableTrait;

	protected $tableDefinition;
	protected $tableCrud;

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

	protected function init_temporary_tables() {
		$this->table_definition  = new CustomTable();
		$this->table_crud        = new Basic( $this->table_definition );
		$this->table_name        = $this->table_definition->get_table_name();
		$this->drop_target_table = false;
	}

	protected function create_table( $table_name = '' ) {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$schema          = $this->table_definition->get_table_schema();

		$wpdb->query( "CREATE TEMPORARY TABLE `{$this->table_name}` ($schema) $charset_collate" );
	}

	protected function add_row( $data, $table_name = '' ) {
		global $wpdb;

		$placeholders = array_intersect_key( $this->table_definition->get_column_placeholders(), $data );

		$wpdb->insert(
			$this->table_name,
			$data,
			$placeholders
		);

		return (int) $wpdb->insert_id;
	}

	protected function get_rows( $table_name = '' ) {
		global $wpdb;

		return $wpdb->get_results(
			"SELECT * FROM `{$this->table_name}` ORDER BY `file_id` ASC",
			ARRAY_A
		);
	}

	protected function get_row( $file_id, $table_name = '' ) {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM `{$this->table_name}` WHERE `file_id` = %d", $file_id ),
			ARRAY_A
		);
	}

	protected function get_last_row( $table_name = '' ) {
		global $wpdb;

		return $wpdb->get_row(
			"SELECT * FROM `{$this->table_name}` ORDER BY `file_id` DESC LIMIT 1;",
			ARRAY_A
		);
	}
}
