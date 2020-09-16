<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBWorker\Worker;

use Screenfeed\AutoWPDB\DBWorker\Worker;
use wpdb;

/**
 * Tests for Worker->delete_table().
 *
 * @covers Worker::delete_table
 * @group  Worker
 */
class Test_DeleteTable extends TestCase {

	public function testShouldReturnTrue() {
		$this->createMocks( true );

		$worker = $this->getMockBuilder( Worker::class )
			->setMethods( [ 'table_exists', 'can_log' ] )
			->getMock();
		$worker
			->expects( $this->once() )
			->method( 'table_exists' )
			->with()
			->willReturn( false );
		$worker
			->expects( $this->never() )
			->method( 'can_log' );

		$result = $worker->delete_table(
			$this->table_name,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertTrue( $result );
		$this->assertCount( 0, $this->get_logs() );
	}

	public function testShouldReturnFalseWhenQueryFails() {
		$this->createMocks( false );

		$worker = $this->getMockBuilder( Worker::class )
			->setMethods( [ 'table_exists', 'can_log' ] )
			->getMock();
		$worker
			->expects( $this->never() )
			->method( 'table_exists' );
		$worker
			->expects( $this->once() )
			->method( 'can_log' )
			->with()
			->willReturn( true );

		$result = $worker->delete_table(
			$this->table_name,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$error = sprintf( 'Deletion of the DB table %s failed.', $this->table_name );

		$this->assertFalse( $result );
		$this->assertContains( $error, $this->get_logs() );
		$this->assertCount( 1, $this->get_logs() );
	}

	public function testShouldReturnFalseWhenTableError() {
		$this->createMocks( true );

		$worker = $this->getMockBuilder( Worker::class )
			->setMethods( [ 'table_exists', 'can_log' ] )
			->getMock();
		$worker
			->expects( $this->once() )
			->method( 'table_exists' )
			->with()
			->willReturn( true );
		$worker
			->expects( $this->once() )
			->method( 'can_log' )
			->with()
			->willReturn( true );

		$result = $worker->delete_table(
			$this->table_name,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$error = sprintf( 'Deletion of the DB table %s failed.', $this->table_name );

		$this->assertFalse( $result );
		$this->assertContains( $error, $this->get_logs() );
		$this->assertCount( 1, $this->get_logs() );
	}

	public function testShouldFailWithoutLogging() {
		$this->createMocks( false );

		$worker = $this->getMockBuilder( Worker::class )
			->setMethods( [ 'table_exists', 'can_log' ] )
			->getMock();
		$worker
			->expects( $this->never() )
			->method( 'table_exists' );
		$worker
			->expects( $this->once() )
			->method( 'can_log' )
			->with()
			->willReturn( false );

		$result = $worker->delete_table(
			$this->table_name,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertFalse( $result );
		$this->assertCount( 0, $this->get_logs() );
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
			->with( "DROP TABLE `{$this->table_name}`" )
			->willReturn( $result );
	}
}
