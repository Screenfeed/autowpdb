<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBWorker\Worker;

use Screenfeed\AutoWPDB\DBWorker\Worker;

/**
 * Tests for Worker->get_last_error().
 *
 * @covers Worker::get_last_error
 * @group  Worker
 */
class Test_GetLastError extends TestCase {

	public function testShouldReturnLastError() {
		global $wpdb;

		$wpdb->hide_errors();

		$query = "SELECT * FROM `{$this->table_name}` LIMIT 1";
		$wpdb->get_results( $query );

		$result = ( new Worker() )->get_last_error();

		$error = "Table '{$wpdb->dbname}.{$this->table_name}' doesn't exist";

		$this->assertSame( $error, $result );
	}
}
