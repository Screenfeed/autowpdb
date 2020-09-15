<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBWorker\Worker;

use Screenfeed\AutoWPDB\DBWorker\Worker;

/**
 * Tests for Worker->table_exists().
 *
 * @covers Worker::table_exists
 * @group  Worker
 */
class Test_TableExists extends TestCase {

	public function testShouldReturnTrueWhenTableExists() {
		global $wpdb;

		$result = ( new Worker() )->table_exists( $wpdb->posts );

		$this->assertTrue( $result );
	}

	public function testShouldReturnFalseWhenTableDoesNotExist() {

		$result = ( new Worker() )->table_exists( $this->table_name );

		$this->assertFalse( $result );
	}
}
