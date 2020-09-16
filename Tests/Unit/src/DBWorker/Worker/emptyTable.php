<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBWorker\Worker;

use Screenfeed\AutoWPDB\DBWorker\Worker;
use wpdb;

/**
 * Tests forWorker->empty_table().
 *
 * @covers Worker::empty_table
 * @group  Worker
 */
class Test_EmptyTable extends TestCase {

	public function testShouldReturnNumberOfDeletedRows() {
		$this->createMocks( '7' );

		$result = ( new Worker() )->empty_table( $this->table_name );

		$this->assertSame( 7, $result );
	}

	public function testShouldReturnZero() {
		$this->createMocks( false );

		$result = ( new Worker() )->empty_table( $this->table_name );

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
			->with( "DELETE FROM `{$this->table_name}`" )
			->willReturn( $result );
	}
}
