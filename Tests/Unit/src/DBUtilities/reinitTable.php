<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBUtilities;

use Screenfeed\AutoWPDB\DBUtilities;
use wpdb;

/**
 * Tests for DBUtilities::reinit_table().
 *
 * @covers DBUtilities::reinit_table
 * @group  DBUtilities
 */
class Test_ReinitTable extends TestCase {

	public function testShouldReturnTrueOnSuccess() {
		$this->createMocks( true );

		$result = DBUtilities::reinit_table( $this->table_name );

		$this->assertTrue( $result );
	}

	public function testShouldReturnFalseOnFailure() {
		$this->createMocks( false );

		$result = DBUtilities::reinit_table( $this->table_name );

		$this->assertFalse( $result );
	}

	public function createMocks( $result = true ) {
		global $wpdb;

		$wpdb = $this->getMockBuilder( wpdb::class )
			->setMethods( [ 'hide_errors', 'query' ] )
			->getMock();
		$wpdb
			->expects( $this->once() )
			->method( 'hide_errors' );
		$wpdb
			->expects( $this->once() )
			->method( 'query' )
			->with( "TRUNCATE TABLE `{$this->table_name}`" )
			->willReturn( $result );
	}
}
