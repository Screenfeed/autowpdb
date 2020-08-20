<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBUtilities;

use Screenfeed\AutoWPDB\DBUtilities;

/**
 * Tests for DBUtilities::reinit_table().
 *
 * @covers DBUtilities::reinit_table
 * @group  DBUtilities
 */
class Test_ReinitTable extends TestCase {
	protected $drop_table = true;

	public function testShouldReinitTable() {
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

		$result = DBUtilities::reinit_table( $this->table_name );

		// Check that the method returns true.
		$this->assertTrue( $result );

		// Check that the table is empty.
		$row = $this->get_last_row();
		$this->assertNull( $row );

		// Insert a row and make sure its id is 1 (auto increment is reset).
		$this->add_row( 'thelast' );
		$row = $this->get_last_row();
		$this->assertSame(
			[
				'id'   => '1',
				'data' => 'thelast',
			],
			$row
		);
	}

	public function testShouldNotReinitTableWhenItDoesNotExist() {

		$result = DBUtilities::reinit_table( $this->table_name );

		$this->assertFalse( $result );
	}
}
