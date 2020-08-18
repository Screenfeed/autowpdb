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

		$this->create_table( $this->table_name );
		$row1_id = $this->add_row( $this->table_name, 'foobar' );
		$row2_id = $this->add_row( $this->table_name, 'barbaz' );
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

	private function create_table( $table_name ) {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$schema          = "
			id bigint(20) unsigned NOT NULL auto_increment,
			data longtext default NULL,
			PRIMARY KEY  (id)";

		$wpdb->query( "CREATE TEMPORARY TABLE `$table_name` ($schema) $charset_collate" );
	}

	private function add_row( $table_name, $data ) {
		global $wpdb;

		$wpdb->insert(
			$table_name,
			[ 'data' => $data ],
			[ 'data' => '%s' ]
		);

		return (int) $wpdb->insert_id;
	}

	private function get_rows( $table_name ) {
		global $wpdb;

		return $wpdb->get_results(
			"SELECT * FROM $table_name ORDER BY `id` ASC",
			ARRAY_A
		);
	}
}
