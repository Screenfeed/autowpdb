<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBUtilities;

use Screenfeed\AutoWPDB\DBUtilities;

/**
 * Tests for DBUtilities::count_table_rows().
 *
 * @covers DBUtilities::count_table_rows
 * @group  DBUtilities
 */
class Test_CountTableRows extends TestCase {
	protected $drop_table = true;

	public function testShouldReturnNumberOfRows() {
		global $wpdb;

		// Create table and contents.
		$this->create_table();
		$this->add_row( 'foobar' );
		$this->add_row( 'barbaz' );
		$this->add_row( 'barbaz' );

		$result = DBUtilities::count_table_rows( $this->table_name );

		$this->assertSame( 3, $result );

		$result = DBUtilities::count_table_rows( $this->table_name, 'DISTINCT *' );

		$this->assertSame( 3, $result );

		$result = DBUtilities::count_table_rows( $this->table_name, 'data' );

		$this->assertSame( 3, $result );

		$result = DBUtilities::count_table_rows( $this->table_name, '"data" ' );

		$this->assertSame( 3, $result );

		$result = DBUtilities::count_table_rows( $this->table_name, " 'data'" );

		$this->assertSame( 3, $result );

		$result = DBUtilities::count_table_rows( $this->table_name, '`data` ' );

		$this->assertSame( 3, $result );

		$result = DBUtilities::count_table_rows( $this->table_name, ' dIStinCt   "data" ' );

		$this->assertSame( 2, $result );
	}
}
