<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBUtilities;

use Screenfeed\AutoWPDB\DBUtilities;

/**
 * Tests for DBUtilities::get_last_error().
 *
 * @covers DBUtilities::get_last_error
 * @group  DBUtilities
 */
class Test_GetLastError extends TestCase {

	public function testShouldReturnLastError() {
		global $wpdb;

		$wpdb->hide_errors();

		$query = "SELECT * FROM `{$this->table_name}` LIMIT 1";
		$wpdb->get_results( $query );

		$result = DBUtilities::get_last_error();

		$error = "Table '{$wpdb->dbname}.{$this->table_name}' doesn't exist";

		$this->assertSame( $error, $result );
	}
}
