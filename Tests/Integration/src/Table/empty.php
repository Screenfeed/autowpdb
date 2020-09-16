<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\Table;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Integration\src\Table\TestCase;

/**
 * Tests for Table->empty().
 *
 * @covers Table::empty
 * @group  Table
 */
class Test_Empty extends TestCase {
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

		$table  = new Table( new CustomTable() );
		$result = $table->empty();

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

		$table  = new Table( new CustomTable() );
		$result = $table->empty();

		$this->assertSame( $result, 0 );
	}
}
