<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBWorker\Worker;

use Screenfeed\AutoWPDB\DBWorker\Worker;

/**
 * Tests for Worker->empty_table().
 *
 * @covers Worker::empty_table
 * @group  Worker
 */
class Test_EmptyTable extends TestCase {
	protected $drop_table = true;

	public function testShouldEmptyTable() {
		$this->create_table();

		// Insert a row => id 1.
		$this->add_row( 'foobar' );
		$row = $this->get_last_row();
		$this->assertSame(
			[
				'id'   => '1',
				'data' => 'foobar',
			],
			$row
		);

		// Insert a row => id 2.
		$this->add_row( 'barbaz' );
		$row = $this->get_last_row();
		$this->assertSame(
			[
				'id'   => '2',
				'data' => 'barbaz',
			],
			$row
		);

		$result = ( new Worker() )->empty_table( $this->table_name );

		// Check that the method returns the number of deleted rows.
		$this->assertSame( $result, 2 );

		// Check that the table is empty.
		$row = $this->get_last_row();
		$this->assertNull( $row );

		// Insert a row and make sure its id is 3 (auto increment is NOT reset).
		$this->add_row( 'thelast' );
		$row = $this->get_last_row();
		$this->assertSame(
			[
				'id'   => '3',
				'data' => 'thelast',
			],
			$row
		);
	}

	public function testShouldNotEmptyTableWhenItDoesNotExist() {

		$result = ( new Worker() )->empty_table( $this->table_name );

		$this->assertSame( $result, 0 );
	}
}
