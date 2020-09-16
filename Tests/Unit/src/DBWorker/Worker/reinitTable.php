<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBWorker\Worker;

use Screenfeed\AutoWPDB\DBWorker\Worker;
use wpdb;

/**
 * Tests for Worker->reinit_table().
 *
 * @covers Worker::reinit_table
 * @group  Worker
 */
class Test_ReinitTable extends TestCase {

	public function testShouldReturnTrueOnSuccess() {
		$this->createMocks( true );

		$result = ( new Worker() )->reinit_table( $this->table_name );

		$this->assertTrue( $result );
	}

	public function testShouldReturnFalseOnFailure() {
		$this->createMocks( false );

		$result = ( new Worker() )->reinit_table( $this->table_name );

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
