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

		$row = $this->get_target_last_row();

		// Check that the target table exists.
		$error = "Table '{$wpdb->dbname}.{$this->target_table_name}' doesn't exist";

		$this->assertNotSame( $error, $wpdb->last_error );

		// Check that the target table is empty.
		$this->assertNull( $row );
	}

	public function testShouldNotCloneTableWhenItDoesNotExist() {
		global $wpdb;

		$table  = new Table( new CustomTable() );
		$result = $table->clone_to( $this->target_table_name );

		// Check that the result is false.
		$this->assertFalse( $result );
	}

	private function create_table() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$schema          = "
			id bigint(20) unsigned NOT NULL auto_increment,
			data longtext default NULL,
			PRIMARY KEY  (id)";

		$wpdb->query( "CREATE TEMPORARY TABLE `{$this->table_name}` ($schema) $charset_collate" );
	}

	private function add_row( $data ) {
		global $wpdb;

		$wpdb->insert(
			$this->table_name,
			[ 'data' => $data ],
			[ 'data' => '%s' ]
		);

		return (int) $wpdb->insert_id;
	}

	private function get_target_last_row() {
		global $wpdb;

		return $wpdb->get_row(
			"SELECT * FROM {$this->target_table_name} ORDER BY `id` DESC LIMIT 1;",
			OBJECT
		);
	}
}
