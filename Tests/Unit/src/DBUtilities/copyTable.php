<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBUtilities;

use Screenfeed\AutoWPDB\DBUtilities;
use wpdb;

/**
 * Tests for DBUtilities::copy_table().
 *
 * @covers DBUtilities::copy_table
 * @group  DBUtilities
 */
class Test_CopyTable extends TestCase {
	protected $target_table_name = 'wp_targettable';

	public function testShouldReturnNumberOfDeletedRows() {
		$this->createMocks( '7' );

		$result = DBUtilities::copy_table( $this->table_name, $this->target_table_name );

		$this->assertEquals( 7, $result );
	}

	public function testShouldReturnZero() {
		$this->createMocks( false );

		$result = DBUtilities::copy_table( $this->table_name, $this->target_table_name );

		$this->assertEquals( 0, $result );
	}

	public function createMocks( $result = 0 ) {
		global $wpdb;

		$wpdb = $this->getMockBuilder( wpdb::class )
			->setMethods( [ 'query' ] )
			->getMock();
		$wpdb
			->expects( $this->once() )
			->method( 'query' )
			->with( "INSERT INTO `{$this->target_table_name}` SELECT * FROM `{$this->table_name}`" )
			->willReturn( $result );
	}
}
