<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\Table;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Integration\src\Table\TestCase;

/**
 * Tests for Table->clone_to().
 *
 * @covers Table::clone_to
 * @group  Table
 */
class Test_CloneTo extends TestCase {
	protected $drop_table        = true;
	protected $drop_target_table = true;

	public function testShouldCloneTable() {
		global $wpdb;

		// Create table and contents.
		$this->create_table();
		$this->add_row( 'foobar' );
		$this->add_row( 'barbaz' );

		$table  = new Table( new CustomTable() );
		$result = $table->clone_to( $this->target_table_name );

		// Check that the result is true.
		$this->assertTrue( $result );

		$row = $this->get_last_row( $this->target_table_name );

		// Check that the target table exists.
		$error = "Table '{$wpdb->dbname}.{$this->target_table_name}' doesn't exist";

		$this->assertNotSame( $error, $wpdb->last_error );

		// Check that the target table is empty.
		$this->assertNull( $row );
	}

	public function testShouldNotCloneTableWhenItDoesNotExist() {
		$table  = new Table( new CustomTable() );
		$result = $table->clone_to( $this->target_table_name );

		// Check that the result is false.
		$this->assertFalse( $result );
	}
}
