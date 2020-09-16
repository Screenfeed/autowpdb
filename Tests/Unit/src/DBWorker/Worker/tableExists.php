<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBWorker\Worker;

use Screenfeed\AutoWPDB\DBWorker\Worker;
use wpdb;

/**
 * Tests for Worker->table_exists().
 *
 * @covers Worker::table_exists
 * @group  Worker
 */
class Test_TableExists extends TestCase {

	public function testShouldReturnTrue() {
		$this->createMocks( $this->table_name );

		$result = ( new Worker() )->table_exists( $this->table_name );

		$this->assertTrue( $result );
	}

	public function testShouldReturnFalse() {
		$this->createMocks();

		$result = ( new Worker() )->table_exists( $this->table_name );

		$this->assertFalse( $result );
	}

	public function createMocks( $result = false ) {
		global $wpdb;

		$wpdb = $this->getMockBuilder( wpdb::class )
			->setMethods( [ 'hide_errors', 'esc_like', 'get_var' ] )
			->getMock();
		$wpdb
			->expects( $this->once() )
			->method( 'hide_errors' );
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
