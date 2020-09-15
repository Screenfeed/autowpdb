<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBWorker\Worker;

use Screenfeed\AutoWPDB\DBWorker\Worker;
use wpdb;

/**
 * Tests for Worker->copy_table().
 *
 * @covers Worker::copy_table
 * @group  Worker
 */
class Test_CopyTable extends TestCase {

	public function testShouldReturnNumberOfDeletedRows() {
		$this->createMocks( '7' );

		$result = ( new Worker() )->copy_table( $this->table_name, $this->target_table_name );

		$this->assertSame( 7, $result );
	}

	public function testShouldReturnZero() {
		$this->createMocks( false );

		$result = ( new Worker() )->copy_table( $this->table_name, $this->target_table_name );

		$this->assertSame( 0, $result );
	}

	public function createMocks( $result = 0 ) {
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
			->with( "INSERT INTO `{$this->target_table_name}` SELECT * FROM `{$this->table_name}`" )
			->willReturn( $result );
	}
}
