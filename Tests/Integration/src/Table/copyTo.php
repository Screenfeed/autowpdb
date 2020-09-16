<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\Table;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Integration\src\Table\TestCase;

/**
 * Tests for Table->copy_to().
 *
 * @covers Table::copy_to
 * @group  Table
 */
class Test_CopyTo extends TestCase {
	protected $drop_table        = true;
	protected $drop_target_table = true;

	public function testShouldCloneTable() {
		$this->create_table();
		$row1_id = $this->add_row( 'foobar' );
		$row2_id = $this->add_row( 'barbaz' );
		$this->create_table( $this->target_table_name );

		$table  = new Table( new CustomTable() );
		$result = $table->copy_to( $this->target_table_name );

		// Two entries copied.
		$this->assertSame( 2, $result );

		$rows     = $this->get_rows( $this->target_table_name );
		$expected = [
			[
				'id'   => "$row1_id",
				'data' => 'foobar',
			],
			[
				'id'   => "$row2_id",
				'data' => 'barbaz',
			],
		];

		$this->assertSame( $expected, $rows );
	}

	public function testShouldNotCloneTableWhenItDoesNotExist() {
		$table  = new Table( new CustomTable() );
		$result = $table->copy_to( $this->target_table_name );

		// Zero rows copied.
		$this->assertSame( 0, $result );
	}
}
