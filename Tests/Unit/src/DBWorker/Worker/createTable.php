<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBWorker\Worker;

use Brain\Monkey\Functions;
use Screenfeed\AutoWPDB\DBWorker\Worker;
use wpdb;

/**
 * Tests for Worker->create_table().
 *
 * @covers Worker::create_table
 * @group  Worker
 */
class Test_CreateTable extends TestCase {
	protected $schema_query    = 'schema_query';
	protected $charset_collate = 'charset_collate';

	public function testShouldReturnTrue() {
		$this->createMocks();

		$worker = $this->getMockBuilder( Worker::class )
			->setMethods( [ 'table_exists', 'can_log' ] )
			->getMock();
		$worker
			->expects( $this->once() )
			->method( 'table_exists' )
			->with()
			->willReturn( true );
		$worker
			->expects( $this->never() )
			->method( 'can_log' );

		$result = $worker->create_table(
			$this->table_name,
			$this->schema_query,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertTrue( $result );
		$this->assertCount( 0, $this->get_logs() );
	}

	public function testShouldReturnFalseWhenDBError() {
		$db_error = 'You messed it up!';

		$this->createMocks( $db_error );

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

		$result = $worker->create_table(
			$this->table_name,
			$this->schema_query,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$error = sprintf( 'Error while creating the DB table %s: %s', $this->table_name, $db_error );

		$this->assertFalse( $result );
		$this->assertContains( $error, $this->get_logs() );
		$this->assertCount( 1, $this->get_logs() );
	}

	public function testShouldReturnFalseWhenTableError() {
		$this->createMocks();

		$worker = $this->getMockBuilder( Worker::class )
			->setMethods( [ 'table_exists', 'can_log' ] )
			->getMock();
		$worker
			->expects( $this->once() )
			->method( 'table_exists' )
			->with()
			->willReturn( false );
		$worker
			->expects( $this->once() )
			->method( 'can_log' )
			->with()
			->willReturn( true );

		$result = $worker->create_table(
			$this->table_name,
			$this->schema_query,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$error = sprintf( 'Creation of the DB table %s failed.', $this->table_name );

		$this->assertFalse( $result );
		$this->assertContains( $error, $this->get_logs() );
		$this->assertCount( 1, $this->get_logs() );
	}

	public function testShouldFailWithoutLogging() {
		$db_error = 'You messed it up!';

		$this->createMocks( $db_error );

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

		$result = $worker->create_table(
			$this->table_name,
			$this->schema_query,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertFalse( $result );
		$this->assertCount( 0, $this->get_logs() );
	}

	public function createMocks( $db_error = '' ) {
		global $wpdb;

		$wpdb = $this->getMockBuilder( wpdb::class )
			->setMethods( [ 'hide_errors', 'get_charset_collate' ] )
			->getMock();
		$wpdb
			->expects( $this->once() )
			->method( 'hide_errors' );
		$wpdb
			->expects( $this->once() )
			->method( 'get_charset_collate' )
			->with()
			->willReturn( $this->charset_collate );

		if ( ! empty( $db_error ) ) {
			$wpdb->last_error = $db_error;
		}

		Functions\expect( 'dbDelta' )
			->once()
			->with( "CREATE TABLE `{$this->table_name}` ({$this->schema_query}) {$this->charset_collate};" );
	}
}
