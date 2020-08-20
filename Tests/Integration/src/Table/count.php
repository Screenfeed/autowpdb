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
class Test_Count extends TestCase {
	protected $drop_table = true;

	public function testShouldReturnNumberOfRows() {
		global $wpdb;

		// Create table and contents.
		$this->create_table();
		$this->add_row( 'foobar' );
		$this->add_row( 'barbaz' );
		$this->add_row( 'barbaz' );

		$table  = new Table( new CustomTable() );
		$result = $table->count();

		$this->assertSame( 3, $result );

		$result = $table->count( 'DISTINCT *' );

		$this->assertSame( 3, $result );

		$result = $table->count( 'data' );

		$this->assertSame( 3, $result );

		$result = $table->count( '"data" ' );

		$this->assertSame( 3, $result );

		$result = $table->count( " 'data'" );

		$this->assertSame( 3, $result );

		$result = $table->count( '`data` ' );

		$this->assertSame( 3, $result );

		$result = $table->count( ' dIStinCt   "data" ' );

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
