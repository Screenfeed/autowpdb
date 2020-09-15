<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBWorker\Worker;

use Screenfeed\AutoWPDB\DBWorker\Worker;
use wpdb;

/**
 * Tests for Worker->clone_table().
 *
 * @covers Worker::clone_table
 * @group  Worker
 */
class Test_CloneTable extends TestCase {

	public function testShouldReturnTrueOnSuccess() {
		$this->createMocks( true );

		$result = ( new Worker() )->clone_table( $this->table_name, $this->target_table_name );

		$this->assertTrue( $result );
	}

	public function testShouldReturnFalseOnFailure() {
		$this->createMocks( false );

		$result = ( new Worker() )->clone_table( $this->table_name, $this->target_table_name );

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
			->with( "CREATE TABLE `{$this->target_table_name}` LIKE `{$this->table_name}`" )
			->willReturn( $result );
	}
}
