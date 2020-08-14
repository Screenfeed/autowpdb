<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBUtilities;

use Screenfeed\AutoWPDB\DBUtilities;
use wpdb;

/**
 * Tests for DBUtilities::table_exists().
 *
 * @covers DBUtilities::table_exists
 * @group  DBUtilities
 */
class Test_TableExists extends TestCase {

	public function testShouldReturnTrue() {
		$this->createMocks( $this->table_name );

		$result = DBUtilities::table_exists( $this->table_name );

		$this->assertTrue( $result );
	}

	public function testShouldReturnFalse() {
		$this->createMocks();

		$result = DBUtilities::table_exists( $this->table_name );

		$this->assertFalse( $result );
	}

	public function createMocks( $result = false ) {
		global $wpdb;

		$wpdb = $this->getMockBuilder( wpdb::class )
			->setMethods( [ 'esc_like', 'get_var' ] )
			->getMock();
		$wpdb
			->expects( $this->once() )
			->method( 'esc_like' )
			->with( $this->table_name )
			->willReturnArgument( 0 );
		$wpdb
			->expects( $this->once() )
			->method( 'get_var' )
			->with( "SHOW TABLES LIKE '{$this->table_name}'" )
			->willReturn( $result );
	}
}
