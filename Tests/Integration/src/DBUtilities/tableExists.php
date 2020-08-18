<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBUtilities;

use Screenfeed\AutoWPDB\DBUtilities;

/**
 * Tests for DBUtilities::table_exists().
 *
 * @covers DBUtilities::table_exists
 * @group  DBUtilities
 */
class Test_TableExists extends TestCase {

	public function testShouldReturnTrueWhenTableExists() {
		global $wpdb;

		$result = DBUtilities::table_exists( $wpdb->posts );

		$this->assertTrue( $result );
	}

	public function testShouldReturnFalseWhenTableDoesNotExist() {

		$result = DBUtilities::table_exists( $this->table_name );

		$this->assertFalse( $result );
	}
}
