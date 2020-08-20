<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBUtilities;

use Screenfeed\AutoWPDB\DBUtilities;

/**
 * Tests for DBUtilities::copy_table().
 *
 * @covers DBUtilities::copy_table
 * @group  DBUtilities
 */
class Test_CopyTable extends TestCase {
	protected $drop_table        = true;
	protected $drop_target_table = true;

	public function testShouldCloneTable() {
		global $wpdb;

		$this->create_table();
		$row1_id = $this->add_row( 'foobar' );
		$row2_id = $this->add_row( 'barbaz' );
		$this->create_table( $this->target_table_name );

		$result = DBUtilities::copy_table( $this->table_name, $this->target_table_name );

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
		global $wpdb;

		$result = DBUtilities::copy_table( $this->table_name, $this->target_table_name );

		// Zero rows copied.
		$this->assertSame( 0, $result );
	}
}
