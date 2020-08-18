<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBUtilities;

use Screenfeed\AutoWPDB\DBUtilities;

/**
 * Tests for DBUtilities::empty_table().
 *
 * @covers DBUtilities::empty_table
 * @group  DBUtilities
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

		$result = DBUtilities::empty_table( $this->table_name );

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

		$result = DBUtilities::empty_table( $this->table_name );

		$this->assertSame( $result, 0 );
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

	private function get_last_row() {
		global $wpdb;

		return $wpdb->get_row(
			"SELECT * FROM {$this->table_name} ORDER BY `id` DESC LIMIT 1;",
			ARRAY_A
		);
	}
}
