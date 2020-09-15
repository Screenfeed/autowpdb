<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBWorker\Worker;

use Brain\Monkey\Functions;
use Screenfeed\AutoWPDB\DBWorker\Worker;
use wpdb;

/**
 * Tests for Worker->count_table_rows().
 *
 * @covers Worker::count_table_rows
 * @group  Worker
 */
class Test_CountTableRows extends TestCase {

	public function testShouldReturnNumberOfRows() {
		$worker = new Worker();

		$this->createMocks( 3 );

		$result = $worker->count_table_rows( $this->table_name, '*' );

		$this->assertSame( 3, $result );

		$result = $worker->count_table_rows( $this->table_name, 'distinct  *' );

		$this->assertSame( 3, $result );

		$result = $worker->count_table_rows( $this->table_name, ' foo' );

		$this->assertSame( 3, $result );

		$result = $worker->count_table_rows( $this->table_name, '"foo" ' );

		$this->assertSame( 3, $result );

		$result = $worker->count_table_rows( $this->table_name, " 'foo'" );

		$this->assertSame( 3, $result );

		$result = $worker->count_table_rows( $this->table_name, '`foo` ' );

		$this->assertSame( 3, $result );

		$result = $worker->count_table_rows( $this->table_name, ' distinct  "foo" ' );

		$this->assertSame( 3, $result );
	}

	public function testShouldReturnZero() {
		$this->createMocks();

		$result = ( new Worker() )->count_table_rows( $this->table_name, '*' );

		$this->assertSame( 0, $result );
	}

	public function createMocks( $result = 0 ) {
		global $wpdb;

		$wpdb = $this->getMockBuilder( wpdb::class )
			->setMethods( [ 'hide_errors', 'get_var' ] )
			->getMock();
		$wpdb
			->expects( $this->any() )
			->method( 'hide_errors' );
		$wpdb
			->expects( $this->any() )
			->method( 'get_var' )
			->will(
				$this->returnCallback(
					function ( $query ) use ( $result ) {
						switch ( $query ) {
							case "SELECT COUNT(*) FROM `{$this->table_name}`":
							case "SELECT COUNT(DISTINCT *) FROM `{$this->table_name}`":
							case "SELECT COUNT(`foo`) FROM `{$this->table_name}`":
							case "SELECT COUNT(DISTINCT `foo`) FROM `{$this->table_name}`":
								return $result;
							default:
								return -1;
						};
					}
				)
			);

		Functions\expect( 'esc_sql' )
			->andReturnUsing( 'addslashes' );
	}
}
