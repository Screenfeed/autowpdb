<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\Table;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Integration\src\Table\TestCase;

/**
 * Tests for Table->get_last_error().
 *
 * @covers Table::get_last_error
 * @group  Table
 */
class Test_GetLastError extends TestCase {

	public function testShouldReturnLastError() {
		global $wpdb;

		$wpdb->hide_errors();

		$query = "SELECT * FROM `{$this->table_name}` LIMIT 1";
		$wpdb->get_results( $query );

		$table  = new Table( new CustomTable() );
		$result = $table->get_last_error();

		$error = "Table '{$wpdb->dbname}.{$this->table_name}' doesn't exist";

		$this->assertSame( $error, $result );
	}
}
