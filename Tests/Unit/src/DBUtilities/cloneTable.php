<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBUtilities;

use Screenfeed\AutoWPDB\DBUtilities;
use wpdb;

/**
 * Tests for DBUtilities::clone_table().
 *
 * @covers DBUtilities::clone_table
 * @group  DBUtilities
 */
class Test_CloneTable extends TestCase {
	protected $target_table_name = 'wp_targettable';

	public function testShouldReturnTrueOnSuccess() {
		$this->createMocks( true );

		$result = DBUtilities::clone_table( $this->table_name, $this->target_table_name );

		$this->assertTrue( $result );
	}

	public function testShouldReturnFalseOnFailure() {
		$this->createMocks( false );

		$result = DBUtilities::clone_table( $this->table_name, $this->target_table_name );

		$this->assertFalse( $result );
	}

	public function createMocks( $result = true ) {
		global $wpdb;

		$wpdb = $this->getMockBuilder( wpdb::class )
			->setMethods( [ 'query' ] )
			->getMock();
		$wpdb
			->expects( $this->once() )
			->method( 'query' )
			->with( "CREATE TABLE `{$this->target_table_name}` LIKE `{$this->table_name}`" )
			->willReturn( $result );
	}
}