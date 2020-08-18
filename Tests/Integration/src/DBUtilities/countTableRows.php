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
}
